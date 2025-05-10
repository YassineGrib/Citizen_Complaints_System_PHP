<?php
/**
 * Admin Logout Script
 * 
 * This file handles admin logout.
 */

// Start session
session_start();

// Clear admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);

// Clear remember cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.php');
exit;
?>
