<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Frontend/html/login.html');
    exit;
}

// Grab POST data
$username  = trim($_POST['username'] ?? '');
$password  = trim($_POST['password'] ?? '');
$user_type = trim($_POST['user_type'] ?? '');

// Validate input
if ($username === '' || $password === '' || !in_array($user_type, ['student', 'advisor'], true)) {
    echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
    exit;
}

// Prepare and execute query
$stmt = $pdo->prepare('SELECT UserID, Password, Role FROM Users WHERE Username = :u AND Role = :r LIMIT 1');
$stmt->execute(['u' => $username, 'r' => $user_type]);
$user = $stmt->fetch();

// Check user existence and password
if (!$user || $password !== $user['Password']) {
    echo "<script>alert('Invalid credentials.'); window.history.back();</script>";
    exit;
}

// Set session and redirect
$_SESSION['user_id'] = (int)$user['UserID'];
$_SESSION['username'] = $username;
$_SESSION['role']     = $user_type;

header('Location: dashboard.php');
exit;
?>
