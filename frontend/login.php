<?php
// 1. Session එකක් ආරම්භ කිරීම (ලොග් වන පරිශීලකයාගේ විස්තර පිටු අතර මතක තබා ගැනීමට)
session_start();

// 2. Errors බ්‍රවුසරයේ පෙන්වීමට On කිරීම
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3. නිවැරදි Path එක හරහා Database සම්බන්ධ කිරීම
require '../db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// පරිශීලකයා Login බොත්තම එබූ විට පමණක් මෙය ක්‍රියාත්මක වේ
if (isset($_POST['login_btn'])) {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Step 1: ඇතුළත් කළ Email එකට අදාළව users වගුවෙන් දත්ත සෙවීම (Prepared Statement)
        $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        // ප්‍රථිඵලය ලබා ගැනීම
        $result = $stmt->get_result();

        // Email එක සහිත පරිශීලකයෙකු හමු වුවහොත්
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Step 2: ඇතුළත් කළ Password එක සහ Database එකේ ඇති Hash කළ Password එක සැසඳීම
            if (password_verify($password, $user['password'])) {
                
                // Session එක තුළ පරිශීලකයාගේ විස්තර තබා ගැනීම
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                // Step 3: Role එක අනුව අදාළ Dashboard එකට පරිශීලකයා යොමු කිරීම (Redirect)
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] === 'teacher') {
                    header("Location: teacher/dashboard.php");
                } elseif ($user['role'] === 'student') {
                    header("Location: student/dashboard.php");
                }
                exit(); // කේතය ක්‍රියාත්මක වීම මෙතනින් නතර කිරීම
                
            } else {
                // මුරපදය වැරදි නම්
                echo "<script>alert('Invalid Password! Please try again.'); window.history.back();</script>";
                exit();
            }
        } else {
            // Email එක දත්ත සමුදායේ නොමැති නම්
            echo "<script>alert('No user found with this email!'); window.history.back();</script>";
            exit();
        }

    } catch (mysqli_sql_exception $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Tuition</title>
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
        <!-- Form එක action="login.php" ලෙස POST method එකෙන් සකසා ඇත -->
        <form action="login.php" method="POST" class="login-form">

            <h2>Login to Your Account</h2>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <!-- බොත්තමේ name එක login_btn ලෙස තබා ඇත -->
            <button type="submit" name="login_btn" class="primary-button">Login</button>
            
            <p class="auth-redirect">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </section>

    <footer>
        <p>&copy; 2026 Smart Tuition Class Management System.</p>
    </footer>

</body>
</html>
