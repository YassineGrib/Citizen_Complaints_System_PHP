<?php
/**
 * Database Setup Script
 *
 * This script creates the database and tables required for the Citizen Complaints System.
 */

// Start output buffering to suppress all messages
ob_start();

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'complaints_db';

// Create connection to MySQL server (without database)
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    ob_end_clean(); // Clear buffer before showing error
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) !== TRUE) {
    ob_end_clean(); // Clear buffer before showing error
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($db_name);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) !== TRUE) {
    ob_end_clean(); // Clear buffer before showing error
    die("Error creating users table: " . $conn->error);
}

// Create complaints table
$sql = "CREATE TABLE IF NOT EXISTS complaints (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tracking_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    complaint_type VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    attachment VARCHAR(255) DEFAULT NULL,
    status ENUM('new', 'in_progress', 'resolved', 'rejected') NOT NULL DEFAULT 'new',
    admin_response TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) !== TRUE) {
    ob_end_clean(); // Clear buffer before showing error
    die("Error creating complaints table: " . $conn->error);
}

// Create responses table for tracking all responses to a complaint
$sql = "CREATE TABLE IF NOT EXISTS responses (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT(11) UNSIGNED NOT NULL,
    response TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) !== TRUE) {
    ob_end_clean(); // Clear buffer before showing error
    die("Error creating responses table: " . $conn->error);
}

// Close connection
$conn->close();

// End output buffering and clear the buffer
ob_end_clean();

// Check database status after setup
function check_admin_exists() {
    // Database credentials
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'complaints_db';

    // Try to connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Check connection
    if ($conn->connect_error) {
        return false;
    }

    // Check if admin user exists
    $result = $conn->query("SELECT id FROM users WHERE role = 'admin'");
    $admin_exists = ($result->num_rows > 0);

    $conn->close();

    return $admin_exists;
}

// Always show the formatted page when this script is run directly
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Citizen Complaints System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .setup-icon {
            font-size: 5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-action {
            margin: 0 5px;
        }
        .setup-steps {
            counter-reset: step-counter;
            list-style-type: none;
            padding-left: 0;
        }
        .setup-step {
            position: relative;
            padding-left: 50px;
            margin-bottom: 20px;
            text-align: left;
        }
        .setup-step:before {
            content: counter(step-counter);
            counter-increment: step-counter;
            position: absolute;
            left: 0;
            top: 0;
            width: 36px;
            height: 36px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .setup-step.completed:before {
            content: "✓";
            background-color: #198754;
        }
        .setup-step.current:before {
            background-color: #fd7e14;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <div class="setup-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h2 class="mb-3">Database Setup Completed Successfully!</h2>
                        <p class="lead mb-4">The database and all required tables have been created.</p>

                        <div class="mb-4">
                            <ul class="setup-steps">
                                <li class="setup-step completed">
                                    <h5>Database Creation</h5>
                                    <p>The database 'complaints_db' has been created successfully.</p>
                                </li>
                                <li class="setup-step completed">
                                    <h5>Tables Setup</h5>
                                    <p>All required tables have been created and configured.</p>
                                </li>
                                <li class="setup-step <?php echo check_admin_exists() ? 'completed' : 'current'; ?>">
                                    <h5>Admin User Creation</h5>
                                    <p><?php echo check_admin_exists() ? 'Admin user has been created.' : 'Create an admin user to manage the system.'; ?></p>
                                </li>
                            </ul>
                        </div>

                        <?php if (!check_admin_exists()): ?>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Next step:</strong> Create an admin user to manage the system.
                        </div>
                        <?php else: ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>All set!</strong> Your system is fully configured and ready to use.
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center mt-4">
                            <?php if (!check_admin_exists()): ?>
                            <a href="add_admin.php" class="btn btn-primary btn-lg btn-action">
                                <i class="bi bi-person-plus me-2"></i>Create Admin User
                            </a>
                            <?php endif; ?>
                            <a href="index.php?setup=complete" class="btn btn-success btn-lg btn-action">
                                <i class="bi bi-house me-2"></i>Go to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
