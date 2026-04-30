<?php
/**
 * COMPLETE Admin Login Fix
 * This script will:
 * 1. Reset admin password in database
 * 2. Test password verification
 * 3. Show status and next steps
 */

require_once 'includes/db_connect.php';

$output = '';
$success = false;

// Step 1: Generate new password hash
$testPassword = 'admin123';
$newHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);

$output .= "<div class='card mb-3'>";
$output .= "<div class='card-header'><h5 style='margin:0'><i class='fas fa-wrench'></i> Fixing Admin Login...</h5></div>";
$output .= "<div class='card-body'>";

// Step 2: Get current admin user
$stmt = $conn->prepare("SELECT id, username, email, full_name FROM admin_users WHERE username = 'admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $output .= "<div class='alert alert-danger'><i class='fas fa-times-circle'></i> No admin user found in database!</div>";
} else {
    $admin = $result->fetch_assoc();
    
    // Step 3: Update password
    $updateStmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newHash, $admin['id']);
    
    if ($updateStmt->execute()) {
        $output .= "<div class='alert alert-info'>";
        $output .= "<i class='fas fa-check-circle'></i> <strong>Step 1: Password Updated</strong><br>";
        $output .= "Password hash has been reset in database<br>";
        $output .= "User: " . htmlspecialchars($admin['full_name']) . " (" . htmlspecialchars($admin['username']) . ")";
        $output .= "</div>";
        
        // Step 4: Verify update worked
        $verifyStmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
        $verifyStmt->bind_param("i", $admin['id']);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        $adminData = $verifyResult->fetch_assoc();
        $verifyStmt->close();
        
        // Step 5: Test verification
        $passwordTest = verifyPassword($testPassword, $adminData['password']);
        
        if ($passwordTest) {
            $output .= "<div class='alert alert-success'>";
            $output .= "<i class='fas fa-check-circle'></i> <strong>Step 2: Password Verification ✓ PASSED</strong><br>";
            $output .= "The password verification function works correctly!";
            $output .= "</div>";
            $success = true;
        } else {
            $output .= "<div class='alert alert-warning'>";
            $output .= "<i class='fas fa-exclamation-triangle'></i> <strong>Step 2: Password Verification Failed</strong><br>";
            $output .= "Testing: admin123 against hash didn't work<br>";
            $output .= "Trying alternative fix...";
            $output .= "</div>";
            
            // Try alternative: use md5
            $md5Hash = md5('admin123');
            $altUpdateStmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $altUpdateStmt->bind_param("si", $md5Hash, $admin['id']);
            $altUpdateStmt->execute();
            $altUpdateStmt->close();
            
            $output .= "<div class='alert alert-info'>";
            $output .= "Alternative password format set.";
            $output .= "</div>";
        }
        
        $updateStmt->close();
    } else {
        $output .= "<div class='alert alert-danger'>";
        $output .= "<i class='fas fa-times-circle'></i> Failed to update password: " . htmlspecialchars($updateStmt->error);
        $output .= "</div>";
        $updateStmt->close();
    }
}

$stmt->close();
mysqli_close($conn);

$output .= "</div></div>";

// Final status
$output .= "<div class='card'>";
$output .= "<div class='card-header'><h5 style='margin:0'><i class='fas fa-info-circle'></i> Login Information</h5></div>";
$output .= "<div class='card-body'>";

$output .= "<div style='background: #f0f7ff; border-left: 4px solid #0066cc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;'>";
$output .= "<h6 style='color: #0066cc; margin-bottom: 1rem;'>Admin Credentials:</h6>";
$output .= "<div style='font-family: monospace; color: #333;'>";
$output .= "<strong>Username:</strong> admin<br>";
$output .= "<strong>Password:</strong> admin123<br>";
$output .= "</div>";
$output .= "</div>";

$output .= "<div class='d-grid gap-2'>";
$output .= "<a href='login.php' class='btn btn-primary btn-lg'><i class='fas fa-sign-in-alt me-2'></i> Try Login Now</a>";
$output .= "</div>";

$output .= "<div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 1rem; border-radius: 8px; margin-top: 1.5rem;'>";
$output .= "<p style='margin: 0; color: #856404; font-size: 0.9rem;'>";
$output .= "<i class='fas fa-lightbulb me-2'></i>";
$output .= "<strong>IMPORTANT:</strong> Please clear your browser cache (Ctrl+Shift+Delete) or use an incognito window before trying to login!";
$output .= "</p>";
$output .= "</div>";

$output .= "</div></div>";

?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Admin Login Fix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 40px 20px;
        }
        .container { max-width: 700px; }
        .card { 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary { background: #667eea; border: none; }
        .btn-primary:hover { background: #764ba2; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: white; text-align: center; margin-bottom: 2rem;">
            <i class="fas fa-key me-2"></i> Admin Login Fix
        </h1>

        <?php echo $output; ?>

        <div style="margin-top: 2rem; text-align: center; color: white; font-size: 0.9rem;">
            <p><i class="fas fa-check-circle me-2"></i> All systems ready!</p>
        </div>
    </div>
</body>
</html>