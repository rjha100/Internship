<?php
/**
 * Registration Page
 * Handles new student and professor registration
 */
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$name = '';
$email = '';
$phone = '';
$role = 'student';
$selectedCourse = '';

// Get available courses for professor registration
$pdo = getDBConnection();
$coursesStmt = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name");
$courses = $coursesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        // Sanitize inputs
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = sanitizeInput($_POST['role'] ?? 'student');
        $selectedCourse = (int)($_POST['course_id'] ?? 0);

        // Validate role
        if (!in_array($role, ['student', 'professor'])) {
            $role = 'student';
        }

        // Validate inputs
        if (empty($name)) {
            $errors['name'] = 'Name is required.';
        } elseif (strlen($name) < 2 || strlen($name) > 100) {
            $errors['name'] = 'Name must be between 2 and 100 characters.';
        }

        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        } elseif (!isValidEmail($email)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (empty($phone)) {
            $errors['phone'] = 'Phone number is required.';
        } elseif (!isValidPhone($phone)) {
            $errors['phone'] = 'Please enter a valid phone number (at least 10 digits).';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        // Professor must select a course
        if ($role === 'professor' && empty($selectedCourse)) {
            $errors['course'] = 'Please select a course to teach.';
        }

        // If no validation errors, check if email exists and create account
        if (empty($errors)) {
            try {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors['email'] = 'This email is already registered.';
                } else {
                    // Create new user account
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users 
                                         (name, email, password, phone, role, created_at) 
                                         VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$name, $email, $hashedPassword, $phone, $role]);

                    // Get the new user's ID
                    $userId = $pdo->lastInsertId();

                    // If professor, assign them to the selected course
                    if ($role === 'professor' && $selectedCourse > 0) {
                        $stmt = $pdo->prepare("INSERT INTO professor_courses (professor_id, course_id) VALUES (?, ?)");
                        $stmt->execute([$userId, $selectedCourse]);
                    }

                    // Log them in
                    setUserSession($userId, $name, $email, $role);
                    setFlashMessage('success', 'Registration successful! Welcome to the Student Management System.');
                    
                    // Redirect based on role
                    if ($role === 'professor') {
                        header('Location: professor/dashboard.php');
                    } else {
                        header('Location: student/dashboard.php');
                    }
                    exit();
                }
            } catch (PDOException $e) {
                $errors[] = 'An error occurred. Please try again later.';
            }
        }
    }
}

$pageTitle = 'Register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo escape($pageTitle); ?> - Student Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-container {
            max-width: 420px;
            width: 100%;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            margin-bottom: 0.75rem;
            backdrop-filter: blur(10px);
        }

        .brand-logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .brand-name {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .brand-tagline {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }

        .auth-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 2.5rem;
        }

        .auth-title {
            text-align: center;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .auth-subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 1.75rem;
            font-size: 0.95rem;
        }

        .auth-tabs {
            display: flex;
            background-color: #f3f4f6;
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 1.75rem;
        }

        .auth-tab {
            flex: 1;
            padding: 0.75rem 1rem;
            text-align: center;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .auth-tab:hover {
            color: #1a1a2e;
        }

        .auth-tab.active {
            background-color: white;
            color: #1a1a2e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #1a1a2e;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control.error {
            border-color: #dc2626;
            background-color: #fef2f2;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.95rem 1.5rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.3s ease;
            margin-top: 0.75rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .required {
            color: #dc2626;
        }

        .email-status {
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .email-status.checking {
            color: #6b7280;
        }

        .email-status.available {
            color: #16a34a;
        }

        .email-status.taken {
            color: #dc2626;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .role-selector {
            display: flex;
            background-color: #f3f4f6;
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 1.25rem;
            gap: 5px;
        }

        .role-option {
            flex: 1;
            text-align: center;
        }

        .role-option input {
            display: none;
        }

        .role-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .role-option label img {
            width: 24px;
            height: 24px;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .role-option input:checked + label {
            background-color: white;
            color: #1a1a2e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .role-option input:checked + label img {
            opacity: 1;
        }

        .role-option label:hover {
            color: #1a1a2e;
        }

        .role-option label:hover img {
            opacity: 0.8;
        }

        .course-select-group {
            display: none;
            margin-bottom: 1.25rem;
        }

        .course-select-group.visible {
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="brand-header">
            <div class="brand-logo">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                >
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <h2 class="brand-name">Student Management</h2>
            <p class="brand-tagline">Manage your courses and enrollments</p>
        </div>
        <div class="auth-card">
            <h1 class="auth-title">Create an account</h1>
            <p class="auth-subtitle">Join us today! Enter your details to get started.</p>

            <div class="auth-tabs">
                <a href="register.php" class="auth-tab active">Sign up</a>
                <a href="login.php" class="auth-tab">Log in</a>
            </div>

            <?php if (isset($errors[0])) : ?>
                <div class="alert alert-error">
                    <?php echo escape($errors[0]); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Role Selection -->
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" id="role_student" name="role" value="student" 
                               <?php echo $role === 'student' ? 'checked' : ''; ?>>
                        <label for="role_student">
                            <img src="assets/student.svg" alt="Student">
                            Student
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="role_professor" name="role" value="professor"
                               <?php echo $role === 'professor' ? 'checked' : ''; ?>>
                        <label for="role_professor">
                            <img src="assets/professor.svg" alt="Professor">
                            Professor
                        </label>
                    </div>
                </div>

                <!-- Course Selection (for professors) -->
                <div class="course-select-group <?php echo $role === 'professor' ? 'visible' : ''; ?>" id="courseSelectGroup">
                    <label for="course_id">Select Course to Teach <span class="required">*</span></label>
                    <select name="course_id" id="course_id" class="form-control <?php echo isset($errors['course']) ? 'error' : ''; ?>">
                        <option value="">-- Choose a course --</option>
                        <?php foreach ($courses as $course) : ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo $selectedCourse == $course['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['course'])) : ?>
                        <div class="error-message"><?php echo escape($errors['course']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" 
                           class="form-control <?php echo isset($errors['name']) ? 'error' : ''; ?>" 
                           value="<?php echo escape($name); ?>" 
                           placeholder="Enter your full name" required>
                    <?php if (isset($errors['name'])) : ?>
                        <div class="error-message"><?php echo escape($errors['name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" 
                           class="form-control <?php echo isset($errors['email']) ? 'error' : ''; ?>" 
                           value="<?php echo escape($email); ?>" 
                           placeholder="Enter your email" required>
                    <div id="emailStatus" class="email-status"></div>
                    <?php if (isset($errors['email'])) : ?>
                        <div class="error-message"><?php echo escape($errors['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" 
                           class="form-control <?php echo isset($errors['phone']) ? 'error' : ''; ?>" 
                           value="<?php echo escape($phone); ?>" 
                           placeholder="Enter your phone number" required>
                    <?php if (isset($errors['phone'])) : ?>
                        <div class="error-message"><?php echo escape($errors['phone']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" 
                               class="form-control <?php echo isset($errors['password']) ? 'error' : ''; ?>" 
                               placeholder="••••••••" required>
                        <?php if (isset($errors['password'])) : ?>
                            <div class="error-message"><?php echo escape($errors['password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm <span class="required">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-control <?php echo isset($errors['confirm_password']) ? 'error' : ''; ?>" 
                               placeholder="••••••••" required>
                        <?php if (isset($errors['confirm_password'])) : ?>
                            <div class="error-message"><?php echo escape($errors['confirm_password']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
            </form>
        </div>
    </div>

    <script>
        // Role selection - show/hide course dropdown
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const courseSelectGroup = document.getElementById('courseSelectGroup');
        const courseSelect = document.getElementById('course_id');

        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'professor') {
                    courseSelectGroup.classList.add('visible');
                    courseSelect.required = true;
                } else {
                    courseSelectGroup.classList.remove('visible');
                    courseSelect.required = false;
                    courseSelect.value = '';
                }
            });
        });

        // Initialize course visibility on page load
        const checkedRole = document.querySelector('input[name="role"]:checked');
        if (checkedRole && checkedRole.value === 'professor') {
            courseSelectGroup.classList.add('visible');
            courseSelect.required = true;
        }

        // Client-side email validation (checks if email is unique)
        const emailInput = document.getElementById('email');
        const emailStatus = document.getElementById('emailStatus');
        const submitBtn = document.getElementById('submitBtn');
        let emailTimeout;
        let isEmailAvailable = false;

        emailInput.addEventListener('input', function() {
            clearTimeout(emailTimeout);
            const email = this.value.trim();
            
            if (email.length === 0) {
                emailStatus.textContent = '';
                emailStatus.className = 'email-status';
                return;
            }

            // Basic email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailStatus.textContent = 'Please enter a valid email address';
                emailStatus.className = 'email-status taken';
                isEmailAvailable = false;
                return;
            }

            emailStatus.textContent = 'Checking availability...';
            emailStatus.className = 'email-status checking';

            // Debounce the AJAX request
            emailTimeout = setTimeout(function() {
                checkEmailAvailability(email);
            }, 500);
        });

        function checkEmailAvailability(email) {
            fetch('check_email.php?email=' + encodeURIComponent(email))
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        emailStatus.textContent = 'Email is available';
                        emailStatus.className = 'email-status available';
                        isEmailAvailable = true;
                    } else {
                        emailStatus.textContent =  '* Email is already registered';
                        emailStatus.className = 'email-status taken';
                        isEmailAvailable = false;
                    }
                })
                .catch(error => {
                    emailStatus.textContent = '';
                    emailStatus.className = 'email-status';
                    isEmailAvailable = true; // Allow form submission, server will validate
                });
        }

        // Form validation before submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }

            if (!isEmailAvailable && emailStatus.classList.contains('taken')) {
                e.preventDefault();
                alert('This email is already registered. Please use a different email.');
                return;
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }

            // Check if professor selected a course
            const selectedRole = document.querySelector('input[name="role"]:checked').value;
            if (selectedRole === 'professor' && !courseSelect.value) {
                e.preventDefault();
                alert('Please select a course to teach.');
                return;
            }
        });
    </script>
</body>
</html>
