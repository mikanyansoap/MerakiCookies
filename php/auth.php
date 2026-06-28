<?php
header("Content-Type: application/json");
require_once "db.php";

$action = $_GET['action'] ?? '';

if ($action === 'signup') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $password = $_POST['customer_password'] ?? '';
    $role = 'customer';

    if (!$name || !$email || !$phone || !$password) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        echo json_encode(["status" => "success", "role" => $role]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Email already registered."]);
    }
}

if ($action === 'login') {
    $email = trim($_POST['customer_email'] ?? '');
    $password = $_POST['customer_password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        echo json_encode(["status" => "success", "role" => $user['role']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }
}
?>