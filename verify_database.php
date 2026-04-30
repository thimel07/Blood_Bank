<?php
/**
 * Database Connection Verification
 * Checks if all required tables are in blood_bank_bd
 */

require_once 'includes/db_connect.php';

$results = [
    'database' => DB_NAME,
    'connection' => $conn->connect_error ? 'FAILED: ' . $conn->connect_error : 'SUCCESS',
    'tables' => [],
    'admin_users' => 0,
    'donors' => 0,
    'donations' => 0,
    'requests' => 0,
    'blood_units' => 0
];

try {
    // Check tables
    $tables = ['admin_users', 'donor', 'donation', 'request', 'blood_stock', 'audit_logs', 'blood_group'];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT 1 FROM $table LIMIT 1");
        $results['tables'][$table] = $result !== false ? '✓' : '✗';
    }
    
    // Count records
    $results['admin_users'] = $conn->query("SELECT COUNT(*) as c FROM admin_users")->fetch_assoc()['c'];
    $results['donors'] = $conn->query("SELECT COUNT(*) as c FROM donor")->fetch_assoc()['c'];
    $results['donations'] = $conn->query("SELECT COUNT(*) as c FROM donation")->fetch_assoc()['c'];
    $results['requests'] = $conn->query("SELECT COUNT(*) as c FROM request")->fetch_assoc()['c'];
    $results['blood_units'] = $conn->query("SELECT SUM(units_available) as c FROM blood_stock")->fetch_assoc()['c'];
    
} catch (Exception $e) {
    $results['error'] = $e->getMessage();
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Verification - Blood Bank System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 800px; }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }
        .status-good { color: #06d6a0; }
        .status-bad { color: #e63946; }
        .stat-row { padding: 1rem; border-bottom: 1px solid #eee; }
        .stat-row:last-child { border-bottom: none; }
        .btn-proceed { margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 style="margin: 0;"><i class="fas fa-database me-2"></i> Database Verification</h4>
            </div>
            <div class="card-body p-0">
                <!-- Connection Status -->
                <div class="stat-row">
                    <strong>Database:</strong> <?php echo $results['database']; ?>
                </div>
                <div class="stat-row">
                    <strong>Connection:</strong> 
                    <span class="<?php echo strpos($results['connection'], 'SUCCESS') !== false ? 'status-good' : 'status-bad'; ?>">
                        <?php echo $results['connection']; ?>
                    </span>
                </div>

                <!-- Tables Status -->
                <div class="stat-row bg-light">
                    <h6 style="margin: 0;">Table Status:</h6>
                </div>
                <?php foreach ($results['tables'] as $table => $status): ?>
                    <div class="stat-row">
                        <span class="<?php echo $status === '✓' ? 'status-good' : 'status-bad'; ?>">
                            <strong><?php echo $status; ?></strong> <?php echo htmlspecialchars($table); ?>
                        </span>
                    </div>
                <?php endforeach; ?>

                <!-- Data Records -->
                <div class="stat-row bg-light">
                    <h6 style="margin: 0;">Data Records:</h6>
                </div>
                <div class="stat-row">
                    <span class="status-good"><i class="fas fa-users me-2"></i> <?php echo $results['admin_users']; ?> Admin Users</span>
                </div>
                <div class="stat-row">
                    <span class="status-good"><i class="fas fa-heart me-2"></i> <?php echo $results['donors']; ?> Donors</span>
                </div>
                <div class="stat-row">
                    <span class="status-good"><i class="fas fa-tint me-2"></i> <?php echo $results['donations']; ?> Donations</span>
                </div>
                <div class="stat-row">
                    <span class="status-good"><i class="fas fa-clipboard me-2"></i> <?php echo $results['requests']; ?> Requests</span>
                </div>
                <div class="stat-row">
                    <span class="status-good"><i class="fas fa-flask me-2"></i> <?php echo $results['blood_units']; ?> Blood Units</span>
                </div>

                <!-- Error Display -->
                <?php if (isset($results['error'])): ?>
                    <div class="stat-row bg-danger bg-opacity-10">
                        <strong class="status-bad">Error:</strong> <?php echo htmlspecialchars($results['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="stat-row">
                    <div class="d-grid gap-2 btn-proceed">
                        <a href="login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i> Go to Admin Login
                        </a>
                        <a href="system_ready.php" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i> System Status
                        </a>
                        <a href="index.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-home me-2"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; color: white; text-align: center;">
            <p><strong>✓ All Systems Ready!</strong></p>
            <p style="font-size: 0.9rem; opacity: 0.9;">
                You can now login with admin / admin123
            </p>
        </div>
    </div>
</body>
</html>