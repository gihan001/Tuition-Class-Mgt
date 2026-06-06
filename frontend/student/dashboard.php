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

// 1. Enrolled Classes ගණන
$res_classes = mysqli_query($conn, "SELECT COUNT(*) as total FROM enrollments WHERE student_id = $student_id");
$total_classes = mysqli_fetch_assoc($res_classes)['total'];

// 2. Upcoming Tests ගණන (අද දිනට පසු එන පරීක්ෂණ)
$res_tests = mysqli_query($conn, "SELECT COUNT(*) as total FROM tests WHERE test_date >= CURDATE()");
$total_tests = mysqli_fetch_assoc($res_tests)['total'];

// 3. Notices ගණන (සීමාවක් නැත, සියලු නිවේදන)
$res_notices = mysqli_query($conn, "SELECT COUNT(*) as total FROM notices WHERE audience IN ('All', 'Students')");
$total_notices = mysqli_fetch_assoc($res_notices)['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Student</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="classes.php">My Classes</a></li>
                <li><a href="timetable.php">Timetable</a></li>
                <li><a href="upcoming_tests.php">Upcoming Tests</a></li>
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <h1>Welcome, Student!</h1>
            </header>

            <div class="cards-container">
                <div class="card">
                    <h3>Enrolled Classes</h3>
                    <h2><?php echo $total_classes; ?></h2>
                </div>
                <div class="card">
                    <h3>Upcoming Tests</h3>
                    <h2><?php echo $total_tests; ?></h2>
                </div>
                <div class="card">
                    <h3>System Notices</h3>
                    <h2><?php echo $total_notices; ?></h2>
                </div>
            </div>
        </main>
    </div>
</body>
</html>