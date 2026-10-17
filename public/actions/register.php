<?php
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\UserService;
use App\Support\Validator;

if ($currentUser || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$emailRepeat = trim((string) ($_POST['email_repeat'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$error = Validator::required($name, 'Name')
    ?? Validator::email($email)
    ?? ($email !== $emailRepeat ? 'The emails do not match.' : null)
    ?? Validator::required($password, 'Password')
    ?? (mb_strlen($name) > 250 || mb_strlen($email) > 250 ? 'Name and email must be at most 250 characters.' : null)
    ?? (UserService::emailExists($email) ? 'This email is already registered.' : null);

if ($error !== null) {
    flash('error', $error);
    redirect('/register.php');
}

UserService::login(UserService::register($name, $email, $password));
redirect('/index.php');
