<?php
namespace App\Models;
use App\Config\Database;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE email = :email AND actif = 1 LIMIT 1");
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function logAction($action, $details, $user_id, $ip) {
        $stmt = $this->db->prepare("INSERT INTO system_logs (action, details, fk_user, ip_address) VALUES (:act, :det, :usr, :ip)");
        $stmt->bindParam(':act', $action);
        $stmt->bindParam(':det', $details);
        $stmt->bindParam(':usr', $user_id);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    }

    // Obtenir le profil lié (soit admin, prof ou étudiant)
    public function getProfile($user_id, $role) {
        switch($role) {
            case 'etudiant':
                $stmt = $this->db->prepare("SELECT e.*, c.nom_classe FROM etudiant e LEFT JOIN inscription i ON e.id_etudiant = i.fk_etudiant LEFT JOIN classe c ON i.fk_classe = c.id_classe WHERE e.fk_user = :uid LIMIT 1");
                break;
            case 'professeur':
                $stmt = $this->db->prepare("SELECT * FROM professeur WHERE fk_user = :uid LIMIT 1");
                break;
            case 'responsable':
            case 'super_admin':
                $stmt = $this->db->prepare("SELECT * FROM staff WHERE fk_user = :uid LIMIT 1");
                break;
            default:
                return null;
        }
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
