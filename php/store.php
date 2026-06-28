<?php
header("Content-Type: application/json");
require_once "db.php";

$action = $_GET['action'] ?? '';

// Handle client checking out shopping carts
if ($action === 'place_order') {
    $qty_cc = intval($_POST['qty_chocolate_chip'] ?? 0);
    $qty_rv = intval($_POST['qty_red_velvet'] ?? 0);
    $qty_sm = intval($_POST['qty_smores'] ?? 0);

    // Build items array with product_id => quantity mapping
    // product_id 1 = Classic Chocolate Chip, 2 = Velvety Red Velvet, 3 = Gooey S'mores
    $items = [];
    if ($qty_cc > 0) $items[] = ['product_id' => 1, 'qty' => $qty_cc, 'name' => 'Classic Chocolate Chip'];
    if ($qty_rv > 0) $items[] = ['product_id' => 2, 'qty' => $qty_rv, 'name' => 'Velvety Red Velvet'];
    if ($qty_sm > 0) $items[] = ['product_id' => 3, 'qty' => $qty_sm, 'name' => "Gooey S'mores"];

    if (empty($items)) {
        echo json_encode(["status" => "error", "message" => "Please select at least one cookie."]);
        exit;
    }

    $payment_method = $_POST['payment_method'] ?? '';
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $delivery_method = $_POST['delivery_method'] ?? '';
    $special_notes = trim($_POST['special_notes'] ?? '');
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "You must be logged in to place an order."]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Look up prices from products table and calculate total
        $total_amount = 0;
        $item_details = [];
        foreach ($items as &$item) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch();
            $item['price'] = $product ? floatval($product['price']) : 0;
            $item['subtotal'] = $item['price'] * $item['qty'];
            $total_amount += $item['subtotal'];
        }
        unset($item);

        // Insert the order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, delivery_method, payment_method, special_notes, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $customer_name, $customer_phone, $delivery_method, $payment_method, $special_notes, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['qty'], $item['price'], $item['subtotal']]);
        }

        $pdo->commit();
        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Failed to place order: " . $e->getMessage()]);
    }
}

// Handle administrative custom rows or edits from Records Management
if ($action === 'admin_add_order') {
    $name = trim($_POST['name'] ?? 'Manual Entry');
    $order = trim($_POST['order'] ?? '');
    $delivery = trim($_POST['delivery'] ?? '');
    $payment = trim($_POST['payment'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, delivery_method, payment_method, special_notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $contact, $delivery, $payment, $order]);
    echo json_encode(["status" => "success"]);
}

// Handle customer submitting feedback/reviews
if ($action === 'submit_feedback') {
    $name = trim($_POST['Customer_Alias'] ?? '');
    if (empty($name)) {
        $name = 'Anonymous';
    }
    $likes = trim($_POST['likes'] ?? '');
    $improvements = trim($_POST['improvements'] ?? '');
    $additional = trim($_POST['additional'] ?? '');

    if (empty($likes)) {
        echo json_encode(["status" => "error", "message" => "Please tell us what you liked about our products."]);
        exit;
    }
    $rating = intval($_POST['rating'] ?? 5);

    $stmt = $pdo->prepare("INSERT INTO feedback (name, likes, improvements, additional, rating) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $likes, $improvements, $additional, $rating]);
    echo json_encode(["status" => "success"]);
}

// Handle updating account details
if ($action === 'update_account') {
    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Not logged in."]);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? null);
    $gender = trim($_POST['gender'] ?? null);

    if (empty($name) || empty($email) || empty($phone)) {
        echo json_encode(["status" => "error", "message" => "Name, Email, and Phone are required."]);
        exit;
    }

    if (!empty($_POST['password'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, gender = ?, password = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $phone, $address, $gender, $hashed, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, gender = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $phone, $address, $gender, $user_id]);
    }

    // Keep session in sync
    $_SESSION['email'] = $email;

    echo json_encode(["status" => "success"]);
    exit;
}
?>