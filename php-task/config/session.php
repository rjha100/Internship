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
 * @param string $role
 */
function setUserSession($id, $name, $email, $role = 'student')
{
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
}

/**
 * Get current logged in user's role
 * @return string|null
 */
function getCurrentUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if current user is a student
 * @return bool
 */
function isStudent()
{
    $role = getCurrentUserRole();
    return $role === 'student' || $role === null; // Treat null/missing role as student for backwards compatibility
}

/**
 * Check if current user is a professor
 * @return bool
 */
function isProfessor()
{
    return getCurrentUserRole() === 'professor';
}

/**
 * Check if current user is an admin
 * @return bool
 */
function isAdmin()
{
    return getCurrentUserRole() === 'admin';
}

/**
 * Require student role
 * Redirects to appropriate page if not a student
 */
function requireStudent()
{
    requireLogin();
    if (!isStudent()) {
        header("Location: " . getBaseUrlByRole());
        exit();
    }
}

/**
 * Require professor role
 * Redirects to appropriate page if not a professor
 */
function requireProfessor()
{
    requireLogin();
    if (!isProfessor()) {
        header("Location: " . getBaseUrlByRole());
        exit();
    }
}

/**
 * Require admin role
 * Redirects to appropriate page if not an admin
 */
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header("Location: " . getBaseUrlByRole());
        exit();
    }
}

/**
 * Get base URL based on user role
 * Automatically determines correct path based on current directory
 * @return string
 */
function getBaseUrlByRole()
{
    $role = getCurrentUserRole();
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    $isInSubfolder = in_array($currentDir, ['student', 'professor', 'admin']);
    $prefix = $isInSubfolder ? '../' : '';
    
    switch ($role) {
        case 'admin':
            return $prefix . 'admin/dashboard.php';
        case 'professor':
            return $prefix . 'professor/dashboard.php';
        case 'student':
        default:
            return $prefix . 'student/dashboard.php';
    }
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
 * Automatically determines correct login.php path based on current directory
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        // Determine if we're in a subfolder
        $currentDir = basename(dirname($_SERVER['PHP_SELF']));
        $isInSubfolder = in_array($currentDir, ['student', 'professor', 'admin']);
        $loginPath = $isInSubfolder ? '../login.php' : 'login.php';
        header("Location: $loginPath");
        exit();
    }
}

/**
 * Redirect if already logged in
 * Redirects to role-specific dashboard (only use on login/register pages)
 */
function redirectIfLoggedIn()
{
    if (isLoggedIn()) {
        $role = getCurrentUserRole();
        
        // Get current script to avoid redirect loops
        $currentScript = $_SERVER['PHP_SELF'];
        
        // Determine target based on role
        switch ($role) {
            case 'admin':
                $target = '/admin/dashboard.php';
                break;
            case 'professor':
                $target = '/professor/dashboard.php';
                break;
            case 'student':
            default:
                $target = '/student/dashboard.php';
                break;
        }
        
        // Only redirect if not already on the target page
        if (strpos($currentScript, $target) === false) {
            header('Location: ' . ltrim($target, '/'));
            exit();
        }
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
