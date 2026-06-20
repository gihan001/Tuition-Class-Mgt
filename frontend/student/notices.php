<?php
session_start();

// Student කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// SQL Query එක: 'All' හෝ 'Students' යන කාණ්ඩවලට පමණක් සීමා කර ලබා ගැනීම
$sql_fetch = "SELECT id, title, audience, content, DATE(created_at) AS publish_date 
              FROM notices 
              WHERE audience IN ('All', 'Students') 
              ORDER BY id DESC";
$res_notices = mysqli_query($conn, $sql_fetch);
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
    <style>
        .clickable-cell { cursor: pointer; color: #2563EB; }
        .clickable-cell:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Student</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="classes.php">My Classes</a></li>
                <li><a href="timetable.php">Timetable</a></li>
                <li><a href="upcoming_tests.php">Upcoming Tests</a></li>
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
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search notices by title..." class="search-bar">
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
                        <?php if (mysqli_num_rows($res_notices) === 0): ?>
                            <tr>
                                <td colspan="3" style="text-align:center;">No notices available.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($notice = mysqli_fetch_assoc($res_notices)): ?>
                                <tr>
                                    <td><?php echo $notice['publish_date']; ?></td>
                                    <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['audience'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?>', '<?php echo $notice['publish_date']; ?>')">
                                        <strong><?php echo htmlspecialchars($notice['title']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($notice['audience']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <div id="viewModal" class="modal"> 
        <div class="modal-content" style="max-width: 550px;">
            <span class="close-btn" onclick="closeViewModal()">&times;</span>
            <h2 id="view_title" style="color: #2563EB; margin-bottom: 5px;">Title</h2>
            <p style="font-size: 0.85rem; color: #6B7280; margin-bottom: 15px;">
                Date: <span id="view_date" style="font-weight: bold;"></span> | 
                Audience: <span id="view_audience" style="font-weight: bold; color: #22C55E;"></span>
            </p>
            <hr>
            <p id="view_content" style="padding: 15px; background: #F9FAFB; border-radius: 6px;"></p>
            <button type="button" onclick="closeViewModal()" class="primary-button" style="width:100%; background-color:#6B7280;">Close</button>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        function openViewModal(title, audience, content, date) {
            document.getElementById('view_title').innerText = title;
            document.getElementById('view_audience').innerText = audience;
            document.getElementById('view_content').innerText = content;
            document.getElementById('view_date').innerText = date;
            document.getElementById('viewModal').classList.add('active');
        }
        function closeViewModal() {
            document.getElementById('viewModal').classList.remove('active');
        }
    </script>
</body>
</html>