<?php
/**
 * Homepage
 *
 * This is the main landing page for the Citizen Complaints System.
 */

// Check if database and tables exist
function check_database_exists() {
    // Database credentials
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'complaints_db';

    // Try to connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass);

    // Check connection
    if ($conn->connect_error) {
        return false;
    }

    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
    if ($result->num_rows == 0) {
        $conn->close();
        return false;
    }

    // Select the database
    $conn->select_db($db_name);

    // Check if tables exist
    $tables = ['users', 'complaints', 'responses'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            $conn->close();
            return false;
        }
    }

    // Check if admin user exists
    $result = $conn->query("SELECT id FROM users WHERE role = 'admin'");
    $admin_exists = ($result->num_rows > 0);

    $conn->close();

    return [
        'database_setup' => true,
        'admin_exists' => $admin_exists
    ];
}

// Check database status
$db_status = check_database_exists();

// If database or tables don't exist, redirect to setup
if ($db_status === false) {
    // Redirect to setup_database.php
    header('Location: setup_database.php');
    exit;
}

// If database exists but admin doesn't, create admin user
if ($db_status !== false && !$db_status['admin_exists']) {
    include 'add_admin.php';
}

// Include language handler
require_once 'includes/language.php';

// Setup notification
$setup_notification = '';
if (isset($_GET['setup']) && $_GET['setup'] == 'complete') {
    $setup_notification = 'Database setup completed successfully!';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>" dir="<?php echo $language_direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('app_name'); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">

    <?php if ($language_direction === 'rtl'): ?>
        <!-- Bootstrap RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
</head>
<body class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark-mode' : ''; ?>">

    <!-- Navigation Bar -->
    <?php include 'includes/nav.php'; ?>

    <?php if (!empty($setup_notification) || ($db_status !== false && !$db_status['admin_exists'])): ?>
    <div class="container mt-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?php if (!empty($setup_notification)): ?>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-success fs-1 me-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">Setup Completed Successfully!</h4>
                        <p class="mb-0"><?php echo $setup_notification; ?></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if ($db_status !== false && !$db_status['admin_exists']): ?>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-person-check-fill text-primary fs-1 me-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">Admin Account Created</h4>
                        <p class="mb-0">Default admin credentials: <strong>Email:</strong> admin@admin.com, <strong>Password:</strong> admin</p>
                        <div class="mt-2">
                            <a href="admin/login.php" class="btn btn-sm btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login to Admin Panel
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero text-center">
        <div class="container">
            <h1><?php echo __('welcome_message'); ?></h1>
            <p class="lead"><?php echo __('welcome_subtitle'); ?></p>
            <a href="complaint.php" class="btn btn-primary btn-lg"><i class="bi bi-arrow-right-circle-fill me-2"></i><?php echo __('get_started'); ?></a>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo __('how_it_works'); ?></h2>

            <div class="row">
                <!-- Step 1 -->
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="step-icon">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <h5 class="card-title">
                                <span class="step-number">1</span>
                                <?php echo __('step_1'); ?>
                            </h5>
                            <p class="card-text"><?php echo __('step_1_desc'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="step-icon">
                                <i class="bi bi-qr-code"></i>
                            </div>
                            <h5 class="card-title">
                                <span class="step-number">2</span>
                                <?php echo __('step_2'); ?>
                            </h5>
                            <p class="card-text"><?php echo __('step_2_desc'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="step-icon">
                                <i class="bi bi-search"></i>
                            </div>
                            <h5 class="card-title">
                                <span class="step-number">3</span>
                                <?php echo __('step_3'); ?>
                            </h5>
                            <p class="card-text"><?php echo __('step_3_desc'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="step-icon">
                                <i class="bi bi-bell"></i>
                            </div>
                            <h5 class="card-title">
                                <span class="step-number">4</span>
                                <?php echo __('step_4'); ?>
                            </h5>
                            <p class="card-text"><?php echo __('step_4_desc'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Complaint Types Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo __('complaint_type'); ?></h2>

            <div class="row">
                <!-- Roads -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="complaint-type-icon">
                                <i class="bi bi-signpost-split"></i>
                            </div>
                            <h5 class="card-title"><?php echo __('roads'); ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Lighting -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="complaint-type-icon">
                                <i class="bi bi-lightbulb"></i>
                            </div>
                            <h5 class="card-title"><?php echo __('lighting'); ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Parks -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="complaint-type-icon">
                                <i class="bi bi-tree"></i>
                            </div>
                            <h5 class="card-title"><?php echo __('parks'); ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Sports -->
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <div class="complaint-type-icon">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <h5 class="card-title"><?php echo __('sports'); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
            darkModeLabel.setAttribute('data-dark-text', '<?php echo __('dark_mode'); ?>');
            darkModeLabel.setAttribute('data-light-text', '<?php echo __('light_mode'); ?>');
        });
    </script>
    <script src="js/darkmode.js"></script>
</body>
</html>
