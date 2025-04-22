<?php
/**
 * schedule-meeting.php  (sits in Backend/PHP/)
 * --------------------------------------------------------------
 * Flat‑file meeting API for students & advisors.
 *
 *   •  GET      → list meetings relevant to the logged‑in user
 *   •  POST     → create / overwrite (status auto‑set)
 *   •  PATCH    → advisor updates a request  (accept / decline / edit)
 *   •  DELETE   → remove one of your own meetings
 *
 *   Storage : meetings/meetings.json    [{…}, …]
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'], $_SESSION['role'])
    || !in_array($_SESSION['role'], ['student', 'advisor'], true)) {
    http_response_code(401);
    exit(json_encode(['error' => 'Not authenticated']));
}

$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['role'];  // "student" | "advisor"

$dbFile = __DIR__ . '/meetings/meetings.json';
if (!file_exists($dbFile)) file_put_contents($dbFile, '[]');

$read  = fn() => json_decode(file_get_contents($dbFile), true) ?: [];
$write = fn($data) => file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT));

/* ------------------------------------------------ GET */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $all = $read();

    if ($userRole === 'advisor') {
        $own = array_filter($all, fn($m) => $m['advisorId'] == $userId && $m['status'] !== 'pending');
        $requests = array_filter($all, fn($m) => $m['advisorId'] == $userId && $m['status'] === 'pending');

        echo json_encode(['own' => array_values($own), 'requests' => array_values($requests)]);
        exit;
    }

    if ($userRole === 'student') {
        $myMeetings = array_filter($all, fn($m) => $m['studentId'] == $userId);
        echo json_encode(['own' => array_values($myMeetings)]);
        exit;
    }

    http_response_code(403);
    exit(json_encode(['error' => 'Unknown role']));
}

/* ------------------------------------------------ POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['advisorId'], $data['studentId'], $data['date'], $data['time'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing required fields']));
    }

    $all = $read();
    $meeting = [
        'id'          => uniqid(),
        'advisorId'   => (int)$data['advisorId'],
        'studentId'   => (int)$data['studentId'],
        'studentName' => $data['studentName'] ?? '',
        'date'        => $data['date'],
        'time'        => $data['time'],
        'status'      => 'pending'
    ];

    $all[] = $meeting;
    $write($all);
    echo json_encode(['success' => true]);
    exit;
}

/* ------------------------------------------------ PATCH */
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing meeting id']));
    }

    $all = $read();
    $updated = false;

    foreach ($all as &$meeting) {
        if ($meeting['id'] === $data['id']) {
            if ($userRole === 'advisor' && $meeting['advisorId'] == $userId) {
                if (isset($data['status'])) {
                    $meeting['status'] = $data['status'];
                }
                if (isset($data['date'])) {
                    $meeting['date'] = $data['date'];
                }
                if (isset($data['time'])) {
                    $meeting['time'] = $data['time'];
                }
                $updated = true;
            }
            break;
        }
    }

    if (!$updated) {
        http_response_code(403);
        exit(json_encode(['error' => 'Forbidden or not found']));
    }

    $write($all);
    echo json_encode(['success' => true]);
    exit;
}

/* ------------------------------------------------ DELETE */
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'Missing meeting id']));
    }

    $all = $read();
    $new = [];
    $deleted = false;

    foreach ($all as $meeting) {
        if ($meeting['id'] === $data['id']) {
            if (($userRole === 'student' && $meeting['studentId'] == $userId)
             || ($userRole === 'advisor' && $meeting['advisorId'] == $userId)) {
                $deleted = true;
                continue;
            }
        }
        $new[] = $meeting;
    }

    if (!$deleted) {
        http_response_code(403);
        exit(json_encode(['error' => 'Forbidden or not found']));
    }

    $write($new);
    echo json_encode(['success' => true]);
    exit;
}

/* ------------------------------------------------ Default */
http_response_code(405);
exit(json_encode(['error' => 'Method not allowed']));
