<?php
$host = "127.0.0.1:3307";
$db_user = "root";       
$db_pass = "root";           
$db_name = "smart_tuition"; 

// Database එකට සම්බන්ධ වීම
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// සම්බන්ධතාවය අසාර්ථක වුවහොත් දෝෂය පෙන්වීම
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>