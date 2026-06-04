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
    <title>Manage Students - Smart Tuition</title>
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
                <li><a href="students.php" class="active">Students</a></li>
                <li><a href="teachers.php">Teachers</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Students</h1>
                <p>View and manage student accounts.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Student List</h2>
                    <button class="primary-button" onclick="openModal()">Add New Student</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>STU-001</td>
                            <td>Ashan Perera</td>
                            <td>ashan@example.com</td>
                            <td>Mathematics - Grade 10</td>
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
                    
                    <h2>Add New Student</h2>
                    
                    <form action="students.php" method="POST">
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" placeholder="Enter full name">
                        </div>
                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter email">
                        </div>
                        <div class="input-group">
                            <label>Assign Class</label>
                            <input type="text" name="class" placeholder="e.g. Mathematics - Grade 10">
                        </div>
                        <button type="submit" class="primary-button submit-btn">Save Student</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
