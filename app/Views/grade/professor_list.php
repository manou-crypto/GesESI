<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row align-items-center mb-4 text-center text-md-start">
    <div class="col">
        <h2 class="fw-bold mb-0 text-primary"><i class="fas fa-edit me-2"></i> Mes Enseignements</h2>
        <p class="text-muted">Sélectionnez une classe pour renseigner les notes</p>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($assignments)): ?>
        <div class="col-12">
            <div class="alert alert-info shadow-sm border-0 rounded-3 p-4 text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>Aucune affectation de cours trouvée pour l'année en cours.</h5>
                <p class="mb-0">Si vous pensez qu'il s'agit d'une erreur, contactez l'administration.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($assignments as $a): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-stat">
                <div class="p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 border border-primary-subtle">
                            <?php echo htmlspecialchars($a->niveau); ?>
                        </span>
                        <div class="bg-white p-2 rounded-3 shadow-sm">
                            <i class="fas fa-book text-warning fa-lg"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($a->cours_nom); ?></h5>
                    <p class="text-muted small mb-3"><i class="fas fa-users me-1"></i> Classe : <strong><?php echo htmlspecialchars($a->nom_classe); ?></strong></p>
                    
                    <a href="<?php echo BASE_URL; ?>/index.php?url=grade/enter/<?php echo $a->id_affectation; ?>" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">
                        Saisir les Notes <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
