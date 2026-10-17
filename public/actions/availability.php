<?php
/** Ajax: returns the availability/booking fragment (HTML) for a room and period. */
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\BookingService;
use App\Hotel\RoomService;
use App\Support\Validator;

$room = RoomService::find((int) ($_GET['id'] ?? 0));
if ($room === null) {
    http_response_code(404);
    echo '<div class="alert alert-danger">Room not found.</div>';
    exit;
}

$checkIn = is_scalar($_GET['check_in'] ?? null) ? (string) $_GET['check_in'] : '';
$checkOut = is_scalar($_GET['check_out'] ?? null) ? (string) $_GET['check_out'] : '';
if (($error = Validator::stay($checkIn, $checkOut)) !== null) {
    http_response_code(422);
    echo '<div class="alert alert-danger">' . e($error) . '</div>';
    exit;
}

$available = RoomService::isAvailable((int) $room['room_id'], $checkIn, $checkOut);
$nights = BookingService::nights($checkIn, $checkOut);
require __DIR__ . '/../partials/availability.php';
