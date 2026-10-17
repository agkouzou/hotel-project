<?php
/** @var array $rooms  @var string $roomQuery  check_in/check_out query string for room links */
?>
<h2 class="h4 mb-3"><?= count($rooms) ?> room(s) found</h2>
<?php if (!$rooms): ?>
    <div class="alert alert-info">No rooms match your search.</div>
<?php endif; ?>
<?php foreach ($rooms as $room) {
    $query = $roomQuery;
    require __DIR__ . '/room_card.php';
} ?>
