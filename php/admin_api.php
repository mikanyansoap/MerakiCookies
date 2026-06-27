<?php
header("Content-Type: application/json");
require_once "db.php";

// Restrict access to admins only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        // --- ORDERS CRUD ---
        case 'get_orders':
            $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            break;

        case 'add_order':
            $name = trim($_POST['customer_name'] ?? '');
            $phone = trim($_POST['customer_phone'] ?? '');
            $details = trim($_POST['order_details'] ?? '');
            $delivery = trim($_POST['delivery_method'] ?? 'Pickup');
            $payment = trim($_POST['payment_method'] ?? 'COD');
            $notes = trim($_POST['special_notes'] ?? '');
            $status = trim($_POST['status'] ?? 'Pending');
            $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

            if (empty($name) || empty($phone) || empty($details)) {
                echo json_encode(["status" => "error", "message" => "Name, Phone, and Order Details are required."]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, order_details, delivery_method, payment_method, special_notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $phone, $details, $delivery, $payment, $notes, $status]);
            echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
            break;

        case 'update_order':
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['customer_name'] ?? '');
            $phone = trim($_POST['customer_phone'] ?? '');
            $details = trim($_POST['order_details'] ?? '');
            $delivery = trim($_POST['delivery_method'] ?? 'Pickup');
            $payment = trim($_POST['payment_method'] ?? 'COD');
            $notes = trim($_POST['special_notes'] ?? '');
            $status = trim($_POST['status'] ?? 'Pending');
            $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

            if (!$id || empty($name) || empty($phone) || empty($details)) {
                echo json_encode(["status" => "error", "message" => "ID, Name, Phone, and Order Details are required."]);
                break;
            }

            $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, customer_name = ?, customer_phone = ?, order_details = ?, delivery_method = ?, payment_method = ?, special_notes = ?, status = ? WHERE id = ?");
            $stmt->execute([$user_id, $name, $phone, $details, $delivery, $payment, $notes, $status, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_order':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- USERS CRUD ---
        case 'get_users':
            $stmt = $pdo->query("SELECT id, name, email, phone, role, address, gender, links, created_at FROM users ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            break;

        case 'add_user':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'customer';
            $address = trim($_POST['address'] ?? null);
            $gender = trim($_POST['gender'] ?? null);
            $links = trim($_POST['links'] ?? null);

            if (empty($name) || empty($email) || empty($phone) || empty($password)) {
                echo json_encode(["status" => "error", "message" => "Name, Email, Phone, and Password are required."]);
                break;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, address, gender, links) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hashed, $role, $address, $gender, $links]);
            echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
            break;

        case 'update_user':
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role = $_POST['role'] ?? 'customer';
            $address = trim($_POST['address'] ?? null);
            $gender = trim($_POST['gender'] ?? null);
            $links = trim($_POST['links'] ?? null);

            if (!$id || empty($name) || empty($email) || empty($phone)) {
                echo json_encode(["status" => "error", "message" => "ID, Name, Email, and Phone are required."]);
                break;
            }

            // Optional password update
            if (!empty($_POST['password'])) {
                $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, address = ?, gender = ?, links = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $role, $address, $gender, $links, $hashed, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, address = ?, gender = ?, links = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $role, $address, $gender, $links, $id]);
            }
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_user':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            // Prevent self-deletion
            if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $id) {
                echo json_encode(["status" => "error", "message" => "You cannot delete your own admin account while logged in."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- FEEDBACK CRUD ---
        case 'get_feedbacks':
            $stmt = $pdo->query("SELECT * FROM feedback ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            break;

        case 'add_feedback':
            $name = trim($_POST['name'] ?? 'Anonymous');
            $likes = trim($_POST['likes'] ?? '');
            $improvements = trim($_POST['improvements'] ?? '');
            $additional = trim($_POST['additional'] ?? '');

            if (empty($likes)) {
                echo json_encode(["status" => "error", "message" => "Likes/Positive feedback content is required."]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO feedback (name, likes, improvements, additional) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $likes, $improvements, $additional]);
            echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
            break;

        case 'update_feedback':
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? 'Anonymous');
            $likes = trim($_POST['likes'] ?? '');
            $improvements = trim($_POST['improvements'] ?? '');
            $additional = trim($_POST['additional'] ?? '');

            if (!$id || empty($likes)) {
                echo json_encode(["status" => "error", "message" => "ID and Likes are required."]);
                break;
            }

            $stmt = $pdo->prepare("UPDATE feedback SET name = ?, likes = ?, improvements = ?, additional = ? WHERE id = ?");
            $stmt->execute([$name, $likes, $improvements, $additional, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_feedback':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- INVENTORY CRUD ---
        case 'get_inventory':
            $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            break;

        case 'add_inventory':
            $item_name = trim($_POST['item_name'] ?? '');
            $quantity = intval($_POST['quantity'] ?? 0);
            $price = floatval($_POST['price'] ?? 0.0);
            $date_made = trim($_POST['date_made'] ?? null);

            if (empty($item_name)) {
                echo json_encode(["status" => "error", "message" => "Item Name is required."]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO inventory (item_name, quantity, price, date_made) VALUES (?, ?, ?, ?)");
            $stmt->execute([$item_name, $quantity, $price, $date_made]);
            echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
            break;

        case 'update_inventory':
            $id = intval($_POST['id'] ?? 0);
            $item_name = trim($_POST['item_name'] ?? '');
            $quantity = intval($_POST['quantity'] ?? 0);
            $price = floatval($_POST['price'] ?? 0.0);
            $date_made = trim($_POST['date_made'] ?? null);

            if (!$id || empty($item_name)) {
                echo json_encode(["status" => "error", "message" => "ID and Item Name are required."]);
                break;
            }

            $stmt = $pdo->prepare("UPDATE inventory SET item_name = ?, quantity = ?, price = ?, date_made = ? WHERE id = ?");
            $stmt->execute([$item_name, $quantity, $price, $date_made, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_inventory':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid action."]);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
