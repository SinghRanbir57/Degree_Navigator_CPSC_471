<?php
/**
 * schedule-meeting.php
 * ----------------------------------------------------------
 * Simple flat‑file meeting scheduler for BOTH students & advisors.
 *  • POST  → save (create or overwrite) a meeting
 *  • GET   → list meetings belonging to current session user
 * ----------------------------------------------------------
 *   Storage : meetings.json (same folder)  [{…}, …]
 *   Fields  : ownerRole (student|advisor), ownerId (int), name, id, date, time
 */

session_start();
header('Content-Type: application/json');

// ---------- 1. Security / session checks ----------
if (!isset($_SESSION['user_id'], $_SESSION['role'])
    || !in_array($_SESSION['role'], ['student', 'advisor'], true)) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}

$ownerRole = $_SESSION['role'];
$ownerId   = (int)$_SESSION['user_id'];

$dbFile = __DIR__ . '/meetings/meetings.json';
if (!file_exists($dbFile)) {
    file_put_contents($dbFile, '[]');               // bootstrap file
}

// ---------- 2. Helper to read / write ----------
function readDB($file)   { return json_decode(file_get_contents($file), true) ?: []; }
function writeDB($file,$data) { file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)); }

// ---------- 3. GET  →  return meetings for current user ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $all = readDB($dbFile);
    $mine = array_values(array_filter($all, function ($m) use ($ownerRole, $ownerId) {
        return $m['ownerRole'] === $ownerRole && $m['ownerId'] === $ownerId;
    }));
    echo json_encode($mine);
    exit;
}

// ---------- 4. POST  →  save meeting ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // basic validation
    foreach (['name','id','date','time'] as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing field: $field"]);
            exit;
        }
    }

    $all = readDB($dbFile);

    // Overwrite logic: if same owner + same student id + same date → replace existing
    $all = array_filter($all, function ($m) use ($ownerRole,$ownerId,$input) {
        return !($m['ownerRole']===$ownerRole &&
                 $m['ownerId']===$ownerId   &&
                 $m['id']===$input['id']    &&
                 $m['date']===$input['date']);
    });

    $all[] = [
        'ownerRole' => $ownerRole,
        'ownerId'   => $ownerId,
        'name'      => trim($input['name']),
        'id'        => trim($input['id']),
        'date'      => $input['date'],
        'time'      => $input['time']
    ];

    writeDB($dbFile, $all);
    echo json_encode(['success' => true]);
    exit;
}
// ---------- 5. DELETE  →  remove a meeting ----------
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id']) || empty($input['date'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id or date']);
        exit;
    }

    $all = readDB($dbFile);
    $filtered = array_filter($all, function ($m) use ($ownerRole, $ownerId, $input) {
        return !($m['ownerRole'] === $ownerRole &&
                 $m['ownerId'] === $ownerId &&
                 $m['id'] === $input['id'] &&
                 $m['date'] === $input['date']);
    });

    writeDB($dbFile, array_values($filtered));
    echo json_encode(['success' => true]);
    exit;
}


// ---------- 6. Unsupported method ----------
http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
