<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$data = loadData();

$errors = [];
$values = [
    'name' => '',
    'goal' => '',
    'start_date' => '',
    'end_date' => '',
    'status' => '',
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = trim((string) ($_POST['name'] ?? ''));
    $values['goal'] = (string) ($_POST['goal'] ?? '');
    $values['start_date'] = (string) ($_POST['start_date'] ?? '');
    $values['end_date'] = (string) ($_POST['end_date'] ?? '');
    $values['status'] = (string) ($_POST['status'] ?? '');
    $data = loadData();

    // Validacions 
    if ($values['name'] === '') $errors[] = 'El nom és obligatori.';
    if (strlen($values['goal']) < 8) $errors[] = 'L’objectiu ha de tindre almenys 8 caràcters.';

        $inici = DateTime::createFromFormat('Y-m-d', $values['start_date']);
        $fi = DateTime::createFromFormat('Y-m-d', $values['end_date']);

        if ($inici !== false && $fi !== false && $inici > $fi) {
            $errors[] = 'La data de inici no pot ser posterior a la data de finalització.';
        }

    if (!$errors) {
        $ids = array_column($data['sprints'], 'id');
        $data['sprints'][] = ['id' => $ids ? max($ids) + 1 : 1, 'name' => $values['name'], 'goal' => $values['goal'], 'start_date' => $values['start_date'], 'end_date' => $values['end_date'], 'status' => $values['status']];
        if (saveData($data)) {
            redirect('sprints.php');
        }
        $errors[] = 'No s’ha pogut crear l’esprint.';
    }
}
$pageTitle = 'Crear esprint';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Crear esprint</h1>
        <p class="text-muted">Els nous esprints es creen amb el rol d’estudiant.</p>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post" class="card card-body">
            <input type="hidden" name="csrf" value="<?= h(csrfToken()) ?>">
            <label class="form-label">Nom</label>
            <input class="form-control mb-3" name="name" value="<?= h($values['name']) ?>" required>

            <label class="form-label">Objectiu</label>
            <input class="form-control mb-3" name="goal" value="<?= h($values['goal']) ?>" required>

            <label class="form-label">Data d'inici</label>
            <input class="form-control mb-3" type="date" name="start_date" value="<?= h($values['start_date']) ?>" required>

            <label class="form-label">Data de finalització</label>
            <input class="form-control mb-3" type="date" name="end_date" value="<?= h($values['end_date']) ?>" required>

            <label class="form-label">Estat</label>
            <select class="form-control mb-3" name="status" required>
                <option value="">Selecciona un estat</option>
                <option value="pendent" <?= $values['status'] === 'pendent' ? 'selected' : '' ?>>Pendent</option>
                <option value="en curs" <?= $values['status'] === 'en curs' ? 'selected' : '' ?>>En curs</option>
                <option value="finalitzat" <?= $values['status'] === 'finalitzat' ? 'selected' : '' ?>>Finalitzat</option>
            </select>

            <button class="btn btn-primary">Crear esprint</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
