-- ====================================================================
-- POV Indian: Luxury Education Platform Expanded Ecosystem
-- Covers Exams, Careers, Scholarships, Reviews, and Application Tracker
-- ====================================================================

-- 1. National & State Exams Hub
CREATE TABLE IF NOT EXISTS `edu_exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `exam_name` VARCHAR(200) NOT NULL,
  `short_code` VARCHAR(50) NOT NULL,
  `category` ENUM('Engineering', 'Medical', 'Management', 'Law', 'Design', 'Government') NOT NULL DEFAULT 'Engineering',
  `conducting_body` VARCHAR(200) NOT NULL DEFAULT 'NTA (National Testing Agency)',
  `exam_level` VARCHAR(50) NOT NULL DEFAULT 'National Level',
  `frequency` VARCHAR(50) DEFAULT 'Twice a year',
  `mode` VARCHAR(50) DEFAULT 'Computer Based Test (CBT)',
  `duration_mins` INT DEFAULT 180,
  `application_start` DATE DEFAULT '2026-02-01',
  `application_end` DATE DEFAULT '2026-03-15',
  `admit_card_date` DATE DEFAULT '2026-04-10',
  `exam_date` DATE DEFAULT '2026-04-20',
  `result_date` DATE DEFAULT '2026-05-15',
  `counselling_date` DATE DEFAULT '2026-06-15',
  `application_fee` VARCHAR(50) DEFAULT '₹1,000 (Gen/OBC)',
  `eligibility_summary` TEXT,
  `syllabus_summary` TEXT,
  `participating_colleges_count` VARCHAR(50) DEFAULT '150+ Top Institutes',
  `official_url` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_exam_cat (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Career Intelligence & Trajectory Paths
CREATE TABLE IF NOT EXISTS `edu_careers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `title` VARCHAR(200) NOT NULL,
  `domain` VARCHAR(100) NOT NULL DEFAULT 'Technology',
  `avg_starting_lpa` DECIMAL(5,2) DEFAULT 7.50,
  `mid_career_lpa` DECIMAL(5,2) DEFAULT 18.00,
  `leadership_lpa` DECIMAL(5,2) DEFAULT 45.00,
  `growth_outlook` VARCHAR(50) DEFAULT '+24% High Demand',
  `preferred_degree` VARCHAR(200) DEFAULT 'B.Tech Computer Science / AI',
  `key_skills` VARCHAR(255) DEFAULT 'Data Structures, Cloud Architecture, ML, Python',
  `typical_recruiters` VARCHAR(255) DEFAULT 'Google, Microsoft, Amazon, Infosys, Flipkart',
  `career_path_json` JSON DEFAULT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_career_domain (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Scholarships Registry
CREATE TABLE IF NOT EXISTS `edu_scholarships` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `provider` VARCHAR(200) NOT NULL DEFAULT 'Government of India',
  `category` ENUM('Merit-Based', 'Means-Cum-Merit', 'Women in STEM', 'Reserved Category', 'Corporate CSR') NOT NULL DEFAULT 'Merit-Based',
  `amount_display` VARCHAR(100) NOT NULL DEFAULT 'Up to ₹2,00,000 / year',
  `applicable_course` VARCHAR(150) DEFAULT 'B.Tech / MBBS / MBA',
  `eligibility` TEXT NOT NULL,
  `family_income_limit` VARCHAR(100) DEFAULT 'Below ₹8,00,000 per annum',
  `deadline` DATE DEFAULT '2026-08-31',
  `documents_required` VARCHAR(255) DEFAULT 'Income Certificate, 12th Marksheet, Admission Letter, Aadhar',
  `official_link` VARCHAR(255) NOT NULL DEFAULT 'https://scholarships.gov.in',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sch_cat (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Verified Student Reviews
CREATE TABLE IF NOT EXISTS `edu_reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `institution_id` INT NOT NULL,
  `reviewer_name` VARCHAR(100) NOT NULL,
  `course_name` VARCHAR(150) NOT NULL DEFAULT 'B.Tech Computer Science',
  `batch_year` VARCHAR(50) NOT NULL DEFAULT 'Class of 2025',
  `verified_student` TINYINT(1) DEFAULT 1,
  `rating_overall` DECIMAL(2,1) NOT NULL DEFAULT 4.5,
  `rating_academics` DECIMAL(2,1) DEFAULT 4.5,
  `rating_placements` DECIMAL(2,1) DEFAULT 4.5,
  `rating_infra` DECIMAL(2,1) DEFAULT 4.5,
  `rating_faculty` DECIMAL(2,1) DEFAULT 4.5,
  `review_title` VARCHAR(255) NOT NULL,
  `pros` TEXT,
  `cons` TEXT,
  `review_body` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`institution_id`) REFERENCES `edu_institutions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Student Application Tracker
CREATE TABLE IF NOT EXISTS `edu_applications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_name` VARCHAR(150) NOT NULL DEFAULT 'Aakash Verma',
  `mobile` VARCHAR(20) NOT NULL DEFAULT '+91 98290 11223',
  `institution_id` INT NOT NULL,
  `program_name` VARCHAR(150) NOT NULL DEFAULT 'B.Tech Computer Science',
  `status` ENUM('Saved', 'Shortlisted', 'Application Started', 'Documents Pending', 'Submitted', 'Under Review', 'Accepted', 'Rejected') NOT NULL DEFAULT 'Application Started',
  `progress_pct` INT DEFAULT 45,
  `next_deadline` DATE DEFAULT '2026-07-15',
  `next_action` VARCHAR(255) DEFAULT 'Upload Class 12th Verified Marks Certificate',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`institution_id`) REFERENCES `edu_institutions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
