<?php
/**
 * Settings
 */
require_once __DIR__ . '/../includes/db_connect.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Settings';
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
        <li><a href="/blood-bank/admin/receipts.php" class="sidebar-link"><i class="fas fa-receipt"></i> <span>Receipts</span></a></li>
        <li class="mt-3 border-top pt-3"><a href="/blood-bank/admin/settings.php" class="sidebar-link active"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
        <li><a href="/blood-bank/admin/logout.php" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<div class="admin-container">
    <div class="admin-top-navbar">
        <div><button class="btn btn-sm btn-outline-secondary" data-sidebar-toggle><i class="fas fa-bars"></i></button><span class="ms-3 fw-bold">Settings</span></div>
        <div class="d-flex gap-2"><button class="btn btn-sm btn-outline-secondary" data-dark-mode-toggle><i class="fas fa-moon"></i></button></div>
    </div>

    <main class="p-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="/blood-bank/admin/dashboard.php">Home</a></li><li class="breadcrumb-item active">Settings</li></ol></nav>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">System Settings</h5>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Hospital Name</label>
                                <input type="text" class="form-control" value="City Blood Bank" placeholder="Enter hospital name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" value="info@bloodbank.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" value="+1-800-BLOOD-1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" rows="3">123 Medical Center, Healthcare City</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>

                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Notification Settings</h5>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                            <label class="form-check-label" for="emailNotif">Email Notifications</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="smsNotif" checked>
                            <label class="form-check-label" for="smsNotif">SMS Notifications</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pushNotif">
                            <label class="form-check-label" for="pushNotif">Push Notifications</label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3">Update Notifications</button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Data Management</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-2"></i> Backup Data
                            </button>
                            <button class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-upload me-2"></i> Restore Data
                            </button>
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-2"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
