<?php
/**
 * Blood Stock Management
 */
require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Blood Stock';

// Get all blood stock
try {
    $stmt = $conn->prepare("
        SELECT bs.stock_id as id, bg.group_name as blood_type, bs.units_available as quantity_units, bs.expiry_date
        FROM blood_stock bs
        JOIN blood_group bg ON bs.blood_group_id = bg.blood_group_id
        ORDER BY bg.group_name
    ");
    $stmt->execute();
    $stocks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $stocks = [];
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="sidebar bg-dark text-white">
    <div class="sidebar-brand p-3 border-bottom">
        <h5 class="mb-0"><i class="fas fa-droplet text-primary"></i> Blood Bank</h5>
    </div>
    <ul class="sidebar-menu list-unstyled my-2">
        <li><a href="/blood-bank/admin/dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="/blood-bank/admin/donors.php" class="sidebar-link"><i class="fas fa-users"></i> <span>Donors</span></a></li>
        <li><a href="/blood-bank/admin/donations.php" class="sidebar-link"><i class="fas fa-tint"></i> <span>Donations</span></a></li>
        <li><a href="/blood-bank/admin/blood_stock.php" class="sidebar-link active"><i class="fas fa-cube"></i> <span>Blood Stock</span></a></li>
        <li><a href="/blood-bank/admin/requests.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> <span>Requests</span></a></li>
        <li><a href="/blood-bank/admin/patients.php" class="sidebar-link"><i class="fas fa-hospital-user"></i> <span>Patients</span></a></li>
        <li><a href="/blood-bank/admin/receipts.php" class="sidebar-link"><i class="fas fa-receipt"></i> <span>Receipts</span></a></li>
        <li class="mt-3 border-top pt-3"><a href="/blood-bank/admin/profile.php" class="sidebar-link"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
        <li><a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="admin-container">
    <div class="admin-top-navbar">
        <div><button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle><i class="fas fa-bars"></i></button><span class="ms-3 fw-bold">Blood Stock</span></div>
        <div class="d-flex gap-2"><button class="btn btn-sm btn-outline-secondary" data-dark-mode-toggle><i class="fas fa-moon"></i></button></div>
    </div>

    <main class="p-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li><li class="breadcrumb-item active">Blood Stock</li></ol></nav>

        <div class="row mb-4">
            <?php foreach ($stocks as $stock): 
                $status = $stock['quantity_units'] > 20 ? 'success' : ($stock['quantity_units'] > 5 ? 'warning' : 'danger');
                $statusText = $stock['quantity_units'] > 20 ? 'In Stock' : ($stock['quantity_units'] > 5 ? 'Low' : 'Critical');
            ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="card border-left text-center">
                    <div class="card-body">
                        <h4 class="text-<?php echo $status; ?> mb-2"><?php echo $stock['blood_type']; ?></h4>
                        <h3 class="card-title"><?php echo $stock['quantity_units']; ?></h3>
                        <p class="text-muted">Units</p>
                        <span class="badge bg-<?php echo $status; ?>"><?php echo $statusText; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Blood Type</th>
                            <th>Units Available</th>
                            <th>Total ML</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $stock): ?>
                        <tr>
                            <td><strong><?php echo $stock['blood_type']; ?></strong></td>
                            <td><?php echo $stock['quantity_units'] ?? '0'; ?></td>
                            <td><?php echo ($stock['quantity_ml'] ?? '0'); ?> ml</td>
                            <td>
                                <?php 
                                $qty = $stock['quantity_units'] ?? 0;
                                $status = $qty > 20 ? 'success' : ($qty > 5 ? 'warning' : 'danger');
                                echo '<span class="badge bg-' . $status . '">' . ($qty > 20 ? 'In Stock' : ($qty > 5 ? 'Low' : 'Critical')) . '</span>';
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($stock['last_updated'] ?? date('Y-m-d'))); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
