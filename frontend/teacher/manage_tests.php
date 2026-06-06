<?php
session_start();

// Teacher කෙනෙක් දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';
$user_id = $_SESSION['user_id'];

// 1. DELETE පරීක්ෂණයක් මකා දැමීම
if (isset($_POST['action']) && $_POST['action'] === 'delete_test') {
    $test_id = (int)$_POST['test_id'];
    if (mysqli_query($conn, "DELETE FROM tests WHERE test_id = $test_id")) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Test Deleted Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error deleting test.'];
    }
    header("Location: manage_tests.php");
    exit();
}

// 2. ADD පරීක්ෂණයක් එකතු කිරීම
if (isset($_POST['add_test'])) {
    $class_id = (int)$_POST['class_id'];
    $title = mysqli_real_escape_string($conn, $_POST['test_title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date = mysqli_real_escape_string($conn, $_POST['test_date']);

    if (mysqli_query($conn, "INSERT INTO tests (class_id, test_title, description, test_date) VALUES ($class_id, '$title', '$desc', '$date')")) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Test Published Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error publishing test.'];
    }
    header("Location: manage_tests.php");
    exit();
}

// 3. EDIT පරීක්ෂණයක් යාවත්කාලීන කිරීම
if (isset($_POST['update_test'])) {
    $test_id = (int)$_POST['test_id'];
    $title = mysqli_real_escape_string($conn, $_POST['test_title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date = mysqli_real_escape_string($conn, $_POST['test_date']);

    if (mysqli_query($conn, "UPDATE tests SET test_title = '$title', description = '$desc', test_date = '$date' WHERE test_id = $test_id")) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Test Updated Successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Error updating test.'];
    }
    header("Location: manage_tests.php");
    exit();
}

// දත්ත ලබා ගැනීම
$sql_tests = "SELECT t.*, c.subject, c.grade FROM tests t 
              JOIN classes c ON t.class_id = c.class_id 
              JOIN teachers tr ON c.teacher_id = tr.teacher_id 
              WHERE tr.user_id = $user_id ORDER BY t.test_date ASC";
$res_tests = mysqli_query($conn, $sql_tests);

$res_classes = mysqli_query($conn, "SELECT c.class_id, c.subject, c.grade FROM classes c JOIN teachers t ON c.teacher_id = t.teacher_id WHERE t.user_id = $user_id");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Tests - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>

<body>

    <?php if (isset($_SESSION['toast'])): ?>
        <div class="toast-notification <?php echo $_SESSION['toast']['type']; ?>" id="toastBox">
            <?php echo $_SESSION['toast']['message']; ?>
        </div>
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>

    <div class="dashboard-container">
        <aside class="sidebar" style="background-color: #1E293B;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="students.php">My Students</a></li>
                <li><a href="classes.php">Assigned Classes</a></li>
                <li><a href="manage_tests.php" class="active">Upcoming Tests</a></li>
                <li><a href="materials.php">Upload Materials</a></li>
                <li><a href="../index.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Manage Upcoming Tests</h1>
            </header>

            <section class="table-container">
                <div class="table-header">
                    <h2>Test List</h2>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by title, subject..." class="search-bar">
                    <button class="primary-button" onclick="openModal('addModal')">Add New Test</button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Title</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($res_tests)) { ?>
                            <tr>
                                <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($row['test_title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>', '<?php echo $row['test_date']; ?>')"><?php echo $row['test_date']; ?></td>
                                <td><?php echo $row['subject'] . ' (' . $row['grade'] . ')'; ?></td>
                                <td class="clickable-cell" onclick="openViewModal('<?php echo htmlspecialchars($row['test_title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>', '<?php echo $row['test_date']; ?>')"><?php echo htmlspecialchars($row['test_title']); ?></td>
                                <td>
                                    <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $row['test_id']; ?>, '<?php echo htmlspecialchars($row['test_title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>', '<?php echo $row['test_date']; ?>')">Edit</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
                                        <input type="hidden" name="action" value="delete_test">
                                        <input type="hidden" name="test_id" value="<?php echo $row['test_id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- Add Test Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
            <h2>Add New Test</h2>
            <form method="POST">
                <div class="input-group">
                    <label>Select Class</label>
                    <select name="class_id" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">-- Select Class --</option>
                        <?php
                        // පන්ති ලැයිස්තුව නැවත Reset කරමු
                        mysqli_data_seek($res_classes, 0);
                        while ($c = mysqli_fetch_assoc($res_classes)) {
                            echo "<option value='{$c['class_id']}'>{$c['subject']} - {$c['grade']}</option>";
                        } ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Test Title</label>
                    <input type="text" name="test_title" placeholder="Enter test title" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Enter test details" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; height: 80px;"></textarea>
                </div>

                <div class="input-group">
                    <label>Test Date</label>
                    <input type="date" name="test_date" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <button type="submit" name="add_test" class="primary-button" style="width: 100%; padding: 12px; background-color: #10B981; border: none; color: white; border-radius: 5px; cursor: pointer;">Save Test</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Test Details</h2>
            <form method="POST">
                <input type="hidden" name="test_id" id="edit_id">

                <div class="input-group">
                    <label>Test Title</label>
                    <input type="text" name="test_title" id="edit_title" placeholder="Enter test title" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_desc" placeholder="Enter test details" required style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; height: 80px;"></textarea>
                </div>

                <div class="input-group">
                    <label>Test Date</label>
                    <input type="date" name="test_date" id="edit_date" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <button type="submit" name="update_test" class="primary-button" style="width: 100%; padding: 12px; background-color: #10B981; border: none; color: white; border-radius: 5px; cursor: pointer;">Update Test</button>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('viewModal')">&times;</span>
            <h2 id="view_title"></h2>
            <p id="view_date" style="color:gray;"></p>
            <p id="view_desc"></p>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function openEditModal(id, title, desc, date) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_desc').value = desc;
            document.getElementById('edit_date').value = date;
            openModal('editModal');
        }

        function openViewModal(title, desc, date) {
            document.getElementById('view_title').innerText = title;
            document.getElementById('view_date').innerText = date;
            document.getElementById('view_desc').innerText = desc;
            openModal('viewModal');
        }
    </script>
</body>

</html>