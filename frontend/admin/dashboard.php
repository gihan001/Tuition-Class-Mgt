<?php
session_start();

// Admin කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Database එක සම්බන්ධ කිරීම
include '../../db.php';

// ---------------------------------------------------------
// දත්ත සමුදායෙන් (Database) ගණනය කිරීම් (COUNT) ලබා ගැනීම
// ---------------------------------------------------------

// 1. මුළු සිසුන් ගණන ලබා ගැනීම
$query_students = "SELECT COUNT(*) AS total_students FROM students";
$res_students = mysqli_query($conn, $query_students);
$total_students = ($res_students) ? mysqli_fetch_assoc($res_students)['total_students'] : 0;

// 2. මුළු ගුරුවරුන් ගණන ලබා ගැනීම
$query_teachers = "SELECT COUNT(*) AS total_teachers FROM teachers";
$res_teachers = mysqli_query($conn, $query_teachers);
$total_teachers = ($res_teachers) ? mysqli_fetch_assoc($res_teachers)['total_teachers'] : 0;

// 3. මුළු පන්ති ගණන ලබා ගැනීම
$query_classes = "SELECT COUNT(*) AS total_classes FROM classes";
$res_classes = mysqli_query($conn, $query_classes);
$total_classes = ($res_classes) ? mysqli_fetch_assoc($res_classes)['total_classes'] : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart Tuition</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    
    <div class="dashboard-container">

        <aside class="sidebar">
            <h2>Smart Tuition</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="students.php">Students</a></li>
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
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h1>
                <p>System Overview</p>
            </header>

            <div class="cards-container">
                <div class="card">
                    <h3>Total Students</h3>
                    <h2><?php echo $total_students; ?></h2>
                </div>
                <div class="card">
                    <h3>Total Teachers</h3>
                    <h2><?php echo $total_teachers; ?></h2>
                </div>
                <div class="card">
                    <h3>Active Classes</h3>
                    <h2><?php echo $total_classes; ?></h2>
                </div>
            </div>
        </main>
        
        <script src="../assets/js/dashboard.js"></script>
    </div>

</body>
</html>