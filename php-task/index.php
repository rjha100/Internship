<?php
/**
 * Index Page
 * Redirects to appropriate page based on login status
 */
require_once 'config/session.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
