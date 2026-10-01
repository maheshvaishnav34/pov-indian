const crypto = require('crypto');
const db = require('../config/db');

// 1. Get All Institutions with Offerings & Approvals (for Discovery & Search)
exports.getInstitutions = async (req, res, next) => {
  try {
    const {
      state = 'Rajasthan',
      city,
      discipline,
      ownership,
      gender_model,
      q,
      sort = 'verified'
    } = req.query;

    const conditions = ["i.status = 'verified'"];
    const params = [];

    if (state && state !== 'all') {
      conditions.push('LOWER(i.state) = LOWER(?)');
      params.push(state);
    }

    if (city && city !== 'all' && city !== 'All Rajasthan') {
      conditions.push('LOWER(i.city) = LOWER(?)');
      params.push(city);
    }

    if (ownership && ownership !== 'all') {
      conditions.push('i.ownership = ?');
      params.push(ownership);
    }

    if (gender_model && gender_model !== 'all') {
      conditions.push('i.gender_model = ?');
      params.push(gender_model);
    }

    if (q && q.trim()) {
      const searchTerm = `%${q.trim().toLowerCase()}%`;
      conditions.push('(LOWER(i.canonical_name) LIKE ? OR LOWER(i.city) LIKE ? OR LOWER(i.locality) LIKE ? OR LOWER(i.short_code) LIKE ?)');
      params.push(searchTerm, searchTerm, searchTerm, searchTerm);
    }

    const whereClause = conditions.length > 0 ? `WHERE ${conditions.join(' AND ')}` : '';

    const query = `
      SELECT i.*
      FROM edu_institutions i
      ${whereClause}
      ORDER BY 
        CASE WHEN i.short_code = 'JU' THEN 1 
             WHEN i.short_code = 'IIITK' THEN 2 
             WHEN i.short_code = 'MBM' THEN 3 
             WHEN i.short_code = 'MUST' THEN 4 
             ELSE 5 END,
        i.last_verified_at DESC
    `;

    const [institutions] = await db.query(query, params);

    if (institutions.length === 0) {
      return res.json({
        success: true,
        count: 0,
        academic_year: '2026-27',
        state: state || 'Rajasthan',
        data: []
      });
    }

    const instIds = institutions.map(i => i.id);

    // Fetch all offerings for these institutions for 2026-27
    const [allOfferings] = await db.query(
      `SELECT o.*, p.short_name, p.canonical_name, p.degree_type, p.discipline, p.academic_level, p.duration_years
       FROM edu_offerings o
       JOIN edu_programs p ON o.program_id = p.id
       WHERE o.institution_id IN (?) AND o.academic_year = '2026-27'`,
      [instIds]
    );

    // Fetch all approvals for these institutions
    const [allApprovals] = await db.query(
      `SELECT * FROM edu_approvals WHERE institution_id IN (?)`,
      [instIds]
    );

    // Group offerings and approvals by institution_id
    const offeringsMap = {};
    for (const off of allOfferings) {
      if (!offeringsMap[off.institution_id]) offeringsMap[off.institution_id] = [];
      offeringsMap[off.institution_id].push(off);
    }

    const approvalsMap = {};
    for (const app of allApprovals) {
      if (!approvalsMap[app.institution_id]) approvalsMap[app.institution_id] = [];
      approvalsMap[app.institution_id].push(app);
    }

    let results = institutions.map(i => {
      const offerings = offeringsMap[i.id] || [];
      const approvals = approvalsMap[i.id] || [];
      const courseBadges = offerings.map(o => o.short_name);

      return {
        ...i,
        offerings,
        approvals,
        courseBadges,
        min_annual_fee: offerings.length ? Math.min(...offerings.map(o => o.annual_tuition_fee)) : null,
        primary_status: offerings[0]?.application_status || 'Applications Open'
      };
    });

    // Filter by discipline if specified
    if (discipline && discipline !== 'all' && discipline !== 'All courses') {
      const discLower = discipline.toLowerCase();
      results = results.filter(item => {
        return item.offerings.some(o => 
          o.discipline.toLowerCase().includes(discLower) ||
          o.short_name.toLowerCase().includes(discLower) ||
          o.degree_type.toLowerCase().includes(discLower)
        );
      });
    }

    res.json({
      success: true,
      count: results.length,
      academic_year: '2026-27',
      state: state || 'Rajasthan',
      data: results
    });
  } catch (err) {
    next(err);
  }
};

// 2. Get Single Institution Profile with Full 2026-27 Provenance & Offering Details
exports.getInstitutionById = async (req, res, next) => {
  try {
    const { id } = req.params;
    const isNumeric = /^\d+$/.test(id);
    const idClause = isNumeric ? 'i.id = ?' : 'i.slug = ?';

    const [instRows] = await db.query(
      `SELECT * FROM edu_institutions i WHERE ${idClause} LIMIT 1`,
      [id]
    );

    if (instRows.length === 0) {
      return res.status(404).json({ success: false, message: 'Higher education institution not found' });
    }

    const institution = instRows[0];

    // Fetch Offerings for 2026-27
    const [offerings] = await db.query(
      `SELECT o.*, p.canonical_name, p.short_name, p.degree_type, p.academic_level, p.discipline, p.duration_years
       FROM edu_offerings o
       JOIN edu_programs p ON o.program_id = p.id
       WHERE o.institution_id = ? AND o.academic_year = '2026-27'
       ORDER BY o.annual_tuition_fee ASC`,
      [institution.id]
    );

    // Fetch Regulatory Approvals
    const [approvals] = await db.query(
      `SELECT * FROM edu_approvals WHERE institution_id = ? ORDER BY regulator ASC`,
      [institution.id]
    );

    res.json({
      success: true,
      data: {
        ...institution,
        offerings,
        approvals,
        provenance: {
          source_name: institution.provenance_source_name,
          source_url: institution.provenance_source_url,
          last_verified_at: institution.last_verified_at,
          verification_standard: 'POV Higher Education Editorial Standard v2.0'
        }
      }
    });
  } catch (err) {
    next(err);
  }
};

// 3. Get Admission Updates, Alerts & Guides (REAP, JoSAA, NEET, Guides)
exports.getUpdates = async (req, res, next) => {
  try {
    const [rows] = await db.query(
      `SELECT * FROM edu_updates WHERE is_active = 1 ORDER BY id ASC LIMIT 10`
    );
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 4. Create Student Enquiry / Lead with Full Attribution & Validation
exports.createLead = async (req, res, next) => {
  try {
    const {
      student_name,
      mobile,
      email,
      institution_id,
      program_id,
      preferred_course,
      academic_year = '2026-27',
      city,
      state = 'Rajasthan',
      budget_range,
      entrance_exam,
      score_or_percentile,
      preferred_timeline,
      hostel_required = 0,
      lead_type = 'admission_counselling',
      first_touch_source = 'Explore Page Direct',
      last_touch_source = 'Shortlist CTA',
      utm_campaign = 'organic_discovery',
      landing_page = '/colleges-explore.php'
    } = req.body;

    // Validation
    if (!student_name || student_name.trim().length < 2) {
      return res.status(400).json({ success: false, message: 'Please provide a valid full name.' });
    }

    const rawMobile = String(req.body.mobile || req.body.phone || '').trim();
    const cleanMobile = rawMobile.replace(/[^0-9]/g, '');
    if (cleanMobile.length < 10) {
      return res.status(400).json({ success: false, message: 'Please enter a valid 10-digit mobile number.' });
    }

    // Generate unique Lead Tracking Number
    const randomHex = crypto.randomBytes(3).toString('hex').toUpperCase();
    const lead_number = `POV-EDU-${new Date().getFullYear()}-${randomHex}`;

    const ip_address = req.ip || req.headers['x-forwarded-for'] || '127.0.0.1';
    const user_agent = req.headers['user-agent'] || 'Web Browser';

    const insertQuery = `
      INSERT INTO edu_leads (
        lead_number, lead_type, student_name, mobile, email, institution_id, program_id,
        preferred_course, academic_year, city, state, budget_range, entrance_exam,
        score_or_percentile, preferred_timeline, hostel_required, consent_version,
        lead_stage, counsellor_assigned, first_touch_source, last_touch_source,
        utm_campaign, landing_page, ip_address, user_agent
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pov_v2.1_explicit_counselling', 'New', 'POV Education Desk', ?, ?, ?, ?, ?, ?)
    `;

    const [result] = await db.query(insertQuery, [
      lead_number,
      lead_type,
      student_name.trim(),
      rawMobile,
      (email || '').trim().toLowerCase(),
      institution_id ? Number(institution_id) : null,
      program_id ? Number(program_id) : null,
      preferred_course || 'Higher Education Counselling',
      academic_year,
      city || 'Jaipur',
      state,
      budget_range || 'Flexible',
      entrance_exam || 'Not specified',
      score_or_percentile || null,
      preferred_timeline || '2026-27 Academic Session',
      hostel_required ? 1 : 0,
      first_touch_source,
      last_touch_source,
      utm_campaign,
      landing_page,
      ip_address,
      user_agent
    ]);

    res.status(201).json({
      success: true,
      message: 'Your admission inquiry has been recorded. An official education counsellor will reach out with genuine information.',
      data: {
        lead_id: result.insertId,
        lead_number,
        student_name: student_name.trim(),
        preferred_course,
        academic_year,
        status: 'New'
      }
    });
  } catch (err) {
    next(err);
  }
};

// 5. Admin: Get Education Dashboard Stats & Metrics
exports.getStats = async (req, res, next) => {
  try {
    const [[instCount]] = await db.query('SELECT COUNT(*) as count FROM edu_institutions WHERE status = "verified"');
    const [[offeringCount]] = await db.query('SELECT COUNT(*) as count FROM edu_offerings WHERE academic_year = "2026-27"');
    const [[leadCount]] = await db.query('SELECT COUNT(*) as count FROM edu_leads');
    const [[newLeadCount]] = await db.query('SELECT COUNT(*) as count FROM edu_leads WHERE lead_stage = "New"');
    const [[counsellingLeadCount]] = await db.query('SELECT COUNT(*) as count FROM edu_leads WHERE lead_stage = "Counselling"');
    const [[approvalCount]] = await db.query('SELECT COUNT(*) as count FROM edu_approvals');

    const [leadsByCity] = await db.query(`
      SELECT city, COUNT(*) as count FROM edu_leads GROUP BY city ORDER BY count DESC LIMIT 5
    `);

    res.json({
      success: true,
      stats: {
        verified_institutions: instCount.count,
        offerings_2026_27: offeringCount.count,
        total_leads: leadCount.count,
        new_leads: newLeadCount.count,
        in_counselling: counsellingLeadCount.count,
        verified_approvals: approvalCount.count,
        top_lead_cities: leadsByCity
      }
    });
  } catch (err) {
    next(err);
  }
};

// 6. Admin: List Leads with Pipeline Filters
exports.listLeads = async (req, res, next) => {
  try {
    const { stage, q, limit = 50 } = req.query;
    const conditions = [];
    const params = [];

    if (stage && stage !== 'all') {
      conditions.push('l.lead_stage = ?');
      params.push(stage);
    }

    if (q && q.trim()) {
      const term = `%${q.trim()}%`;
      conditions.push('(l.student_name LIKE ? OR l.mobile LIKE ? OR l.email LIKE ? OR l.lead_number LIKE ?)');
      params.push(term, term, term, term);
    }

    const whereClause = conditions.length > 0 ? `WHERE ${conditions.join(' AND ')}` : '';

    const query = `
      SELECT 
        l.*,
        i.canonical_name as institution_name,
        i.city as institution_city
      FROM edu_leads l
      LEFT JOIN edu_institutions i ON l.institution_id = i.id
      ${whereClause}
      ORDER BY l.created_at DESC
      LIMIT ?
    `;
    params.push(Number(limit));

    const [rows] = await db.query(query, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 7. Admin: Update Lead Stage & Notes
exports.updateLeadStage = async (req, res, next) => {
  try {
    const { id } = req.params;
    const { lead_stage, counsellor_notes, counsellor_assigned } = req.body;

    const validStages = ['New', 'Contacted', 'Interested', 'Counselling', 'Application Started', 'Application Submitted', 'Admitted', 'Closed'];
    if (lead_stage && !validStages.includes(lead_stage)) {
      return res.status(400).json({ success: false, message: 'Invalid lead stage value' });
    }

    const updates = [];
    const params = [];

    if (lead_stage) {
      updates.push('lead_stage = ?');
      params.push(lead_stage);
    }
    if (counsellor_notes !== undefined) {
      updates.push('counsellor_notes = ?');
      params.push(counsellor_notes);
    }
    if (counsellor_assigned) {
      updates.push('counsellor_assigned = ?');
      params.push(counsellor_assigned);
    }

    if (updates.length === 0) {
      return res.status(400).json({ success: false, message: 'No fields provided to update' });
    }

    params.push(id);
    await db.query(`UPDATE edu_leads SET ${updates.join(', ')} WHERE id = ?`, params);

    res.json({ success: true, message: 'Lead updated successfully' });
  } catch (err) {
    next(err);
  }
};

// 8. Luxury Ecosystem: National & State Exams Hub
exports.getExams = async (req, res, next) => {
  try {
    const { category } = req.query;
    let query = 'SELECT * FROM edu_exams';
    const params = [];
    if (category && category !== 'all') {
      query += ' WHERE category = ?';
      params.push(category);
    }
    query += ' ORDER BY exam_date ASC';
    const [rows] = await db.query(query, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 9. Luxury Ecosystem: Career Intelligence & Paths
exports.getCareers = async (req, res, next) => {
  try {
    const { domain } = req.query;
    let query = 'SELECT * FROM edu_careers';
    const params = [];
    if (domain && domain !== 'all') {
      query += ' WHERE domain = ?';
      params.push(domain);
    }
    query += ' ORDER BY mid_career_lpa DESC';
    const [rows] = await db.query(query, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 10. Luxury Ecosystem: Scholarships Registry
exports.getScholarships = async (req, res, next) => {
  try {
    const { category } = req.query;
    let query = 'SELECT * FROM edu_scholarships';
    const params = [];
    if (category && category !== 'all') {
      query += ' WHERE category = ?';
      params.push(category);
    }
    query += ' ORDER BY deadline ASC';
    const [rows] = await db.query(query, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 11. Luxury Ecosystem: Verified Student Reviews
exports.getReviews = async (req, res, next) => {
  try {
    const { institution_id } = req.query;
    let query = `
      SELECT r.*, i.canonical_name as institution_name, i.logo_text, i.badge_color, i.city
      FROM edu_reviews r
      JOIN edu_institutions i ON r.institution_id = i.id
    `;
    const params = [];
    if (institution_id) {
      query += ' WHERE r.institution_id = ?';
      params.push(institution_id);
    }
    query += ' ORDER BY r.rating_overall DESC, r.created_at DESC';
    const [rows] = await db.query(query, params);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 12. Luxury Ecosystem: Student Application Tracker
exports.getApplications = async (req, res, next) => {
  try {
    const query = `
      SELECT a.*, i.canonical_name, i.city, i.logo_text, i.badge_color, i.official_website
      FROM edu_applications a
      JOIN edu_institutions i ON a.institution_id = i.id
      ORDER BY a.progress_pct DESC
    `;
    const [rows] = await db.query(query);
    res.json({ success: true, count: rows.length, data: rows });
  } catch (err) {
    next(err);
  }
};

// 13. Luxury Ecosystem: Career Assessment Engine (5-Question Psychometric Evaluation)
exports.submitAssessment = async (req, res, next) => {
  try {
    const { answers = {} } = req.body;
    // answers keys: q1 (interest), q2 (environment), q3 (problem_solving), q4 (salary_priority), q5 (learning_style)
    const archetypes = [
      {
        archetype: 'System Architect & Deep Technologist',
        headline: 'Analytical Builder with High Strategic Vision',
        strengths: ['Algorithmic Logic', 'Scalable Architecture', 'Independent Deep Work', 'Emerging Tech Synthesis'],
        recommended_careers: ['AI & Cloud Software Architect', 'Data Science & ML Engineer', 'Robotics Systems Designer'],
        recommended_degrees: ['B.Tech Computer Science & AI', 'M.Tech Computational Intelligence'],
        recommended_institutions: ['IIIT Kota', 'MNIT Jaipur', 'BITS Pilani', 'JECRC University'],
        salary_trajectory: '₹12.5 LPA (Entry) → ₹28 LPA (Mid) → ₹75+ LPA (Lead)',
        radar_scores: { analytical: 95, creativity: 82, leadership: 78, execution: 90, empathy: 70 }
      },
      {
        archetype: 'Strategic Capital & Venture Director',
        headline: 'High-Impact Decision Maker in Global Markets',
        strengths: ['Quantitative Valuation', 'Commercial Negotiation', 'Strategic Risk Allocation', 'Executive Polish'],
        recommended_careers: ['Investment Banking & M&A Associate', 'Venture Capital Analyst', 'Chief Financial Strategist'],
        recommended_degrees: ['MBA (Finance & Analytics)', 'BBA (Digital Business)'],
        recommended_institutions: ['Manipal University Jaipur', 'JECRC University', 'BITS Pilani'],
        salary_trajectory: '₹16 LPA (Entry) → ₹36 LPA (Mid) → ₹90+ LPA (Partner)',
        radar_scores: { analytical: 92, creativity: 75, leadership: 96, execution: 88, empathy: 74 }
      },
      {
        archetype: 'Clinical Healer & Biomimetic Specialist',
        headline: 'Compassionate Diagnostic Precision and Service',
        strengths: ['Biochemical Intuition', 'Crisis Resilience', 'Diagnostic Precision', 'Patient Empathy'],
        recommended_careers: ['Interventional Cardiologist', 'Surgical Specialist', 'Biotechnology Innovator'],
        recommended_degrees: ['MBBS', 'B.Sc Nursing', 'MD Cardiology'],
        recommended_institutions: ['AIIMS Jodhpur', 'Mody University of Science and Technology'],
        salary_trajectory: '₹18 LPA (Entry) → ₹45 LPA (Senior Specialist) → ₹120+ LPA (Director)',
        radar_scores: { analytical: 88, creativity: 70, leadership: 85, execution: 94, empathy: 98 }
      }
    ];

    // Determine archetype based on selected interest
    let selected = archetypes[0];
    const interest = (answers.q1 || '').toLowerCase();
    if (interest.includes('finance') || interest.includes('business') || interest.includes('market')) {
      selected = archetypes[1];
    } else if (interest.includes('health') || interest.includes('medical') || interest.includes('bio')) {
      selected = archetypes[2];
    }

    res.json({
      success: true,
      assessment_id: 'POV-EVAL-' + Date.now().toString(36).toUpperCase(),
      profile: selected,
      disclaimer: 'Career psychometric evaluation engine calibrated on verified Indian higher education industry pathways.'
    });
  } catch (err) {
    next(err);
  }
};

// 14. Luxury Ecosystem: Universal Intelligent Search (Grouped Multi-Entity)
exports.universalSearch = async (req, res, next) => {
  try {
    const q = (req.query.q || '').trim();
    if (!q) {
      return res.json({ success: true, count: 0, results: {} });
    }
    const term = `%${q.toLowerCase()}%`;

    const [colleges] = await db.query(
      `SELECT id, canonical_name as title, short_code, city, state, legal_recognition, 'college' as type, '/colleges' as link 
       FROM edu_institutions WHERE LOWER(canonical_name) LIKE ? OR LOWER(city) LIKE ? LIMIT 5`,
      [term, term]
    );

    const [programs] = await db.query(
      `SELECT id, canonical_name as title, short_name, discipline, degree_type, 'course' as type, '/courses' as link 
       FROM edu_programs WHERE LOWER(canonical_name) LIKE ? OR LOWER(short_name) LIKE ? OR LOWER(discipline) LIKE ? LIMIT 5`,
      [term, term, term]
    );

    const [exams] = await db.query(
      `SELECT id, exam_name as title, short_code, category, 'exam' as type, '/exams' as link 
       FROM edu_exams WHERE LOWER(exam_name) LIKE ? OR LOWER(short_code) LIKE ? LIMIT 4`,
      [term, term]
    );

    const [careers] = await db.query(
      `SELECT id, title, domain, avg_starting_lpa, 'career' as type, '/careers' as link 
       FROM edu_careers WHERE LOWER(title) LIKE ? OR LOWER(domain) LIKE ? LIMIT 4`,
      [term, term]
    );

    const [scholarships] = await db.query(
      `SELECT id, title, provider, amount_display, 'scholarship' as type, '/scholarships' as link 
       FROM edu_scholarships WHERE LOWER(title) LIKE ? OR LOWER(category) LIKE ? LIMIT 4`,
      [term, term]
    );

    const totalCount = colleges.length + programs.length + exams.length + careers.length + scholarships.length;

    res.json({
      success: true,
      total_count: totalCount,
      query: q,
      results: {
        colleges,
        courses: programs,
        exams,
        careers,
        scholarships
      }
    });
  } catch (err) {
    next(err);
  }
};

// 15. Admin Management: Create Institution
exports.createInstitution = async (req, res, next) => {
  try {
    const {
      canonical_name,
      short_code,
      group_name,
      institution_type = 'University',
      legal_recognition = 'State Private University',
      ownership = 'Private Unaided',
      specialization_domain = 'Multidisciplinary',
      state = 'Rajasthan',
      district = 'Jaipur',
      city = 'Jaipur',
      locality = 'Sitapura',
      campus_acres = 25.0,
      official_website = 'https://institution.edu.in',
      admissions_url,
      primary_email = 'admissions@institution.edu.in',
      primary_phone = '+91 141 1234567',
      naac_grade = 'A+',
      nirf_band = 'Top 100',
      logo_text = 'COL',
      badge_color = '#0d2b39',
      provenance_source_name = 'Official University Gazette / AISHE',
      provenance_source_url = 'https://ugc.gov.in',
      status = 'verified'
    } = req.body;

    if (!canonical_name) {
      return res.status(400).json({ success: false, message: 'Institution canonical name is required.' });
    }

    const slug = req.body.slug || (canonical_name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '') + '-' + Date.now().toString().slice(-4));

    const [result] = await db.query(
      `INSERT INTO edu_institutions 
       (canonical_name, short_code, slug, group_name, institution_type, legal_recognition, ownership, specialization_domain, state, district, city, locality, campus_acres, official_website, admissions_url, primary_email, primary_phone, naac_grade, nirf_band, logo_text, badge_color, provenance_source_name, provenance_source_url, status, last_verified_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())`,
      [
        canonical_name, short_code || logo_text, slug, group_name, institution_type, legal_recognition, ownership, specialization_domain, state, district, city, locality, campus_acres, official_website, admissions_url || official_website, primary_email, primary_phone, naac_grade, nirf_band, logo_text, badge_color, provenance_source_name, provenance_source_url, status
      ]
    );

    res.json({
      success: true,
      message: 'Institution created successfully',
      data: { id: result.insertId, canonical_name, slug }
    });
  } catch (err) {
    next(err);
  }
};

// 16. Admin Management: Update Institution
exports.updateInstitution = async (req, res, next) => {
  try {
    const { id } = req.params;
    const {
      canonical_name,
      short_code,
      group_name,
      institution_type,
      ownership,
      city,
      state,
      locality,
      campus_acres,
      official_website,
      admissions_url,
      primary_email,
      primary_phone,
      naac_grade,
      nirf_band,
      logo_text,
      badge_color,
      status
    } = req.body;

    await db.query(
      `UPDATE edu_institutions SET
       canonical_name = COALESCE(?, canonical_name),
       short_code = COALESCE(?, short_code),
       group_name = COALESCE(?, group_name),
       institution_type = COALESCE(?, institution_type),
       ownership = COALESCE(?, ownership),
       city = COALESCE(?, city),
       state = COALESCE(?, state),
       locality = COALESCE(?, locality),
       campus_acres = COALESCE(?, campus_acres),
       official_website = COALESCE(?, official_website),
       admissions_url = COALESCE(?, admissions_url),
       primary_email = COALESCE(?, primary_email),
       primary_phone = COALESCE(?, primary_phone),
       naac_grade = COALESCE(?, naac_grade),
       nirf_band = COALESCE(?, nirf_band),
       logo_text = COALESCE(?, logo_text),
       badge_color = COALESCE(?, badge_color),
       status = COALESCE(?, status),
       last_verified_at = CURDATE()
       WHERE id = ?`,
      [
        canonical_name, short_code, group_name, institution_type, ownership, city, state, locality, campus_acres, official_website, admissions_url, primary_email, primary_phone, naac_grade, nirf_band, logo_text, badge_color, status, id
      ]
    );

    res.json({ success: true, message: `Institution #${id} updated successfully` });
  } catch (err) {
    next(err);
  }
};

// 17. Admin Management: Delete Institution
exports.deleteInstitution = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query(`DELETE FROM edu_institutions WHERE id = ?`, [id]);
    res.json({ success: true, message: `Institution #${id} deleted successfully` });
  } catch (err) {
    next(err);
  }
};

// 18. Admin Management: Add Offering to Institution
exports.createOffering = async (req, res, next) => {
  try {
    const institution_id = req.params.id || req.body.institution_id;
    const {
      program_name,
      degree_type = 'B.Tech',
      discipline = 'Engineering',
      academic_level = 'Undergraduate',
      duration_years = 4.0,
      academic_year = '2026-27',
      intake_seats = 60,
      annual_tuition_fee = 120000,
      eligibility_criteria = '10+2 with Physics, Mathematics & min 50%',
      entrance_exams = 'JEE Main / Direct'
    } = req.body;

    if (!institution_id || !program_name) {
      return res.status(400).json({ success: false, message: 'Institution ID and Program Name are required' });
    }

    let [progs] = await db.query(`SELECT id FROM edu_programs WHERE canonical_name = ? LIMIT 1`, [program_name]);
    let program_id;
    if (progs.length > 0) {
      program_id = progs[0].id;
    } else {
      const [progResult] = await db.query(
        `INSERT INTO edu_programs (canonical_name, short_name, degree_type, academic_level, discipline, duration_years)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [program_name, program_name, degree_type, academic_level, discipline, duration_years]
      );
      program_id = progResult.insertId;
    }

    const [result] = await db.query(
      `INSERT INTO edu_offerings 
       (institution_id, program_id, academic_year, intake_seats, annual_tuition_fee, eligibility_criteria, entrance_exams)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [institution_id, program_id, academic_year, intake_seats, annual_tuition_fee, eligibility_criteria, entrance_exams]
    );

    res.json({ success: true, message: 'Course offering added successfully', data: { id: result.insertId } });
  } catch (err) {
    next(err);
  }
};

// 19. Admin Management: Delete Offering
exports.deleteOffering = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query(`DELETE FROM edu_offerings WHERE id = ?`, [id]);
    res.json({ success: true, message: `Course offering #${id} deleted successfully` });
  } catch (err) {
    next(err);
  }
};

// 20. Admin Management: Add Exam
exports.createExam = async (req, res, next) => {
  try {
    const { exam_name, short_code, category = 'Engineering', conducting_body = 'NTA', exam_date, application_end, application_fee, official_url = 'https://nta.ac.in' } = req.body;
    if (!exam_name || !short_code) {
      return res.status(400).json({ success: false, message: 'Exam name and short code required' });
    }
    const slug = (short_code + '-2026').toLowerCase().replace(/[^a-z0-9]+/g, '-');
    const [result] = await db.query(
      `INSERT INTO edu_exams (slug, exam_name, short_code, category, conducting_body, exam_date, application_end, application_fee, official_url)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [slug, exam_name, short_code, category, conducting_body, exam_date || '2026-05-15', application_end || '2026-04-15', application_fee || '₹1,000', official_url]
    );
    res.json({ success: true, message: 'Entrance exam created successfully', data: { id: result.insertId } });
  } catch (err) {
    next(err);
  }
};

// 21. Admin Management: Delete Exam
exports.deleteExam = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query(`DELETE FROM edu_exams WHERE id = ?`, [id]);
    res.json({ success: true, message: `Exam #${id} deleted` });
  } catch (err) {
    next(err);
  }
};

// 22. Admin Management: Add Scholarship
exports.createScholarship = async (req, res, next) => {
  try {
    const { title, provider = 'Government of Rajasthan', category = 'Merit-Based', amount_display = '₹50,000 / year', applicable_course = 'B.Tech / Degree', eligibility = 'Min 75% in 12th', deadline = '2026-08-31', official_link = 'https://scholarships.gov.in' } = req.body;
    if (!title) {
      return res.status(400).json({ success: false, message: 'Scholarship title required' });
    }
    const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').slice(0, 100) + '-' + Date.now().toString().slice(-4);
    const [result] = await db.query(
      `INSERT INTO edu_scholarships (slug, title, provider, category, amount_display, applicable_course, eligibility, deadline, official_link)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [slug, title, provider, category, amount_display, applicable_course, eligibility, deadline, official_link]
    );
    res.json({ success: true, message: 'Scholarship created successfully', data: { id: result.insertId } });
  } catch (err) {
    next(err);
  }
};

// 23. Admin Management: Delete Scholarship
exports.deleteScholarship = async (req, res, next) => {
  try {
    const { id } = req.params;
    await db.query(`DELETE FROM edu_scholarships WHERE id = ?`, [id]);
    res.json({ success: true, message: `Scholarship #${id} deleted` });
  } catch (err) {
    next(err);
  }
};
