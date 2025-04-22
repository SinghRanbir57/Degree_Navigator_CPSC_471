<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$userId = $_SESSION['user_id'];

try {
    // Pull full student profile details with program and year
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


    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        throw new Exception("Student not found");
    }

    // Add derived fields
    $profile['Program'] = "B.Sc. in Computer Science";  // You can make this dynamic later
    $profile['Major'] = $profile['MajorMinor'];
    $profile['Minor'] = "Mathematics"; // You could make this dynamic via schema change
    $profile['Year'] = $profile['Course_year'];
    $profile['Semester'] = "Winter 2025"; // You could also pull this from latest DegreePlan

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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
