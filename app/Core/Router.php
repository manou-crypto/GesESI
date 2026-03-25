<?php
namespace App\Core;

class Router {
    protected $controller = 'AuthController'; // Contrôleur par défaut (Login)
    protected $method = 'index'; // Méthode par défaut
    protected $params = []; // Paramètres

    public function __construct() {
        $url = $this->parseUrl();

        // Si l'utilisateur est déjà connecté, on redirige vers le dashboard par défaut, sauf s'il veut un autre contrôleur
        session_start();
        $isLoggedIn = isset($_SESSION['user_id']);

        if (!$isLoggedIn) {
            $this->controller = 'AuthController';
        } else {
            // Utilisateur connecté, s'il n'y a pas d'URL (page d'accueil), il va au Dashboard
            if (!isset($url[0])) {
                $this->controller = 'DashboardController';
            }
        }

        // Vérification de l'existence du contrôleur
        if (isset($url[0]) && file_exists('app/Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        // Require the controller and instantiate
        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        if (!class_exists($controllerClass) && file_exists('app/Controllers/' . $this->controller . '.php')) {
            require_once 'app/Controllers/' . $this->controller . '.php';
        }
        $this->controller = new $controllerClass;

        // Check if method exists
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Get parameters
        $this->params = $url ? array_values($url) : [];

        // Appeler le contrôleur et la méthode avec les paramètres
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
