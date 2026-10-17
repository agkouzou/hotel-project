<?php
require __DIR__ . '/../boot/boot.php';

if ($currentUser) {
    redirect('/index.php');
}
$pageTitle = 'Register';
require __DIR__ . '/_header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="h3 mb-3">Register</h1>
        <form action="/actions/register.php" method="post" class="js-validate">
            <?= App\Support\Csrf::field() ?>
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" maxlength="250" required>
                <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="250" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email_repeat">Repeat email</label>
                <input type="email" class="form-control" id="email_repeat" name="email_repeat" data-match="email" required>
                <div class="invalid-feedback">The emails do not match.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter a password.</div>
            </div>
            <button class="btn btn-primary" type="submit">Register</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/_footer.php'; ?>
