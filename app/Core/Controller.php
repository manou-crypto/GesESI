<?php
namespace App\Core;

class Controller {
    // Méthode pour charger un modèle
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        $modelClass = '\\App\\Models\\' . $model;
        return new $modelClass();
    }

    // Méthode pour rendre une vue
    public function view($view, $data = []) {
        // Extraire les données pour les rendre accessibles par nom de variable dans la vue
        extract($data);

        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            // Affichage d'erreur propre
            die("View '" . $view . "' does not exist.");
        }
    }

    // Redirection propre
    public function redirect($url) {
        header('Location: ' . BASE_URL . '/index.php?url=' . $url);
        exit();
    }
}
