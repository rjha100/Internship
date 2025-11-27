<?php
/**
 * Admin - Manage Courses
 * List, add, edit, delete courses
 */
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'Manage Courses';

$pdo = getDBConnection();
$errors = [];

// Handle delete course
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    // Delete course (enrollments and professor_courses will cascade)
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$deleteId]);
    setFlashMessage('success', 'Course deleted successfully.');
    redirect('courses.php');
}

// Handle add/edit course form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = sanitize($_POST['course_name'] ?? '');
    $editId = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;
    
    // Validation
    if (empty($courseName)) {
        $errors[] = 'Course name is required.';
    }
    
    // Check if course name already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_name = ? AND id != ?");
        $stmt->execute([$courseName, $editId ?? 0]);
        if ($stmt->fetch()) {
            $errors[] = 'Course name already exists.';
        }
    }
    
    if (empty($errors)) {
        if ($editId) {
            // Update existing course
            $stmt = $pdo->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
            $stmt->execute([$courseName, $editId]);
            setFlashMessage('success', 'Course updated successfully.');
        } else {
            // Create new course
            $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
            $stmt->execute([$courseName]);
            setFlashMessage('success', 'Course created successfully.');
        }
        redirect('courses.php');
    }
}

// Get course for editing
$editCourse = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCourse = $stmt->fetch();
}

// Get all courses with stats
$stmt = $pdo->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as student_count,
           (SELECT COUNT(*) FROM professor_courses pc WHERE pc.course_id = c.id) as professor_count
    FROM courses c 
    ORDER BY c.course_name
");
$courses = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        width: 100%;
        max-width: 500px;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }
    
    .action-btns {
        display: flex;
        gap: 0.5rem;
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
    
    .course-stats {
        display: flex;
        gap: 1rem;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.85rem;
        color: #6b7280;
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
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title">Manage Courses</h1>
                <p class="page-subtitle"><?php echo count($courses); ?> course(s)</p>
            </div>
            <button class="btn btn-primary" onclick="openModal()">+ Add Course</button>
        </div>
    </div>

    <div class="card">
        <?php if (count($courses) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Students</th>
                        <th>Professors</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><strong><?php echo escape($course['course_name']); ?></strong></td>
                            <td>
                                <span class="badge badge-primary"><?php echo $course['student_count']; ?> enrolled</span>
                            </td>
                            <td>
                                <span class="badge badge-success"><?php echo $course['professor_count']; ?> assigned</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="courses.php?edit=<?php echo $course['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="courses.php?delete=<?php echo $course['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this course? This will also remove all enrollments and professor assignments.');">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="empty-state">
                <h3>No courses found</h3>
                <p>Click "Add Course" to create your first course.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Course Modal -->
<div class="modal-overlay <?php echo $editCourse ? 'active' : ''; ?>" id="courseModal">
    <div class="modal">
        <div class="modal-header">
            <h2><?php echo $editCourse ? 'Edit Course' : 'Add New Course'; ?></h2>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form method="POST" action="courses.php">
            <?php if ($editCourse) : ?>
                <input type="hidden" name="edit_id" value="<?php echo $editCourse['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="course_name">Course Name</label>
                <input type="text" id="course_name" name="course_name" class="form-control" required
                       value="<?php echo $editCourse ? escape($editCourse['course_name']) : ''; ?>"
                       placeholder="e.g., Introduction to Computer Science">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <?php echo $editCourse ? 'Update Course' : 'Create Course'; ?>
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('courseModal').classList.add('active');
}

function closeModal() {
    document.getElementById('courseModal').classList.remove('active');
    if (window.location.search.includes('edit=')) {
        window.location.href = 'courses.php';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

document.getElementById('courseModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include '../includes/footer.php'; ?>
