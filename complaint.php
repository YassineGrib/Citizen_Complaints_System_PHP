<?php
/**
 * Complaint Submission Form
 *
 * This file contains the form for submitting a new complaint.
 */

// Include language handler
require_once 'includes/language.php';

// Include database configuration
require_once 'config/database.php';

// Include email configuration (optional)
if (file_exists('config/mail.php')) {
    require_once 'config/mail.php';
}

// Complaint types
$complaint_types = [
    'roads' => __('roads'),
    'lighting' => __('lighting'),
    'parks' => __('parks'),
    'sports' => __('sports'),
    'waste' => __('waste'),
    'water' => __('water'),
    'noise' => __('noise'),
    'other' => __('other')
];

// Initialize variables
$name = '';
$email = '';
$phone = '';
$complaint_type = '';
$subject = '';
$description = '';
$location = '';
$errors = [];
$success = false;
$tracking_id = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $complaint_type = sanitize_input($_POST['complaint_type'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $location = sanitize_input($_POST['location'] ?? '');

    // Validate form data
    if (empty($name)) {
        $errors['name'] = __('required');
    }

    if (empty($email)) {
        $errors['email'] = __('required');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = __('invalid_email');
    }

    if (empty($phone)) {
        $errors['phone'] = __('required');
    }

    if (empty($complaint_type)) {
        $errors['complaint_type'] = __('required');
    }

    if (empty($subject)) {
        $errors['subject'] = __('required');
    }

    if (empty($description)) {
        $errors['description'] = __('required');
    }

    if (empty($location)) {
        $errors['location'] = __('required');
    }

    // Handle file upload
    $attachment = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_type = $_FILES['attachment']['type'];

        // Check file size
        if ($file_size > $max_size) {
            $errors['attachment'] = __('file_too_large');
        }

        // Check file type
        if (!in_array($file_type, $allowed_types)) {
            $errors['attachment'] = __('invalid_file_type');
        }

        // If no errors, move the file
        if (!isset($errors['attachment'])) {
            // Generate unique filename
            $new_file_name = uniqid() . '_' . $file_name;
            $upload_dir = 'uploads/';

            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the file
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $attachment = $new_file_name;
            } else {
                $errors['attachment'] = __('error');
            }
        }
    }

    // If no errors, save the complaint
    if (empty($errors)) {
        // Generate tracking ID
        $tracking_id = 'CMP' . date('Ymd') . rand(1000, 9999);

        // Get database connection
        $conn = get_db_connection();

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO complaints (tracking_id, name, email, phone, complaint_type, subject, description, location, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $tracking_id, $name, $email, $phone, $complaint_type, $subject, $description, $location, $attachment);

        // Execute statement
        if ($stmt->execute()) {
            $success = true;

            // Send email notification (if PHPMailer is available)
            if (function_exists('send_email')) {
                $subject = __('app_name') . ' - ' . __('complaint_submitted');
                $body = '<p>' . __('complaint_submitted') . '</p>';
                $body .= '<p>' . __('tracking_id_message') . ' <strong>' . $tracking_id . '</strong></p>';
                $body .= '<p>' . __('subject') . ': ' . $subject . '</p>';
                $body .= '<p>' . __('status') . ': ' . __('new') . '</p>';
                $body .= '<p>' . __('submission_date') . ': ' . date('Y-m-d H:i:s') . '</p>';

                send_email($email, $subject, $body);
            }

            // Reset form fields
            $name = $email = $phone = $complaint_type = $subject = $description = $location = '';
        } else {
            $errors['db'] = __('error');
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>" dir="<?php echo $language_direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('submit_complaint') . ' - ' . __('app_name'); ?></title>

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

    <div class="container py-5">
        <?php if ($success): ?>
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-check-circle-fill me-2"></i><?php echo __('complaint_submitted'); ?></h4>
                </div>
                <div class="card-body text-center">
                    <p class="lead"><?php echo __('tracking_id_message'); ?></p>
                    <div class="tracking-id"><?php echo $tracking_id; ?></div>
                    <p><?php echo __('track_complaint'); ?> <?php echo __('to_check_status'); ?></p>
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-2"><i class="bi bi-house-fill me-2"></i><?php echo __('home'); ?></a>
                        <a href="track.php?tracking_id=<?php echo $tracking_id; ?>" class="btn btn-secondary"><i class="bi bi-search me-2"></i><?php echo __('track_complaint'); ?></a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i><?php echo __('complaint_form'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['db'])): ?>
                        <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                        <!-- Personal Information -->
                        <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i><?php echo __('personal_info'); ?></h5>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label"><?php echo __('name'); ?> *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="" placeholder="<?php echo __('name'); ?>" required>
                                </div>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label for="email" class="form-label"><?php echo __('email'); ?> *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="" placeholder="<?php echo __('email'); ?>" required>
                                </div>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label for="phone" class="form-label"><?php echo __('phone'); ?> *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="" placeholder="<?php echo __('phone'); ?>" required>
                                </div>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Complaint Information -->
                        <h5 class="mb-3"><i class="bi bi-info-circle-fill me-2"></i><?php echo __('complaint_info'); ?></h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="complaint_type" class="form-label"><?php echo __('complaint_type'); ?> *</label>
                                <select class="form-select <?php echo isset($errors['complaint_type']) ? 'is-invalid' : ''; ?>" id="complaint_type" name="complaint_type" required>
                                    <option value="" selected disabled><?php echo __('select_type'); ?></option>
                                    <?php foreach ($complaint_types as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo $complaint_type === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['complaint_type'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['complaint_type']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label for="location" class="form-label"><?php echo __('location'); ?> *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                    <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" id="location" name="location" value="" placeholder="<?php echo __('location'); ?>" required>
                                </div>
                                <?php if (isset($errors['location'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['location']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label"><?php echo __('subject'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-chat-left-text-fill"></i></span>
                                <input type="text" class="form-control <?php echo isset($errors['subject']) ? 'is-invalid' : ''; ?>" id="subject" name="subject" value="" placeholder="<?php echo __('subject'); ?>" required>
                            </div>
                            <?php if (isset($errors['subject'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['subject']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><?php echo __('description'); ?> *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-text-fill"></i></span>
                                <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="5" placeholder="<?php echo __('description'); ?>" required></textarea>
                            </div>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label"><?php echo __('attachment'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-paperclip"></i></span>
                                <input type="file" class="form-control <?php echo isset($errors['attachment']) ? 'is-invalid' : ''; ?>" id="attachment" name="attachment">
                            </div>
                            <div class="form-text"><?php echo __('attachment_help'); ?></div>
                            <?php if (isset($errors['attachment'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['attachment']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-2"></i><?php echo __('reset'); ?></button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-2"></i><?php echo __('submit'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
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
            darkModeLabel.setAttribute('data-dark-text', '<?php echo __('dark_mode'); ?>');
            darkModeLabel.setAttribute('data-light-text', '<?php echo __('light_mode'); ?>');
        });
    </script>
    <script src="js/darkmode.js"></script>
</body>
</html>
