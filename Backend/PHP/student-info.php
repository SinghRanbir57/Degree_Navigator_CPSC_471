<?php

// this endpiont returns a detailed profile for the logged in student,
// includes personal info, academic standing, etc.
session_start();
header('Content-Type: application/json');
require_once 'db.php';


//authorization check
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);

    exit(json_encode(['error' => 'Unauthorized']));
}

//we're in
$userId = $_SESSION['user_id'];

try {
    // pull full student profile details with program and year
    $stmt = $pdo->prepare("
        SELECT 
            u.UserID AS StudentID,
            u.FirstName, u.LastName, u.Email, u.PhoneNumber, u.Address,
            u.BirthDate, u.SIN, u.Username,
            s.MajorMinor, s.GPA, s.Course_year
        FROM Users u
        JOIN Students s ON u.UserID = s.StudentID
        WHERE u.UserID = ?
    ");


    $stmt->execute([$userId] );

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    // check if found.
    if (!$profile) {
        throw new Exception("Student not found");
    }

    // Fetch enrolled courses
    $stmt = $pdo->prepare("
        SELECT c.CourseCode, c.CourseName, e.Grade
        FROM Enrollment e
        JOIN Courses c ON e.CourseID = c.CourseID
        WHERE e.StudentID = ?
    ");
    $stmt->execute([$userId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'profile' => $profile,
        'courses' => $courses
    ]);
    //catch any final errors.
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode(['error' => $e->getMessage()]);

}
?>
