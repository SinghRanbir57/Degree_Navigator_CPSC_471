<?php

// login handler,
// script processes logoin requests from  the login form.
// validates input, checks credentials, and sets session data upon any succesful login.
session_start();
require_once 'db.php';

// only allow post requests to this script
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Frontend/html/login.html');
    exit;
}

//grab psot data from form, use null operator 
$username  = trim($_POST['username'] ?? '');
$password  = trim($_POST['password'] ?? '');
$user_type = trim($_POST['user_type'] ?? '');

//validate input
if ($username === '' || $password === '' || !in_array($user_type, ['student', 'advisor'], true)) {
    echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
    exit;
}

//prepare and execute query
$stmt = $pdo->prepare('SELECT UserID, Password, Role FROM Users WHERE Username = :u AND Role = :r LIMIT 1');

$stmt->execute(['u' => $username, 'r' => $user_type]);
$user = $stmt->fetch();

//check user existence and password
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
