<?php
require __DIR__ . '/../boot/boot.php';

use App\Hotel\BookingService;
use App\Hotel\FavoriteService;
use App\Hotel\RoomService;
use App\Support\Validator;

$room = RoomService::find((int) ($_GET['id'] ?? 0));
if ($room === null) {
    http_response_code(404);
    flash('error', 'Room not found.');
    redirect('/index.php');
}

// Dates default to tonight if the URL does not carry valid ones.
$checkIn = is_scalar($_GET['check_in'] ?? null) ? (string) $_GET['check_in'] : '';
$checkOut = is_scalar($_GET['check_out'] ?? null) ? (string) $_GET['check_out'] : '';
if (Validator::stay($checkIn, $checkOut) !== null) {
    $checkIn = date('Y-m-d');
    $checkOut = date('Y-m-d', strtotime('+1 day'));
}

$available = RoomService::isAvailable((int) $room['room_id'], $checkIn, $checkOut);
$nights = BookingService::nights($checkIn, $checkOut);
$isFavorite = $currentUser ? FavoriteService::isFavorite((int) $currentUser['user_id'], (int) $room['room_id']) : false;
$today = date('Y-m-d');
$pageTitle = $room['name'];
$scripts = ['/assets/js/room.js'];

require __DIR__ . '/_header.php';
?>
<div id="room" data-room-id="<?= (int) $room['room_id'] ?>">
<div class="row">
    <div class="col-lg-7">
        <img src="<?= e(room_photo($room['photo_url'])) ?>" class="img-fluid rounded mb-3" alt="<?= e($room['name']) ?>">
    </div>
    <div class="col-lg-5">
        <h1 class="h2"><?= e($room['name']) ?>
            <?php if ($currentUser): ?>
                <form action="/actions/favorite.php" method="post" id="favorite-form" class="d-inline">
                    <?= App\Support\Csrf::field() ?>
                    <input type="hidden" name="room_id" value="<?= (int) $room['room_id'] ?>">
                    <button class="btn btn-link fs-3 p-0 text-danger favorite-toggle" id="favorite-button"
                            title="Toggle favorite" aria-pressed="<?= $isFavorite ? 'true' : 'false' ?>"><?= $isFavorite ? '♥' : '♡' ?></button>
                </form>
            <?php endif; ?>
        </h1>
        <p class="text-muted"><?= e($room['city']) ?>, <?= e($room['area']) ?> &middot; <?= e($room['address']) ?></p>
        <p><span class="badge bg-secondary"><?= e($room['type_title']) ?></span>
            Guests: <strong><?= (int) $room['count_of_guests'] ?></strong> &middot;
            <strong><?= (int) $room['price'] ?> &euro;</strong> / night</p>
        <p id="rating-summary"><?php require __DIR__ . '/partials/rating_summary.php'; ?></p>
        <p>
            <span class="badge <?= $room['wifi'] ? 'bg-success' : 'bg-light text-muted' ?>">WiFi</span>
            <span class="badge <?= $room['parking'] ? 'bg-success' : 'bg-light text-muted' ?>">Parking</span>
            <span class="badge <?= $room['pet_friendly'] ? 'bg-success' : 'bg-light text-muted' ?>">Pet friendly</span>
        </p>
        <p><?= e($room['description_short']) ?></p>
    </div>
</div>

<section class="card card-body my-4">
    <h2 class="h4">Availability &amp; booking</h2>
    <form method="get" action="/room.php" id="availability-form" class="row g-2 align-items-end">
        <input type="hidden" name="id" value="<?= (int) $room['room_id'] ?>">
        <div class="col-md-4">
            <label class="form-label" for="check_in">Check-in</label>
            <input type="date" class="form-control" id="check_in" name="check_in" min="<?= $today ?>" value="<?= e($checkIn) ?>" required>
            <div class="invalid-feedback">Please choose a check-in date.</div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="check_out">Check-out</label>
            <input type="date" class="form-control" id="check_out" name="check_out" min="<?= $today ?>" value="<?= e($checkOut) ?>" required>
            <div class="invalid-feedback">Check-out must be after check-in.</div>
        </div>
        <div class="col-md-4"><button class="btn btn-outline-primary" type="submit">Check availability</button></div>
    </form>
    <div class="mt-3" id="availability" aria-live="polite">
        <?php require __DIR__ . '/partials/availability.php'; ?>
    </div>
</section>

<h2 class="h4">Description</h2>
<p><?= nl2br(e($room['description_long'])) ?></p>

<h2 class="h4 mt-4">Location</h2>
<iframe class="map" loading="lazy" allowfullscreen title="Map"
        src="https://maps.google.com/maps?q=<?= e($room['location_lat']) ?>,<?= e($room['location_long']) ?>&z=15&output=embed"></iframe>

<section id="reviews-section" class="mt-4">
    <h2 class="h4">Reviews</h2>
    <?php if ($currentUser): ?>
        <form action="/actions/review.php" method="post" id="review-form" class="card card-body mb-3 js-validate">
            <?= App\Support\Csrf::field() ?>
            <input type="hidden" name="room_id" value="<?= (int) $room['room_id'] ?>">
            <div id="review-error" class="alert alert-danger d-none"></div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label" for="rate">Rating</label>
                    <select class="form-select" id="rate" name="rate" required>
                        <option value="">Choose...</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"><?= stars($i) ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="invalid-feedback">Please choose a rating.</div>
                </div>
                <div class="col-md-9">
                    <label class="form-label" for="comment">Comment</label>
                    <textarea class="form-control" id="comment" name="comment" rows="2" maxlength="250"></textarea>
                </div>
            </div>
            <div class="mt-2"><button class="btn btn-primary">Add review</button></div>
        </form>
    <?php else: ?>
        <p><a href="/login.php">Log in</a> to leave a review.</p>
    <?php endif; ?>
    <div id="reviews" aria-live="polite"><p class="text-muted">Loading reviews...</p></div>
</section>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
