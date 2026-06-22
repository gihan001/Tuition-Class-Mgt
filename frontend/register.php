<?php
// 1. Errors බ්‍රවුසරයේ පෙන්වීමට On කිරීම (බග් එකක් ආවොත් බලාගන්න)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. ඔබගේ File Structure එකට අනුව නිවැරදි Path එක හරහා Database සම්බන්ධ කිරීම
require '../db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// පරිශීලකයා Register බොත්තම එබූ විට පමණක් මෙය ක්‍රියාත්මක වේ
if (isset($_POST['register_btn'])) {
    
    // Form එකෙන් එවන දත්ත විචල්‍යයන් වලට ලබාගැනීම
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $class_name = trim($_POST['class_name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    
    // ආරක්ෂාව සඳහා Password එක Hash කිරීම
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Step 1: Users වගුවට දත්ත ඇතුළත් කිරීම
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $password, $role);
        $stmt->execute();

        // Step 2: සාර්ථකව Insert වූ පසු අලුත් User ID එක ලබා ගැනීම
        $user_id = $stmt->insert_id; 

        // Step 3: ශිෂ්‍යයෙක් (Student) නම් පමණක් Students Table එකට දැමීම
        if ($role === 'student') {
            if ($class_name === '' || $contact === '') {
                echo "<script>alert('Please provide class name and contact number for student registration.'); window.history.back();</script>";
                exit;
            }

            $student_stmt = $conn->prepare("INSERT INTO students (user_id, class_name, contact) VALUES (?, ?, ?)");
            $student_stmt->bind_param("iss", $user_id, $class_name, $contact);
            $student_stmt->execute();
        }

        // Step 4: ගුරුවරයෙක් (Teacher) නම් පමණක් Teachers Table එකට දැමීම
        if ($role === 'teacher') {
            if ($subject === '') {
                echo "<script>alert('Please provide subject for teacher registration.'); window.history.back();</script>";
                exit;
            }

            $teacher_stmt = $conn->prepare("INSERT INTO teachers (user_id, name, subject) VALUES (?, ?, ?)");
            $teacher_stmt->bind_param("iss", $user_id, $full_name, $subject);
            $teacher_stmt->execute();
        }

        // සාර්ථක වුවහොත් පණිවිඩයක් පෙන්වා Login පිටුවට යැවීම
        echo "<script>
                alert('Registration Successful! Please login.');
                window.location.href = 'login.php';
              </script>";
        exit;

    } catch (mysqli_sql_exception $e) {
        // එකම Email එක දෙවරක් ඇතුළත් කළහොත් (Duplicate Entry Error)
        if ((int) $e->getCode() === 1062) {
            echo "<script>alert('Error: Email already exists. Please use a different email.'); window.history.back();</script>";
        } else {
            echo "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Tuition</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body class="auth-page">

    <nav>
        <h2>Smart Tuition</h2>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>

    <section class="login-container">
        <form action="register.php" method="POST" class="login-form">

            <h2>Create an Account</h2>

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>
            
            <div class="input-group">
                <label>Register As</label>
                <select name="role" id="roleSelect" onchange="toggleFields()" required>
                    <option value="">-- Select Role --</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <div id="studentFields" style="display: none;">
                <div class="input-group">
                    <label>Class Name</label>
                    <input type="text" name="class_name" placeholder="e.g. Mathematics - Grade 10">
                </div>
                <div class="input-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact" placeholder="Enter contact number">
                </div>
            </div>

            <div id="teacherFields" style="display: none;">
                <div class="input-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Mathematics">
                </div>
            </div>

            <button type="submit" name="register_btn" class="primary-button">Register</button>
            
            <p class="auth-redirect">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </section>

    <footer>
        <p>&copy; 2026 Smart Tuition Class Management System.</p>
    </footer>

    <script src="assets/js/registration.js"></script>
</body>
</html>
