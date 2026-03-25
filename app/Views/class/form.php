<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto mt-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
                <h4 class="m-0"><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($title ?? 'Formulaire Classe'); ?></h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?url=class/<?php echo $action; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la classe <span class="text-danger">*</span></label>
                        <input type="text" name="nom_classe" class="form-control" value="<?php echo isset($class_data) ? \App\Core\Security::escape($class_data->nom_classe) : ''; ?>" required placeholder="ex: L1 Informatique">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Niveau / Filière <span class="text-danger">*</span></label>
                        <input type="text" name="niveau" class="form-control" value="<?php echo isset($class_data) ? \App\Core\Security::escape($class_data->niveau) : ''; ?>" required placeholder="ex: Licence 1, Master 2...">
                    </div>

                    <button class="btn btn-primary px-4 py-2"><i class="fas fa-save"></i> Enregistrer la classe</button>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=class" class="btn btn-outline-secondary px-4 py-2 ms-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
