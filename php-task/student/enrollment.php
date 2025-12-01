<?php
/**
 * Course Enrollment Page for Students
 * Allows students to enroll in a course
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require student role
requireStudent();

$pageTitle = 'Enroll in Course';
$errors = [];
$success = '';

$pdo = getDBConnection();

// Get current student info
$studentId = getCurrentUserId();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

// Get all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY course_name");
$courses = $stmt->fetchAll();

// Get already enrolled course IDs
$stmt = $pdo->prepare("SELECT course_id FROM enrollments WHERE student_id = ?");
$stmt->execute([$studentId]);
$enrolledCourseIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Pre-select course if passed via URL
$selectedCourseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    }

    $courseId = (int)($_POST['course_id'] ?? 0);

    if (empty($courseId)) {
        $errors[] = 'Please select a course.';
    }

    // Check if course exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, course_name FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();

        if (!$course) {
            $errors[] = 'Selected course does not exist.';
        }
    }

    // Check if already enrolled
    if (empty($errors)) {
        if (in_array($courseId, $enrolledCourseIds)) {
            $errors[] = 'You are already enrolled in this course.';
        }
    }

    // Process enrollment
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
            $stmt->execute([$studentId, $courseId]);

            setFlashMessage('success', 'Successfully enrolled in ' . escape($course['course_name']) . '!');
            header('Location: enrollments_list.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = 'Error processing enrollment. Please try again.';
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Course Enrollment</h1>
        <p class="page-subtitle">Select a course to enroll in</p>
    </div>

    <div class="grid" style="max-width: 600px; margin: 0 auto;">
        <div class="card">
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error) : ?>
                        <p style="margin: 0.25rem 0;"><?php echo escape($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="student_info">Student</label>
                    <input type="text" id="student_info" class="form-control"
                           value="<?php echo escape($student['name'] . ' (' . $student['email'] . ')'); ?>" 
                           disabled style="background: #f5f5f5; color: #666;">
                </div>

                <div class="form-group">
                    <label for="course_id">Select Course</label>
                    <select name="course_id" id="course_id" class="form-control" required>
                        <option value="">-- Choose a course --</option>
                        <?php foreach ($courses as $course) : ?>
                            <?php $isEnrolled = in_array($course['id'], $enrolledCourseIds); ?>
                            <option value="<?php echo $course['id']; ?>"
                                <?php echo $selectedCourseId == $course['id'] ? 'selected' : ''; ?>
                                <?php echo $isEnrolled ? 'disabled' : ''; ?>>
                                <?php echo escape($course['course_name']); ?>
                                <?php echo $isEnrolled ? ' (Already Enrolled)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Enroll Now
                </button>
            </form>

            <div style="text-align: center; margin-top: 1rem;">
                <a href="courses.php" style="color: #667eea; text-decoration: none;">
                    ← Back to Courses
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
