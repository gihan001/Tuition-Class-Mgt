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
    <title>Manage Notices - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <h2>Smart Tuition</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="teachers.php">Teachers</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="notices.php" class="active">Notices</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>System Notices</h1>
                <p>Publish announcements for students and teachers.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Recent Notices</h2>
                    <button class="primary-button" onclick="openModal()">Publish New Notice</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2026-05-25</td>
                            <td>Special Holiday Announcement</td>
                            <td>All</td>
                            <td>
                                <button class="action-btn delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2026-05-20</td>
                            <td>Term Test Schedule</td>
                            <td>Students</td>
                            <td>
                                <button class="action-btn delete-btn">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div id="addModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    
                    <h2>Publish Notice</h2>
                    
                    <form action="notices.php" method="POST">
                        <div class="input-group">
                            <label>Notice Title</label>
                            <input type="text" name="title" placeholder="e.g. Holiday Announcement">
                        </div>
                        <div class="input-group">
                            <label>Select Audience</label>
                            <select name="audience" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Poppins', sans-serif;">
                                <option>All (Students & Teachers)</option>
                                <option>Students Only</option>
                                <option>Teachers Only</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Notice Content</label>
                            <textarea name="content" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Poppins', sans-serif; min-height: 80px; resize: vertical;" placeholder="Type your announcement here..."></textarea>
                        </div>
                        <button type="submit" class="primary-button submit-btn">Publish</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
