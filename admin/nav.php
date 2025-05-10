<?php
/**
 * Admin Navigation Bar
 *
 * This file contains the navigation bar for the admin dashboard.
 */

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>

<nav class="navbar navbar-expand-lg <?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?> shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i><?php echo __('admin_dashboard'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo !isset($_GET['status']) ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-list-ul me-1"></i> <?php echo __('all_complaints'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['status']) && $_GET['status'] === 'new' ? 'active' : ''; ?>" href="dashboard.php?status=new">
                        <i class="bi bi-plus-circle-fill me-1"></i> <?php echo __('new_complaints'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['status']) && $_GET['status'] === 'in_progress' ? 'active' : ''; ?>" href="dashboard.php?status=in_progress">
                        <i class="bi bi-hourglass-split me-1"></i> <?php echo __('in_progress_complaints'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['status']) && $_GET['status'] === 'resolved' ? 'active' : ''; ?>" href="dashboard.php?status=resolved">
                        <i class="bi bi-check-circle-fill me-1"></i> <?php echo __('resolved_complaints'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['status']) && $_GET['status'] === 'rejected' ? 'active' : ''; ?>" href="dashboard.php?status=rejected">
                        <i class="bi bi-x-circle-fill me-1"></i> <?php echo __('rejected_complaints'); ?>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <!-- Dark Mode Toggle -->
                <li class="nav-item me-3">
                    <div class="form-check form-switch nav-link d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="darkModeSwitch" <?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="darkModeSwitch">
                            <?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? __('light_mode') : __('dark_mode'); ?>
                        </label>
                    </div>
                </li>

                <!-- Language Dropdown -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-translate me-1"></i> <?php echo __('language'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <?php foreach ($available_languages as $code => $name): ?>
                            <li>
                                <a class="dropdown-item <?php echo $current_language === $code ? 'active' : ''; ?>" href="?lang=<?php echo $code; ?>">
                                    <?php echo $name; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> <?php echo __('logout'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Search Form -->
<div class="container mt-3">
    <form action="dashboard.php" method="GET" class="d-flex">
        <?php if (isset($_GET['status'])): ?>
            <input type="hidden" name="status" value="<?php echo $_GET['status']; ?>">
        <?php endif; ?>
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="<?php echo __('search'); ?>..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> <?php echo __('search'); ?></button>
        </div>
    </form>
</div>
