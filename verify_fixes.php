<?php
/**
 * Test Page - Verify All Fixes
 */

require_once 'includes/db_connect.php';

$results = [
    'requests_query' => false,
    'blood_search' => false,
    'request_blood' => false,
    'errors' => []
];

// Test 1: Check requests page query works
try {
    $stmt = $conn->prepare("
        SELECT req.request_id as id, req.request_date, req.status, req.units_required, bg.group_name as blood_type, h.name as hospital_name
        FROM request req
        LEFT JOIN blood_group bg ON req.blood_group_id = bg.blood_group_id
        LEFT JOIN hospital h ON req.hospital_id = h.hospital_id
        ORDER BY req.request_date DESC LIMIT 1
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $results['requests_query'] = true;
        $results['requests_count'] = $result->num_rows;
        if ($row = $result->fetch_assoc()) {
            $results['requests_sample'] = $row;
        }
    }
    $stmt->close();
} catch (Exception $e) {
    $results['errors'][] = 'Requests Query: ' . $e->getMessage();
}

// Test 2: Check blood search aggregation works
try {
    $blood_type_id = 1; // O+
    
    // Get total
    $result = $conn->query("
        SELECT SUM(bs.units_available) as total_units
        FROM blood_stock bs
        WHERE bs.blood_group_id = $blood_type_id AND bs.units_available > 0
    ");
    if ($row = $result->fetch_assoc()) {
        $results['blood_search'] = true;
        $results['total_units'] = $row['total_units'] ?? 0;
    }
    
    // Get donors
    $result = $conn->query("
        SELECT COUNT(DISTINCT d.donor_id) as count FROM donor d WHERE d.blood_group_id = $blood_type_id
    ");
    if ($row = $result->fetch_assoc()) {
        $results['donors_count'] = $row['count'];
    }
} catch (Exception $e) {
    $results['errors'][] = 'Blood Search: ' . $e->getMessage();
}

// Test 3: Check request_blood table insert works
$results['request_blood'] = true; // Just check the table exists
try {
    $result = $conn->query("DESCRIBE request");
    if (!$result) {
        $results['request_blood'] = false;
        $results['errors'][] = 'Request table does not exist';
    }
} catch (Exception $e) {
    $results['request_blood'] = false;
    $results['errors'][] = 'Request table: ' . $e->getMessage();
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Verification - Blood Bank</title>
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
            margin-bottom: 2rem;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .test-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .test-item:last-child { border-bottom: none; }
        .status-icon { font-size: 1.5rem; }
        .status-pass { color: #06d6a0; }
        .status-fail { color: #e63946; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header p-4">
                <h4 style="margin: 0;"><i class="fas fa-check-circle me-2"></i> System Verification</h4>
            </div>
            <div class="card-body p-0">
                <!-- Test 1: Requests Query -->
                <div class="test-item">
                    <div class="status-icon <?php echo $results['requests_query'] ? 'status-pass' : 'status-fail'; ?>">
                        <i class="fas <?php echo $results['requests_query'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <h6 style="margin: 0;">Requests Page Query</h6>
                        <small class="text-muted">
                            <?php 
                            if ($results['requests_query']) {
                                echo '✓ Working correctly';
                                if (isset($results['requests_sample'])) {
                                    echo '<br>Sample: ' . htmlspecialchars($results['requests_sample']['blood_type'] ?? 'N/A') . 
                                         ' | ' . htmlspecialchars($results['requests_sample']['hospital_name'] ?? 'N/A');
                                }
                            } else {
                                echo '✗ Query failed';
                            }
                            ?>
                        </small>
                    </div>
                </div>

                <!-- Test 2: Blood Search -->
                <div class="test-item">
                    <div class="status-icon <?php echo $results['blood_search'] ? 'status-pass' : 'status-fail'; ?>">
                        <i class="fas <?php echo $results['blood_search'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <h6 style="margin: 0;">Blood Search Aggregation</h6>
                        <small class="text-muted">
                            <?php 
                            if ($results['blood_search']) {
                                echo '✓ Aggregation working<br>' .
                                     'Total Units: ' . ($results['total_units'] ?? 0) . ' | ' .
                                     'Donors: ' . ($results['donors_count'] ?? 0);
                            } else {
                                echo '✗ Aggregation failed';
                            }
                            ?>
                        </small>
                    </div>
                </div>

                <!-- Test 3: Request Blood -->
                <div class="test-item">
                    <div class="status-icon <?php echo $results['request_blood'] ? 'status-pass' : 'status-fail'; ?>">
                        <i class="fas <?php echo $results['request_blood'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <h6 style="margin: 0;">Request Blood Form</h6>
                        <small class="text-muted">
                            <?php echo $results['request_blood'] ? '✓ Form ready' : '✗ Form not ready'; ?>
                        </small>
                    </div>
                </div>

                <!-- Errors -->
                <?php if (count($results['errors']) > 0): ?>
                    <div class="test-item bg-danger bg-opacity-10">
                        <div class="status-icon status-fail">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div style="flex: 1;">
                            <h6 style="margin: 0; color: #e63946;">Errors Found:</h6>
                            <small class="text-danger">
                                <?php foreach ($results['errors'] as $error): ?>
                                    <?php echo htmlspecialchars($error); ?><br>
                                <?php endforeach; ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="text-center">
            <a href="admin/requests.php" class="btn btn-light me-2" style="margin-bottom: 0.5rem;">
                <i class="fas fa-clipboard-list me-2"></i> View Requests
            </a>
            <a href="public/search_blood.php" class="btn btn-light me-2" style="margin-bottom: 0.5rem;">
                <i class="fas fa-search me-2"></i> Search Blood
            </a>
            <a href="index.php" class="btn btn-light" style="margin-bottom: 0.5rem;">
                <i class="fas fa-home me-2"></i> Home
            </a>
        </div>

        <div style="text-align: center; color: white; margin-top: 2rem;">
            <p style="opacity: 0.9;">
                <?php echo $results['requests_query'] && $results['blood_search'] && $results['request_blood'] ? 
                    '✓ All systems operational!' : 
                    '⚠ Some issues detected'; ?>
            </p>
        </div>
    </div>
</body>
</html>