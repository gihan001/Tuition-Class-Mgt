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
    <title>Notices - Smart Tuition</title>
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
                <li><a href="timetable.php">Timetable</a></li>
                <li><a href="notices.php" class="active">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Notices</h1>
                <p>Important announcements for students.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Recent Notices</h2>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Audience</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2026-05-25</td>
                            <td>Special Holiday Announcement</td>
                            <td>All</td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
