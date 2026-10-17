/* Front-end form validation (back-end validation is always repeated server-side). */
(function () {
    'use strict';

    function validateForm(form) {
        var ok = form.checkValidity();

        // check-out must be after check-in
        var inEl = form.querySelector('[name="check_in"]');
        var outEl = form.querySelector('[name="check_out"]');
        if (inEl && outEl) {
            var bad = inEl.value && outEl.value && outEl.value <= inEl.value;
            outEl.setCustomValidity(bad ? 'Check-out must be after check-in.' : '');
            if (bad) { ok = false; }
        }

        // fields that must equal another field (e.g. repeat email)
        form.querySelectorAll('[data-match]').forEach(function (el) {
            var other = form.querySelector('[name="' + el.dataset.match + '"]');
            var bad = other && el.value !== other.value;
            el.setCustomValidity(bad ? 'Values do not match.' : '');
            if (bad) { ok = false; }
        });

        form.classList.add('was-validated');
        return ok;
    }

    window.hotelValidateForm = validateForm;

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (form.classList && form.classList.contains('js-validate') && !validateForm(form)) {
            event.preventDefault();
            event.stopPropagation();
        }
    }, true);
})();
