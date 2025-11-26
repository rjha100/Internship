<?php
/**
 * Logout Page
 * Handles user logout
 */
require_once 'config/session.php';

// Destroy the session
destroyUserSession();

// Redirect to login page with message
session_start();
setFlashMessage('success', 'You have been successfully logged out.');
header('Location: login.php');
exit();
