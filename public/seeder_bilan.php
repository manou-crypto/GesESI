<?php
// Script de Seeder - Bilan Semestriel
require_once '../app/Config/config.php';
require_once '../app/Config/Database.php';

try {
    $db = \App\Config\Database::getInstance();

    // 1. Mise à jour de la structure de base de données en direct
    try {
        $db->exec("ALTER TABLE cours ADD COLUMN ue_nom VARCHAR(150) NOT NULL DEFAULT 'UE Principale' AFTER id_cours");
    }
    catch (\PDOException $e) { /* Ignore si existe déjà */
    }

    try {
        $db->exec("ALTER TABLE cours MODIFY COLUMN coefficient DECIMAL(4,2) NOT NULL DEFAULT 1.0");
    }
    catch (\PDOException $e) { /* Ignore erreur synthaxe */
    }

    // Nettoyage des anciennes données pour le test
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("TRUNCATE TABLE note");
    $db->exec("TRUNCATE TABLE inscription");
    $db->exec("TRUNCATE TABLE affectation_cours");
    $db->exec("DELETE FROM etudiant");
    $db->exec("DELETE FROM cours");
    $db->exec("DELETE FROM classe");
    $db->exec("DELETE FROM utilisateur WHERE role = 'etudiant'");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // L'année active par défaut
    $stmtY = $db->query("SELECT id_annee FROM annee_academique WHERE est_active = 1 LIMIT 1");
    $yearId = $stmtY->fetch()->id_annee ?? 1;

    // 2. Création de la Classe
    $stmtClass = $db->prepare("INSERT INTO classe (nom_classe, niveau, fk_annee) VALUES (?, ?, ?)");
    $stmtClass->execute(["Electronique, Info et Télécoms 2ème année", "EIT 2 - Semestre 3", $yearId]);
    $classeId = $db->lastInsertId();

    // 3. Création des Etudiants de l'image
    $eleves = [
        "24NP00956" => ["nom" => "CISSE", "prenom" => "YAYA"],
        "24NP00578" => ["nom" => "COULIBALY", "prenom" => "ZIE IBRAHIMA"],
        "24NP00736" => ["nom" => "DIANE", "prenom" => "MADJARA LARISSA"],
        "24NP00243" => ["nom" => "FOFANA", "prenom" => "MAMADOU JUNIOR"],
        "24NP00893" => ["nom" => "KABORE", "prenom" => "ISSA"],
        "24NP00804" => ["nom" => "KANGAH", "prenom" => "KOUAKOU HENRI JOEL"],
        "24NP01152" => ["nom" => "KEUMAHON", "prenom" => "MAHONTO JEAN JACQUES"],
        "24NP00785" => ["nom" => "KOFFI", "prenom" => "N'GUESSAN RAOUL"],
        "24NP01183" => ["nom" => "KONE", "prenom" => "HAMED"],
        "24NP00719" => ["nom" => "KOUASSI", "prenom" => "FAMIEN WATKE SOURALAIS"],
        "24NP00910" => ["nom" => "KOUASSI", "prenom" => "N'DA HANAN CHRIST MARIANE"],
        "24NP00759" => ["nom" => "N'GBESSO", "prenom" => "CHRIST URIEL JUNIOR"],
        "24NP00825" => ["nom" => "OCHOU", "prenom" => "OCHOU JUNIOR ERVIN EMMANUEL"],
        "24NP00268" => ["nom" => "QUATTARA", "prenom" => "ABDALLAH"],
        "24NP00298" => ["nom" => "QUATTARA", "prenom" => "CHECK ABOUBACAR JUNIOR"],
        "24NP00649" => ["nom" => "SAGNON", "prenom" => "NALOUROUGO BRAHIMA JUSTIN"],
        "24NP00467" => ["nom" => "SERME", "prenom" => "ISSOUF"],
        "24NP00152" => ["nom" => "SORHO", "prenom" => "DONAPORGO DAVID-PAUL"],
        "24NP00932" => ["nom" => "TIE", "prenom" => "ARMEL AIME"],
        "24NP00428" => ["nom" => "TOURE", "prenom" => "SIE GNINDANFOWA ABDOUL KADER"],
    ];

    $hash = password_hash('mdp123', PASSWORD_DEFAULT);
    $etudiantsIds = [];

    foreach ($eleves as $mat => $data) {
        $email = strtolower($data['nom']) . rand(100, 999) . "@ecole.com";
        $db->prepare("INSERT INTO utilisateur (email, password_hash, role) VALUES (?, ?, 'etudiant')")->execute([$email, $hash]);
        $uId = $db->lastInsertId();

        $db->prepare("INSERT INTO etudiant (matricule, nom, prenom, fk_user) VALUES (?, ?, ?, ?)")->execute([$mat, $data['nom'], $data['prenom'], $uId]);
        $eId = $db->lastInsertId();
        $etudiantsIds[] = $eId;

        // Inscription à la classe
        $db->prepare("INSERT INTO inscription (fk_etudiant, fk_classe, fk_annee) VALUES (?, ?, ?)")->execute([$eId, $classeId, $yearId]);
    }

    // 4. Création des Cours par UE
    $ue_matieres = [
        "UE1 : Mathématiques et Physique" => [
            ["libelle" => "Optique et photonique", "coef" => 2],
            ["libelle" => "Probabilité 2", "coef" => 2],
            ["libelle" => "Statistiques inférentielles", "coef" => 1]
        ],
        "UE2 : Circuits et Systèmes Electroniques" => [
            ["libelle" => "Electronique approfondie", "coef" => 2],
            ["libelle" => "CAO Electronique", "coef" => 1.5],
            ["libelle" => "Capteurs et actionneurs", "coef" => 1],
            ["libelle" => "Conception de Systèmes Electroniques", "coef" => 2]
        ],
        "UE3 : Réseaux et Télécommunications" => [
            ["libelle" => "Transmission Analogique", "coef" => 2],
            ["libelle" => "Réseaux Informatiques 1", "coef" => 2],
            ["libelle" => "Réseaux Mobiles 1", "coef" => 2]
        ],
        "UE4 : Programmation avancée et Web" => [
            ["libelle" => "Télécommunications", "coef" => 2],
            ["libelle" => "Programmation Web 1", "coef" => 2],
            ["libelle" => "Framework web 1", "coef" => 1],
            ["libelle" => "BE Electronique", "coef" => 1]
        ],
        "UE5 : Entrepreneuriat et Projet" => [
            ["libelle" => "Methodologie mémoire", "coef" => 1],
            ["libelle" => "Projet d'Application 1", "coef" => 1],
            ["libelle" => "Entrepreneuriat", "coef" => 1]
        ],
        "UE6 : Langues, Sc Humaines et Sociales" => [
            ["libelle" => "Anglais 3", "coef" => 2],
            ["libelle" => "Management Opérationnel", "coef" => 2],
            ["libelle" => "Education Physique", "coef" => 2]
        ]
    ];

    $coursIds = [];
    foreach ($ue_matieres as $ue => $matieres) {
        foreach ($matieres as $m) {
            $db->prepare("INSERT INTO cours (ue_nom, libelle, coefficient) VALUES (?, ?, ?)")->execute([$ue, $m['libelle'], $m['coef']]);
            $coursIds[] = $db->lastInsertId();
        }
    }

    // 5. Génération de Notes Aléatoires (mais réalistes)
    foreach ($etudiantsIds as $eId) {
        foreach ($coursIds as $cId) {
            // Random grade between 8.50 and 18.50
            $grade = mt_rand(850, 1850) / 100;
            $db->prepare("INSERT INTO note (valeur, type_evaluation, date_evaluation, fk_etudiant, fk_cours, fk_annee) VALUES (?, 'Semestre', CURRENT_DATE, ?, ?, ?)")->execute([$grade, $eId, $cId, $yearId]);
        }
    }

    echo "<h1>🚀 Base de Données Initialisée avec Succès !</h1>";
    echo "<p>Les 20 étudiants de la Planche de Notes (EIT 2), l'ensemble des UEs, des ECUEs, de leurs coefficients, et 400 notes ont été générés !</p>";
    echo "<a href='../public/index.php?url=dashboard' style='padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; font-family: sans-serif;'>Retour au Dashboard</a>";

}
catch (\Exception $e) {
    echo "Erreur lors du Seeding : " . $e->getMessage();
}
