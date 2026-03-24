<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><i class="fas fa-chalkboard-teacher text-success me-2"></i> Corps Enseignant</h2>
        <p class="text-muted small mb-0">Professeurs et intervenants par classe d'affectation</p>
    </div>
    <div class="col-auto">
        <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/create" class="btn btn-success d-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i> Ajouter un Enseignant
        </a>
    </div>
</div>

<style>
.folder-card {
    border: none; border-radius: 14px; cursor: pointer;
    transition: all 0.25s ease; overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}
.folder-card:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(0,0,0,0.15); }
.folder-top { height: 14px; border-radius: 8px 12px 0 0; width: 55%; }
.folder-body { padding: 20px; border-radius: 0 10px 14px 14px; border: 3px solid; border-top: none; min-height: 130px; }
.class-panel { display: none; }
.class-panel.show { display: block; }
.prof-card { background: #f8f9fa; border-radius: 12px; transition: background .2s; }
.prof-card:hover { background: #e9ecef; }
.folder-color-0 { color: #198754; border-color: #198754; }
.folder-top-0   { background: #198754; }
.folder-color-1 { color: #0d6efd; border-color: #0d6efd; }
.folder-top-1   { background: #0d6efd; }
.folder-color-2 { color: #6f42c1; border-color: #6f42c1; }
.folder-top-2   { background: #6f42c1; }
.folder-color-3 { color: #fd7e14; border-color: #fd7e14; }
.folder-top-3   { background: #fd7e14; }
.folder-color-4 { color: #dc3545; border-color: #dc3545; }
.folder-top-4   { background: #dc3545; }
</style>

<?php
$palette = ['198754','0d6efd','6f42c1','fd7e14','dc3545'];
?>

<?php if (!empty($classesprofs)): ?>
<!-- NIVEAU 1 : Dossiers Classe -->
<div class="row g-4 mb-4">
    <?php foreach($classesprofs as $ci => $cl):
        $c = $ci % 5;
        $folderId = 'prof-panel-' . $cl->id_classe;
        $count = count($profsByClass[$cl->id_classe] ?? []);
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="folder-card" onclick="openFolder('<?php echo $folderId; ?>')">
            <div class="folder-top folder-top-<?php echo $c; ?>"></div>
            <div class="folder-body folder-color-<?php echo $c; ?>">
                <div>
                    <i class="fas fa-folder fa-2x mb-2"></i>
                    <div class="fw-bold fs-6 lh-sm"><?php echo htmlspecialchars($cl->nom_classe); ?></div>
                    <small class="opacity-75"><?php echo htmlspecialchars($cl->niveau ?? ''); ?></small>
                </div>
                <div class="mt-3 d-flex gap-1 flex-wrap">
                    <span class="badge bg-white text-dark shadow-sm">
                        <i class="fas fa-chalkboard-teacher me-1"></i><?php echo $count; ?> prof(s)
                    </span>
                    <span class="badge bg-white text-dark shadow-sm">
                        <i class="fas fa-calendar me-1"></i><?php echo htmlspecialchars($cl->annee_libelle); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- NIVEAU 2 : Panneau des profs de la classe -->
<?php foreach($classesprofs as $ci => $cl):
    $c = $ci % 5;
    $folderId = 'prof-panel-' . $cl->id_classe;
    $profs = $profsByClass[$cl->id_classe] ?? [];
    // Group courses per prof
    $profData = [];
    foreach($profs as $row) {
        $pid = $row->id_prof;
        if (!isset($profData[$pid])) {
            $profData[$pid] = ['info' => $row, 'cours' => []];
        }
        $profData[$pid]['cours'][] = $row->cours_libelle;
    }
?>
<div class="class-panel" id="<?php echo $folderId; ?>">
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white" style="border-bottom: 3px solid #<?php echo $palette[$c]; ?>40;">
            <div>
                <h5 class="m-0 fw-bold">
                    <i class="fas fa-folder-open me-2" style="color:#<?php echo $palette[$c]; ?>"></i>
                    <?php echo htmlspecialchars($cl->nom_classe); ?>
                </h5>
                <small class="text-muted"><?php echo htmlspecialchars($cl->niveau ?? ''); ?> — Année <?php echo htmlspecialchars($cl->annee_libelle); ?></small>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="closeFolder('<?php echo $folderId; ?>')">
                <i class="fas fa-times me-1"></i> Fermer
            </button>
        </div>
        <div class="card-body">
            <?php if(empty($profData)): ?>
                <p class="text-muted text-center py-3">Aucun professeur affecté à cette classe.</p>
            <?php else: ?>
            <div class="row g-3">
                <?php foreach($profData as $pid => $pd):
                    $prof = $pd['info'];
                    $cours = array_unique($pd['cours']);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="prof-card p-3 h-100">
                        <div class="d-flex align-items-start gap-3 mb-2">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($prof->prenom . ' ' . $prof->nom); ?>&background=<?php echo $palette[$c]; ?>&color=fff&size=64"
                                 class="rounded-circle flex-shrink-0" width="52" height="52">
                            <div class="overflow-hidden flex-grow-1">
                                <div class="fw-bold text-truncate"><?php echo \App\Core\Security::escape($prof->prenom . ' ' . $prof->nom); ?></div>
                                <div class="small text-muted text-truncate">
                                    <i class="fas fa-at me-1"></i><?php echo \App\Core\Security::escape($prof->email ?? '—'); ?>
                                </div>
                                <span class="badge mt-1" style="background:#<?php echo $palette[$c]; ?>25; color:#<?php echo $palette[$c]; ?>">
                                    <?php echo \App\Core\Security::escape($prof->specialite ?? ''); ?>
                                </span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted fw-bold d-block mb-1"><i class="fas fa-book me-1"></i>Cours dispensés :</small>
                            <?php foreach($cours as $coursName): ?>
                                <span class="badge bg-light text-dark border me-1 mb-1"><?php echo \App\Core\Security::escape($coursName); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex gap-1 mt-3 justify-content-end">
                            <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/edit/<?php echo $prof->id_prof; ?>"
                               class="btn btn-xs btn-outline-primary px-2 py-1" style="font-size:.75rem;">
                                <i class="fas fa-edit me-1"></i>Modifier
                            </a>
                            <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/delete/<?php echo $prof->id_prof; ?>"
                               onclick="return confirm('Supprimer <?php echo addslashes($prof->nom); ?> ?');"
                               class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size:.75rem;">
                                <i class="fas fa-trash"></i>Supprimer
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php else: ?>
<!-- Fallback tableau simple si aucune affectation -->
<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Aucun professeur affecté à une classe. Liste complète ci-dessous :</div>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0 text-center">
        <thead class="table-dark">
            <tr>
                <th>Matricule</th><th>Identité</th><th>Email</th><th>Spécialité</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($teachers as $t): ?>
            <tr>
                <td class="text-muted"><?php echo \App\Core\Security::escape($t->matricule); ?></td>
                <td class="fw-bold"><?php echo \App\Core\Security::escape($t->nom . ' ' . $t->prenom); ?></td>
                <td><?php echo \App\Core\Security::escape($t->email); ?></td>
                <td><span class="badge bg-secondary"><?php echo \App\Core\Security::escape($t->specialite); ?></span></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/edit/<?php echo $t->id_prof; ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=teacher/delete/<?php echo $t->id_prof; ?>" onclick="return confirm('Supprimer ?');" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script>
function openFolder(id) {
    document.querySelectorAll('.class-panel').forEach(p => p.classList.remove('show'));
    const panel = document.getElementById(id);
    if(panel) { panel.classList.add('show'); setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100); }
}
function closeFolder(id) { document.getElementById(id).classList.remove('show'); }
</script>

<?php require_once '../app/Views/layout/footer.php'; ?>
