<?php
require __DIR__ . '/../db.php';

$email = isset($argv[1]) ? $argv[1] : 'admin@xmail.com';
$passwordToCheck = isset($argv[2]) ? $argv[2] : '1q2w3e';

$sql = "SELECT id, email, password, role FROM users WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if (! $result) {
    echo "Query error: " . $conn->error . "\n";
    exit(1);
}

if ($result->num_rows === 0) {
    echo "No user found with email: $email\n";
    exit(0);
}

$user = $result->fetch_assoc();
echo "User row:\n";
print_r($user);
echo "\nPassword verify with provided password ('$passwordToCheck'):\n";
var_export(password_verify($passwordToCheck, $user['password']));
echo "\n";

// Clean up connection
$conn->close();

?>
