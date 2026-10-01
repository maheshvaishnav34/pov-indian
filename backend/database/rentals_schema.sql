-- ====================================================================
-- POV Indian: Rentals & Stays Database Schema (Find a place that fits your life)
-- Compliant with PRD v2.0 (Long-Term Rentals, PG/Shared, Short-Stay Stays)
-- ====================================================================

CREATE TABLE IF NOT EXISTS `rental_properties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `rental_type` ENUM('long_term', 'pg_shared', 'short_stay') NOT NULL DEFAULT 'long_term',
  `category` VARCHAR(100) NOT NULL DEFAULT 'Flat / Apartment',
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `locality` VARCHAR(150) NOT NULL,
  `landmark` VARCHAR(200) NOT NULL,
  `landmark_distance` VARCHAR(100) DEFAULT '',
  `latitude` DECIMAL(10, 7) NOT NULL,
  `longitude` DECIMAL(10, 7) NOT NULL,
  `geo_confidence` VARCHAR(50) DEFAULT 'Exact Verified',
  `price` VARCHAR(50) NOT NULL,
  `numeric_price` INT NOT NULL,
  `price_unit` VARCHAR(50) NOT NULL DEFAULT '/month',
  `deposit` VARCHAR(100) DEFAULT '1 Month',
  `bhk` VARCHAR(50) DEFAULT '1 BHK',
  `bathrooms` INT DEFAULT 1,
  `furnishing` VARCHAR(50) DEFAULT 'Semi-Furnished',
  `preferred_tenant` VARCHAR(100) DEFAULT 'Family / Working Professionals',
  `food_rule` VARCHAR(100) DEFAULT 'Non-Veg Allowed',
  `curfew_rule` VARCHAR(100) DEFAULT 'No Curfew / 24x7',
  `power_backup` VARCHAR(100) DEFAULT 'Full Power Backup',
  `water_supply` VARCHAR(100) DEFAULT '24x7 Municipal Water',
  `brokerage_type` VARCHAR(50) DEFAULT 'zero_brokerage',
  `image` VARCHAR(255) NOT NULL,
  `gallery` JSON DEFAULT NULL,
  `amenities` JSON DEFAULT NULL,
  `host_name` VARCHAR(100) DEFAULT 'Verified Host',
  `host_phone` VARCHAR(50) DEFAULT '+91 98765 43210',
  `verified_badge` TINYINT(1) DEFAULT 1,
  `description` TEXT,
  `status` ENUM('active', 'paused', 'rented', 'pending') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_city (`city`),
  INDEX idx_rental_type (`rental_type`),
  INDEX idx_numeric_price (`numeric_price`),
  INDEX idx_lat_lng (`latitude`, `longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rental_visits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `token` VARCHAR(64) NOT NULL UNIQUE,
  `visit_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) NOT NULL,
  `seeker_name` VARCHAR(100) NOT NULL,
  `seeker_phone` VARCHAR(50) NOT NULL,
  `seeker_notes` TEXT,
  `status` ENUM('pending', 'confirmed', 'rescheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `owner_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_property_id (`property_id`),
  INDEX idx_token (`token`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rental_requirements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `requirement_type` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `preferred_landmark` VARCHAR(200) DEFAULT '',
  `max_budget` INT NOT NULL,
  `min_budget` INT DEFAULT 0,
  `move_in_date` DATE DEFAULT NULL,
  `tenant_profile` VARCHAR(100) DEFAULT 'Working Professional',
  `food_preference` VARCHAR(100) DEFAULT 'Any',
  `contact_name` VARCHAR(100) NOT NULL,
  `contact_phone` VARCHAR(50) NOT NULL,
  `additional_notes` TEXT,
  `status` ENUM('active', 'matched', 'closed') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rental_assisted_onboarding` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `owner_name` VARCHAR(100) NOT NULL,
  `owner_phone` VARCHAR(50) NOT NULL,
  `property_type` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `landmark` VARCHAR(200) NOT NULL,
  `expected_rent` VARCHAR(50) DEFAULT '',
  `photos_count` INT DEFAULT 0,
  `notes` TEXT,
  `status` ENUM('new', 'in_review', 'published', 'rejected') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
