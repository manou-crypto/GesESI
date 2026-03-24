# 🎓 School Management Pro
**Application Web de Gestion Académique — PHP / MySQL / Bootstrap 5**

> Plateforme de gestion académique complète pour l'ECOLE SUPERIEURE D'INDUSTRIE (ESI), filière Sciences de l'Information et de la Communication. Gère les étudiants, les enseignants, les cours, les classes, les notes, et génère des bilans semestriels (Planche de Notes).

---

## 📋 Sommaire
1. [Technologies](#-technologies)
2. [Architecture](#-architecture-mvc)
3. [Modules Disponibles](#-modules-disponibles)
4. [Base de Données](#-base-de-données)
5. [Installation XAMPP](#-installation-xampp)
6. [Comptes de Test](#-comptes-de-test)
7. [Sécurité](#-sécurité)

---

## 🛠 Technologies

| Couche | Technologie |
|---|---|
| Backend | PHP 8+ (MVC natif) |
| Base de données | MySQL 8 (via XAMPP) |
| Frontend | Bootstrap 5 + Font Awesome 6 |
| Connexion BDD | PDO (requêtes préparées) |
| Serveur local | XAMPP (Apache + MySQL) |

---

## 🏗 Architecture MVC

```
Nouveau dossier/
├── public/                  ← Racine Web (seul dossier accessible via URL)
│   ├── index.php            ← Front Controller & Routeur central
│   ├── css/style.css        ← Styles UI premium (Bootstrap + Custom)
│   ├── seeder_bilan.php     ← Script de peuplement auto (dev uniquement)
├── app/
│   ├── Config/
│   │   ├── Database.php     ← Connexion PDO (Singleton MySQL)
│   │   └── config.php       ← Constantes (BASE_URL, APP_NAME)
│   ├── Core/
│   │   ├── Router.php       ← Routage dynamique des URLs
│   │   ├── Controller.php   ← Contrôleur parent (render + redirect)
│   │   └── Security.php     ← CSRF, XSS, RBAC, Escape
│   ├── Controllers/
│   │   ├── AuthController.php        ← Login / Logout
│   │   ├── DashboardController.php   ← Dashboard (stats dynamiques)
│   │   ├── StudentController.php     ← CRUD Étudiants
│   │   ├── TeacherController.php     ← CRUD Professeurs
│   │   ├── ClassController.php       ← CRUD Classes/Filières
│   │   ├── CourseController.php      ← CRUD Cours (ECUEs) avec UEs
│   │   ├── YearController.php        ← Gestion Années Académiques
│   │   └── ReportController.php      ← Bilan Semestriel (Planche de Notes)
│   └── Views/
│       ├── layout/          ← header.php, sidebar.php, footer.php
│       ├── auth/            ← login.php
│       ├── dashboard/       ← index.php, students.php
│       ├── student/         ← index.php, form.php
│       ├── teacher/         ← index.php, form.php
│       ├── class/           ← index.php, form.php
│       ├── course/          ← index.php, form.php
│       ├── year/            ← index.php
│       └── report/          ← bilan.php (Planche de Notes)
├── database/
│   ├── schema.sql           ← Script SQL complet (structure + données test)
│   └── seeder_planche.sql   ← Données EIT 2 S3 (20 étudiants + 16 profs + notes)
└── tools/
    └── generate_seeder.php  ← Générateur PHP du fichier seeder_planche.sql
```

---

## 📦 Modules Disponibles

### 1. Authentification & Sécurité
- Connexion/Déconnexion par email + mot de passe (bcrypt)
- Contrôle d'accès par rôles (RBAC) : `super_admin`, `responsable`, `professeur`, `etudiant`
- Protection CSRF sur tous les formulaires
- Échappement XSS sur tous les affichages

### 2. Dashboard
- Statistiques dynamiques : nombre d'étudiants, professeurs, classes, cours
- Interface adaptée au rôle de l'utilisateur connecté

### 3. Gestion des Étudiants
- Liste + recherche des étudiants
- Ajout avec affectation automatique à une classe et une filière
- Édition et suppression avec confirmation

### 4. Gestion des Professeurs
- CRUD complet des enseignants
- Affectation obligatoire à un cours (ECUE) et une classe lors de la création

### 5. Gestion des Classes
- Création de classes avec niveau/filière
- Association automatique à l'année académique active

### 6. Gestion des Cours (ECUEs & UEs)
- Structuration des matières par **Unité d'Enseignement (UE)**
- CRUD complet avec `ue_nom`, `libelle`, `coefficient`

### 7. Années Académiques
- Consultation des années (ex: 2024-2025, 2025-2026)
- Marquage de l'année active

### 8. Planche de Notes (Bilan Semestriel)
- Tableau croisé de toutes les matières × tous les étudiants
- Calcul automatique des **moyennes par ECUE**, par **UE**, et **semestrielle**
- Classement automatique par rang
- Mention VAL / ENC / N-VAL selon la moyenne ≥ 10
- Ligne de **moyenne de la classe** en bas
- Bouton d'impression intégré

---

## 🗄 Base de Données

**Nom de la base :** `ecole_db`

| Table | Description |
|---|---|
| `annee_academique` | Années scolaires avec marqueur d'année active |
| `utilisateur` | Comptes centralisés (email + hash + rôle) |
| `etudiant` | Profils étudiants liés à `utilisateur` |
| `professeur` | Profils enseignants avec spécialité |
| `staff` | Administrateurs (super_admin, responsable) |
| `classe` | Classes/Filières avec niveau et année |
| `cours` | Matières (ECUEs) avec UE parente et coefficient |
| `inscription` | Liaison étudiant ↔ classe ↔ année |
| `affectation_cours` | Liaison prof ↔ cours ↔ classe ↔ année |
| `note` | Notes des évaluations (valeur, type, date) |
| `system_logs` | Logs de sécurité (audit) |

---

## ⚙ Installation XAMPP

### Étape 1 — Cloner/Copier le projet
Placez le dossier dans :
```
C:\xampp\htdocs\Nouveau dossier\
```

### Étape 2 — Importer la base de données
1. Démarrez **XAMPP** (Apache + MySQL)
2. Ouvrez **phpMyAdmin** : `http://localhost/phpmyadmin/`
3. Importez `database/schema.sql` (crée la base + tables + données de test)

### Étape 3 — (Optionnel) Charger les données EIT 2 Semestre 3
Pour charger les **20 étudiants** de la planche + **16 professeurs** + **380 notes** :
- Importez `database/seeder_planche.sql` dans phpMyAdmin

### Étape 4 — Accéder à l'application
```
http://localhost/Nouveau%20dossier/public/
```

---

## 👤 Comptes de Test

> **Mot de passe pour tous les comptes :** `password`

| Rôle | Email |
|---|---|
| Super Admin | `admin@ecole.com` |
| Responsable | `resp@ecole.com` |
| Professeur | `prof.mathematiques@ecole.com` |
| Étudiant | `etudiant1@ecole.com` |

> Après import de `seeder_planche.sql`, les étudiants ont des emails du type `cisse101@ecole.com`, et les professeurs des emails du type `deroh301@ecole.com`. Mot de passe : `password`.

---

## 🔒 Sécurité

| Mesure | Implémentation |
|---|---|
| Injection SQL | Toutes les requêtes utilisent PDO avec requêtes préparées |
| XSS | `htmlspecialchars()` via `Security::escape()` partout |
| CSRF | Token de formulaire synchronisé avec la session |
| Mots de passe | `password_hash()` bcrypt (cost 10) |
| RBAC | `Security::requireRole()` au début de chaque contrôleur |

---

## 📅 Historique des modifications principales

| Date | Modification |
|---|---|
| Mars 2026 | Initialisation MVC + Auth + Dashboard |
| Mars 2026 | CRUD Étudiants, Professeurs, Classes, Cours |
| Mars 2026 | Migration SQLite → MySQL (schema.sql unique) |
| Mars 2026 | Ajout des UEs (Unités d'Enseignement) aux Cours |
| Mars 2026 | Module Planche de Notes (ReportController + bilan.php) |
| Mars 2026 | Seeder SQL complet (EIT 2 S3 : 20 élèves, 16 profs, 380 notes) |

---

*Application développée pour l'ECOLE SUPERIEURE D'INDUSTRIE (ESI) — Filière Sciences de l'Information et de la Communication*
