<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar" style="background-color: #0F172A;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php" class="active">My Students</a></li>
                <li><a href="materials.php">Upload Materials</a></li>
                <li><a href="classes.php">Assigned Classes</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>My Students</h1>
                <p>View students assigned to your classes.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Student List</h2>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>STU-001</td>
                            <td>Ashan Perera</td>
                            <td>Mathematics - Grade 10</td>
                            <td>
                                <button class="action-btn view-btn">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
