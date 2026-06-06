<?php
session_start();

// Admin කෙනෙක් දැයි පරීක්ෂා කිරීමේ ආරක්ෂක පියවර
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// Errors බ්‍රවුසරයේ පෙන්වීමට On කිරීම
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------------------
// 1. ශිෂ්‍යයෙක් මකා දැමීමේ කොටස (DELETE STUDENT BACKEND)
// ----------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'delete_student') {
    $user_id_del = (int)$_POST['user_id'];

    // Schema එකේ ON DELETE CASCADE නිසා, Users වගුවෙන් මැකූ සැනින් Students වගුවෙන්ද මැකී යයි
    $sql_del = "DELETE FROM users WHERE id = $user_id_del";
    
    if (mysqli_query($conn, $sql_del)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Student Deleted Successfully!'];
        header("Location: students.php");
        exit();
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting student. Please try again.'];
        header("Location: students.php");
        exit();
    }
}

// ----------------------------------------------------
// 2. ශිෂ්‍යයෙක් ඇතුළත් කිරීමේ කොටස (ADD STUDENT BACKEND)
// ----------------------------------------------------
if (isset($_POST['submit_btn'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $grade = trim($_POST['grade'] ?? '');      
    $contact = trim($_POST['contact'] ?? '');

    // subject ඉවත් කර පරීක්ෂා කිරීම
    if ($full_name !== '' && $email !== '' && $password !== '' && $grade !== '' && $contact !== '') {
        
        $full_name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $grade_esc = mysqli_real_escape_string($conn, $grade);
        $contact_esc = mysqli_real_escape_string($conn, $contact);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_esc = mysqli_real_escape_string($conn, $password_hash);

        $role = 'student';
        $role_esc = mysqli_real_escape_string($conn, $role);
        
        $sql_user = "INSERT INTO users (full_name, email, password, role) VALUES ('{$full_name_esc}', '{$email_esc}', '{$password_hash_esc}', '{$role_esc}')";
        $res_user = mysqli_query($conn, $sql_user);

        if ($res_user) {
            $new_user_id = mysqli_insert_id($conn);

            // subject රහිතව ශිෂ්‍යයා ඇතුළත් කිරීමේ SQL එක
            $sql_student = "INSERT INTO students (user_id, grade, contact) VALUES (" . (int)$new_user_id . ", '{$grade_esc}', '{$contact_esc}')";
            $res_student = mysqli_query($conn, $sql_student);

            if ($res_student) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Student Added Successfully!'];
                header("Location: students.php");
                exit();
            } else {
                mysqli_query($conn, "DELETE FROM users WHERE id=" . (int)$new_user_id);
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error adding student details.'];
                header("Location: students.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error: Email might already exist!'];
            header("Location: students.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all required fields.'];
        header("Location: students.php");
        exit();
    }
}

// ----------------------------------------------------
// 3. ශිෂ්‍ය විස්තර යාවත්කාලීන කිරීමේ කොටස (UPDATE STUDENT BACKEND)
// ----------------------------------------------------
if (isset($_POST['update_btn'])) {
    $user_id = (int)$_POST['user_id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if ($user_id > 0 && $full_name !== '' && $email !== '' && $grade !== '' && $contact !== '') {
        
        $full_name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $grade_esc = mysqli_real_escape_string($conn, $grade);
        $contact_esc = mysqli_real_escape_string($conn, $contact);

        $sql_update_user = "UPDATE users SET full_name = '{$full_name_esc}', email = '{$email_esc}' WHERE id = $user_id";
        $sql_update_student = "UPDATE students SET grade = '{$grade_esc}', contact = '{$contact_esc}' WHERE user_id = $user_id";

        if (mysqli_query($conn, $sql_update_user) && mysqli_query($conn, $sql_update_student)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Student Updated Successfully!'];
            header("Location: students.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating student details.'];
            header("Location: students.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all fields for update.'];
        header("Location: students.php");
        exit();
    }
}

// ----------------------------------------------------
// 4. සිසුන් ලැයිස්තුව ලබා ගැනීම (FETCH STUDENTS)
// ----------------------------------------------------
$res = mysqli_query($conn, "SELECT students.student_id, students.user_id, users.full_name, users.email, students.grade, students.contact FROM students INNER JOIN users ON users.id = students.user_id ORDER BY students.student_id ASC");
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

    <?php if (isset($_SESSION['toast'])): ?>
        <div class="toast-notification <?php echo $_SESSION['toast']['type']; ?>" id="toastBox">
            <?php echo $_SESSION['toast']['message']; ?>
        </div>
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>

    <div class="dashboard-container">

        <aside class="sidebar">
            <h2>Smart Tuition</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php" class="active">Students</a></li>
                <li><a href="teachers.php">Teachers</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="notices.php">Notices</a></li>
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
                    
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search students by name, grade..." class="search-bar">
                    
                    <button class="primary-button" onclick="openModal()">Add New Student</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Grade</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res) === 0): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($student = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td>STU-<?php echo str_pad((string)$student['student_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($student['contact']); ?></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="openEditModal('<?php echo $student['user_id']; ?>', '<?php echo htmlspecialchars($student['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($student['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($student['grade'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($student['contact'], ENT_QUOTES); ?>')">
                                            Edit
                                        </button>
                                        
                                        <form action="students.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                                            <input type="hidden" name="action" value="delete_student">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$student['user_id']; ?>">
                                            <button type="submit" class="action-btn delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
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
                            <input type="password" name="password" placeholder="Create a password for student" required>
                        </div>
                        <div class="input-group">
                            <label>Grade/Level</label>
                            <input type="text" name="grade" placeholder="e.g. Grade 10" required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" required>
                        </div>
                        <button type="submit" name="submit_btn" class="primary-button">Add Student</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Student Details</h2>
                    <form action="students.php" method="POST">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" required>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="email" id="edit_email" required>
                        </div>
                        <div class="input-group">
                            <label>Grade/Level</label>
                            <input type="text" name="grade" id="edit_grade" required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" id="edit_contact" required>
                        </div>
                        <button type="submit" name="update_btn" class="primary-button submit-btn">Update Details</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            var toast = document.getElementById('toastBox');
            if (toast) {
                setTimeout(function() {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(function() { toast.remove(); }, 500); 
                }, 3000); 
            }
        });

        // openEditModal ශ්‍රිතය යාවත්කාලීන කරන ලදි
        function openEditModal(id, name, email, grade, contact) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_full_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_grade').value = grade;
            document.getElementById('edit_contact').value = contact;
            
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        window.addEventListener("click", function(event) {
            let editModal = document.getElementById('editModal');
            if (event.target === editModal) {
                editModal.classList.remove("active");
            }
        });
    </script>
</body>
</html>