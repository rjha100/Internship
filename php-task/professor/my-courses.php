<?php
/**
 * Professor - My Courses Page
 * Shows list of courses assigned to the professor
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require professor role
requireProfessor();

$pageTitle = 'My Courses';

$pdo = getDBConnection();
$userId = getCurrentUserId();

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
        <h1 class="page-title">My Courses</h1>
        <p class="page-subtitle">Courses you are teaching</p>
    </div>

    <div class="card">
        <?php if (count($courses) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Enrolled Students</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($course['course_name']); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <?php echo $course['student_count']; ?> students
                                </span>
                            </td>
                            <td>
                                <a href="students.php?course_id=<?php echo $course['id']; ?>" 
                                   class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                    View Students
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3>No courses assigned</h3>
                <p>You haven't been assigned to any courses yet. Please contact an administrator.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
