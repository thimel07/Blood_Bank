<?php
/**
 * Quick Fix: Update Admin Password Hash
 * This script fixes the admin password hash in the already-imported database
 */

require_once 'includes/db_connect.php';

$fixed = false;
$message = '';

// Check if database exists
$databases = mysqli_query($conn, "SHOW DATABASES LIKE 'blood_bank%'");
if (mysqli_num_rows($databases) === 0) {
    $message = '<div class="alert alert-danger"><strong>Error:</strong> Database not found. Please run setup.php first to import the database.</div>';
} else {
    // Update the password hash for all admin users
    $correctHash = '$2y$10$r9h/cIPz0gi.URNNX3kh2OPST9/PgBkqquzi.Ee3KVd4oVygzANJG';
    
    $query = "UPDATE admin_users SET password = ? WHERE username IN ('admin', 'staff1', 'staff2')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $correctHash);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $fixed = true;
        $message = '<div class="alert alert-success"><strong>✓ Success!</strong> Updated ' . $affected . ' admin accounts with correct password hash.<br><br>You can now login with:<br><strong>Username: admin<br>Password: admin123</strong></div>';
    } else {
        $message = '<div class="alert alert-danger"><strong>Error:</strong> ' . $stmt->error . '</div>';
    }
    
    $stmt->close();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Admin Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 600px; }
        .card { border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 style="margin: 0;"><i class="fas fa-key"></i> Admin Password Fix</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                
                <?php if ($fixed): ?>
                    <hr>
                    <p style="color: #666;">The admin password hashes have been corrected in your database.</p>
                    <a href="login.php" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt"></i> Go to Login Page
                    </a>
                <?php endif; ?>
                
                <hr style="margin-top: 30px;">
                <h5>Login Credentials:</h5>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><code>admin</code></td>
                    </tr>
                    <tr>
                        <td><strong>Password:</strong></td>
                        <td><code>admin123</code></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <strong>Note:</strong> If you see this message multiple times, try the setup process again.
                </div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>