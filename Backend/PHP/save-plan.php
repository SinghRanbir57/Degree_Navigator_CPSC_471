<?php
session_start();
require_once 'db.php';


//first we check for a valid session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

//parse the current incoming JSON
$data = json_decode(file_get_contents("php://input" ), true);

// extract values
$semester = $data['semester'] ?? null;

$year     = $data['year'] ?? null;

$courses  = $data['courses'] ?? [];

// vaalidate
if (!$semester || !$year || !is_array($courses) || empty($courses)) {
    http_response_code(400);
    echo json_encode([

        "error" => "Missing or invalid data",
        "semester" => $semester,
        "year" => $year,
        "courses" => $courses

    ]);
    exit;
}

try {
    //get or create SemesterID
    $stmt = $pdo->prepare("SELECT SemesterID FROM Semesters WHERE Term = ? AND Year = ?");
    $stmt->execute([$semester, $year]);
    $semesterId = $stmt->fetchColumn();

    //check the type
    if (!$semesterId) {
        $insertSemester = $pdo->prepare("INSERT INTO Semesters (Term, Year) VALUES (?, ?)");
        $insertSemester->execute([$semester, $year]);
        $semesterId = $pdo->lastInsertId();
    }

    //save the degree Plan
    $studentId = $_SESSION['user_id'];
    $planStmt = $pdo->prepare("INSERT INTO DegreePlan (StudentID, SemesterID) VALUES (?, ?)");
    $planStmt->execute([$studentId, $semesterId]);

    echo json_encode(["success" => true]);

    //catch any erros
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error", "details" => $e->getMessage()]);
}
?>
