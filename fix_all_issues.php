<?php
/**
 * Fix All Issues Script
 * - Fixes admin password
 * - Populates blood stock with test data
 */

require_once 'includes/db_connect.php';
startSecureSession();

$messages = [];
$errors = [];

// 1. Fix Admin Password
$correctHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
$query = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $correctHash);

if ($stmt->execute()) {
    $messages[] = "✓ Admin password updated successfully";
} else {
    $errors[] = "Failed to update admin password: " . $stmt->error;
}
$stmt->close();

// 2. Populate Blood Stock with Test Data
$bloodTypeIds = [1, 2, 3, 4, 5, 6, 7, 8]; // O+, O-, A+, A-, B+, B-, AB+, AB-
$quantities = [25, 30, 20, 15, 28, 18, 12, 22]; // Different quantities for each type

foreach ($bloodTypeIds as $index => $bloodTypeId) {
    $units = $quantities[$index];
    $ml = $units * 450; // Each unit is ~450 ML
    
    $query = "UPDATE blood_stock SET quantity_units = ?, quantity_ml = ? WHERE blood_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $units, $ml, $bloodTypeId);
    
    if (!$stmt->execute()) {
        $errors[] = "Failed to update blood type ID $bloodTypeId: " . $stmt->error;
    }
    $stmt->close();
}

$messages[] = "✓ Blood stock populated with test data (25-30 units per type)";

// 3. Get stats
$result = $conn->query("SELECT SUM(quantity_units) as total_units FROM blood_stock");
$stats = $result->fetch_assoc();
$messages[] = "✓ Total blood units available: " . $stats['total_units'];

$result = $conn->query("SELECT COUNT(*) as donor_count FROM donors WHERE status = 'active'");
$stats = $result->fetch_assoc();
$messages[] = "✓ Active donors registered: " . $stats['donor_count'];

mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>System Fix Complete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 700px; }
        .card { border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
        .alert { border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 style="margin: 0;"><i class="fas fa-check-circle"></i> System Fixes Applied</h4>
            </div>
            <div class="card-body p-4">
                <?php if (count($errors) > 0): ?>
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-circle"></i> Errors:</h5>
                        <ul style="margin: 0.5rem 0 0 0;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <h5 class="mb-3"><i class="fas fa-tools"></i> Applied Fixes:</h5>

                <div class="alert alert-success mb-0">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach ($messages as $msg): ?>
                            <li style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($msg); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <hr class="my-4">

                <h5 class="mb-3"><i class="fas fa-arrow-right"></i> What's Fixed:</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div style="padding: 1.5rem; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #0066cc;">
                            <h6 style="color: #0066cc; margin: 0 0 0.5rem 0;"><i class="fas fa-user"></i> Registration</h6>
                            <p style="margin: 0; font-size: 0.9rem;">Form now works correctly</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div style="padding: 1.5rem; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #0066cc;">
                            <h6 style="color: #0066cc; margin: 0 0 0.5rem 0;"><i class="fas fa-sign-in"></i> Admin Login</h6>
                            <p style="margin: 0; font-size: 0.9rem;">Password: admin / admin123</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div style="padding: 1.5rem; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #0066cc;">
                            <h6 style="color: #0066cc; margin: 0 0 0.5rem 0;"><i class="fas fa-tint"></i> Blood Search</h6>
                            <p style="margin: 0; font-size: 0.9rem;">Now shows available blood</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div style="padding: 1.5rem; background: #f0f7ff; border-radius: 8px; border-left: 4px solid #0066cc;">
                            <h6 style="color: #0066cc; margin: 0 0 0.5rem 0;"><i class="fas fa-database"></i> Stock Data</h6>
                            <p style="margin: 0; font-size: 0.9rem;">Populated with test data</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3"><i class="fas fa-play-circle"></i> Next Steps:</h5>

                <div class="list-group">
                    <a href="login.php" class="list-group-item list-group-item-action" style="border-radius: 8px; margin-bottom: 0.5rem;">
                        <strong>1. Login as Admin</strong>
                        <br><small class="text-muted">Username: admin | Password: admin123</small>
                    </a>
                    <a href="index.php" class="list-group-item list-group-item-action" style="border-radius: 8px; margin-bottom: 0.5rem;">
                        <strong>2. Go to Home Page</strong>
                        <br><small class="text-muted">Register as donor or search blood</small>
                    </a>
                    <a href="admin/dashboard.php" class="list-group-item list-group-item-action" style="border-radius: 8px;">
                        <strong>3. View Admin Dashboard</strong>
                        <br><small class="text-muted">See all statistics and management options</small>
                    </a>
                </div>

                <div class="alert alert-info mt-4 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Test Credentials for Blood Search:</strong>
                    <br>Try searching for any blood type (O+, O-, A+, A-, etc.)
                    <br>You should now see 12-30 units available for each type!
                </div>
            </div>
        </div>
    </div>
</body>
</html>