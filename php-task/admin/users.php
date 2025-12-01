<?php
/**
 * Admin - Manage Users
 * List, add, edit, delete users
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'Manage Users';

$pdo = getDBConnection();
$errors = [];
$success = '';

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // Don't allow deleting yourself
    if ($deleteId === getCurrentUserId()) {
        setFlashMessage('error', 'You cannot delete your own account.');
    } else {
        // Delete user (enrollments and professor_courses will cascade)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$deleteId]);
        setFlashMessage('success', 'User deleted successfully.');
    }
    redirect('users.php');
}

// Handle add/edit user form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $role = sanitize($_POST['role'] ?? 'student');
    $password = $_POST['password'] ?? '';
    $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;
    
    // Validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!in_array($role, ['student', 'professor', 'admin'])) $errors[] = 'Invalid role.';
    
    // Check if email already exists (for new users or if email changed)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $editId ?? 0]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        }
    }
    
    if (empty($errors)) {
        if ($editId) {
            // Update existing user
            if (!empty($password)) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $role, password_hash($password, PASSWORD_DEFAULT), $editId]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $role, $editId]);
            }
            setFlashMessage('success', 'User updated successfully.');
        } else {
            // Create new user
            if (empty($password)) {
                $errors[] = 'Password is required for new users.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $role]);
                setFlashMessage('success', 'User created successfully.');
            }
        }

        if (empty($errors)) {
            redirect('users.php');
        }
    }
}

// Get user for editing
$editUser = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

// Get all users with filter
$roleFilter = isset($_GET['role']) && in_array($_GET['role'], ['student', 'professor', 'admin']) ? $_GET['role'] : null;

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Get total count for pagination
if ($roleFilter) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
    $countStmt->execute([$roleFilter]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
}
$totalUsers = $countStmt->fetch()['total'];
$totalPages = ceil($totalUsers / $perPage);

// Get users for current page
if ($roleFilter) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$roleFilter, $perPage, $offset]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$perPage, $offset]);
}
$users = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .filters {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        background: white;
        border-radius: 6px;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .action-btns .btn-icon {
        display: none;
    }

    .action-btns .btn-icon svg {
        width: 14px;
        height: 14px;
    }

    .action-btns .btn-text {
        display: inline;
    }

    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .role-badge.student { background: #dbeafe; color: #1e40af; }
    .role-badge.professor { background: #d1fae5; color: #065f46; }
    .role-badge.admin { background: #fef3c7; color: #92400e; }

    @media (max-width: 768px) {
        .filter-btn {
            padding: 0.35rem 0.6rem;
            font-size: 0.8rem;
        }

        .role-badge {
            padding: 0.2rem 0.4rem;
            font-size: 0.65rem;
            white-space: nowrap;
        }

        .action-btns {
            flex-direction: row;
            gap: 0.4rem;
        }

        .action-btns .btn-sm {
            padding: 0.45rem;
            font-size: 0.7rem;
            white-space: nowrap;
        }

        .action-btns .btn-text {
            display: none;
        }

        .action-btns .btn-icon {
            display: inline-flex;
        }

        .action-btns .btn-icon svg {
            width: 16px;
            height: 16px;
        }

        .table th {
            font-size: 0.75rem;
            padding: 0.6rem 0.5rem;
        }

        .table td {
            font-size: 0.85rem;
            padding: 0.7rem 0.5rem;
        }

        .table td strong {
            font-size: 0.9rem;
        }

        .table td, .table th {
            white-space: nowrap;
        }

        .table td:first-child {
            white-space: normal;
        }

        .hide-mobile {
            display: none;
        }
    }
    
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        margin: 1rem;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-header h2 {
        font-size: 1.25rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .modal {
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem;
            max-height: 85vh;
        }

        .modal-header {
            margin-bottom: 1rem;
        }

        .modal-header h2 {
            font-size: 1rem;
        }

        .modal-close {
            font-size: 1.25rem;
        }

        .modal .form-group {
            margin-bottom: 0.75rem;
        }

        .modal .form-group label {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .modal .form-control {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }

        .modal .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }
    }
    
    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .btn-danger:hover {
        background: #dc2626;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        text-decoration: none;
        color: #374151;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .pagination .active {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .pagination .disabled {
        color: #9ca3af;
        pointer-events: none;
    }

    .pagination-info {
        text-align: center;
        color: #6b7280;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .pagination a,
        .pagination span {
            padding: 0.35rem 0.5rem;
            font-size: 0.7rem;
        }

        .pagination-info {
            font-size: 0.7rem;
        }
    }

    /* Expandable Row Styles */
    .expand-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.2rem;
        color: #6b7280;
        transition: transform 0.2s;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .expand-btn svg {
        width: 16px;
        height: 16px;
    }

    .expand-btn.expanded {
        transform: rotate(180deg);
    }

    .row-details {
        display: none;
    }

    .row-details.show {
        display: table-row;
    }

    .row-details td {
        padding: 0.5rem 0.75rem !important;
        background: #f9fafb;
        border-top: none !important;
    }

    .detail-content {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .detail-item svg {
        width: 14px;
        height: 14px;
        opacity: 0.7;
    }

    .detail-label {
        font-weight: 600;
        color: #374151;
    }

    @media (max-width: 768px) {
        .expand-btn {
            display: inline-flex;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:active {
            background: #f3f4f6;
        }

        .name-cell {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-content {
            flex-direction: column;
            gap: 0.6rem;
            font-size: 0.85rem;
        }

        .detail-item {
            gap: 0.5rem;
        }

        .detail-item svg {
            width: 16px;
            height: 16px;
        }

        .row-details td {
            padding: 0.75rem 1rem !important;
        }
    }

</style>

<div class="container">
    <?php if ($flash) : ?>
        <div class="alert alert-<?php echo escape($flash['type']); ?>">
            <?php echo escape($flash['message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo escape($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title">Manage Users</h1>
                <p class="page-subtitle"><?php echo $totalUsers; ?> user(s) found</p>
            </div>
            <button class="btn btn-primary" onclick="openModal()">+ Add User</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <a href="users.php" class="filter-btn <?php echo !$roleFilter ? 'active' : ''; ?>">All</a>
        <a href="users.php?role=student" class="filter-btn <?php echo $roleFilter === 'student' ? 'active' : ''; ?>">Students</a>
        <a href="users.php?role=professor" class="filter-btn <?php echo $roleFilter === 'professor' ? 'active' : ''; ?>">Professors</a>
        <a href="users.php?role=admin" class="filter-btn <?php echo $roleFilter === 'admin' ? 'active' : ''; ?>">Admins</a>
    </div>

    <div class="card">
        <?php if (count($users) > 0) : ?>
            <div class="table-responsive admin-user-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hide-mobile">Email</th>
                        <th class="hide-mobile">Phone</th>
                        <th>Role</th>
                        <th class="hide-mobile">Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user) : ?>
                        <tr class="clickable-row" onclick="toggleDetails(<?php echo $index; ?>, event)">
                            <td>
                                <div class="name-cell">
                                    <button class="expand-btn" id="expand-<?php echo $index; ?>" type="button">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                    <strong><?php echo escape($user['name']); ?></strong>
                                </div>
                            </td>
                            <td class="hide-mobile"><?php echo escape($user['email']); ?></td>
                            <td class="hide-mobile"><?php echo $user['phone'] ? escape($user['phone']) : '<span style="color: #9ca3af;">—</span>'; ?></td>
                            <td>
                                <span class="role-badge <?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="hide-mobile"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-btns" onclick="event.stopPropagation()">
                                    <a href="users.php?edit=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <span class="btn-icon"><svg viewBox="0 0 494.936 494.936" fill="currentColor"><path d="M389.844,182.85c-6.743,0-12.21,5.467-12.21,12.21v222.968c0,23.562-19.174,42.735-42.736,42.735H67.157c-23.562,0-42.736-19.174-42.736-42.735V150.285c0-23.562,19.174-42.735,42.736-42.735h267.741c6.743,0,12.21-5.467,12.21-12.21s-5.467-12.21-12.21-12.21H67.157C30.126,83.13,0,113.255,0,150.285v267.743c0,37.029,30.126,67.155,67.157,67.155h267.741c37.03,0,67.156-30.126,67.156-67.155V195.061C402.054,188.318,396.587,182.85,389.844,182.85z"/><path d="M483.876,20.791c-14.72-14.72-38.669-14.714-53.377,0L221.352,229.944c-0.28,0.28-3.434,3.559-4.251,5.396l-28.963,65.069c-2.057,4.619-1.056,10.027,2.521,13.6c2.337,2.336,5.461,3.576,8.639,3.576c1.675,0,3.362-0.346,4.96-1.057l65.07-28.963c1.83-0.815,5.114-3.97,5.396-4.25L483.876,74.169c7.131-7.131,11.06-16.61,11.06-26.692C494.936,37.396,491.007,27.915,483.876,20.791z M466.61,56.897L257.457,266.05c-0.035,0.036-0.055,0.078-0.089,0.107l-33.989,15.131L238.51,247.3c0.03-0.036,0.071-0.055,0.107-0.09L447.765,38.058c5.038-5.039,13.819-5.033,18.846,0.005c2.518,2.51,3.905,5.855,3.905,9.414C470.516,51.036,469.127,54.38,466.61,56.897z"/></svg></span>
                                        <span class="btn-text">Edit</span>
                                    </a>
                                    <?php if ($user['id'] !== getCurrentUserId()) : ?>
                                        <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           title="Delete"
                                           onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this user?');">
                                            <span class="btn-icon"><svg viewBox="0 0 482.428 482.429" fill="currentColor"><path d="M381.163,57.799h-75.094C302.323,25.316,274.686,0,241.214,0c-33.471,0-61.104,25.315-64.85,57.799h-75.098c-30.39,0-55.111,24.728-55.111,55.117v2.828c0,23.223,14.46,43.1,34.83,51.199v260.369c0,30.39,24.724,55.117,55.112,55.117h210.236c30.389,0,55.111-24.729,55.111-55.117V166.944c20.369-8.1,34.83-27.977,34.83-51.199v-2.828C436.274,82.527,411.551,57.799,381.163,57.799z M241.214,26.139c19.037,0,34.927,13.645,38.443,31.66h-76.879C206.293,39.783,222.184,26.139,241.214,26.139z M375.305,427.312c0,15.978-13,28.979-28.973,28.979H136.096c-15.973,0-28.973-13.002-28.973-28.979V170.861h268.182V427.312z M410.135,115.744c0,15.978-13,28.979-28.973,28.979H101.266c-15.973,0-28.973-13.001-28.973-28.979v-2.828c0-15.978,13-28.979,28.973-28.979h279.897c15.973,0,28.973,13.001,28.973,28.979V115.744z"/><path d="M171.144,422.863c7.218,0,13.069-5.853,13.069-13.068V262.641c0-7.216-5.852-13.07-13.069-13.07c-7.217,0-13.069,5.854-13.069,13.07v147.154C158.074,417.012,163.926,422.863,171.144,422.863z"/><path d="M241.214,422.863c7.218,0,13.07-5.853,13.07-13.068V262.641c0-7.216-5.854-13.07-13.07-13.07c-7.217,0-13.069,5.854-13.069,13.07v147.154C228.145,417.012,233.996,422.863,241.214,422.863z"/><path d="M311.284,422.863c7.217,0,13.068-5.853,13.068-13.068V262.641c0-7.216-5.852-13.07-13.068-13.07c-7.219,0-13.07,5.854-13.07,13.07v147.154C298.213,417.012,304.067,422.863,311.284,422.863z"/></svg></span>
                                            <span class="btn-text">Delete</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <!-- Expandable details row -->
                        <tr class="row-details" id="details-<?php echo $index; ?>">
                            <td colspan="6">
                                <div class="detail-content">
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="detail-label">Email:</span>
                                        <?php echo escape($user['email']); ?>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                        </svg>
                                        <span class="detail-label">Phone:</span>
                                        <?php echo $user['phone'] ? escape($user['phone']) : '—'; ?>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span class="detail-label">Created:</span>
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <?php if ($totalPages > 1) : ?>
            <div class="pagination">
                <?php
                $queryParams = $roleFilter ? "role=$roleFilter&" : "";
                ?>
                <a href="users.php?<?php echo $queryParams; ?>page=1" 
                   class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">&laquo;</a>
                <a href="users.php?<?php echo $queryParams; ?>page=<?php echo max(1, $page - 1); ?>" 
                   class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">&lsaquo;</a>
                
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                for ($i = $startPage; $i <= $endPage; $i++) :
                    ?>
                    <a href="users.php?<?php echo $queryParams; ?>page=<?php echo $i; ?>" 
                       class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <a href="users.php?<?php echo $queryParams; ?>page=<?php echo min($totalPages, $page + 1); ?>" 
                   class="<?php echo $page >= $totalPages ? 'disabled' : ''; ?>">&rsaquo;</a>
                <a href="users.php?<?php echo $queryParams; ?>page=<?php echo $totalPages; ?>" 
                   class="<?php echo $page >= $totalPages ? 'disabled' : ''; ?>">&raquo;</a>
            </div>
            <div class="pagination-info">
                Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $perPage, $totalUsers); ?> of <?php echo $totalUsers; ?> users
            </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="empty-state">
                <h3>No users found</h3>
                <p>No users match the current filter.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal-overlay <?php echo $editUser ? 'active' : ''; ?>" id="userModal">
    <div class="modal">
        <div class="modal-header">
            <h2><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="users.php">
            <?php if ($editUser) : ?>
                <input type="hidden" name="edit_id" value="<?php echo $editUser['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?php echo $editUser ? escape($editUser['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required
                       value="<?php echo $editUser ? escape($editUser['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       value="<?php echo $editUser ? escape($editUser['phone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="student" <?php echo ($editUser && $editUser['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="professor" <?php echo ($editUser && $editUser['role'] === 'professor') ? 'selected' : ''; ?>>Professor</option>
                    <option value="admin" <?php echo ($editUser && $editUser['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Password <?php echo $editUser ? '(leave blank to keep current)' : ''; ?></label>
                <input type="password" id="password" name="password" class="form-control"
                       <?php echo !$editUser ? 'required' : ''; ?>>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <?php echo $editUser ? 'Update User' : 'Create User'; ?>
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDetails(index, event) {
    // Only work on mobile
    if (window.innerWidth > 768) return;
    
    // Don't toggle if clicking on buttons
    if (event.target.closest('.action-btns')) return;
    
    const detailsRow = document.getElementById('details-' + index);
    const expandBtn = document.getElementById('expand-' + index);
    
    detailsRow.classList.toggle('show');
    expandBtn.classList.toggle('expanded');
}

function openModal() {
    document.getElementById('userModal').classList.add('active');
}

function closeModal() {
    document.getElementById('userModal').classList.remove('active');
    // Clear the edit parameter from URL
    if (window.location.search.includes('edit=')) {
        window.location.href = 'users.php';
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// Close modal on backdrop click
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include '../includes/footer.php'; ?>
