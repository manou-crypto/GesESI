<?php require_once '../app/Views/layout/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><i class="fas fa-folder-open text-warning me-2"></i> Dossiers des Étudiants</h2>
        <p class="text-muted small mb-0">Consultez et gérez les dossiers académiques par classe</p>
    </div>
    <div class="col-auto">
        <a href="<?php echo BASE_URL; ?>/index.php?url=student/create" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i> Ajouter un Étudiant
        </a>
    </div>
</div>

<style>
/* --- Folder Icons (level 1) --- */
.folder-card {
    background: #fff;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}
.folder-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0,0,0,0.14);
}
.folder-card .folder-top {
    width: 55%;
    height: 14px;
    border-radius: 8px 12px 0 0;
    margin-bottom: -1px;
}
.folder-card .folder-body {
    padding: 20px;
    border-radius: 0 10px 14px 14px;
    border: 3px solid;
    border-top: none;
    min-height: 130px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* --- Student mini-cards (level 2) --- */
.student-minicard {
    background: #f8f9fa;
    border-radius: 10px;
    transition: background 0.2s;
}
.student-minicard:hover {
    background: #e9ecef;
}

/* --- Collapse animation --- */
.class-panel { display: none; }
.class-panel.show { display: block; }

/* Color palettes for folders (Variations de Bleu Ciel) */
.folder-color-0 { color: #00BFFF; border-color: #00BFFF; }
.folder-top-0   { background: #00BFFF; }
.folder-color-1 { color: #1E90FF; border-color: #1E90FF; }
.folder-top-1   { background: #1E90FF; }
.folder-color-2 { color: #0080FF; border-color: #0080FF; }
.folder-top-2   { background: #0080FF; }
.folder-color-3 { color: #4682B4; border-color: #4682B4; }
.folder-top-3   { background: #4682B4; }
.folder-color-4 { color: #87CEEB; border-color: #87CEEB; }
.folder-top-4   { background: #87CEEB; }
</style>

<?php if (!empty($students)):
    // Regrouper par classe
    $grouped = [];
    foreach($students as $s) {
        $key = $s->section ?? 'Sans classe';
        $grouped[$key][] = $s;
    }
    $colors = [0, 1, 2, 3, 4];
    $i = 0;
?>

<!-- NIVEAU 1 : Grille des Dossiers de Classes -->
<div class="row g-4 mb-5" id="folder-grid">
    <?php foreach($grouped as $classe => $eleves):
        $c = $colors[$i % count($colors)];
        $folderId = 'panel-' . $i;
    ?>
    <div class="col-6 col-md-4 col-lg-3">
        <div class="folder-card" onclick="openFolder('<?php echo $folderId; ?>', '<?php echo addslashes($classe); ?>')">
            <div class="folder-top folder-top-<?php echo $c; ?>"></div>
            <div class="folder-body folder-color-<?php echo $c; ?>">
                <div>
                    <i class="fas fa-folder fa-2x mb-2"></i>
                    <div class="fw-bold fs-6"><?php echo htmlspecialchars($classe); ?></div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white text-dark shadow-sm">
                        <i class="fas fa-users me-1"></i><?php echo count($eleves); ?> étudiant(s)
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php $i++; endforeach; ?>
</div>

<!-- NIVEAU 2 : Panneau des étudiants (s'ouvre sous la grille) -->
<?php $i = 0; foreach($grouped as $classe => $eleves):
    $c = $colors[$i % count($colors)];
    $folderId = 'panel-' . $i;
?>
<div class="class-panel" id="<?php echo $folderId; ?>">
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-header d-flex justify-content-between align-items-center rounded-top-3 py-3" style="background: var(--folder-c); border-bottom: 3px solid;">
            <h5 class="m-0 fw-bold">
                <i class="fas fa-folder-open me-2"></i> <?php echo htmlspecialchars($classe); ?>
                <span class="badge bg-white text-dark ms-2 fw-normal"><?php echo count($eleves); ?> étudiants</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary" onclick="closeFolder('<?php echo $folderId; ?>')">
                <i class="fas fa-times me-1"></i> Fermer
            </button>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach($eleves as $student): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="student-minicard p-3 d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student->prenom . ' ' . $student->nom); ?>&background=<?php echo ['0d6efd','6f42c1','fd7e14','198754','dc3545'][$c]; ?>&color=fff&size=64"
                             class="rounded-circle flex-shrink-0" width="48" height="48">
                        <div class="overflow-hidden flex-grow-1">
                            <div class="fw-bold text-truncate"><?php echo \App\Core\Security::escape($student->prenom . ' ' . $student->nom); ?></div>
                            <div class="text-muted small text-truncate">
                                <i class="fas fa-at me-1"></i><?php echo \App\Core\Security::escape($student->email ?? '—'); ?>
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-id-card me-1"></i><?php echo \App\Core\Security::escape($student->matricule ?? '—'); ?>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 ms-auto">
                            <a href="<?php echo BASE_URL; ?>/index.php?url=student/edit/<?php echo $student->id_etudiant; ?>"
                               class="btn btn-xs btn-outline-primary px-2 py-1" style="font-size:0.75rem;" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/index.php?url=student/delete/<?php echo $student->id_etudiant; ?>"
                               onclick="return confirm('Supprimer le dossier de <?php echo addslashes($student->nom); ?> ?');"
                               class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size:0.75rem;" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php $i++; endforeach; ?>

<script>
function openFolder(id, name) {
    // Fermer tous les panneaux d'abord
    document.querySelectorAll('.class-panel').forEach(p => p.classList.remove('show'));
    // Ouvrir le panneau cliqué
    const panel = document.getElementById(id);
    panel.classList.add('show');
    // Scroll vers le panneau
    setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
}
function closeFolder(id) {
    document.getElementById(id).classList.remove('show');
}
</script>

<?php else: ?>
<div class="text-center py-5 text-muted">
    <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
    <p class="fs-5">Aucun dossier de classe enregistré.</p>
    <a href="<?php echo BASE_URL; ?>/index.php?url=student/create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Créer le premier dossier
    </a>
</div>
<?php endif; ?>

<?php require_once '../app/Views/layout/footer.php'; ?>
