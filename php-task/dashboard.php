<?php
/**
 * Dashboard Page
 * Main page after login - currently empty as per requirements
 */
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Require login to access this page
requireLogin();

$pageTitle = 'Dashboard';
include 'includes/header.php';

// Get flash message
$flash = getFlashMessage();
?>

<div class="container">
    <?php if ($flash) : ?>
        <div class="alert alert-<?php echo escape($flash['type']); ?>">
            <?php echo escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 class="card-title">Dashboard</h2>
        <div style="text-align: center; padding: 3rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
            </svg>
            <h3 style="color: #2c3e50; margin-top: 1.5rem;">Welcome, <?php echo escape(getCurrentUserName()); ?>!</h3>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">You are logged in as <?php echo escape(getCurrentUserEmail()); ?></p>
            <p style="color: #7f8c8d; margin-top: 1rem;">Use the navigation above to manage courses and enrollments.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
