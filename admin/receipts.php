<?php
/**
 * Receipts Management
 */
require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Receipts';

// Get all receipts
try {
    $stmt = $conn->prepare("
        SELECT * FROM receipts
        ORDER BY receipt_date DESC
        LIMIT 100
    ");
    $stmt->execute();
    $receipts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $receipts = [];
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="sidebar bg-dark text-white">
    <div class="sidebar-brand p-3 border-bottom"><h5 class="mb-0"><i class="fas fa-droplet text-primary"></i> Blood Bank</h5></div>
    <ul class="sidebar-menu list-unstyled my-2">
        <li><a href="/blood-bank/admin/dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="/blood-bank/admin/donors.php" class="sidebar-link"><i class="fas fa-users"></i> <span>Donors</span></a></li>
        <li><a href="/blood-bank/admin/donations.php" class="sidebar-link"><i class="fas fa-tint"></i> <span>Donations</span></a></li>
        <li><a href="/blood-bank/admin/blood_stock.php" class="sidebar-link"><i class="fas fa-cube"></i> <span>Blood Stock</span></a></li>
        <li><a href="/blood-bank/admin/requests.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> <span>Requests</span></a></li>
        <li><a href="/blood-bank/admin/patients.php" class="sidebar-link"><i class="fas fa-hospital-user"></i> <span>Patients</span></a></li>
        <li><a href="/blood-bank/admin/receipts.php" class="sidebar-link active"><i class="fas fa-receipt"></i> <span>Receipts</span></a></li>
        <li class="mt-3 border-top pt-3"><a href="/blood-bank/admin/profile.php" class="sidebar-link"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
        <li><a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="admin-container">
    <div class="admin-top-navbar">
        <div><button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle><i class="fas fa-bars"></i></button><span class="ms-3 fw-bold">Receipts</span></div>
        <div class="d-flex gap-2"><button class="btn btn-sm btn-outline-secondary" onclick="exportTableToCSV('receiptsTable', 'receipts.csv')"><i class="fas fa-download me-1"></i> Export</button><button class="btn btn-sm btn-outline-secondary" data-dark-mode-toggle><i class="fas fa-moon"></i></button></div>
    </div>

    <main class="p-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li><li class="breadcrumb-item active">Receipts</li></ol></nav>

        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="receiptsTable">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Type</th>
                            <th>Blood Type</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($receipt['receipt_number']); ?></strong></td>
                            <td><span class="badge bg-secondary"><?php echo ucfirst($receipt['transaction_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($receipt['blood_type']); ?></td>
                            <td><?php echo $receipt['quantity_ml']; ?> ml</td>
                            <td><?php echo date('M d, Y', strtotime($receipt['receipt_date'])); ?></td>
                            <td><button class="btn btn-sm btn-outline-primary" onclick="printReceipt(<?php echo $receipt['id']; ?>)"><i class="fas fa-print me-1"></i> Print</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
function printReceipt(id) {
    window.print();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
