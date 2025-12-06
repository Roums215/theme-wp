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

        // Mobile Menu Toggle
        const mobileMenuBtn = $('#mobile-menu-toggle');
        const mobileMenu = $('#mobile-menu');

        mobileMenuBtn.on('click', function () {
            mobileMenu.toggleClass('hidden');
            // Optional: Animate fade in/out
            if (!mobileMenu.hasClass('hidden')) {
                mobileMenu.hide().fadeIn(200);
            }
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function (e) {
            if (!mobileMenuBtn.is(e.target) && mobileMenuBtn.has(e.target).length === 0 &&
                !mobileMenu.is(e.target) && mobileMenu.has(e.target).length === 0) {
                mobileMenu.addClass('hidden');
            }
        });

    })(jQuery);