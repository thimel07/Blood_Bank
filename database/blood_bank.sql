-- Blood Bank Management System Database Schema

CREATE DATABASE IF NOT EXISTS blood_bank_system;
USE blood_bank_system;

-- Admin/Staff Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(50) DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Blood Type Table
CREATE TABLE IF NOT EXISTS blood_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blood_type VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Donor Table
CREATE TABLE IF NOT EXISTS donors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    blood_type_id INT NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    medical_history TEXT,
    last_donation_date DATE,
    donation_count INT DEFAULT 0,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id)
) ENGINE=InnoDB;

-- Donation Table
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    blood_type_id INT NOT NULL,
    quantity_ml INT NOT NULL DEFAULT 450,
    donation_date DATETIME NOT NULL,
    expiry_date DATE NOT NULL,
    health_status VARCHAR(100),
    hemoglobin_level DECIMAL(5, 2),
    blood_pressure VARCHAR(20),
    temperature DECIMAL(5, 2),
    status ENUM('collected', 'tested', 'approved', 'discarded') DEFAULT 'collected',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(id),
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id)
) ENGINE=InnoDB;

-- Blood Stock Table
CREATE TABLE IF NOT EXISTS blood_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blood_type_id INT NOT NULL UNIQUE,
    quantity_units INT DEFAULT 0,
    quantity_ml INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id)
) ENGINE=InnoDB;

-- Patient/Recipient Table
CREATE TABLE IF NOT EXISTS patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    blood_type_id INT NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    hospital_name VARCHAR(100),
    ward_number VARCHAR(50),
    address TEXT,
    city VARCHAR(50),
    medical_condition TEXT,
    admitted_date DATE,
    status ENUM('active', 'discharged', 'deceased') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id)
) ENGINE=InnoDB;

-- Blood Request Table
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    blood_type_id INT NOT NULL,
    units_required INT NOT NULL,
    urgency_level ENUM('routine', 'urgent', 'emergency') DEFAULT 'routine',
    request_date DATETIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'fulfilled', 'cancelled') DEFAULT 'pending',
    approved_by INT,
    approval_date DATETIME,
    fulfilled_date DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (blood_type_id) REFERENCES blood_types(id),
    FOREIGN KEY (approved_by) REFERENCES admin_users(id)
) ENGINE=InnoDB;

-- Receipt/Invoice Table
CREATE TABLE IF NOT EXISTS receipts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_number VARCHAR(50) UNIQUE NOT NULL,
    donation_id INT,
    request_id INT,
    transaction_type ENUM('donation', 'request') NOT NULL,
    donor_id INT,
    patient_id INT,
    blood_type VARCHAR(10),
    quantity_ml INT,
    receipt_date DATETIME NOT NULL,
    issued_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donation_id) REFERENCES donations(id),
    FOREIGN KEY (request_id) REFERENCES blood_requests(id),
    FOREIGN KEY (donor_id) REFERENCES donors(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (issued_by) REFERENCES admin_users(id)
) ENGINE=InnoDB;

-- Statistics/Audit Log
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
) ENGINE=InnoDB;

-- Insert Blood Types
INSERT INTO blood_types (blood_type, description) VALUES
('O+', 'O Positive - Universal Donor'),
('O-', 'O Negative - Universal Donor'),
('A+', 'A Positive'),
('A-', 'A Negative'),
('B+', 'B Positive'),
('B-', 'B Negative'),
('AB+', 'AB Positive - Universal Recipient'),
('AB-', 'AB Negative');

-- Insert Default Admin User (username: admin, password: admin123)
INSERT INTO admin_users (username, email, password, full_name, phone, role) 
VALUES ('admin', 'admin@bloodbank.com', '$2y$10$dXJ3SVp1YVRrN0RybWRPLk5pWnpkWXpSekUzRTA1MTAxMjM0NTY3ZQ==', 'System Administrator', '1234567890', 'admin');

-- Insert Initial Blood Stock
INSERT INTO blood_stock (blood_type_id, quantity_units, quantity_ml)
SELECT id, 0, 0 FROM blood_types;

-- Create Trigger to Update Stock After Donation
DELIMITER $$

CREATE TRIGGER update_stock_after_donation
AFTER UPDATE ON donations
FOR EACH ROW
BEGIN
    IF NEW.status = 'approved' AND OLD.status != 'approved' THEN
        UPDATE blood_stock 
        SET quantity_units = quantity_units + 1,
            quantity_ml = quantity_ml + NEW.quantity_ml
        WHERE blood_type_id = NEW.blood_type_id;
        
        UPDATE donors 
        SET last_donation_date = NEW.donation_date,
            donation_count = donation_count + 1
        WHERE id = NEW.donor_id;
    END IF;
END$$

-- Create Trigger to Reduce Stock After Request Fulfillment
CREATE TRIGGER update_stock_after_request
AFTER UPDATE ON blood_requests
FOR EACH ROW
BEGIN
    IF NEW.status = 'fulfilled' AND OLD.status != 'fulfilled' THEN
        UPDATE blood_stock 
        SET quantity_units = quantity_units - NEW.units_required
        WHERE blood_type_id = NEW.blood_type_id;
    END IF;
END$$

DELIMITER ;

-- Create Indexes for Performance
CREATE INDEX idx_donor_blood_type ON donors(blood_type_id);
CREATE INDEX idx_donor_status ON donors(status);
CREATE INDEX idx_donation_date ON donations(donation_date);
CREATE INDEX idx_donation_status ON donations(status);
CREATE INDEX idx_patient_blood_type ON patients(blood_type_id);
CREATE INDEX idx_request_status ON blood_requests(status);
CREATE INDEX idx_request_date ON blood_requests(request_date);
CREATE INDEX idx_receipt_date ON receipts(receipt_date);
