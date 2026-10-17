<?php /** @var array $reviews */ ?>
<?php if (!$reviews): ?>
    <p class="text-muted">No reviews yet.</p>
<?php endif; ?>
<?php foreach ($reviews as $review): ?>
    <div class="border-bottom py-2">
        <div><strong><?= e($review['user_name'] ?? 'Former user') ?></strong>
            <span class="text-warning ms-2"><?= stars((int) $review['rate']) ?></span>
            <small class="text-muted ms-2"><?= e(date('d/m/Y', strtotime($review['created_time']))) ?></small>
        </div>
        <?php if ($review['comment'] !== null && $review['comment'] !== ''): ?>
            <div><?= nl2br(e($review['comment'])) ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
