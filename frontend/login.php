<?php
// Session එකක් ආරම්භ කිරීම (ලොග් වන පරිශීලකයාගේ විස්තර පිටු අතර මතක තබා ගැනීමට)
session_start();
require '../db.php';

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // පරිශීලකයා ඇතුළත් කළ Email එක දත්ත සමුදායේ තිබේදැයි පරීක්ෂා කිරීම
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email එක හමු වූයේ නම්, එම පේළියේ දත්ත ලබා ගැනීම
        $user = $result->fetch_assoc();
        
        // ඇතුළත් කළ Password එක සහ Database එකේ ඇති Hash කළ Password එක සැසඳීම
        if (password_verify($password, $user['password'])) {
            
            // Session එක තුළ පරිශීලකයාගේ විස්තර තබා ගැනීම
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            // Role එක අනුව අදාළ Dashboard එකට පරිශීලකයා යොමු කිරීම (Redirect)
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'teacher') {
                header("Location: teacher/dashboard.php");
            } elseif ($user['role'] == 'student') {
                header("Location: student/dashboard.php");
            }
            exit(); // කේතය ක්‍රියාත්මක වීම මෙතනින් නතර කිරීම
            
        } else {
            echo "<script>alert('Invalid Password! Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email!');</script>";
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
<body>

    <nav>
        <h2>Smart Tuition</h2>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>

    <section class="login-container">
        <form action="login.php" method="POST" class="login-form" onsubmit="return validateLogin(event)">
            <h2>User Login</h2>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group password-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" data-target="password" aria-label="Show password">👁️</button>
                </div>
            </div>

            <button type="submit" name="login_btn" class="primary-button">Login</button>

            <a href="#" class="forgot-password">Forgot password?</a>

            <p class="auth-redirect">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </section>

    <footer>
        <p>&copy; 2026 Smart Tuition Class Management System.</p>
    </footer>
    <script src="assets/js/validation.js"></script>
</body>
</html>