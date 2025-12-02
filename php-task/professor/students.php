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

<style>
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
    }
</style>

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
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hide-mobile">Email</th>
                        <th class="hide-mobile">Phone</th>
                        <th class="hide-mobile">Enrolled Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student) : ?>
                        <tr class="clickable-row" onclick="toggleDetails(<?php echo $index; ?>, event)">
                            <td>
                                <div class="name-cell">
                                    <button class="expand-btn" id="expand-<?php echo $index; ?>" type="button">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                    <strong><?php echo escape($student['name']); ?></strong>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <a href="mailto:<?php echo escape($student['email']); ?>" style="color: #10b981; text-decoration: none;">
                                    <?php echo escape($student['email']); ?>
                                </a>
                            </td>
                            <td class="hide-mobile">
                                <?php if ($student['phone']) : ?>
                                    <a href="tel:<?php echo escape($student['phone']); ?>" style="color: #10b981; text-decoration: none;">
                                        <?php echo escape($student['phone']); ?>
                                    </a>
                                <?php else : ?>
                                    <span style="color: #9ca3af;">Not provided</span>
                                <?php endif; ?>
                            </td>
                            <td class="hide-mobile">
                                <?php echo date('M j, Y', strtotime($student['enrolled_date'])); ?>
                            </td>
                        </tr>
                        <!-- Expandable details row -->
                        <tr class="row-details" id="details-<?php echo $index; ?>">
                            <td colspan="4">
                                <div class="detail-content">
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="detail-label">Email:</span>
                                        <a href="mailto:<?php echo escape($student['email']); ?>" style="color: #10b981; text-decoration: none;">
                                            <?php echo escape($student['email']); ?>
                                        </a>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                        </svg>
                                        <span class="detail-label">Phone:</span>
                                        <?php if ($student['phone']) : ?>
                                            <a href="tel:<?php echo escape($student['phone']); ?>" style="color: #10b981; text-decoration: none;">
                                                <?php echo escape($student['phone']); ?>
                                            </a>
                                        <?php else : ?>
                                            <span style="color: #9ca3af;">Not provided</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span class="detail-label">Enrolled:</span>
                                        <?php echo date('M j, Y', strtotime($student['enrolled_date'])); ?>
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

<script>
function toggleDetails(index, event) {
    // Only toggle on mobile
    if (window.innerWidth > 768) return;
    
    // Don't toggle if clicking on a link
    if (event.target.closest('a')) return;
    
    const details = document.getElementById('details-' + index);
    const expandBtn = document.getElementById('expand-' + index);
    
    details.classList.toggle('show');
    expandBtn.classList.toggle('expanded');
}
</script>

<?php include '../includes/footer.php'; ?>
