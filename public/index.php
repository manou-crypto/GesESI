<?php
use App\Core\Router;

// Gestion de l'autoloading minimal sans dépendre de Composer
spl_autoload_register(function($className) {
    // Transformer l'espace de nom en chemin de fichier
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Supprimer "App\" du début pour le namespace "App"
    $path = preg_replace('/^App' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $path);
    
    // Construire le chemin final dans le dossier app/
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Charger la configuration
require_once '../app/Config/config.php';

// Initialiser l'application
$app = new Router();
