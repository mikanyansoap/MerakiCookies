<?php
require_once "db.php";

echo "Seeding database...\n";

try {
    // 1. Seed Users
    $adminPassword = password_hash("admin123", PASSWORD_BCRYPT);
    $customerPassword = password_hash("password123", PASSWORD_BCRYPT);

    // Delete existing records to start fresh
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE feedback;");
    $pdo->exec("TRUNCATE TABLE orders;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Insert Admin
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(["Admin User", "admin@meraki-admin.com", "09762963830", $adminPassword, "admin"]);
    $adminId = $pdo->lastInsertId();
    echo "Seeded Admin User (Email: admin@meraki-admin.com, Password: admin123)\n";

    // Insert Customer
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, address, gender, links) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        "Sophia Peduca",
        "sophia@gmail.com",
        "09123456789",
        $customerPassword,
        "customer",
        "Makati City, Metro Manila",
        "Female",
        "https://facebook.com/sophia"
    ]);
    $customerId = $pdo->lastInsertId();
    echo "Seeded Customer User (Email: sophia@gmail.com, Password: password123)\n";

    // 2. Seed Orders
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, order_details, delivery_method, payment_method, special_notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $customerId,
        "Sophia Peduca",
        "09123456789",
        "2x Classic Chocolate Chip, 1x Velvety Red Velvet",
        "Pickup",
        "GCash",
        "Less sweet if possible",
        "Pending"
    ]);

    $stmt->execute([
        null,
        "Angela Fernandez",
        "91234567",
        "2x Velvety Red Velvet",
        "Delivery",
        "Maya",
        "Deliver in the afternoon",
        "Completed"
    ]);
    echo "Seeded sample orders.\n";

    // 3. Seed Feedback
    $stmt = $pdo->prepare("INSERT INTO feedback (name, likes, improvements, additional) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        "Sophia Peduca",
        "The Classic Chocolate Chip cookies are extremely delicious and gooey!",
        "More flavor options please.",
        "Great website experience!"
    ]);
    echo "Seeded sample feedback.\n";

    echo "Database seeding completed successfully!\n";

} catch (PDOException $e) {
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
?>
