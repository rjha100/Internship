<?php
/**
 * Professor Dashboard Page
 * Main page after login - shows professor overview and statistics
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require professor role
requireProfessor();

$pageTitle = 'Dashboard';

$pdo = getDBConnection();
$userId = getCurrentUserId();

// Get professor's assigned courses count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM professor_courses WHERE professor_id = ?");
$stmt->execute([$userId]);
$myCourses = $stmt->fetch()['total'];

// Get total students in professor's courses
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT e.student_id) as total 
    FROM enrollments e 
    INNER JOIN professor_courses pc ON e.course_id = pc.course_id 
    WHERE pc.professor_id = ?
");
$stmt->execute([$userId]);
$totalStudents = $stmt->fetch()['total'];

// Get professor's courses with student counts
$stmt = $pdo->prepare("
    SELECT c.id, c.course_name, 
           (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as student_count
    FROM courses c 
    INNER JOIN professor_courses pc ON c.id = pc.course_id 
    WHERE pc.professor_id = ?
    ORDER BY c.course_name
");
$stmt->execute([$userId]);
$courses = $stmt->fetchAll();

// Get user details
$stmt = $pdo->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userDetails = $stmt->fetch();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon svg {
        width: 20px;
        height: 20px;
        color: white;
    }

    .stat-icon.purple { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }

    .stat-content h3 {
        font-size: 1.35rem;
        color: #1a1a2e;
        font-weight: 700;
        margin-bottom: 0.1rem;
    }

    .stat-content p {
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .welcome-card {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .welcome-content h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .welcome-content p {
        opacity: 0.9;
        font-size: 0.85rem;
    }

    .welcome-actions {
        display: flex;
        gap: 0.75rem;
    }

    .welcome-actions .btn {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .welcome-actions .btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 900px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1fr;
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

        .stat-content h3 {
            font-size: 1rem;
        }

        .stat-content p {
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

        .section-title {
            font-size: 0.85rem;
            margin-bottom: 0.875rem;
            padding-bottom: 0.5rem;
        }

        .course-list li {
            padding: 0.6rem 0;
            gap: 0.6rem;
        }

        .course-list .course-icon {
            width: 30px;
            height: 30px;
        }

        .course-list .course-icon svg {
            width: 14px;
            height: 14px;
        }

        .course-list .course-name {
            font-size: 0.8rem;
        }

        .profile-item {
            padding: 0.5rem;
        }

        .profile-item .icon-wrapper {
            width: 28px;
            height: 28px;
        }

        .profile-item svg {
            width: 14px;
            height: 14px;
        }

        .profile-item .info span {
            font-size: 0.75rem;
        }

        .profile-item .info small {
            font-size: 0.65rem;
        }

        .view-all-link {
            padding: 0.6rem;
            font-size: 0.75rem;
        }

        .student-count {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            white-space: nowrap;
        }
    }

    .section-title {
        font-size: 1rem;
        color: #1a1a2e;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #10b981;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .course-list {
        list-style: none;
    }

    .course-list li {
        padding: 0.875rem 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.875rem;
        transition: all 0.2s ease;
    }

    .course-list li:last-child {
        border-bottom: none;
    }

    .course-list li:hover {
        padding-left: 0.5rem;
    }

    .course-info {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .course-list .course-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .course-list .course-icon svg {
        width: 20px;
        height: 20px;
        color: #10b981;
    }

    .course-list .course-name {
        color: #1a1a2e;
        font-weight: 500;
    }

    .student-count {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1));
        color: #059669;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .profile-item {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .profile-item:hover {
        background-color: #f9fafb;
    }

    .profile-item .icon-wrapper {
        width: 36px;
        height: 36px;
        background-color: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-item svg {
        width: 18px;
        height: 18px;
        color: #6b7280;
    }

    .profile-item .info span {
        color: #1a1a2e;
        font-weight: 500;
        display: block;
    }

    .profile-item .info small {
        color: #9ca3af;
        font-size: 0.8rem;
    }

    .view-students-link {
        color: #10b981;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .view-students-link:hover {
        text-decoration: underline;
    }
</style>

<div class="container">
    <?php if ($flash) : ?>
        <div class="alert alert-<?php echo escape($flash['type']); ?>">
            <?php echo escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Welcome Banner -->
    <div class="welcome-card">
        <div class="welcome-content">
            <h2>Welcome, Professor <?php echo escape(getCurrentUserName()); ?>!</h2>
            <p>Manage your courses and view your students' progress.</p>
        </div>
        <div class="welcome-actions">
            <a href="my-courses.php" class="btn">My Courses</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3><?php echo $myCourses; ?></h3>
                <p>My Courses</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Total Students</p>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- My Courses -->
        <div class="card">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                My Assigned Courses
            </h3>
            
            <?php if (count($courses) > 0) : ?>
                <ul class="course-list">
                    <?php foreach ($courses as $course) : ?>
                        <li>
                            <div class="course-info">
                                <div class="course-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="course-name"><?php echo escape($course['course_name']); ?></span>
                                    <a href="students.php?course_id=<?php echo $course['id']; ?>" class="view-students-link">
                                        View Students →
                                    </a>
                                </div>
                            </div>
                            <span class="student-count"><?php echo $course['student_count']; ?> students</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <h3>No courses assigned</h3>
                    <p>You haven't been assigned to any courses yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Profile Info -->
        <div class="card">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                My Profile
            </h3>
            
            <div class="profile-info">
                <div class="profile-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="info">
                        <span><?php echo escape($userDetails['name']); ?></span>
                        <small>Full Name</small>
                    </div>
                </div>

                <div class="profile-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="info">
                        <span><?php echo escape($userDetails['email']); ?></span>
                        <small>Email Address</small>
                    </div>
                </div>

                <div class="profile-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div class="info">
                        <span><?php echo escape($userDetails['phone']); ?></span>
                        <small>Phone Number</small>
                    </div>
                </div>

                <div class="profile-item">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="info">
                        <span><?php echo date('M d, Y', strtotime($userDetails['created_at'])); ?></span>
                        <small>Member Since</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
