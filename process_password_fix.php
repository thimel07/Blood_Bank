<?php
/**
 * Update Admin Password
 */

require_once 'includes/db_connect.php';

$correctHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);

// Update all admin accounts
$query = "UPDATE admin_users SET password = ? WHERE username IN ('admin', 'staff1', 'staff2')";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $correctHash);

if ($stmt->execute()) {
    $affected = $stmt->affected_rows;
    $_SESSION['message'] = "✓ Successfully updated " . $affected . " admin accounts!";
} else {
    $_SESSION['error'] = "Error: " . $stmt->error;
}

$stmt->close();
mysqli_close($conn);

// Redirect back
header('Location: test_admin_login.php');
exit;
?>