<?php
/**
 * @var array|null $currentUser
 * @var array $room
 * @var string $checkIn
 * @var string $checkOut
 * @var bool $available
 * @var int $nights
 */
?>
<?php if ($available): ?>
    <div class="alert alert-success mb-2">Available for <?= (int) $nights ?> night(s): total
        <strong><?= $nights * (int) $room['price'] ?> &euro;</strong></div>
    <?php if ($currentUser): ?>
        <form action="/actions/book.php" method="post">
            <?= App\Support\Csrf::field() ?>
            <input type="hidden" name="room_id" value="<?= (int) $room['room_id'] ?>">
            <input type="hidden" name="check_in" value="<?= e($checkIn) ?>">
            <input type="hidden" name="check_out" value="<?= e($checkOut) ?>">
            <button class="btn btn-success">Book now</button>
        </form>
    <?php else: ?>
        <a href="/login.php" class="btn btn-success">Log in to book</a>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-danger mb-0">Not available for these dates.</div>
<?php endif; ?>
