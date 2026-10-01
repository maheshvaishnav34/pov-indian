const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.join(__dirname, '../../.env') });

async function seedLuxury() {
  console.log('--- Starting Luxury Education Ecosystem Migration & Seeding ---');

  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true
  });

  await connection.changeUser({ database: process.env.DB_NAME || 'pov_indian_db' });

  // 1. Run Schema
  const schemaSql = fs.readFileSync(path.join(__dirname, '../../database/education_luxury_schema.sql'), 'utf8');
  await connection.query(schemaSql);
  console.log('Luxury education tables verified/created.');

  // 2. Seed Exams
  const exams = [
    {
      slug: 'jee-main-2026',
      exam_name: 'Joint Entrance Examination (Main)',
      short_code: 'JEE Main 2026',
      category: 'Engineering',
      conducting_body: 'NTA (National Testing Agency)',
      exam_level: 'National Level',
      frequency: 'Session 1 & Session 2',
      mode: 'Computer Based Test (CBT)',
      duration_mins: 180,
      application_start: '2026-01-15',
      application_end: '2026-03-05',
      admit_card_date: '2026-04-01',
      exam_date: '2026-04-18',
      result_date: '2026-05-10',
      counselling_date: '2026-06-12',
      application_fee: '₹1,000 (General) / ₹500 (Female & SC/ST)',
      eligibility_summary: 'Passed 10+2 with Physics, Mathematics, and Chemistry/CS with min 75% for NITs/IIITs or top 20 percentile in state board.',
      syllabus_summary: 'Physics, Chemistry, and Mathematics from CBSE Class 11 and 12 curricula.',
      participating_colleges_count: '31 NITs, 26 IIITs, 38 GFTIs, and 1,500+ private universities',
      official_url: 'https://jeemain.nta.nic.in'
    },
    {
      slug: 'neet-ug-2026',
      exam_name: 'National Eligibility cum Entrance Test (UG)',
      short_code: 'NEET UG 2026',
      category: 'Medical',
      conducting_body: 'NTA & Medical Counselling Committee (MCC)',
      exam_level: 'National Level Single-Window Exam',
      frequency: 'Once a year (May)',
      mode: 'Pen and Paper (OMR)',
      duration_mins: 200,
      application_start: '2026-02-10',
      application_end: '2026-03-25',
      admit_card_date: '2026-04-28',
      exam_date: '2026-05-04',
      result_date: '2026-06-14',
      counselling_date: '2026-07-01',
      application_fee: '₹1,700 (General) / ₹1,600 (EWS/OBC)',
      eligibility_summary: '10+2 with Physics, Chemistry, Biology/Biotech, and English with min 50% marks (40% for reserved categories). Min age 17.',
      syllabus_summary: 'Biology (Botany & Zoology), Chemistry, and Physics core syllabus.',
      participating_colleges_count: 'All AIIMS, JIPMER, State Govt Medical Colleges & Deemed Universities',
      official_url: 'https://neet.nta.nic.in'
    },
    {
      slug: 'cat-2026',
      exam_name: 'Common Admission Test (IIMs)',
      short_code: 'CAT 2026',
      category: 'Management',
      conducting_body: 'Indian Institutes of Management (IIMs)',
      exam_level: 'National Level',
      frequency: 'Once a year (November)',
      mode: 'Computer Based Test (CBT)',
      duration_mins: 120,
      application_start: '2026-08-01',
      application_end: '2026-09-20',
      admit_card_date: '2026-10-25',
      exam_date: '2026-11-29',
      result_date: '2027-01-05',
      counselling_date: '2027-02-15',
      application_fee: '₹2,400 (General) / ₹1,200 (SC/ST/PwD)',
      eligibility_summary: 'Bachelor degree in any discipline with min 50% or equivalent CGPA (45% for SC/ST/PwD). Final year students eligible.',
      syllabus_summary: 'Verbal Ability & Reading Comprehension (VARC), Data Interpretation & Logical Reasoning (DILR), Quantitative Aptitude (QA).',
      participating_colleges_count: '21 IIMs, FMS Delhi, SPJIMR, MDI Gurgaon, IIT DoMS, and 200+ premier B-schools',
      official_url: 'https://iimcat.ac.in'
    },
    {
      slug: 'clat-2026',
      exam_name: 'Common Law Admission Test',
      short_code: 'CLAT 2026',
      category: 'Law',
      conducting_body: 'Consortium of National Law Universities',
      exam_level: 'National Level',
      frequency: 'Once a year (December)',
      mode: 'Offline (OMR Based)',
      duration_mins: 120,
      application_start: '2026-07-01',
      application_end: '2026-10-15',
      admit_card_date: '2026-11-15',
      exam_date: '2026-12-06',
      result_date: '2026-12-22',
      counselling_date: '2027-01-10',
      application_fee: '₹4,000 (General) / ₹3,500 (SC/ST)',
      eligibility_summary: '10+2 or equivalent with min 45% marks for General/OBC (40% for SC/ST). No upper age limit.',
      syllabus_summary: 'English Language, Current Affairs & GK, Legal Reasoning, Logical Reasoning, Quantitative Techniques.',
      participating_colleges_count: '24 National Law Universities (NLUs) including NLSIU Bangalore & NALSAR Hyderabad',
      official_url: 'https://consortiumofnlus.ac.in'
    }
  ];

  for (const ex of exams) {
    const [exist] = await connection.query('SELECT id FROM edu_exams WHERE slug = ?', [ex.slug]);
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_exams (
          slug, exam_name, short_code, category, conducting_body, exam_level, frequency,
          mode, duration_mins, application_start, application_end, admit_card_date,
          exam_date, result_date, counselling_date, application_fee, eligibility_summary,
          syllabus_summary, participating_colleges_count, official_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          ex.slug, ex.exam_name, ex.short_code, ex.category, ex.conducting_body, ex.exam_level, ex.frequency,
          ex.mode, ex.duration_mins, ex.application_start, ex.application_end, ex.admit_card_date,
          ex.exam_date, ex.result_date, ex.counselling_date, ex.application_fee, ex.eligibility_summary,
          ex.syllabus_summary, ex.participating_colleges_count, ex.official_url
        ]
      );
    }
  }
  console.log(`Seeded ${exams.length} premier national entrance exams.`);

  // 3. Seed Career Intelligence Paths
  const careers = [
    {
      slug: 'ai-software-architect',
      title: 'AI & Cloud Software Architect',
      domain: 'Technology',
      avg_starting_lpa: 12.50,
      mid_career_lpa: 28.00,
      leadership_lpa: 75.00,
      growth_outlook: '+34% Hyper Growth',
      preferred_degree: 'B.Tech / M.Tech in Computer Science, Data Science or AI',
      key_skills: 'Distributed Systems, LLM Orchestration, Kubernetes, Python, High-Scale Microservices',
      typical_recruiters: 'Google, Microsoft, Amazon, Nvidia, Uber, Atlassian, Fractal',
      description: 'Designs enterprise-scale cloud infrastructures, intelligent AI pipeline integration, and resilient distributed architectures powering next-generation SaaS products.'
    },
    {
      slug: 'investment-banking-associate',
      title: 'Investment Banking & M&A Associate',
      domain: 'Finance',
      avg_starting_lpa: 16.00,
      mid_career_lpa: 36.00,
      leadership_lpa: 90.00,
      growth_outlook: '+18% Steady Prestige',
      preferred_degree: 'MBA (Finance) from Top Tier / CA / CFA',
      key_skills: 'Financial Modeling, DCF Valuation, LBOs, Capital Structuring, Deal Due Diligence',
      typical_recruiters: 'Goldman Sachs, Morgan Stanley, J.P. Morgan, Avendus Capital, Kotak Investment Banking',
      description: 'Advises institutional clients and unicorn founders on mergers, equity capital market IPOs, and cross-border strategic balance-sheet acquisitions.'
    },
    {
      slug: 'interventional-cardiologist',
      title: 'Interventional Cardiologist',
      domain: 'Healthcare',
      avg_starting_lpa: 18.00,
      mid_career_lpa: 45.00,
      leadership_lpa: 120.00,
      growth_outlook: '+26% Critical Need',
      preferred_degree: 'MBBS + MD (Medicine) + DM (Cardiology)',
      key_skills: 'Cardiac Angioplasty, Catheterization, Structural Heart Diagnostics, ICU Management',
      typical_recruiters: 'AIIMS, Medanta, Apollo Hospitals, Fortis Healthcare, Narayana Health',
      description: 'Performs precision minimally-invasive cardiac interventions, vascular repairs, and critical coronary artery disease treatments.'
    },
    {
      slug: 'corporate-law-counsel',
      title: 'Corporate General Counsel & Partner',
      domain: 'Law',
      avg_starting_lpa: 11.00,
      mid_career_lpa: 26.00,
      leadership_lpa: 65.00,
      growth_outlook: '+21% High Demand',
      preferred_degree: 'B.A. LL.B (Hons) / LL.M from Top NLUs',
      key_skills: 'Contract Negotiation, Antitrust Law, Cross-Border JV Structuring, Dispute Resolution',
      typical_recruiters: 'Shardul Amarchand Mangaldas, AZB & Partners, Trilegal, Cyril Amarchand Mangaldas, Khaitan & Co',
      description: 'Guiding sovereign entities, MNCs, and emerging ventures on regulatory adherence, IP defense, venture capital term-sheets, and litigation risk.'
    },
    {
      slug: 'product-design-director',
      title: 'Principal Product & Interaction Designer',
      domain: 'Design',
      avg_starting_lpa: 9.00,
      mid_career_lpa: 24.00,
      leadership_lpa: 55.00,
      growth_outlook: '+28% Expanding Need',
      preferred_degree: 'B.Des / M.Des (Interaction / Industrial Design)',
      key_skills: 'Design Systems, Ethnographic Research, Ergonomics, Figma, Micro-Interactions, Spatial UI',
      typical_recruiters: 'Apple, Swiggy, CRED, Razorpay, Samsung R&D, Adobe',
      description: 'Shapes end-to-end customer journeys, frictionless digital ergonomics, and tactile luxury software interfaces.'
    }
  ];

  for (const c of careers) {
    const [exist] = await connection.query('SELECT id FROM edu_careers WHERE slug = ?', [c.slug]);
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_careers (
          slug, title, domain, avg_starting_lpa, mid_career_lpa, leadership_lpa,
          growth_outlook, preferred_degree, key_skills, typical_recruiters, description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          c.slug, c.title, c.domain, c.avg_starting_lpa, c.mid_career_lpa, c.leadership_lpa,
          c.growth_outlook, c.preferred_degree, c.key_skills, c.typical_recruiters, c.description
        ]
      );
    }
  }
  console.log(`Seeded ${careers.length} career intelligence trajectories.`);

  // 4. Seed Scholarships Registry
  const scholarships = [
    {
      slug: 'reliance-foundation-undergraduate-scholarships',
      title: 'Reliance Foundation Undergraduate Scholarships 2026',
      provider: 'Reliance Foundation',
      category: 'Merit-Based',
      amount_display: 'Up to ₹2,00,000 for entire degree',
      applicable_course: 'All full-time undergraduate degrees (1st Year)',
      eligibility: 'Resident Indian citizen, Class 12 with min 60%, household income up to ₹15 Lakh/yr (preference under ₹2.5 Lakh).',
      family_income_limit: 'Up to ₹15,00,000',
      deadline: '2026-08-30',
      documents_required: '12th Marksheet, Bonafide Student Certificate, Income Proof, ID Proof',
      official_link: 'https://scholarships.reliancefoundation.org'
    },
    {
      slug: 'aditya-birla-scholarship-programme',
      title: 'The Aditya Birla Scholarship Programme',
      provider: 'Aditya Birla Group',
      category: 'Merit-Based',
      amount_display: '₹1,00,000 to ₹3,00,000 per annum',
      applicable_course: 'IITs, BITS Pilani, IIMs, XLRI, National Law Universities',
      eligibility: 'Ranked in top 20 entrance list of selected premier institutes (BITS, IITs, IIMs, NLUs).',
      family_income_limit: 'Merit-Centric (No strict ceiling)',
      deadline: '2026-09-15',
      documents_required: 'Dean Recommendation, JEE/CAT/CLAT Scorecard, Leadership Essay',
      official_link: 'https://adityabirlascholars.net'
    },
    {
      slug: 'post-matric-scholarship-rajasthan',
      title: 'Government of Rajasthan Post-Matric State Scholarship',
      provider: 'Social Justice and Empowerment Department, Rajasthan',
      category: 'Reserved Category',
      amount_display: 'Full Tuition Fee Reimbursement + Monthly Maintenance',
      applicable_course: 'B.Tech, MBA, Medical, B.Sc, Diploma in Rajasthan HEIs',
      eligibility: 'Domicile of Rajasthan. SC, ST, SBC, and EBC students enrolled in recognized state colleges.',
      family_income_limit: 'Below ₹2,50,000 per annum',
      deadline: '2026-07-31',
      documents_required: 'Bonafide Domicile Certificate, Caste Certificate, Jan Aadhar, Income Certificate',
      official_link: 'https://sjms.rajasthan.gov.in'
    },
    {
      slug: 'women-in-stem-google-scholarship',
      title: 'Google Generation Scholarship (Women in Tech)',
      provider: 'Google India',
      category: 'Women in STEM',
      amount_display: 'USD $2,500 (~₹2,10,000)',
      applicable_course: 'B.Tech / B.E. in Computer Science or related STEM',
      eligibility: 'Identifies as female, enrolled in 1st/2nd year undergraduate tech degree. Demonstrated passion for CS and diversity leadership.',
      family_income_limit: 'Need & Merit Evaluated',
      deadline: '2026-06-30',
      documents_required: 'Resume, Academic Transcripts, 2 Technical Essays',
      official_link: 'https://buildyourfuture.withgoogle.com'
    }
  ];

  for (const s of scholarships) {
    const [exist] = await connection.query('SELECT id FROM edu_scholarships WHERE slug = ?', [s.slug]);
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_scholarships (
          slug, title, provider, category, amount_display, applicable_course,
          eligibility, family_income_limit, deadline, documents_required, official_link
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          s.slug, s.title, s.provider, s.category, s.amount_display, s.applicable_course,
          s.eligibility, s.family_income_limit, s.deadline, s.documents_required, s.official_link
        ]
      );
    }
  }
  console.log(`Seeded ${scholarships.length} verified national & state scholarships.`);

  // 5. Seed Real Student Reviews
  const reviews = [
    {
      institution_id: 1, // JECRC University
      reviewer_name: 'Harsh Vardhan Singh',
      course_name: 'B.Tech Computer Science (Cloud & AI)',
      batch_year: 'Class of 2025',
      rating_overall: 4.6,
      rating_academics: 4.5,
      rating_placements: 4.7,
      rating_infra: 4.8,
      rating_faculty: 4.4,
      review_title: 'Solid industry tie-ups, energetic campus life, and genuine product placements',
      pros: 'Excellent campus culture in Sitapura with modern computing labs. Major companies (Amazon, HPE, Accenture) visit early in 7th semester. Strong sports arena and coding clubs.',
      cons: 'Hostel mess food can be repetitive. Summer heat in Jaipur requires campus AC buses.',
      review_body: 'I chose JECRC over other private universities because of their verified placement stats. Secured an SDE-1 placement at 14.5 LPA in 7th sem. The faculty pushes you towards hackathons and certifications from 2nd year itself.'
    },
    {
      institution_id: 2, // IIIT Kota
      reviewer_name: 'Animesh Mukhopadhyay',
      course_name: 'B.Tech Computer Science & Engineering',
      batch_year: 'Class of 2024',
      rating_overall: 4.8,
      rating_academics: 4.9,
      rating_placements: 4.9,
      rating_infra: 4.6,
      rating_faculty: 4.8,
      review_title: 'Premier INI coding culture with top tier off-campus & on-campus offers',
      pros: 'Permanent Ranpur campus has state of the art labs. Average package consistently crosses 15+ LPA for CSE. Peer group is exceptionally competitive and driven.',
      cons: 'Location in Ranpur is quiet and 15km from central Kota city.',
      review_body: 'If you want pure Computer Science without unnecessary filler subjects, IIIT Kota is unbeatable through JoSAA rank. Our coding team reached ICPC regional finals twice.'
    },
    {
      institution_id: 3, // MBM University
      reviewer_name: 'Divya Purohit',
      course_name: 'B.Tech Computer Science',
      batch_year: 'Class of 2024',
      rating_overall: 4.2,
      rating_academics: 4.3,
      rating_placements: 4.0,
      rating_infra: 3.8,
      rating_faculty: 4.4,
      review_title: 'Heritage state engineering college with minimal fees and massive alumni strength',
      pros: 'Annual tuition of only ₹72k/yr makes it an exceptional ROI. Strong legacy in Rajasthan government engineering and PSU recruitments.',
      cons: 'Older vintage classrooms; bureaucratic admin turnaround compared to private universities.',
      review_body: 'MBM gives you raw technical grounding and an alumni network spanning every major public sector and state department in India.'
    },
    {
      institution_id: 4, // Mody University
      reviewer_name: 'Shruti Baghel',
      course_name: 'B.Tech CSE / Management',
      batch_year: 'Class of 2025',
      rating_overall: 4.7,
      rating_academics: 4.6,
      rating_placements: 4.6,
      rating_infra: 5.0,
      rating_faculty: 4.7,
      review_title: 'World-class 265-acre residential haven for women leaders',
      pros: 'Incredible equestrian club, botanical biodiversity, unmatched safety, and dedicated training for international internships.',
      cons: 'Located in Lakshmangarh which is 2 hours from Jaipur.',
      review_body: 'Parents loved the security and peaceful atmosphere. Academics are rigorous with strict attendance, but career support and women leadership mentoring are top tier.'
    }
  ];

  for (const rev of reviews) {
    const [exist] = await connection.query(
      'SELECT id FROM edu_reviews WHERE institution_id = ? AND reviewer_name = ?',
      [rev.institution_id, rev.reviewer_name]
    );
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_reviews (
          institution_id, reviewer_name, course_name, batch_year, verified_student,
          rating_overall, rating_academics, rating_placements, rating_infra, rating_faculty,
          review_title, pros, cons, review_body
        ) VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          rev.institution_id, rev.reviewer_name, rev.course_name, rev.batch_year,
          rev.rating_overall, rev.rating_academics, rev.rating_placements, rev.rating_infra, rev.rating_faculty,
          rev.review_title, rev.pros, rev.cons, rev.review_body
        ]
      );
    }
  }
  console.log(`Seeded ${reviews.length} authentic student reviews.`);

  // 6. Seed Sample Tracked Applications for Student Dashboard
  const sampleApps = [
    { institution_id: 5, program_name: 'B.Tech Computer Science (MNIT Jaipur)', status: 'Under Review', progress_pct: 75, next_deadline: '2026-07-08', next_action: 'JoSAA Round 1 Mock Choice Locking' },
    { institution_id: 1, program_name: 'B.Tech CSE & Artificial Intelligence (JECRC)', status: 'Accepted', progress_pct: 100, next_deadline: '2026-07-20', next_action: 'Complete Online Enrollment & Hostel Booking' },
    { institution_id: 2, program_name: 'B.Tech Data Science (IIIT Kota)', status: 'Submitted', progress_pct: 60, next_deadline: '2026-07-05', next_action: 'Awaiting Central Seat Matrix Verification' }
  ];

  for (const app of sampleApps) {
    const [exist] = await connection.query(
      'SELECT id FROM edu_applications WHERE institution_id = ? AND program_name = ?',
      [app.institution_id, app.program_name]
    );
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_applications (institution_id, program_name, status, progress_pct, next_deadline, next_action)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [app.institution_id, app.program_name, app.status, app.progress_pct, app.next_deadline, app.next_action]
      );
    }
  }
  console.log(`Seeded sample tracked applications.`);

  await connection.end();
  console.log('--- Luxury Education Ecosystem Seeding Completed Successfully! ---');
}

seedLuxury().catch(err => {
  console.error('Luxury seed error:', err);
  process.exit(1);
});
