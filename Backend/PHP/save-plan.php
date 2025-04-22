<?php
ini_set('display_errors',       1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once 'db.php';

// First we check for a valid session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Parse the incoming JSON
$data = json_decode(file_get_contents("php://input"), true);

// Extract values
$semester = $data['semester'] ?? null;
$year     = $data['year'] ?? null;
$courses  = $data['courses'] ?? [];

// Validate
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
    // Get or create SemesterID
    $stmt = $pdo->prepare("SELECT SemesterID FROM Semesters WHERE Term = ? AND Year = ?");
    $stmt->execute([$semester, $year]);
    $semesterId = $stmt->fetchColumn();

    if (!$semesterId) {
        $insertSemester = $pdo->prepare("INSERT INTO Semesters (Term, Year) VALUES (?, ?)");
        $insertSemester->execute([$semester, $year]);
        $semesterId = $pdo->lastInsertId();
    }

    // Save the Degree Plan
    $studentId = $_SESSION['user_id'];
    $planStmt = $pdo->prepare("INSERT INTO DegreePlan (StudentID, SemesterID) VALUES (?, ?)");
    $planStmt->execute([$studentId, $semesterId]);


    // 1) insert the plan header
    $planStmt = $pdo->prepare("
    INSERT INTO DegreePlan (StudentID, SemesterID)
    VALUES (?, ?)
    ");
    $planStmt->execute([$studentId, $semesterId]);

    // 2) grab the new PlanID
    $planId = $pdo->lastInsertId();

    // 3) prepare statements for looking up & inserting
    $getCourseId = $pdo->prepare("
    SELECT CourseID 
    FROM Courses 
    WHERE CourseCode = ?
    ");
    $insPlanCourse = $pdo->prepare("
    INSERT INTO DegreePlanCourses (PlanID, CourseID) 
    VALUES (?, ?)
    ");

    // 4) loop through each course code and save it
    foreach ($courses as $courseCode) {
    // look up the internal CourseID
    $getCourseId->execute([$courseCode]);
    $courseId = $getCourseId->fetchColumn();

    if ($courseId) {
        // insert the plan→course link
        $insPlanCourse->execute([$planId, $courseId]);
    } else {
        // you could log or handle "unknown course code" here
        error_log("Unknown course code: {$courseCode} for student {$studentId}");
    }
    }


    // OPTIONAL: Write to a log file
    $logData = "Student ID: $studentId\nSemester: $semester $year\nCourses:\n - " . implode("\n - ", $courses) . "\n\n";
    file_put_contents("plans/semester_plan_user{$studentId}.txt", $logData, FILE_APPEND);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Server error", "details" => $e->getMessage()]);
}
?>
