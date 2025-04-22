<?php
// advisee-info.php
// Returns a list of advisees for the currently logged-in advisor

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'advisor') {
    http_response_code(401);
    exit(json_encode(['error' => 'Not authenticated as advisor']));
}

$advisorId = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare(
        "SELECT u.UserID AS StudentID, u.FirstName, u.LastName, u.Email,
                s.MajorMinor, s.Course_year, s.GPA
         FROM Advisees a
         JOIN Students s ON s.StudentID = a.StudentID
         JOIN Users u ON u.UserID = s.StudentID
         WHERE a.AdvisorID = ?"
    );
    $stmt->execute([$advisorId]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($students);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
