<?php
namespace App\Core;

class Security {
    
    // Génère un token CSRF et le stocke en session
    public static function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Valide le token CSRF soumis dans le formulaire
    public static function validateCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die("Erreur CSRF: Requête invalide ou compromise.");
        }
    }

    // Echappe les données pour prévenir les failles XSS
    public static function escape($html) {
        return htmlspecialchars($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // Middleware de protection par rôle (RBAC)
    public static function requireRole($allowedRoles = []) {
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit();
        }

        if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
            // Affichage d'erreur d'accès non autorisé
            header('HTTP/1.0 403 Forbidden');
            echo "Erreur 403 : Accès au module interdit pour le rôle (" . self::escape($_SESSION['role']) . ").";
            exit();
        }
    }
}
