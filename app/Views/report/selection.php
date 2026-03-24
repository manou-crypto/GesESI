<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
            <div class="card-header text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
                <h4 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i> Génération de Planche</h4>
            </div>
            <div class="card-body p-4 text-center">
                <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                <h5 class="text-muted mb-4">Sélectionnez une classe pour générer le bilan des notes</h5>
                
                <form action="<?php echo BASE_URL; ?>/index.php" method="GET">
                    <input type="hidden" name="url" value="report/bilan">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-start">Classe Académique</label>
                        <select name="classe" class="form-select form-select-lg border-2" required>
                            <option value="" disabled selected>Choisir une classe...</option>
                            <?php foreach($classes as $c): ?>
                                <option value="<?php echo $c->id_classe; ?>"><?php echo htmlspecialchars($c->nom_classe); ?> (<?php echo htmlspecialchars($c->niveau); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm py-3 fw-bold">
                        <i class="fas fa-magic me-2"></i> Faire apparaître la Planche
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
