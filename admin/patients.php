<?php
/**
 * Patients Management
 */
require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Patients';

// Get all patients
try {
    $stmt = $conn->prepare("
        SELECT r.recipient_id as id, r.name, bg.group_name as blood_type
        FROM recipient r
        LEFT JOIN blood_group bg ON r.blood_group_id = bg.blood_group_id
        ORDER BY r.recipient_id DESC
    ");
    $stmt->execute();
    $patients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $patients = [];
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
        <li><a href="/blood-bank/admin/patients.php" class="sidebar-link active"><i class="fas fa-hospital-user"></i> <span>Patients</span></a></li>
        <li><a href="/blood-bank/admin/receipts.php" class="sidebar-link"><i class="fas fa-receipt"></i> <span>Receipts</span></a></li>
        <li class="mt-3 border-top pt-3"><a href="/blood-bank/admin/profile.php" class="sidebar-link"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
        <li><a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="admin-container">
    <div class="admin-top-navbar">
        <div><button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle><i class="fas fa-bars"></i></button><span class="ms-3 fw-bold">Patients</span></div>
        <div class="d-flex gap-2"><a href="/blood-bank/admin/patients.php?new" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Add Patient</a><button class="btn btn-sm btn-outline-secondary" data-dark-mode-toggle><i class="fas fa-moon"></i></button></div>
    </div>

    <main class="p-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li><li class="breadcrumb-item active">Patients</li></ol></nav>

        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Blood Type</th>
                            <th>Hospital</th>
                            <th>Status</th>
                            <th>Admitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['patient_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-danger"><?php echo $patient['blood_type'] ?? 'N/A'; ?></span></td>
                            <td><?php echo htmlspecialchars($patient['hospital_name'] ?? 'N/A'); ?></td>
                            <td><span class="badge <?php echo ($patient['status'] ?? 'inactive') === 'active' ? 'badge-active' : 'badge-inactive'; ?>"><?php echo ucfirst($patient['status'] ?? 'inactive'); ?></span></td>
                            <td><?php echo ($patient['admitted_date'] ?? null) ? date('M d, Y', strtotime($patient['admitted_date'])) : '-'; ?></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
