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
                            <th>Subject</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Monday</td>
                            <td>04:00 PM</td>
                            <td>Mathematics</td>
                            <td>Mr. Sunimal Silva</td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
