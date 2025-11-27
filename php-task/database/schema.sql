
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Users table (replaces students table, now supports multiple roles)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('student', 'professor', 'admin') DEFAULT 'student',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL
);

-- Professor-Course assignments (which professor teaches which course)
CREATE TABLE IF NOT EXISTS professor_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    professor_id INT NOT NULL,
    course_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_professor_course (professor_id, course_id)
);

-- Student enrollments
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_course (student_id, course_id)
);

-- =====================
-- Sample Data
-- =====================

-- Admin user (password: password)
INSERT INTO users (name, email, password, phone, role, created_at) VALUES
('System Admin', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'admin', NOW());

-- Professors (password: password)
INSERT INTO users (name, email, password, phone, role, created_at) VALUES
('Dr. Sarah Miller', 'sarah.miller@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1112223333', 'professor', NOW()),
('Prof. Michael Chen', 'michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '4445556666', 'professor', NOW());

-- Students (password: password)
INSERT INTO users (name, email, password, phone, role, created_at) VALUES
('John Doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'student', NOW()),
('Jane Smith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2345678901', 'student', NOW()),
('Bob Johnson', 'bob.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3456789012', 'student', NOW()),
('Alice Williams', 'alice.williams@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '4567890123', 'student', NOW()),
('Charlie Brown', 'charlie.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5678901234', 'student', NOW());

-- Courses
INSERT INTO courses (course_name) VALUES
('Introduction to Computer Science'),
('Web Development Fundamentals'),
('Database Management Systems');

-- Assign professors to courses
-- Dr. Sarah Miller teaches CS and Web Dev
INSERT INTO professor_courses (professor_id, course_id) VALUES
(2, 1),
(2, 2);
-- Prof. Michael Chen teaches Database
INSERT INTO professor_courses (professor_id, course_id) VALUES
(3, 3);

-- Student enrollments (student_id starts at 4)
INSERT INTO enrollments (student_id, course_id) VALUES
(4, 1),
(4, 2),
(5, 1),
(5, 3),
(6, 2),
(7, 1),
(7, 2),
(7, 3),
(8, 3);

-- =====================
-- Useful Queries
-- =====================

-- Get all students in a specific course
-- SELECT u.id, u.name, u.email, u.phone, u.created_at
-- FROM users u
-- INNER JOIN enrollments e ON u.id = e.student_id
-- WHERE e.course_id = 1 AND u.role = 'student';

-- Get all courses with student count
-- SELECT c.id, c.course_name, COUNT(e.student_id) AS student_count
-- FROM courses c
-- LEFT JOIN enrollments e ON c.id = e.course_id
-- GROUP BY c.id, c.course_name;

-- Get professor's assigned courses with student counts
-- SELECT c.id, c.course_name, COUNT(e.student_id) AS student_count
-- FROM courses c
-- INNER JOIN professor_courses pc ON c.id = pc.course_id
-- LEFT JOIN enrollments e ON c.id = e.course_id
-- WHERE pc.professor_id = 2
-- GROUP BY c.id, c.course_name;
