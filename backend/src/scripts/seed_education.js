const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.join(__dirname, '../../.env') });

async function seedEducation() {
  console.log('=== Starting POV Indian Higher Education Data Seeding ===');

  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true
  });

  await connection.changeUser({ database: process.env.DB_NAME || 'pov_indian_db' });

  // 1. Run Schema Creation
  const schemaSql = fs.readFileSync(path.join(__dirname, '../../database/education_schema.sql'), 'utf8');
  await connection.query(schemaSql);
  console.log('Education schema verified/created successfully.');

  // 2. Seed Reusable Canonical Programs
  const programs = [
    { canonical_name: 'Bachelor of Technology in Computer Science and Engineering', short_name: 'B.Tech CSE', degree_type: 'B.Tech', academic_level: 'Undergraduate', discipline: 'Engineering', specialization: 'Computer Science & AI', duration_years: 4.0 },
    { canonical_name: 'Bachelor of Technology in Data Science & Artificial Intelligence', short_name: 'B.Tech Data Science', degree_type: 'B.Tech', academic_level: 'Undergraduate', discipline: 'Engineering', specialization: 'Data Science', duration_years: 4.0 },
    { canonical_name: 'Master of Business Administration', short_name: 'MBA', degree_type: 'MBA', academic_level: 'Postgraduate', discipline: 'Business', specialization: 'Finance, Marketing & Analytics', duration_years: 2.0 },
    { canonical_name: 'Bachelor of Design in Communication & UI/UX', short_name: 'Design (B.Des)', degree_type: 'B.Des', academic_level: 'Undergraduate', discipline: 'Design', specialization: 'Interaction Design', duration_years: 4.0 },
    { canonical_name: 'Bachelor of Architecture', short_name: 'Architecture (B.Arch)', degree_type: 'B.Arch', academic_level: 'Undergraduate', discipline: 'Engineering', specialization: 'Sustainable Architecture', duration_years: 5.0 },
    { canonical_name: 'Bachelor of Science in Nursing', short_name: 'Nursing (B.Sc)', degree_type: 'B.Sc', academic_level: 'Undergraduate', discipline: 'Healthcare', specialization: 'General Nursing & Midwifery', duration_years: 4.0 },
    { canonical_name: 'Bachelor of Business Administration', short_name: 'BBA', degree_type: 'BBA', academic_level: 'Undergraduate', discipline: 'Business', specialization: 'Digital Enterprise', duration_years: 3.0 },
    { canonical_name: 'Bachelor of Medicine, Bachelor of Surgery', short_name: 'MBBS', degree_type: 'MBBS', academic_level: 'Undergraduate', discipline: 'Healthcare', specialization: 'Clinical Medicine', duration_years: 5.5 }
  ];

  for (const prog of programs) {
    await connection.query(
      `INSERT INTO edu_programs (canonical_name, short_name, degree_type, academic_level, discipline, specialization, duration_years)
       VALUES (?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE short_name = VALUES(short_name), discipline = VALUES(discipline)`,
      [prog.canonical_name, prog.short_name, prog.degree_type, prog.academic_level, prog.discipline, prog.specialization, prog.duration_years]
    );
  }
  console.log('Seeded academic programs.');

  // Fetch Program IDs
  const [progRows] = await connection.query('SELECT id, short_name FROM edu_programs');
  const progMap = Object.fromEntries(progRows.map(r => [r.short_name, r.id]));

  // 3. Seed Institutions (Featuring all colleges shown in Images 1-4)
  const institutions = [
    {
      canonical_name: 'JECRC University',
      short_code: 'JU',
      slug: 'jecrc-university-jaipur',
      former_names: null,
      group_name: 'JECRC Foundation',
      institution_type: 'University',
      legal_recognition: 'State Private University (Rajasthan Vidhan Sabha Act No. 15)',
      relationship: 'Independent',
      ownership: 'Private Unaided',
      specialization_domain: 'Engineering, Management & Design',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 2012,
      state: 'Rajasthan',
      district: 'Jaipur',
      city: 'Jaipur',
      locality: 'Sitapura Industrial Area',
      pincode: '302022',
      address: 'Plot No. IS-2036 to 2039, Ramchandrapura Industrial Area, Sitapura, Jaipur, Rajasthan 302022',
      latitude: 26.7821,
      longitude: 75.8643,
      campus_acres: 32.0,
      official_website: 'https://jecrcuniversity.edu.in',
      admissions_url: 'https://jecrcuniversity.edu.in/admissions-2026',
      primary_email: 'admissions@jecrcuniversity.edu.in',
      primary_phone: '+91 141 6565656',
      naac_grade: 'NAAC Accredited',
      nirf_band: 'Ranked in Top 100 Innovation',
      logo_text: 'JU',
      badge_color: '#d97746',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'Official University Admissions Notice 2026-27',
      provenance_source_url: 'https://jecrcuniversity.edu.in/admission-bulletin-2026',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Section 2(f) Recognized State University', ref_number: 'F.No. 8-23/2012 (CPP-I/PU)', effective_year: '2026-27', evidence_document_url: 'https://ugc.gov.in/privatestateuniversity.aspx' },
        { regulator: 'AICTE', approval_type: 'Approved for Technical & Management Programs', ref_number: 'North-West/1-109743/2026/EOA', effective_year: '2026-27', evidence_document_url: 'https://facilities.aicte-india.org/dashboard' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 175000, intake: 360, eligibility: '10+2 with min 60% in PCM. Valid JEE Main / REAP / JEST score.', exams: 'JEE Main / REAP / Direct Merit', status: 'Applications Open', deadline: '2026-07-25', source_title: 'JU Academic Prospectus 2026-27', verified_date: '2026-06-18' },
        { program_short: 'MBA', annual_tuition_fee: 165000, intake: 120, eligibility: 'Graduation in any stream with min 50%. CAT/MAT/CMAT or University GD-PI.', exams: 'CAT / MAT / CMAT', status: 'Applications Open', deadline: '2026-07-20', source_title: 'JU School of Management Prospectus', verified_date: '2026-06-18' },
        { program_short: 'Design (B.Des)', annual_tuition_fee: 145000, intake: 60, eligibility: '10+2 with 50% from recognized board + Design Aptitude Test.', exams: 'DAT / UCEED', status: 'Applications Open', deadline: '2026-07-15', source_title: 'JU Design Faculty Circular', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'Indian Institute of Information Technology Kota',
      short_code: 'IIITK',
      slug: 'iiit-kota',
      former_names: 'IIIT Kota (transit campus at MNIT)',
      group_name: 'Institutes of National Importance',
      institution_type: 'Standalone HEI',
      legal_recognition: 'Institute of National Importance (IIIT Public-Private Partnership Act, 2017)',
      relationship: 'Independent',
      ownership: 'Institute of National Importance',
      specialization_domain: 'Information Technology, Computer Science & Electronics',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 2013,
      state: 'Rajasthan',
      district: 'Kota',
      city: 'Kota',
      locality: 'Ranpur Permanent Campus',
      pincode: '325003',
      address: 'Permanent Campus, RIICO Industrial Area, Ranpur, Kota, Rajasthan 325003',
      latitude: 25.1054,
      longitude: 75.8341,
      campus_acres: 100.3,
      official_website: 'https://iiitkota.ac.in',
      admissions_url: 'https://iiitkota.ac.in/admission-btech',
      primary_email: 'office@iiitkota.ac.in',
      primary_phone: '+91 744 2690000',
      naac_grade: 'Autonomous INI',
      nirf_band: 'Ranked in Top 100 NIRF Engineering',
      logo_text: 'IIITK',
      badge_color: '#1b4d63',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'CSAB / JoSAA 2026 Central Seat Matrix',
      provenance_source_url: 'https://josaa.nic.in/seatmatrix2026',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Institute of National Importance by Act of Parliament', ref_number: 'MoE / INI-IIIT-2017', effective_year: '2026-27', evidence_document_url: 'https://education.gov.in' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 220000, intake: 180, eligibility: '10+2 with Physics, Mathematics + JEE Main Rank qualifying for JoSAA.', exams: 'JEE Main via JoSAA / CSAB', status: 'Counselling Soon', deadline: '2026-07-05', source_title: 'JoSAA Business Rules 2026', verified_date: '2026-06-18' },
        { program_short: 'B.Tech Data Science', annual_tuition_fee: 220000, intake: 60, eligibility: '10+2 with PCM, JEE Main JoSAA allocation.', exams: 'JEE Main via JoSAA', status: 'Counselling Soon', deadline: '2026-07-05', source_title: 'JoSAA Business Rules 2026', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'MBM University',
      short_code: 'MBM',
      slug: 'mbm-university-jodhpur',
      former_names: 'M.B.M. Engineering College Jodhpur (Est. 1951)',
      group_name: 'State Government Technical Universities',
      institution_type: 'University',
      legal_recognition: 'State Public University (Government of Rajasthan Act No. 20 of 2021)',
      relationship: 'Independent',
      ownership: 'State Government',
      specialization_domain: 'Engineering, Technology & Applied Sciences',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 1951,
      state: 'Rajasthan',
      district: 'Jodhpur',
      city: 'Jodhpur',
      locality: 'Ratanada',
      pincode: '342011',
      address: 'Air Force Area, Ratanada, Jodhpur, Rajasthan 342011',
      latitude: 26.2680,
      longitude: 73.0360,
      campus_acres: 90.0,
      official_website: 'https://mbm.ac.in',
      admissions_url: 'https://mbm.ac.in/reap-admissions-2026',
      primary_email: 'registrar@mbm.ac.in',
      primary_phone: '+91 291 2515002',
      naac_grade: 'State Govt University',
      nirf_band: 'Heritage Engineering Institution',
      logo_text: 'MBM',
      badge_color: '#9c4221',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'Rajasthan REAP 2026 Information Bulletin',
      provenance_source_url: 'https://reap2026.rajasthan.gov.in',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'State Public University 2(f)', ref_number: 'Govt. Raj Act 20/2021', effective_year: '2026-27', evidence_document_url: 'https://ugc.gov.in' },
        { regulator: 'AICTE', approval_type: 'AICTE Approved Government Technical Institution', ref_number: 'North-West/Govt-MBM/2026', effective_year: '2026-27', evidence_document_url: 'https://facilities.aicte-india.org' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 72000, intake: 120, eligibility: '10+2 with PCM (min 45% for Gen, 40% reserved categories) via REAP Rajasthan merit.', exams: 'REAP 2026 / JEE Main Score', status: 'Counselling Soon', deadline: '2026-07-18', source_title: 'REAP 2026 Seat Matrix & Fees Gazette', verified_date: '2026-06-18' },
        { program_short: 'Architecture (B.Arch)', annual_tuition_fee: 65000, intake: 40, eligibility: '10+2 with Physics, Chem, Math + Valid NATA Score.', exams: 'NATA 2026 / REAP', status: 'Counselling Soon', deadline: '2026-07-18', source_title: 'Council of Architecture Gazette', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'Mody University of Science and Technology',
      short_code: 'MUST',
      slug: 'mody-university-sikar',
      former_names: 'Mody Institute of Technology and Science',
      group_name: 'Mody Education Foundation',
      institution_type: 'University',
      legal_recognition: 'State Private University for Women',
      relationship: 'Independent',
      ownership: 'Private Unaided',
      specialization_domain: 'Engineering, Management, Law & Nursing for Women',
      delivery_mode: 'On-campus',
      gender_model: 'Women',
      est_year: 1998,
      state: 'Rajasthan',
      district: 'Sikar',
      city: 'Sikar',
      locality: 'Lakshmangarh',
      pincode: '332311',
      address: 'Sikar Road, Lakshmangarh, District Sikar, Rajasthan 332311',
      latitude: 27.8180,
      longitude: 75.0320,
      campus_acres: 265.0,
      official_website: 'https://modyuniversity.ac.in',
      admissions_url: 'https://modyuniversity.ac.in/apply-now-2026',
      primary_email: 'admissions@modyuniversity.ac.in',
      primary_phone: '+91 1573 225001',
      naac_grade: 'NAAC A+',
      nirf_band: 'Excellence in Women Higher Education',
      logo_text: 'MUST',
      badge_color: '#4a6750',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'University Admissions Page 2026-27',
      provenance_source_url: 'https://modyuniversity.ac.in/academic-bulletin-2026',
      last_verified_at: '2026-06-09',
      approvals: [
        { regulator: 'UGC', approval_type: 'State Private University under Section 2(f)', ref_number: 'F.9-37/2004(CPP-I)', effective_year: '2026-27', evidence_document_url: 'https://ugc.gov.in' },
        { regulator: 'INC', approval_type: 'Indian Nursing Council Approved College of Nursing', ref_number: 'INC-MUST-2026', effective_year: '2026-27', evidence_document_url: 'https://indiannursingcouncil.org' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 195000, intake: 180, eligibility: '10+2 PCM min 55% for women candidates. JEE Main / MEET / Direct Merit.', exams: 'JEE Main / MEET 2026', status: 'Applications Open', deadline: '2026-07-30', source_title: 'Mody University Admission Notice', verified_date: '2026-06-09' },
        { program_short: 'MBA', annual_tuition_fee: 170000, intake: 60, eligibility: 'Bachelor degree in any discipline with min 50%. CAT/MAT/CMAT.', exams: 'CAT / MAT / CMAT', status: 'Applications Open', deadline: '2026-07-25', source_title: 'Mody University Admission Notice', verified_date: '2026-06-09' },
        { program_short: 'Nursing (B.Sc)', annual_tuition_fee: 110000, intake: 60, eligibility: '10+2 with PCB and English with min 50% for female candidates.', exams: 'Rajasthan State Nursing Entrance / Merit', status: 'Applications Open', deadline: '2026-07-20', source_title: 'INC & MUST Nursing Bulletin', verified_date: '2026-06-09' }
      ]
    },
    {
      canonical_name: 'Malaviya National Institute of Technology Jaipur',
      short_code: 'MNIT',
      slug: 'mnit-jaipur',
      former_names: 'Malaviya Regional Engineering College (MREC)',
      group_name: 'National Institutes of Technology (NITs)',
      institution_type: 'Standalone HEI',
      legal_recognition: 'Institute of National Importance (NITSER Act, 2007)',
      relationship: 'Independent',
      ownership: 'Institute of National Importance',
      specialization_domain: 'Engineering, Technology, Sciences & Management',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 1963,
      state: 'Rajasthan',
      district: 'Jaipur',
      city: 'Jaipur',
      locality: 'Jawahar Lal Nehru Marg, Malviya Nagar',
      pincode: '302017',
      address: 'JLN Marg, Malviya Nagar, Jaipur, Rajasthan 302017',
      latitude: 26.8640,
      longitude: 75.8110,
      campus_acres: 317.0,
      official_website: 'https://mnit.ac.in',
      admissions_url: 'https://mnit.ac.in/admissions',
      primary_email: 'admissions@mnit.ac.in',
      primary_phone: '+91 141 2529078',
      naac_grade: 'Autonomous INI',
      nirf_band: 'NIRF Engineering Rank 37',
      logo_text: 'MNIT',
      badge_color: '#0d2b39',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'JoSAA / CSAB 2026 Central Portal',
      provenance_source_url: 'https://josaa.nic.in',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Institute of National Importance under Ministry of Education', ref_number: 'NITSER Act 2007', effective_year: '2026-27', evidence_document_url: 'https://education.gov.in' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 145000, intake: 120, eligibility: '10+2 with PCM + top JEE Main percentile qualified for JoSAA seat allocation.', exams: 'JEE Main (JoSAA/CSAB/DASA)', status: 'Counselling Soon', deadline: '2026-07-08', source_title: 'JoSAA Seat Matrix 2026', verified_date: '2026-06-18' },
        { program_short: 'Architecture (B.Arch)', annual_tuition_fee: 145000, intake: 60, eligibility: '10+2 with PCM + JEE Main Paper 2 (B.Arch) qualified.', exams: 'JEE Main Paper 2', status: 'Counselling Soon', deadline: '2026-07-08', source_title: 'JoSAA Seat Matrix 2026', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'All India Institute of Medical Sciences Jodhpur',
      short_code: 'AIIMS',
      slug: 'aiims-jodhpur',
      former_names: null,
      group_name: 'AIIMS Apex Healthcare Network',
      institution_type: 'Standalone HEI',
      legal_recognition: 'Institute of National Importance (AIIMS Act, 1956 amended)',
      relationship: 'Independent',
      ownership: 'Institute of National Importance',
      specialization_domain: 'Medical, Nursing & Health Sciences',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 2012,
      state: 'Rajasthan',
      district: 'Jodhpur',
      city: 'Jodhpur',
      locality: 'Basni Industrial Area Phase-2',
      pincode: '342005',
      address: 'Basni, Phase-2, Jodhpur, Rajasthan 342005',
      latitude: 26.2415,
      longitude: 73.0125,
      campus_acres: 120.0,
      official_website: 'https://aiimsjodhpur.edu.in',
      admissions_url: 'https://aiimsjodhpur.edu.in/courses',
      primary_email: 'dean@aiimsjodhpur.edu.in',
      primary_phone: '+91 291 2740742',
      naac_grade: 'Apex National Hospital',
      nirf_band: 'NIRF Medical Rank 13',
      logo_text: 'AIIMS',
      badge_color: '#0e3a4e',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'MCC NEET UG 2026 Official Portal',
      provenance_source_url: 'https://mcc.nic.in',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Institute of National Importance (Ministry of Health & Family Welfare)', ref_number: 'AIIMS Act MoHFW', effective_year: '2026-27', evidence_document_url: 'https://mohfw.gov.in' }
      ],
      offerings: [
        { program_short: 'MBBS', annual_tuition_fee: 5856, intake: 125, eligibility: '10+2 with PCB & English min 60%. NEET UG qualified with top national rank.', exams: 'NEET UG 2026 via MCC', status: 'Counselling Soon', deadline: '2026-07-15', source_title: 'MCC Seat Information 2026', verified_date: '2026-06-18' },
        { program_short: 'Nursing (B.Sc)', annual_tuition_fee: 3165, intake: 60, eligibility: '10+2 with PCB with min 55%. AIIMS B.Sc Nursing Entrance Exam.', exams: 'AIIMS Nursing Entrance', status: 'Applications Open', deadline: '2026-06-30', source_title: 'AIIMS Exams Portal', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'Birla Institute of Technology and Science Pilani',
      short_code: 'BITS',
      slug: 'bits-pilani',
      former_names: 'BITS Pilani Rajasthan',
      group_name: 'Birla Education Trust',
      institution_type: 'University',
      legal_recognition: 'Deemed to be University & Institute of Eminence (IoE)',
      relationship: 'Independent',
      ownership: 'Private Unaided',
      specialization_domain: 'Engineering, Sciences, Pharmacy & Management',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 1964,
      state: 'Rajasthan',
      district: 'Jhunjhunu',
      city: 'Pilani',
      locality: 'Vidya Vihar Campus',
      pincode: '333031',
      address: 'Vidya Vihar, Pilani, Jhunjhunu District, Rajasthan 333031',
      latitude: 28.3639,
      longitude: 75.5873,
      campus_acres: 328.0,
      official_website: 'https://bits-pilani.ac.in',
      admissions_url: 'https://bitsadmission.com',
      primary_email: 'admissions@pilani.bits-pilani.ac.in',
      primary_phone: '+91 1596 242205',
      naac_grade: 'NAAC A Grade / IoE',
      nirf_band: 'NIRF Engineering Rank 20',
      logo_text: 'BITS',
      badge_color: '#1a365d',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'BITSAT 2026 Admission Brochure',
      provenance_source_url: 'https://bitsadmission.com/bitsat2026',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Deemed to be University under Section 3 & Institute of Eminence', ref_number: 'UGC F.6-1/2018 (CPP-I)', effective_year: '2026-27', evidence_document_url: 'https://ugc.gov.in' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 545000, intake: 210, eligibility: '10+2 with aggregate 75% in PCM and min 60% in each subject + BITSAT score.', exams: 'BITSAT 2026', status: 'Counselling Soon', deadline: '2026-07-02', source_title: 'BITSAT Information Bulletin 2026', verified_date: '2026-06-18' }
      ]
    },
    {
      canonical_name: 'Manipal University Jaipur',
      short_code: 'MUJ',
      slug: 'manipal-university-jaipur',
      former_names: null,
      group_name: 'Manipal Education Group (MEMG)',
      institution_type: 'University',
      legal_recognition: 'State Private University (Govt of Rajasthan Act No. 21 of 2011)',
      relationship: 'Independent',
      ownership: 'Private Unaided',
      specialization_domain: 'Engineering, Architecture, Design, Management & Law',
      delivery_mode: 'On-campus',
      gender_model: 'Co-ed',
      est_year: 2011,
      state: 'Rajasthan',
      district: 'Jaipur',
      city: 'Jaipur',
      locality: 'Dehmi Kalan, Ajmer Road',
      pincode: '303007',
      address: 'Jaipur-Ajmer Expressway, Dehmi Kalan, Jaipur, Rajasthan 303007',
      latitude: 26.8437,
      longitude: 75.5652,
      campus_acres: 122.0,
      official_website: 'https://jaipur.manipal.edu',
      admissions_url: 'https://jaipur.manipal.edu/admissions',
      primary_email: 'admissions@jaipur.manipal.edu',
      primary_phone: '+91 141 3999100',
      naac_grade: 'NAAC A+ (CGPA 3.28)',
      nirf_band: 'NIRF Engineering Rank 64',
      logo_text: 'MUJ',
      badge_color: '#ea580c',
      hero_image: 'assets/img/heroes/cat-colleges.jpg',
      status: 'verified',
      provenance_source_name: 'MET 2026 / Manipal University Official Portal',
      provenance_source_url: 'https://jaipur.manipal.edu/admission-fee-2026',
      last_verified_at: '2026-06-18',
      approvals: [
        { regulator: 'UGC', approval_type: 'Recognized State Private University', ref_number: 'UGC 2(f) 2011', effective_year: '2026-27', evidence_document_url: 'https://ugc.gov.in' },
        { regulator: 'AICTE', approval_type: 'Approved for Technical & Management Programs', ref_number: 'AICTE-NW-MUJ-2026', effective_year: '2026-27', evidence_document_url: 'https://aicte-india.org' }
      ],
      offerings: [
        { program_short: 'B.Tech CSE', annual_tuition_fee: 385000, intake: 480, eligibility: '10+2 with min 50% in PCM + Valid MET / JEE Main rank.', exams: 'MET 2026 / JEE Main', status: 'Applications Open', deadline: '2026-07-28', source_title: 'MUJ Fee Structure 2026-27', verified_date: '2026-06-18' },
        { program_short: 'BBA', annual_tuition_fee: 175000, intake: 180, eligibility: '10+2 in any stream with min 50% from recognized board.', exams: 'Direct Merit / Interview', status: 'Applications Open', deadline: '2026-07-25', source_title: 'MUJ Admissions Notice', verified_date: '2026-06-18' }
      ]
    }
  ];

  for (const inst of institutions) {
    const [existing] = await connection.query('SELECT id FROM edu_institutions WHERE slug = ?', [inst.slug]);
    let instId;

    if (existing.length > 0) {
      instId = existing[0].id;
      await connection.query(
        `UPDATE edu_institutions SET 
          canonical_name = ?, short_code = ?, legal_recognition = ?, ownership = ?, 
          specialization_domain = ?, city = ?, locality = ?, naac_grade = ?, 
          nirf_band = ?, logo_text = ?, badge_color = ?, provenance_source_name = ?, 
          provenance_source_url = ?, last_verified_at = ?
         WHERE id = ?`,
        [inst.canonical_name, inst.short_code, inst.legal_recognition, inst.ownership,
         inst.specialization_domain, inst.city, inst.locality, inst.naac_grade,
         inst.nirf_band, inst.logo_text, inst.badge_color, inst.provenance_source_name,
         inst.provenance_source_url, inst.last_verified_at, instId]
      );
    } else {
      const [insertRes] = await connection.query(
        `INSERT INTO edu_institutions (
          canonical_name, short_code, slug, group_name, institution_type, legal_recognition, 
          relationship, ownership, specialization_domain, delivery_mode, gender_model, 
          est_year, state, district, city, locality, pincode, address, latitude, longitude, 
          campus_acres, official_website, admissions_url, primary_email, primary_phone, 
          naac_grade, nirf_band, logo_text, badge_color, hero_image, status, 
          provenance_source_name, provenance_source_url, last_verified_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          inst.canonical_name, inst.short_code, inst.slug, inst.group_name, inst.institution_type, inst.legal_recognition,
          inst.relationship, inst.ownership, inst.specialization_domain, inst.delivery_mode, inst.gender_model,
          inst.est_year, inst.state, inst.district, inst.city, inst.locality, inst.pincode, inst.address, inst.latitude, inst.longitude,
          inst.campus_acres, inst.official_website, inst.admissions_url, inst.primary_email, inst.primary_phone,
          inst.naac_grade, inst.nirf_band, inst.logo_text, inst.badge_color, inst.hero_image, inst.status,
          inst.provenance_source_name, inst.provenance_source_url, inst.last_verified_at
        ]
      );
      instId = insertRes.insertId;
    }

    // Insert approvals
    for (const app of inst.approvals) {
      const [appExist] = await connection.query(
        'SELECT id FROM edu_approvals WHERE institution_id = ? AND regulator = ?',
        [instId, app.regulator]
      );
      if (appExist.length === 0) {
        await connection.query(
          `INSERT INTO edu_approvals (institution_id, regulator, approval_type, reference_number, effective_year, evidence_document_url)
           VALUES (?, ?, ?, ?, ?, ?)`,
          [instId, app.regulator, app.approval_type, app.ref_number, app.effective_year, app.evidence_document_url]
        );
      }
    }

    // Insert offerings
    for (const off of inst.offerings) {
      const progId = progMap[off.program_short];
      if (progId) {
        const [offExist] = await connection.query(
          'SELECT id FROM edu_offerings WHERE institution_id = ? AND program_id = ? AND academic_year = ?',
          [instId, progId, '2026-27']
        );
        if (offExist.length === 0) {
          await connection.query(
            `INSERT INTO edu_offerings (
              institution_id, program_id, academic_year, intake_seats, annual_tuition_fee, 
              eligibility_criteria, entrance_exams, application_status, application_deadline, 
              source_title, source_url, verified_date
            ) VALUES (?, ?, '2026-27', ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
              instId, progId, off.intake, off.annual_tuition_fee, off.eligibility,
              off.exams, off.status, off.deadline, off.source_title, inst.provenance_source_url, off.verified_date
            ]
          );
        }
      }
    }
  }
  console.log(`Seeded ${institutions.length} institutions with 2026-27 offerings and regulator approvals.`);

  // 4. Seed Admission Updates & Exam Alerts (Matching Image 3)
  const updates = [
    {
      badge_type: 'EXAM',
      title: 'REAP 2026 counselling registration window',
      slug: 'reap-2026-counselling-registration-window',
      summary: 'Centre for Electronic Governance Rajasthan opens the REAP 2026 single-window registration for government and private engineering colleges in Rajasthan.',
      read_time: '4 min read',
      verified_date_text: 'Updated 19 Jun',
      source_authority: 'CEG Rajasthan Official Notice',
      source_url: 'https://reap2026.rajasthan.gov.in'
    },
    {
      badge_type: 'DEADLINE',
      title: 'JoSAA 2026: choice filling starts soon',
      slug: 'josaa-2026-choice-filling-starts-soon',
      summary: 'Joint Seat Allocation Authority schedule confirmed for IITs, NITs (MNIT Jaipur) and IIITs (IIIT Kota). Round 1 mock seat allocation dates released.',
      read_time: 'Official notice',
      verified_date_text: 'Checked 18 Jun',
      source_authority: 'JoSAA / CSAB Secretariat',
      source_url: 'https://josaa.nic.in'
    },
    {
      badge_type: 'GUIDE',
      title: 'B.Tech CSE in Rajasthan: a practical shortlist',
      slug: 'btech-cse-in-rajasthan-practical-shortlist',
      summary: 'Verified breakdown of fees, NAAC/UGC status, seat intake, and placement realities across 12 prominent engineering faculties in Jaipur, Kota, and Jodhpur.',
      read_time: 'POVIndian desk',
      verified_date_text: 'Refreshed 16 Jun',
      source_authority: 'POV Education Editorial Desk',
      source_url: '/blog.php?tag=practical-shortlist'
    }
  ];

  for (const upd of updates) {
    const [exist] = await connection.query('SELECT id FROM edu_updates WHERE slug = ?', [upd.slug]);
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_updates (badge_type, title, slug, summary, read_time, verified_date_text, source_authority, source_url)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
        [upd.badge_type, upd.title, upd.slug, upd.summary, upd.read_time, upd.verified_date_text, upd.source_authority, upd.source_url]
      );
    }
  }
  console.log('Seeded admission alerts and guides.');

  // 5. Seed Initial Sample Inquiries (to populate Admin Leads tab)
  const initialLeads = [
    {
      lead_number: 'POV-EDU-2026-0891',
      lead_type: 'admission_counselling',
      student_name: 'Aakash Verma',
      mobile: '+91 98290 11223',
      email: 'aakash.verma@gmail.com',
      institution_id: 1, // JECRC
      preferred_course: 'B.Tech Computer Science',
      academic_year: '2026-27',
      city: 'Jaipur',
      budget_range: '₹1.5 Lakh - ₹2.5 Lakh/yr',
      entrance_exam: 'JEE Main (87.4 percentile)',
      lead_stage: 'Counselling',
      counsellor_assigned: 'Deepak Sharma (POV Desk)',
      counsellor_notes: 'Spoke on 28th June. Student interested in AI/ML specialization. Sent fee breakdown and scholarship brochure.',
      first_touch_source: 'Rajasthan 2026-27 Explore Page',
      last_touch_source: 'Shortlist Enquiry Modal'
    },
    {
      lead_number: 'POV-EDU-2026-0892',
      lead_type: 'course_enquiry',
      student_name: 'Pooja Choudhary',
      mobile: '+91 94140 88776',
      email: 'pooja.choudhary@outlook.com',
      institution_id: 4, // Mody University
      preferred_course: 'B.Tech CSE / Nursing',
      academic_year: '2026-27',
      city: 'Sikar',
      budget_range: '₹1.5 Lakh - ₹2 Lakh/yr',
      entrance_exam: 'Class 12th (89.2% PCM)',
      lead_stage: 'Contacted',
      counsellor_assigned: 'Ananya Sen',
      counsellor_notes: 'Parent enquired about girls hostel safety and transport from Jaipur.',
      first_touch_source: 'Search: Women Colleges Rajasthan',
      last_touch_source: 'College Detail Modal'
    },
    {
      lead_number: 'POV-EDU-2026-0893',
      lead_type: 'admission_counselling',
      student_name: 'Rohan Meena',
      mobile: '+91 99823 44556',
      email: 'rohan.meena99@gmail.com',
      institution_id: 3, // MBM
      preferred_course: 'B.Tech Mechanical / CSE',
      academic_year: '2026-27',
      city: 'Jodhpur',
      budget_range: 'Under ₹1 Lakh/yr (Govt College)',
      entrance_exam: 'REAP 2026 Candidate',
      lead_stage: 'New',
      counsellor_assigned: 'POV Education Desk',
      counsellor_notes: 'Requested guidance on REAP choice filling sequence for government seats.',
      first_touch_source: 'REAP Counselling Alert Card',
      last_touch_source: 'General Counselling CTA'
    }
  ];

  for (const lead of initialLeads) {
    const [exist] = await connection.query('SELECT id FROM edu_leads WHERE lead_number = ?', [lead.lead_number]);
    if (exist.length === 0) {
      await connection.query(
        `INSERT INTO edu_leads (
          lead_number, lead_type, student_name, mobile, email, institution_id, 
          preferred_course, academic_year, city, budget_range, entrance_exam, 
          lead_stage, counsellor_assigned, counsellor_notes, first_touch_source, last_touch_source
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [
          lead.lead_number, lead.lead_type, lead.student_name, lead.mobile, lead.email, lead.institution_id,
          lead.preferred_course, lead.academic_year, lead.city, lead.budget_range, lead.entrance_exam,
          lead.lead_stage, lead.counsellor_assigned, lead.counsellor_notes, lead.first_touch_source, lead.last_touch_source
        ]
      );
    }
  }
  console.log('Seeded sample admission leads.');

  await connection.end();
  console.log('=== Higher Education Seeding Completed Successfully! ===');
}

seedEducation().catch(err => {
  console.error('Education seeding error:', err);
  process.exit(1);
});
