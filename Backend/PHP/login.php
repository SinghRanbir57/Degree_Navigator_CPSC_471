<?php
/**
 * Here we have login.php, it processes the login form and drops the user onto the dashboard.php.
 * The login form should point its action to this file and POST:
 *          username, password, user_type
 */

session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    header('Location: ../../Frontend/html/login.html');
    exit;
}

//sanitisation / trimming

$username  = trim($_POST['username']  ?? '');

$password  = trim($_POST['password']  ?? '');

$user_type = trim($_POST['user_type'] ?? '');
//^^^
if ($username === '' || $password === '' ||
    !in_array( $user_type, ['student', 'advisor'], true)) {
    fail('Please fill in all fields.');
}

/*
   Look up the user:  We stiill store plain‑text passwords per spec
   (upgrade to password_hash()/password_verify()).
 */

$stmt = $pdo->prepare(

    'SELECT UserID, Password, Role
       FROM Users
      WHERE Username = :u AND Role = :r
      LIMIT 1'
);
$stmt->execute(['u' => $username, 'r' => $user_type]);
$user = $stmt->fetch();
//check if equivalent
if (!$user || $password !== $user['Password']) {
    fail('Invalid credentials.');
}

/*  Success – stash session + redirect  */
//call
$_SESSION['user_id'] = (int)$user['UserID'];
$_SESSION['username'] = $username;
$_SESSION['role']     = $user_type;

header('Location: dashboard.php');
exit;

/*  Helpers  */
function fail(string $msg): void
{
    // if fail
    echo "<script>alert('$msg'); window.history.back();</script>";
    exit;
}
?>
