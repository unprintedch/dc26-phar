/* global Swiper */
(function () {
    'use strict';

    if (typeof window.Swiper === 'undefined') return;

    document.querySelectorAll('.dc26-partners-slider').forEach(function (slider) {
        var el = slider.querySelector('.dc26-partners-swiper');
        if (!el) return;

        new Swiper(el, {
            slidesPerView:  'auto',
            loop:           true,
            speed:          700,
            autoplay: {
                delay:                  3500,
                disableOnInteraction:   false,
                pauseOnMouseEnter:      true,
            },
            navigation: {
                prevEl: slider.querySelector('.dc26-partners-btn--prev'),
                nextEl: slider.querySelector('.dc26-partners-btn--next'),
            },
            a11y: {
                prevSlideMessage: 'Précédent',
                nextSlideMessage: 'Suivant',
            },
        });
    });
}());
