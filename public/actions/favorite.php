<?php
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\FavoriteService;
use App\Hotel\RoomService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}
if (!$currentUser) {
    action_error('Please log in to manage favorites.', '/login.php', 401);
}

$roomId = (int) ($_POST['room_id'] ?? 0);
if (RoomService::find($roomId) === null) {
    action_error('Room not found.', '/index.php', 404);
}

$isFavorite = FavoriteService::toggle((int) $currentUser['user_id'], $roomId);

if (is_ajax()) {
    json_response(['ok' => true, 'favorite' => $isFavorite]);
}
redirect('/room.php?id=' . $roomId);
