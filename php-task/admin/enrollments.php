<?php

/**
 * Admin - View All Enrollments
 * List and manage all student enrollments
 */

require_once '../config/session.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$pageTitle = 'All Enrollments';

$pdo = getDBConnection();

// Handle unenroll
if (isset($_GET['unenroll']) && is_numeric($_GET['unenroll'])) {
    $enrollmentId = (int)$_GET['unenroll'];
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
    $stmt->execute([$enrollmentId]);
    setFlashMessage('success', 'Enrollment removed successfully.');
    redirect('enrollments.php');
}

// Get filter parameters
$courseFilter = isset($_GET['course']) && is_numeric($_GET['course']) ? (int)$_GET['course'] : null;

// Get all courses for filter dropdown
$stmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name");
$courses = $stmt->fetchAll();

// Pagination settings
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Build count query for pagination
$countSql = "
    SELECT COUNT(*) as total
    FROM enrollments e 
    INNER JOIN users u ON e.student_id = u.id 
    INNER JOIN courses c ON e.course_id = c.id 
    WHERE u.role = 'student'
";

if ($courseFilter) {
    $countSql .= " AND c.id = ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$courseFilter]);
} else {
    $countStmt = $pdo->query($countSql);
}
$totalEnrollments = $countStmt->fetch()['total'];
$totalPages = ceil($totalEnrollments / $perPage);

// Build query with optional filter and pagination
$sql = "
    SELECT e.id, e.enrolled_at, 
           u.id as user_id, u.name as student_name, u.email as student_email,
           c.id as course_id, c.course_name
    FROM enrollments e 
    INNER JOIN users u ON e.student_id = u.id 
    INNER JOIN courses c ON e.course_id = c.id 
    WHERE u.role = 'student'
";

if ($courseFilter) {
    $sql .= " AND c.id = ?";
}

$sql .= " ORDER BY e.enrolled_at DESC LIMIT $perPage OFFSET $offset";

if ($courseFilter) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$courseFilter]);
} else {
    $stmt = $pdo->query($sql);
}
$enrollments = $stmt->fetchAll();

include '../includes/header.php';
$flash = getFlashMessage();
?>

<style>
    .filter-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-bar select {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: white;
        font-size: 0.9rem;
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
        .filter-bar {
            gap: 0.5rem;
        }

        .filter-bar label {
            font-size: 0.9rem;
        }

        .filter-bar select {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

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

        .btn-sm {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
            border-radius: 8px;
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
            font-size: 0.9rem;
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

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        text-decoration: none;
        color: #374151;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .pagination .active {
        background: #f59e0b;
        color: white;
        border-color: #f59e0b;
    }

    .pagination .disabled {
        color: #9ca3af;
        pointer-events: none;
    }

    .pagination-info {
        text-align: center;
        color: #6b7280;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .pagination a,
        .pagination span {
            padding: 0.35rem 0.5rem;
            font-size: 0.7rem;
        }

        .pagination-info {
            font-size: 0.7rem;
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
        <h1 class="page-title">All Enrollments</h1>
        <p class="page-subtitle"><?php echo $totalEnrollments; ?> enrollment(s) found</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <label>Filter by course:</label>
        <select onchange="filterByCourse(this.value)">
            <option value="">All Courses</option>
            <?php foreach ($courses as $course) : ?>
                <option value="<?php echo $course['id']; ?>" 
                        <?php echo $courseFilter === $course['id'] ? 'selected' : ''; ?>>
                    <?php echo escape($course['course_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($courseFilter) : ?>
            <a href="enrollments.php" class="btn btn-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (count($enrollments) > 0) : ?>
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th class="hide-mobile">Course</th>
                        <th class="hide-mobile">Enrolled Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $index => $enrollment) : ?>
                        <tr class="clickable-row" onclick="toggleDetails(<?php echo $index; ?>, event)">
                            <td>
                                <div class="name-cell">
                                    <button class="expand-btn" id="expand-<?php echo $index; ?>" type="button">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                    <strong><?php echo escape($enrollment['student_name']); ?></strong>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                <a href="enrollments.php?course=<?php echo $enrollment['course_id']; ?>" 
                                   style="color: #f59e0b; text-decoration: none;">
                                    <?php echo escape($enrollment['course_name']); ?>
                                </a>
                            </td>
                            <td class="hide-mobile">
                                <?php echo date('M j, Y \a\t g:i A', strtotime($enrollment['enrolled_at'])); ?>
                            </td>
                            <td>
                                <div onclick="event.stopPropagation()">
                                    <a href="enrollments.php?unenroll=<?php echo $enrollment['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to remove this enrollment?');">
                                        Remove
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
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="detail-label">Email:</span>
                                        <?php echo escape($enrollment['student_email']); ?>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                        <span class="detail-label">Course:</span>
                                        <?php echo escape($enrollment['course_name']); ?>
                                    </div>
                                    <div class="detail-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span class="detail-label">Enrolled:</span>
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($enrollment['enrolled_at'])); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            
            <?php if ($totalPages > 1) : ?>
            <div class="pagination">
                <?php
                $filterParam = $courseFilter ? "&course=$courseFilter" : "";
                if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $filterParam; ?>" class="page-btn">&laquo; Prev</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?><?php echo $filterParam; ?>" 
                       class="page-btn <?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $filterParam; ?>" class="page-btn">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <h3>No enrollments found</h3>
                <p><?php echo $courseFilter ? 'No students enrolled in this course.' : 'No student enrollments yet.'; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDetails(index, event) {
    // Only work on mobile
    if (window.innerWidth > 768) return;
    
    // Don't toggle if clicking on buttons
    if (event.target.closest('.btn')) return;
    
    const detailsRow = document.getElementById('details-' + index);
    const expandBtn = document.getElementById('expand-' + index);
    
    detailsRow.classList.toggle('show');
    expandBtn.classList.toggle('expanded');
}

function filterByCourse(courseId) {
    if (courseId) {
        window.location.href = 'enrollments.php?course=' + courseId;
    } else {
        window.location.href = 'enrollments.php';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
