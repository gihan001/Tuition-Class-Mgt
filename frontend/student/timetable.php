<?php
session_start();

// Student kiyala check kirima
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// Log wela inna student ge ID eka ganna
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc();
$student_id = $student_data['student_id'] ?? 0;

// Enroll wela inna classes wala timetable eka ganna query eka
$timetable_query = "SELECT c.class_day, c.class_time, c.subject, c.grade, t.name AS teacher_name 
                    FROM enrollments e
                    INNER JOIN classes c ON e.class_id = c.class_id
                    INNER JOIN teachers t ON c.teacher_id = t.teacher_id
                    WHERE e.student_id = $student_id
                    ORDER BY FIELD(c.class_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), c.class_time ASC";

$res_timetable = mysqli_query($conn, $timetable_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Student</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="classes.php">My Classes</a></li>
                <li><a href="timetable.php" class="active">Timetable</a></li>
                <li><a href="upcoming_tests.php">Upcoming Tests</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Timetable</h1>
                <p>Your weekly class schedule.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Weekly Timetable</h2>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Subject</th> <th>Grade</th>   <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_timetable) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">No scheduled classes found. Enroll in a class first.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($res_timetable)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['class_day']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['class_time']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>