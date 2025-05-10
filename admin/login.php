<?php
/**
 * Admin Login Page
 *
 * This file handles admin authentication.
 */

// Include language handler
require_once '../includes/language.php';

// Include database configuration
require_once '../config/database.php';

// Initialize variables
$email = '';
$error = '';
$success = '';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate form data
    if (empty($email) || empty($password)) {
        $error = __('required');
    } else {
        // Get database connection
        $conn = get_db_connection();

        // Check if users table exists
        $table_check = $conn->query("SHOW TABLES LIKE 'users'");
        if ($table_check->num_rows == 0) {
            $error = "Database tables not found. Please run setup_database.php first.";
        } else {
            // Prepare SQL statement
            $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ? AND role = 'admin'");

            // Check if prepare was successful
            if ($stmt === false) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);

                // Execute statement
                $stmt->execute();

                // Get result
                $result = $stmt->get_result();

                // Check if admin exists
                if ($result->num_rows > 0) {
                    $admin = $result->fetch_assoc();

                    // Verify password
                    if (password_verify($password, $admin['password'])) {
                        // Set session variables
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_email'] = $admin['email'];

                        // Set remember cookie if requested
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            $expires = time() + (86400 * 30); // 30 days

                            // Store token in database
                            $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                            $stmt->bind_param("si", $token, $admin['id']);
                            $stmt->execute();

                            // Set cookie
                            setcookie('admin_remember', $token, $expires, '/');
                        }

                        // Redirect to dashboard
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $error = __('login_failed');
                    }
                } else {
                    $error = __('login_failed');
                }

                // Close statement
                $stmt->close();
            }
        }

        // Close connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>" dir="<?php echo $language_direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_login') . ' - ' . __('app_name'); ?></title>

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
</head>
<body class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark-mode' : ''; ?>">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><?php echo __('admin_login'); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?php echo __('email'); ?></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label"><?php echo __('password'); ?></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember"><?php echo __('remember_me'); ?></label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i><?php echo __('login'); ?>
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="change_password.php" class="text-decoration-none">
                                    <i class="bi bi-key me-1"></i>Forgot or change password?
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="../index.php" class="text-decoration-none">
                            <i class="bi bi-house me-1"></i><?php echo __('home'); ?>
                        </a>
                    </div>
                </div>
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
