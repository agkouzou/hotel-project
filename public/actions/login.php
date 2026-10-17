<?php
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\UserService;

if ($currentUser || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$userId = ($email !== '' && $password !== '') ? UserService::authenticate($email, $password) : null;
if ($userId === null) {
    flash('error', 'Invalid email or password.');
    redirect('/login.php');
}

UserService::login($userId);
redirect('/index.php');
