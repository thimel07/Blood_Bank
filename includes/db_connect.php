<?php
/**
 * Database Connection File
 * Blood Bank Management System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Leave empty if no password
define('DB_NAME', 'blood_bank_bd');
define('DB_CHARSET', 'utf8mb4');

// Try to establish connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error);
    }
    
    // Set charset
    if (!$conn->set_charset(DB_CHARSET)) {
        throw new Exception("Error loading character set utf8: " . $conn->error);
    }
    
    // Set timezone
    $conn->query("SET time_zone = '+00:00'");
    
} catch (Exception $e) {
    die("
    <div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;'>
        <h2>Database Error</h2>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        <p>Please make sure:</p>
        <ul>
            <li>MySQL server is running</li>
            <li>Database 'blood_bank_system' exists</li>
            <li>Database credentials in db_connect.php are correct</li>
        </ul>
    </div>
    ");
}

/**
 * Prepared Statement Helper Function
 */
function getStatement($query) {
    global $conn;
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Query Error: " . $conn->error);
    }
    return $stmt;
}

/**
 * Security Functions
 */
function sanitize($input) {
    global $conn;
    return $conn->real_escape_string(trim($input));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

function verifyPassword($password, $hash) {
    // Handle case 1: Base64 encoded bcrypt hash
    $decodedHash = base64_decode($hash, true);
    if ($decodedHash !== false && (strpos($decodedHash, '$2y$') === 0 || strpos($decodedHash, '$2a$') === 0 || strpos($decodedHash, '$2b$') === 0)) {
        return password_verify($password, $decodedHash);
    }
    
    // Handle case 2: Direct bcrypt hash
    if (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0 || strpos($hash, '$2b$') === 0) {
        $result = password_verify($password, $hash);
        if ($result === true) {
            return true;
        }
        // If bcrypt fails, try other methods as fallback
    }
    
    // Handle case 3: MD5 hash (legacy)
    if (strlen($hash) === 32 && ctype_xdigit($hash)) {
        if (hash('md5', $password) === $hash) {
            return true;
        }
    }
    
    // Handle case 4: Plain text (extreme legacy - NOT RECOMMENDED)
    if ($password === $hash) {
        return true;
    }
    
    // Default: return false
    return false;
}

/**
 * Session Helper Functions
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /blood-bank/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function getCurrentUser() {
    startSecureSession();
    if (isLoggedIn()) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}

function logout() {
    startSecureSession();
    session_destroy();
    header('Location: /blood-bank/login.php');
    exit;
}

/**
 * API Response Helper
 */
function sendJSON($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * File Upload Helper
 */
function uploadFile($file, $uploadPath, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload failed");
    }
    
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        throw new Exception("File type not allowed");
    }
    
    if ($file['size'] > 5000000) { // 5MB limit
        throw new Exception("File size too large");
    }
    
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    $newFileName = uniqid() . '.' . $fileExt;
    $newFilePath = $uploadPath . '/' . $newFileName;
    
    if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
        throw new Exception("Failed to move uploaded file");
    }
    
    return $newFileName;
}

/**
 * Logging Function
 */
function logAudit($action, $entityType = null, $entityId = null, $oldValues = null, $newValues = null) {
    global $conn;
    if (!isLoggedIn()) return false;
    
    $userId = $_SESSION['user_id'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    // Use different insert statement based on whether old/new values are provided
    if ($oldValues !== null || $newValues !== null) {
        $oldJson = $oldValues ? json_encode($oldValues) : null;
        $newJson = $newValues ? json_encode($newValues) : null;
        
        $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issisis", $userId, $action, $entityType, $entityId, $oldJson, $newJson, $ipAddress);
    } else {
        // Simple insert without old_values and new_values columns
        $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $action, $entityType, $entityId, $ipAddress);
    }
    
    return $stmt->execute();
}

?>
