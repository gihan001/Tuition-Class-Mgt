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
// 1. පන්තියක් මකා දැමීමේ කොටස (DELETE CLASS BACKEND)
// ----------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'delete_class') {
    $class_id_del = (int)$_POST['class_id'];

    $sql_del = "DELETE FROM classes WHERE class_id = $class_id_del";
    
    if (mysqli_query($conn, $sql_del)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Deleted Successfully!'];
        header("Location: classes.php");
        exit();
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting class.'];
        header("Location: classes.php");
        exit();
    }
}

// ----------------------------------------------------
// 2. අලුත් පන්තියක් ඇතුළත් කිරීමේ කොටස (ADD CLASS BACKEND)
// ----------------------------------------------------
if (isset($_POST['submit_btn'])) {
    $subject = trim($_POST['subject'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    $class_day = trim($_POST['class_day'] ?? '');
    $class_time = trim($_POST['class_time'] ?? '');
    // අලුතින් එකතු කළ Class Fee විචල්‍යය
    $class_fee = (float)($_POST['class_fee'] ?? 0.00); 

    if ($subject !== '' && $grade !== '' && $teacher_id > 0 && $class_day !== '' && $class_time !== '' && $class_fee >= 0) {
        
        $subject_esc = mysqli_real_escape_string($conn, $subject);
        $grade_esc = mysqli_real_escape_string($conn, $grade);
        $class_day_esc = mysqli_real_escape_string($conn, $class_day);
        $class_time_esc = mysqli_real_escape_string($conn, $class_time);

        // class_fee දත්ත සමුදායට ඇතුළත් කිරීම
        $sql_class = "INSERT INTO classes (subject, grade, teacher_id, class_day, class_time, class_fee) VALUES ('{$subject_esc}', '{$grade_esc}', $teacher_id, '{$class_day_esc}', '{$class_time_esc}', $class_fee)";
        
        if (mysqli_query($conn, $sql_class)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Created Successfully!'];
            header("Location: classes.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error creating class.'];
            header("Location: classes.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all required fields correctly.'];
        header("Location: classes.php");
        exit();
    }
}

// ----------------------------------------------------
// 3. පන්ති විස්තර යාවත්කාලීන කිරීමේ කොටස (UPDATE CLASS BACKEND)
// ----------------------------------------------------
if (isset($_POST['update_btn'])) {
    $class_id = (int)$_POST['class_id'];
    $subject = trim($_POST['subject'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $teacher_id = (int)($_POST['teacher_id'] ?? 0);
    $class_day = trim($_POST['class_day'] ?? '');
    $class_time = trim($_POST['class_time'] ?? '');
    // අලුතින් එකතු කළ Class Fee විචල්‍යය
    $class_fee = (float)($_POST['class_fee'] ?? 0.00); 

    if ($class_id > 0 && $subject !== '' && $grade !== '' && $teacher_id > 0 && $class_day !== '' && $class_time !== '' && $class_fee >= 0) {
        
        $subject_esc = mysqli_real_escape_string($conn, $subject);
        $grade_esc = mysqli_real_escape_string($conn, $grade);
        $class_day_esc = mysqli_real_escape_string($conn, $class_day);
        $class_time_esc = mysqli_real_escape_string($conn, $class_time);

        // class_fee දත්ත සමුදායේ යාවත්කාලීන කිරීම
        $sql_update = "UPDATE classes SET subject = '{$subject_esc}', grade = '{$grade_esc}', teacher_id = $teacher_id, class_day = '{$class_day_esc}', class_time = '{$class_time_esc}', class_fee = $class_fee WHERE class_id = $class_id";
        
        if (mysqli_query($conn, $sql_update)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Updated Successfully!'];
            header("Location: classes.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating class details.'];
            header("Location: classes.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all fields for update.'];
        header("Location: classes.php");
        exit();
    }
}

// ----------------------------------------------------
// 4. පන්ති ලැයිස්තුව ලබා ගැනීම (FETCH CLASSES)
// ----------------------------------------------------
// SELECT විධානයට classes.class_fee ද එකතු කර ඇත
$sql_fetch = "SELECT classes.class_id, classes.subject, classes.grade, classes.class_day, classes.class_time, classes.class_fee, classes.teacher_id, teachers.name AS teacher_name 
              FROM classes 
              INNER JOIN teachers ON teachers.teacher_id = classes.teacher_id 
              ORDER BY classes.class_id ASC";
$res_classes = mysqli_query($conn, $sql_fetch);

$teachers_list = [];
$res_teachers = mysqli_query($conn, "SELECT teacher_id, name FROM teachers ORDER BY name ASC");
while ($t_row = mysqli_fetch_assoc($res_teachers)) {
    $teachers_list[] = $t_row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - Smart Tuition</title>
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
                <li><a href="teachers.php">Teachers</a></li>
                <li><a href="classes.php" class="active">Classes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Classes</h1>
                <p>Create and manage tuition classes.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Class List</h2>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search classes by subject, grade, teacher..." class="search-bar">
                    <button class="primary-button" onclick="openModal()">Add New Class</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Teacher</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Fee (Rs.)</th> <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_classes) === 0): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;">No classes found. Please add a class.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($class = mysqli_fetch_assoc($res_classes)): ?>
                                <tr>
                                    <td>CLS-<?php echo str_pad((string)$class['class_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($class['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($class['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_day']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_time']); ?></td>
                                    <td><?php echo number_format($class['class_fee'], 2); ?></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="openEditModal('<?php echo $class['class_id']; ?>', '<?php echo htmlspecialchars($class['subject'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($class['grade'], ENT_QUOTES); ?>', '<?php echo $class['teacher_id']; ?>', '<?php echo htmlspecialchars($class['class_day'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($class['class_time'], ENT_QUOTES); ?>', '<?php echo $class['class_fee']; ?>')">
                                            Edit
                                        </button>

                                        <form action="classes.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this class?');">
                                            <input type="hidden" name="action" value="delete_class">
                                            <input type="hidden" name="class_id" value="<?php echo (int)$class['class_id']; ?>">
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
                    <h2>Add New Class</h2>
                    <form action="classes.php" method="POST">
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="e.g. Mathematics" required>
                        </div>
                        <div class="input-group">
                            <label>Grade/Level</label>
                            <input type="text" name="grade" placeholder="e.g. Grade 10" required>
                        </div>
                        <div class="input-group">
                            <label>Assign Teacher</label>
                            <select name="teacher_id" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers_list as $teacher): ?>
                                    <option value="<?php echo $teacher['teacher_id']; ?>">
                                        <?php echo htmlspecialchars($teacher['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Class Day</label>
                            <select name="class_day" required>
                                <option value="">-- Select Day --</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Class Time</label>
                            <input type="text" name="class_time" placeholder="e.g. 04:00 PM - 06:00 PM" required>
                        </div>
                        <div class="input-group">
                            <label>Class Fee (Rs.)</label>
                            <input type="number" step="0.01" name="class_fee" placeholder="e.g. 2500.00" required>
                        </div>

                        <button type="submit" name="submit_btn" class="primary-button submit-btn">Save Class</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Class Details</h2>
                    <form action="classes.php" method="POST">
                        <input type="hidden" name="class_id" id="edit_class_id">
                        
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject" id="edit_subject" required>
                        </div>
                        <div class="input-group">
                            <label>Grade/Level</label>
                            <input type="text" name="grade" id="edit_grade" required>
                        </div>
                        <div class="input-group">
                            <label>Assign Teacher</label>
                            <select name="teacher_id" id="edit_teacher_id" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers_list as $teacher): ?>
                                    <option value="<?php echo $teacher['teacher_id']; ?>">
                                        <?php echo htmlspecialchars($teacher['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Class Day</label>
                            <select name="class_day" id="edit_class_day" required>
                                <option value="">-- Select Day --</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Class Time</label>
                            <input type="text" name="class_time" id="edit_class_time" required>
                        </div>
                        <div class="input-group">
                            <label>Class Fee (Rs.)</label>
                            <input type="number" step="0.01" name="class_fee" id="edit_class_fee" required>
                        </div>

                        <button type="submit" name="update_btn" class="primary-button submit-btn">Update Class</button>
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

        // fee දත්තය ලබා ගැනීමට යාවත්කාලීන කළ JS ශ්‍රිතය
        function openEditModal(id, subject, grade, teacher_id, day, time, fee) {
            document.getElementById('edit_class_id').value = id;
            document.getElementById('edit_subject').value = subject;
            document.getElementById('edit_grade').value = grade;
            document.getElementById('edit_teacher_id').value = teacher_id;
            document.getElementById('edit_class_day').value = day;
            document.getElementById('edit_class_time').value = time;
            document.getElementById('edit_class_fee').value = fee; // ගාස්තුව පිරවීම
            
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