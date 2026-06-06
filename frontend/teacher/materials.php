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
    <title>Upload Materials - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>

<body>

    <div class="dashboard-container">

        <aside class="sidebar" style="background-color: #0F172A;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php">My Students</a></li>
                <li><a href="classes.php">Assigned Classes</a></li>
                <li><a href="manage_tests.php">Upcoming Tests</a></li>
                <li><a href="materials.php" class="active">Upload Materials</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Upload Materials</h1>
                <p>Share resources with your students.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Uploaded Materials</h2>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Uploaded On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mathematics - Chapter 5</td>
                            <td>Grade 10</td>
                            <td>2026-05-12</td>
                            <td><button class="action-btn download-btn">Download</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div class="modal" id="uploadModal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Upload Material</h2>
                    <form action="materials.php" method="POST" enctype="multipart/form-data">
                        <div class="input-group">
                            <label>Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="input-group">
                            <label>Class</label>
                            <input type="text" name="class" required>
                        </div>
                        <div class="input-group">
                            <label>File</label>
                            <input type="file" name="file" required>
                        </div>
                        <button type="submit" class="primary-button">Upload</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>

</html>