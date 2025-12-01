<?php
/**
 * Admin Dashboard
 * Overview of system statistics and quick actions
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'Admin Dashboard';

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total users by role
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$roleStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$stats['students'] = $roleStats['student'] ?? 0;
$stats['professors'] = $roleStats['professor'] ?? 0;
$stats['admins'] = $roleStats['admin'] ?? 0;
$stats['total_users'] = array_sum($roleStats);

// Total courses
$stmt = $pdo->query("SELECT COUNT(*) FROM courses");
$stats['courses'] = $stmt->fetchColumn();

// Total enrollments
$stmt = $pdo->query("SELECT COUNT(*) FROM enrollments");
$stats['enrollments'] = $stmt->fetchColumn();

// Recent users (last 5)
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll();

// Recent enrollments (last 5)
$stmt = $pdo->query("
    SELECT e.id, u.name as student_name, c.course_name, e.enrolled_at 
    FROM enrollments e 
    INNER JOIN users u ON e.student_id = u.id 
    INNER JOIN courses c ON e.course_id = c.id 
    ORDER BY e.enrolled_at DESC 
    LIMIT 5
");
$recentEnrollments = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .dashboard-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    
    /* Stats Row - 5 cards in a row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }
    
    @media (max-width: 1200px) {
        .stats-row {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .stat-icon svg {
        width: 22px;
        height: 22px;
        color: white;
    }
    
    .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    .stat-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stat-icon.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
    
    .stat-content {
        flex: 1;
        min-width: 0;
    }
    
    .stat-number {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.2rem;
    }
    
    /* Main Grid - 2 columns */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    
    @media (max-width: 1024px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .stat-card {
            padding: 0.75rem;
            gap: 0.5rem;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
        }

        .stat-icon svg {
            width: 14px;
            height: 14px;
        }

        .stat-number {
            font-size: 1rem;
        }

        .stat-label {
            font-size: 0.65rem;
        }

        .welcome-card {
            padding: 0.875rem 1rem;
        }

        .welcome-content h2 {
            font-size: 0.95rem;
        }

        .welcome-content p {
            font-size: 0.7rem;
        }

        .welcome-actions .btn {
            padding: 0.35rem 0.7rem;
            font-size: 0.65rem;
        }

        .dashboard-card .card-header {
            padding: 0.6rem 0.875rem;
        }

        .dashboard-card .card-header h3 {
            font-size: 0.75rem;
        }

        .dashboard-card .card-header a {
            font-size: 0.65rem;
        }

        .quick-actions-grid {
            gap: 0.5rem;
            padding: 0.75rem;
        }

        .action-btn {
            padding: 0.6rem 0.5rem;
            font-size: 0.65rem;
            gap: 0.35rem;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .list-item {
            padding: 0.5rem 0.875rem;
        }

        .list-item-name {
            font-size: 0.75rem;
        }

        .list-item-detail {
            font-size: 0.6rem;
        }

        .list-item-badge {
            font-size: 0.55rem;
            padding: 0.1rem 0.35rem;
        }
    }
    
    /* Cards */
    .dashboard-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .dashboard-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafafa;
    }
    
    .dashboard-card .card-header h3 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }
    
    .dashboard-card .card-header a {
        color: #f59e0b;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .dashboard-card .card-header a:hover {
        text-decoration: underline;
    }
    
    .dashboard-card .card-body {
        padding: 0;
    }
    
    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        padding: 1rem;
    }
    
    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 0.75rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        color: #374151;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.8rem;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);
        border-color: transparent;
    }
    
    .action-btn svg {
        width: 20px;
        height: 20px;
        opacity: 0.8;
    }
    
    .action-btn:hover svg {
        opacity: 1;
    }
    
    /* User/Enrollment List */
    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }
    
    .list-item:last-child {
        border-bottom: none;
    }
    
    .list-item:hover {
        background: #fafafa;
    }
    
    .list-item-info {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    
    .list-item-name {
        font-weight: 600;
        color: #1a1a2e;
        font-size: 0.85rem;
    }
    
    .list-item-detail {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 5px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    
    .role-badge.student { background: #ede9fe; color: #7c3aed; }
    .role-badge.professor { background: #d1fae5; color: #059669; }
    .role-badge.admin { background: #fef3c7; color: #d97706; }
    
    .empty-message {
        padding: 1.5rem;
        text-align: center;
        color: #9ca3af;
        font-size: 0.8rem;
    }
</style>

<div class="container">
    <?php if ($flash) : ?>
        <div class="alert alert-<?php echo escape($flash['type']); ?>">
            <?php echo escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">System overview and management</p>
    </div>

    <div class="dashboard-container">
        <!-- Statistics Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['students']; ?></div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['professors']; ?></div>
                    <div class="stat-label">Professors</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon amber">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['admins']; ?></div>
                    <div class="stat-label">Admins</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['courses']; ?></div>
                    <div class="stat-label">Courses</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pink">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo $stats['enrollments']; ?></div>
                    <div class="stat-label">Enrollments</div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="main-grid">
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="users.php" class="action-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Manage Users
                        </a>
                        <a href="courses.php" class="action-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            Manage Courses
                        </a>
                        <a href="enrollments.php" class="action-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            View Enrollments
                        </a>
                        <a href="professors.php" class="action-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                            Professor Assignments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Recent Users</h3>
                    <a href="users.php">View All →</a>
                </div>
                <div class="card-body">
                    <?php if (count($recentUsers) > 0) : ?>
                        <?php foreach ($recentUsers as $user) : ?>
                            <div class="list-item">
                                <div class="list-item-info">
                                    <span class="list-item-name"><?php echo escape($user['name']); ?></span>
                                    <span class="list-item-detail"><?php echo escape($user['email']); ?></span>
                                </div>
                                <span class="role-badge <?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="empty-message">No users yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Enrollments - Full Width -->
            <div class="dashboard-card" style="grid-column: 1 / -1;">
                <div class="card-header">
                    <h3>Recent Enrollments</h3>
                    <a href="enrollments.php">View All →</a>
                </div>
                <div class="card-body">
                    <?php if (count($recentEnrollments) > 0) : ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 0;">
                            <?php foreach ($recentEnrollments as $enrollment) : ?>
                                <div class="list-item">
                                    <div class="list-item-info">
                                        <span class="list-item-name"><?php echo escape($enrollment['student_name']); ?></span>
                                        <span class="list-item-detail"><?php echo escape($enrollment['course_name']); ?></span>
                                    </div>
                                    <span class="list-item-detail">
                                        <?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-message">No enrollments yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
