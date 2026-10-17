<?php
require __DIR__ . '/../boot/boot.php';

use App\Hotel\RoomService;
use App\Hotel\RoomTypeService;

$cities = RoomService::cities();
$types = RoomTypeService::all();
$today = date('Y-m-d');

// Default stay: today + 2 days. URL parameters (set by every search) take precedence.
$filters = RoomService::filtersFromRequest($_GET);
$searched = isset($_GET['check_in']) || isset($_GET['check_out']);
if (!$searched) {
    $filters['check_in'] = $today;
    $filters['check_out'] = date('Y-m-d', strtotime('+2 days'));
}

// An explicit search needs a city; the default landing view lists every city.
$error = $searched ? RoomService::validateSearch($filters) : null;
$rooms = $error === null ? RoomService::search($filters) : [];
$roomQuery = http_build_query(['check_in' => $filters['check_in'], 'check_out' => $filters['check_out']]);

$pageTitle = 'Search rooms';
$scripts = ['/assets/js/search.js'];
require __DIR__ . '/_header.php';
?>
<h1 class="mb-4">Find your room</h1>
<form action="/index.php" method="get" id="search-form">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label" for="check_in">Check-in</label>
            <input type="date" class="form-control" id="check_in" name="check_in" min="<?= $today ?>" value="<?= e($filters['check_in']) ?>" required>
            <div class="invalid-feedback">Please choose a check-in date.</div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="check_out">Check-out</label>
            <input type="date" class="form-control" id="check_out" name="check_out" min="<?= $today ?>" value="<?= e($filters['check_out']) ?>" required>
            <div class="invalid-feedback">Check-out must be after check-in.</div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="cities">City <small class="text-muted">(Ctrl+click for several)</small></label>
            <select class="form-select" id="cities" name="cities[]" multiple size="<?= min(4, max(2, count($cities))) ?>" required>
                <?php foreach ($cities as $c): ?>
                    <option value="<?= e($c['city']) ?>" <?= in_array($c['city'], $filters['cities'], true) ? 'selected' : '' ?>><?= e($c['city']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please choose a city.</div>
        </div>
        <div class="col-md-3">
            <div class="form-label">Room type</div>
            <?php foreach ($types as $t): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="types[]" id="type-<?= (int) $t['type_id'] ?>"
                           value="<?= (int) $t['type_id'] ?>" <?= in_array((int) $t['type_id'], $filters['types'], true) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="type-<?= (int) $t['type_id'] ?>"><?= e($t['title']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label">Price per night (&euro;)</label>
            <div class="input-group">
                <input type="number" min="0" class="form-control" name="price_min" placeholder="Min" value="<?= e($filters['price_min']) ?>">
                <input type="number" min="0" class="form-control" name="price_max" placeholder="Max" value="<?= e($filters['price_max']) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="guests">Guests (at least)</label>
            <input type="number" min="1" class="form-control" id="guests" name="guests" value="<?= e($filters['guests']) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </div>
</form>

<section id="results" class="mt-4" aria-live="polite">
    <?php if ($error !== null): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php else: ?>
        <?php require __DIR__ . '/partials/results.php'; ?>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
