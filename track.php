<?php
/**
 * Complaint Tracking Page
 * 
 * This file allows citizens to track the status of their complaints.
 */

// Include language handler
require_once 'includes/language.php';

// Include database configuration
require_once 'config/database.php';

// Initialize variables
$tracking_id = '';
$complaint = null;
$error = '';

// Check if tracking ID is provided
if (isset($_GET['tracking_id']) && !empty($_GET['tracking_id'])) {
    $tracking_id = sanitize_input($_GET['tracking_id']);
    
    // Get database connection
    $conn = get_db_connection();
    
    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE tracking_id = ?");
    $stmt->bind_param("s", $tracking_id);
    
    // Execute statement
    $stmt->execute();
    
    // Get result
    $result = $stmt->get_result();
    
    // Check if complaint exists
    if ($result->num_rows > 0) {
        $complaint = $result->fetch_assoc();
    } else {
        $error = __('complaint_not_found');
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get tracking ID
    $tracking_id = sanitize_input($_POST['tracking_id'] ?? '');
    
    // Redirect to the same page with tracking ID as GET parameter
    header("Location: track.php?tracking_id=" . urlencode($tracking_id));
    exit;
}

// Status badge classes
$status_badges = [
    'new' => 'bg-info',
    'in_progress' => 'bg-warning text-dark',
    'resolved' => 'bg-success',
    'rejected' => 'bg-danger'
];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>" dir="<?php echo $language_direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('track_complaint') . ' - ' . __('app_name'); ?></title>
    
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
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo __('tracking_title'); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($complaint): ?>
                    <!-- Complaint Details -->
                    <div class="mb-4">
                        <h5><?php echo __('complaint_details'); ?></h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><?php echo __('tracking_id'); ?>:</strong> <?php echo $complaint['tracking_id']; ?></p>
                                <p><strong><?php echo __('name'); ?>:</strong> <?php echo $complaint['name']; ?></p>
                                <p><strong><?php echo __('email'); ?>:</strong> <?php echo $complaint['email']; ?></p>
                                <p><strong><?php echo __('phone'); ?>:</strong> <?php echo $complaint['phone']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <strong><?php echo __('status'); ?>:</strong> 
                                    <span class="badge <?php echo $status_badges[$complaint['status']]; ?>">
                                        <?php echo __($complaint['status']); ?>
                                    </span>
                                </p>
                                <p><strong><?php echo __('complaint_type'); ?>:</strong> <?php echo __($complaint['complaint_type']); ?></p>
                                <p><strong><?php echo __('submission_date'); ?>:</strong> <?php echo date('Y-m-d H:i', strtotime($complaint['created_at'])); ?></p>
                                <p><strong><?php echo __('last_update'); ?>:</strong> <?php echo date('Y-m-d H:i', strtotime($complaint['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5><?php echo __('subject'); ?></h5>
                        <p><?php echo $complaint['subject']; ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5><?php echo __('description'); ?></h5>
                        <p><?php echo nl2br($complaint['description']); ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h5><?php echo __('location'); ?></h5>
                        <p><?php echo $complaint['location']; ?></p>
                    </div>
                    
                    <?php if (!empty($complaint['attachment'])): ?>
                        <div class="mb-4">
                            <h5><?php echo __('attachment'); ?></h5>
                            <p>
                                <a href="uploads/<?php echo $complaint['attachment']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark"></i> <?php echo __('view'); ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($complaint['admin_response'])): ?>
                        <div class="mb-4">
                            <h5><?php echo __('admin_response'); ?></h5>
                            <div class="card">
                                <div class="card-body">
                                    <?php echo nl2br($complaint['admin_response']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Tracking Form -->
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tracking_id" class="form-label"><?php echo __('tracking_id'); ?></label>
                                    <input type="text" class="form-control" id="tracking_id" name="tracking_id" value="<?php echo $tracking_id; ?>" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary"><?php echo __('track'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
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
            darkModeLabel.setAttribute('data-dark-text', '<?php echo __('dark_mode'); ?>');
            darkModeLabel.setAttribute('data-light-text', '<?php echo __('light_mode'); ?>');
        });
    </script>
    <script src="js/darkmode.js"></script>
</body>
</html>
