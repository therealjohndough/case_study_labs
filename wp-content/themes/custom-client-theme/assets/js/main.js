/**
 * Custom Client Theme JavaScript
 * 
 * @package CustomClientTheme
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Mobile Navigation Toggle
        $('.menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.main-navigation').toggleClass('toggled');
            
            // Update ARIA attributes
            var expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });

        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });

        // Add animation on scroll
        $(window).on('scroll', function() {
            var scrolled = $(this).scrollTop();
            
            // Parallax effect for hero section
            $('.hero-section').css('transform', 'translateY(' + (scrolled * 0.1) + 'px)');
        });

        // Enhanced glitch effect for interactive elements
        $('.card, .btn, .post-card').on('mouseenter', function() {
            $(this).addClass('glitch-active');
            setTimeout(() => {
                $(this).removeClass('glitch-active');
            }, 300);
        });

        // Frontend editing form toggle
        $('#toggle-editing').on('click', function() {
            var form = $('#frontend-editing-form');
            var isVisible = form.is(':visible');
            
            form.slideToggle(300);
            $(this).text(isVisible ? 'Edit Content' : 'Hide Editor');
        });
    });

})(jQuery);