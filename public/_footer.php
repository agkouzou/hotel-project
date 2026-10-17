</main>
<footer class="border-top mt-5 py-3 text-center text-muted">
    <div class="container">&copy; <?= date('Y') ?> Hotel. All rights reserved.</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
<?php foreach ($scripts ?? [] as $script): ?>
    <script src="<?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
