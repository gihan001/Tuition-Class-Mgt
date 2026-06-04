<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
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
                <h1>Welcome, Admin!</h1>
                <p>System Overview</p>
            </header>

            <div class="cards-container">
                <div class="card">
                    <h3>Total Students</h3>
                    <h2>150</h2>
                </div>
                <div class="card">
                    <h3>Total Teachers</h3>
                    <h2>12</h2>
                </div>
                <div class="card">
                    <h3>Active Classes</h3>
                    <h2>24</h2>
                </div>
            </div>
        </main>
        <script src="../assets/js/dashboard.js"></script>
    </div>

</body>
</html>