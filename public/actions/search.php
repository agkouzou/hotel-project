<?php
/** Ajax: returns the search results fragment (HTML). */
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\RoomService;

$filters = RoomService::filtersFromRequest($_GET);

if (($error = RoomService::validateSearch($filters)) !== null) {
    http_response_code(422);
    echo '<div class="alert alert-danger">' . e($error) . '</div>';
    exit;
}

$rooms = RoomService::search($filters);
$roomQuery = http_build_query(['check_in' => $filters['check_in'], 'check_out' => $filters['check_out']]);
require __DIR__ . '/../partials/results.php';
