const crypto = require('crypto');
const db = require('../config/db');

// Helper to safely parse JSON columns
function parseListingData(row) {
  if (!row) return null;
  return {
    ...row,
    gallery: typeof row.gallery === 'string' ? JSON.parse(row.gallery || '[]') : (row.gallery || []),
    amenities: typeof row.amenities === 'string' ? JSON.parse(row.amenities || '[]') : (row.amenities || []),
    verified_badge: Boolean(row.verified_badge),
    distance_km: row.distance_km !== undefined ? Number(Number(row.distance_km).toFixed(2)) : undefined
  };
}

// 1. Search & Filter Properties (PRD Section 10 & 10.1)
exports.getProperties = async (req, res, next) => {
  try {
    const {
      intent,
      rental_type,
      city,
      landmark,
      bhk,
      min_price,
      max_price,
      food_rule,
      curfew_rule,
      brokerage_type,
      lat,
      lng,
      radius = 15, // km
      sort = 'recommended',
      page = 1,
      limit = 20
    } = req.query;

    const conditions = ["status = 'active'"];
    const params = [];

    // Filter by Intent / Rental Type
    const selectedType = rental_type || (intent !== 'all' ? intent : null);
    if (selectedType) {
      conditions.push('rental_type = ?');
      params.push(selectedType);
    }

    // Filter by City
    if (city && city !== 'all') {
      conditions.push('LOWER(city) = LOWER(?)');
      params.push(city);
    }

    // Search by Landmark or Keyword
    if (landmark && landmark.trim()) {
      conditions.push('(landmark LIKE ? OR locality LIKE ? OR title LIKE ?)');
      const searchParam = `%${landmark.trim()}%`;
      params.push(searchParam, searchParam, searchParam);
    }

    // Filter by BHK / Sharing
    if (bhk && bhk !== 'all') {
      conditions.push('(bhk LIKE ? OR category LIKE ?)');
      params.push(`%${bhk}%`, `%${bhk}%`);
    }

    // Filter by Price range
    if (min_price && !isNaN(min_price)) {
      conditions.push('numeric_price >= ?');
      params.push(Number(min_price));
    }
    if (max_price && !isNaN(max_price)) {
      conditions.push('numeric_price <= ?');
      params.push(Number(max_price));
    }

    // Lifestyle filters (PRD Section 2)
    if (food_rule) {
      conditions.push('food_rule LIKE ?');
      params.push(`%${food_rule}%`);
    }
    if (curfew_rule) {
      conditions.push('curfew_rule LIKE ?');
      params.push(`%${curfew_rule}%`);
    }
    if (brokerage_type) {
      conditions.push('brokerage_type = ?');
      params.push(brokerage_type);
    }

    // Geolocation / Distance Calculation (Haversine formula in KM)
    let selectFields = '*';
    let geoOrder = '';
    const hasCoords = lat && lng && !isNaN(lat) && !isNaN(lng);

    if (hasCoords) {
      const userLat = Number(lat);
      const userLng = Number(lng);
      selectFields += `, ( 6371 * acos( cos( radians(${userLat}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(${userLng}) ) + sin( radians(${userLat}) ) * sin( radians( latitude ) ) ) ) AS distance_km`;
      
      if (radius && !isNaN(radius)) {
        conditions.push(`( 6371 * acos( cos( radians(${userLat}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(${userLng}) ) + sin( radians(${userLat}) ) * sin( radians( latitude ) ) ) ) <= ?`);
        params.push(Number(radius));
      }
    }

    // Sorting
    let orderBy = 'ORDER BY id DESC';
    if (sort === 'price_asc') {
      orderBy = 'ORDER BY numeric_price ASC';
    } else if (sort === 'price_desc') {
      orderBy = 'ORDER BY numeric_price DESC';
    } else if (sort === 'distance' && hasCoords) {
      orderBy = 'ORDER BY distance_km ASC';
    } else if (sort === 'verified') {
      orderBy = 'ORDER BY verified_badge DESC, id DESC';
    }

    const whereClause = conditions.length > 0 ? `WHERE ${conditions.join(' AND ')}` : '';
    
    // Pagination
    const pageNum = Math.max(1, Number(page));
    const pageLimit = Math.max(1, Math.min(50, Number(limit)));
    const offset = (pageNum - 1) * pageLimit;

    // Count query
    const countSql = `SELECT COUNT(*) AS total FROM rental_properties ${whereClause}`;
    const [countResult] = await db.query(countSql, params);
    const total = countResult[0]?.total || 0;

    // Data query
    const dataSql = `SELECT ${selectFields} FROM rental_properties ${whereClause} ${orderBy} LIMIT ? OFFSET ?`;
    const [rows] = await db.query(dataSql, [...params, pageLimit, offset]);

    res.json({
      success: true,
      total,
      page: pageNum,
      limit: pageLimit,
      total_pages: Math.ceil(total / pageLimit),
      data: rows.map(parseListingData)
    });
  } catch (error) {
    next(error);
  }
};

// 2. Curated Picks for Hero Section (PRD Section 10)
exports.getCurated = async (req, res, next) => {
  try {
    // 3 Editorial picks: 1 BHK Pune (112), Nashik Co-Living (109), Jaipur Heritage (111)
    const [rows] = await db.query(
      `SELECT * FROM rental_properties WHERE id IN (112, 109, 111) ORDER BY FIELD(id, 112, 109, 111)`
    );
    res.json({
      success: true,
      count: rows.length,
      data: rows.map(parseListingData)
    });
  } catch (error) {
    next(error);
  }
};

// 3. Property Detail by ID or Slug (PRD Section 12)
exports.getPropertyById = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { lat, lng } = req.query;

    let selectFields = '*';
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
      const uLat = Number(lat);
      const uLng = Number(lng);
      selectFields += `, ( 6371 * acos( cos( radians(${uLat}) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(${uLng}) ) + sin( radians(${uLat}) ) * sin( radians( latitude ) ) ) ) AS distance_km`;
    }

    const isNumeric = !isNaN(id);
    const query = isNumeric
      ? `SELECT ${selectFields} FROM rental_properties WHERE id = ? LIMIT 1`
      : `SELECT ${selectFields} FROM rental_properties WHERE slug = ? LIMIT 1`;

    const [rows] = await db.query(query, [id]);
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Property not found' });
    }

    const property = parseListingData(rows[0]);

    // Fetch up to 3 similar properties in the same city
    const [similar] = await db.query(
      `SELECT * FROM rental_properties WHERE city = ? AND id != ? AND status = 'active' LIMIT 3`,
      [property.city, property.id]
    );

    res.json({
      success: true,
      data: property,
      similar_properties: similar.map(parseListingData)
    });
  } catch (error) {
    next(error);
  }
};

// 4. Create New Property (PRD Section 11 & Owner Listing)
exports.createProperty = async (req, res, next) => {
  try {
    const {
      title,
      rental_type = 'long_term',
      category = 'Flat / Apartment',
      city,
      state = 'India',
      locality,
      landmark,
      landmark_distance = '',
      latitude = 18.5204,
      longitude = 73.8567,
      price,
      numeric_price,
      price_unit = '/ month',
      deposit = '1 Month',
      bhk = '1 BHK',
      bathrooms = 1,
      furnishing = 'Semi-Furnished',
      preferred_tenant = 'All Welcome',
      food_rule = 'Non-Veg Allowed',
      curfew_rule = 'No Curfew',
      power_backup = 'Inverter Backup',
      water_supply = '24x7 Water Supply',
      brokerage_type = 'zero_brokerage',
      image = 'realestate_01.webp',
      gallery = [],
      amenities = [],
      host_name,
      host_phone,
      description
    } = req.body;

    if (!title || !city || !price || !host_phone) {
      return res.status(400).json({
        success: false,
        message: 'Title, city, price, and host phone number are required.'
      });
    }

    const calculatedPriceNum = numeric_price || parseInt(String(price).replace(/[^0-9]/g, ''), 10) || 0;
    const baseSlug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    const slug = `${baseSlug}-${Date.now().toString().slice(-4)}`;

    const insertSql = `
      INSERT INTO rental_properties (
        title, slug, rental_type, category, city, state, locality,
        landmark, landmark_distance, latitude, longitude,
        price, numeric_price, price_unit, deposit, bhk, bathrooms,
        furnishing, preferred_tenant, food_rule, curfew_rule,
        power_backup, water_supply, brokerage_type, image, gallery,
        amenities, host_name, host_phone, description, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
    `;

    const [result] = await db.query(insertSql, [
      title,
      slug,
      rental_type,
      category,
      city,
      state,
      locality || landmark,
      landmark,
      landmark_distance,
      latitude,
      longitude,
      price,
      calculatedPriceNum,
      price_unit,
      deposit,
      bhk,
      bathrooms,
      furnishing,
      preferred_tenant,
      food_rule,
      curfew_rule,
      power_backup,
      water_supply,
      brokerage_type,
      image,
      JSON.stringify(gallery.length ? gallery : [image]),
      JSON.stringify(amenities),
      host_name || 'Verified Owner',
      host_phone,
      description || title
    ]);

    res.status(201).json({
      success: true,
      message: 'Property listing created successfully!',
      property_id: result.insertId,
      slug
    });
  } catch (error) {
    next(error);
  }
};

// 5. Schedule a Property Visit (PRD Section 16)
exports.createVisit = async (req, res, next) => {
  try {
    const {
      property_id,
      visit_date,
      time_slot,
      seeker_name,
      seeker_phone,
      seeker_notes = ''
    } = req.body;

    if (!property_id || !visit_date || !time_slot || !seeker_name || !seeker_phone) {
      return res.status(400).json({
        success: false,
        message: 'Property ID, visit date, time slot, name, and phone are required.'
      });
    }

    // Check if property exists
    const [propRows] = await db.query(
      `SELECT id, title, price, landmark, host_name, host_phone FROM rental_properties WHERE id = ? LIMIT 1`,
      [property_id]
    );

    if (propRows.length === 0) {
      return res.status(404).json({ success: false, message: 'Property not found.' });
    }

    const prop = propRows[0];
    const token = 'vst_' + crypto.randomBytes(12).toString('hex');

    const insertSql = `
      INSERT INTO rental_visits (
        property_id, token, visit_date, time_slot,
        seeker_name, seeker_phone, seeker_notes, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    `;

    const [result] = await db.query(insertSql, [
      property_id,
      token,
      visit_date,
      time_slot,
      seeker_name,
      seeker_phone,
      seeker_notes
    ]);

    // Format WhatsApp confirmation text for owner/warden
    const waText = encodeURIComponent(
      `Hi ${prop.host_name},\nI would like to schedule a visit to your property *${prop.title}* (${prop.price}) on *${visit_date}* during *${time_slot}*.\nName: ${seeker_name}\nPhone: ${seeker_phone}\nVisit Token: ${token}`
    );
    const waUrl = `https://wa.me/${prop.host_phone.replace(/[^0-9]/g, '')}?text=${waText}`;

    res.status(201).json({
      success: true,
      message: 'Visit scheduled successfully!',
      visit_id: result.insertId,
      token,
      property: {
        id: prop.id,
        title: prop.title,
        price: prop.price,
        landmark: prop.landmark
      },
      status: 'pending',
      whatsapp_url: waUrl
    });
  } catch (error) {
    next(error);
  }
};

// 6. Get Visit Status by Token (PRD Section 16 - Seeker view)
exports.getVisitByToken = async (req, res, next) => {
  try {
    const { token } = req.params;
    const query = `
      SELECT v.*, p.title AS property_title, p.price, p.landmark, p.city, p.host_name, p.host_phone, p.image
      FROM rental_visits v
      JOIN rental_properties p ON v.property_id = p.id
      WHERE v.token = ?
      LIMIT 1
    `;

    const [rows] = await db.query(query, [token]);
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Visit token not found.' });
    }

    res.json({
      success: true,
      data: rows[0]
    });
  } catch (error) {
    next(error);
  }
};

// 7. Update Visit Status (Owner / Admin management)
exports.updateVisitStatus = async (req, res, next) => {
  try {
    const { token } = req.params;
    const { status, owner_notes, reschedule_date, reschedule_time } = req.body;

    const allowed = ['pending', 'confirmed', 'rescheduled', 'completed', 'cancelled'];
    if (!status || !allowed.includes(status)) {
      return res.status(400).json({
        success: false,
        message: `Status must be one of: ${allowed.join(', ')}`
      });
    }

    let updateSql = `UPDATE rental_visits SET status = ?`;
    const params = [status];

    if (owner_notes !== undefined) {
      updateSql += `, owner_notes = ?`;
      params.push(owner_notes);
    }
    if (status === 'rescheduled' && reschedule_date && reschedule_time) {
      updateSql += `, visit_date = ?, time_slot = ?`;
      params.push(reschedule_date, reschedule_time);
    }

    updateSql += ` WHERE token = ?`;
    params.push(token);

    const [result] = await db.query(updateSql, params);
    if (result.affectedRows === 0) {
      return res.status(404).json({ success: false, message: 'Visit token not found.' });
    }

    res.json({
      success: true,
      message: `Visit status updated to ${status}!`,
      token,
      status
    });
  } catch (error) {
    next(error);
  }
};

// 8. List Visits (Admin / Owner CRM - PRD Section 13 & 14)
exports.listVisits = async (req, res, next) => {
  try {
    const { status, property_id, page = 1, limit = 25 } = req.query;
    const conditions = [];
    const params = [];

    if (status) {
      conditions.push('v.status = ?');
      params.push(status);
    }
    if (property_id) {
      conditions.push('v.property_id = ?');
      params.push(Number(property_id));
    }

    const whereClause = conditions.length > 0 ? `WHERE ${conditions.join(' AND ')}` : '';
    const offset = (Math.max(1, Number(page)) - 1) * Number(limit);

    const sql = `
      SELECT v.*, p.title AS property_title, p.price, p.city, p.landmark
      FROM rental_visits v
      JOIN rental_properties p ON v.property_id = p.id
      ${whereClause}
      ORDER BY v.id DESC
      LIMIT ? OFFSET ?
    `;

    const [rows] = await db.query(sql, [...params, Number(limit), offset]);
    res.json({
      success: true,
      count: rows.length,
      data: rows
    });
  } catch (error) {
    next(error);
  }
};

// 9. Post Requirement - 'I Need a Property' (PRD Section 17)
exports.postRequirement = async (req, res, next) => {
  try {
    const {
      requirement_type,
      city,
      preferred_landmark = '',
      max_budget,
      min_budget = 0,
      move_in_date = null,
      tenant_profile = 'Working Professional',
      food_preference = 'Any',
      contact_name,
      contact_phone,
      additional_notes = ''
    } = req.body;

    if (!requirement_type || !city || !max_budget || !contact_name || !contact_phone) {
      return res.status(400).json({
        success: false,
        message: 'Requirement type, city, max budget, contact name, and phone are required.'
      });
    }

    const insertSql = `
      INSERT INTO rental_requirements (
        requirement_type, city, preferred_landmark, max_budget, min_budget,
        move_in_date, tenant_profile, food_preference, contact_name, contact_phone,
        additional_notes, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
    `;

    const [result] = await db.query(insertSql, [
      requirement_type,
      city,
      preferred_landmark,
      Number(max_budget),
      Number(min_budget),
      move_in_date,
      tenant_profile,
      food_preference,
      contact_name,
      contact_phone,
      additional_notes
    ]);

    // Rule-based instant match search (PRD Section 17.1)
    const matchSql = `
      SELECT * FROM rental_properties
      WHERE city = ? AND numeric_price <= ? AND status = 'active'
      ORDER BY numeric_price DESC
      LIMIT 5
    `;
    const [matches] = await db.query(matchSql, [city, Number(max_budget) * 1.15]); // 15% tolerance

    res.status(201).json({
      success: true,
      message: 'Your requirement has been posted successfully!',
      requirement_id: result.insertId,
      instant_matches_count: matches.length,
      matches: matches.map(parseListingData)
    });
  } catch (error) {
    next(error);
  }
};

// 10. Get Requirement Matches
exports.getRequirementMatches = async (req, res, next) => {
  try {
    const { id } = req.params;
    const [reqRows] = await db.query(`SELECT * FROM rental_requirements WHERE id = ? LIMIT 1`, [id]);
    if (reqRows.length === 0) {
      return res.status(404).json({ success: false, message: 'Requirement not found.' });
    }

    const reqItem = reqRows[0];
    const matchSql = `
      SELECT * FROM rental_properties
      WHERE city = ? AND numeric_price <= ? AND status = 'active'
      ORDER BY numeric_price DESC
      LIMIT 10
    `;
    const [matches] = await db.query(matchSql, [reqItem.city, Number(reqItem.max_budget) * 1.2]);

    res.json({
      success: true,
      requirement: reqItem,
      matches: matches.map(parseListingData)
    });
  } catch (error) {
    next(error);
  }
};

// 11. Assisted WhatsApp Onboarding Intake (Small-Town Wedge - PRD Section 5 & 11)
exports.submitAssistedOnboarding = async (req, res, next) => {
  try {
    const {
      owner_name,
      owner_phone,
      property_type,
      city,
      landmark,
      expected_rent = '',
      notes = ''
    } = req.body;

    if (!owner_name || !owner_phone || !property_type || !city || !landmark) {
      return res.status(400).json({
        success: false,
        message: 'Owner name, phone, property type, city, and landmark are required.'
      });
    }

    const insertSql = `
      INSERT INTO rental_assisted_onboarding (
        owner_name, owner_phone, property_type, city, landmark, expected_rent, notes, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new')
    `;

    const [result] = await db.query(insertSql, [
      owner_name,
      owner_phone,
      property_type,
      city,
      landmark,
      expected_rent,
      notes
    ]);

    const waText = encodeURIComponent(
      `Hi POV Indian Team,\nI have submitted my property for Assisted Onboarding.\nName: ${owner_name}\nType: ${property_type}\nCity: ${city}\nLandmark: ${landmark}\nRent: ${expected_rent}\nPlease guide me on WhatsApp.`
    );
    const waUrl = `https://wa.me/919876543210?text=${waText}`;

    res.status(201).json({
      success: true,
      message: 'Assisted listing request received! Our team will contact you in 2 hours.',
      request_id: result.insertId,
      whatsapp_direct_url: waUrl
    });
  } catch (error) {
    next(error);
  }
};

// 12. Create Property (PRD Section 11 - Admin & Host self-serve)
exports.createProperty = async (req, res, next) => {
  try {
    const {
      title,
      rental_type = 'long_term',
      category = 'Flat / Apartment',
      city = 'Pune',
      state = 'Maharashtra',
      locality = '',
      landmark = '',
      landmark_distance = '',
      latitude = 0,
      longitude = 0,
      geo_confidence = 'Exact Verified',
      price = '₹12,000',
      numeric_price,
      price_unit = '/ month',
      deposit = '₹20,000',
      bhk = '1 BHK',
      bathrooms = 1,
      furnishing = 'Semi-Furnished',
      preferred_tenant = 'All Welcome',
      food_rule = 'Non-Veg Allowed',
      curfew_rule = 'No Curfew',
      power_backup = 'Inverter Backup',
      water_supply = '24x7 Water Supply',
      brokerage_type = 'zero_brokerage',
      image = 'realestate_01.webp',
      gallery,
      amenities,
      host_name = 'Verified Host',
      host_phone = '+91 98765 43210',
      description = ''
    } = req.body;

    if (!title) {
      return res.status(400).json({ success: false, message: 'Property title is required.' });
    }

    const numPrice = numeric_price !== undefined ? Number(numeric_price) : (Number(String(price).replace(/[^0-9]/g, '')) || 0);
    const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') + '-' + Date.now();

    const galleryJson = typeof gallery === 'string' ? gallery : JSON.stringify(gallery || [image]);
    const amenitiesJson = typeof amenities === 'string' ? amenities : JSON.stringify(amenities || ['Wifi', 'Water Supply', 'Security']);

    const insertSql = `
      INSERT INTO rental_properties (
        title, slug, rental_type, category, city, state, locality,
        landmark, landmark_distance, latitude, longitude, geo_confidence,
        price, numeric_price, price_unit, deposit, bhk, bathrooms,
        furnishing, preferred_tenant, food_rule, curfew_rule,
        power_backup, water_supply, brokerage_type, image, gallery,
        amenities, host_name, host_phone, verified_badge, description, status
      ) VALUES (
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, 1, ?, 'active'
      )
    `;

    const [result] = await db.query(insertSql, [
      title, slug, rental_type, category, city, state, locality,
      landmark, landmark_distance, Number(latitude) || 0, Number(longitude) || 0, geo_confidence,
      price, numPrice, price_unit, deposit, bhk, Number(bathrooms) || 1,
      furnishing, preferred_tenant, food_rule, curfew_rule,
      power_backup, water_supply, brokerage_type, image, galleryJson,
      amenitiesJson, host_name, host_phone, description || title
    ]);

    res.status(201).json({
      success: true,
      message: 'Rental property created successfully!',
      id: result.insertId,
      slug
    });
  } catch (error) {
    next(error);
  }
};

// 13. Update Property
exports.updateProperty = async (req, res, next) => {
  try {
    const { id } = req.params;
    const [existing] = await db.query('SELECT * FROM rental_properties WHERE id = ?', [id]);
    if (existing.length === 0) {
      return res.status(404).json({ success: false, message: 'Property not found.' });
    }

    const cur = existing[0];
    const b = req.body;

    const numPrice = b.numeric_price !== undefined ? Number(b.numeric_price) : (b.price ? (Number(String(b.price).replace(/[^0-9]/g, '')) || cur.numeric_price) : cur.numeric_price);
    const galleryJson = b.gallery !== undefined ? (typeof b.gallery === 'string' ? b.gallery : JSON.stringify(b.gallery)) : cur.gallery;
    const amenitiesJson = b.amenities !== undefined ? (typeof b.amenities === 'string' ? b.amenities : JSON.stringify(b.amenities)) : cur.amenities;

    const updateSql = `
      UPDATE rental_properties SET
        title = ?, rental_type = ?, category = ?, city = ?, state = ?, locality = ?,
        landmark = ?, landmark_distance = ?, latitude = ?, longitude = ?, geo_confidence = ?,
        price = ?, numeric_price = ?, price_unit = ?, deposit = ?, bhk = ?, bathrooms = ?,
        furnishing = ?, preferred_tenant = ?, food_rule = ?, curfew_rule = ?,
        power_backup = ?, water_supply = ?, brokerage_type = ?, image = ?, gallery = ?,
        amenities = ?, host_name = ?, host_phone = ?, description = ?
      WHERE id = ?
    `;

    await db.query(updateSql, [
      b.title !== undefined ? b.title : cur.title,
      b.rental_type !== undefined ? b.rental_type : cur.rental_type,
      b.category !== undefined ? b.category : cur.category,
      b.city !== undefined ? b.city : cur.city,
      b.state !== undefined ? b.state : cur.state,
      b.locality !== undefined ? b.locality : cur.locality,
      b.landmark !== undefined ? b.landmark : cur.landmark,
      b.landmark_distance !== undefined ? b.landmark_distance : cur.landmark_distance,
      b.latitude !== undefined ? Number(b.latitude) : cur.latitude,
      b.longitude !== undefined ? Number(b.longitude) : cur.longitude,
      b.geo_confidence !== undefined ? b.geo_confidence : cur.geo_confidence,
      b.price !== undefined ? b.price : cur.price,
      numPrice,
      b.price_unit !== undefined ? b.price_unit : cur.price_unit,
      b.deposit !== undefined ? b.deposit : cur.deposit,
      b.bhk !== undefined ? b.bhk : cur.bhk,
      b.bathrooms !== undefined ? Number(b.bathrooms) : cur.bathrooms,
      b.furnishing !== undefined ? b.furnishing : cur.furnishing,
      b.preferred_tenant !== undefined ? b.preferred_tenant : cur.preferred_tenant,
      b.food_rule !== undefined ? b.food_rule : cur.food_rule,
      b.curfew_rule !== undefined ? b.curfew_rule : cur.curfew_rule,
      b.power_backup !== undefined ? b.power_backup : cur.power_backup,
      b.water_supply !== undefined ? b.water_supply : cur.water_supply,
      b.brokerage_type !== undefined ? b.brokerage_type : cur.brokerage_type,
      b.image !== undefined ? b.image : cur.image,
      galleryJson,
      amenitiesJson,
      b.host_name !== undefined ? b.host_name : cur.host_name,
      b.host_phone !== undefined ? b.host_phone : cur.host_phone,
      b.description !== undefined ? b.description : cur.description,
      id
    ]);

    res.json({
      success: true,
      message: 'Rental property updated successfully!',
      id: Number(id)
    });
  } catch (error) {
    next(error);
  }
};

// 14. Delete Property
exports.deleteProperty = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query('DELETE FROM rental_properties WHERE id = ?', [id]);
    res.json({ success: true, message: 'Rental property deleted successfully.' });
  } catch (error) {
    next(error);
  }
};
