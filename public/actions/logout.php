<?php
require __DIR__ . '/../../boot/boot.php';

// POST only (CSRF-protected by the bootstrap): a plain link could be triggered from another site.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    App\Hotel\UserService::logout();
}
redirect('/index.php');
