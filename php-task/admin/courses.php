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
        margin: 1rem;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .modal-header h2 {
        font-size: 1.25rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .modal {
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem;
        }

        .modal-header {
            margin-bottom: 1rem;
        }

        .modal-header h2 {
            font-size: 1rem;
        }

        .modal-close {
            font-size: 1.25rem;
        }

        .modal .form-group {
            margin-bottom: 0.75rem;
        }

        .modal .form-group label {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .modal .form-control {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }

        .modal .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }
    }

    /* Expandable Row Styles */
    .expand-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.2rem;
        color: #6b7280;
        transition: transform 0.2s;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .expand-btn svg {
        width: 16px;
        height: 16px;
    }

    .expand-btn.expanded {
        transform: rotate(180deg);
    }

    .row-details {
        display: none;
    }

    .row-details.show {
        display: table-row;
    }

    .row-details td {
        padding: 0.5rem 0.75rem !important;
        background: #f9fafb;
        border-top: none !important;
    }

    .detail-content {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.35rem;
        flex-wrap: nowrap;
    }

    .detail-item svg {
        width: 14px;
        height: 14px;
        opacity: 0.7;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .detail-label {
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .name-cell {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .hide-mobile {
        display: table-cell;
    }
    
    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .action-btns .btn-icon {
        display: none;
    }

    .action-btns .btn-icon svg {
        width: 14px;
        height: 14px;
    }

    .action-btns .btn-text {
        display: inline;
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

    @media (max-width: 768px) {
        .table th {
            font-size: 0.85rem;
            padding: 0.85rem 0.75rem;
            font-weight: 600;
        }

        .table td {
            font-size: 0.95rem;
            padding: 1rem 0.75rem;
        }

        .table td strong {
            font-size: 1rem;
            font-weight: 600;
        }

        .hide-mobile {
            display: none;
        }

        .expand-btn {
            display: inline-flex;
            padding: 0.3rem;
        }

        .expand-btn svg {
            width: 20px;
            height: 20px;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:active {
            background: #f3f4f6;
        }

        .name-cell {
            gap: 0.6rem;
        }

        .detail-content {
            flex-direction: column;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .detail-item {
            gap: 0.5rem;
        }

        .detail-item svg {
            width: 18px;
            height: 18px;
        }

        .detail-label {
            font-size: 0.95rem;
        }

        .row-details td {
            padding: 1rem 1.25rem !important;
        }

        .action-btns {
            flex-direction: row;
            gap: 0.3rem;
        }

        .action-btns .btn-sm {
            padding: 0.5rem;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .action-btns .btn-text {
            display: none;
        }

        .action-btns .btn-icon {
            display: inline-flex;
        }

        .action-btns .btn-icon svg {
            width: 16px;
            height: 16px;
        }
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
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th class="hide-mobile">Students</th>
                        <th class="hide-mobile">Professors</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $index => $course) : ?>
                        <tr class="clickable-row" onclick="toggleDetails(<?php echo $index; ?>, event)">
                            <td>
                                <div class="name-cell">
                                    <button class="expand-btn" id="expand-<?php echo $index; ?>" type="button">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                    <strong><?php echo escape($course['course_name']); ?></strong>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <span class="badge badge-primary"><?php echo $course['student_count']; ?> enrolled</span>
                            </td>
                            <td class="hide-mobile">
                                <span class="badge badge-success"><?php echo $course['professor_count']; ?> assigned</span>
                            </td>
                            <td>
                                <div class="action-btns" onclick="event.stopPropagation()">
                                    <a href="courses.php?edit=<?php echo $course['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <span class="btn-icon"><svg viewBox="0 0 494.936 494.936" fill="currentColor"><path d="M389.844,182.85c-6.743,0-12.21,5.467-12.21,12.21v222.968c0,23.562-19.174,42.735-42.736,42.735H67.157c-23.562,0-42.736-19.174-42.736-42.735V150.285c0-23.562,19.174-42.735,42.736-42.735h267.741c6.743,0,12.21-5.467,12.21-12.21s-5.467-12.21-12.21-12.21H67.157C30.126,83.13,0,113.255,0,150.285v267.743c0,37.029,30.126,67.155,67.157,67.155h267.741c37.03,0,67.156-30.126,67.156-67.155V195.061C402.054,188.318,396.587,182.85,389.844,182.85z"/><path d="M483.876,20.791c-14.72-14.72-38.669-14.714-53.377,0L221.352,229.944c-0.28,0.28-3.434,3.559-4.251,5.396l-28.963,65.069c-2.057,4.619-1.056,10.027,2.521,13.6c2.337,2.336,5.461,3.576,8.639,3.576c1.675,0,3.362-0.346,4.96-1.057l65.07-28.963c1.83-0.815,5.114-3.97,5.396-4.25L483.876,74.169c7.131-7.131,11.06-16.61,11.06-26.692C494.936,37.396,491.007,27.915,483.876,20.791z M466.61,56.897L257.457,266.05c-0.035,0.036-0.055,0.078-0.089,0.107l-33.989,15.131L238.51,247.3c0.03-0.036,0.071-0.055,0.107-0.09L447.765,38.058c5.038-5.039,13.819-5.033,18.846,0.005c2.518,2.51,3.905,5.855,3.905,9.414C470.516,51.036,469.127,54.38,466.61,56.897z"/></svg></span>
                                        <span class="btn-text">Edit</span>
                                    </a>
                                    <a href="courses.php?delete=<?php echo $course['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this course? This will also remove all enrollments and professor assignments.');">
                                        <span class="btn-icon"><svg viewBox="0 0 482.428 482.429" fill="currentColor"><path d="M381.163,57.799h-75.094C302.323,25.316,274.686,0,241.214,0c-33.471,0-61.104,25.315-64.85,57.799h-75.098c-30.39,0-55.111,24.728-55.111,55.117v2.828c0,23.223,14.46,43.1,34.83,51.199v260.369c0,30.39,24.724,55.117,55.112,55.117h210.236c30.389,0,55.111-24.729,55.111-55.117V166.944c20.369-8.1,34.83-27.977,34.83-51.199v-2.828C436.274,82.527,411.551,57.799,381.163,57.799z M241.214,26.139c19.037,0,34.927,13.645,38.443,31.66h-76.879C206.293,39.783,222.184,26.139,241.214,26.139z M375.305,427.312c0,15.978-13,28.979-28.973,28.979H136.096c-15.973,0-28.973-13.002-28.973-28.979V170.861h268.182V427.312z M410.135,115.744c0,15.978-13,28.979-28.973,28.979H101.266c-15.973,0-28.973-13.001-28.973-28.979v-2.828c0-15.978,13-28.979,28.973-28.979h279.897c15.973,0,28.973,13.001,28.973,28.979V115.744z"/><path d="M171.144,422.863c7.218,0,13.069-5.853,13.069-13.068V262.641c0-7.216-5.852-13.07-13.069-13.07c-7.217,0-13.069,5.854-13.069,13.07v147.154C158.074,417.012,163.926,422.863,171.144,422.863z"/><path d="M241.214,422.863c7.218,0,13.07-5.853,13.07-13.068V262.641c0-7.216-5.854-13.07-13.07-13.07c-7.217,0-13.069,5.854-13.069,13.07v147.154C228.145,417.012,233.996,422.863,241.214,422.863z"/><path d="M311.284,422.863c7.217,0,13.068-5.853,13.068-13.068V262.641c0-7.216-5.852-13.07-13.068-13.07c-7.219,0-13.07,5.854-13.07,13.07v147.154C298.213,417.012,304.067,422.863,311.284,422.863z"/></svg></span>
                                        <span class="btn-text">Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Expandable details row -->
                        <tr class="row-details" id="details-<?php echo $index; ?>">
                            <td colspan="4">
                                <div class="detail-content">
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span class="detail-label">Students:</span>
                                        <span class="badge badge-primary"><?php echo $course['student_count']; ?> enrolled</span>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span class="detail-label">Professors:</span>
                                        <span class="badge badge-success"><?php echo $course['professor_count']; ?> assigned</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
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
function toggleDetails(index, event) {
    // Only toggle on mobile
    if (window.innerWidth > 768) return;
    
    // Don't toggle if clicking on a button/link
    if (event.target.closest('.btn') || event.target.closest('a')) return;
    
    const details = document.getElementById('details-' + index);
    const expandBtn = document.getElementById('expand-' + index);
    
    details.classList.toggle('show');
    expandBtn.classList.toggle('expanded');
}

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
