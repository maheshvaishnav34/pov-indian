const db = require('../config/db');

exports.getAllCategories = async (req, res, next) => {
  try {
    const [rows] = await db.query(
      `SELECT id, name, slug, count_text AS count, icon, hero_image AS hero, description AS \`desc\`, show_in_signup
       FROM categories 
       WHERE is_active = TRUE 
       ORDER BY display_order ASC, id ASC`
    );
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

exports.getCategoryBySlug = async (req, res, next) => {
  try {
    const { slug } = req.params;
    const [rows] = await db.query(
      `SELECT id, name, slug, count_text AS count, icon, hero_image AS hero, description AS \`desc\`, show_in_signup
       FROM categories 
       WHERE slug = ? AND is_active = TRUE`,
      [slug]
    );

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Category not found' });
    }

    res.json({ success: true, data: rows[0] });
  } catch (err) {
    next(err);
  }
};

exports.createCategory = async (req, res, next) => {
  try {
    const { name, slug, count_text, icon, hero_image, description, show_in_signup } = req.body;
    if (!name) {
      return res.status(400).json({ success: false, message: 'Category name is required' });
    }
    const genSlug = slug || name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const showVal = show_in_signup !== undefined ? (show_in_signup ? 1 : 0) : 1;
    const [result] = await db.query(
      `INSERT INTO categories (name, slug, count_text, icon, hero_image, description, show_in_signup, is_active)
       VALUES (?, ?, ?, ?, ?, ?, ?, TRUE)`,
      [name, genSlug, count_text || '0+ Listings', icon || 'fa-solid fa-folder', hero_image || '', description || '', showVal]
    );
    res.status(201).json({ success: true, message: 'Category created successfully', id: result.insertId });
  } catch (err) {
    next(err);
  }
};

exports.toggleCategorySignup = async (req, res, next) => {
  try {
    const id = Number(req.params.id);
    if (!id) {
      return res.status(400).json({ success: false, message: 'Valid category ID required' });
    }
    const [rows] = await db.query('SELECT id, name, show_in_signup FROM categories WHERE id = ?', [id]);
    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Category not found' });
    }
    const current = Boolean(rows[0].show_in_signup);
    const newState = !current;
    await db.query('UPDATE categories SET show_in_signup = ? WHERE id = ?', [newState ? 1 : 0, id]);
    res.json({
      success: true,
      id,
      show_in_signup: newState,
      message: newState
        ? `Category '${rows[0].name}' will now show in Sign Up form!`
        : `Category '${rows[0].name}' is now hidden from Sign Up form.`
    });
  } catch (err) {
    next(err);
  }
};

