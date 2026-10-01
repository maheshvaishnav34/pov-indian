const db = require('../config/db');

exports.submitForm = async (req, res, next) => {
  try {
    const { type, name, email, phone, subject, message, extra_data } = req.body;
    const ip = req.headers['x-forwarded-for'] || req.socket.remoteAddress || '';

    const allowedTypes = [
      'contact', 'list-business', 'contributor', 'signup', 'subscribe',
      'requirement-post', 'schedule-visit', 'rental-enquiry', 'list-rental'
    ];
    if (!allowedTypes.includes(type)) {
      return res.status(400).json({ success: false, message: 'Invalid submission type' });
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return res.status(400).json({ success: false, message: 'Valid email address is required' });
    }

    const sql = `
      INSERT INTO form_submissions (type, name, email, phone, subject, message, extra_data, ip_address)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const [result] = await db.query(sql, [
      type,
      name ? String(name).trim() : null,
      String(email).trim().toLowerCase(),
      phone ? String(phone).trim() : null,
      subject ? String(subject).trim() : null,
      message ? String(message).trim() : null,
      extra_data ? JSON.stringify(extra_data) : null,
      ip
    ]);

    res.status(201).json({
      success: true,
      message: 'Submission successfully received and stored',
      submissionId: result.insertId
    });
  } catch (err) {
    next(err);
  }
};

// Admin: View all submissions
exports.getAllSubmissions = async (req, res, next) => {
  try {
    const { type, status, limit = 50 } = req.query;
    let sql = 'SELECT * FROM form_submissions WHERE 1=1';
    const params = [];

    if (type) {
      sql += ' AND type = ?';
      params.push(type);
    }
    if (status) {
      sql += ' AND status = ?';
      params.push(status);
    }

    sql += ' ORDER BY created_at DESC LIMIT ?';
    params.push(Number(limit));

    const [rows] = await db.query(sql, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};
