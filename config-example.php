<?php
/**
 * Configuration Example File
 * Copy this file to config.php and update with your values
 * This file is for reference only
 */

// ===================== DATABASE CONFIGURATION =====================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty for XAMPP default
define('DB_NAME', 'blood_bank_system');
define('DB_CHARSET', 'utf8mb4');

// ===================== APPLICATION CONFIGURATION =====================
// Hospital/Organization Details
define('APP_NAME', 'Blood Bank Management System');
define('HOSPITAL_NAME', 'City Blood Bank');
define('HOSPITAL_ADDRESS', '123 Medical Center, Healthcare City');
define('HOSPITAL_PHONE', '+1-800-BLOOD-1');
define('HOSPITAL_EMAIL', 'info@bloodbank.com');
define('HOSPITAL_WEBSITE', 'https://bloodbank.com');

// ===================== SYSTEM SETTINGS =====================
// Enable/Disable Features
define('ENABLE_EMAIL_NOTIFICATIONS', false);    // Set to true when configured
define('ENABLE_SMS_NOTIFICATIONS', false);      // Requires SMS gateway
define('ENABLE_PDF_EXPORT', false);             // Requires dompdf library
define('ENABLE_ADVANCED_REPORTS', true);

// Session Configuration
define('SESSION_TIMEOUT', 1800);                // 30 minutes in seconds
define('SESSION_SECURE', false);                // Set to true for HTTPS only
define('SESSION_HTTP_ONLY', true);              // Prevent JS access to session

// ===================== SECURITY SETTINGS =====================
// Security Keys
define('APP_SECRET_KEY', 'change_this_to_random_string_min_32_chars');
define('ENCRYPTION_KEY', 'another_random_string_min_32_chars');

// Password Policy
define('MIN_PASSWORD_LENGTH', 8);
define('REQUIRE_SPECIAL_CHARS', true);
define('REQUIRE_UPPERCASE', true);
define('REQUIRE_NUMBERS', true);

// Login Settings
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);              // 15 minutes
define('PASSWORD_EXPIRY_DAYS', 90);

// ===================== EMAIL CONFIGURATION =====================
// SMTP Settings (for notifications)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@bloodbank.com');
define('SMTP_FROM_NAME', 'Blood Bank System');

// ===================== SMS CONFIGURATION =====================
// SMS Gateway Settings (Twilio example)
define('SMS_PROVIDER', 'twilio');              // Options: twilio, vonage, aws-sns
define('SMS_ACCOUNT_SID', 'your_account_sid');
define('SMS_AUTH_TOKEN', 'your_auth_token');
define('SMS_FROM_NUMBER', '+1234567890');

// ===================== API CONFIGURATION =====================
// Third-party API Keys
define('GOOGLE_MAPS_API_KEY', 'your_google_maps_api_key');
define('STRIPE_PUBLIC_KEY', 'pk_live_...');     // For payments
define('STRIPE_SECRET_KEY', 'sk_live_...');

// ===================== FILE UPLOAD SETTINGS =====================
define('MAX_UPLOAD_SIZE', 5242880);             // 5MB in bytes
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// ===================== LOGGING & DEBUGGING =====================
define('DEBUG_MODE', false);                    // Set to true for development only
define('LOG_LEVEL', 'info');                    // Options: debug, info, warning, error
define('LOG_FILE', __DIR__ . '/logs/app.log');
define('LOG_MAX_SIZE', 10485760);               // 10MB

// ===================== PERFORMANCE SETTINGS =====================
define('ENABLE_CACHING', true);
define('CACHE_DURATION', 3600);                 // 1 hour
define('CACHE_TYPE', 'file');                   // Options: file, redis, memcached

// ===================== TIMEZONE =====================
define('APP_TIMEZONE', 'UTC');
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// ===================== PAGINATION =====================
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGES_FOR_DROPDOWN', 10);

// ===================== BACKUP SETTINGS =====================
define('AUTO_BACKUP_ENABLED', false);
define('BACKUP_FREQUENCY', 'daily');             // Options: daily, weekly, monthly
define('BACKUP_DIR', __DIR__ . '/backups/');
define('BACKUP_RETENTION_DAYS', 30);

// ===================== BLOOD BANK SETTINGS =====================
// Blood Donation Settings
define('MIN_DONATION_INTERVAL_DAYS', 56);       // 8 weeks minimum
define('BLOOD_UNIT_SIZE_ML', 450);              // Standard donation
define('BLOOD_SHELF_LIFE_DAYS', 42);            // Typical for RBC

// Stock Alerts
define('LOW_STOCK_THRESHOLD', 5);               // Alert when units below this
define('CRITICAL_STOCK_THRESHOLD', 2);         // Critical alert threshold
define('OVERSTAKE_LIMIT', 100);                 // Maximum units per blood type

// ===================== DONOR REQUIREMENTS =====================
define('MIN_DONOR_AGE', 18);
define('MAX_DONOR_AGE', 65);
define('MIN_DONOR_WEIGHT_KG', 50);
define('MIN_HEMOGLOBIN_LEVEL', 12.5);           // g/dL

// ===================== NOTIFICATION SETTINGS =====================
define('NOTIFY_ON_low_STOCK', true);
define('NOTIFY_ON_DONATION', true);
define('NOTIFY_ON_REQUEST_APPROVAL', true);
define('NOTIFY_ON_RECIPIENT_DISCHARGE', true);

// ===================== FEATURE FLAGS =====================
// Use these to enable/disable features without code changes
define('FEATURE_MULTI_BRANCH', false);
define('FEATURE_INVENTORY_TRACKING', true);
define('FEATURE_DONOR_REGISTRATION_ONLINE', true);
define('FEATURE_MOBILE_APP', false);
define('FEATURE_PAYMENT_PROCESSING', false);
define('FEATURE_BLOOD_TEST_INTEGRATION', false);

// ===================== EXTERNAL SERVICE INTEGRATIONS =====================
// Laboratory Management System
define('LAB_API_URL', 'https://lab-system.hospital.com/api/');
define('LAB_API_KEY', 'your_lab_api_key');

// Hospital Management System (HIS)
define('HIS_API_URL', 'https://his.hospital.com/api/');
define('HIS_API_KEY', 'your_his_api_key');

// ===================== GOOGLE ANALYTICS (optional) =====================
define('GOOGLE_ANALYTICS_ID', 'UA-XXXXXXXXX-X');

// ===================== ERROR HANDLING =====================
// Custom Error Pages
define('ERROR_404_PAGE', 'errors/404.php');
define('ERROR_500_PAGE', 'errors/500.php');
define('ERROR_MAINTENANCE_PAGE', 'errors/maintenance.php');

// ===================== ENVIRONMENT DETECTION =====================
define('ENVIRONMENT', 'development');           // Options: development, staging, production

if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

?>
