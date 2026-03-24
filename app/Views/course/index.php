<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><i class="fas fa-book-open text-primary me-2"></i> Catalogue des Matières</h2>
        <p class="text-muted small mb-0">Organisation par Unité d'Enseignement (UE)</p>
    </div>
    <div class="col-auto d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>/index.php?url=course/create" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i> Nouvelle Matière (ECUE)
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
.ue-section { border-left: 4px solid; padding-left: 12px; margin-bottom: 18px; }
.course-row { background: #f8f9fa; border-radius: 8px; padding: 10px 14px; margin-bottom: 8px; transition: background .2s; }
.course-row:hover { background: #e9ecef; }
.folder-color-0 { color: #0d6efd; border-color: #0d6efd; }
.folder-top-0   { background: #0d6efd; }
.folder-color-1 { color: #6f42c1; border-color: #6f42c1; }
.folder-top-1   { background: #6f42c1; }
.folder-color-2 { color: #fd7e14; border-color: #fd7e14; }
.folder-top-2   { background: #fd7e14; }
.folder-color-3 { color: #198754; border-color: #198754; }
.folder-top-3   { background: #198754; }
.folder-color-4 { color: #dc3545; border-color: #dc3545; }
.folder-top-4   { background: #dc3545; }
.ue-color-0 { border-color: #0d6efd; }
.ue-color-1 { border-color: #6f42c1; }
.ue-color-2 { border-color: #fd7e14; }
.ue-color-3 { border-color: #198754; }
.ue-color-4 { border-color: #dc3545; }
</style>

<?php
$ueColors = ['0d6efd','6f42c1','fd7e14','198754','dc3545'];
$folderColors = [0,1,2,3,4];
?>

<!-- ===== NIVEAU 1 : Grille de Dossiers par Classe ===== -->
<?php if (!empty($classesCours)): ?>
<div class="row g-4 mb-4" id="class-folder-grid">
    <?php foreach($classesCours as $ci => $cl):
        $c = $folderColors[$ci % 5];
        $folderId = 'class-panel-' . $cl->id_classe;
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
                        <i class="fas fa-book me-1"></i><?php echo count($coursesByClass[$cl->id_classe] ?? []); ?> cours
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

<!-- ===== NIVEAU 2 : Panneau des cours de la classe ===== -->
<?php foreach($classesCours as $ci => $cl):
    $c = $folderColors[$ci % 5];
    $folderId = 'class-panel-' . $cl->id_classe;
    $classCourses = $coursesByClass[$cl->id_classe] ?? [];

    // Grouper par UE
    $byUE = [];
    foreach($classCourses as $course) {
        $byUE[$course->ue_nom][] = $course;
    }
?>
<div class="class-panel" id="<?php echo $folderId; ?>">
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white border-bottom-0" style="border-bottom: 3px solid #<?php echo $ueColors[$c]; ?>20;">
            <div>
                <h5 class="m-0 fw-bold">
                    <i class="fas fa-folder-open me-2" style="color:#<?php echo $ueColors[$c]; ?>"></i>
                    <?php echo htmlspecialchars($cl->nom_classe); ?>
                </h5>
                <small class="text-muted"><?php echo htmlspecialchars($cl->niveau ?? ''); ?> — Année <?php echo htmlspecialchars($cl->annee_libelle); ?></small>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="closeFolder('<?php echo $folderId; ?>')">
                <i class="fas fa-times me-1"></i> Fermer
            </button>
        </div>
        <div class="card-body">
            <?php if(empty($byUE)): ?>
                <p class="text-muted text-center py-3">Aucun cours affecté à cette classe.</p>
            <?php else:
                $ueIdx = 0;
                foreach($byUE as $ueName => $ueCourses):
                    $uc = $folderColors[$ueIdx % 5];
            ?>
            <div class="ue-section ue-color-<?php echo $uc; ?> mb-4">
                <h6 class="fw-bold mb-3" style="color:#<?php echo $ueColors[$uc]; ?>">
                    <i class="fas fa-layer-group me-2"></i><?php echo htmlspecialchars($ueName); ?>
                </h6>
                <div class="row g-2">
                    <?php foreach($ueCourses as $course): ?>
                    <div class="col-md-6">
                        <div class="course-row d-flex align-items-center gap-3">
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold text-dark text-truncate">
                                    <?php echo \App\Core\Security::escape($course->libelle); ?>
                                </div>
                                <small class="text-muted">
                                    <span class="badge bg-secondary-subtle text-dark me-1">Coef. <?php echo (float)$course->coefficient; ?></span>
                                    <?php if(!empty($course->prof_nom)): ?>
                                    <i class="fas fa-chalkboard-teacher me-1 text-muted"></i>
                                    <?php echo \App\Core\Security::escape($course->prof_nom . ' ' . $course->prof_prenom); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <a href="<?php echo BASE_URL; ?>/index.php?url=course/edit/<?php echo $course->id_cours; ?>"
                                   class="btn btn-xs btn-outline-primary px-2 py-1" style="font-size:.75rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/index.php?url=course/delete/<?php echo $course->id_cours; ?>"
                                   onclick="return confirm('Supprimer ce cours ?');"
                                   class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size:.75rem;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $ueIdx++; endforeach; endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php else: ?>
<!-- Fallback : tous les cours si aucune affectation -->
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Aucun cours n'est encore affecté à une classe. Voici tous les cours enregistrés :
</div>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th>Unité d'Enseignement (UE)</th>
                <th>Matière (ECUE)</th>
                <th>Coefficient</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($courses as $c): ?>
            <tr>
                <td class="text-muted small"><?php echo \App\Core\Security::escape($c->ue_nom); ?></td>
                <td class="fw-bold"><?php echo \App\Core\Security::escape($c->libelle); ?></td>
                <td><span class="badge bg-secondary"><?php echo (float)$c->coefficient; ?></span></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=course/edit/<?php echo $c->id_cours; ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></a>
                    <a href="<?php echo BASE_URL; ?>/index.php?url=course/delete/<?php echo $c->id_cours; ?>" onclick="return confirm('Supprimer ?');" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
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
    if(panel) {
        panel.classList.add('show');
        setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
    }
}
function closeFolder(id) {
    document.getElementById(id).classList.remove('show');
}
</script>

<?php require_once '../app/Views/layout/footer.php'; ?>
