<?php
header("Content-Type: application/json");
require_once "db.php";

$target = $_GET['target'] ?? '';

if ($target === 'orders') {
    $stmt = $pdo->query("SELECT id, user_id, customer_name as name, order_details as cookie, (IF(order_details LIKE '%Chocolate%', 45, 0) + IF(order_details LIKE '%Red Velvet%', 50, 0)) as price, created_at as date FROM orders ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($target === 'records') {
    $stmt = $pdo->query("SELECT id, user_id, customer_name as name, order_details as `order`, delivery_method as delivery, payment_method as payment, customer_phone as contact, created_at as date FROM orders ORDER BY id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($target === 'history') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id as order_id, created_at as date, order_details as item, '1' as quantity, payment_method as payment, status FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll());
}

if ($target === 'feedback') {
    $stmt = $pdo->query("SELECT feedback_id AS id, name, likes, improvements, additional, rating FROM feedback ORDER BY feedback_id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($target === 'account') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT name, email, phone, role, address, gender, links FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetch() ?: new stdClass());
}
?>
