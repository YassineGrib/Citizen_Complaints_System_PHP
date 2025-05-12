<?php
/**
 * Admin 404 Error Page
 * 
 * This file displays a custom 404 error page when a requested admin page is not found.
 */

// Include language handler
require_once '../includes/language.php';

// Check if admin is logged in
session_start();
$is_admin_logged_in = isset($_SESSION['admin_id']);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>" dir="<?php echo $language_direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('page_not_found') . ' - ' . __('admin_dashboard'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/styles.css">
    
    <?php if ($language_direction === 'rtl'): ?>
        <!-- Bootstrap RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>

    <style>
        .error-container {
            text-align: center;
            padding: 100px 0;
        }
        .error-code {
            font-size: 150px;
            font-weight: bold;
            color: #f8f9fa;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
            margin-bottom: 0;
            line-height: 1;
        }
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        .dark-mode .error-code {
            color: #343a40;
        }
        .error-actions {
            margin-top: 30px;
        }
    </style>
</head>
<body class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark-mode' : ''; ?>">
    
    <?php if ($is_admin_logged_in): ?>
        <!-- Admin Navigation Bar -->
        <?php include 'nav.php'; ?>
    <?php else: ?>
        <!-- Public Navigation Bar -->
        <?php include '../includes/nav.php'; ?>
    <?php endif; ?>
    
    <div class="container">
        <div class="error-container">
            <div class="error-code">404</div>
            <div class="error-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h2><?php echo __('page_not_found'); ?></h2>
            <p class="lead"><?php echo __('page_not_found_message'); ?></p>
            
            <div class="error-actions">
                <?php if ($is_admin_logged_in): ?>
                    <a href="dashboard.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-speedometer2 me-2"></i><?php echo __('admin_dashboard'); ?>
                    </a>
                <?php else: ?>
                    <a href="../index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-fill me-2"></i><?php echo __('home'); ?>
                    </a>
                    <a href="login.php" class="btn btn-outline-primary btn-lg ms-2">
                        <i class="bi bi-shield-lock-fill me-2"></i><?php echo __('admin_login'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="py-4 bg-dark text-white">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo __('app_name'); ?>. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dark Mode JS -->
    <script>
        // Set dark mode text for JS
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeLabel = document.querySelector('label[for="darkModeSwitch"]');
            if (darkModeLabel) {
                darkModeLabel.setAttribute('data-dark-text', '<?php echo __('dark_mode'); ?>');
                darkModeLabel.setAttribute('data-light-text', '<?php echo __('light_mode'); ?>');
            }
        });
    </script>
    <script src="../js/darkmode.js"></script>
</body>
</html>
