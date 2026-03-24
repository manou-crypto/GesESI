<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;
use App\Models\User;

class DashboardController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Vérifier que l'utilisateur est connecté pour l'ensemble du contrôleur
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth');
        }
    }

    public function index() {
        $userModel = new User();
        $profile = $userModel->getProfile($_SESSION['user_id'], $_SESSION['role']);

        $data = [
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email'],
            'profile' => $profile
        ];

        // Charger une vue différente selon le rôle
        if ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'responsable') {
            require_once '../app/Models/Stat.php';
            $statModel = new \App\Models\Stat();
            
            // Si c'est un responsable, on filtre par sa classe
            $id_classe_filter = ($_SESSION['role'] === 'responsable') ? ($_SESSION['id_classe'] ?? null) : null;
            $data['stats'] = $statModel->getDashboardStats($id_classe_filter);
            
            $this->view('dashboard/admin', $data);
        } elseif ($_SESSION['role'] === 'professeur') {
            $this->redirect('grade/professor_list');
        } else {
            $this->redirect('grade/student_view');
        }
    }

    public function students() {
        $this->redirect('student/index');
    }
}
