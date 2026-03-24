<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto mt-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header text-white py-3 shadow-sm" style="background: linear-gradient(90deg, #2E7D32, #4CAF50);">
                <h4 class="m-0"><i class="fas fa-chalkboard-teacher"></i> <?php echo htmlspecialchars($title ?? 'Formulaire Enseignant'); ?></h4>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/index.php?url=teacher/<?php echo $action; ?>" method="POST">
                    <div class="mb-3">
                        <label>Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" value="<?php echo isset($teacher) ? \App\Core\Security::escape($teacher->nom) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control" value="<?php echo isset($teacher) ? \App\Core\Security::escape($teacher->prenom) : ''; ?>" required>
                    </div>
                    <?php if(!isset($teacher)): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de connexion <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="Ex: p.nom@ecole.com">
                        <small class="text-muted">Mot de passe de connexion par défaut : <strong>password</strong></small>
                    </div>
                    <?php endif; ?>
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label>Description libre de la spécialité <span class="text-danger">*</span></label>
                            <input type="text" name="specialite" class="form-control" placeholder="ex: Prof. certifié de Mathématiques" value="<?php echo isset($teacher) ? \App\Core\Security::escape($teacher->specialite) : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Affectation : Matière enseignée <span class="text-danger">*</span></label>
                            <select name="fk_cours" class="form-select" required>
                                <option value="" disabled <?php echo !isset($teacher->fk_cours) ? 'selected' : ''; ?>>Sélectionner une Matière...</option>
                                <?php if(isset($courses)): foreach($courses as $c): ?>
                                    <option value="<?php echo $c->id_cours; ?>" <?php echo (isset($teacher->fk_cours) && $teacher->fk_cours == $c->id_cours) ? 'selected' : ''; ?>>
                                        <?php echo \App\Core\Security::escape($c->libelle); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Affectation : Classe / Groupe cible <span class="text-danger">*</span></label>
                            <select name="fk_classe" class="form-select" required>
                                <option value="" disabled <?php echo !isset($teacher->fk_classe) ? 'selected' : ''; ?>>Sélectionner une Classe...</option>
                                <?php if(isset($classes)): foreach($classes as $cl): ?>
                                    <option value="<?php echo $cl->id_classe; ?>" <?php echo (isset($teacher->fk_classe) && $teacher->fk_classe == $cl->id_classe) ? 'selected' : ''; ?>>
                                        <?php echo \App\Core\Security::escape($cl->nom_classe); ?> (<?php echo \App\Core\Security::escape($cl->niveau); ?>)
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>


                    <button class="btn btn-success px-4 py-2"><i class="fas fa-save"></i> Enregistrer le Profil</button>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=teacher" class="btn btn-outline-secondary px-4 py-2 ms-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
