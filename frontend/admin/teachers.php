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
// 1. ගුරුවරයෙක් මකා දැමීමේ කොටස (DELETE TEACHER BACKEND)
// ----------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'delete_teacher') {
    $user_id_del = (int)$_POST['user_id'];

    // Schema එකේ ON DELETE CASCADE නිසා, Users වගුවෙන් මැකූ සැනින් Teachers වගුවෙන්ද මැකී යයි
    $sql_del = "DELETE FROM users WHERE id = $user_id_del";
    
    if (mysqli_query($conn, $sql_del)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Teacher Deleted Successfully!'];
        header("Location: teachers.php");
        exit();
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting teacher. Please try again.'];
        header("Location: teachers.php");
        exit();
    }
}

// ----------------------------------------------------
// 2. ගුරුවරයෙක් ඇතුළත් කිරීමේ කොටස (ADD TEACHER BACKEND)
// ----------------------------------------------------
if (isset($_POST['submit_btn'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $subjects = trim($_POST['subjects'] ?? ''); 
    $contact = trim($_POST['contact'] ?? ''); // අලුතින් එකතු කළ කොටස

    // සියලුම කොටු පරීක්ෂා කිරීම
    if ($full_name !== '' && $email !== '' && $password !== '' && $subjects !== '' && $contact !== '') {
        
        $full_name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $subjects_esc = mysqli_real_escape_string($conn, $subjects);
        $contact_esc = mysqli_real_escape_string($conn, $contact);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_esc = mysqli_real_escape_string($conn, $password_hash);

        $role = 'teacher';
        $role_esc = mysqli_real_escape_string($conn, $role);
        
        // Step A: Users වගුවට ඇතුළත් කිරීම
        $sql_user = "INSERT INTO users (full_name, email, password, role) VALUES ('{$full_name_esc}', '{$email_esc}', '{$password_hash_esc}', '{$role_esc}')";
        $res_user = mysqli_query($conn, $sql_user);

        if ($res_user) {
            $new_user_id = mysqli_insert_id($conn);

            // Step B: Teachers වගුවට contact අංකයත් සමඟ ඇතුළත් කිරීම
            $sql_teacher = "INSERT INTO teachers (user_id, name, subject, contact) VALUES (" . (int)$new_user_id . ", '{$full_name_esc}', '{$subjects_esc}', '{$contact_esc}')";
            $res_teacher = mysqli_query($conn, $sql_teacher);

            if ($res_teacher) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Teacher Added Successfully!'];
                header("Location: teachers.php");
                exit();
            } else {
                mysqli_query($conn, "DELETE FROM users WHERE id=" . (int)$new_user_id);
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error adding teacher details.'];
                header("Location: teachers.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error: Email might already exist!'];
            header("Location: teachers.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all required fields.'];
        header("Location: teachers.php");
        exit();
    }
}

// ----------------------------------------------------
// 3. ගුරුවරයෙකුගේ විස්තර යාවත්කාලීන කිරීම (UPDATE TEACHER BACKEND)
// ----------------------------------------------------
if (isset($_POST['update_btn'])) {
    $user_id = (int)$_POST['user_id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subjects = trim($_POST['subjects'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if ($user_id > 0 && $full_name !== '' && $email !== '' && $subjects !== '' && $contact !== '') {
        
        $full_name_esc = mysqli_real_escape_string($conn, $full_name);
        $email_esc = mysqli_real_escape_string($conn, $email);
        $subjects_esc = mysqli_real_escape_string($conn, $subjects);
        $contact_esc = mysqli_real_escape_string($conn, $contact);

        $sql_update_user = "UPDATE users SET full_name = '{$full_name_esc}', email = '{$email_esc}' WHERE id = $user_id";
        $sql_update_teacher = "UPDATE teachers SET name = '{$full_name_esc}', subject = '{$subjects_esc}', contact = '{$contact_esc}' WHERE user_id = $user_id";

        if (mysqli_query($conn, $sql_update_user) && mysqli_query($conn, $sql_update_teacher)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Teacher Updated Successfully!'];
            header("Location: teachers.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating teacher details.'];
            header("Location: teachers.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all fields for update.'];
        header("Location: teachers.php");
        exit();
    }
}

// ----------------------------------------------------
// 4. ගුරුවරුන් ලැයිස්තුව ලබා ගැනීම (FETCH TEACHERS)
// ----------------------------------------------------
$res = mysqli_query($conn, "SELECT teachers.teacher_id, teachers.user_id, users.full_name, users.email, teachers.subject, teachers.contact FROM teachers INNER JOIN users ON users.id = teachers.user_id ORDER BY teachers.teacher_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Smart Tuition</title>
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
                <li><a href="students.php">Students</a></li>
                <li><a href="teachers.php" class="active">Teachers</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Teachers</h1>
                <p>View and manage teacher accounts.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Teacher List</h2>
                    
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search teachers by name, subject, contact..." class="search-bar">
                    
                    <button class="primary-button" onclick="openModal()">Add New Teacher</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Teacher ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subjects</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res) === 0): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No teachers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($teacher = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td>TEA-<?php echo str_pad((string)$teacher['teacher_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['contact']); ?></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="openEditModal('<?php echo $teacher['user_id']; ?>', '<?php echo htmlspecialchars($teacher['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($teacher['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($teacher['subject'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($teacher['contact'], ENT_QUOTES); ?>')">
                                            Edit
                                        </button>
                                        
                                        <form action="teachers.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this teacher?');">
                                            <input type="hidden" name="action" value="delete_teacher">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$teacher['user_id']; ?>">
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
                    <h2>Add New Teacher</h2>
                    <form action="teachers.php" method="POST">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" placeholder="Enter full name" required>
                        </div>
                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter email" required>
                        </div>
                        <div class="input-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Create password for teacher" required>
                        </div>
                        <div class="input-group">
                            <label>Subjects</label>
                            <input type="text" name="subjects" placeholder="e.g. Mathematics, Physics" required>
                        </div>
                        <div class="input-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact" placeholder="Enter contact number" required>
                        </div>
                        <button type="submit" name="submit_btn" class="primary-button submit-btn">Save Teacher</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Teacher Details</h2>
                    <form action="teachers.php" method="POST">
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
                            <label>Subjects</label>
                            <input type="text" name="subjects" id="edit_subjects" required>
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
        // Toast Notification එක තත්පර 3කින් ස්වයංක්‍රීයව සැඟවීම
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

        // Edit Modal එක විවෘත කිරීම සහ දත්ත කොටු තුළට ලිවීම
        function openEditModal(id, name, email, subjects, contact) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_full_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_subjects').value = subjects;
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