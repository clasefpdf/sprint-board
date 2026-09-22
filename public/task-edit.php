<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$data = loadData();
$errors = [];

$id = (int) ($_GET['id'] ?? 0);
$task = findRecord($data['tasks'] ?? [], $id);
$values = [
    'title' => $task['title'] ?? '',
    'description' => $task['description'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['title'] = trim((string) ($_POST['title'] ?? ''));
    $values['description'] = trim((string) ($_POST['description'] ?? ''));
    if (!validCsrf()) $errors[] = 'La sessió no és vàlida.';
    if ($values['title'] === '') $errors[] = 'El títol és obligatori.';
    if ($values['description'] === '') $errors[] = 'La descripció és obligatòria.';
    if (!$errors) {
        foreach ($data['tasks'] as $key => $taskUpdate) {
            if ((int) $taskUpdate['id'] === $id) {
                $data['tasks'][$key]['title'] = $values['title'];
                $data['tasks'][$key]['description'] = $values['description'];
                break;
            }
        }

        if (saveData($data)) {
            redirect('task.php?id=' . $id);
        }

        $errors[] = 'No s’ha pogut guardar la tasca. Intenta-ho de nou.';
    }
}
$pageTitle = 'Nova tasca';
require __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <h1>Edita la tasca</h1>


        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endforeach; ?>

        <form method="post" class="card card-body">
            <input type="hidden" name="csrf" value="<?= h(csrfToken()) ?>">

            <label class="form-label">Títol actual:     <?= h($task['title']) ?></label>
            <input class="form-control mb-3" name="title" value="<?= h($values['title']) ?>" required>

            <label class="form-label">Descripció actual:     <?= h($task['description']) ?></label>
            <textarea class="form-control mb-3" name="description" rows="4" required><?= h($values['description']) ?></textarea>

            <button class="btn btn-primary">Editar tasca</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
