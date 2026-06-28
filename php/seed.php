<?php
require_once "db.php";

echo "Seeding database...\n";

try {
    // 1. Seed Users
    $adminPassword = password_hash("admin123", PASSWORD_BCRYPT);
    $customerPassword = password_hash("password123", PASSWORD_BCRYPT);

    // Delete existing records to start fresh
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE order_items;");
    $pdo->exec("TRUNCATE TABLE feedback;");
    $pdo->exec("TRUNCATE TABLE orders;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Insert Admin
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(["Admin User", "admin@meraki-admin.com", "09762963830", $adminPassword, "admin"]);
    $adminId = $pdo->lastInsertId();
    echo "Seeded Admin User (Email: admin@meraki-admin.com, Password: admin123)\n";

    // Insert Master Admin
    $stmt->execute(["Master Admin", "admin@meraki.com", "0000000000", $adminPassword, "admin"]);
    echo "Seeded Master Admin (Email: admin@meraki.com, Password: admin123)\n";

    // Insert Customer
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, address, gender, links) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        "Sophia Peduca", "sophia@gmail.com", "09123456789",
        $customerPassword, "customer",
        "Makati City, Metro Manila", "Female", "https://facebook.com/sophia"
    ]);
    $customerId = $pdo->lastInsertId();
    echo "Seeded Customer (Email: sophia@gmail.com, Password: password123)\n";

    // 2. Seed Orders
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, delivery_method, payment_method, special_notes, status, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$customerId, "Sophia Peduca", "09123456789", "Pickup", "GCash", "Less sweet if possible", "Pending", 140.00]);
    $order1 = $pdo->lastInsertId();

    $stmt->execute([null, "Angela Fernandez", "91234567", "Delivery", "Maya", "Deliver in the afternoon", "Completed", 100.00]);
    $order2 = $pdo->lastInsertId();
    echo "Seeded sample orders.\n";

    // 3. Seed Order Items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$order1, 1, 2, 45.00, 90.00]);
    $stmt->execute([$order1, 2, 1, 50.00, 50.00]);
    $stmt->execute([$order2, 2, 2, 50.00, 100.00]);
    echo "Seeded order items.\n";

    // 4. Seed Feedback
    $stmt = $pdo->prepare("INSERT INTO feedback (name, likes, improvements, additional) VALUES (?, ?, ?, ?)");
    $stmt->execute(["Sophia Peduca", "The Classic Chocolate Chip cookies are extremely delicious and gooey!", "More flavor options please.", "Great website experience!"]);
    echo "Seeded sample feedback.\n";

    echo "Database seeding completed successfully!\n";

} catch (PDOException $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
?>
