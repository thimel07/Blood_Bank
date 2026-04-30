<?php
/**
 * Blood Request Page
 */
require_once __DIR__ . '/../includes/db_connect.php';

$blood_type_id = intval($_GET['blood_type_id'] ?? 0);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $blood_group_id = intval($_POST['blood_group_id'] ?? 0);
        $units_required = intval($_POST['units_required'] ?? 1);
        
        if (empty($name) || empty($phone) || $blood_group_id == 0) {
            $error = 'All required fields must be filled!';
        } else {
            // Create blood request in the request table
            $stmt = $conn->prepare("
                INSERT INTO request (blood_group_id, units_required, status, request_date)
                VALUES (?, ?, 'pending', NOW())
            ");
            $stmt->bind_param("ii", $blood_group_id, $units_required);
            
            if ($stmt->execute()) {
                $request_id = $conn->insert_id;
                $message = 'Blood request submitted successfully! Request ID: ' . $request_id . '. The hospital staff will process your request within 24 hours.';
            } else {
                $error = 'Error creating request: ' . $stmt->error;
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
    <title>Request Blood - Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #e63946; }
        body { 
            background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 40px 20px;
        }
        .container { max-width: 600px; }
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            background: var(--primary);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        h2 { color: var(--primary); font-weight: 700; margin-bottom: 2rem; }
        .form-label { font-weight: 600; color: #333; }
        .required::after { content: " *"; color: var(--primary); }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: #c1121f; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header p-4">
            <h4 style="margin: 0;"><i class="fas fa-tint me-2"></i> Blood Request Form</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i> <strong>Success!</strong><br> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="text-center mt-3">
                    <a href="/blood-bank/public/search_blood.php" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i> Search More Blood
                    </a>
                    <a href="/blood-bank/" class="btn btn-secondary">
                        <i class="fas fa-home me-2"></i> Go Home
                    </a>
                </div>
            <?php else: ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label for="blood_group_id" class="form-label required">Blood Type</label>
                        <select name="blood_group_id" id="blood_group_id" class="form-select" required>
                            <option value="">Select Blood Type</option>
                            <?php
                            $result = $conn->query("SELECT blood_group_id, group_name FROM blood_group ORDER BY group_name");
                            while ($row = $result->fetch_assoc()) {
                                $selected = $row['blood_group_id'] == $blood_type_id ? 'selected' : '';
                                echo "<option value='{$row['blood_group_id']}' $selected>{$row['group_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="units_required" class="form-label required">Units Required</label>
                        <input type="number" name="units_required" id="units_required" class="form-control" min="1" max="50" value="1" required>
                        <small class="text-muted">1 unit = 450ml of blood</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="name" class="form-label required">Recipient Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="form-label required">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="01700000000" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i> Submit Blood Request
                        </button>
                        <a href="/blood-bank/public/search_blood.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Search
                        </a>
                    </div>
                </form>

                <div class="alert alert-info mt-4">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i> How It Works</h6>
                    <small>
                        <ol style="margin-bottom: 0; padding-left: 1.5rem;">
                            <li>Fill in your blood type and required units</li>
                            <li>Provide recipient information and contact details</li>
                            <li>Submit the form to create a request</li>
                            <li>Hospital staff will confirm your request within 24 hours</li>
                            <li>We'll contact you at the provided phone number</li>
                        </ol>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="margin-top: 2rem; text-align: center; color: white;">
        <p style="opacity: 0.9;"><i class="fas fa-heart me-2"></i> Thank you for trusting our blood bank</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
</body>
</html>
