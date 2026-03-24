<?php
require_once '../app/Config/config.php';
require_once '../app/Config/Database.php';

try {
    $db = \App\Config\Database::getInstance();
    $db->exec("ALTER TABLE classe ADD COLUMN niveau VARCHAR(100) AFTER nom_classe");
    echo "SUCCESS: niveau column added to classe table.";
} catch(\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
