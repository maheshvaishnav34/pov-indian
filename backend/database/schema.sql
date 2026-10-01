-- Database: pov_indian_db
CREATE DATABASE IF NOT EXISTS pov_indian_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE pov_indian_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'business_owner', 'editor', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_email (email)
) ENGINE=InnoDB;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    count_text VARCHAR(50) DEFAULT '0+',
    icon VARCHAR(100),
    hero_image VARCHAR(255),
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category_slug (slug)
) ENGINE=InnoDB;

-- 3. Locations Table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    count_text VARCHAR(50) DEFAULT '0+',
    state VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location_slug (slug)
) ENGINE=InnoDB;

-- 4. Listings Table
CREATE TABLE IF NOT EXISTS listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    location_id INT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    location_text VARCHAR(150),
    phone VARCHAR(50),
    price VARCHAR(100),
    rating DECIMAL(2,1) DEFAULT 5.0,
    badge VARCHAR(50) DEFAULT '',
    image VARCHAR(255),
    avatar VARCHAR(255),
    description TEXT,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords VARCHAR(255) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    schema_type VARCHAR(50) DEFAULT 'LocalBusiness',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    INDEX idx_listing_cat (category_id),
    INDEX idx_listing_loc (location_id),
    INDEX idx_listing_status (status)
) ENGINE=InnoDB;

-- 5. Blogs Table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    body LONGTEXT,
    author VARCHAR(100),
    tag VARCHAR(100),
    tag_slug VARCHAR(100),
    image VARCHAR(255),
    read_time VARCHAR(50) DEFAULT '5 min read',
    is_published BOOLEAN DEFAULT TRUE,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_blog_slug (slug),
    INDEX idx_blog_tag (tag_slug)
) ENGINE=InnoDB;

-- 6. Form Submissions Table
CREATE TABLE IF NOT EXISTS form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('contact', 'list-business', 'contributor', 'signup', 'subscribe') NOT NULL,
    name VARCHAR(150) NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NULL,
    subject VARCHAR(200) NULL,
    message TEXT NULL,
    extra_data JSON NULL,
    ip_address VARCHAR(45) NULL,
    status ENUM('new', 'in_review', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sub_type (type),
    INDEX idx_sub_status (status)
) ENGINE=InnoDB;
