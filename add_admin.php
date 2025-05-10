<?php
/**
 * Admin User Creation Script
 *
 * This script creates an admin user for the Citizen Complaints System.
 */

// Include database configuration
require_once 'config/database.php';

// Default admin credentials
$default_name = 'Administrator';
$default_email = 'admin@admin.com';
$default_password = 'admin';

// Check if script is run from command line or included from index.php
$is_cli = (php_sapi_name() === 'cli');
$is_included = (basename($_SERVER['SCRIPT_NAME']) !== basename(__FILE__));

// Create admin user automatically if run from command line or included from index.php
if ($is_cli || $is_included) {
    $name = $default_name;
    $email = $default_email;
    $password = $default_password;

    // Create admin user
    $conn = get_db_connection();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Admin user already exists with email: $email\n";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert admin user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Admin user created successfully!\n";
            echo "Email: $email\n";
            echo "Password: $password\n";
            echo "Please change your password after logging in.\n";
        } else {
            echo "Error creating admin user: " . $stmt->error . "\n";
        }

        $stmt->close();
    }

    $conn->close();
    exit;
}

// Check if form is submitted
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : $default_name;
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : $default_email;
    $password = isset($_POST['password']) ? $_POST['password'] : $default_password;

    // Validate inputs
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 5) {
        $errors[] = "Password must be at least 5 characters long";
    }

    // If no errors, create admin user
    if (empty($errors)) {
        // Get database connection
        $conn = get_db_connection();

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert admin user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Admin user created successfully!";
                $success .= "<br><br>You can now log in to the admin dashboard with:";
                $success .= "<br>Email: " . $email;
                $success .= "<br>Password: " . $password;
                $success .= "<br><br><strong>Important:</strong> Please change your password after logging in.";
            } else {
                $errors[] = "Error creating admin user: " . $stmt->error;
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User - Citizen Complaints System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .setup-icon {
            font-size: 4rem;
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
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <?php if (isset($success)): ?>
                        <div class="card-body text-center p-5">
                            <div class="setup-icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <h2 class="mb-3">Admin User Created Successfully!</h2>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Important:</strong> Please change your password after logging in.
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <a href="admin/login.php" class="btn btn-primary btn-lg btn-action">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Go to Admin Login
                                </a>
                                <a href="index.php" class="btn btn-success btn-lg btn-action">
                                    <i class="bi bi-house me-2"></i>Go to Homepage
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create Admin User</h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : $default_name; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : $default_email; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" value="<?php echo isset($password) ? $password : $default_password; ?>" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Homepage
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-person-plus me-2"></i>Create Admin User
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
