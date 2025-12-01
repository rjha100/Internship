<?php
/**
 * Enrollments List Page for Students
 * Displays current student's enrollments
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require student role
requireStudent();

$pageTitle = 'My Enrollments';

$pdo = getDBConnection();
$studentId = getCurrentUserId();

// Get all enrollments for current student with course details
$stmt = $pdo->prepare("
    SELECT e.id as enrollment_id, c.id as course_id, c.course_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ?
    ORDER BY e.id DESC
");
$stmt->execute([$studentId]);
$enrollments = $stmt->fetchAll();

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
        <h1 class="page-title">My Enrollments</h1>
        <p class="page-subtitle">View your course enrollments</p>
    </div>

    <div class="card">
        <?php if (count($enrollments) > 0) : ?>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($enrollment['course_name']); ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
                <h3>No enrollments yet</h3>
                <p>You haven't enrolled in any courses yet.</p>
                <a href="courses.php" class="btn btn-primary" style="margin-top: 1rem;">Browse Courses</a>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="courses.php" class="btn btn-primary">
            Enroll in More Courses
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
