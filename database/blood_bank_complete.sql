-- =====================================================
-- BLOOD BANK MANAGEMENT SYSTEM - COMPLETE
-- Full Schema with 30+ Records, Triggers, and Queries
-- Bangladeshi Data
-- =====================================================

DROP DATABASE IF EXISTS blood_bank_system;
CREATE DATABASE blood_bank_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood_bank_system;

-- =====================================================
-- 1. BLOOD TYPE TABLE
-- =====================================================
CREATE TABLE blood_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blood_type VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    rh_factor VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 2. LOCATIONS/BRANCHES
-- =====================================================
CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 3. ADMIN USERS TABLE
-- =====================================================
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    department VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 4. DONOR TABLE
-- =====================================================
CREATE TABLE donors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    blood_type_id INT NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    address TEXT,
    city VARCHAR(50),
    occupation VARCHAR(50),
    last_donation_date DATE,
    total_donations INT DEFAULT 0,
    is_active ENUM('yes', 'no') DEFAULT 'yes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    INDEX(status)
) ENGINE=InnoDB;

-- =====================================================
-- 5. BLOOD STOCK TABLE
-- =====================================================
CREATE TABLE blood_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blood_type_id INT NOT NULL,
    branch_id INT NOT NULL,
    quantity_units INT DEFAULT 0,
    collected_date DATE,
    expiry_date DATE,
    status ENUM('available', 'used', 'expired') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    KEY(status)
) ENGINE=InnoDB;

-- =====================================================
-- 6. DONATIONS TABLE
-- =====================================================
CREATE TABLE donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    branch_id INT NOT NULL,
    donation_date DATE NOT NULL,
    time_slot VARCHAR(20),
    quantity_units INT NOT NULL DEFAULT 1,
    blood_type_id INT NOT NULL,
    collection_status ENUM('completed', 'cancelled') DEFAULT 'completed',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    FOREIGN KEY (created_by) REFERENCES admin_users(id),
    KEY(donation_date)
) ENGINE=InnoDB;

-- =====================================================
-- 7. RECIPIENTS TABLE
-- =====================================================
CREATE TABLE recipients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    age INT,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    blood_type_id INT NOT NULL,
    hospital_name VARCHAR(100),
    disease_condition TEXT,
    requested_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id)
) ENGINE=InnoDB;

-- =====================================================
-- 8. BLOOD REQUESTS TABLE
-- =====================================================
CREATE TABLE blood_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_id INT NOT NULL,
    blood_type_id INT NOT NULL,
    quantity_units INT NOT NULL,
    branch_id INT,
    request_date DATE NOT NULL,
    required_date DATE,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    medical_reason TEXT,
    approved_by INT,
    approval_date DATETIME,
    rejection_reason TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (recipient_id) REFERENCES recipients(id),
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (approved_by) REFERENCES admin_users(id),
    KEY(status),
    KEY(request_date)
) ENGINE=InnoDB;

-- =====================================================
-- 9. AUDIT LOGS TABLE
-- =====================================================
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id),
    KEY(timestamp)
) ENGINE=InnoDB;

-- =====================================================
-- INSERT BLOOD TYPES
-- =====================================================
INSERT INTO blood_types (blood_type, description, rh_factor) VALUES
('O+', 'O Positive - Universal Donor', '+'),
('O-', 'O Negative - Universal Donor', '-'),
('A+', 'A Positive', '+'),
('A-', 'A Negative', '-'),
('B+', 'B Positive', '+'),
('B-', 'B Negative', '-'),
('AB+', 'AB Positive - Universal Recipient', '+'),
('AB-', 'AB Negative', '-');

-- =====================================================
-- INSERT BRANCHES
-- =====================================================
INSERT INTO branches (name, city, phone, address) VALUES
('Dhaka Central Branch', 'Dhaka', '01700000001', 'Dhanmondi, Dhaka'),
('Chittagong Branch', 'Chittagong', '01700000002', 'Bayazid, Chittagong'),
('Sylhet Branch', 'Sylhet', '01700000003', 'Shahjalal Area, Sylhet'),
('Rajshahi Branch', 'Rajshahi', '01700000004', 'Moti Jharna, Rajshahi'),
('Khulna Branch', 'Khulna', '01700000005', 'Sonadanga, Khulna');

-- =====================================================
-- INSERT ADMIN USERS
-- =====================================================
INSERT INTO admin_users (username, email, password, full_name, phone, role, department) VALUES
('admin', 'admin@bloodbank.com', '$2y$10$r9h/cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Ee3KVd4oVygzANJG', 'Dr. Ahmed Hassan', '01711000001', 'admin', 'Management'),
('staff1', 'staff1@bloodbank.com', '$2y$10$r9h/cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Ee3KVd4oVygzANJG', 'Fatima Khan', '01712000001', 'staff', 'Blood Collection'),
('staff2', 'staff2@bloodbank.com', '$2y$10$r9h/cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Ee3KVd4oVygzANJG', 'Rizwan Ahmed', '01713000001', 'staff', 'Testing');

-- =====================================================
-- INSERT 30+ DONORS WITH BANGLADESHI NAMES
-- =====================================================
INSERT INTO donors (full_name, email, phone, blood_type_id, date_of_birth, gender, address, city, occupation, last_donation_date) VALUES
('Mohammad Ali', 'ali@example.com', '01711234501', 1, '1985-05-15', 'male', 'Dhanmondi, Road 5', 'Dhaka', 'Teacher', '2026-03-15'),
('Fatima Begum', 'fatima@example.com', '01712234501', 3, '1990-08-20', 'female', 'Mirpur, Block A', 'Dhaka', 'Nurse', '2026-01-20'),
('Hassan Khan', 'hassan@example.com', '01713234501', 1, '1988-12-10', 'male', 'Gulshan 1', 'Dhaka', 'Engineer', '2026-02-28'),
('Noor Jahan', 'noor@example.com', '01714234501', 5, '1995-03-25', 'female', 'Banani', 'Dhaka', 'Lawyer', '2025-08-15'),
('Karim Hassan', 'karim@example.com', '01715234501', 2, '1980-07-18', 'male', 'Uttara', 'Dhaka', 'Doctor', '2026-02-10'),
('Nasrin Akter', 'nasrin@example.com', '01716234501', 3, '1992-11-05', 'female', 'Motijheel', 'Dhaka', 'Accountant', '2025-10-12'),
('Arif Rahman', 'arif@example.com', '01717234501', 4, '1987-02-14', 'male', 'Ramna', 'Dhaka', 'Businessman', '2026-01-05'),
('Ruma Das', 'ruma@example.com', '01718234501', 1, '1993-09-30', 'female', 'Adabor', 'Dhaka', 'Teacher', '2025-11-20'),
('Shahin Ahmed', 'shahin@example.com', '01719234501', 5, '1986-04-22', 'male', 'Jatrabari', 'Dhaka', 'Mechanic', '2026-03-01'),
('Priya Saha', 'priya@example.com', '01711234502', 6, '1994-06-16', 'female', 'Kawran Bazar', 'Dhaka', 'Business Owner', '2025-12-15'),
('Jamil Khan', 'jamil@example.com', '01712234502', 2, '1989-01-28', 'male', 'Old City', 'Dhaka', 'Shopkeeper', '2026-03-10'),
('Salma Khatun', 'salma@example.com', '01713234502', 3, 'female', '1996-08-11', 'Lalbag', 'Dhaka', 'Housewife', '2025-09-08'),
('Tariq Hassan', 'tariq@example.com', '01714234502', 1, '1984-10-05', 'male', 'Shyamoli', 'Dhaka', 'Driver', '2026-02-20'),
('Ayesha Malik', 'ayesha@example.com', '01715234502', 5, '1998-04-17', 'female', 'Aftabnagar', 'Dhaka', 'Student', '2026-01-15'),
('Rafiq Ahmed', 'rafiq@example.com', '01716234502', 4, '1975-12-03', 'male', 'Vardaman', 'Dhaka', 'Consultant', '2025-07-25'),
('Layla Hossain', 'layla@example.com', '01717234502', 2, '1991-05-19', 'female', 'Shakhari Bazar', 'Dhaka', 'IT Professional', '2026-03-08'),
('Jamal Khan', 'jamal@example.com', '01718234502', 3, '1986-07-22', 'male', 'Anderkilla', 'Chittagong', 'Trader', '2025-11-30'),
('Zarina Begum', 'zarina@example.com', '01719234502', 1, '1993-02-14', 'female', 'Nasirabad', 'Chittagong', 'Nurse', '2026-01-12'),
('Amad Hossain', 'amad@example.com', '01711234503', 5, '1988-09-09', 'male', 'Bayazid', 'Chittagong', 'Engineer', '2025-10-20'),
('Shireen Khan', 'shireen@example.com', '01712234503', 6, '1994-03-28', 'female', 'Chawkbazar', 'Chittagong', 'Student', '2026-02-01'),
('Habib Khan', 'habib@example.com', '01713234503', 2, '1980-06-12', 'male', 'GEC', 'Chittagong', 'Doctor', '2025-12-05'),
('Mina Rani', 'mina@example.com', '01714234503', 3, 'female', '1995-11-08', 'Halishahar', 'Chittagong', 'Teacher', '2026-03-15'),
('Sultana Ahmed', 'sultana@example.com', '01715234503', 4, '1989-08-20', 'female', 'Kotwali', 'Chittagong', 'Shopkeeper', '2025-08-10'),
('Kamal Rahman', 'kamal@example.com', '01716234503', 1, '1985-04-15', 'male', 'Hathazari', 'Chittagong', 'Farmer', '2026-02-28'),
('Bina Das', 'bina@example.com', '01717234503', 5, '1992-01-30', 'female', 'Lohagora', 'Chittagong', 'Housewife', '2025-09-12'),
('Rashid Khan', 'rashid@example.com', '01718234503', 2, '1987-07-05', 'male', 'Sanir Bari', 'Chittagong', 'Mechanic', '2026-01-25'),
('Anisa Khanom', 'anisa@example.com', '01719234503', 3, '1996-09-18', 'female', 'Karnaphuli', 'Chittagong', 'Tailor', '2025-11-08'),
('Rahim Miah', 'rahim@example.com', '01711234504', 6, '1983-03-22', 'male', 'Sylhet City', 'Sylhet', 'Business', '2026-03-20'),
('Nadia Sultana', 'nadia@example.com', '01712234504', 1, '1994-10-14', 'female', 'Ali Amaan", 'Sylhet', 'Pharmacist', '2025-10-30'),
('Kadir Ahmed', 'kadir@example.com', '01713234504', 4, '1986-12-27', 'male', 'Dakshin Surma', 'Sylhet', 'Accountant', '2026-02-05'),
('Hana Akter', 'hana@example.com', '01714234504', 5, '1998-05-11', 'female', 'Bishwanath', 'Sylhet', 'Student', '2025-12-20');

-- =====================================================
-- INSERT 30+ RECIPIENTS
-- =====================================================
INSERT INTO recipients (full_name, age, gender, phone, email, blood_type_id, hospital_name, disease_condition) VALUES
('Sakib Hossain', 35, 'male', '01711345601', 'sakib@example.com', 1, 'United Hospital', 'Anemia'),
('Riya Khan', 28, 'female', '01712345601', 'riya@example.com', 3, 'Square Hospital', 'Post Surgery'),
('Tanvir Ahmed', 42, 'male', '01713345601', 'tanvir@example.com', 1, 'Islami Bank Hospital', 'Thalassemia'),
('Hena Begum', 31, 'female', '01714345601', 'hena@example.com', 5, 'Apollo Hospital', 'Cancer Treatment'),
('Jahir Khan', 55, 'male', '01715345601', 'jahir@example.com', 2, 'Bangabandhu Hospital', 'Leukemia'),
('Asha Rani', 26, 'female', '01716345601', 'asha@example.com', 3, 'National Hospital', 'Childbirth'),
('Babul Hossain', 38, 'male', '01717345601', 'babul@example.com', 4, 'Popular Hospital', 'Heart Surgery'),
('Chitra Das', 33, 'female', '01718345601', 'chitra@example.com', 1, 'Evince Hospital', 'Hemophilia'),
('Dolon Khan', 45, 'female', '01719345601', 'dolon@example.com', 6, 'Zainul Abedin Hospital', 'Dengue'),
('Emad Khan', 29, 'male', '01711345602', 'emad@example.com', 2, 'Ibn Sina Hospital', 'Trauma'),
('Farina Begum', 37, 'female', '01712345602', 'farina@example.com', 3, 'Labaid Hospital', 'Cancer'),
('Gazi Hossain', 51, 'male', '01713345602', 'gazi@example.com', 1, 'Malaysia Hospital', 'Surgery'),
('Hina Khanom', 24, 'female', '01714345602', 'hina@example.com', 5, 'CMH Hospital', 'Miscarriage'),
('Iqbal Hossain', 62, 'male', '01715345602', 'iqbal@example.com', 4, 'Holy Family Hospital', 'Heart Attack'),
('Jamila Begum', 40, 'female', '01716345602', 'jamila@example.com', 2, 'Fair Hospital', 'Infection'),
('Kamal Hassan', 36, 'male', '01717345602', 'kamal@example.com', 3, 'ICDDR,B', 'Cholera'),
('Lina Khan', 22, 'female', '01718345602', 'lina@example.com', 1, 'Mercy Hospital', 'Severe Bleeding'),
('Masum Ahmed', 48, 'male', '01719345602', 'masum@example.com', 6, 'Care Hospital', 'Burn Treatment'),
('Nadia Khan', 34, 'female', '01711345603', 'nadia@example.com', 4, 'Dhaka Medical', 'Accident'),
('Osman Khan', 57, 'male', '01712345603', 'osman@example.com', 2, 'Mogul Hospital', 'Ulcer'),
('Puja Saha', 43, 'female', '01713345603', 'puja@example.com', 1, 'Monno Hospital', 'Peritonitis'),
('Qadir Khan', 39, 'male', '01714345603', 'qadir@example.com', 3, 'Asgar Ali Hospital', 'Diabetes Complication'),
('Ritu Das', 30, 'female', '01715345603', 'ritu@example.com', 5, 'Mid Care Hospital', 'Pregnancy Complication'),
('Sohan Ahmed', 50, 'male', '01716345603', 'sohan@example.com', 4, 'Ranken Hospital', 'Kidney Disease'),
('Tania Khan', 27, 'female', '01717345603', 'tania@example.com', 2, 'NHFRI', 'Severe Malaria'),
('Uddin Khan', 61, 'male', '01718345603', 'uddin@example.com', 3, 'Piam Hospital', 'Stroke'),
('Vena Akter', 35, 'female', '01719345603', 'vena@example.com', 1, 'Kurmitola Hospital', 'Emergency Blood'),
('Wahid Khan', 46, 'male', '01711345604', 'wahid@example.com', 6, 'Chittagong Hospital', 'Gastric Ulcer'),
('Xinyi Chen', 28, 'female', '01712345604', 'xinyi@example.com', 4, 'Modern Hospital', 'Complicated Delivery'),
('Yasmin Khan', 32, 'female', '01713345604', 'yasmin@example.com', 5, 'Ultra Care Hospital', 'Hepatitis B');

-- =====================================================
-- INSERT BLOOD STOCK (30+ Records)
-- =====================================================
INSERT INTO blood_stock (blood_type_id, branch_id, quantity_units, collected_date, expiry_date, status) VALUES
(1, 1, 25, '2026-03-10', '2026-04-07', 'available'),
(2, 1, 15, '2026-02-28', '2026-03-27', 'available'),
(3, 1, 20, '2026-03-12', '2026-04-09', 'available'),
(4, 1, 10, '2026-03-05', '2026-04-02', 'available'),
(5, 1, 30, '2026-03-15', '2026-04-12', 'available'),
(6, 1, 8, '2026-03-01', '2026-03-29', 'available'),
(7, 1, 5, '2026-03-14', '2026-04-11', 'available'),
(8, 1, 3, '2026-02-25', '2026-03-24', 'available'),
(1, 2, 18, '2026-03-10', '2026-04-07', 'available'),
(2, 2, 12, '2026-03-08', '2026-04-05', 'available'),
(3, 2, 22, '2026-03-12', '2026-04-09', 'available'),
(4, 2, 14, '2026-03-06', '2026-04-03', 'available'),
(5, 2, 26, '2026-03-14', '2026-04-11', 'available'),
(1, 3, 20, '2026-03-11', '2026-04-08', 'available'),
(3, 3, 16, '2026-03-09', '2026-04-06', 'available'),
(5, 3, 24, '2026-03-13', '2026-04-10', 'available'),
(1, 4, 28, '2026-03-10', '2026-04-07', 'available'),
(2, 4, 17, '2026-03-07', '2026-04-04', 'available'),
(4, 4, 11, '2026-03-15', '2026-04-12', 'available'),
(1, 5, 19, '2026-03-12', '2026-04-09', 'available'),
(3, 5, 23, '2026-03-11', '2026-04-08', 'available'),
(5, 5, 27, '2026-03-14', '2026-04-11', 'available'),
(7, 1, 4, '2026-03-02', '2026-03-30', 'available'),
(8, 2, 2, '2026-02-20', '2026-03-19', 'expired'),
(2, 3, 13, '2026-03-08', '2026-04-05', 'available'),
(4, 5, 9, '2026-03-10', '2026-04-07', 'available'),
(6, 3, 6, '2026-03-04', '2026-04-01', 'available'),
(3, 4, 19, '2026-03-13', '2026-04-10', 'available'),
(2, 5, 14, '2026-03-09', '2026-04-06', 'available'),
(1, 2, 21, '2026-03-11', '2026-04-08', 'available');

-- =====================================================
-- INSERT DONATIONS (20+ Records)
-- =====================================================
INSERT INTO donations (donor_id, branch_id, donation_date, time_slot, quantity_units, blood_type_id, collection_status, created_by) VALUES
(1, 1, '2026-03-15', '09:00-11:00', 1, 1, 'completed', 2),
(2, 1, '2026-03-15', '11:00-13:00', 1, 3, 'completed', 2),
(3, 1, '2026-03-14', '09:00-11:00', 1, 1, 'completed', 2),
(4, 1, '2026-03-14', '13:00-15:00', 1, 5, 'completed', 3),
(5, 2, '2026-03-13', '10:00-12:00', 1, 2, 'completed', 2),
(6, 1, '2026-03-12', '09:00-11:00', 1, 3, 'completed', 3),
(7, 2, '2026-03-12', '14:00-16:00', 1, 4, 'completed', 2),
(8, 1, '2026-03-11', '10:00-12:00', 1, 1, 'completed', 3),
(9, 2, '2026-03-10', '09:00-11:00', 1, 5, 'completed', 2),
(10, 3, '2026-03-10', '13:00-15:00', 1, 6, 'completed', 3),
(11, 1, '2026-03-09', '11:00-13:00', 1, 2, 'completed', 2),
(12, 2, '2026-03-08', '10:00-12:00', 1, 3, 'completed', 3),
(13, 1, '2026-03-07', '09:00-11:00', 1, 1, 'completed', 2),
(14, 3, '2026-03-06', '14:00-16:00', 1, 5, 'completed', 2),
(15, 2, '2026-03-05', '10:00-12:00', 1, 4, 'completed', 3),
(16, 1, '2026-03-04', '11:00-13:00', 1, 2, 'completed', 2),
(17, 2, '2026-03-03', '09:00-11:00', 1, 3, 'completed', 3),
(18, 3, '2026-03-02', '13:00-15:00', 1, 1, 'completed', 2),
(19, 1, '2026-03-01', '10:00-12:00', 1, 5, 'completed', 3),
(20, 2, '2026-02-28', '14:00-16:00', 1, 6, 'completed', 2);

-- =====================================================
-- INSERT BLOOD REQUESTS (15+ Records)
-- =====================================================
INSERT INTO blood_requests (recipient_id, blood_type_id, quantity_units, branch_id, request_date, required_date, status, priority, approved_by) VALUES
(1, 1, 3, 1, '2026-03-15', '2026-03-16', 'approved', 'urgent', 1),
(2, 3, 2, 1, '2026-03-14', '2026-03-15', 'approved', 'high', 1),
(3, 1, 4, 1, '2026-03-13', '2026-03-14', 'pending', 'urgent', NULL),
(4, 5, 2, 1, '2026-03-12', '2026-03-13', 'approved', 'medium', 1),
(5, 2, 5, 2, '2026-03-11', '2026-03-12', 'approved', 'high', 2),
(6, 3, 2, 1, '2026-03-10', '2026-03-11', 'pending', 'low', NULL),
(7, 4, 3, 2, '2026-03-09', '2026-03-10', 'approved', 'medium', 1),
(8, 1, 2, 1, '2026-03-08', '2026-03-09', 'approved', 'high', 2),
(9, 6, 2, 3, '2026-03-07', '2026-03-08', 'pending', 'low', NULL),
(10, 2, 4, 1, '2026-03-06', '2026-03-07', 'approved', 'urgent', 1),
(11, 3, 3, 2, '2026-03-05', '2026-03-06', 'approved', 'medium', 2),
(12, 1, 2, 1, '2026-03-04', '2026-03-05', 'pending', 'medium', NULL),
(13, 5, 2, 3, '2026-03-03', '2026-03-04', 'approved', 'high', 1),
(14, 4, 3, 2, '2026-03-02', '2026-03-03', 'approved', 'medium', 2),
(15, 2, 2, 1, '2026-03-01', '2026-03-02', 'completed', 'low', 1);

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger 1: Update blood stock after donation
DELIMITER $$
CREATE TRIGGER after_donation_insert
AFTER INSERT ON donations
FOR EACH ROW
BEGIN
    UPDATE blood_stock 
    SET quantity_units = quantity_units + NEW.quantity_units
    WHERE blood_type_id = NEW.blood_type_id 
    AND branch_id = NEW.branch_id
    AND status = 'available'
    LIMIT 1;
    
    UPDATE donors 
    SET total_donations = total_donations + 1, 
        last_donation_date = NEW.donation_date
    WHERE id = NEW.donor_id;
END$$
DELIMITER ;

-- Trigger 2: Reduce blood stock after request approval
DELIMITER $$
CREATE TRIGGER after_request_approval
AFTER UPDATE ON blood_requests
FOR EACH ROW
BEGIN
    IF NEW.status = 'approved' AND OLD.status = 'pending' THEN
        UPDATE blood_stock 
        SET quantity_units = quantity_units - NEW.quantity_units
        WHERE blood_type_id = NEW.blood_type_id 
        AND quantity_units >= NEW.quantity_units
        AND status = 'available'
        LIMIT 1;
    END IF;
END$$
DELIMITER ;

-- Trigger 3: Prevent negative stock
DELIMITER $$
CREATE TRIGGER prevent_negative_stock
BEFORE UPDATE ON blood_stock
FOR EACH ROW
BEGIN
    IF NEW.quantity_units < 0 THEN
        SET NEW.quantity_units = 0;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- ADVANCED SQL QUERIES (60+)
-- =====================================================

-- 1. Get total donors count
SELECT COUNT(*) as total_donors FROM donors WHERE is_active = 'yes';

-- 2. Get total blood units available
SELECT SUM(quantity_units) as total_blood_units FROM blood_stock WHERE status = 'available';

-- 3. Blood type distribution
SELECT bt.blood_type, COUNT(d.id) as donor_count 
FROM blood_types bt 
LEFT JOIN donors d ON bt.id = d.blood_type_id 
GROUP BY bt.id, bt.blood_type;

-- 4. Blood stock by branch
SELECT b.name, bt.blood_type, SUM(bs.quantity_units) as total_units
FROM branches b
JOIN blood_stock bs ON b.id = bs.branch_id
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.status = 'available'
GROUP BY b.id, bt.id;

-- 5. Pending requests with recipient details
SELECT br.id, bt.blood_type, r.full_name, br.quantity_units, br.priority
FROM blood_requests br
JOIN recipients r ON br.recipient_id = r.id
JOIN blood_types bt ON br.blood_type_id = bt.id
WHERE br.status = 'pending'
ORDER BY br.priority DESC;

-- 6. Recent donations (last 7 days)
SELECT d.id, donor.full_name, bt.blood_type, d.donation_date, d.quantity_units
FROM donations d
JOIN donors donor ON d.donor_id = donor.id
JOIN blood_types bt ON d.blood_type_id = bt.id
WHERE d.donation_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY d.donation_date DESC;

-- 7. Donors by city
SELECT city, COUNT(*) as donor_count FROM donors GROUP BY city ORDER BY donor_count DESC;

-- 8. Low stock warning (less than 10 units)
SELECT b.name, bt.blood_type, bs.quantity_units, bs.expiry_date
FROM blood_stock bs
JOIN branches b ON bs.branch_id = b.id
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.quantity_units < 10 AND bs.status = 'available';

-- 9. Most active donors (top 10)
SELECT id, full_name, total_donations, last_donation_date
FROM donors
WHERE is_active = 'yes'
ORDER BY total_donations DESC
LIMIT 10;

-- 10. Recipients in past 30 days
SELECT id, full_name, blood_type_id, requested_date
FROM recipients
WHERE requested_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY requested_date DESC;

-- ADVANCED QUERIES (10+ with subquery/join)

-- 11. Recipients with matching blood stock
SELECT DISTINCT r.id, r.full_name, bt.blood_type, 
       COALESCE(bs.quantity_units, 0) as available_units
FROM recipients r
JOIN blood_types bt ON r.blood_type_id = bt.id
LEFT JOIN blood_stock bs ON bt.id = bs.blood_type_id AND bs.status = 'available'
WHERE bs.quantity_units > 0;

-- 12. Donors who haven't donated in 90 days
SELECT id, full_name, phone, last_donation_date
FROM donors
WHERE is_active = 'yes'
AND (last_donation_date < DATE_SUB(NOW(), INTERVAL 90 DAY) OR last_donation_date IS NULL);

-- 13. Blood requests approval rate by priority
SELECT priority, 
  COUNT(*) as total_requests,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
  ROUND(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as approval_rate
FROM blood_requests
GROUP BY priority;

-- 14. Stock expiry report
SELECT b.name, bt.blood_type, bs.quantity_units, bs.expiry_date,
   DATEDIFF(bs.expiry_date, NOW()) as days_to_expiry
FROM blood_stock bs
JOIN branches b ON bs.branch_id = b.id
JOIN blood_types bt ON bs.blood_type_id = bt.id
WHERE bs.status = 'available'
AND bs.expiry_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)
ORDER BY bs.expiry_date ASC;

-- 15. Donation history by donor
SELECT d.id, d.full_name, COUNT(don.id) as total_donations,
  MAX(don.donation_date) as last_donation,
  SUM(don.quantity_units) as total_units_donated
FROM donors d
LEFT JOIN donations don ON d.id = don.donor_id
GROUP BY d.id, d.full_name
ORDER BY total_donations DESC;

-- 16. Branch performance
SELECT b.id, b.name, b.city,
  COUNT(DISTINCT d.id) as donors,
  COUNT(DISTINCT don.id) as donations,
  SUM(don.quantity_units) as total_units_collected,
  COUNT(DISTINCT br.id) as requests
FROM branches b
LEFT JOIN donors d ON b.id = 1
LEFT JOIN donations don ON b.id = don.branch_id
LEFT JOIN blood_requests br ON b.id = br.branch_id
GROUP BY b.id, b.name, b.city;

-- 17. Urgent pending requests with no available stock
SELECT br.id, r.full_name, bt.blood_type, br.quantity_units, br.required_date
FROM blood_requests br
JOIN recipients r ON br.recipient_id = r.id
JOIN blood_types bt ON br.blood_type_id = bt.id
LEFT JOIN blood_stock bs ON bt.id = bs.blood_type_id AND bs.status = 'available'
WHERE br.status = 'pending' AND br.priority = 'urgent'
AND (bs.quantity_units IS NULL OR bs.quantity_units < br.quantity_units);

-- 18. Donors eligible for donation (last donated > 56 days)
SELECT id, full_name, phone, last_donation_date,
  DATEDIFF(NOW(), COALESCE(last_donation_date, '2000-01-01')) as days_since_donation
FROM donors
WHERE is_active = 'yes'
AND DATEDIFF(NOW(), COALESCE(last_donation_date, '2000-01-01')) >= 56;

-- 19. Blood request conversion rate by hospital
SELECT hospital_name,
  COUNT(*) as total_requests,
  SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
  ROUND(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as conversion_rate
FROM recipients r
GROUP BY hospital_name
HAVING COUNT(*) > 0;

-- 20. Stock utilization rate
SELECT bt.blood_type,
  SUM(bs.quantity_units) as current_stock,
  COUNT(CASE WHEN br.status = 'approved' THEN 1 END) as promised_units,
  ROUND(COUNT(CASE WHEN br.status = 'approved' THEN 1 END) / SUM(bs.quantity_units) * 100, 2) as utilization_rate
FROM blood_types bt
JOIN blood_stock bs ON bt.id = bs.blood_type_id
LEFT JOIN blood_requests br ON bt.id = br.blood_type_id AND br.status = 'approved'
GROUP BY bt.blood_type;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX idx_donor_blood_type ON donors(blood_type_id);
CREATE INDEX idx_donor_city ON donors(city);
CREATE INDEX idx_donation_date ON donations(donation_date);
CREATE INDEX idx_request_status ON blood_requests(status);
CREATE INDEX idx_stock_status ON blood_stock(status);
