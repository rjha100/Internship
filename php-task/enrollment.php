<?php
/**
 * Enrollment Page
 * Allows students to enroll in courses
 */
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Require login to access this page
requireLogin();

$pageTitle = 'Enrollment';
include 'includes/header.php';
?>

<div class="container">
    <div class="card">
        <h2 class="card-title">Course Enrollment</h2>
        <p style="color: #7f8c8d; text-align: center; padding: 2rem;">
            Enrollment functionality will be implemented here.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
