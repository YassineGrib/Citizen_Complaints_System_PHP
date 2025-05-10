<?php
/**
 * View Complaint
 * 
 * This file displays the details of a complaint and allows admins to update its status.
 */

// Include language handler
require_once '../includes/language.php';

// Include database configuration
require_once '../config/database.php';

// Include email configuration (optional)
if (file_exists('../config/mail.php')) {
    require_once '../config/mail.php';
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$complaint_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$complaint = null;
$error = '';
$success = '';
$responses = [];

// Get database connection
$conn = get_db_connection();

// Check if form is submitted for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitize_input($_POST['status']);
    
    // Update complaint status
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $complaint_id);
    
    if ($stmt->execute()) {
        $success = __('status_updated');
        
        // Get complaint details for email notification
        $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ?");
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $complaint = $result->fetch_assoc();
        
        // Send email notification (if PHPMailer is available)
        if (function_exists('send_email') && $complaint) {
            $subject = __('app_name') . ' - ' . __('status_updated');
            $body = '<p>' . __('status_updated') . '</p>';
            $body .= '<p>' . __('tracking_id') . ': <strong>' . $complaint['tracking_id'] . '</strong></p>';
            $body .= '<p>' . __('subject') . ': ' . $complaint['subject'] . '</p>';
            $body .= '<p>' . __('status') . ': <strong>' . __($new_status) . '</strong></p>';
            $body .= '<p>' . __('last_update') . ': ' . date('Y-m-d H:i:s') . '</p>';
            
            send_email($complaint['email'], $subject, $body);
        }
    } else {
        $error = __('error');
    }
    
    $stmt->close();
}

// Check if form is submitted for adding response
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_response'])) {
    $response = sanitize_input($_POST['response']);
    
    // Add response
    $stmt = $conn->prepare("INSERT INTO responses (complaint_id, response) VALUES (?, ?)");
    $stmt->bind_param("is", $complaint_id, $response);
    
    if ($stmt->execute()) {
        $success = __('response_added');
        
        // Update admin_response in complaints table
        $stmt = $conn->prepare("UPDATE complaints SET admin_response = ? WHERE id = ?");
        $stmt->bind_param("si", $response, $complaint_id);
        $stmt->execute();
        
        // Get complaint details for email notification
        $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ?");
        $stmt->bind_param("i", $complaint_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $complaint = $result->fetch_assoc();
        
        // Send email notification (if PHPMailer is available)
        if (function_exists('send_email') && $complaint) {
            $subject = __('app_name') . ' - ' . __('response_added');
            $body = '<p>' . __('response_added') . '</p>';
            $body .= '<p>' . __('tracking_id') . ': <strong>' . $complaint['tracking_id'] . '</strong></p>';
            $body .= '<p>' . __('subject') . ': ' . $complaint['subject'] . '</p>';
            $body .= '<p>' . __('response') . ':</p>';
            $body .= '<div style="padding: 10px; background-color: #f8f9fa; border-left: 4px solid #0d6efd; margin: 10px 0;">';
            $body .= nl2br($response);
            $body .= '</div>';
            
            send_email($complaint['email'], $subject, $body);
        }
    } else {
        $error = __('error');
    }
    
    $stmt->close();
}

// Get complaint details
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $complaint = $result->fetch_assoc();
} else {
    $error = __('complaint_not_found');
}

$stmt->close();

// Get responses
if ($complaint) {
    $stmt = $conn->prepare("SELECT * FROM responses WHERE complaint_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $responses[] = $row;
    }
    
    $stmt->close();
}

// Close connection
$conn->close();

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
    <title><?php echo __('complaint_details') . ' - ' . __('app_name'); ?></title>
    
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
    
    <!-- Navigation Bar -->
    <?php include 'nav.php'; ?>
    
    <div class="container py-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <div class="text-center">
                <a href="dashboard.php" class="btn btn-primary"><?php echo __('back'); ?></a>
            </div>
        <?php elseif ($complaint): ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Complaint Details -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?php echo __('complaint_details'); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
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
                                        <a href="../uploads/<?php echo $complaint['attachment']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark"></i> <?php echo __('view'); ?>
                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Responses -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?php echo __('responses'); ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($responses)): ?>
                                <div class="alert alert-info">
                                    <?php echo __('no_responses'); ?>
                                </div>
                            <?php else: ?>
                                <?php foreach ($responses as $response): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <p class="mb-1"><?php echo nl2br($response['response']); ?></p>
                                            <small class="text-muted"><?php echo date('Y-m-d H:i', strtotime($response['created_at'])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- Add Response Form -->
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $complaint_id); ?>">
                                <div class="mb-3">
                                    <label for="response" class="form-label"><?php echo __('add_response'); ?></label>
                                    <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
                                </div>
                                <button type="submit" name="add_response" class="btn btn-primary"><?php echo __('save'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Status Update -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?php echo __('update_status'); ?></h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $complaint_id); ?>">
                                <div class="mb-3">
                                    <label for="status" class="form-label"><?php echo __('status'); ?></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="new" <?php echo $complaint['status'] === 'new' ? 'selected' : ''; ?>><?php echo __('new'); ?></option>
                                        <option value="in_progress" <?php echo $complaint['status'] === 'in_progress' ? 'selected' : ''; ?>><?php echo __('in_progress'); ?></option>
                                        <option value="resolved" <?php echo $complaint['status'] === 'resolved' ? 'selected' : ''; ?>><?php echo __('resolved'); ?></option>
                                        <option value="rejected" <?php echo $complaint['status'] === 'rejected' ? 'selected' : ''; ?>><?php echo __('rejected'); ?></option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary"><?php echo __('save'); ?></button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?php echo __('actions'); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> <?php echo __('back'); ?>
                                </a>
                                <a href="../track.php?tracking_id=<?php echo $complaint['tracking_id']; ?>" target="_blank" class="btn btn-info">
                                    <i class="bi bi-eye"></i> <?php echo __('public_view'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
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
    <script src="../js/darkmode.js"></script>
</body>
</html>
