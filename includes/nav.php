<?php
/**
 * Navigation Bar
 *
 * This file contains the navigation bar for the Citizen Complaints System.
 */
?>

<nav class="navbar navbar-expand-lg <?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'navbar-dark bg-dark' : 'navbar-light bg-light'; ?> shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <?php echo __('app_name'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-fill me-1"></i> <?php echo __('home'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'complaint.php' ? 'active' : ''; ?>" href="complaint.php">
                        <i class="bi bi-pencil-square me-1"></i> <?php echo __('submit_complaint'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'track.php' ? 'active' : ''; ?>" href="track.php">
                        <i class="bi bi-search me-1"></i> <?php echo __('track_complaint'); ?>
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

                <!-- Admin Login -->
                <li class="nav-item">
                    <a class="nav-link" href="admin/login.php">
                        <i class="bi bi-shield-lock-fill me-1"></i> <?php echo __('admin_login'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
