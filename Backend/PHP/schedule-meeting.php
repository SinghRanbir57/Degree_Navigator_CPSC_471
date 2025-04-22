<?php
/**
 * schedule-meeting.php  (Backend/PHP/)
 * --------------------------------------------------------------
 * MySQL‑backed meeting API for students & advisors.
 *
 *   • GET      → list meetings relevant to the logged‑in user
 *   • POST     → create new meeting request (status auto‑set)
 *   • PATCH    → advisor updates a request (accept/decline/edit)
 *   • DELETE   → remove one of your own meetings
 *
 *   Storage : MySQL table `Meetings`
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';  // Defines $pdo as PDO

if (!isset($_SESSION['user_id'], $_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'advisor'], true)) {
    http_response_code(401);
    exit(json_encode(['error' => 'Not authenticated']));
}

$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['role'];  // "student" | "advisor"

switch ($_SERVER['REQUEST_METHOD']) {

  case 'GET':
    if ($userRole === 'advisor') {
        $ownStmt = $pdo->prepare(
            "SELECT * FROM Meetings 
             WHERE advisorId = ? AND status <> 'pending'"
        );
        $reqStmt = $pdo->prepare(
            "SELECT * FROM Meetings 
             WHERE advisorId = ? AND status = 'pending'"
        );
        $ownStmt->execute([$userId]);
        $reqStmt->execute([$userId]);
        echo json_encode([
            'own'      => $ownStmt->fetchAll(PDO::FETCH_ASSOC),
            'requests' => $reqStmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT * FROM Meetings WHERE studentId = ?"
        );
        $stmt->execute([$userId]);
        echo json_encode(['own' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }
    break;

  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    file_put_contents("debug.json", json_encode($data, JSON_PRETTY_PRINT));

    if (!isset($data['advisorId'], $data['studentId'], $data['studentName'], $data['date'], $data['time'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing required fields']));
    }

    // Validate student ID and name match
    $verify = $pdo->prepare(
        "SELECT FirstName, LastName FROM Users WHERE UserID = ? AND Role = 'student'"
    );
    $verify->execute([(int)$data['studentId']]);
    $user = $verify->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        exit(json_encode(['error' => 'Student not found']));
    }

    $fullName = $user['FirstName'] . ' ' . $user['LastName'];
    if (trim($data['studentName']) !== $fullName) {
        http_response_code(400);
        exit(json_encode(['error' => 'Student name does not match ID']));
    }

    // Insert meeting as accepted right away
    $stmt = $pdo->prepare(
        "INSERT INTO Meetings
         (id, advisorId, studentId, studentName, date, time, status)
         VALUES (?, ?, ?, ?, ?, ?, 'accepted')"
    );
    $id = bin2hex(random_bytes(8));
    $stmt->execute([
        $id,
        (int)$data['advisorId'],
        (int)$data['studentId'],
        $data['studentName'],
        $data['date'],
        $data['time']
    ]);

    echo json_encode(['success' => true, 'id' => $id]);
    break;

  case 'PATCH':
    if ($userRole !== 'advisor') {
        http_response_code(403);
        exit(json_encode(['error' => 'Forbidden']));
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $fields = [];
    $params = [];

    foreach (['status', 'date', 'time'] as $f) {
        if (isset($data[$f])) {
            $fields[] = "{$f} = ?";
            $params[] = $data[$f];
        }
    }

    if (!$fields || !isset($data['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing fields to update']));
    }

    $params[] = $data['id'];
    $params[] = $userId;

    $sql = "UPDATE Meetings SET " . implode(', ', $fields) . " WHERE id = ? AND advisorId = ?";
    $stmt = $pdo->prepare($sql);
    $updated = $stmt->execute($params);

    if (!$updated || $stmt->rowCount() === 0) {
        http_response_code(403);
        exit(json_encode(['error' => 'Forbidden or not found']));
    }

    echo json_encode(['success' => true]);
    break;

  case 'DELETE':
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing meeting id']));
    }

    $sql = "DELETE FROM Meetings WHERE id = ? AND (studentId = ? OR advisorId = ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['id'], $userId, $userId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(403);
        exit(json_encode(['error' => 'Forbidden or not found']));
    }

    echo json_encode(['success' => true]);
    break;

  default:
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}