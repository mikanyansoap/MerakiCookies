<?php
header("Content-Type: application/json");
require_once "db.php";

$target = $_GET['target'] ?? '';

if ($target === 'orders') {
    $stmt = $pdo->query("SELECT o.order_id AS id, o.user_id, o.customer_name as name, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.product_name) SEPARATOR ', ') as cookie, o.total_amount as price, o.created_at as date FROM orders o LEFT JOIN order_items oi ON o.order_id = oi.order_id LEFT JOIN products p ON oi.product_id = p.product_id GROUP BY o.order_id ORDER BY o.order_id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($target === 'records') {
    $stmt = $pdo->query("SELECT o.order_id AS id, o.user_id, o.customer_name as name, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.product_name) SEPARATOR ', ') as `order`, o.delivery_method as delivery, o.payment_method as payment, o.customer_phone as contact, o.created_at as date FROM orders o LEFT JOIN order_items oi ON o.order_id = oi.order_id LEFT JOIN products p ON oi.product_id = p.product_id GROUP BY o.order_id ORDER BY o.order_id DESC");
    echo json_encode($stmt->fetchAll());
}

if ($target === 'history') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT o.order_id, o.created_at as date, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.product_name) SEPARATOR ', ') as item, '1' as quantity, o.payment_method as payment, o.status FROM orders o LEFT JOIN order_items oi ON o.order_id = oi.order_id LEFT JOIN products p ON oi.product_id = p.product_id WHERE o.user_id = ? GROUP BY o.order_id ORDER BY o.order_id DESC");
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
