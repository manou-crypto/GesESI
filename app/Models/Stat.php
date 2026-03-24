<?php
namespace App\Models;
use App\Config\Database;

class Stat {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDashboardStats($id_classe = null) {
        $stats = [
            'total_students' => 0,
            'total_teachers' => 0,
            'total_courses' => 0,
            'active_year' => 'N/A'
        ];

        try {
            if ($id_classe) {
                // Étudiants inscrits dans CETTE classe pour l'année active
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count FROM inscription i 
                    JOIN annee_academique aa ON i.fk_annee = aa.id_annee
                    WHERE i.fk_classe = ? AND aa.est_active = 1
                ");
                $stmt->execute([$id_classe]);
                $stats['total_students'] = $stmt->fetch()->count;

                // Professeurs affectés à CETTE classe pour l'année active
                $stmt = $this->db->prepare("
                    SELECT COUNT(DISTINCT fk_prof) as count FROM affectation_cours ac
                    JOIN annee_academique aa ON ac.fk_annee = aa.id_annee
                    WHERE ac.fk_classe = ? AND aa.est_active = 1
                ");
                $stmt->execute([$id_classe]);
                $stats['total_teachers'] = $stmt->fetch()->count;

                // Cours affectés à CETTE classe
                $stmt = $this->db->prepare("
                    SELECT COUNT(DISTINCT fk_cours) as count FROM affectation_cours ac
                    JOIN annee_academique aa ON ac.fk_annee = aa.id_annee
                    WHERE ac.fk_classe = ? AND aa.est_active = 1
                ");
                $stmt->execute([$id_classe]);
                $stats['total_courses'] = $stmt->fetch()->count;
            } else {
                // Vue globale (Super Admin)
                $stats['total_students'] = $this->db->query("SELECT COUNT(*) as count FROM etudiant")->fetch()->count;
                $stats['total_teachers'] = $this->db->query("SELECT COUNT(*) as count FROM professeur")->fetch()->count;
                $stats['total_courses'] = $this->db->query("SELECT COUNT(*) as count FROM cours")->fetch()->count;
            }

            // Année académique (commune)
            $stmt = $this->db->query("SELECT libelle FROM annee_academique WHERE est_active = 1 LIMIT 1");
            $year = $stmt->fetch();
            if ($year) {
                $stats['active_year'] = $year->libelle;
            }
        } catch (\PDOException $e) {
            error_log("Stat Error: " . $e->getMessage());
        }

        return $stats;
    }
}
