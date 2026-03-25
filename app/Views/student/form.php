<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto mt-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #00BFFF, #87CEEB);">
                <h4 class="m-0"><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($title); ?></h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?url=student/<?php echo $action; ?>" method="POST">
                    <div class="mb-3">
                        <label>Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" value="<?php echo isset($student) ? \App\Core\Security::escape($student->nom) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control" value="<?php echo isset($student) ? \App\Core\Security::escape($student->prenom) : ''; ?>" required>
                    </div>
                    <?php if(!$student): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de connexion <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="Ex: e.nom@ecole.com">
                        <small class="text-muted">Cet email servira d'identifiant. Mot de passe par défaut : <strong>password</strong></small>
                    </div>
                    <?php endif; ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Classe / Filière <span class="text-danger">*</span></label>
                            <select name="classe" class="form-select" required>
                                <option value="" disabled <?php echo !isset($student->fk_classe) ? 'selected' : ''; ?>>Sélectionner une classe...</option>
                                <?php if(isset($classes)): ?>
                                    <?php foreach($classes as $c): ?>
                                        <option value="<?php echo $c->id_classe; ?>" <?php echo (isset($student->fk_classe) && $student->fk_classe == $c->id_classe) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c->nom_classe); ?> <?php echo $c->niveau ? '(' . htmlspecialchars($c->niveau) . ')' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">L'étudiant sera automatiquement inscrit pour l'année active.</small>
                        </div>
                    </div>

                    <button class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=dashboard/students" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
