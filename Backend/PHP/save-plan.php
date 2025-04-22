<?php
// save-plans.php

ini_set('display_errors',       1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';  // must define $pdo as a PDO instance

// 1) authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// 2) parse JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

$semester = $data['semester'] ?? null;
$year     = $data['year']     ?? null;
$courses  = $data['courses']  ?? [];

if (!$semester || !$year || !is_array($courses) || count($courses) === 0) {
    http_response_code(400);
    echo json_encode([
        "error"    => "Missing or invalid data",
        "received" => $data
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 3) find or create the semester
    $stmt = $pdo->prepare("SELECT SemesterID FROM Semesters WHERE Term = ? AND Year = ?");
    $stmt->execute([$semester, $year]);
    $semesterId = $stmt->fetchColumn();

    if (!$semesterId) {
        $stmt = $pdo->prepare("INSERT INTO Semesters (Term, Year) VALUES (?, ?)");
        $stmt->execute([$semester, $year]);
        $semesterId = $pdo->lastInsertId();
    }

    // 4) insert degree plan header
    $studentId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        INSERT INTO DegreePlan (StudentID, SemesterID)
        VALUES (?, ?)
    ");
    $stmt->execute([$studentId, $semesterId]);
    $planId = $pdo->lastInsertId();

    // 5) link each course to the plan
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

    // 6) success response
    echo json_encode([
        "success" => true,
        "plan_id" => $planId
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "error"   => "Server error",
        "message" => $e->getMessage()
    ]);
}
