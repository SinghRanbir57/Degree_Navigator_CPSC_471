<?php
/**
 * dashboard.php — Here we have dashboard.php, it redirects users to the appropriate static HTMLd dashboard.
 */

session_start();

require_once 'db.php';

/* --------- Session check ---------  */
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ../../Frontend/html/joint/login.html');
    exit;
}

$role = $_SESSION['role'];

/*  --------- Redirect based on the given role --------- */
if ($role === 'student' ) {
    header('Location: /Frontend/html/student/student-dashboard.php');
    exit;
    
}

if ($role === 'advisor') {
    header('Location: /Frontend/html/advisor/advisor-dashboard.html');
    exit;
}

/* --------- Catch any unexpected roles --------- */
http_response_code(403);

echo "Forbidden: Unknown role.";
exit;
?>
