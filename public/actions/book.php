<?php
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\BookingService;
use App\Hotel\RoomService;
use App\Support\Validator;

if (!$currentUser) {
    redirect('/login.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$roomId = (int) ($_POST['room_id'] ?? 0);
$in = (string) ($_POST['check_in'] ?? '');
$out = (string) ($_POST['check_out'] ?? '');
$back = '/room.php?' . http_build_query(['id' => $roomId, 'check_in' => $in, 'check_out' => $out]);

if (RoomService::find($roomId) === null) {
    flash('error', 'Room not found.');
    redirect('/index.php');
}
if (($error = Validator::stay($in, $out)) !== null) {
    flash('error', $error);
    redirect($back);
}

// Availability is re-checked server-side inside a transaction.
if (BookingService::create((int) $currentUser['user_id'], $roomId, $in, $out) === null) {
    flash('error', 'Sorry, the room is no longer available for these dates.');
    redirect($back);
}

flash('success', 'Your booking was confirmed.');
redirect('/profile.php');
