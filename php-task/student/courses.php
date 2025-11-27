<?php
/**
 * Course Browsing Page for Students
 * Shows list of all available courses
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require student role
requireStudent();

$pageTitle = 'Courses';

// Get all courses, ordered by enrollment count (most enrolled first)
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT c.*, 
    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrolled_count 
    FROM courses c ORDER BY enrolled_count DESC, c.course_name ASC");
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
        <h1 class="page-title">Available Courses</h1>
        <p class="page-subtitle">Browse all courses available for enrollment</p>
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
                                <span class="badge badge-primary">
                                    <?php echo $course['enrolled_count']; ?> students
                                </span>
                            </td>
                            <td>
                                <a href="enrollment.php?course_id=<?php echo $course['id']; ?>" 
                                   class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                    Enroll
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
                <h3>No courses available</h3>
                <p>There are no courses in the system yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
