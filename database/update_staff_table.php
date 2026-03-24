// Autoloading minimal
spl_autoload_register(function($className) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $path = preg_replace('/^App' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $path);
    $file = __DIR__ . '/../app/' . $path . '.php';
    if (file_exists($file)) require_once $file;
});
require_once __DIR__ . '/../app/Config/config.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    
    // Ajouter la colonne fk_classe si elle n'existe pas
    $db->exec("ALTER TABLE staff ADD COLUMN fk_classe INT DEFAULT NULL AFTER prenom");
    $db->exec("ALTER TABLE staff ADD CONSTRAINT fk_staff_classe FOREIGN KEY (fk_classe) REFERENCES classe(id_classe) ON DELETE SET NULL");
    
    // Assigner le responsable de test (id_staff = 2) à la classe L1 Informatique (id_classe = 1)
    $db->exec("UPDATE staff SET fk_classe = 1 WHERE id_staff = 2");
    
    echo "Migration réussie : Colonne fk_classe ajoutée et responsable test assigné.\n";
} catch (Exception $e) {
    echo "Erreur lors de la migration : " . $e->getMessage() . "\n";
}
