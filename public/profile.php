<?php
require __DIR__ . '/../boot/boot.php';

use App\Hotel\BookingService;
use App\Hotel\FavoriteService;
use App\Hotel\ReviewService;

if (!$currentUser) {
    redirect('/index.php');
}

$userId = (int) $currentUser['user_id'];
$bookings = BookingService::forUser($userId);
$favorites = FavoriteService::forUser($userId);
$reviews = ReviewService::forUser($userId);
$pageTitle = 'My profile';

require __DIR__ . '/_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0"><?= e($currentUser['name']) ?></h1>
        <span class="text-muted"><?= e($currentUser['email']) ?></span>
    </div>
    <form action="/actions/logout.php" method="post">
        <?= App\Support\Csrf::field() ?>
        <button class="btn btn-outline-secondary">Logout</button>
    </form>
</div>

<h2 class="h4 mt-4">Bookings</h2>
<?php if (!$bookings): ?><p class="text-muted">No bookings yet.</p><?php endif; ?>
<?php foreach ($bookings as $b): ?>
    <div class="card mb-3">
        <div class="row g-0">
            <div class="col-md-3">
                <a href="/room.php?id=<?= (int) $b['room_id'] ?>"><img src="<?= e(room_photo($b['photo_url'])) ?>" class="img-fluid rounded-start room-photo" alt="<?= e($b['name']) ?>"></a>
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <h5 class="card-title"><a href="/room.php?id=<?= (int) $b['room_id'] ?>" class="text-decoration-none"><?= e($b['name']) ?></a></h5>
                    <p class="mb-1 text-muted"><?= e($b['type_title']) ?></p>
                    <p class="mb-1"><?= e($b['description_short']) ?></p>
                    <p class="mb-0">Check-in: <strong><?= e($b['check_in_date']) ?></strong> &middot;
                        Check-out: <strong><?= e($b['check_out_date']) ?></strong> &middot;
                        Total: <strong><?= (int) $b['total_price'] ?> &euro;</strong></p>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<h2 class="h4 mt-4">Favorites</h2>
<?php if (!$favorites): ?><p class="text-muted">No favorite rooms yet.</p><?php endif; ?>
<?php foreach ($favorites as $room) {
    $query = '';
    require __DIR__ . '/partials/room_card.php';
} ?>

<h2 class="h4 mt-4">My reviews</h2>
<?php if (!$reviews): ?><p class="text-muted">You have not left any reviews.</p><?php endif; ?>
<ul class="list-group">
    <?php foreach ($reviews as $rv): ?>
        <li class="list-group-item">
            <a href="/room.php?id=<?= (int) $rv['room_id'] ?>"><?= e($rv['room_name']) ?></a>
            <span class="text-warning ms-2"><?= stars((int) $rv['rate']) ?></span>
        </li>
    <?php endforeach; ?>
</ul>
<?php require __DIR__ . '/_footer.php'; ?>
