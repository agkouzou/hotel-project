<?php
require __DIR__ . '/../boot/boot.php';

if ($currentUser) {
    redirect('/index.php');
}
$pageTitle = 'Login';
require __DIR__ . '/_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h3 mb-3">Login</h1>
        <form action="/actions/login.php" method="post" class="js-validate">
            <?= App\Support\Csrf::field() ?>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
            <a href="/register.php" class="ms-2">Create an account</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
