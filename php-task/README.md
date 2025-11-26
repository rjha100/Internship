# Student Management System

A PHP + MySQL web application for managing students, courses, and enrollments.

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)

### Database Setup

1. Start your MySQL server

2. Log into MySQL:
   ```bash
   mysql -u root -p
   ```

3. Create the database and tables by running the SQL file:
   ```bash
   source path/to/database/schema.sql
   ```
   
   Or copy and paste the contents of `database/schema.sql` into your MySQL client.

### Configuration

1. Open `config/database.php`

2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'student_management');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Add your password if set
   ```

### Running the Application

#### Using PHP Built-in Server
```bash
cd php-task
php -S localhost:8000
```
Then open `http://localhost:8000` in your browser.

## How Each Page Works

### Login Page (`login.php`)
- Students enter their email and password to log in
- Credentials are verified against the database using prepared statements
- Passwords are hashed using PHP's `password_hash()` function
- On successful login, user is redirected to the dashboard
- CSRF protection is implemented

### Registration Page (`register.php`)
- New students can create an account
- **Client-side validation:**
  - All fields are required
  - Email format validation
  - Real-time email uniqueness check via AJAX
  - Password confirmation match
  - Minimum password length (6 characters)
- **Server-side validation:**
  - All inputs are sanitized
  - Email uniqueness is verified in database
  - Password is securely hashed before storage
- After successful registration, user is automatically logged in

### Dashboard (`dashboard.php`)
- Welcome page shown after login
- Displays user's name and email
- Navigation to other sections

### Course Management (`courses.php`)
- View list of all courses
- Add new courses (to be implemented)

### Enrollment Page (`enrollment.php`)
- Enroll in courses (to be implemented)
- Students can only enroll themselves (not other students)

### Enrollment List (`enrollments_list.php`)
- View all enrollments (to be implemented)
- Shows Student Name, Email, and Course Name using JOIN

### Logout (`logout.php`)
- Destroys the user session
- Redirects to login page

## Security Features

1. **Prepared Statements (PDO)**: All database queries use prepared statements to prevent SQL injection

2. **Password Hashing**: Passwords are hashed using `password_hash()` with bcrypt

3. **Input Validation**: All form inputs are validated on both client and server side

4. **Output Escaping**: All output is escaped using `htmlspecialchars()` to prevent XSS

5. **CSRF Protection**: Forms include CSRF tokens to prevent cross-site request forgery

6. **Session Security**: Proper session management with secure session handling

## Sample Login Credentials

After running the SQL file, you can log in with these sample accounts:

| Email | Password |
|-------|----------|
| john.doe@email.com | password123 |
| jane.smith@email.com | password123 |
| bob.johnson@email.com | password123 |
| alice.williams@email.com | password123 |
| charlie.brown@email.com | password123 |

## Assumptions Made

1. **Single User Type**: The system only has student users (no admin roles)
2. **Self-Enrollment Only**: Students can only enroll themselves in courses
3. **No Email Verification**: Email addresses are not verified via email confirmation
4. **Basic Phone Validation**: Phone numbers are validated for format only (digits, spaces, dashes, parentheses)
5. **No Password Recovery**: Password reset functionality is not implemented
6. **Session-Based Auth**: Authentication uses PHP sessions (no JWT or other token-based auth)

## Future Enhancements (Bonus Tasks)

- [ ] AJAX search for students
- [ ] Pagination for student list
- [ ] Delete student functionality with confirmation
- [ ] Admin dashboard
- [ ] Course editing and deletion
- [ ] Enrollment date tracking
