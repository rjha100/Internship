<?php
/**
 * Professor - View Students in Course
 * Shows list of students enrolled in a specific course
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require professor role
requireProfessor();

$pageTitle = 'Course Students';

$pdo = getDBConnection();
$userId = getCurrentUserId();

// Validate course_id parameter
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    setFlashMessage('error', 'Invalid course selected.');
    redirect('my-courses.php');
}

$courseId = (int)$_GET['course_id'];

// Verify that this professor is assigned to this course
$stmt = $pdo->prepare("
    SELECT c.id, c.course_name 
    FROM courses c 
    INNER JOIN professor_courses pc ON c.id = pc.course_id 
    WHERE c.id = ? AND pc.professor_id = ?
");
$stmt->execute([$courseId, $userId]);
$course = $stmt->fetch();

if (!$course) {
    setFlashMessage('error', 'You are not authorized to view students for this course.');
    redirect('my-courses.php');
}

// Get students enrolled in this course
$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.email, u.phone, e.enrolled_at as enrolled_date
    FROM users u 
    INNER JOIN enrollments e ON u.id = e.student_id 
    WHERE e.course_id = ? AND u.role = 'student'
    ORDER BY u.name
");
$stmt->execute([$courseId]);
$students = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<div class="container">
    <?php if ($flash) : ?>
        <div class="alert alert-<?php echo escape($flash['type']); ?>">
            <?php echo escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title">Students in <?php echo escape($course['course_name']); ?></h1>
                <p class="page-subtitle"><?php echo count($students); ?> student(s) enrolled</p>
            </div>
            <a href="my-courses.php" class="btn btn-secondary">← Back to My Courses</a>
        </div>
    </div>

    <div class="card">
        <?php if (count($students) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Enrolled Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($student['name']); ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?php echo escape($student['email']); ?>" style="color: #10b981; text-decoration: none;">
                                    <?php echo escape($student['email']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($student['phone']) : ?>
                                    <a href="tel:<?php echo escape($student['phone']); ?>" style="color: #10b981; text-decoration: none;">
                                        <?php echo escape($student['phone']); ?>
                                    </a>
                                <?php else : ?>
                                    <span style="color: #9ca3af;">Not provided</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($student['enrolled_date'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <h3>No students enrolled</h3>
                <p>No students have enrolled in this course yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
