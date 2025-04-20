<?php
$servername = "localhost"; // or your DB host
$db_username = "your_mysql_user";
$db_password = "your_mysql_password";
$db_name = "your_database_name";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>