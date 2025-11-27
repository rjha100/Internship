<?php
/**
 * Check Email Availability API
 * Used for AJAX client-side validation during registration
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$email = sanitizeInput($_GET['email'] ?? '');

if (empty($email) || !isValidEmail($email)) {
    echo json_encode(['available' => false, 'error' => 'Invalid email']);
    exit();
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $available = !$stmt->fetch();
    echo json_encode(['available' => $available]);
} catch (PDOException $e) {
    echo json_encode(['available' => true, 'error' => 'Database error']);
}
