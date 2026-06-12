<?php
session_start();

// Teacher කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// ලොග් වී සිටින ගුරුවරයාගේ teacher_id එක ලබා ගැනීම
$user_id = $_SESSION['user_id'];
$res_teacher = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id = $user_id");
$teacher_data = mysqli_fetch_assoc($res_teacher);
$teacher_id = $teacher_data['teacher_id'] ?? 0;

// 1. පන්තියක් මකා දැමීම (DELETE)
if (isset($_POST['action']) && $_POST['action'] === 'delete_class') {
    $class_id_del = (int)$_POST['class_id'];
    
    // අදාළ ගුරුවරයාගේ පන්තියක් පමණක් මැකීමට teacher_id පරීක්ෂාව
    $sql_del = "DELETE FROM classes WHERE class_id = $class_id_del AND teacher_id = $teacher_id";
    if (mysqli_query($conn, $sql_del)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Deleted Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting class.'];
    }
    header("Location: classes.php");
    exit();
}

// 2. අලුත් පන්තියක් එකතු කිරීම (ADD)
if (isset($_POST['add_class'])) {
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $grade = mysqli_real_escape_string($conn, trim($_POST['grade']));
    $class_day = mysqli_real_escape_string($conn, trim($_POST['class_day']));
    $class_time = mysqli_real_escape_string($conn, trim($_POST['class_time']));
    $class_fee = (float)$_POST['class_fee'];

    $sql_add = "INSERT INTO classes (subject, grade, teacher_id, class_day, class_time, class_fee) 
                VALUES ('$subject', '$grade', $teacher_id, '$class_day', '$class_time', $class_fee)";
    
    if (mysqli_query($conn, $sql_add)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Created Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error creating class.'];
    }
    header("Location: classes.php");
    exit();
}

// 3. පන්තියක් යාවත්කාලීන කිරීම (UPDATE)
if (isset($_POST['update_class'])) {
    $class_id = (int)$_POST['class_id'];
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $grade = mysqli_real_escape_string($conn, trim($_POST['grade']));
    $class_day = mysqli_real_escape_string($conn, trim($_POST['class_day']));
    $class_time = mysqli_real_escape_string($conn, trim($_POST['class_time']));
    $class_fee = (float)$_POST['class_fee'];

    $sql_update = "UPDATE classes 
                   SET subject = '$subject', grade = '$grade', class_day = '$class_day', class_time = '$class_time', class_fee = $class_fee 
                   WHERE class_id = $class_id AND teacher_id = $teacher_id";
    
    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Class Updated Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating class.'];
    }
    header("Location: classes.php");
    exit();
}

// 4. ගුරුවරයාට අදාළ පන්ති ලැයිස්තුව ලබා ගැනීම
$sql_fetch = "SELECT * FROM classes WHERE teacher_id = $teacher_id ORDER BY class_id DESC";
$res_classes = mysqli_query($conn, $sql_fetch);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes - Smart Tuition</title>
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
        
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php">My Students</a></li>
                <li><a href="classes.php" class="active">Assigned Classes</a></li>
                <li><a href="manage_tests.php">Upcoming Tests</a></li>
                <li><a href="materials.php">Upload Materials</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage My Classes</h1>
                <p>Create and manage your tuition classes.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>My Class List</h2>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search classes by subject, grade..." class="search-bar">
                    <button class="primary-button" onclick="openModal('addModal')">Add New Class</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Fee (Rs.)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_classes) === 0): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">You have not added any classes yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($class = mysqli_fetch_assoc($res_classes)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($class['subject']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($class['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_day']); ?></td>
                                    <td><?php echo htmlspecialchars($class['class_time']); ?></td>
                                    <td><?php echo number_format($class['class_fee'], 2); ?></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $class['class_id']; ?>, '<?php echo htmlspecialchars($class['subject'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($class['grade'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($class['class_day'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($class['class_time'], ENT_QUOTES); ?>', '<?php echo $class['class_fee']; ?>')">
                                            Edit
                                        </button>

                                        <form action="classes.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this class?');">
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
        </main>
    </div>

    <div id="addModal" class="modal"> 
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h2>Add New Class</h2>
            <form action="classes.php" method="POST">
                <div class="input-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Mathematics" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Grade/Level</label>
                    <input type="text" name="grade" placeholder="e.g. Grade 10" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Class Day</label>
                    <select name="class_day" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
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
                    <input type="text" name="class_time" placeholder="e.g. 04:00 PM - 06:00 PM" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Class Fee (Rs.)</label>
                    <input type="number" step="0.01" name="class_fee" placeholder="e.g. 2500.00" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <button type="submit" name="add_class" class="primary-button" style="width: 100%; padding: 12px; background-color: #10B981; border: none; color: white; border-radius: 5px; cursor: pointer;">Save Class</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal"> 
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Class Details</h2>
            <form action="classes.php" method="POST">
                <input type="hidden" name="class_id" id="edit_class_id">
                
                <div class="input-group">
                    <label>Subject</label>
                    <input type="text" name="subject" id="edit_subject" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Grade/Level</label>
                    <input type="text" name="grade" id="edit_grade" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Class Day</label>
                    <select name="class_day" id="edit_class_day" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
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
                    <input type="text" name="class_time" id="edit_class_time" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="input-group">
                    <label>Class Fee (Rs.)</label>
                    <input type="number" step="0.01" name="class_fee" id="edit_class_fee" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <button type="submit" name="update_class" class="primary-button" style="width: 100%; padding: 12px; background-color: #10B981; border: none; color: white; border-radius: 5px; cursor: pointer;">Update Class</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        // Toast Notification Auto-hide
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

        // Modals පාලනය කිරීම
        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        // Edit Modal එකට දත්ත යැවීම
        function openEditModal(id, subject, grade, day, time, fee) {
            document.getElementById('edit_class_id').value = id;
            document.getElementById('edit_subject').value = subject;
            document.getElementById('edit_grade').value = grade;
            document.getElementById('edit_class_day').value = day;
            document.getElementById('edit_class_time').value = time;
            document.getElementById('edit_class_fee').value = fee;
            
            openModal('editModal');
        }

        // Modal එකෙන් පිටත ක්ලික් කළ විට එය වැසීම
        window.addEventListener("click", function(event) {
            let addModal = document.getElementById('addModal');
            let editModal = document.getElementById('editModal');
            if (event.target === addModal) { addModal.classList.remove("active"); }
            if (event.target === editModal) { editModal.classList.remove("active"); }
        });
    </script>
</body>
</html>