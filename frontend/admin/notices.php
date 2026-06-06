<?php
session_start();

// Admin කෙනෙක් දැයි පරීක්ෂා කිරීමේ ආරක්ෂක පියවර
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// Errors බ්‍රවුසරයේ පෙන්වීමට On කිරීම
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------------------------
// 1. නිවේදනයක් මකා දැමීමේ කොටස (DELETE NOTICE BACKEND)
// ----------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'delete_notice') {
    $notice_id_del = (int)$_POST['notice_id'];

    $sql_del = "DELETE FROM notices WHERE id = $notice_id_del";
    
    if (mysqli_query($conn, $sql_del)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Notice Deleted Successfully!'];
        header("Location: notices.php");
        exit();
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting notice.'];
        header("Location: notices.php");
        exit();
    }
}

// ----------------------------------------------------
// 2. අලුත් නිවේදනයක් පළ කිරීමේ කොටස (ADD NOTICE BACKEND)
// ----------------------------------------------------
if (isset($_POST['submit_btn'])) {
    $title = trim($_POST['title'] ?? '');
    $audience = trim($_POST['audience'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title !== '' && $audience !== '' && $content !== '') {
        
        $title_esc = mysqli_real_escape_string($conn, $title);
        $audience_esc = mysqli_real_escape_string($conn, $audience);
        $content_esc = mysqli_real_escape_string($conn, $content);

        $sql_notice = "INSERT INTO notices (title, audience, content) VALUES ('{$title_esc}', '{$audience_esc}', '{$content_esc}')";
        
        if (mysqli_query($conn, $sql_notice)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Notice Published Successfully!'];
            header("Location: notices.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error publishing notice.'];
            header("Location: notices.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all required fields.'];
        header("Location: notices.php");
        exit();
    }
}

// ----------------------------------------------------
// 3. නිවේදනයක් යාවත්කාලීන කිරීමේ කොටස (UPDATE NOTICE BACKEND)
// ----------------------------------------------------
if (isset($_POST['update_btn'])) {
    $notice_id = (int)$_POST['notice_id'];
    $title = trim($_POST['title'] ?? '');
    $audience = trim($_POST['audience'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($notice_id > 0 && $title !== '' && $audience !== '' && $content !== '') {
        
        $title_esc = mysqli_real_escape_string($conn, $title);
        $audience_esc = mysqli_real_escape_string($conn, $audience);
        $content_esc = mysqli_real_escape_string($conn, $content);

        $sql_update = "UPDATE notices SET title = '{$title_esc}', audience = '{$audience_esc}', content = '{$content_esc}' WHERE id = $notice_id";
        
        if (mysqli_query($conn, $sql_update)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Notice Updated Successfully!'];
            header("Location: notices.php");
            exit();
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating notice.'];
            header("Location: notices.php");
            exit();
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill all fields for update.'];
        header("Location: notices.php");
        exit();
    }
}

// ----------------------------------------------------
// 4. නිවේදන ලැයිස්තුව ලබා ගැනීම (FETCH NOTICES)
// ----------------------------------------------------
$res_notices = mysqli_query($conn, "SELECT id, title, audience, content, DATE(created_at) AS publish_date FROM notices ORDER BY id DESC");
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
    <style>
        .clickable-cell {
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Toast Notification බැනරය -->
    <?php if (isset($_SESSION['toast'])): ?>
        <div class="toast-notification <?php echo $_SESSION['toast']['type']; ?>" id="toastBox">
            <?php echo $_SESSION['toast']['message']; ?>
        </div>
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>

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
                    
                    <!-- 🔍 Search Bar එක (දැන් Date සහ Audience මඟින්ද සෙවිය හැක) -->
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search notices by title, date, audience..." class="search-bar">
                    
                    <button class="primary-button" onclick="openModal()">Publish New Notice</button>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Publish Date</th>
                            <th>Notice Title</th>
                            <th>Audience</th>
                            <th style="width: 180px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_notices) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No notices published yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($notice = mysqli_fetch_assoc($res_notices)): ?>
                                <tr>
                                    <!-- දත්ත කොටු ක්ලික් කළ විට View Modal එක විවෘත වේ (Action තීරුව හැර) -->
                                    <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['audience'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?>', '<?php echo $notice['publish_date']; ?>')">
                                        <?php echo $notice['publish_date']; ?>
                                    </td>
                                    <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['audience'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?>', '<?php echo $notice['publish_date']; ?>')">
                                        <strong><?php echo htmlspecialchars($notice['title']); ?></strong>
                                    </td>
                                    <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['audience'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?>', '<?php echo $notice['publish_date']; ?>')">
                                        <?php echo htmlspecialchars($notice['audience']); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <!-- Edit බොත්තම -->
                                        <button class="action-btn edit-btn" onclick="openEditModal('<?php echo $notice['id']; ?>', '<?php echo htmlspecialchars($notice['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['audience'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notice['content'], ENT_QUOTES); ?>')">
                                            Edit
                                        </button>
                                        
                                        <!-- Delete බොත්තම -->
                                        <form action="notices.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this notice?');">
                                            <input type="hidden" name="action" value="delete_notice">
                                            <input type="hidden" name="notice_id" value="<?php echo (int)$notice['id']; ?>">
                                            <button type="submit" class="action-btn delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- ➕ Pop-up Publish Modal Form එක -->
            <div id="addModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Publish Notice</h2>
                    <form action="notices.php" method="POST">
                        <div class="input-group">
                            <label>Notice Title</label>
                            <input type="text" name="title" placeholder="e.g. Holiday Announcement" required>
                        </div>
                        <div class="input-group">
                            <label>Select Audience</label>
                            <select name="audience" required>
                                <option value="All">All (Students & Teachers)</option>
                                <option value="Students">Students Only</option>
                                <option value="Teachers">Teachers Only</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Notice Content</label>
                            <textarea name="content" placeholder="Type your announcement here..." required></textarea>
                        </div>
                        <button type="submit" name="submit_btn" class="primary-button submit-btn">Publish</button>
                    </form>
                </div>
            </div>

            <!-- 📝 Pop-up Edit Modal Form එක -->
            <div id="editModal" class="modal"> 
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Notice Details</h2>
                    <form action="notices.php" method="POST">
                        <input type="hidden" name="notice_id" id="edit_notice_id">
                        <div class="input-group">
                            <label>Notice Title</label>
                            <input type="text" name="title" id="edit_title" required>
                        </div>
                        <div class="input-group">
                            <label>Select Audience</label>
                            <select name="audience" id="edit_audience" required>
                                <option value="All">All (Students & Teachers)</option>
                                <option value="Students">Students Only</option>
                                <option value="Teachers">Teachers Only</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Notice Content</label>
                            <textarea name="content" id="edit_content" required></textarea>
                        </div>
                        <button type="submit" name="update_btn" class="primary-button submit-btn">Update Notice</button>
                    </form>
                </div>
            </div>

            <!-- 👁️ Pop-up View Modal එක -->
            <div id="viewModal" class="modal"> 
                <div class="modal-content" style="max-width: 550px;">
                    <span class="close-btn" onclick="closeViewModal()">&times;</span>
                    <h2 id="view_title" style="color: #2563EB; margin-bottom: 5px;">Notice Title</h2>
                    <p style="font-size: 0.85rem; color: #6B7280; margin-bottom: 15px;">
                        Published Date: <span id="view_date" style="font-weight: bold;"></span> | 
                        Audience: <span id="view_audience" style="font-weight: bold; color: #22C55E;"></span>
                    </p>
                    <hr style="border: 0; border-top: 1px solid #E5E7EB; margin-bottom: 15px;">
                    <div class="input-group">
                        <p id="view_content" style="white-space: pre-line; line-height: 1.6; color: #374151; background: #F9FAFB; padding: 15px; border-radius: 6px; border: 1px solid #E5E7EB; max-height: 250px; overflow-y: auto;">
                            Notice content will appear here...
                        </p>
                    </div>
                    <button type="button" onclick="closeViewModal()" class="primary-button" style="background-color: #6B7280; margin-top: 15px; width: 100%;">Close</button>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        // Toast Notification එක තත්පර 3කින් සැඟවීම
        window.addEventListener('DOMContentLoaded', (event) => {
            var toast = document.getElementById('toastBox');
            if (toast) {
                setTimeout(function() {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(function() { toast.remove(); }, 500); 
                }, 3000);
            }
        });

        // EDIT MODAL පාලනය කරන JAVASCRIPT
        function openEditModal(id, title, audience, content) {
            document.getElementById('edit_notice_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_audience').value = audience;
            document.getElementById('edit_content').value = content;
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // VIEW MODAL පාලනය කරන JAVASCRIPT
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

        // Modal වලින් පිටත ක්ලික් කළහොත් ඒවා වැසීම
        window.addEventListener("click", function(event) {
            let editModal = document.getElementById('editModal');
            let viewModal = document.getElementById('viewModal');
            if (event.target === editModal) { editModal.classList.remove("active"); }
            if (event.target === viewModal) { viewModal.classList.remove("active"); }
        });
    </script>
</body>
</html>