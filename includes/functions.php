<?php

declare(strict_types=1);

function h(string|int|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function validCsrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

function activeSprint(array $sprints): ?array
{
    foreach ($sprints as $sprint) {
        if (($sprint['status'] ?? '') === 'active') {
            return $sprint;
        }
    }

    return null;
}

function statusLabel(string $status): string
{
    return [
        'todo' => 'To do',
        'in_progress' => 'In progress',
        'done' => 'Done',
    ][$status] ?? $status;
}
function fechaActual(bool $withTime): string
{
    if ($withTime == true) {
        return date('d-m-Y H:i:s');
    }
    else {
        return date('d-m-Y');
    }

}
