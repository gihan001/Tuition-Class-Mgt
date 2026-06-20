<?php
session_start();

// Teacher කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// ලොග් වී සිටින ගුරුවරයාගේ teacher_id එක ලබා ගැනීම
$user_id = $_SESSION['user_id'];
$res_teacher = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id = $user_id");
$teacher_data = mysqli_fetch_assoc($res_teacher);
$teacher_id = $teacher_data['teacher_id'] ?? 0;

// ගුරුවරයාගේ පන්ති වලට Enroll වී ඇති සිසුන්ගේ දත්ත ලබා ගැනීම
$sql_students = "SELECT s.student_id, u.full_name, u.email, s.contact, c.subject, c.grade 
                 FROM enrollments e
                 JOIN students s ON e.student_id = s.student_id
                 JOIN users u ON s.user_id = u.id
                 JOIN classes c ON e.class_id = c.class_id
                 WHERE c.teacher_id = $teacher_id
                 ORDER BY c.subject ASC, u.full_name ASC";
$res_students = mysqli_query($conn, $sql_students);
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
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar" style="background-color: #0F172A;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php" class="active">My Students</a></li>
                <li><a href="classes.php">Assigned Classes</a></li>
                <li><a href="manage_tests.php">Upcoming Tests</a></li>
                <li><a href="materials.php">Upload Materials</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>My Students</h1>
                <p>View students enrolled in your classes.</p>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Student List</h2>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by name, class..." class="search-bar">
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class (Subject - Grade)</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_students) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No students have enrolled in your classes yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($res_students)): ?>
                                <tr>
                                    <td>STU-<?php echo str_pad((string)$row['student_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['subject'] . ' - ' . $row['grade']); ?></td>
                                    <td style="text-align: center;">
                                        <button class="action-btn view-btn" onclick="openViewModal('STU-<?php echo str_pad((string)$row['student_id'], 3, '0', STR_PAD_LEFT); ?>', '<?php echo htmlspecialchars($row['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['contact'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['subject'] . ' - ' . $row['grade'], ENT_QUOTES); ?>')">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <div id="viewModal" class="modal"> 
                <div class="modal-content" style="max-width: 450px; text-align: left;">
                    <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
                    <h2 style="color: #0F172A; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px; margin-bottom: 20px;">Student Details</h2>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="color: #6B7280; font-size: 0.9rem;">Student ID:</span>
                        <h3 id="view_student_id" style="margin: 0; color: #111827;"></h3>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="color: #6B7280; font-size: 0.9rem;">Full Name:</span>
                        <h3 id="view_name" style="margin: 0; color: #111827;"></h3>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <span style="color: #6B7280; font-size: 0.9rem;">Email Address:</span>
                        <p id="view_email" style="margin: 0; color: #374151; font-weight: 500;"></p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <span style="color: #6B7280; font-size: 0.9rem;">Contact Number:</span>
                        <p id="view_contact" style="margin: 0; color: #374151; font-weight: 500;"></p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <span style="color: #6B7280; font-size: 0.9rem;">Enrolled Class:</span>
                        <p id="view_class" style="margin: 0; color: #10B981; font-weight: bold; background: #ECFDF5; padding: 5px 10px; display: inline-block; border-radius: 4px;"></p>
                    </div>

                    <button type="button" onclick="closeModal('viewModal')" class="primary-button" style="width: 100%; background-color: #6B7280;">Close</button>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        // View Modal එක විවෘත කර දත්ත පිරවීම
        function openViewModal(id, name, email, contact, className) {
            document.getElementById('view_student_id').innerText = id;
            document.getElementById('view_name').innerText = name;
            document.getElementById('view_email').innerText = email;
            document.getElementById('view_contact').innerText = contact;
            document.getElementById('view_class').innerText = className;
            
            document.getElementById('viewModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Modal එකෙන් පිටත ක්ලික් කළ විට එය වැසීම
        window.addEventListener("click", function(event) {
            let viewModal = document.getElementById('viewModal');
            if (event.target === viewModal) {
                viewModal.classList.remove("active");
            }
        });
    </script>
</body>
</html>