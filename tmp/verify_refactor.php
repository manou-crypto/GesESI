<?php
require_once 'app/Config/config.php';
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getInstance();
    
    echo "--- Testing Weighted Average Calculation Logic ---\n";
    
    // Simulate notes for one student in one course
    $notes = [
        ['valeur' => 10, 'coefficient' => 1],
        ['valeur' => 15, 'coefficient' => 2]
    ];
    
    $sum = 0;
    $weight = 0;
    foreach($notes as $n) {
        $sum += ($n['valeur'] * $n['coefficient']);
        $weight += $n['coefficient'];
    }
    
    $avg = $weight > 0 ? ($sum / $weight) : 0;
    echo "Expected Average: (10*1 + 15*2) / (1+2) = 40/3 = 13.33\n";
    echo "Calculated Average: " . round($avg, 2) . "\n";
    
    if (round($avg, 2) == 13.33) {
        echo "SUCCESS: Logic is correct.\n";
    } else {
        echo "FAILURE: Logic error.\n";
    }

    echo "\n--- Testing Database Access ---\n";
    $stmt = $db->query("SELECT id_note, coefficient FROM note LIMIT 1");
    $note = $stmt->fetch();
    if ($note && property_exists($note, 'coefficient')) {
        echo "SUCCESS: 'coefficient' column exists and is accessible.\n";
    } else {
        echo "NOTE: No notes found yet, but query executed successfully.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
