<?php
declare(strict_types=1);

/** Escape user-originated content for HTML output. */
function e(mixed $value): string
{
    return htmlentities((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function stars(int $rate): string
{
    $rate = max(0, min(5, $rate));
    return str_repeat('★', $rate) . str_repeat('☆', 5 - $rate);
}

function room_photo(string $file): string
{
    return '/assets/images/rooms/' . rawurlencode($file);
}

function is_ajax(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

/** Error reply for actions that serve both plain form posts and Ajax calls. */
function action_error(string $message, string $redirectTo, int $status = 422): never
{
    if (is_ajax()) {
        json_response(['ok' => false, 'error' => $message], $status);
    }
    flash('error', $message);
    redirect($redirectTo);
}
