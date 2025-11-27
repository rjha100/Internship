<?php
/**
 * Registration Page
 * Handles new student registration
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

        // If no validation errors, check if email exists and create account
        if (empty($errors)) {
            try {
                $pdo = getDBConnection();
                
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors['email'] = 'This email is already registered.';
                } else {
                    // Create new student account
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO students 
                                         (name, email, password, phone, created_at) 
                                         VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$name, $email, $hashedPassword, $phone]);
                    
                    // Get the new user's ID and log them in
                    $userId = $pdo->lastInsertId();
                    setUserSession($userId, $name, $email);
                    setFlashMessage('success', 'Registration successful! Welcome to the Student Management System.');
                    header('Location: dashboard.php');
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .auth-container {
            max-width: 450px;
            width: 100%;
            padding: 1rem;
        }

        .auth-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 2.5rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .auth-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .form-control.error {
            border-color: #dc2626;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .btn {
            display: inline-block;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }

        .auth-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .required {
            color: #dc2626;
        }

        .email-status {
            font-size: 0.85rem;
            margin-top: 0.35rem;
        }

        .email-status.checking {
            color: #7f8c8d;
        }

        .email-status.available {
            color: #059669;
        }

        .email-status.taken {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    width="64" height="64" viewBox="0 0 24 24" fill="none" 
                    stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Register as a new student</p>

            <?php if (isset($errors[0])) : ?>
                <div class="alert alert-error">
                    <?php echo escape($errors[0]); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
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
                    <label for="email">Email Address <span class="required">*</span></label>
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

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" 
                           class="form-control <?php echo isset($errors['password']) ? 'error' : ''; ?>" 
                           placeholder="Create a password (min. 6 characters)" required>
                    <?php if (isset($errors['password'])) : ?>
                        <div class="error-message"><?php echo escape($errors['password']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control <?php echo isset($errors['confirm_password']) ? 'error' : ''; ?>" 
                           placeholder="Confirm your password" required>
                    <?php if (isset($errors['confirm_password'])) : ?>
                        <div class="error-message"><?php echo escape($errors['confirm_password']); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
            </form>

            <div class="auth-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>

    <script>
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
                        emailStatus.textContent = '✓ Email is available';
                        emailStatus.className = 'email-status available';
                        isEmailAvailable = true;
                    } else {
                        emailStatus.textContent = '✗ Email is already registered';
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
        });
    </script>
</body>
</html>
