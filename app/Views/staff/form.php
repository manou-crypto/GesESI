<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i> <?php echo $title; ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo BASE_URL; ?>/index.php?url=staff/<?php echo $action; ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?php echo $member ? htmlspecialchars($member->nom) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?php echo $member ? htmlspecialchars($member->prenom) : ''; ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $member ? htmlspecialchars($member->email) : ''; ?>" <?php echo $member ? 'readonly' : ''; ?> required>
                            <?php if(!$member): ?>
                                <small class="text-muted">Mot de passe par défaut : <strong>password</strong></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Rôle</label>
                            <select name="role" id="roleSelect" class="form-select" required onchange="toggleClasseField()">
                                <option value="responsable" <?php echo ($member && $member->role == 'responsable') ? 'selected' : ''; ?>>Responsable</option>
                                <option value="super_admin" <?php echo ($member && $member->role == 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                            </select>
                        </div>
                        <div class="col-12" id="classeField" style="display: none;">
                            <label class="form-label fw-bold">Classe Assignée</label>
                            <select name="fk_classe" class="form-select">
                                <option value="">Choisir une classe...</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c->id_classe; ?>" <?php echo ($member && $member->fk_classe == $c->id_classe) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c->nom_classe); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Uniquement pour les responsables.</small>
                        </div>
                    </div>

                    <script>
                        function toggleClasseField() {
                            const role = document.getElementById('roleSelect').value;
                            const field = document.getElementById('classeField');
                            field.style.display = (role === 'responsable') ? 'block' : 'none';
                        }
                        // Initial check
                        document.addEventListener('DOMContentLoaded', toggleClasseField);
                    </script>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-danger px-4">Enregistrer</button>
                        <a href="<?php echo BASE_URL; ?>/index.php?url=staff/index" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>
