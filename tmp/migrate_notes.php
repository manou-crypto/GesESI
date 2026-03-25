<?php
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "Checking for 'coefficient' column in 'note' table...\n";
    
    $stmt = $db->query("SHOW COLUMNS FROM note LIKE 'coefficient'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "Column 'coefficient' missing. Adding it...\n";
        $db->exec("ALTER TABLE note ADD COLUMN coefficient DECIMAL(3,1) DEFAULT 1.0 AFTER valeur");
        echo "Column 'coefficient' added successfully.\n";
    } else {
        echo "Column 'coefficient' already exists.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
