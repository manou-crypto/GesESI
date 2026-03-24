<?php require_once '../app/Views/layout/header.php'; ?>

<!-- Content Header -->
<div class="row">
    <div class="col-12 bg-info text-white p-3 rounded d-flex align-items-center mb-4 mt-3" style="background: linear-gradient(90deg, #3498db, #2980b9);">
        <h2 class="m-0"><i class="fas fa-user-graduate me-3"></i> Espace Étudiant</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white fs-5 fw-bold">
                Mes derniers résultats académiques
            </div>
            <div class="card-body">
                <p class="text-muted">Vos résultats seront disponibles ici une fois validés par vos enseignants ou le responsable pédagogique.</p>
                <!-- Simulation d'un relevé de note -->
                <table class="table table-bordered text-center mt-3">
                    <thead class="bg-light">
                        <tr>
                            <th>Matière</th>
                            <th>Moyenne</th>
                            <th>Rang</th>
                            <th>Avis du conseil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Algèbre</td>
                            <td class="text-success fw-bold">15/20</td>
                            <td>4e</td>
                            <td>Bon travail</td>
                        </tr>
                        <tr>
                            <td>Physique</td>
                            <td class="text-danger fw-bold">09/20</td>
                            <td>31e</td>
                            <td>Doit s'améliorer</td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-outline-primary mt-3"><i class="fas fa-file-pdf"></i> Imprimer mon relevé PDF</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/Views/layout/footer.php'; ?>
