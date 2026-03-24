-- --------------------------------------------------------
-- SCRIPT SQL COMPLET - MYSQL POUR XAMPP
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS ecole_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecole_db;

-- 1. Table des années académiques (ex: 2024-2025)
CREATE TABLE IF NOT EXISTS annee_academique (
    id_annee INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE,
    est_active TINYINT(1) DEFAULT 0
);

-- 2. Table Utilisateur - Gestion unifiée des comptes et RBAC
CREATE TABLE IF NOT EXISTS utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'responsable', 'professeur', 'etudiant') NOT NULL,
    actif TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Entités spécifiques par rôle
CREATE TABLE IF NOT EXISTS etudiant (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    fk_user INT NOT NULL,
    FOREIGN KEY (fk_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS professeur (
    id_prof INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    specialite VARCHAR(150),
    fk_user INT NOT NULL,
    FOREIGN KEY (fk_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS staff (
    id_staff INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    fk_user INT NOT NULL,
    fk_classe INT NULL,
    FOREIGN KEY (fk_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (fk_classe) REFERENCES classe(id_classe) ON DELETE SET NULL
);

-- 4. Structure académique
CREATE TABLE IF NOT EXISTS classe (
    id_classe INT AUTO_INCREMENT PRIMARY KEY,
    nom_classe VARCHAR(100) NOT NULL,
    niveau VARCHAR(100),
    fk_annee INT NOT NULL,
    FOREIGN KEY (fk_annee) REFERENCES annee_academique(id_annee) ON DELETE CASCADE
);

-- Liaison Etudiant - Classe
CREATE TABLE IF NOT EXISTS inscription (
    id_inscription INT AUTO_INCREMENT PRIMARY KEY,
    fk_etudiant INT NOT NULL,
    fk_classe INT NOT NULL,
    fk_annee INT NOT NULL,
    FOREIGN KEY (fk_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (fk_classe) REFERENCES classe(id_classe) ON DELETE CASCADE,
    FOREIGN KEY (fk_annee) REFERENCES annee_academique(id_annee) ON DELETE CASCADE,
    UNIQUE(fk_etudiant, fk_annee)
);

CREATE TABLE IF NOT EXISTS cours (
    id_cours INT AUTO_INCREMENT PRIMARY KEY,
    ue_nom VARCHAR(150) NOT NULL DEFAULT 'UE Principale',
    libelle VARCHAR(150) NOT NULL,
    coefficient DECIMAL(3,1) NOT NULL DEFAULT 1.0
);

-- Affectation des professeurs aux cours par classe
CREATE TABLE IF NOT EXISTS affectation_cours (
    id_affectation INT AUTO_INCREMENT PRIMARY KEY,
    fk_prof INT NOT NULL,
    fk_cours INT NOT NULL,
    fk_classe INT NOT NULL,
    fk_annee INT NOT NULL,
    FOREIGN KEY (fk_prof) REFERENCES professeur(id_prof) ON DELETE CASCADE,
    FOREIGN KEY (fk_cours) REFERENCES cours(id_cours) ON DELETE CASCADE,
    FOREIGN KEY (fk_classe) REFERENCES classe(id_classe) ON DELETE CASCADE,
    FOREIGN KEY (fk_annee) REFERENCES annee_academique(id_annee) ON DELETE CASCADE
);

-- 5. Gestion des Notes
CREATE TABLE IF NOT EXISTS note (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    valeur DECIMAL(4,2) NOT NULL CHECK(valeur >= 0 AND valeur <= 20),
    type_evaluation VARCHAR(50) NOT NULL DEFAULT 'Devoir',
    date_evaluation DATE NOT NULL,
    verrouille TINYINT(1) DEFAULT 0,
    fk_etudiant INT NOT NULL,
    fk_cours INT NOT NULL,
    fk_annee INT NOT NULL,
    FOREIGN KEY (fk_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (fk_cours) REFERENCES cours(id_cours) ON DELETE CASCADE,
    FOREIGN KEY (fk_annee) REFERENCES annee_academique(id_annee) ON DELETE CASCADE
);

-- 6. Logs de Sécurité (Audit)
CREATE TABLE IF NOT EXISTS system_logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    fk_user INT,
    date_log DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fk_user) REFERENCES utilisateur(id_user) ON DELETE SET NULL
);

-- 7. Insertion de données de test (Bouchonnage)
INSERT INTO annee_academique (libelle, est_active) VALUES ('2023-2024', 0), ('2024-2025', 1);

-- Utilisateurs de test (mot de passe : mdp)
-- Note: les hashs ci-dessous correspondent au mot de passe "mdp123" haché en BCRYPT
INSERT INTO utilisateur (email, password_hash, role) VALUES 
('admin@ecole.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin'),
('resp@ecole.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'responsable'),
('prof.mathematiques@ecole.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur'),
('etudiant1@ecole.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant');

-- Liaison aux entités
INSERT INTO staff (nom, prenom, fk_user) VALUES ('Directeur', 'Global', 1), ('Pedagogie', 'Chef', 2);
INSERT INTO professeur (matricule, nom, prenom, specialite, fk_user) VALUES ('P001', 'Durand', 'Paul', 'Mathématiques', 3);
INSERT INTO etudiant (matricule, nom, prenom, fk_user) VALUES ('E001', 'Dupont', 'Jean', 4);

-- Cours et Classes
INSERT INTO cours (libelle, coefficient) VALUES ('Algèbre', 4), ('Analyse', 4), ('Physique', 3);
INSERT INTO classe (nom_classe, fk_annee) VALUES ('L1 Informatique', 2), ('L2 Informatique', 2);

-- Inscriptions et Affectations
INSERT INTO inscription (fk_etudiant, fk_classe, fk_annee) VALUES (1, 1, 2);
INSERT INTO affectation_cours (fk_prof, fk_cours, fk_classe, fk_annee) VALUES (1, 1, 1, 2);

-- Notes de test
INSERT INTO note (valeur, type_evaluation, date_evaluation, fk_etudiant, fk_cours, fk_annee) VALUES (15.5, 'Partiel', '2024-11-15', 1, 1, 2);
