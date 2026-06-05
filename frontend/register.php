<?php
// Database connection ගොනුව සම්බන්ධ කරගැනීම
require '../db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// පරිශීලකයා Register බොත්තම එබූ විට පමණක් මෙය ක්‍රියාත්මක වේ
if (isset($_POST['register_btn'])) {
    
    // Form එකෙන් එවන දත්ත විචල්‍යයන් (Variables) වලට ලබාගැනීම
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // ආරක්ෂාව සඳහා Password එක Hash කිරීම (සඟවා ගබඩා කිරීම)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Database එකට දත්ත ඇතුළත් කිරීමේ Prepared Statement
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $password, $role);
        $stmt->execute();

        // සාර්ථක නම් පණිවිඩයක් පෙන්වා Login පිටුවට යැවීම
        echo "<script>
                alert('Registration Successful! Please login.');
                window.location.href = 'login.php';
              </script>";
    } catch (mysqli_sql_exception $e) {
        // එකම Email එක තිබේ නම් friendly message එකක් පෙන්වීම
        if ((int) $e->getCode() === 1062) {
            echo "<script>alert('Error: Email already exists. Please use a different email.');</script>";
        } else {
            echo "<script>alert('Error: Something went wrong while registering the user.');</script>";
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
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="register_btn" class="primary-button">Register</button>
        </form>
    </section>

    <footer>
        <p>&copy; 2026 Smart Tuition Class Management System.</p>
    </footer>

</body>

</html>