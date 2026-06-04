# Smart Tuition Class Management System

Smart Tuition is a PHP and MySQL based class management system for students, teachers, and admins. The project uses a simple frontend with separate dashboard areas for each role.

## Project Structure

- `frontend/index.php` - public home page
- `frontend/login.php` - user login
- `frontend/register.php` - user registration
- `frontend/about.php` - about page
- `frontend/contact.php` - contact page
- `frontend/admin/` - admin dashboard pages
- `frontend/student/` - student dashboard pages
- `frontend/teacher/` - teacher dashboard pages
- `frontend/assets/` - CSS, JavaScript, and images
- `db.php` - database connection settings

## Requirements

- PHP 8+ or compatible PHP version
- MySQL / MariaDB
- XAMPP or another local PHP stack

## Local Setup

1. Copy the project folder into your web server root.
2. Start Apache and MySQL from XAMPP.
3. Create a database named `smart_tuition`.
4. Import your tables into the database.
5. Update `db.php` if your MySQL host, port, username, or password are different.

## Run The Project

Open this URL in your browser:

http://localhost/smart_tuition/frontend/index.php

To log in, open:

http://localhost/smart_tuition/frontend/login.php

## Notes

- Admin users are redirected to `frontend/admin/dashboard.php` after successful login.
- Teacher users are redirected to `frontend/teacher/dashboard.php`.
- Student users are redirected to `frontend/student/dashboard.php`.

## Development Notes

- Frontend documentation is available in `documentation/Frontend Development Documentation.md`.
- The current database connection in `db.php` uses `127.0.0.1:3307` with the `smart_tuition` database.