<?php
/**
 * Admin - Professor Course Assignments
 * Manage which professors are assigned to which courses
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'Professor Assignments';

$pdo = getDBConnection();
$errors = [];

// Handle remove assignment
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $assignmentId = (int)$_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM professor_courses WHERE id = ?");
    $stmt->execute([$assignmentId]);
    setFlashMessage('success', 'Assignment removed successfully.');
    redirect('professors.php');
}

// Handle add assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $professorId = isset($_POST['professor_id']) ? (int)$_POST['professor_id'] : 0;
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    if (!$professorId || !$courseId) {
        $errors[] = 'Please select both a professor and a course.';
    } else {
        // Check if assignment already exists
        $stmt = $pdo->prepare("SELECT id FROM professor_courses WHERE professor_id = ? AND course_id = ?");
        $stmt->execute([$professorId, $courseId]);
        if ($stmt->fetch()) {
            $errors[] = 'This professor is already assigned to this course.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO professor_courses (professor_id, course_id) VALUES (?, ?)");
            $stmt->execute([$professorId, $courseId]);
            setFlashMessage('success', 'Professor assigned to course successfully.');
            redirect('professors.php');
        }
    }
}

// Get all professors
$stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'professor' ORDER BY name");
$professors = $stmt->fetchAll();

// Get all courses
$stmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name");
$courses = $stmt->fetchAll();

// Get all assignments with details
$stmt = $pdo->query("
    SELECT pc.id, u.name as professor_name, u.email as professor_email, c.course_name
    FROM professor_courses pc
    INNER JOIN users u ON pc.professor_id = u.id
    INNER JOIN courses c ON pc.course_id = c.id
    ORDER BY u.name, c.course_name
");
$assignments = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .assignment-form {
        display: flex;
        gap: 1rem;
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    
    .assignment-form .form-group {
        flex: 1;
        min-width: 200px;
        margin-bottom: 0;
    }
    
    .assignment-form label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }
    
    .assignment-form select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 0.95rem;
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
        <h1 class="page-title">Professor Course Assignments</h1>
        <p class="page-subtitle">Manage which professors teach which courses</p>
    </div>

    <!-- Add Assignment Form -->
    <form method="POST" action="professors.php" class="assignment-form">
        <div class="form-group">
            <label for="professor_id">Professor</label>
            <select id="professor_id" name="professor_id" required>
                <option value="">Select a professor...</option>
                <?php foreach ($professors as $prof) : ?>
                    <option value="<?php echo $prof['id']; ?>">
                        <?php echo escape($prof['name']); ?> (<?php echo escape($prof['email']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="course_id">Course</label>
            <select id="course_id" name="course_id" required>
                <option value="">Select a course...</option>
                <?php foreach ($courses as $course) : ?>
                    <option value="<?php echo $course['id']; ?>">
                        <?php echo escape($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Assign Professor</button>
    </form>

    <div class="card">
        <div class="card-header">
            <h3>Current Assignments</h3>
        </div>
        <?php if (count($assignments) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment) : ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($assignment['professor_name']); ?></strong>
                                <br>
                                <small style="color: #6b7280;"><?php echo escape($assignment['professor_email']); ?></small>
                            </td>
                            <td><?php echo escape($assignment['course_name']); ?></td>
                            <td>
                                <a href="professors.php?remove=<?php echo $assignment['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to remove this assignment?');">
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
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                <h3>No assignments yet</h3>
                <p>Use the form above to assign professors to courses.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
