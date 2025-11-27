<?php
/**
 * Index Page
 * Redirects to appropriate page based on login status and role
 */
require_once 'config/session.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $role = getCurrentUserRole();
    
    // Redirect to role-specific dashboard
    switch ($role) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'professor':
            header('Location: professor/dashboard.php');
            break;
        case 'student':
        default:
            header('Location: student/dashboard.php');
            break;
    }
} else {
    header('Location: login.php');
}
exit();
