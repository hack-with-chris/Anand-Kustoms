CREATE DATABASE IF NOT EXISTS anand_kustoms;
USE anand_kustoms;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    badge VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    details JSON NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL UNIQUE,
    category VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    year YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(40) DEFAULT NULL,
    vehicle VARCHAR(120) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO services (badge, title, description, details, sort_order)
VALUES
  ('Most popular', 'Full body wraps', 'Design-driven wraps that protect paint while making your car stand out on the road.', JSON_ARRAY('Premium vinyl', 'Paint-safe prep', 'Finish protection'), 1),
  ('Signature', 'Custom interiors', 'Luxury trim, stitched finishes, and tailored upgrades designed around your lifestyle.', JSON_ARRAY('Leather upgrades', 'Ambient lighting', 'Trim personalization'), 2),
  ('Precision', 'Performance tuning', 'Drive better with carefully tuned setups focused on power, balance, and reliability.', JSON_ARRAY('ECU remaps', 'Exhaust tuning', 'Road testing'), 3)
ON DUPLICATE KEY UPDATE
    badge = VALUES(badge),
    description = VALUES(description),
    details = VALUES(details),
    sort_order = VALUES(sort_order);

INSERT INTO projects (title, category, description, year)
VALUES
  ('Urban Night Series', 'Wrap & paint', 'A satin-black build with smoked accents and complete cabin detailing.', 2025),
  ('Trackline GT', 'Performance', 'A sharper suspension setup and signature tuning package for daily performance driving.', 2024),
  ('Executive Luxe', 'Interior', 'Custom leather patterns, ambient lighting, and premium trims for refined comfort.', 2024)
ON DUPLICATE KEY UPDATE
    category = VALUES(category),
    description = VALUES(description),
    year = VALUES(year);
