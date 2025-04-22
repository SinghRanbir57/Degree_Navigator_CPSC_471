<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'advisor') {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$userId = $_SESSION['user_id'];

try {
    // Get advisor profile
    $stmt = $pdo->prepare("SELECT FirstName, LastName, Email, PhoneNumber, Address, Department FROM Users WHERE id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) throw new Exception("Advisor not found");

    // Optionally: Get advisees
    $stmt = $pdo->prepare("SELECT u.id, u.FirstName, u.LastName, u.Email
                           FROM Users u
                           JOIN Advisees a ON u.id = a.StudentID
                           WHERE a.AdvisorID = ?");
    $stmt->execute([$userId]);
    $advisees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'profile'   => $profile,
        'advisees'  => $advisees
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
