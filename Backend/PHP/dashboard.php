<?php
// Here we have the script that sreves as a role based redirect handler.
// After a user logs in it redirects them to their appropriate dashboard.
// student or advisor.

session_start();

require_once 'db.php';

// check the seession
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header('Location: ../../Frontend/html/joint/login.html');
    exit;
}
// extract role from sess
$role = $_SESSION['role'];

/*   Redirect based on the given role  */
if ($role === 'student' ) {
    header('Location: /Frontend/html/student/student-dashboard.php');
    exit;
    
}
// if user is an advisor, redirect them
if ($role === 'advisor') {
    header('Location: /Frontend/html/advisor/advisor-dashboard.php');
    exit;
}

/* --------- Catch any unexpected roles --------- */
http_response_code(403);

echo "Forbidden: Unknown role.";
exit;
?>
