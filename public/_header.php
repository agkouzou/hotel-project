<?php
/** @var array|null $currentUser  @var string $pageTitle */
$pageTitle ??= 'Hotel';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(App\Support\Csrf::token()) ?>">
    <title><?= e($pageTitle) ?> | Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/index.php">Hotel</a>
        <ul class="navbar-nav ms-auto">
            <?php if ($currentUser): ?>
                <li class="nav-item"><a class="nav-link" href="/profile.php"><?= e($currentUser['name']) ?></a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<main class="container">
    <?php foreach (take_flash() as $msg): ?>
        <div class="alert alert-<?= $msg['type'] === 'error' ? 'danger' : e($msg['type']) ?>"><?= e($msg['message']) ?></div>
    <?php endforeach; ?>
