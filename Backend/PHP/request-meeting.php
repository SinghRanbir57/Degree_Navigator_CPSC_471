<?php

// this endpoint allows only students to request a meetin gwith their asdsigned advisor,
// get, fethc advisor id based on full name
// create a new pendin meeting request.
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';


// validate the session must be a student.
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') 
{
    http_response_code(401);

    exit(json_encode(['error' => 'Not authenticated']));
}

$userId = (int)$_SESSION['user_id'];

// GET request: lookup advisorId

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['advisorName'])) {
    $advisorName = trim($_GET['advisorName']);
    // query advsiors id from teh database, to verify.
    $stmt = $pdo->prepare("
        SELECT a.AdvisorID
        FROM Advisors a
        JOIN Users u ON u.UserID = a.AdvisorID
        WHERE CONCAT(u.FirstName, ' ', u.LastName) = ?
    ");
    $stmt->execute([$advisorName]);
    $advisor = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['advisorId' => $advisor['AdvisorID'] ?? null]);
    
    exit;
}

// POST request, submit meeting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Debug payload to file
    file_put_contents(__DIR__ . "/request-debug.json", json_encode($data, JSON_PRETTY_PRINT));
    // validate any required fields.
    if (!isset($data['advisorId'], $data['date'], $data['time'])) {
        http_response_code(400);
        exit(json_encode([
            'error' => 'Missing required fields',
            'received' => $data
        
        ]));
    }

    //check that student is actually assigned to advisor
    $check = $pdo->prepare("SELECT 1 FROM Advisees WHERE AdvisorID = ? AND StudentID = ?");
    $check->execute([(int)$data['advisorId'], $userId]);

    if ($check->rowCount() === 0) {
        http_response_code(403);
        
        exit(json_encode(['error' => 'You are not assigned to this advisor']));
    }

    //insert into meetings table
    $stmt = $pdo->prepare("
        INSERT INTO Meetings (id, advisorId, studentId, studentName, date, time, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    //insert meeting request into the database. !!!
    $id = bin2hex(random_bytes(8));
    $studentName = $data['studentName'] ?? '';
    $stmt->execute([
        $id,
        (int)$data['advisorId'],
        $userId,
        $studentName,
        $data['date'],
        $data['time']
    ]);

    echo json_encode(['success' => true, 'id' => $id]);
    exit;
}
//fallback for unsupported http 
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
