<?php
require_once "db.php";

try {
    $email = 'admin@meraki.com';
    $password = 'admin123';
    $name = 'Master Admin';
    $role = 'admin';
    $phone = '0000000000';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Default admin account already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);
        echo "Default admin account created successfully! Login with admin@meraki.com / admin123";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
