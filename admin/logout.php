<?php
/**
 * Logout
 */

require_once __DIR__ . '/../includes/db_connect.php';

// Log the logout action
if (isLoggedIn()) {
    logAudit('User Logout', 'admin_user', $_SESSION['user_id']);
}

session_destroy();
header('Location: /blood-bank/login.php?message=Logged out successfully');
exit;
?>
