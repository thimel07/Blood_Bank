<?php
/**
 * Donors Management
 */

require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();

$user = getCurrentUser();
$pageTitle = 'Donors';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM donor WHERE donor_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        
        logAudit('Delete Donor', 'donor', $delete_id);
        
        header('Location: ?success=1');
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

// Get all donors
try {
    $stmt = $conn->prepare("
        SELECT 
            d.donor_id as id,
            d.name as full_name,
            bg.group_name as blood_type,
            d.phone,
            d.last_donation_date,
            COALESCE(COUNT(dn.donation_id), 0) as donation_count,
            'active' as status
        FROM donor d
        LEFT JOIN blood_group bg ON d.blood_group_id = bg.blood_group_id
        LEFT JOIN donation dn ON d.donor_id = dn.donor_id
        GROUP BY d.donor_id, d.name, bg.group_name, d.phone, d.last_donation_date
        ORDER BY d.donor_id DESC
    ");
    $stmt->execute();
    $donors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $donors = [];
}

?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="sidebar bg-dark text-white">
    <div class="sidebar-brand p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-droplet text-primary"></i> Blood Bank
        </h5>
    </div>
    <ul class="sidebar-menu list-unstyled my-2">
        <li><a href="/blood-bank/admin/dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="/blood-bank/admin/donors.php" class="sidebar-link active"><i class="fas fa-users"></i> <span>Donors</span></a></li>
        <li><a href="/blood-bank/admin/donations.php" class="sidebar-link"><i class="fas fa-tint"></i> <span>Donations</span></a></li>
        <li><a href="/blood-bank/admin/blood_stock.php" class="sidebar-link"><i class="fas fa-cube"></i> <span>Blood Stock</span></a></li>
        <li><a href="/blood-bank/admin/requests.php" class="sidebar-link"><i class="fas fa-clipboard-list"></i> <span>Requests</span></a></li>
        <li><a href="/blood-bank/admin/patients.php" class="sidebar-link"><i class="fas fa-hospital-user"></i> <span>Patients</span></a></li>
        <li><a href="/blood-bank/admin/receipts.php" class="sidebar-link"><i class="fas fa-receipt"></i> <span>Receipts</span></a></li>
        <li class="mt-3 border-top pt-3"><a href="/blood-bank/admin/profile.php" class="sidebar-link"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
        <li><a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="admin-container">
    <div class="admin-top-navbar">
        <div>
            <button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle><i class="fas fa-bars"></i></button>
            <span class="ms-3 fw-bold">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></span>
        </div>
        <div class="d-flex gap-2">
            <a href="/blood-bank/admin/donors.php?new" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Add Donor</a>
            <button class="btn btn-sm btn-outline-secondary" data-dark-mode-toggle><i class="fas fa-moon"></i></button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/blood-bank/admin/profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/blood-bank/admin/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <main class="p-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Donors</li>
            </ol>
        </nav>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Operation completed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Search & Filter</h5>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search donors by name or phone...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="bloodTypeFilter">
                            <option value="">All Blood Types</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="donorsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Blood Type</th>
                            <th>Donations</th>
                            <th>Last Donation</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donors as $donor): ?>
                            <tr class="donor-row" data-blood="<?php echo $donor['blood_type']; ?>">
                                <td><strong><?php echo htmlspecialchars($donor['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                <td><span class="badge bg-danger"><?php echo $donor['blood_type']; ?></span></td>
                                <td><?php echo $donor['donation_count']; ?></td>
                                <td><?php echo $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'Never'; ?></td>
                                <td>
                                    <span class="badge <?php echo $donor['status'] === 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo ucfirst($donor['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="viewDonor(<?php echo $donor['id']; ?>)">View</a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDonor(<?php echo $donor['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="delete_id" id="deleteId">
</form>

<script>
function searchDonors() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const bloodType = document.getElementById('bloodTypeFilter').value;
    const rows = document.querySelectorAll('.donor-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const blood = row.getAttribute('data-blood');
        const match = (text.includes(searchTerm) && (!bloodType || blood === bloodType));
        row.style.display = match ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('bloodTypeFilter').value = '';
    searchDonors();
}

function deleteDonor(id) {
    showConfirm('Are you sure you want to delete this donor?').then(result => {
        if (result.isConfirmed) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteForm').submit();
        }
    });
}

function viewDonor(id) {
    alert('Donor details for ID: ' + id);
}

document.getElementById('searchInput').addEventListener('keyup', searchDonors);
document.getElementById('bloodTypeFilter').addEventListener('change', searchDonors);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
