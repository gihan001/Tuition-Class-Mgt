<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// Simple form handling for adding a student (procedural, easy to read)
if (isset($_POST['submit_btn'])) {
    // Get and trim input values
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $class_name = trim($_POST['class_name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if ($full_name !== '' && $email !== '' && $password !== '' && $class_name !== '' && $contact !== '') {
        // Escape values for safety (basic)
        $full_name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $class_name_esc = mysqli_real_escape_string($conn, $class_name);
        $contact_esc = mysqli_real_escape_string($conn, $contact);

        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_esc = mysqli_real_escape_string($conn, $password_hash);

        // Insert into users table first
        $role = 'student';
        $role_esc = mysqli_real_escape_string($conn, $role);
        $sql_user = "INSERT INTO users (full_name, email, password, role) VALUES ('{$full_name_esc}', '{$email_esc}', '{$password_hash_esc}', '{$role_esc}')";
        $res_user = mysqli_query($conn, $sql_user);

        if ($res_user) {
            // Get newly inserted user id
            $new_user_id = mysqli_insert_id($conn);

            // Insert into students table with the new user id
            $sql_student = "INSERT INTO students (user_id, class_name, contact) VALUES (" . (int)$new_user_id . ", '{$class_name_esc}', '{$contact_esc}')";
            $res_student = mysqli_query($conn, $sql_student);

            if ($res_student) {
                echo "<script>alert('Student Added Successfully!'); window.location='students.php';</script>";
                exit();
            } else {
                // If students insert fails, optionally delete the created user (simple rollback)
                mysqli_query($conn, "DELETE FROM users WHERE id=" . (int)$new_user_id);
                echo "<script>alert('Error adding student details. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Error creating user. Please try again.');</script>";
        }
    }
}

// Fetch students joined with users using a simple INNER JOIN
$res = mysqli_query($conn, "SELECT students.student_id, students.user_id, users.full_name, users.email, students.class_name, students.contact FROM students INNER JOIN users ON users.id = students.user_id ORDER BY students.id ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>

<body>

    <div class="dashboard-container">

        <aside class="sidebar">
            <h2>Smart Tuition</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php" class="active">Students</a></li>
                <li><a href="teachers.php">Teachers</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Students</h1>
                <p>View and manage student accounts.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Student List</h2>
                    <button class="primary-button" onclick="openModal()">Add New Student</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5">No students found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars('STU-' . str_pad((string)$student['user_id'], 3, '0', STR_PAD_LEFT)); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                                    <td>
                                        <button class="action-btn edit-btn">Edit</button>
                                        <form action="students.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                                            <input type="hidden" name="action" value="delete_student">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$student['user_id']; ?>">
                                            <button type="submit" class="action-btn delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>

                    <h2>Add New Student</h2>

                    <form action="students.php" method="POST">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" required>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="input-group">
                            <label>Password</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="input-group">
                            <label>Class Name</label>
                            <input type="text" name="class_name" placeholder="e.g. Mathematics - Grade 10" required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" required>
                        </div>
                        <button type="submit" name="submit_btn" class="primary-button">Add Student</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>

</html>