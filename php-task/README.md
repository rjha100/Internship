# Student Management System

A PHP + MySQL web application for managing students, courses, and enrollments with multi-role support (Student, Professor, Admin).

## Features

- **Multi-Role Authentication**: Student, Professor, and Admin roles with role-specific dashboards
- **Course Management**: Full CRUD operations for courses (Admin)
- **Enrollment System**: Students can browse and enroll in courses
- **Professor Assignments**: Admins can assign professors to courses
- **User Management**: Admin can manage all users with role filtering
- **Mobile Responsive**: Fully responsive design with expandable table rows for mobile
- **Modern UI**: Catppuccin-inspired purple gradient theme with Inter font

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

## User Roles

### Student
- View available courses
- Enroll/unenroll from courses
- View personal enrollment history

### Professor
- View assigned courses
- View enrolled students in their courses

### Admin
- Manage all users (create, edit, delete)
- Manage courses (create, edit, delete)
- Assign professors to courses
- View and manage all enrollments

## Page Structure

### Public Pages
- **Login** (`login.php`) - Authentication with email/password
- **Register** (`register.php`) - New student registration with real-time email validation

### Student Pages (`student/`)
- **Dashboard** - Welcome page with quick stats
- **Courses** - Browse and enroll in available courses
- **My Enrollments** - View enrolled courses

### Professor Pages (`professor/`)
- **Dashboard** - Overview of assigned courses
- **My Courses** - List of courses they teach
- **Students** - View students enrolled in their courses

### Admin Pages (`admin/`)
- **Dashboard** - System overview with statistics
- **Users** - User management with pagination and role filtering
- **Courses** - Course CRUD operations
- **Professors** - Professor-to-course assignments
- **Enrollments** - View/manage all enrollments with course filtering

## Mobile Responsive Features

- **Hamburger Navigation**: Collapsible menu on mobile devices
- **Expandable Table Rows**: Tap to expand hidden column data on mobile
- **Responsive Forms**: Full-width inputs and buttons on small screens
- **Touch-Friendly**: Larger tap targets and buttons for mobile interaction


## Technology Stack

- **Backend**: PHP 7.4+ with PDO for database access
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Styling**: Custom CSS
- **Font**: Inter

## Project Structure

```
php-task/
├── admin/              # Admin panel pages
│   ├── dashboard.php
│   ├── users.php
│   ├── courses.php
│   ├── professors.php
│   └── enrollments.php
├── professor/          # Professor pages
│   ├── dashboard.php
│   ├── my-courses.php
│   └── students.php
├── student/            # Student pages
│   ├── dashboard.php
│   ├── courses.php
│   ├── enrollment.php
│   └── enrollments_list.php
├── config/             # Configuration files
│   ├── database.php
│   └── session.php
├── includes/           # Shared components
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── database/           # Database schema
│   └── schema.sql
├── assets/             # Static assets
├── index.php           # Entry point (redirects to login)
├── login.php
├── logout.php
├── register.php
└── check_email.php     # AJAX email validation
```
