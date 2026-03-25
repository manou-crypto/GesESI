<?php
require_once 'app/Config/config.php';
require_once 'app/Config/Database.php';

echo "--- Final Project Check ---\n";
echo "App Name: " . APP_NAME . "\n";
echo "Base URL: " . BASE_URL . "\n";

echo "\nChecking File Existence:\n";
$files = ['index.php', 'css/style.css', 'app/Core/Router.php', 'app/Views/layout/header.php'];
foreach($files as $f) {
    echo "- $f: " . (file_exists($f) ? "EXISTS" : "MISSING") . "\n";
}

echo "\nChecking View Paths (Sample: professor_list.php):\n";
$content = file_get_contents('app/Views/grade/professor_list.php');
if (strpos($content, "require_once 'app/Views/layout/header.php'") !== false) {
    echo "SUCCESS: Included path is correct.\n";
} else {
    echo "FAILURE: Included path is still incorrect.\n";
}

echo "\nChecking CSS for Burgundy Colors:\n";
$css = file_get_contents('css/style.css');
if (strpos($css, "#800000") !== false) {
    echo "SUCCESS: Burgundy color found in CSS.\n";
} else {
    echo "FAILURE: Burgundy color NOT found in CSS.\n";
}

echo "\nChecking Database for Coefficient column:\n";
try {
    $db = \App\Config\Database::getInstance();
    $stmt = $db->query("SHOW COLUMNS FROM note LIKE 'coefficient'");
    if ($stmt->fetch()) {
        echo "SUCCESS: 'coefficient' column exists.\n";
    } else {
        echo "FAILURE: 'coefficient' column missing.\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
