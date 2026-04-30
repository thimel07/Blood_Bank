-- Create admin_users table in blood_bank_bd
CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(50) DEFAULT 'staff',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Copy admin users from blood_bank_system to blood_bank_bd
INSERT INTO admin_users (id, username, email, password, full_name, phone, role, status, created_at, updated_at)
SELECT id, username, email, password, full_name, phone, role, status, created_at, updated_at
FROM blood_bank_system.admin_users
ON DUPLICATE KEY UPDATE 
  email = VALUES(email),
  password = VALUES(password),
  full_name = VALUES(full_name),
  phone = VALUES(phone),
  role = VALUES(role),
  status = VALUES(status),
  updated_at = VALUES(updated_at);

-- Verify the table
SELECT * FROM admin_users;