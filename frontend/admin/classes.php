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
    <title>Manage Classes - Smart Tuition</title>
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
                <li><a href="classes.php" class="active">Classes</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Classes</h1>
                <p>Create and manage tuition classes.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Class List</h2>
                    <button class="primary-button" onclick="openModal()">Add New Class</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Class ID</th>
                            <th>Subject & Grade</th>
                            <th>Teacher</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CLS-001</td>
                            <td>Mathematics - Grade 10</td>
                            <td>Mr. Sunimal Silva</td>
                            <td>Monday 04:00 PM</td>
                            <td>
                                <button class="action-btn edit-btn">Edit</button>
                                <button class="action-btn delete-btn">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>CLS-002</td>
                            <td>Science - Grade 11</td>
                            <td>Mrs. Priyanthi Perera</td>
                            <td>Wednesday 03:30 PM</td>
                            <td>
                                <button class="action-btn edit-btn">Edit</button>
                                <button class="action-btn delete-btn">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div id="addModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    
                    <h2>Add New Class</h2>
                    
                    <form action="classes.php" method="POST">
                        <div class="input-group">
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="e.g. Mathematics">
                        </div>
                        <div class="input-group">
                            <label>Grade/Level</label>
                            <input type="text" name="level" placeholder="e.g. Grade 10">
                        </div>
                        <div class="input-group">
                            <label>Assign Teacher</label>
                            <select name="teacher" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Poppins', sans-serif;">
                                <option>Mr. Sunimal Silva</option>
                                <option>Mrs. Priyanthi Perera</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Class Day & Time</label>
                            <input type="text" name="schedule" placeholder="e.g. Monday 04:00 PM">
                        </div>
                        <button type="submit" class="primary-button submit-btn">Save Class</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
