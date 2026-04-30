<?php
/**
 * Test Admin Credentials
 * This script helps diagnose and fix admin login issues
 */

require_once 'includes/db_connect.php';
startSecureSession();

$testPassword = 'admin123';
$correctHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);

$showMessage = false;
$messageType = '';
$messageText = '';

if (isset($_SESSION['message'])) {
    $showMessage = true;
    $messageType = 'success';
    $messageText = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $showMessage = true;
    $messageType = 'danger';
    $messageText = $_SESSION['error'];
    unset($_SESSION['error']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Credentials Test</title>
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
                <h4 style="margin: 0;"><i class="fas fa-tools"></i> Admin Login Troubleshooting</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($showMessage): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($messageText); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <h5 class="mb-3">✅ Correct Password for Admin</h5>
                <div class="alert alert-success mb-4">
                    <strong>Username:</strong> <code>admin</code><br>
                    <strong>Password:</strong> <code>admin123</code>
                </div>

                <h5 class="mb-3">Current Hash in Database</h5>
                <div class="alert alert-info" style="word-break: break-all; font-family: monospace; font-size: 0.85rem;">
                    <?php
                        // Try to get current password hash
                        $stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = 'admin' LIMIT 1");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            echo "Current Hash: " . htmlspecialchars($user['password']);
                        } else {
                            echo "No admin user found in database";
                        }
                        $stmt->close();
                    ?>
                </div>

                <h5 class="mb-3">New Correct Hash</h5>
                <div class="alert alert-warning" style="word-break: break-all; font-family: monospace; font-size: 0.85rem;">
                    <?php echo htmlspecialchars($correctHash); ?>
                </div>

                <h5 class="mb-3">Fix Admin Password</h5>
                <form method="POST" action="process_password_fix.php">
                    <button type="submit" class="btn btn-success w-100 py-3">
                        <i class="fas fa-refresh me-2"></i> Update Admin Password in Database
                    </button>
                </form>

                <hr class="my-4">

                <h5 class="mb-3">Test Password Verification</h5>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_password'])) {
                        $testInput = $_POST['test_password'];
                        $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = 'admin' LIMIT 1");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            if (password_verify($testInput, $user['password'])) {
                                echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ✓ Password matches!</div>';
                            } else {
                                echo '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ✗ Password does not match</div>';
                            }
                        }
                        $stmt->close();
                    }
                ?>
                <form method="POST" class="mb-3">
                    <input type="password" name="test_password" class="form-control mb-2" placeholder="Enter password to test">
                    <button type="submit" class="btn btn-primary w-100">Test Password</button>
                </form>

                <div class="alert alert-info mt-4" style="font-size: 0.9rem;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Steps to fix:</strong>
                    <ol style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                        <li>Click "Update Admin Password" button above</li>
                        <li>Go to <a href="login.php" class="alert-link">login page</a></li>
                        <li>Enter: <code>admin</code> / <code>admin123</code></li>
                        <li>You should now login successfully</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>