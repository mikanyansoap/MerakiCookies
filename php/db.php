<?php
// Start safe session tracking across the site
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$db_user = "root"; // Update with your DB username
$db_pass = "";     // Update with your DB password
$db_name = "meraki_db";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}
?>