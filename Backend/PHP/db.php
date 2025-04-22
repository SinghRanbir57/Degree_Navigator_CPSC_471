<?php
/**
 * This is the Centralised PDO connection.
 * ------------------------------------------------------------
 * Edit the $user and $pass values to match your local database
 * credentials.
 */

$host = 'localhost';
$db   = 'DegreeNavigator';
$user = 'root';          //  your MySQL username
$pass = 'Lukatosic10!';              // your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // throw any exceptions

    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // associativve arrays

    PDO::ATTR_EMULATE_PREPARES   => false,                   // native prepares
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
?>