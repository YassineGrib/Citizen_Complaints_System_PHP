<?php
/**
 * Admin Dashboard
 * 
 * This file displays the admin dashboard with complaints listing.
 */

// Include language handler
require_once '../includes/language.php';

// Include database configuration
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$complaints = [];
$total_complaints = 0;

// Get database connection
$conn = get_db_connection();

// Build SQL query
$sql = "SELECT * FROM complaints";
$params = [];
$types = "";

// Add status filter
if (!empty($status_filter)) {
    $sql .= " WHERE status = ?";
    $params[] = $status_filter;
    $types .= "s";
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR subject LIKE ? OR description LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }
} 
// Only search filter
else if (!empty($search)) {
    $sql .= " WHERE name LIKE ? OR subject LIKE ? OR description LIKE ?";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Add order by
$sql .= " ORDER BY created_at DESC";

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute statement
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Fetch complaints
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

// Get total count
$total_complaints = count($complaints);

// Close statement and connection
$stmt->close();
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
    <title><?php echo __('admin_dashboard') . ' - ' . __('app_name'); ?></title>
    
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
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <?php 
                    if (!empty($status_filter)) {
                        echo __($status_filter . '_complaints');
                    } else {
                        echo __('all_complaints');
                    }
                    ?>
                </h4>
                <span class="badge bg-light text-dark"><?php echo $total_complaints; ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($complaints)): ?>
                    <div class="alert alert-info">
                        <?php echo __('no_complaints'); ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo __('tracking_id'); ?></th>
                                    <th><?php echo __('name'); ?></th>
                                    <th><?php echo __('subject'); ?></th>
                                    <th><?php echo __('complaint_type'); ?></th>
                                    <th><?php echo __('submission_date'); ?></th>
                                    <th><?php echo __('status'); ?></th>
                                    <th><?php echo __('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $index => $complaint): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $complaint['tracking_id']; ?></td>
                                        <td><?php echo $complaint['name']; ?></td>
                                        <td><?php echo $complaint['subject']; ?></td>
                                        <td><?php echo __($complaint['complaint_type']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($complaint['created_at'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $status_badges[$complaint['status']]; ?>">
                                                <?php echo __($complaint['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" class="btn btn-sm btn-primary">
                                                <?php echo __('view'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
    <script src="../js/darkmode.js"></script>
</body>
</html>
