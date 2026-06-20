<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// ශිෂ්‍යයාගේ student_id එක ලබා ගැනීම
$user_id = $_SESSION['user_id'];
$res_stu = mysqli_query($conn, "SELECT student_id FROM students WHERE user_id = $user_id");
$student = mysqli_fetch_assoc($res_stu);
$student_id = $student['student_id'];

// ශිෂ්‍යයා Enroll වී ඇති පන්ති වලට අදාළ Test ලැයිස්තුව ලබා ගැනීම
$sql = "SELECT t.*, c.subject, c.grade, tr.name AS teacher_name 
        FROM tests t
        JOIN enrollments e ON t.class_id = e.class_id
        JOIN classes c ON t.class_id = c.class_id
        JOIN teachers tr ON c.teacher_id = tr.teacher_id
        WHERE e.student_id = $student_id AND t.test_date >= CURDATE()
        ORDER BY t.test_date ASC";
$res_tests = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upcoming Tests - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .clickable-row { cursor: pointer; transition: background 0.3s; }
        .clickable-row:hover { background-color: #f1f5f9; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Student</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="classes.php">My Classes</a></li>
                <li><a href="timetable.php">Timetable</a></li>
                <li><a href="upcoming_tests.php" class="active">Upcoming Tests</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Upcoming Tests</h1>
            </header>

            <section class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Teacher</th>
                            <th>Test Title</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($res_tests)) { ?>
                        <tr class="clickable-row" onclick="openViewModal('<?php echo htmlspecialchars($row['test_title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>', '<?php echo $row['test_date']; ?>', '<?php echo htmlspecialchars($row['subject'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['grade'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['teacher_name'], ENT_QUOTES); ?>')">
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo htmlspecialchars($row['grade']); ?></td>
                            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['test_title']); ?></strong></td>
                            <td><?php echo $row['test_date']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <h2 id="view_title" style="color: #2563EB;"></h2>
            <p style="color:gray; font-size: 0.9rem;" id="view_date"></p>
            <p style="font-weight: 600;" id="view_subject"></p>
            <p style="font-weight: 600;" id="view_teacher"></p>
            <hr>
            <h4 style="margin-top:15px;">Description:</h4>
            <p id="view_desc" style="margin-top:5px;"></p>
        </div>
    </div>

    <script>
        function openViewModal(title, desc, date, subject, grade, teacher) {
            document.getElementById('view_title').innerText = title;
            document.getElementById('view_date').innerText = 'Date: ' + date;
            document.getElementById('view_subject').innerText = 'Subject: ' + subject + ' (' + grade + ')';
            document.getElementById('view_teacher').innerText = 'Teacher: ' + teacher;
            document.getElementById('view_desc').innerText = desc;
            document.getElementById('viewModal').classList.add('active');
        }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    </script>
</body>
</html>