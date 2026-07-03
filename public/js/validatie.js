/**
 * Globale client-side validatie via Bootstrap 5 validation API.
 *
 * Voorkomt indienen van formulieren met ongeldige HTML5-velden.
 */
(function () {
    'use strict';

    // Activeer Bootstrap validatiestijlen bij indienen
    document.querySelectorAll('form[novalidate]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
})();
