<?php
/**
 * Donor Registration Page
 */
require_once __DIR__ . '/../includes/db_connect.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $blood_type_id = intval($_POST['blood_type_id'] ?? 0);
        $date_of_birth = $_POST['date_of_birth'] ?? '';
        $city = sanitize($_POST['city'] ?? '');
        $gender = $_POST['gender'] ?? 'male';
        
        if (empty($full_name) || empty($phone) || $blood_type_id == 0 || empty($date_of_birth)) {
            $error = 'All fields are required!';
        } else {
            $stmt = $conn->prepare("
                INSERT INTO donors (full_name, phone, blood_type_id, date_of_birth, city, gender, status)
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->bind_param("ssisss", $full_name, $phone, $blood_type_id, $date_of_birth, $city, $gender);
            
            if ($stmt->execute()) {
                $message = 'Thank you! Your donor registration is complete. We will contact you soon.';
                $donor_id = $conn->insert_id;
            } else {
                $error = 'Error registering donor: ' . $stmt->error;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #e63946; }
        body { background: linear-gradient(135deg, var(--primary) 0%, #1d3557 100%); min-height: 100vh; padding: 2rem 0; }
        .container { max-width: 600px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        h2 { color: var(--primary); font-weight: 700; margin-bottom: 2rem; }
        .form-label { font-weight: 600; }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: #c1121f; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h2><i class="fas fa-user-plus me-2"></i>Register as Blood Donor</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="needs-validation">
            <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone (017XXXXXXXX) *</label>
                <input type="tel" name="phone" class="form-control" pattern="017[0-9]{8}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Date of Birth *</label>
                <input type="date" name="date_of_birth" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender *</label>
                <select name="gender" class="form-select" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Blood Type *</label>
                <select name="blood_type_id" class="form-select" required>
                    <option value="">Select Blood Type</option>
                    <?php
                    $result = $conn->query("SELECT id, blood_type FROM blood_types ORDER BY blood_type");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['blood_type']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Register as Donor</button>
        </form>
        
        <hr>
        <p class="text-center"><a href="/blood-bank/">Back to Home</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
