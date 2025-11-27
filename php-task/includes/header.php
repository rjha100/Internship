<?php
/**
 * Header Template
 * Common header included in all pages
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' - ' : ''; ?>Student Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1a1a2e;
        }

        .navbar {
            background-color: white;
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #e5e7eb;
        }

        .navbar-brand {
            color: #667eea;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand:hover {
            color: #764ba2;
        }

        .navbar-brand svg {
            width: 28px;
            height: 28px;
        }

        .navbar-nav {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            background-color: #f3f4f6;
            padding: 4px;
            border-radius: 10px;
        }

        .navbar-nav a {
            color: #6b7280;
            text-decoration: none;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .navbar-nav a:hover {
            color: #1a1a2e;
        }

        .navbar-nav a.active {
            background-color: white;
            color: #1a1a2e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
        }

        .btn-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .btn-danger:hover {
            background-color: #fecaca;
        }

        .btn-success {
            background-color: #d1fae5;
            color: #059669;
        }

        .btn-success:hover {
            background-color: #a7f3d0;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
        }

        .card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 1.25rem;
            color: #1a1a2e;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #667eea;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1a1a2e;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control.error {
            border-color: #dc2626;
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }

        select.form-control option {
            padding: 0.5rem;
        }

        select.form-control option:disabled {
            color: #9ca3af;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .alert-info {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tr:hover {
            background-color: #f9fafb;
        }

        .table td {
            color: #4b5563;
        }

        .footer {
            background-color: white;
            color: #6b7280;
            text-align: center;
            padding: 1.25rem;
            margin-top: auto;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9rem;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-primary {
            background-color: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6b7280;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
            opacity: 0.4;
        }

        .empty-state h3 {
            color: #374151;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .empty-state p {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .navbar-nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .user-info {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php if (isLoggedIn()) : 
    $currentPage = basename($_SERVER['PHP_SELF']);
    $role = getCurrentUserRole();
    
    // Determine base path for links based on current directory
    $currentDir = basename(dirname($_SERVER['PHP_SELF']));
    $isInSubfolder = in_array($currentDir, ['student', 'professor', 'admin']);
    $logoutPath = $isInSubfolder ? '../logout.php' : 'logout.php';
    
    // Role-specific colors and labels
    $roleColors = [
        'student' => ['gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'badge' => '#667eea'],
        'professor' => ['gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'badge' => '#10b981'],
        'admin' => ['gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'badge' => '#f59e0b']
    ];
    $roleLabels = ['student' => 'Student', 'professor' => 'Professor', 'admin' => 'Admin'];
?>
<style>
    .role-badge-nav {
        display: inline-block;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-left: 0.5rem;
        color: white;
        background-color: <?php echo $roleColors[$role]['badge']; ?>;
    }
    
    <?php if ($role === 'professor') : ?>
    .navbar-brand { color: #10b981 !important; }
    .navbar-brand:hover { color: #059669 !important; }
    .navbar-nav a.active { color: #10b981 !important; }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; }
    .btn-primary:hover { box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35) !important; }
    <?php elseif ($role === 'admin') : ?>
    .navbar-brand { color: #f59e0b !important; }
    .navbar-brand:hover { color: #d97706 !important; }
    .navbar-nav a.active { color: #f59e0b !important; }
    .btn-primary { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; }
    .btn-primary:hover { box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35) !important; }
    <?php endif; ?>
</style>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
        </svg>
        <?php 
        if ($role === 'student') echo 'Student Portal';
        elseif ($role === 'professor') echo 'Professor Portal';
        else echo 'Admin Panel';
        ?>
    </a>
    <ul class="navbar-nav">
        <?php if ($role === 'student') : ?>
            <li>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="courses.php" class="<?php echo $currentPage === 'courses.php' ? 'active' : ''; ?>">
                    Courses
                </a>
            </li>
            <li>
                <a href="enrollment.php" class="<?php echo $currentPage === 'enrollment.php' ? 'active' : ''; ?>">
                    Enroll
                </a>
            </li>
            <li>
                <a href="enrollments_list.php" class="<?php echo $currentPage === 'enrollments_list.php' ? 'active' : ''; ?>">
                    My Courses
                </a>
            </li>
        <?php elseif ($role === 'professor') : ?>
            <li>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="my-courses.php" class="<?php echo in_array($currentPage, ['my-courses.php', 'students.php']) ? 'active' : ''; ?>">
                    My Courses
                </a>
            </li>
        <?php elseif ($role === 'admin') : ?>
            <li>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                    Users
                </a>
            </li>
            <li>
                <a href="courses.php" class="<?php echo $currentPage === 'courses.php' ? 'active' : ''; ?>">
                    Courses
                </a>
            </li>
            <li>
                <a href="enrollments.php" class="<?php echo $currentPage === 'enrollments.php' ? 'active' : ''; ?>">
                    Enrollments
                </a>
            </li>
            <li>
                <a href="professors.php" class="<?php echo $currentPage === 'professors.php' ? 'active' : ''; ?>">
                    Professors
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="user-info">
        <span>
            👋 <?php echo escape(getCurrentUserName()); ?>
            <span class="role-badge-nav"><?php echo $roleLabels[$role]; ?></span>
        </span>
        <a href="<?php echo $logoutPath; ?>" class="btn btn-danger">Logout</a>
    </div>
</nav>
<?php endif; ?>
