<?php
/** Ajax: returns the reviews list and the rating summary of a room as JSON (HTML fragments). */
require __DIR__ . '/../../boot/boot.php';

use App\Hotel\ReviewService;
use App\Hotel\RoomService;

$room = RoomService::find((int) ($_GET['room_id'] ?? 0));
if ($room === null) {
    json_response(['ok' => false, 'error' => 'Room not found.'], 404);
}

$reviews = ReviewService::forRoom((int) $room['room_id']);

ob_start();
require __DIR__ . '/../partials/reviews.php';
$reviewsHtml = ob_get_clean();

ob_start();
require __DIR__ . '/../partials/rating_summary.php';
$summaryHtml = ob_get_clean();

json_response(['ok' => true, 'reviews' => $reviewsHtml, 'summary' => $summaryHtml]);
