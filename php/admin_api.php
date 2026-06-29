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
            $stmt = $pdo->query("SELECT o.order_id AS id, o.user_id, o.customer_name AS customer_name, o.customer_phone, GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.product_name) SEPARATOR ', ') AS order_details, o.delivery_method, o.payment_method, o.special_notes, o.status, o.total_amount, o.created_at FROM orders o LEFT JOIN order_items oi ON o.order_id = oi.order_id LEFT JOIN products p ON oi.product_id = p.product_id GROUP BY o.order_id ORDER BY o.order_id DESC");
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

            if (empty($name) || empty($phone)) {
                echo json_encode(["status" => "error", "message" => "Name and Phone are required."]);
                break;
            }

            // Combine details into special_notes since order_details column doesn't exist
            $combined_notes = $notes;
            if (!empty($details)) {
                $combined_notes = trim($details . ($notes ? ' | ' . $notes : ''));
            }

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, special_notes, delivery_method, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $phone, $combined_notes, $delivery, $payment, $status]);
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

            if (!$id || empty($name) || empty($phone)) {
                echo json_encode(["status" => "error", "message" => "ID, Name, and Phone are required."]);
                break;
            }

            $combined_notes = $notes;
            if (!empty($details)) {
                $combined_notes = trim($details . ($notes ? ' | ' . $notes : ''));
            }

            $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, customer_name = ?, customer_phone = ?, delivery_method = ?, payment_method = ?, special_notes = ?, status = ? WHERE order_id = ?");
            $stmt->execute([$user_id, $name, $phone, $delivery, $payment, $combined_notes, $status, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_order':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            // Delete associated order_items first
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$id]);
            // Then delete the order
            $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- USERS CRUD ---
        case 'get_users':
            $stmt = $pdo->query("SELECT user_id AS id, name, email, phone, role, address, gender, links, created_at FROM users ORDER BY user_id DESC");
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
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, address = ?, gender = ?, links = ?, password = ? WHERE user_id = ?");
                $stmt->execute([$name, $email, $phone, $role, $address, $gender, $links, $hashed, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, address = ?, gender = ?, links = ? WHERE user_id = ?");
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
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- FEEDBACK CRUD ---
        case 'get_feedbacks':
            $stmt = $pdo->query("SELECT feedback_id AS id, name, likes, improvements, additional, rating FROM feedback ORDER BY feedback_id DESC");
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

            $stmt = $pdo->prepare("UPDATE feedback SET name = ?, likes = ?, improvements = ?, additional = ? WHERE feedback_id = ?");
            $stmt->execute([$name, $likes, $improvements, $additional, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_feedback':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        // --- INVENTORY CRUD ---
        case 'get_inventory':
            $stmt = $pdo->query("SELECT product_id AS id, product_name AS item_name, quantity, price, date_made, description FROM products ORDER BY product_id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            break;

        case 'add_inventory':
            $item_name = trim($_POST['item_name'] ?? '');
            $price = floatval($_POST['price'] ?? 0.0);
            $description = trim($_POST['description'] ?? '');
            $quantity = intval($_POST['quantity'] ?? 0);
            $date_made = trim($_POST['date_made'] ?? '');
            $date_made = empty($date_made) ? null : $date_made;

            if (empty($item_name)) {
                echo json_encode(["status" => "error", "message" => "Item Name is required."]);
                break;
            }

            $stmt = $pdo->prepare("INSERT INTO products (product_name, price, description, quantity, date_made) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$item_name, $price, $description, $quantity, $date_made]);
            echo json_encode(["status" => "success", "id" => $pdo->lastInsertId()]);
            break;

        case 'update_inventory':
            $id = intval($_POST['id'] ?? 0);
            $item_name = trim($_POST['item_name'] ?? '');
            $price = floatval($_POST['price'] ?? 0.0);
            $description = trim($_POST['description'] ?? '');
            $quantity = intval($_POST['quantity'] ?? 0);
            $date_made = trim($_POST['date_made'] ?? '');
            $date_made = empty($date_made) ? null : $date_made;

            if (!$id || empty($item_name)) {
                echo json_encode(["status" => "error", "message" => "ID and Item Name are required."]);
                break;
            }

            $stmt = $pdo->prepare("UPDATE products SET product_name = ?, price = ?, description = ?, quantity = ?, date_made = ? WHERE product_id = ?");
            $stmt->execute([$item_name, $price, $description, $quantity, $date_made, $id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'delete_inventory':
            $id = intval($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(["status" => "error", "message" => "ID is required."]);
                break;
            }
            $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "success"]);
            break;

        case 'backup_db':
            $dbName = 'meraki_db';
            $dumpExe = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (!file_exists($dumpExe)) {
                echo json_encode(["status" => "error", "message" => "mysqldump not found. Please ensure XAMPP is installed at C:\xampp"]);
                break;
            }
            $filename = 'meraki_backup_' . date('Ymd_His') . '.sql';
            $filepath = __DIR__ . '/../' . $filename;
            $command = "\"$dumpExe\" -u root $dbName > \"$filepath\" 2>&1";
            exec($command, $output, $result_code);
            if ($result_code === 0) {
                echo json_encode(["status" => "success", "file" => $filename]);
            } else {
                echo json_encode(["status" => "error", "message" => "Backup failed. Output: " . implode(" ", $output)]);
            }
            break;

        case 'restore_db':
            if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(["status" => "error", "message" => "No valid backup file uploaded."]);
                break;
            }
            $dbName = 'meraki_db';
            $mysqlExe = 'C:\xampp\mysql\bin\mysql.exe';
            if (!file_exists($mysqlExe)) {
                echo json_encode(["status" => "error", "message" => "mysql exe not found. Please ensure XAMPP is installed at C:\xampp"]);
                break;
            }
            $tempFile = $_FILES['backup_file']['tmp_name'];
            $command = "\"$mysqlExe\" -u root $dbName < \"$tempFile\" 2>&1";
            exec($command, $output, $result_code);
            if ($result_code === 0) {
                echo json_encode(["status" => "success", "message" => "Database restored successfully!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Restore failed. Output: " . implode(" ", $output)]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid action."]);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
