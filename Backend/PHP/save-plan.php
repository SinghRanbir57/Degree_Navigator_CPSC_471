<?php
// save-plans.php
// allows for student to save a degree plan for a specific semster,
// validates input, handles semsester look up/creation,
// links selected courses to that plan..
ini_set('display_errors',       1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';  // must define $pdo as a PDO instance

// authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// parse the JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

$semester = $data['semester'] ?? null;
$year     = $data['year']     ?? null;
$courses  = $data['courses']  ?? [];

//check for missing or invalid vals
if (!$semester || !$year || !is_array($courses) || count($courses) === 0) {
    http_response_code(400);
    
    echo json_encode([
        "error"    => "Missing or invalid data",
        "received" => $data
    ]);

    exit;

}

//start db transaction
try {
    $pdo->beginTransaction();

    //find or create the semester
    $stmt = $pdo->prepare("SELECT SemesterID FROM Semesters WHERE Term = ? AND Year = ?");
    $stmt->execute([$semester, $year]);
    $semesterId = $stmt->fetchColumn();

    if (!$semesterId) {
        $stmt = $pdo->prepare("INSERT INTO Semesters (Term, Year) VALUES (?, ?)");
        
        $stmt->execute([$semester, $year]);
        
        $semesterId = $pdo->lastInsertId();
    }

    // insert degree plan header
    $studentId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        INSERT INTO DegreePlan (StudentID, SemesterID)
        VALUES (?, ?)

    ");
    $stmt->execute([$studentId, $semesterId]);
    $planId = $pdo->lastInsertId();

    // link each course to the plan
    $getCourseId   = $pdo->prepare("SELECT CourseID FROM Courses WHERE CourseCode = ?");
    $insPlanCourse = $pdo->prepare("
        INSERT INTO DegreePlanCourses (PlanID, CourseID)
        VALUES (?, ?)
    ");

    foreach ($courses as $code) {
        $getCourseId->execute([$code]);
        $courseId = $getCourseId->fetchColumn();
        if ($courseId) {
            $insPlanCourse->execute([$planId, $courseId]);
        } else {
            // optional: collect unknown codes to report back
            error_log("Unknown course code “{$code}” for student {$studentId}");
        }
    }

    $pdo->commit();

    // success response
    echo json_encode([
        "success" => true,
        "plan_id" => $planId
    ]);

    //catch any fail errors.
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "error"   => "Server error",
        "message" => $e->getMessage()
    ]);
}
