<?php
require_once __DIR__ . '/../app/Config/Database.php';
use App\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "Running migrations...\n";
    
    // Check if column exists in professeur
    $cols = $db->query("SHOW COLUMNS FROM professeur LIKE 'telephone'")->fetch();
    if (!$cols) {
        $db->exec("ALTER TABLE professeur ADD COLUMN telephone VARCHAR(20) DEFAULT NULL AFTER prenom");
        echo "Column 'telephone' added to 'professeur' table.\n";
    } else {
        echo "Column 'telephone' already exists in 'professeur'.\n";
    }
    
    // Check if column exists in staff
    $colsStaff = $db->query("SHOW COLUMNS FROM staff LIKE 'telephone'")->fetch();
    if (!$colsStaff) {
        $db->exec("ALTER TABLE staff ADD COLUMN telephone VARCHAR(20) DEFAULT NULL AFTER prenom");
        echo "Column 'telephone' added to 'staff' table.\n";
    } else {
        echo "Column 'telephone' already exists in 'staff'.\n";
    }
    
    echo "Migration completed successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
