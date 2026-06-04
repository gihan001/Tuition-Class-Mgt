<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Smart Tuition</title>

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
                <li><a href="notices.php">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Welcome, Student!</h1>
                <p>Your overview</p>
            </header>

            <div class="cards-container">
                <div class="card">
                    <h3>Enrolled Classes</h3>
                    <h2>3</h2>
                </div>
                <div class="card">
                    <h3>Upcoming Tests</h3>
                    <h2>1</h2>
                </div>
                <div class="card">
                    <h3>Unread Notices</h3>
                    <h2>2</h2>
                </div>
            </div>
        </main>
        <script src="../assets/js/dashboard.js"></script>
    </div>

</body>
</html>
