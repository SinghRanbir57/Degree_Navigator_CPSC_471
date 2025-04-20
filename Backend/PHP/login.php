<?php
session_start();
include("db.php"); // You’ll create this file to connect to MySQL

// Grab posted form data
$username = $_POST['username'];
$password = $_POST['password'];
$user_type = $_POST['user_type']; // 'student' or 'advisor'

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND user_type = ?");
$stmt->bind_param("ss", $username, $user_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Check password (plain text for now — you’ll upgrade to hashed passwords later)
    if ($password === $user['password']) {
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $user_type;

        // Redirect based on user type
        if ($user_type === "student") {
            header("Location: ../Frontend/html/student-dashboard.html");
        } else {
            header("Location: ../Frontend/html/advisor-dashboard.html");
        }
        exit();
    }
}

// If we reach here, login failed
echo "<script>alert('Invalid credentials'); window.history.back();</script>";
?>