const db = require('../config/db');

exports.getAllBlogs = async (req, res, next) => {
  try {
    const { tag, limit = 50 } = req.query;

    let sql = `
      SELECT 
        slug,
        image AS img,
        tag,
        tag_slug,
        DATE_FORMAT(published_at, '%M %d, %Y') AS date,
        title,
        excerpt,
        author,
        read_time AS \`read\`,
        body
      FROM blogs
      WHERE is_published = TRUE
    `;
    const params = [];

    if (tag) {
      sql += ' AND tag_slug = ?';
      params.push(tag);
    }

    sql += ' ORDER BY published_at DESC LIMIT ?';
    params.push(Number(limit));

    const [rows] = await db.query(sql, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

exports.getBlogBySlug = async (req, res, next) => {
  try {
    const { slug } = req.params;
    const [rows] = await db.query(
      `SELECT 
        slug,
        image AS img,
        tag,
        tag_slug,
        DATE_FORMAT(published_at, '%M %d, %Y') AS date,
        title,
        excerpt,
        author,
        read_time AS \`read\`,
        body
       FROM blogs
       WHERE slug = ? AND is_published = TRUE`,
      [slug]
    );

    if (rows.length === 0) {
      return res.status(404).json({ success: false, message: 'Article not found' });
    }

    res.json({ success: true, data: rows[0] });
  } catch (err) {
    next(err);
  }
};

exports.createBlog = async (req, res, next) => {
  try {
    const { title, slug, tag, tag_slug, image, excerpt, author, read_time, body } = req.body;
    if (!title) {
      return res.status(400).json({ success: false, message: 'Article title is required' });
    }
    const genSlug = slug || title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const [result] = await db.query(
      `INSERT INTO blogs (slug, image, tag, tag_slug, title, excerpt, author, read_time, body, is_published, published_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, NOW())`,
      [
        genSlug,
        image || 'assets/img/blog/blog-1.jpg',
        tag || 'Business',
        tag_slug || (tag ? tag.toLowerCase().replace(/[^a-z0-9]+/g, '-') : 'business'),
        title,
        excerpt || '',
        author || 'Admin',
        read_time || '5 min read',
        body || ''
      ]
    );
    res.status(201).json({ success: true, message: 'Blog article created successfully', id: result.insertId });
  } catch (err) {
    next(err);
  }
};

