-- =====================================================
-- Mental Health DBMS - Complete Database Schema
-- =====================================================
-- Database: mentalhealth
-- Created based on MentalHealth_DBMS project structure
-- =====================================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS mentalhealth CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE mentalhealth;

-- =====================================================
-- DROP EXISTING TABLES (if reimporting)
-- =====================================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS crisis_alert;
DROP TABLE IF EXISTS daily_logs;
DROP TABLE IF EXISTS recommendations;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS insurance_plan;
DROP TABLE IF EXISTS insurance_company;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS counsellors;
DROP TABLE IF EXISTS clinic;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS analyst;
DROP TABLE IF EXISTS emergency_sos_team;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- ADMIN TABLE
-- =====================================================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- USERS TABLE (Patients)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other', 'Prefer not to say'),
    emergency_contact VARCHAR(255),
    medical_history TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- COUNSELLORS TABLE
-- =====================================================
CREATE TABLE counsellors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(255),
    qualification VARCHAR(255),
    experience_years INT,
    license_number VARCHAR(50),
    bio TEXT,
    availability TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- CLINIC TABLE
-- =====================================================
CREATE TABLE clinic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_name VARCHAR(150) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    operating_hours VARCHAR(255),
    services_offered TEXT,
    website VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSURANCE COMPANY TABLE
-- =====================================================
CREATE TABLE insurance_company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20),
    address TEXT,
    website VARCHAR(255),
    policy_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSURANCE PLAN TABLE
-- =====================================================
CREATE TABLE insurance_plan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    insurance_company_id INT,
    plan_name VARCHAR(100) NOT NULL,
    plan_type VARCHAR(50),
    coverage_amount DECIMAL(10, 2),
    premium_amount DECIMAL(10, 2),
    coverage_details TEXT,
    terms_conditions TEXT,
    validity_period VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (insurance_company_id) REFERENCES insurance_company(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- ANALYST TABLE
-- =====================================================
CREATE TABLE analyst (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(255),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- EMERGENCY SOS TEAM TABLE
-- =====================================================
CREATE TABLE emergency_sos_team (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20),
    emergency_hotline VARCHAR(20),
    availability VARCHAR(50),
    response_time VARCHAR(50),
    coverage_area TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- SESSIONS TABLE (Appointments/Bookings)
-- =====================================================
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    counsellor_id INT NOT NULL,
    clinic_id INT,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    session_type ENUM('Video', 'In-Person', 'Phone', 'Chat') DEFAULT 'Video',
    session_duration INT DEFAULT 60 COMMENT 'Duration in minutes',
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled', 'No-Show') DEFAULT 'Pending',
    notes TEXT,
    reason_for_visit TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (counsellor_id) REFERENCES counsellors(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinic(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DAILY LOGS TABLE (Mood Tracking)
-- =====================================================
CREATE TABLE daily_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    log_date DATE NOT NULL,
    mood_score INT CHECK (mood_score >= 1 AND mood_score <= 10),
    mood_description VARCHAR(50),
    stress_level ENUM('Low', 'Medium', 'High', 'Very High'),
    sleep_hours DECIMAL(3, 1),
    sleep_quality ENUM('Poor', 'Fair', 'Good', 'Excellent'),
    physical_activity VARCHAR(255),
    journal_entry TEXT,
    triggers TEXT,
    coping_strategies TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- CRISIS ALERT TABLE
-- =====================================================
CREATE TABLE crisis_alert (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    alert_type ENUM('Emergency SOS', 'High Risk', 'Suicide Ideation', 'Self Harm', 'Other') NOT NULL,
    severity_level ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    alert_message TEXT,
    location TEXT,
    alert_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Active',
    assigned_to INT COMMENT 'SOS team member ID',
    response_notes TEXT,
    resolution_notes TEXT,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES emergency_sos_team(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- RECOMMENDATIONS TABLE (AI/System Recommendations)
-- =====================================================
CREATE TABLE recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    recommendation_type ENUM('Therapy', 'Medication', 'Lifestyle', 'Exercise', 'Meditation', 'Other'),
    recommendation_text TEXT NOT NULL,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    source VARCHAR(100) COMMENT 'AI, Counsellor, System',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    is_followed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT SAMPLE DATA
-- =====================================================

-- Sample Admin
INSERT INTO admin (name, email, password, phone) VALUES
('System Administrator', 'admin@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1700-000000'),
('Dr. Sarah Ahmed', 'sarah.ahmed@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1700-000001');

-- Sample Users (Patients)
INSERT INTO users (first_name, last_name, email, password, phone, gender, date_of_birth, emergency_contact) VALUES
('Rahim', 'Khan', 'rahim.khan@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1711-111111', 'Male', '1995-05-15', 'Karim Khan: +880 1711-111112'),
('Fatima', 'Rahman', 'fatima.r@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1722-222222', 'Female', '1998-08-20', 'Ayesha Rahman: +880 1722-222223'),
('Kamal', 'Hossain', 'kamal.h@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1733-333333', 'Male', '1992-03-10', 'Salma Hossain: +880 1733-333334');

-- Sample Counsellors
INSERT INTO counsellors (name, email, password, phone, specialization, qualification, experience_years, license_number) VALUES
('Dr. Nusrat Jahan', 'nusrat.jahan@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1744-444444', 'Clinical Psychology, Depression & Anxiety', 'PhD in Clinical Psychology', 8, 'PSY-BD-12345'),
('Dr. Tanvir Ahmed', 'tanvir.ahmed@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1755-555555', 'Trauma & PTSD Specialist', 'MD Psychiatry', 10, 'PSY-BD-12346'),
('Counsellor Mehrin Islam', 'mehrin.islam@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1766-666666', 'Family & Relationship Counselling', 'Masters in Counselling Psychology', 5, 'COUNS-BD-12347');

-- Sample Clinics
INSERT INTO clinic (clinic_name, email, password, address, phone, operating_hours) VALUES
('MindCare Dhaka', 'dhaka@mindcare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'House 45, Road 27, Gulshan-1, Dhaka-1212', '+880 2-9876543', 'Sat-Thu: 9AM-8PM, Fri: Closed'),
('MindCare Chittagong', 'ctg@mindcare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Agrabad C/A, Chittagong-4100', '+880 31-654321', 'Sat-Thu: 10AM-7PM, Fri: Closed');

-- Sample Insurance Companies
INSERT INTO insurance_company (company_name, email, password, contact_number, address) VALUES
('Green Delta Insurance', 'info@greendelta.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 2-9565656', 'Green Delta Aims Tower, 51-52 Mohakhali C/A, Dhaka'),
('Pragati Life Insurance', 'support@pragatilife.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 2-8315213', 'Pragati Sarani, Shahjadpur, Dhaka');

-- Sample Insurance Plans
INSERT INTO insurance_plan (insurance_company_id, plan_name, plan_type, coverage_amount, premium_amount, coverage_details) VALUES
(1, 'Mental Health Gold Plan', 'Comprehensive', 500000.00, 15000.00, 'Full coverage for therapy sessions, medication, and hospitalization'),
(1, 'Mental Health Silver Plan', 'Standard', 250000.00, 8000.00, '80% coverage for therapy sessions and medication'),
(2, 'Complete Mind Care', 'Premium', 750000.00, 20000.00, '100% coverage including emergency SOS services');

-- Sample Analyst
INSERT INTO analyst (name, email, password, phone, specialization) VALUES
('Data Analyst Team', 'analyst@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1777-777777', 'Mental Health Data Analysis & Reporting');

-- Sample Emergency SOS Team
INSERT INTO emergency_sos_team (team_name, email, password, contact_number, emergency_hotline, availability) VALUES
('24/7 Crisis Response Team', 'sos@mentalhealth.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+880 1888-888888', '999', '24/7');

-- Sample Sessions
INSERT INTO sessions (user_id, counsellor_id, clinic_id, session_date, session_time, session_type, status, reason_for_visit) VALUES
(1, 1, 1, '2024-03-20', '10:00:00', 'Video', 'Confirmed', 'Anxiety management consultation'),
(2, 2, 1, '2024-03-21', '14:00:00', 'In-Person', 'Pending', 'Trauma counseling session'),
(3, 3, 2, '2024-03-22', '11:00:00', 'Phone', 'Confirmed', 'Family relationship counseling');

-- Sample Daily Logs
INSERT INTO daily_logs (user_id, log_date, mood_score, mood_description, stress_level, sleep_hours, sleep_quality, journal_entry) VALUES
(1, '2024-03-18', 7, 'Calm', 'Low', 7.5, 'Good', 'Had a productive day at work. Feeling optimistic.'),
(1, '2024-03-17', 5, 'Anxious', 'Medium', 6.0, 'Fair', 'Work deadline causing some stress.'),
(2, '2024-03-18', 6, 'Neutral', 'Medium', 7.0, 'Good', 'Regular day, practiced breathing exercises.');

-- Sample Crisis Alert
INSERT INTO crisis_alert (user_id, alert_type, severity_level, alert_message, status) VALUES
(3, 'High Risk', 'High', 'User reported severe depression symptoms and isolation', 'In Progress');

-- Sample Recommendations
INSERT INTO recommendations (user_id, recommendation_type, recommendation_text, priority, source) VALUES
(1, 'Therapy', 'Continue regular therapy sessions focusing on cognitive behavioral techniques', 'High', 'Counsellor'),
(1, 'Exercise', 'Incorporate 30 minutes of daily physical activity to improve mood', 'Medium', 'AI'),
(2, 'Meditation', 'Practice mindfulness meditation for 15 minutes daily', 'Medium', 'System');

-- =====================================================
-- VERIFICATION QUERY
-- =====================================================
SELECT 'Database Setup Complete!' as Status;

SELECT 'Table' as Type, 'admins' as Name, COUNT(*) as Count FROM admin
UNION ALL SELECT 'Table', 'users', COUNT(*) FROM users
UNION ALL SELECT 'Table', 'counsellors', COUNT(*) FROM counsellors
UNION ALL SELECT 'Table', 'clinics', COUNT(*) FROM clinic
UNION ALL SELECT 'Table', 'insurance_companies', COUNT(*) FROM insurance_company
UNION ALL SELECT 'Table', 'insurance_plans', COUNT(*) FROM insurance_plan
UNION ALL SELECT 'Table', 'analysts', COUNT(*) FROM analyst
UNION ALL SELECT 'Table', 'emergency_sos_teams', COUNT(*) FROM emergency_sos_team
UNION ALL SELECT 'Table', 'sessions', COUNT(*) FROM sessions
UNION ALL SELECT 'Table', 'daily_logs', COUNT(*) FROM daily_logs
UNION ALL SELECT 'Table', 'crisis_alerts', COUNT(*) FROM crisis_alert
UNION ALL SELECT 'Table', 'recommendations', COUNT(*) FROM recommendations;

-- =====================================================
-- IMPORTANT NOTES
-- =====================================================
-- 
-- Default Password Hash: password123
-- All sample accounts use password: password123
-- 
-- To generate new password hashes in PHP:
-- password_hash('your_password', PASSWORD_DEFAULT)
--
-- To verify passwords in PHP:
-- password_verify($input_password, $stored_hash)
--
-- Sample Login Credentials:
-- ----------------------
-- Admin: admin@mentalhealth.com / password123
-- User: rahim.khan@email.com / password123
-- Counsellor: nusrat.jahan@mentalhealth.com / password123
-- Clinic: dhaka@mindcare.com / password123
-- Insurance: info@greendelta.com / password123
-- Analyst: analyst@mentalhealth.com / password123
-- SOS Team: sos@mentalhealth.com / password123
--
-- =====================================================