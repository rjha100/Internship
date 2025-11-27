<?php
/**
 * Enrollments List Page
 * Displays all enrollments with student and course information
 */
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Require login to access this page
requireLogin();

$pageTitle = 'Enrollment List';
include 'includes/header.php';
?>

<div class="container">
    <div class="card">
        <h2 class="card-title">Enrollment List</h2>
        <p style="color: #7f8c8d; text-align: center; padding: 2rem;">
            Enrollment list functionality will be implemented here.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
