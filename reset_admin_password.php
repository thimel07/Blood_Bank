<?php
/**
 * Reset Admin Password - Direct Fix
 */

require_once 'includes/db_connect.php';

// Test password
$testPassword = 'admin123';

// Generate correct bcrypt hash
$correctHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);

// Update admin password
$stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $correctHash);

$updateSuccess = false;
if ($stmt->execute()) {
    $updateSuccess = true;
}
$stmt->close();

// Verify the update
$stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Test verification
$verificationWorks = verifyPassword($testPassword, $admin['password']);

$message = '';

if ($updateSuccess) {
    $message .= "<div class='alert alert-success'>";
    $message .= "<i class='fas fa-check-circle'></i> <strong>✓ Password Reset Successful!</strong><br>";
    $message .= "Admin password has been updated in the database.<br>";
    $message .= "<strong>Credentials:</strong><br>";
    $message .= "Username: <code>admin</code><br>";
    $message .= "Password: <code>admin123</code><br>";
    if ($verificationWorks) {
        $message .= "<br><span class='badge bg-success'>✓ Verification Test: PASSED</span>";
    }
    $message .= "</div>";
} else {
    $message .= "<div class='alert alert-danger'>";
    $message .= "<i class='fas fa-times-circle'></i> Update failed";
    $message .= "</div>";
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 600px; }
        .card { border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-large { padding: 12px 24px; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header p-4">
                <h4 style="margin: 0;"><i class="fas fa-key"></i> Admin Password Reset</h4>
            </div>
            <div class="card-body p-4">
                <?php echo $message; ?>

                <hr style="margin: 2rem 0;">

                <h5 style="margin-bottom: 1rem; color: #333;"><i class="fas fa-arrow-right"></i> What's Next?</h5>

                <div style="background: #e3f2fd; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                    <ol style="margin: 0; color: #1565c0;">
                        <li style="margin-bottom: 0.5rem;">Close this tab</li>
                        <li style="margin-bottom: 0.5rem;"><strong>Clear your browser cache</strong> (Ctrl+Shift+Delete)</li>
                        <li style="margin-bottom: 0;"><a href="login.php" style="color: #1565c0; font-weight: bold; text-decoration: underline;">Go to Login Page</a></li>
                    </ol>
                </div>

                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Go to Admin Login
                    </a>
                    <a href="index.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-home me-2"></i> Go to Home Page
                    </a>
                </div>

                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; border-radius: 8px; margin-top: 2rem;">
                    <p style="margin: 0; color: #856404; font-size: 0.9rem;">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Important:</strong> Clear your browser cache or use an incognito window to ensure you're using the new password.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>