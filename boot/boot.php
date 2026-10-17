<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/app/Support/Autoloader.php';
require ROOT_PATH . '/app/Support/helpers.php';

App\Support\Autoloader::register('App', ROOT_PATH . '/app');
App\Support\Config::load(ROOT_PATH . '/config/config.json');

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// CSRF: every POST request must carry the session token (form field or X-CSRF-Token header).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !App\Support\Csrf::isValid()) {
    if (is_ajax()) {
        json_response(['ok' => false, 'error' => 'Invalid or missing security token. Please reload the page.'], 419);
    }
    http_response_code(419);
    exit('Invalid or missing security token. Please go back, reload the page and try again.');
}

$currentUser = App\Hotel\UserService::current();
