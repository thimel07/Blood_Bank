<?php
/**
 * Admin Login Diagnostic & Fix
 */

require_once 'includes/db_connect.php';

$result = '';
$password_correct = false;
$test_password = 'admin123';

// Get admin user
$stmt = $conn->prepare("SELECT id, username, password, status FROM admin_users WHERE username = 'admin' LIMIT 1");
$stmt->execute();
$queryResult = $stmt->get_result();

if ($queryResult->num_rows > 0) {
    $admin = $queryResult->fetch_assoc();
    $storedHash = $admin['password'];
    
    // Test password
    $password_correct = verifyPassword($test_password, $storedHash);
    
    // Get more info
    $result = "
        <div class='alert alert-info'>
            <strong>Admin User Found:</strong>
            <ul style='margin-top: 0.5rem;'>
                <li><strong>Username:</strong> {$admin['username']}</li>
                <li><strong>Status:</strong> {$admin['status']}</li>
                <li><strong>Password Hash:</strong> <code style='word-break: break-all;'>" . htmlspecialchars(substr($storedHash, 0, 50)) . "...</code></li>
            </ul>
        </div>
        ";
    
    if ($password_correct) {
        $result .= "<div class='alert alert-success'><i class='fas fa-check-circle'></i> <strong>✓ Password is CORRECT!</strong><br>You should be able to login with: <strong>admin / admin123</strong></div>";
    } else {
        $result .= "
            <div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i> <strong>✗ Password verification failed</strong><br>
                The stored hash doesn't match the password 'admin123'. Click the button below to fix it.
            </div>
            ";
    }
} else {
    $result = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> No admin user found in database</div>";
}

$stmt->close();

// Process password fix if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_password'])) {
    $newHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
    $updateStmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
    $updateStmt->bind_param("s", $newHash);
    
    if ($updateStmt->execute()) {
        $result = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> <strong>✓ Password Updated!</strong><br>Your admin password has been reset to: <strong>admin / admin123</strong><br><a href='login.php' class='alert-link'>Go to Login</a></div>" . $result;
        $password_correct = true;
    } else {
        $result = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> <strong>✗ Update Failed:</strong> " . htmlspecialchars($updateStmt->error) . "</div>" . $result;
    }
    
    $updateStmt->close();
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login Diagnostic</title>
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
                <h4 style="margin: 0;"><i class="fas fa-stethoscope"></i> Admin Login Diagnostic</h4>
            </div>
            <div class="card-body p-4">
                <?php echo $result; ?>

                <?php if (!$password_correct): ?>
                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                        <h5 style="color: #856404; margin-bottom: 1rem;"><i class="fas fa-tools"></i> Fix Admin Password</h5>
                        <p style="margin-bottom: 1rem; color: #856404;">Click the button below to set the admin password to: <strong>admin123</strong></p>
                        <form method="POST">
                            <button type="submit" name="fix_password" value="1" class="btn btn-warning w-100">
                                <i class="fas fa-refresh me-2"></i> Fix Admin Password
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Ready to Login</h5>
                        <p style="margin-bottom: 0.5rem;">Everything is configured correctly!</p>
                        <a href="login.php" class="btn btn-success mt-2">
                            <i class="fas fa-sign-in-alt me-2"></i> Go to Login
                        </a>
                    </div>
                <?php endif; ?>

                <hr style="margin: 2rem 0;">

                <h5 style="margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Login Credentials</h5>
                <div style="background: #f0f7ff; border-left: 4px solid #0066cc; padding: 1rem; border-radius: 8px;">
                    <div style="margin-bottom: 0.5rem;"><strong>Username:</strong> <code>admin</code></div>
                    <div><strong>Password:</strong> <code>admin123</code></div>
                </div>

                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #ddd;">
                    <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;"><i class="fas fa-lightbulb"></i> <strong>Tip:</strong> If login still doesn't work after fixing the password:</p>
                    <ol style="color: #666; font-size: 0.9rem;">
                        <li>Clear your browser cache (Ctrl+Shift+Delete)</li>
                        <li>Try in an incognito/private window</li>
                        <li>Make sure MySQL server is running</li>
                        <li>Verify the database was imported correctly</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>