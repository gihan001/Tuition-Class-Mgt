<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

include '../../db.php';

// ලොග් වී සිටින ගුරුවරයාගේ User ID එක
$user_id = $_SESSION['user_id'];

// 1. ගුරුවරයාගේ teacher_id එක ලබා ගැනීම
$res_teacher = mysqli_query($conn, "SELECT teacher_id FROM teachers WHERE user_id = $user_id");
$teacher_data = mysqli_fetch_assoc($res_teacher);
$teacher_id = $teacher_data['teacher_id'] ?? 0;

// 2. ගුරුවරයාගේ මුළු පන්ති ගණන ලබා ගැනීම (Assigned Classes)
$res_classes = mysqli_query($conn, "SELECT COUNT(*) AS total_classes FROM classes WHERE teacher_id = $teacher_id");
$total_classes = mysqli_fetch_assoc($res_classes)['total_classes'] ?? 0;

// 3. ගුරුවරයාගේ පන්තිවලට සහභාගී වන මුළු සිසුන් ගණන ලබා ගැනීම (My Students)
// DISTINCT භාවිතා කරන්නේ එක් සිසුවෙක් පන්ති කිහිපයකට ආවත් එක් අයෙක් ලෙස ගණනය කිරීමටයි
$res_students = mysqli_query($conn, "SELECT COUNT(DISTINCT e.student_id) AS total_students 
                                     FROM enrollments e 
                                     JOIN classes c ON e.class_id = c.class_id 
                                     WHERE c.teacher_id = $teacher_id");
$total_students = mysqli_fetch_assoc($res_students)['total_students'] ?? 0;

// 4. අද සහ ඉදිරියට පැවැත්වීමට නියමිත පරීක්ෂණ ගණන ලබා ගැනීම (Upcoming Tests)
$res_tests = mysqli_query($conn, "SELECT COUNT(*) AS total_tests 
                                  FROM tests t 
                                  JOIN classes c ON t.class_id = c.class_id 
                                  WHERE c.teacher_id = $teacher_id AND t.test_date >= CURDATE()");
$total_tests = mysqli_fetch_assoc($res_tests)['total_tests'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Smart Tuition</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    
    <div class="dashboard-container">

        <aside class="sidebar" style="background-color: #0F172A;">
            <h2>Smart Teacher</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="students.php">My Students</a></li>
                <li><a href="classes.php">Assigned Classes</a></li>
                <li><a href="manage_tests.php">Upcoming Tests</a></li>
                <li><a href="materials.php">Upload Materials</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <button class="toggle-btn" onclick="toggleSidebar(event)">☰ Menu</button>
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Teacher'); ?>!</h1>
                <p>Your overview</p>
            </header>

            <div class="cards-container">
                <div class="card">
                    <h3>Assigned Classes</h3>
                    <h2><?php echo $total_classes; ?></h2>
                </div>
                <div class="card">
                    <h3>My Students</h3>
                    <h2><?php echo $total_students; ?></h2>
                </div>
                <div class="card">
                    <h3>Upcoming Tests</h3>
                    <h2><?php echo $total_tests; ?></h2>
                </div>
            </div>
        </main>
        <script src="../assets/js/dashboard.js"></script>
    </div>

</body>
</html>