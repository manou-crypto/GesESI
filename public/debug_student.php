<?php
require_once '../app/Controllers/StudentController.php';
$reflection = new ReflectionClass('App\Controllers\StudentController');
$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
echo "Methods in StudentController:\n";
foreach ($methods as $m) {
    echo "- " . $m->getName() . "\n";
}
?>
