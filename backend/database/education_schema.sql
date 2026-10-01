-- ====================================================================
-- POV Indian: Higher Education Data Graph Schema
-- Compliant with POVIndian Master Product Strategy (Layers 1 to 5)
-- Canonical Hierarchy: Institution -> Campus -> Department -> Program -> Specialization -> Offering (2026-27)
-- ====================================================================

-- 1. Institutions
CREATE TABLE IF NOT EXISTS `edu_institutions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `canonical_name` VARCHAR(255) NOT NULL,
  `short_code` VARCHAR(50) DEFAULT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `former_names` VARCHAR(255) DEFAULT NULL,
  `group_name` VARCHAR(150) DEFAULT NULL,
  `institution_type` ENUM('University', 'College', 'Standalone HEI') NOT NULL DEFAULT 'University',
  `legal_recognition` VARCHAR(100) NOT NULL DEFAULT 'State Private University', 
  `relationship` ENUM('Independent', 'Affiliated', 'Constituent', 'Autonomous') DEFAULT 'Independent',
  `affiliating_university` VARCHAR(255) DEFAULT NULL,
  `ownership` ENUM('Central Government', 'State Government', 'Private Unaided', 'Government-Aided', 'Institute of National Importance') NOT NULL DEFAULT 'Private Unaided',
  `specialization_domain` VARCHAR(150) NOT NULL DEFAULT 'Multidisciplinary',
  `delivery_mode` VARCHAR(50) NOT NULL DEFAULT 'On-campus',
  `gender_model` ENUM('Co-ed', 'Women', 'Men') NOT NULL DEFAULT 'Co-ed',
  `est_year` INT DEFAULT 2012,
  `state` VARCHAR(100) NOT NULL DEFAULT 'Rajasthan',
  `district` VARCHAR(100) NOT NULL DEFAULT 'Jaipur',
  `city` VARCHAR(100) NOT NULL DEFAULT 'Jaipur',
  `locality` VARCHAR(150) NOT NULL DEFAULT 'Sitapura',
  `pincode` VARCHAR(10) DEFAULT '302022',
  `address` TEXT,
  `latitude` DECIMAL(10, 7) DEFAULT 26.7820,
  `longitude` DECIMAL(10, 7) DEFAULT 75.8640,
  `campus_acres` DECIMAL(6, 2) DEFAULT 32.0,
  `official_website` VARCHAR(255) NOT NULL,
  `admissions_url` VARCHAR(255) DEFAULT NULL,
  `primary_email` VARCHAR(150) DEFAULT 'admissions@institution.edu.in',
  `primary_phone` VARCHAR(50) DEFAULT '+91 141 6565656',
  `naac_grade` VARCHAR(20) DEFAULT 'A+',
  `nirf_band` VARCHAR(50) DEFAULT 'Top 150',
  `logo_text` VARCHAR(10) DEFAULT 'JU',
  `badge_color` VARCHAR(20) DEFAULT '#0d2b39',
  `hero_image` VARCHAR(255) DEFAULT 'assets/img/heroes/cat-colleges.jpg',
  `status` ENUM('draft', 'under_review', 'verified', 'archived') NOT NULL DEFAULT 'verified',
  `provenance_source_name` VARCHAR(150) NOT NULL DEFAULT 'Official University Gazette / AISHE',
  `provenance_source_url` VARCHAR(255) NOT NULL DEFAULT 'https://ugc.gov.in',
  `last_verified_at` DATE NOT NULL DEFAULT '2026-06-18',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_state_city (`state`, `city`),
  INDEX idx_type (`institution_type`),
  INDEX idx_ownership (`ownership`),
  INDEX idx_verified (`last_verified_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Regulatory Approvals & Recognitions (UGC, AICTE, BCI, PCI, INC, COA)
CREATE TABLE IF NOT EXISTS `edu_approvals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `institution_id` INT NOT NULL,
  `regulator` VARCHAR(50) NOT NULL, -- UGC, AICTE, PCI, BCI, INC, COA, NBA, NAAC
  `approval_type` VARCHAR(100) NOT NULL, -- 2(f) & 12(B), AICTE Extension of Approval, NAAC Accreditation
  `reference_number` VARCHAR(150) DEFAULT NULL,
  `effective_year` VARCHAR(20) NOT NULL DEFAULT '2026-27',
  `valid_upto` DATE DEFAULT NULL,
  `evidence_document_url` VARCHAR(255) DEFAULT NULL,
  `verification_notes` TEXT,
  `verified_by` VARCHAR(100) DEFAULT 'POV Desk Researcher',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`institution_id`) REFERENCES `edu_institutions`(`id`) ON DELETE CASCADE,
  INDEX idx_inst_reg (`institution_id`, `regulator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Academic Programs (Reusable Canonical Academic Identity)
CREATE TABLE IF NOT EXISTS `edu_programs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `canonical_name` VARCHAR(255) NOT NULL,
  `short_name` VARCHAR(100) NOT NULL,
  `degree_type` VARCHAR(50) NOT NULL DEFAULT 'B.Tech', -- B.Tech, MBA, MBBS, BBA, B.Des, etc.
  `academic_level` ENUM('Undergraduate', 'Postgraduate', 'Diploma', 'Doctorate', 'Integrated') NOT NULL DEFAULT 'Undergraduate',
  `discipline` VARCHAR(100) NOT NULL DEFAULT 'Engineering',
  `specialization` VARCHAR(150) DEFAULT 'Computer Science and Engineering',
  `duration_years` DECIMAL(3, 1) NOT NULL DEFAULT 4.0,
  `study_mode` ENUM('Full-time', 'Part-time', 'Online', 'ODL', 'Hybrid') NOT NULL DEFAULT 'Full-time',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_degree_discipline (`degree_type`, `discipline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Academic-Year Offerings (Year-Specific Facts: 2026–27)
CREATE TABLE IF NOT EXISTS `edu_offerings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `institution_id` INT NOT NULL,
  `program_id` INT NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL DEFAULT '2026-27',
  `intake_seats` INT DEFAULT 180,
  `annual_tuition_fee` INT NOT NULL DEFAULT 150000,
  `other_fee_annual` INT DEFAULT 25000,
  `one_time_deposit` INT DEFAULT 10000,
  `hostel_fee_annual` INT DEFAULT 85000,
  `fee_currency` VARCHAR(10) DEFAULT 'INR',
  `eligibility_criteria` TEXT NOT NULL,
  `qualifying_subjects` VARCHAR(255) DEFAULT 'Physics, Mathematics & Chemistry/CS (min 50%)',
  `entrance_exams` VARCHAR(255) DEFAULT 'JEE Main / REAP / University Entrance',
  `counselling_authority` VARCHAR(150) DEFAULT 'REAP Rajasthan / CSAB',
  `application_status` ENUM('Applications Open', 'Counselling Soon', 'Registration Closed', 'Merit List Out') NOT NULL DEFAULT 'Applications Open',
  `application_deadline` DATE DEFAULT '2026-07-15',
  `brochure_pdf_url` VARCHAR(255) DEFAULT NULL,
  `source_title` VARCHAR(200) NOT NULL DEFAULT 'Official Fee Circular 2026-27',
  `source_url` VARCHAR(255) NOT NULL DEFAULT 'https://institution.edu.in/admissions-2026',
  `verified_date` DATE NOT NULL DEFAULT '2026-06-18',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`institution_id`) REFERENCES `edu_institutions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`program_id`) REFERENCES `edu_programs`(`id`) ON DELETE CASCADE,
  INDEX idx_inst_year (`institution_id`, `academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Student Inquiries & Admission Leads (Layer 3 - Lead Engine)
CREATE TABLE IF NOT EXISTS `edu_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_number` VARCHAR(64) NOT NULL UNIQUE,
  `lead_type` ENUM('general_enquiry', 'course_enquiry', 'admission_counselling', 'fee_details', 'callback_request', 'brochure_download') NOT NULL DEFAULT 'admission_counselling',
  `student_name` VARCHAR(150) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `institution_id` INT DEFAULT NULL,
  `program_id` INT DEFAULT NULL,
  `preferred_course` VARCHAR(150) DEFAULT 'B.Tech Computer Science',
  `academic_year` VARCHAR(20) NOT NULL DEFAULT '2026-27',
  `city` VARCHAR(100) DEFAULT 'Jaipur',
  `state` VARCHAR(100) DEFAULT 'Rajasthan',
  `budget_range` VARCHAR(100) DEFAULT '₹1.5 Lakh - ₹3 Lakh/yr',
  `entrance_exam` VARCHAR(100) DEFAULT 'JEE Main / REAP',
  `score_or_percentile` VARCHAR(50) DEFAULT NULL,
  `preferred_timeline` VARCHAR(100) DEFAULT 'Immediate (2026 session)',
  `hostel_required` TINYINT(1) DEFAULT 0,
  `consent_version` VARCHAR(50) NOT NULL DEFAULT 'pov_v2.1_explicit_counselling',
  `consent_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `lead_stage` ENUM('New', 'Contacted', 'Interested', 'Counselling', 'Application Started', 'Application Submitted', 'Admitted', 'Closed') NOT NULL DEFAULT 'New',
  `counsellor_assigned` VARCHAR(100) DEFAULT 'POV Education Desk',
  `counsellor_notes` TEXT,
  `first_touch_source` VARCHAR(100) DEFAULT 'Direct / Organic Discovery',
  `last_touch_source` VARCHAR(100) DEFAULT 'Explore Page Shortlist',
  `utm_campaign` VARCHAR(100) DEFAULT 'rajasthan_higher_ed_2026',
  `landing_page` VARCHAR(255) DEFAULT '/colleges-explore.php',
  `ip_address` VARCHAR(50) DEFAULT '127.0.0.1',
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`institution_id`) REFERENCES `edu_institutions`(`id`) ON DELETE SET NULL,
  INDEX idx_lead_stage (`lead_stage`),
  INDEX idx_lead_mobile (`mobile`),
  INDEX idx_inst_lead (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Admission Updates, Exam Alerts & Guides (Image 3 Section)
CREATE TABLE IF NOT EXISTS `edu_updates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `badge_type` ENUM('EXAM', 'DEADLINE', 'GUIDE', 'ADMISSION') NOT NULL DEFAULT 'EXAM',
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `summary` TEXT NOT NULL,
  `read_time` VARCHAR(50) DEFAULT '4 min read',
  `verified_date_text` VARCHAR(100) DEFAULT 'Updated 19 Jun',
  `source_authority` VARCHAR(150) DEFAULT 'Official Notice / Desk',
  `source_url` VARCHAR(255) DEFAULT 'https://reap2026.com',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
