/* v3.1: Ajax availability check, reviews and favorites on the room page. */
(function () {
    'use strict';

    var root = document.getElementById('room');
    if (!root) { return; }
    var roomId = root.dataset.roomId;
    var ajaxHeaders = { 'X-Requested-With': 'XMLHttpRequest' };

    function showError(box, message) {
        box.textContent = message;
        box.classList.remove('d-none');
    }

    // --- availability for the chosen dates -------------------------------------------------
    var availForm = document.getElementById('availability-form');
    var availBox = document.getElementById('availability');
    if (availForm && availBox) {
        availForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!window.hotelValidateForm(availForm)) { return; }

            var qs = new URLSearchParams(new FormData(availForm)).toString();
            fetch('/actions/availability.php?' + qs, { headers: ajaxHeaders })
                .then(function (response) {
                    return response.text().then(function (html) {
                        availBox.innerHTML = html;
                        if (response.ok) { history.replaceState(null, '', '/room.php?' + qs); }
                    });
                })
                .catch(function () {
                    availBox.innerHTML = '<div class="alert alert-danger">Could not check availability.</div>';
                });
        });
    }

    // --- reviews ---------------------------------------------------------------------------
    var reviewsBox = document.getElementById('reviews');
    var summaryBox = document.getElementById('rating-summary');

    function loadReviews() {
        return fetch('/actions/reviews.php?room_id=' + encodeURIComponent(roomId), { headers: ajaxHeaders })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.ok) { throw new Error(data.error); }
                reviewsBox.innerHTML = data.reviews;
                if (summaryBox) { summaryBox.innerHTML = data.summary; }
            })
            .catch(function () {
                reviewsBox.innerHTML = '<div class="alert alert-danger">Could not load reviews.</div>';
            });
    }
    loadReviews();

    var reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        var reviewError = document.getElementById('review-error');
        reviewForm.addEventListener('submit', function (event) {
            event.preventDefault();
            reviewError.classList.add('d-none');
            if (!window.hotelValidateForm(reviewForm)) { return; }

            fetch(reviewForm.action, {
                method: 'POST',
                headers: ajaxHeaders,
                body: new FormData(reviewForm)
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.ok) { showError(reviewError, data.error || 'Could not save the review.'); return; }
                    reviewForm.reset();
                    reviewForm.classList.remove('was-validated');
                    return loadReviews();
                })
                .catch(function () { showError(reviewError, 'Could not save the review.'); });
        });
    }

    // --- favorite toggle -------------------------------------------------------------------
    var favForm = document.getElementById('favorite-form');
    var favButton = document.getElementById('favorite-button');
    if (favForm && favButton) {
        favForm.addEventListener('submit', function (event) {
            event.preventDefault();
            fetch(favForm.action, {
                method: 'POST',
                headers: ajaxHeaders,
                body: new FormData(favForm)
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.ok) { return; }
                    favButton.textContent = data.favorite ? '♥' : '♡';
                    favButton.setAttribute('aria-pressed', data.favorite ? 'true' : 'false');
                });
        });
    }
})();
