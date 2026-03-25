<?php require_once 'app/Views/layout/header.php'; ?>

<!-- Content Header -->
<div class="row">
    <div class="col-12 bg-success text-white p-3 rounded d-flex align-items-center mb-4 mt-3" style="background: linear-gradient(90deg, #1abc9c, #2ecc71);">
        <h2 class="m-0"><i class="fas fa-chalkboard-teacher me-3"></i> Bonjour Professeur !</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card card-stat bg-white p-4 text-center">
            <h3><i class="fas fa-users text-primary mb-2"></i></h3>
            <h4>Mes Classes</h4>
            <p class="text-muted">Gérer les étudiants de vos classes assignées.</p>
            <a href="#" class="btn btn-primary mt-2">Voir les classes</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stat bg-white p-4 text-center">
            <h3><i class="fas fa-edit text-warning mb-2"></i></h3>
            <h4>Saisie des notes</h4>
            <p class="text-muted">Entrer et valider les notes d'évaluations et devoirs.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?url=grades" class="btn btn-warning mt-2 text-white">Aller à la saisie</a>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
