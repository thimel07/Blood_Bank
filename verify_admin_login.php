<?php
/**
 * Verify Admin Password Fix
 */

require_once 'includes/db_connect.php';

$testPassword = 'admin123';
$result = '';

// Get current hash from database
$stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = 'admin' LIMIT 1");
$stmt->execute();
$queryResult = $stmt->get_result();

if ($queryResult->num_rows > 0) {
    $admin = $queryResult->fetch_assoc();
    $currentHash = $admin['password'];
    
    // Test with improved verifyPassword function
    $passwordWorks = verifyPassword($testPassword, $currentHash);
    
    $result .= "<div class='alert alert-info'>";
    $result .= "<h5>Current Hash Status:</h5>";
    $result .= "<ul style='margin-top: 0.5rem;'>";
    $result .= "<li><strong>Username:</strong> {$admin['username']}</li>";
    $result .= "<li><strong>Hash Format:</strong> " . (base64_decode($currentHash, true) !== false ? "Base64 Encoded" : "Direct") . "</li>";
    $result .= "<li><strong>Hash (first 60 chars):</strong> <code>" . htmlspecialchars(substr($currentHash, 0, 60)) . "...</code></li>";
    $result .= "</ul>";
    $result .= "</div>";
    
    if ($passwordWorks) {
        $result .= "<div class='alert alert-success'><i class='fas fa-check-circle'></i> <strong>✓ SUCCESS!</strong><br>Password verification is working correctly!<br>You can login with: <strong>admin / admin123</strong></div>";
    } else {
        $result .= "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> <strong>✗ Password verification failed</strong><br>We'll fix this by resetting the password hash...</div>";
        
        // If verification fails, reset with new hash
        $newHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);
        $updateStmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
        $updateStmt->bind_param("s", $newHash);
        
        if ($updateStmt->execute()) {
            $result .= "<div class='alert alert-success' style='margin-top: 1rem;'><i class='fas fa-check-circle'></i> <strong>✓ Password has been reset!</strong><br>New hash stored in database.<br>You can now login with: <strong>admin / admin123</strong></div>";
            $passwordWorks = true;
        } else {
            $result .= "<div class='alert alert-danger' style='margin-top: 1rem;'><i class='fas fa-times-circle'></i> <strong>✗ Failed to update password</strong></div>";
        }
        
        $updateStmt->close();
    }
} else {
    $result = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> No admin user found!</div>";
}

$stmt->close();
mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Fix - Password Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 700px; }
        .card { border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 style="margin: 0;"><i class="fas fa-key"></i> Admin Password Verification</h4>
            </div>
            <div class="card-body p-4">
                <?php echo $result; ?>

                <hr style="margin: 2rem 0;">

                <h5 style="margin-bottom: 1rem;"><i class="fas fa-sign-in-alt"></i> Login Credentials</h5>
                <div style="background: #f0f7ff; border-left: 4px solid #0066cc; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <div style="margin-bottom: 0.5rem;"><strong>Username:</strong> <code>admin</code></div>
                    <div style="margin-bottom: 0.5rem;"><strong>Password:</strong> <code>admin123</code></div>
                </div>

                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Go to Login Page
                    </a>
                    <a href="fix_all_issues.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-tools me-2"></i> Run Full System Fix
                    </a>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #ddd;">
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><i class="fas fa-info-circle me-2"></i> <strong>If login still fails:</strong></p>
                    <ol style="color: #666; font-size: 0.9rem;">
                        <li>Clear browser cache (Ctrl+Shift+Delete)</li>
                        <li>Try in incognito/private window</li>
                        <li>Check MySQL server is running</li>
                        <li>Verify database was imported</li>
                        <li>Refresh this page to auto-fix</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>