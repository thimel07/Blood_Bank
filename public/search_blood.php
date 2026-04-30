<?php
/**
 * Blood Search Page - Find Available Blood
 */
require_once __DIR__ . '/../includes/db_connect.php';

$blood_type_id = intval($_GET['blood_type_id'] ?? 0);
$blood_type_name = '';
$total_units = 0;
$stock_details = [];
$donor_details = [];

if ($blood_type_id > 0) {
    // Get blood type name
    $result = $conn->query("SELECT group_name FROM blood_group WHERE blood_group_id = $blood_type_id");
    if ($row = $result->fetch_assoc()) {
        $blood_type_name = $row['group_name'];
    }
    
    // Get TOTAL aggregated units
    $result = $conn->query("
        SELECT SUM(units_available) as total FROM blood_stock 
        WHERE blood_group_id = $blood_type_id AND units_available > 0
    ");
    $row = $result->fetch_assoc();
    $total_units = $row['total'] ?? 0;
    
    // Get individual stock details
    $result = $conn->query("
        SELECT 
            bs.stock_id,
            bs.units_available,
            bs.expiry_date,
            COALESCE(b.name, 'Main Hospital') as location
        FROM blood_stock bs
        LEFT JOIN branch b ON bs.branch_id = b.branch_id
        WHERE bs.blood_group_id = $blood_type_id AND bs.units_available > 0
        ORDER BY bs.expiry_date ASC
    ");
    while ($row = $result->fetch_assoc()) {
        $stock_details[] = $row;
    }
    
    // Get all donors for this blood type
    $result = $conn->query("
        SELECT DISTINCT
            d.donor_id,
            d.name,
            d.phone,
            d.last_donation_date,
            COUNT(dn.donation_id) as total_donations
        FROM donor d
        LEFT JOIN donation dn ON d.donor_id = dn.donor_id
        WHERE d.blood_group_id = $blood_type_id
        GROUP BY d.donor_id, d.name, d.phone, d.last_donation_date
        ORDER BY d.name
    ");
    while ($row = $result->fetch_assoc()) {
        $donor_details[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Search - Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e63946;
            --success: #06d6a0;
        }
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .search-container {
            background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .summary-card {
            border-left: 5px solid var(--primary);
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%);
        }
        .units-badge {
            font-size: 2.5rem;
            background: var(--success);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            display: inline-block;
            font-weight: bold;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary);
        }
        .stock-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid var(--success);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .donor-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #0d6efd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .stock-item:hover, .donor-item:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .expiry-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .expiry-fresh {
            background: #d4edda;
            color: #155724;
        }
        .expiry-warning {
            background: #fff3cd;
            color: #856404;
        }
        .expiry-danger {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Search Header -->
    <div class="search-container">
        <div class="container">
            <h2><i class="fas fa-search me-2"></i> Blood Search Results</h2>
            <p style="opacity: 0.9; margin-bottom: 1.5rem;">Complete information about available blood inventory</p>
        </div>
    </div>

    <!-- Results Section -->
    <div class="container my-5">
        <?php if ($blood_type_id > 0): ?>
            <?php if ($total_units > 0): ?>
                <!-- SUMMARY CARD -->
                <div class="card summary-card mb-5">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <!-- Left: Blood Type & Status -->
                            <div class="col-md-6">
                                <h3 style="margin: 0; color: var(--primary); font-weight: 700;">
                                    <i class="fas fa-tint me-2"></i> <?php echo htmlspecialchars($blood_type_name); ?> Blood Type
                                </h3>
                                <p class="text-muted mt-2 mb-0">
                                    <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-check-circle me-1"></i> In Stock
                                    </span>
                                </p>
                            </div>
                            
                            <!-- Right: Total Units -->
                            <div class="col-md-6 text-md-end" style="margin-top: 1.5rem; margin-md-top: 0;">
                                <div class="units-badge">
                                    <?php echo $total_units; ?> Units
                                </div>
                                <p class="text-muted mt-2 mb-0">Total Available</p>
                            </div>
                        </div>

                        <!-- Summary Info -->
                        <hr style="margin: 2rem 0;">
                        <div class="row" style="font-size: 0.95rem;">
                            <div class="col-md-4">
                                <p><strong><i class="fas fa-warehouse me-2" style="color: var(--primary);"></i>Storage Locations:</strong></p>
                                <p style="font-size: 1.5rem; color: var(--primary); font-weight: bold; margin-bottom: 0;">
                                    <?php echo count($stock_details); ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><i class="fas fa-user-circle me-2" style="color: #0d6efd;"></i>Active Donors:</strong></p>
                                <p style="font-size: 1.5rem; color: #0d6efd; font-weight: bold; margin-bottom: 0;">
                                    <?php echo count($donor_details); ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong><i class="fas fa-calendar me-2" style="color: #fd7e14;"></i>Status:</strong></p>
                                <p style="font-size: 1rem; margin-bottom: 0;">
                                    <?php 
                                    if ($total_units > 30) echo '<span class="badge bg-success">Excellent Stock</span>';
                                    elseif ($total_units > 10) echo '<span class="badge bg-info">Good Stock</span>';
                                    else echo '<span class="badge bg-warning">Low Stock</span>';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STOCK LOCATIONS SECTION -->
                <div class="mb-5">
                    <h3 class="section-title">
                        <i class="fas fa-warehouse me-2"></i> Storage Locations
                    </h3>
                    
                    <?php if (count($stock_details) > 0): ?>
                        <div class="row">
                            <?php foreach ($stock_details as $stock): ?>
                                <div class="col-md-6">
                                    <div class="stock-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 style="margin: 0; color: var(--primary); font-weight: 600;">
                                                    <i class="fas fa-location-dot me-2"></i> <?php echo htmlspecialchars($stock['location']); ?>
                                                </h5>
                                            </div>
                                            <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                                <?php echo $stock['units_available']; ?> Units
                                            </span>
                                        </div>
                                        
                                        <p class="text-muted mb-2">Expiry Date:</p>
                                        <?php 
                                        $expiry = strtotime($stock['expiry_date']);
                                        $days_left = floor(($expiry - time()) / (60 * 60 * 24));
                                        
                                        if ($days_left < 0) {
                                            $badge_class = 'expiry-danger';
                                            $label = 'EXPIRED';
                                        } elseif ($days_left < 7) {
                                            $badge_class = 'expiry-danger';
                                            $label = $days_left . ' days remaining';
                                        } elseif ($days_left < 14) {
                                            $badge_class = 'expiry-warning';
                                            $label = $days_left . ' days remaining';
                                        } else {
                                            $badge_class = 'expiry-fresh';
                                            $label = $days_left . ' days remaining';
                                        }
                                        ?>
                                        <span class="expiry-badge <?php echo $badge_class; ?>">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo date('M d, Y', $expiry); ?> (<?php echo $label; ?>)
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No stock details available.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- DONORS SECTION -->
                <div class="mb-5">
                    <h3 class="section-title">
                        <i class="fas fa-heart me-2"></i> Available Donors for <?php echo htmlspecialchars($blood_type_name); ?>
                    </h3>
                    
                    <?php if (count($donor_details) > 0): ?>
                        <div class="row">
                            <?php foreach ($donor_details as $donor): ?>
                                <div class="col-md-6">
                                    <div class="donor-item">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 style="margin: 0; color: #0d6efd; font-weight: 600;">
                                                    <i class="fas fa-user-circle me-2"></i> <?php echo htmlspecialchars($donor['name']); ?>
                                                </h5>
                                            </div>
                                            <span class="badge bg-info" style="font-size: 0.9rem; padding: 0.5rem 0.8rem;">
                                                <?php echo $donor['total_donations']; ?> Donations
                                            </span>
                                        </div>
                                        
                                        <p class="mb-2">
                                            <strong><i class="fas fa-phone me-2" style="color: #0d6efd;"></i> Phone:</strong><br>
                                            <a href="tel:<?php echo htmlspecialchars($donor['phone']); ?>" class="text-decoration-none" style="color: #0d6efd;">
                                                <?php echo htmlspecialchars($donor['phone']); ?>
                                            </a>
                                        </p>
                                        
                                        <p class="mb-0">
                                            <strong><i class="fas fa-calendar me-2" style="color: #0d6efd;"></i> Last Donation:</strong><br>
                                            <?php 
                                            if ($donor['last_donation_date']) {
                                                echo '<span style="color: #666;">' . date('M d, Y', strtotime($donor['last_donation_date'])) . '</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">Never Donated</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No donors registered for this blood type yet.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="mb-5 p-4 bg-white rounded-3 text-center" style="border: 2px solid var(--primary);">
                    <h5 class="mb-3">Need This Blood?</h5>
                    <a href="/blood-bank/public/request_blood.php?blood_type_id=<?php echo $blood_type_id; ?>" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-plus me-2"></i> Submit Request
                    </a>
                    <a href="/blood-bank/" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                </div>

            <?php else: ?>
                <!-- No Stock Available -->
                <div class="alert alert-warning alert-lg" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> No Stock Available</h4>
                    <p>Sorry, <strong><?php echo htmlspecialchars($blood_type_name); ?></strong> blood is not currently available.</p>
                    <hr>
                    <p class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> You can still submit a request, and we'll notify you when blood becomes available.
                    </p>
                    <div style="margin-top: 1rem;">
                        <a href="/blood-bank/public/request_blood.php?blood_type_id=<?php echo $blood_type_id; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Submit Request Anyway
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- No Search Yet -->
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading"><i class="fas fa-search me-2"></i> Start Searching</h4>
                <p class="mb-0">Go back to home page and select a blood type from the dropdown to search for available units.</p>
                <div style="margin-top: 1rem;">
                    <a href="/blood-bank/" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>