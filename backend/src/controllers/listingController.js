const db = require('../config/db');

exports.getListings = async (req, res, next) => {
  try {
    const { category, location, q, limit = 100 } = req.query;

    let sql = `
      SELECT 
        l.id,
        l.image AS img,
        l.avatar,
        c.name AS cat,
        c.slug AS cat_slug,
        l.category_id,
        l.location_id,
        l.rating,
        l.title,
        l.slug,
        l.location_text AS loc,
        l.phone,
        l.price,
        l.badge,
        l.description AS \`desc\`,
        l.meta_title,
        l.meta_description,
        l.meta_keywords,
        l.canonical_url,
        l.og_image,
        l.schema_type
      FROM listings l
      LEFT JOIN categories c ON l.category_id = c.id
      LEFT JOIN locations loc ON l.location_id = loc.id
      WHERE l.status = 'approved'
    `;
    const params = [];

    if (category) {
      sql += ' AND c.slug = ?';
      params.push(category);
    }
    if (location) {
      sql += ' AND (loc.slug = ? OR l.location_text LIKE ?)';
      params.push(location, `%${location}%`);
    }
    if (q) {
      sql += ' AND (l.title LIKE ? OR l.description LIKE ? OR l.location_text LIKE ? OR c.name LIKE ?)';
      const queryPattern = `%${q}%`;
      params.push(queryPattern, queryPattern, queryPattern, queryPattern);
    }

    sql += ' ORDER BY l.id ASC LIMIT ?';
    params.push(Number(limit));

    const [rows] = await db.query(sql, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

exports.getListingById = async (req, res, next) => {
  try {
    const { id } = req.params;
    const [rows] = await db.query(
      `SELECT 
        l.id,
        l.image AS img,
        l.avatar,
        c.name AS cat,
        c.slug AS cat_slug,
        l.category_id,
        l.location_id,
        l.rating,
        l.title,
        l.slug,
        l.location_text AS loc,
        l.phone,
        l.price,
        l.badge,
        l.description AS \`desc\`,
        l.meta_title,
        l.meta_description,
        l.meta_keywords,
        l.canonical_url,
        l.og_image,
        l.schema_type
       FROM listings l
       LEFT JOIN categories c ON l.category_id = c.id
       LEFT JOIN locations loc ON l.location_id = loc.id
       WHERE l.id = ? AND l.status = 'approved'`,
      [id]
    );

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Listing not found' });
    }

    res.json({ success: true, data: rows[0] });
  } catch (err) {
    next(err);
  }
};

// Admin: Create listing
exports.createListing = async (req, res, next) => {
  try {
    const {
      category_id,
      location_id,
      title,
      slug,
      location_text,
      phone,
      price,
      rating = 5.0,
      badge = '',
      image,
      avatar,
      description,
      meta_title,
      meta_description,
      meta_keywords,
      canonical_url,
      og_image,
      schema_type = 'LocalBusiness'
    } = req.body;

    if (!category_id || !title) {
      return res.status(400).json({ success: false, message: 'Category ID and Title are required' });
    }

    const genSlug = slug || title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

    const [result] = await db.query(
      `INSERT INTO listings 
       (category_id, location_id, title, slug, location_text, phone, price, rating, badge, image, avatar, description, meta_title, meta_description, meta_keywords, canonical_url, og_image, schema_type, status)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')`,
      [
        category_id,
        location_id || null,
        title,
        genSlug,
        location_text || '',
        phone || '',
        price || '',
        rating,
        badge,
        image || '',
        avatar || '',
        description || '',
        meta_title || null,
        meta_description || null,
        meta_keywords || null,
        canonical_url || null,
        og_image || null,
        schema_type || 'LocalBusiness'
      ]
    );

    res.status(201).json({
      success: true,
      message: 'Listing created successfully',
      id: result.insertId
    });
  } catch (err) {
    next(err);
  }
};

exports.updateListing = async (req, res, next) => {
  try {
    const { id } = req.params;
    const {
      category_id,
      location_id,
      title,
      slug,
      location_text,
      phone,
      price,
      rating,
      badge,
      image,
      avatar,
      description,
      meta_title,
      meta_description,
      meta_keywords,
      canonical_url,
      og_image,
      schema_type
    } = req.body;

    const [existing] = await db.query('SELECT * FROM listings WHERE id = ?', [id]);
    if (existing.length === 0) {
      return res.status(404).json({ success: false, message: 'Listing not found' });
    }

    const cur = existing[0];
    const newTitle = title !== undefined ? title : cur.title;
    const newSlug = slug || (title ? title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') : cur.slug);

    await db.query(
      `UPDATE listings SET
        category_id = ?,
        location_id = ?,
        title = ?,
        slug = ?,
        location_text = ?,
        phone = ?,
        price = ?,
        rating = ?,
        badge = ?,
        image = ?,
        avatar = ?,
        description = ?,
        meta_title = ?,
        meta_description = ?,
        meta_keywords = ?,
        canonical_url = ?,
        og_image = ?,
        schema_type = ?
       WHERE id = ?`,
      [
        category_id !== undefined ? category_id : cur.category_id,
        location_id !== undefined ? (location_id || null) : cur.location_id,
        newTitle,
        newSlug,
        location_text !== undefined ? location_text : cur.location_text,
        phone !== undefined ? phone : cur.phone,
        price !== undefined ? price : cur.price,
        rating !== undefined ? Number(rating) : cur.rating,
        badge !== undefined ? badge : cur.badge,
        image !== undefined ? image : cur.image,
        avatar !== undefined ? avatar : cur.avatar,
        description !== undefined ? description : cur.description,
        meta_title !== undefined ? meta_title : cur.meta_title,
        meta_description !== undefined ? meta_description : cur.meta_description,
        meta_keywords !== undefined ? meta_keywords : cur.meta_keywords,
        canonical_url !== undefined ? canonical_url : cur.canonical_url,
        og_image !== undefined ? og_image : cur.og_image,
        schema_type !== undefined ? schema_type : (cur.schema_type || 'LocalBusiness'),
        id
      ]
    );

    res.json({
      success: true,
      message: 'Listing updated successfully',
      id: Number(id)
    });
  } catch (err) {
    next(err);
  }
};

exports.deleteListing = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query('DELETE FROM listings WHERE id = ?', [id]);
    res.json({ success: true, message: 'Listing deleted successfully' });
  } catch (err) {
    next(err);
  }
};


