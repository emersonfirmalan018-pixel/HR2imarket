-- ======================================
-- Create Database
-- ======================================
CREATE DATABASE IF NOT EXISTS imarket;
USE imarket;

-- ======================================
-- Departments
-- ======================================
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- ======================================
-- Users
-- ======================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    fullname VARCHAR(200),
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'admin') DEFAULT 'employee',
    department_id INT DEFAULT NULL,
    manager_id INT DEFAULT NULL,
    contact VARCHAR(50),
    address TEXT,
    birthday DATE,
    id_number VARCHAR(50),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ======================================
-- Courses
-- ======================================
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50),
    link_url TEXT,
    file_name TEXT,
    tags TEXT,
    description TEXT,
    duration VARCHAR(50),
    level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    completed_date DATE DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================
-- Trainings
-- ======================================
CREATE TABLE IF NOT EXISTS trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    training_date DATE NOT NULL,
    training_time TIME NOT NULL,
    duration DECIMAL(4,2) NOT NULL,
    location VARCHAR(200),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================
-- Training Progress
-- ======================================
CREATE TABLE IF NOT EXISTS training_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    completion TINYINT UNSIGNED NOT NULL DEFAULT 0 CHECK (completion BETWEEN 0 AND 100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- ======================================
-- Succession Planning
-- ======================================
CREATE TABLE IF NOT EXISTS succession_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(200) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS succession_key_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    role_name VARCHAR(200) NOT NULL,
    criticality ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
    timeline ENUM('Immediate (0-6 months)', 'Short-term (6-12 months)', 'Long-term (1-3 years)') DEFAULT 'Long-term (1-3 years)',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES succession_plans(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS succession_successors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_role_id INT NOT NULL,
    user_id INT NOT NULL,
    readiness ENUM('Ready Now', '1-2 Years', '3+ Years') DEFAULT 'Ready Now',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (key_role_id) REFERENCES succession_key_roles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ======================================
-- Default Data
-- ======================================
INSERT IGNORE INTO departments (id, name) VALUES
(1, 'HR'),
(2, 'Customer Service'),
(3, 'Finance'),
(4, 'IT'),
(5, 'Sales');

-- Admin account (SHA2 for initial password)
INSERT IGNORE INTO users (id, username, fullname, email, password, role, department_id)
VALUES (1, 'admin', 'System Admin', 'admin@imarket.com', SHA2('admin123', 256), 'admin', 1);

-- Sample employee
INSERT IGNORE INTO users (id, username, fullname, email, password, role, department_id, contact, address, birthday, photo)
VALUES (2, 'johndoe', 'John Doe', 'john@imarket.com', SHA2('password123', 256), 'employee', 2, '09123456789', 'Manila, Philippines', '1995-07-10', NULL);

-- ======================================
-- Sample Courses
-- ======================================
INSERT IGNORE INTO courses (title, type, link_url, tags, description, duration, level, completed_date, status)
VALUES
('Introduction to Cybersecurity', 'Online', 'https://example.com/cyber', 'Security, IT', 'Learn the basics of cybersecurity and protecting digital assets.', '6 hours', 'Beginner', '2025-08-15', 'active'),
('Advanced Data Analytics', 'Online', 'https://example.com/analytics', 'Data, Analytics', 'Master data visualization and predictive analytics techniques.', '12 hours', 'Advanced', NULL, 'active'),
('Customer Service Excellence', 'Workshop', NULL, 'Customer Service, Soft Skills', 'Improve communication, empathy, and problem-solving in customer interactions.', '4 hours', 'Intermediate', '2025-09-05', 'active');

-- ======================================
-- Sample Training Progress
-- ======================================
INSERT IGNORE INTO training_progress (user_id, course_id, completion)
VALUES
(2, 1, 100),
(2, 2, 60),
(2, 3, 20);
