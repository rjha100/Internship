<?php
/**
 * Admin - View All Enrollments
 * List and manage all student enrollments
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'All Enrollments';

$pdo = getDBConnection();

// Handle unenroll
if (isset($_GET['unenroll']) && is_numeric($_GET['unenroll'])) {
    $enrollmentId = (int)$_GET['unenroll'];
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
    $stmt->execute([$enrollmentId]);
    setFlashMessage('success', 'Enrollment removed successfully.');
    redirect('enrollments.php');
}

// Get filter parameters
$courseFilter = isset($_GET['course']) && is_numeric($_GET['course']) ? (int)$_GET['course'] : null;

// Get all courses for filter dropdown
$stmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name");
$courses = $stmt->fetchAll();

// Build query with optional filter
$sql = "
    SELECT e.id, e.enrolled_at, 
           u.id as user_id, u.name as student_name, u.email as student_email,
           c.id as course_id, c.course_name
    FROM enrollments e 
    INNER JOIN users u ON e.student_id = u.id 
    INNER JOIN courses c ON e.course_id = c.id 
    WHERE u.role = 'student'
";

if ($courseFilter) {
    $sql .= " AND c.id = ?";
}

$sql .= " ORDER BY e.enrolled_at DESC";

if ($courseFilter) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$courseFilter]);
} else {
    $stmt = $pdo->query($sql);
}
$enrollments = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .filter-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-bar select {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        font-size: 0.9rem;
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

    <div class="page-header">
        <h1 class="page-title">All Enrollments</h1>
        <p class="page-subtitle"><?php echo count($enrollments); ?> enrollment(s) found</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <label>Filter by course:</label>
        <select onchange="filterByCourse(this.value)">
            <option value="">All Courses</option>
            <?php foreach ($courses as $course) : ?>
                <option value="<?php echo $course['id']; ?>" 
                        <?php echo $courseFilter === $course['id'] ? 'selected' : ''; ?>>
                    <?php echo escape($course['course_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($courseFilter) : ?>
            <a href="enrollments.php" class="btn btn-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (count($enrollments) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Enrolled Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($enrollment['student_name']); ?></strong>
                                <br>
                                <small style="color: #6b7280;"><?php echo escape($enrollment['student_email']); ?></small>
                            </td>
                            <td>
                                <a href="enrollments.php?course=<?php echo $enrollment['course_id']; ?>" 
                                   style="color: #f59e0b; text-decoration: none;">
                                    <?php echo escape($enrollment['course_name']); ?>
                                </a>
                            </td>
                            <td><?php echo date('M j, Y \a\t g:i A', strtotime($enrollment['enrolled_at'])); ?></td>
                            <td>
                                <a href="enrollments.php?unenroll=<?php echo $enrollment['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to remove this enrollment?');">
                                    Remove
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <h3>No enrollments found</h3>
                <p><?php echo $courseFilter ? 'No students enrolled in this course.' : 'No student enrollments yet.'; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterByCourse(courseId) {
    if (courseId) {
        window.location.href = 'enrollments.php?course=' + courseId;
    } else {
        window.location.href = 'enrollments.php';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
