const { execSync } = require('child_process');
const path = require('path');
const db = require('../config/db');

async function seedRentals() {
  console.log('--- Starting Rentals & Stays Seeding ---');
  try {
    // 1. Fetch JSON from PHP helper
    const phpScript = path.join(__dirname, 'exportRentals.php');
    const stdout = execSync(`php "${phpScript}"`, { encoding: 'utf-8', maxBuffer: 10 * 1024 * 1024 });
    const listings = JSON.parse(stdout);

    console.log(`Loaded ${listings.length} properties from data source.`);

    // 2. Clear or upsert into rental_properties
    for (const item of listings) {
      const slug = item.title
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '') + '-' + item.id;

      const galleryJson = JSON.stringify(item.gallery || [item.img]);
      const amenitiesJson = JSON.stringify(item.amenities || []);

      const query = `
        INSERT INTO rental_properties (
          id, title, slug, rental_type, category, city, state, locality,
          landmark, landmark_distance, latitude, longitude, geo_confidence,
          price, numeric_price, price_unit, deposit, bhk, bathrooms,
          furnishing, preferred_tenant, food_rule, curfew_rule,
          power_backup, water_supply, brokerage_type, image, gallery,
          amenities, host_name, host_phone, verified_badge, description, status
        ) VALUES (
          ?, ?, ?, ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?, ?,
          ?, ?, ?, ?,
          ?, ?, ?, ?, ?,
          ?, ?, ?, ?, ?, ?
        )
        ON DUPLICATE KEY UPDATE
          title = VALUES(title),
          slug = VALUES(slug),
          rental_type = VALUES(rental_type),
          category = VALUES(category),
          city = VALUES(city),
          state = VALUES(state),
          locality = VALUES(locality),
          landmark = VALUES(landmark),
          landmark_distance = VALUES(landmark_distance),
          latitude = VALUES(latitude),
          longitude = VALUES(longitude),
          geo_confidence = VALUES(geo_confidence),
          price = VALUES(price),
          numeric_price = VALUES(numeric_price),
          price_unit = VALUES(price_unit),
          deposit = VALUES(deposit),
          bhk = VALUES(bhk),
          bathrooms = VALUES(bathrooms),
          furnishing = VALUES(furnishing),
          preferred_tenant = VALUES(preferred_tenant),
          food_rule = VALUES(food_rule),
          curfew_rule = VALUES(curfew_rule),
          power_backup = VALUES(power_backup),
          water_supply = VALUES(water_supply),
          brokerage_type = VALUES(brokerage_type),
          image = VALUES(image),
          gallery = VALUES(gallery),
          amenities = VALUES(amenities),
          host_name = VALUES(host_name),
          host_phone = VALUES(host_phone),
          verified_badge = VALUES(verified_badge),
          description = VALUES(description),
          status = VALUES(status)
      `;

      const values = [
        item.id,
        item.title,
        slug,
        item.rental_type || 'long_term',
        item.category || 'Flat / Apartment',
        item.city || 'Pune',
        item.state || 'Maharashtra',
        item.locality || '',
        item.landmark || '',
        item.landmark_distance || '',
        Number(item.latitude) || 0,
        Number(item.longitude) || 0,
        item.geo_confidence || 'Exact Verified',
        item.price || '₹0',
        Number(item.price_num) || 0,
        item.price_unit || '/ month',
        item.deposit || 'Nil',
        item.bhk || (item.category === 'PG / Hostel' ? 'Single / Double Sharing' : '1 BHK'),
        Number(item.bathrooms) || 1,
        item.furnishing || 'Semi-Furnished',
        item.tenant_preference || 'All Welcome',
        item.food_label || item.food_policy || 'Non-Veg Allowed',
        item.curfew_label || item.gate_curfew || 'No Curfew',
        item.power_backup || 'Inverter Backup',
        item.water_supply || '24x7 Water Supply',
        item.brokerage_type || 'zero_brokerage',
        item.img || 'realestate_01.webp',
        galleryJson,
        amenitiesJson,
        item.provider_name || 'Verified Host',
        item.phone || '+91 98765 43210',
        1,
        item.desc || item.title,
        'active'
      ];

      await db.query(query, values);
    }

    console.log(`✅ Successfully seeded ${listings.length} properties into rental_properties table!`);
    process.exit(0);
  } catch (error) {
    console.error('❌ Seeding failed:', error);
    process.exit(1);
  }
}

seedRentals();
