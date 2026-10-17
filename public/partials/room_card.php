<?php
/** @var array $room  @var string $query  Query string (check_in/check_out) appended to the room link */
$link = '/room.php?id=' . (int) $room['room_id'] . ($query !== '' ? '&' . $query : '');
?>
<div class="card mb-3 room-card">
    <div class="row g-0">
        <div class="col-md-4">
            <a href="<?= e($link) ?>"><img src="<?= e(room_photo($room['photo_url'])) ?>" class="img-fluid rounded-start room-photo" alt="<?= e($room['name']) ?>"></a>
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title"><a href="<?= e($link) ?>" class="text-decoration-none"><?= e($room['name']) ?></a></h5>
                <p class="text-muted mb-1"><?= e($room['city']) ?>, <?= e($room['area']) ?> &middot; <?= e($room['type_title']) ?></p>
                <p class="mb-1">Guests: <strong><?= (int) $room['count_of_guests'] ?></strong> &middot;
                    <strong><?= (int) $room['price'] ?> &euro;</strong> / night</p>
                <p class="card-text"><?= e($room['description_short']) ?></p>
            </div>
        </div>
    </div>
</div>
