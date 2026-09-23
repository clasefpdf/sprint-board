<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$id = (int) ($_GET['id'] ?? 0);
$data = loadData();
$task = findRecord($data['tasks'], $id);

// Mostrar les ultimes 3 tasqques visitades al index
$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];

foreach ($_SESSION['tasks'] as $taskId) {
    if ($taskId === $id) {
        $_SESSION['tasks'] = array_diff($_SESSION['tasks'], [$taskId]);
        break;
    }
}
$_SESSION['tasks'][] = (int) ($_SESSION['task_id'] ?? 0);
$_SESSION['task_id'] = $id;

if (!$task) {
    http_response_code(404);
    exit('Tasca no trobada');
}

$errors = [];

$task['team_name'] = recordName(
    $data['teams'],
    $task['team_id'] ?? null,
    'Sense equip'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim((string) ($_POST['body'] ?? ''));
    if (!validCsrf()) {
        $errors[] = 'La sessió no és vàlida. Torna a carregar el formulari.';
    }
    if ($body === '') {
        $errors[] = 'El comentari no pot estar buit.';
    }

    if (!$errors) {
    $data['comments'][] = [
        'id' => $data['next_ids']['comments']++,
        'task_id' => $id,
        'user_id' => $_SESSION['user_id'],
        'body' => $body,
        'created_at' => date('Y-m-d H:i:s'),
    ];
        if (saveData($data)) {
            redirect('task.php?id=' . $id);
        }

        $errors[] = 'No s’ha pogut guardar el comentari.';
    }
}

$comments = [];
foreach ($data['comments'] as $comment) {
    if ((int) $comment['task_id'] === $id) {
        $comment['name'] = recordName($data['users'], $comment['user_id'], 'Usuari');
        $comments[] = $comment;
    }
}
$comments = array_reverse($comments);

$pageTitle = $task['title'];
require __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between">
    <div>
        <h1><?= h($task['title']) ?></h1>
        <a class="btn btn-primary" href="task-edit.php?id=<?= $task['id'] ?>">Editar tasca</a>
        <p><?= h($task['description']) ?></p>
        <p class="text-muted">
            Estat: <?= h($task['status']) ?> · Equip: <?= h($task['team_name']) ?>
        </p>
    </div>
    <a href="board.php" class="btn btn-outline-secondary align-self-start">Tornar</a>
</div>

<hr>

<h2 class="h4">Comentaris</h2>

<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endforeach; ?>

<?php foreach ($comments as $comment): ?>
    <div class="card mb-2">
        <div class="card-body">
            <strong><?= h($comment['name']) ?></strong>
            <p class="mb-0"><?= h($comment['body']) ?></p>
        </div>
    </div>
<?php endforeach; ?>

<form method="post" class="mt-3">
    <input type="hidden" name="csrf" value="<?= h(csrfToken()) ?>">
    <textarea
        name="body"
        class="form-control mb-2"
        placeholder="Escriu un comentari..."
        required
    ></textarea>
    <button class="btn btn-primary">Afegir comentari</button>
</form>


<?php require __DIR__ . '/../includes/footer.php'; ?>

