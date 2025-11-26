<?php
/**
 * Session Configuration
 * Handles session initialization and management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user's ID
 * @return int|null
 */
function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged in user's name
 * @return string|null
 */
function getCurrentUserName()
{
    return $_SESSION['user_name'] ?? null;
}

/**
 * Get current logged in user's email
 * @return string|null
 */
function getCurrentUserEmail()
{
    return $_SESSION['user_email'] ?? null;
}

/**
 * Set user session after login
 * @param int $id
 * @param string $name
 * @param string $email
 */
function setUserSession($id, $name, $email)
{
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession()
{
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Redirect if not logged in
 * @param string $redirectTo
 */
function requireLogin($redirectTo = 'login.php')
{
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
}

/**
 * Redirect if already logged in
 * @param string $redirectTo
 */
function redirectIfLoggedIn($redirectTo = 'dashboard.php')
{
    if (isLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
