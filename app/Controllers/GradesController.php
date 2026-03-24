<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Security;

class GradesController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        Security::requireRole(['super_admin', 'responsable', 'professeur']);
    }

    public function index() {
        // En conditions réelles, on récupèrera d'abord les classes du prof via le modèle.
        // Simulons des données pour l'interface demandée
        $db = \App\Config\Database::getInstance();
        
        // Liste de faux étudiants pour afficher l'interface de saisie
        $stmt = $db->query("SELECT id_etudiant, nom, prenom, matricule FROM etudiant LIMIT 10");
        $students = $stmt->fetchAll();

        $this->view('grades/index', [
            'students' => $students,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::validateCsrfToken($_POST['csrf_token'] ?? '');
            
            // Simulation de sauvegarde des notes
            $cours_id = (int)$_POST['cours_id'];
            $notes = $_POST['notes'] ?? []; // Tableau [id_etudiant => note]

            $db = \App\Config\Database::getInstance();

            // L'année active : 
            // Normalement on fait une requête : SELECT id_annee FROM annee_academique WHERE est_active = 1
            $annee_id = 2; // Simulation

            // Parcours des notes soumises
            foreach($notes as $id_etu => $valeur) {
                if ($valeur !== '' && is_numeric($valeur)) {
                    $note = (float)$valeur;
                    if ($note >= 0 && $note <= 20) {
                        try {
                            $stmt = $db->prepare("INSERT INTO note (valeur, type_evaluation, date_evaluation, fk_etudiant, fk_cours, fk_annee) VALUES (:val, 'Devoir', CURRENT_DATE, :etu, :crs, :ann)");
                            $stmt->execute([
                                ':val' => $note,
                                ':etu' => (int)$id_etu,
                                ':crs' => $cours_id,
                                ':ann' => $annee_id
                            ]);
                        } catch(\Exception $e) {
                            // Ignorer pour la démo si contraintes bdd
                        }
                    }
                }
            }
            
            // Log de l'action
            $stmtLog = $db->prepare("INSERT INTO system_logs (action, details, fk_user) VALUES ('Saisie Notes', 'Notes insérées pour le cours id ' || ?, ?)");
            $stmtLog->execute([$cours_id, $_SESSION['user_id']]);

            // Redirection avec succès
            $this->redirect('grades?success=1');
        }
    }
}
