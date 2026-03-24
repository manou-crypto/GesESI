<?php
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "Checking for 'fk_classe' column in 'staff' table...\n";
    
    $stmt = $db->query("SHOW COLUMNS FROM staff LIKE 'fk_classe'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "Column 'fk_classe' missing. Adding it...\n";
        $db->exec("ALTER TABLE staff ADD COLUMN fk_classe INT NULL AFTER fk_user");
        $db->exec("ALTER TABLE staff ADD CONSTRAINT fk_staff_classe FOREIGN KEY (fk_classe) REFERENCES classe(id_classe) ON DELETE SET NULL");
        echo "Column 'fk_classe' added successfully.\n";
    } else {
        echo "Column 'fk_classe' already exists.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
