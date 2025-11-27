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

// Handle unenroll action
if (isset($_POST['unenroll']) && isset($_POST['enrollment_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $enrollmentId = (int)$_POST['enrollment_id'];
        
        // Verify this enrollment belongs to current user
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE id = ? AND student_id = ?");
        $stmt->execute([$enrollmentId, $studentId]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
            $stmt->execute([$enrollmentId]);
            setFlashMessage('success', 'Successfully unenrolled from course.');
        }
    }
    header('Location: enrollments_list.php');
    exit();
}

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
        <p class="page-subtitle">View and manage your course enrollments</p>
    </div>

    <div class="card">
        <?php if (count($enrollments) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $enrollment) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($enrollment['course_name']); ?></strong>
                            </td>
                            <td>
                                <form method="POST" action="" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to unenroll from this course?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                    <button type="submit" name="unenroll" class="btn" 
                                            style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #ff6b6b; color: white;">
                                        Unenroll
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
