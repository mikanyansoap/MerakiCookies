<?php
require_once "db.php";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'feedback'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "Table exists.\n";
        $stmt = $pdo->query("DESCRIBE feedback");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo "Table does not exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
