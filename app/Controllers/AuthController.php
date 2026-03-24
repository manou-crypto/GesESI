<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;
use App\Core\Security;

class AuthController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        
        $csrf_token = Security::generateCsrfToken();
        $this->view('auth/login', ['csrf_token' => $csrf_token]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::validateCsrfToken($_POST['csrf_token'] ?? '');
            
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user->password_hash)) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                // Régénérer l'ID pour bloquer le Session Fixation
                session_regenerate_id(true);

                // Définition de la session logicielle globale
                $_SESSION['user_id'] = $user->id_user;
                $_SESSION['email'] = $user->email;
                $_SESSION['role'] = $user->role;

                // Si c'est un responsable, on récupère sa classe assignée
                if ($user->role === 'responsable') {
                    $profile = $userModel->getProfile($user->id_user, $user->role);
                    $_SESSION['id_classe'] = $profile->fk_classe ?? null;
                }

                // Log système
                $userModel->logAction('Login', 'Connexion reussie', $user->id_user, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

                $this->redirect('dashboard');
            } else {
                $csrf_token = Security::generateCsrfToken();
                $this->view('auth/login', [
                    'csrf_token' => $csrf_token,
                    'error' => 'Identifiants incorrects'
                ]);
            }
        } else {
            $this->redirect('auth');
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userModel = new User();
        if(isset($_SESSION['user_id'])) {
             $userModel->logAction('Logout', 'Deconnexion normale', $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        }

        session_unset();
        session_destroy();
        $this->redirect('auth');
    }
}
