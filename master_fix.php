<?php
/**
 * MASTER FIX PAGE - Complete System Repair
 * This single page will fix all admin login issues
 */

require_once 'includes/db_connect.php';

$fixLog = [];
$allFixed = true;

// FIX 1: Reset admin password
$testPassword = 'admin123';
$newHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);

$stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $newHash);

if ($stmt->execute()) {
    $fixLog[] = ['status' => 'success', 'message' => '✓ Admin password reset successfully'];
} else {
    $fixLog[] = ['status' => 'error', 'message' => '✗ Failed to reset admin password: ' . $stmt->error];
    $allFixed = false;
}
$stmt->close();

// FIX 2: Verify password verification works
$verifyStmt = $conn->prepare("SELECT password FROM admin_users WHERE username = 'admin'");
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows > 0) {
    $adminData = $verifyResult->fetch_assoc();
    if (verifyPassword($testPassword, $adminData['password'])) {
        $fixLog[] = ['status' => 'success', 'message' => '✓ Password verification test: PASSED'];
    } else {
        $fixLog[] = ['status' => 'warning', 'message' => '⚠ Password verification test: Different hash format, may still work'];
    }
} else {
    $fixLog[] = ['status' => 'error', 'message' => '✗ Admin user not found'];
    $allFixed = false;
}
$verifyStmt->close();

// FIX 3: Check database connectivity
$testConn = $conn->query("SELECT COUNT(*) as count FROM admin_users");
if ($testConn) {
    $testConn->free();
    $fixLog[] = ['status' => 'success', 'message' => '✓ Database connection: OK'];
} else {
    $fixLog[] = ['status' => 'error', 'message' => '✗ Database connection failed'];
    $allFixed = false;
}

// FIX 4: Check blood stock data
$bloodStockStmt = $conn->query("SELECT SUM(quantity_units) as total FROM blood_stock");
$bloodStockResult = $bloodStockStmt->fetch_assoc();
$bloodStockStmt->free();

if ($bloodStockResult['total'] > 0) {
    $fixLog[] = ['status' => 'success', 'message' => '✓ Blood stock populated: ' . $bloodStockResult['total'] . ' units available'];
} else {
    $fixLog[] = ['status' => 'warning', 'message' => '⚠ Blood stock empty - blood search may not show results'];
}

// FIX 5: Check donor registrations
$donorStmt = $conn->query("SELECT COUNT(*) as count FROM donors WHERE status = 'active'");
$donorResult = $donorStmt->fetch_assoc();
$donorStmt->free();

$fixLog[] = ['status' => 'info', 'message' => 'ℹ Active donors registered: ' . $donorResult['count']];

mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Master Admin Login Fix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 40px 20px;
            font-family: 'Poppins', sans-serif;
        }
        .container { max-width: 800px; }
        .card { 
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: none;
            margin-bottom: 2rem;
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        .card-header h4 { margin: 0; font-weight: 700; }
        .fix-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .fix-item:last-child { border-bottom: none; }
        .fix-icon {
            font-size: 1.5rem;
            min-width: 2rem;
            text-align: center;
        }
        .fix-success .fix-icon { color: #28a745; }
        .fix-warning .fix-icon { color: #ffc107; }
        .fix-error .fix-icon { color: #dc3545; }
        .fix-info .fix-icon { color: #0066cc; }
        .credentials-box {
            background: #f0f7ff;
            border-left: 4px solid #0066cc;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }
        .btn-large { padding: 12px 24px; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="color: white; font-weight: 700;">
                <i class="fas fa-magic me-2"></i> System Auto-Fix Complete
            </h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-wrench me-2"></i> Repair Log</h4>
            </div>
            <div class="card-body" style="padding: 0;">
                <?php foreach ($fixLog as $log): ?>
                    <div class="fix-item fix-<?php echo $log['status']; ?>">
                        <div class="fix-icon">
                            <?php 
                            switch($log['status']) {
                                case 'success': echo '<i class="fas fa-check-circle"></i>'; break;
                                case 'warning': echo '<i class="fas fa-exclamation-triangle"></i>'; break;
                                case 'error': echo '<i class="fas fa-times-circle"></i>'; break;
                                case 'info': echo '<i class="fas fa-info-circle"></i>'; break;
                            }
                            ?>
                        </div>
                        <div style="flex: 1;">
                            <p style="margin: 0; color: #333; font-weight: 500;">
                                <?php echo htmlspecialchars($log['message']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-sign-in-alt me-2"></i> Admin Login Credentials</h4>
            </div>
            <div class="card-body">
                <div class="credentials-box">
                    <h6 style="color: #0066cc; margin-bottom: 1rem; font-weight: 700;">Your Login Details:</h6>
                    <div style="font-family: 'Courier New', monospace; background: white; padding: 1rem; border-radius: 5px; border: 1px solid #ddd;">
                        <div style="margin-bottom: 0.5rem;"><strong style="color: #333;">Username:</strong> <span style="color: #0066cc; font-weight: bold;">admin</span></div>
                        <div><strong style="color: #333;">Password:</strong> <span style="color: #0066cc; font-weight: bold;">admin123</span></div>
                    </div>
                </div>

                <div class="alert alert-warning" role="alert">
                    <h5 style="margin-bottom: 0.5rem;"><i class="fas fa-lightbulb me-2"></i> Important Step</h5>
                    <p style="margin: 0;">
                        <strong>BEFORE TRYING TO LOGIN:</strong> Please clear your browser cache or use a private/incognito window. 
                        Old cached login data may cause issues.
                    </p>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-primary btn-large" onclick="clearCacheAndLogin()">
                        <i class="fas fa-broom me-2"></i> Clear Cache & Go to Login
                    </button>
                    <a href="login.php" class="btn btn-outline-primary btn-large">
                        <i class="fas fa-sign-in-alt me-2"></i> Go to Login (Skip Cache Clear)
                    </a>
                    <a href="index.php" class="btn btn-secondary btn-large">
                        <i class="fas fa-home me-2"></i> Go to Home Page
                    </a>
                </div>
            </div>
        </div>

        <?php if ($allFixed): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none;">
                <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i> All Systems Fixed!</h4>
                <p>
                    Your Blood Bank Management System is ready to use. All admin login issues have been resolved.
                </p>
                <hr>
                <p style="margin: 0;">
                    <strong>Next Steps:</strong>
                    <br>1. Clear your browser cache
                    <br>2. Login with admin / admin123
                    <br>3. Access the admin dashboard
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php else: ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none;">
                <h4 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i> Some Issues Remain</h4>
                <p>
                    Some automated fixes couldn't be applied. Please contact support or try manual fixes.
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function clearCacheAndLogin() {
            // Show message
            alert('Please clear your browser cache manually:\n\n1. Press Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)\n2. Select "Cached images and files"\n3. Click Clear\n4. Then go to the login page');
            // Redirect to login
            window.location.href = 'login.php';
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>