const db = require('../config/db');

exports.getAllLocations = async (req, res, next) => {
  try {
    const [rows] = await db.query(
      `SELECT id, name, slug, count_text AS count, state 
       FROM locations 
       WHERE is_active = TRUE 
       ORDER BY id ASC`
    );
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

exports.getLocationBySlug = async (req, res, next) => {
  try {
    const { slug } = req.params;
    const [rows] = await db.query(
      `SELECT id, name, slug, count_text AS count, state 
       FROM locations 
       WHERE slug = ? AND is_active = TRUE`,
      [slug]
    );

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Location not found' });
    }

    res.json({ success: true, data: rows[0] });
  } catch (err) {
    next(err);
  }
};

exports.createLocation = async (req, res, next) => {
  try {
    const { name, slug, count_text, state } = req.body;
    if (!name) {
      return res.status(400).json({ success: false, message: 'Location name is required' });
    }
    const genSlug = slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const [result] = await db.query(
      `INSERT INTO locations (name, slug, count_text, state, is_active)
       VALUES (?, ?, ?, ?, TRUE)`,
      [name, genSlug, count_text || '0+ Listings', state || '']
    );
    res.status(201).json({ success: true, message: 'Location created successfully', id: result.insertId });
  } catch (err) {
    next(err);
  }
};

