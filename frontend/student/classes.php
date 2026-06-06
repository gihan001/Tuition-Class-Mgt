<?php
session_start();

// Student කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// 1. ලොග් වී සිටින ශිෂ්‍යයාගේ student_id එක ලබා ගැනීම
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc();
$student_id = $student_data['student_id'] ?? 0;

// ----------------------------------------------------
// 2. අලුත් පන්තියකට Enroll වීමේ Backend කොටස
// ----------------------------------------------------
if (isset($_POST['enroll_btn'])) {
    $class_id = (int)$_POST['class_id'];

    if ($class_id > 0 && $student_id > 0) {
        $check_query = "SELECT * FROM enrollments WHERE student_id = $student_id AND class_id = $class_id";
        $check_res = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_res) > 0) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'You are already enrolled in this class!'];
        } else {
            $sql_enroll = "INSERT INTO enrollments (student_id, class_id) VALUES ($student_id, $class_id)";
            if (mysqli_query($conn, $sql_enroll)) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Successfully Enrolled!'];
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Enrollment failed. Try again.'];
            }
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid Request. Please select a valid class.'];
    }
    header("Location: classes.php");
    exit();
}

// ----------------------------------------------------
// 3. Database එකෙන් දත්ත ලබා ගැනීම (FETCH)
// ----------------------------------------------------

// සියලුම පන්ති දත්ත Array එකකට ලබාගෙන එය JS සඳහා JSON බවට පත් කිරීම
$all_classes_query = "SELECT classes.class_id, classes.subject, classes.grade, classes.class_fee, teachers.name AS teacher_name 
                      FROM classes 
                      INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id 
                      ORDER BY classes.subject ASC";
$all_classes_res = mysqli_query($conn, $all_classes_query);

$classes_array = [];
while ($row = mysqli_fetch_assoc($all_classes_res)) {
    $classes_array[] = $row;
}
// JavaScript වලට කියවිය හැකි ලෙස JSON කිරීම
$classes_json = json_encode($classes_array);

// මෙම ශිෂ්‍යයා Enroll වී ඇති පන්ති පමණක් වගුවට ලබා ගැනීම
$my_classes_query = "SELECT classes.subject, classes.grade, classes.class_fee, teachers.name AS teacher_name, enrollments.status 
                     FROM enrollments 
                     INNER JOIN classes ON enrollments.class_id = classes.class_id
                     INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id
                     WHERE enrollments.student_id = $student_id
                     ORDER BY enrollments.created_at DESC";
$my_classes = mysqli_query($conn, $my_classes_query);
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
            <h2>Smart Student</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="classes.php" class="active">My Classes</a></li>
                <li><a href="timetable.php">Timetable</a></li>
                <li><a href="upcoming_tests.php">Upcoming Tests</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>My Enrolled Classes</h1>
                <p>View the details of the classes you are currently attending.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Class List</h2>
                    <button class="primary-button" onclick="openEnrollModal()">Enroll in New Class</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade/Level</th>
                            <th>Teacher</th>
                            <th>Class Fee (Rs.)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($my_classes) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">You haven't enrolled in any classes yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($my_classes)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['subject']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                                    <td>Rs. <?php echo number_format($row['class_fee'], 2); ?></td>
                                    <td><span class="status-badge live"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <div id="addModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    
                    <h2>Enroll in a Class</h2>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Follow the steps below to select your class.</p>
                    
                    <form action="classes.php" method="POST">
                        
                        <div class="input-group">
                            <label>1. Select Subject</label>
                            <select id="subjectSelect" onchange="filterGrades()" required>
                                <option value="">-- First, Select a Subject --</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>2. Select Grade/Level</label>
                            <select id="gradeSelect" onchange="filterTeachers()" required disabled>
                                <option value="">-- Then, Select a Grade --</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>3. Select Teacher & Fee</label>
                            <select name="class_id" id="teacherSelect" required disabled>
                                <option value="">-- Finally, Select Teacher --</option>
                            </select>
                        </div>

                        <button type="submit" name="enroll_btn" class="primary-button submit-btn">Confirm Enrollment</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        // --------------------------------------------------------------
        // Cascading Dropdowns සඳහා අවශ්‍ය JavaScript Logic
        // --------------------------------------------------------------
        
        // PHP හරහා ලබාගත් සියලුම පන්ති දත්ත JS Array එකක් ලෙස ලබා ගැනීම
        const classData = <?php echo $classes_json; ?>;

        // Modal එක විවෘත කිරීම සහ මුල් Dropdown එක (Subject) පිරවීම
        function openEnrollModal() {
            document.getElementById('addModal').classList.add('active');
            
            let subjectSelect = document.getElementById('subjectSelect');
            subjectSelect.innerHTML = '<option value="">-- First, Select a Subject --</option>';
            document.getElementById('gradeSelect').innerHTML = '<option value="">-- Then, Select a Grade --</option>';
            document.getElementById('teacherSelect').innerHTML = '<option value="">-- Finally, Select Teacher --</option>';
            
            document.getElementById('gradeSelect').disabled = true;
            document.getElementById('teacherSelect').disabled = true;

            // දත්ත සමුදායේ ඇති විෂයයන් (Subjects) Duplicate නොවී ලබා ගැනීම
            let uniqueSubjects = [...new Set(classData.map(item => item.subject))];
            
            uniqueSubjects.forEach(subject => {
                let option = document.createElement('option');
                option.value = subject;
                option.textContent = subject;
                subjectSelect.appendChild(option);
            });
        }

        // විෂය තේරූ පසු ඊට අදාළ ශ්‍රේණි (Grades) පෙන්වීම
        function filterGrades() {
            let selectedSubject = document.getElementById('subjectSelect').value;
            let gradeSelect = document.getElementById('gradeSelect');
            let teacherSelect = document.getElementById('teacherSelect');

            gradeSelect.innerHTML = '<option value="">-- Then, Select a Grade --</option>';
            teacherSelect.innerHTML = '<option value="">-- Finally, Select Teacher --</option>';
            teacherSelect.disabled = true;

            if (selectedSubject !== "") {
                // තෝරාගත් විෂයට අදාළ දත්ත පමණක් වෙන් කිරීම
                let filteredBySubject = classData.filter(item => item.subject === selectedSubject);
                let uniqueGrades = [...new Set(filteredBySubject.map(item => item.grade))];

                uniqueGrades.forEach(grade => {
                    let option = document.createElement('option');
                    option.value = grade;
                    option.textContent = grade;
                    gradeSelect.appendChild(option);
                });
                gradeSelect.disabled = false; // Grade කොටුව සක්‍රීය කිරීම
            } else {
                gradeSelect.disabled = true;
            }
        }

        // ශ්‍රේණිය තේරූ පසු අදාළ ගුරුවරුන් සහ ගාස්තු පෙන්වීම
        function filterTeachers() {
            let selectedSubject = document.getElementById('subjectSelect').value;
            let selectedGrade = document.getElementById('gradeSelect').value;
            let teacherSelect = document.getElementById('teacherSelect');

            teacherSelect.innerHTML = '<option value="">-- Finally, Select Teacher --</option>';

            if (selectedSubject !== "" && selectedGrade !== "") {
                // විෂයට සහ ශ්‍රේණියට ගැලපෙන පන්ති වෙන් කිරීම
                let finalClasses = classData.filter(item => item.subject === selectedSubject && item.grade === selectedGrade);

                finalClasses.forEach(c => {
                    let option = document.createElement('option');
                    option.value = c.class_id; // Database එකට යන්නේ මේ Class ID එකයි
                    option.textContent = c.teacher_name + " | Fee: Rs. " + parseFloat(c.class_fee).toFixed(2);
                    teacherSelect.appendChild(option);
                });
                teacherSelect.disabled = false; // Teacher කොටුව සක්‍රීය කිරීම
            } else {
                teacherSelect.disabled = true;
            }
        }

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
    </script>
</body>
</html>