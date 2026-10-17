<?php
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\ReviewService;
use App\Hotel\RoomService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}
if (!$currentUser) {
    action_error('Please log in to leave a review.', '/login.php', 401);
}

$roomId = (int) ($_POST['room_id'] ?? 0);
$rate = (int) ($_POST['rate'] ?? 0);
$comment = is_scalar($_POST['comment'] ?? null) ? trim((string) $_POST['comment']) : '';
$back = '/room.php?id=' . $roomId;

if (RoomService::find($roomId) === null) {
    action_error('Room not found.', '/index.php', 404);
}
if ($rate < 1 || $rate > 5) {
    action_error('Please choose a rating between 1 and 5.', $back);
}
if (mb_strlen($comment) > 250) {
    action_error('The comment must be at most 250 characters.', $back);
}

ReviewService::create((int) $currentUser['user_id'], $roomId, $rate, $comment === '' ? null : $comment);

if (is_ajax()) {
    json_response(['ok' => true]);
}
redirect($back);
