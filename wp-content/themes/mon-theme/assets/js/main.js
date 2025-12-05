/**
 * MAIN.JS - JavaScript du thème
 * 
 * Emplacement : /themes/mon-theme-ecommerce/assets/js/main.js
 * 
 * @package MonECommerceTheme
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        console.log('Thème Mon E-Commerce chargé !');

        // Défilement fluide pour les liens d'ancrage
        $('a[href^="#"]').on('click', function (e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });

    });

})(jQuery);