<?php
require_once 'app/Config/Database.php';
require_once 'app/Core/Security.php';

use App\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "--- Testing Staff Table ---\n";
    $stmt = $db->query("SELECT * FROM staff LIMIT 1");
    $staff = $stmt->fetch();
    if ($staff && property_exists($staff, 'fk_classe')) {
        echo "SUCCESS: 'fk_classe' property exists in staff object.\n";
    } else {
        echo "FAILURE: 'fk_classe' property NOT found in staff object.\n";
    }

    echo "\n--- Testing Course Affectation Logic ---\n";
    // Just checking if we can query affectation_cours
    $stmt = $db->query("SELECT * FROM affectation_cours LIMIT 1");
    echo "SUCCESS: affectation_cours table is accessible.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
