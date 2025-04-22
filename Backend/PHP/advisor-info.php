<?php

// this scirpt fetches the profile details of currently logged in advisor.
// ensrue session validation, confirms roles etc.

// start session
session_start();
header('Content-Type: application/json');
require_once 'db.php';

// include json for api
header('Content-Type: application/json');
require_once 'db.php';


// check if user id and role are already set in the session.
try {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        throw new Exception("Missing session user_id or role.");
    
    }

    if ($_SESSION['role'] !== 'advisor') {

        throw new Exception("User is not an advisor. Role: " . $_SESSION['role']);
    
    }

// extract advisors user id from session
    $userId = $_SESSION['user_id'];

    //step 1: Test that user exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->execute([$userId]);
    $userCheck = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$userCheck) {
        throw new Exception("User with ID $userId not found in Users table.");
    }

    //step 2: Join with Advisors
    $stmt = $pdo->prepare( "
        SELECT 
            u.UserID AS AdvisorID,
            u.FirstName, u.LastName, u.Email, u.PhoneNumber, u.Address,
            u.BirthDate, u.SIN, u.Username,
            a.Department
        FROM Users u
        JOIN Advisors a ON u.UserID = a.AdvisorID
        WHERE u.UserID = ?

    ");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    //check if the profile was found
    if (!$profile) {
        throw new Exception("No match in Advisors table for AdvisorID = $userId");
    }
    // reuturn profile data.
    echo json_encode(['profile' => $profile]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
