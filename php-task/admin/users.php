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

if ($roleFilter) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
    $stmt->execute([$roleFilter]);
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
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
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }
    
    .action-btns {
        display: flex;
        gap: 0.5rem;
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
                <p class="page-subtitle"><?php echo count($users); ?> user(s) found</p>
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
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><strong><?php echo escape($user['name']); ?></strong></td>
                            <td><?php echo escape($user['email']); ?></td>
                            <td><?php echo $user['phone'] ? escape($user['phone']) : '<span style="color: #9ca3af;">—</span>'; ?></td>
                            <td>
                                <span class="role-badge <?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="users.php?edit=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <?php if ($user['id'] !== getCurrentUserId()) : ?>
                                        <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this user?');">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
