<?php
/**
 * Admin Dashboard
 * Blood Bank Management System
 */

require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();

$user = getCurrentUser();
$pageTitle = 'Dashboard';

// Get statistics
try {
    // Total Donors
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM donor");
    $stmt->execute();
    $totalDonors = $stmt->get_result()->fetch_assoc()['total'];

    // Total Blood Units
    $stmt = $conn->prepare("SELECT COALESCE(SUM(units_available), 0) as total FROM blood_stock");
    $stmt->execute();
    $totalUnits = $stmt->get_result()->fetch_assoc()['total'];

    // Total Requests
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM request WHERE status IN ('pending', 'approved')");
    $stmt->execute();
    $totalRequests = $stmt->get_result()->fetch_assoc()['total'];

    // Approved Requests
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM request WHERE status = 'approved'");
    $stmt->execute();
    $approvedRequests = $stmt->get_result()->fetch_assoc()['total'];

    // Recent donations (for chart)
    $stmt = $conn->prepare("
        SELECT DATE(donation_date) as date, COUNT(*) as count
        FROM donation
        WHERE donation_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(donation_date)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $donationChartData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Blood stock data (for chart)
    $stmt = $conn->prepare("
        SELECT bg.group_name as blood_type, SUM(bs.units_available) as quantity_units
        FROM blood_stock bs
        JOIN blood_group bg ON bs.blood_group_id = bg.blood_group_id
        GROUP BY bs.blood_group_id, bg.group_name
        ORDER BY bg.group_name
    ");
    $stmt->execute();
    $bloodStockData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    error_log($e->getMessage());
}

?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.8rem 1.5rem;
        margin: 0.5rem 1rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .sidebar-link:hover {
        background: rgba(230, 57, 70, 0.1);
        color: white;
    }

    .sidebar-link.active {
        background: rgba(230, 57, 70, 0.2);
        color: var(--primary-color);
    }

    .sidebar-link i {
        width: 24px;
        margin-right: 12px;
    }

    .admin-top-navbar {
        background: white;
        border-bottom: 1px solid #eee;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .breadcrumb-custom {
        margin-bottom: 2rem;
    }

    .stat-animation {
        transition: all 0.3s ease;
    }

    .stat-animation:hover {
        transform: translateY(-5px);
    }
</style>

<!-- Sidebar -->
<div class="sidebar bg-dark text-white">
    <div class="sidebar-brand p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-droplet text-primary"></i> Blood Bank
        </h5>
    </div>
    <ul class="sidebar-menu list-unstyled my-2">
        <li>
            <a href="/blood-bank/admin/dashboard.php" class="sidebar-link active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/donors.php" class="sidebar-link">
                <i class="fas fa-users"></i>
                <span>Donors</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/donations.php" class="sidebar-link">
                <i class="fas fa-tint"></i>
                <span>Donations</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/blood_stock.php" class="sidebar-link">
                <i class="fas fa-cube"></i>
                <span>Blood Stock</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/requests.php" class="sidebar-link">
                <i class="fas fa-clipboard-list"></i>
                <span>Requests</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/patients.php" class="sidebar-link">
                <i class="fas fa-hospital-user"></i>
                <span>Patients</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/receipts.php" class="sidebar-link">
                <i class="fas fa-receipt"></i>
                <span>Receipts</span>
            </a>
        </li>
        <li class="mt-3 border-top pt-3">
            <a href="/blood-bank/admin/profile.php" class="sidebar-link">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/settings.php" class="sidebar-link">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="admin-container">
    <!-- Top Navbar -->
    <div class="admin-top-navbar">
        <div>
            <button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle>
                <i class="fas fa-bars"></i>
            </button>
            <span class="ms-3 fw-bold">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-primary" data-dark-mode-toggle>
                <i class="fas fa-moon"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> 
                    <?php echo htmlspecialchars($user['username']); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/blood-bank/admin/profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/blood-bank/admin/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="p-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="breadcrumb-custom">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card stat-animation border-0 primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon text-primary me-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Active Donors</h6>
                                <h3 class="stat-value text-primary mb-0" data-target="<?php echo $totalDonors; ?>">
                                    <?php echo $totalDonors; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card stat-animation border-0 success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon text-success me-3">
                                <i class="fas fa-tint fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Blood Units</h6>
                                <h3 class="stat-value text-success mb-0" data-target="<?php echo $totalUnits; ?>">
                                    <?php echo $totalUnits; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card stat-animation border-0 warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon text-warning me-3">
                                <i class="fas fa-clipboard-list fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Pending Requests</h6>
                                <h3 class="stat-value text-warning mb-0" data-target="<?php echo $totalRequests; ?>">
                                    <?php echo $totalRequests; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card stat-animation border-0 info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon text-info me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Approved Requests</h6>
                                <h3 class="stat-value text-info mb-0" data-target="<?php echo $approvedRequests; ?>">
                                    <?php echo $approvedRequests; ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <h5 class="card-title mb-3">Blood Stock Distribution</h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="bloodStockChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <h5 class="card-title mb-3">Monthly Donations</h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="donationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Quick Actions</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="/blood-bank/admin/donors.php?new" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i> Add Donor
                            </a>
                            <a href="/blood-bank/admin/donations.php?new" class="btn btn-success btn-sm">
                                <i class="fas fa-tint me-1"></i> Record Donation
                            </a>
                            <a href="/blood-bank/admin/requests.php" class="btn btn-warning btn-sm">
                                <i class="fas fa-list me-1"></i> View Requests
                            </a>
                            <a href="/blood-bank/admin/patients.php?new" class="btn btn-info btn-sm">
                                <i class="fas fa-hospital-user me-1"></i> Add Patient
                            </a>
                            <button class="btn btn-secondary btn-sm" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> Print Report
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV('statsTable', 'dashboard-report.csv')">
                                <i class="fas fa-download me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i> Recent Donations
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Donor Name</th>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Donation Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td><span class="badge bg-danger">O+</span></td>
                                    <td>450 ml</td>
                                    <td><?php echo date('M d, Y'); ?></td>
                                    <td><span class="badge badge-approved">Approved</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td><span class="badge bg-success">A+</span></td>
                                    <td>450 ml</td>
                                    <td><?php echo date('M d, Y', strtotime('-1 day')); ?></td>
                                    <td><span class="badge badge-approved">Approved</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="/blood-bank/admin/donations.php" class="btn btn-sm btn-outline-primary">View All Donations</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Scripts for Charts -->
<script>
// Blood Stock Chart
const bloodStockLabels = <?php echo json_encode(array_column($bloodStockData, 'blood_type') ?? []); ?>;
const bloodStockValues = <?php echo json_encode(array_column($bloodStockData, 'quantity_units') ?? []); ?>;

createBloodStockChart('bloodStockChart', bloodStockLabels, bloodStockValues);

// Donation Chart
const donationLabels = <?php echo json_encode(array_column($donationChartData, 'date') ?? []); ?>;
const donationValues = <?php echo json_encode(array_column($donationChartData, 'count') ?? []); ?>;

createDonationChart('donationChart', donationLabels, donationValues);

// Custom stat colors
document.querySelectorAll('.card.primary').forEach(el => {
    el.style.borderLeft = '4px solid var(--primary-color)';
});
document.querySelectorAll('.card.success').forEach(el => {
    el.style.borderLeft = '4px solid var(--success-color)';
});
document.querySelectorAll('.card.warning').forEach(el => {
    el.style.borderLeft = '4px solid var(--warning-color)';
});
document.querySelectorAll('.card.info').forEach(el => {
    el.style.borderLeft = '4px solid var(--info-color)';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
