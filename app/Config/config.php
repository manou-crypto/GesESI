<?php
define('APP_NAME', 'GesESI');

// Détection dynamique de l'URL de base pour s'adapter automatiquement au nom du dossier (Nouveau_dossier, Nouveau%20dossier, etc.)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Récupère le dossier contenant index.php (ex: /Nouveau_dossier)
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));

define('BASE_URL', $protocol . $host . rtrim($script_path, '/')); 
define('ROOT_PATH', dirname(__DIR__, 2));
