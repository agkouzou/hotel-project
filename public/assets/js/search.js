/* v3.1: Ajax search. Results load below the form and the URL is kept in sync for reloads. */
(function () {
    'use strict';

    var form = document.getElementById('search-form');
    var results = document.getElementById('results');
    if (!form || !results) { return; }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!window.hotelValidateForm(form)) { return; }

        var qs = new URLSearchParams(new FormData(form)).toString();
        results.setAttribute('aria-busy', 'true');

        fetch('/actions/search.php?' + qs, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) {
                return response.text().then(function (html) {
                    results.innerHTML = html;
                    if (response.ok) {
                        history.replaceState(null, '', '/index.php?' + qs);
                    }
                });
            })
            .catch(function () {
                results.innerHTML = '<div class="alert alert-danger">Search failed. Please try again.</div>';
            })
            .finally(function () {
                results.removeAttribute('aria-busy');
            });
    });
})();
