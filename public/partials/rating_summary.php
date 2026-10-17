<?php /** @var array $room */ ?>
Average rating:
<?php if ((int) $room['count_reviews'] > 0): ?>
    <span class="text-warning"><?= stars((int) round((float) $room['avg_reviews'])) ?></span>
    <?= number_format((float) $room['avg_reviews'], 1) ?> (<?= (int) $room['count_reviews'] ?>)
<?php else: ?>
    <span class="text-muted">no reviews yet</span>
<?php endif; ?>
