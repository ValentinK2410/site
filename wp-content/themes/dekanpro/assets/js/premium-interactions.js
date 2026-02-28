/**
 * DekanPro — Premium Interactions
 * Scroll-reveal animations, smooth transitions, lazy-load fade-in.
 */
(function () {
    'use strict';

    /* -----------------------------------------------
       Scroll-Reveal: fade-in cards as they enter viewport
       ----------------------------------------------- */
    function initScrollReveal() {
        var elements = document.querySelectorAll('.dp-reveal');
        if (!elements.length) return;

        if (!('IntersectionObserver' in window)) {
            elements.forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
        );

        elements.forEach(function (el, i) {
            el.style.transitionDelay = (i % 6) * 80 + 'ms';
            observer.observe(el);
        });
    }

    /* -----------------------------------------------
       Image lazy-load fade-in
       ----------------------------------------------- */
    function initImageFadeIn() {
        var images = document.querySelectorAll(
            '.entry-media img, .post-thumbnail img, .gallery-card-thumb img'
        );
        if (!images.length) return;

        images.forEach(function (img) {
            if (img.complete && img.naturalHeight > 0) return;

            img.style.opacity = '0';
            img.style.transition = 'opacity 0.5s ease';

            img.addEventListener('load', function () {
                img.style.opacity = '1';
            });

            img.addEventListener('error', function () {
                img.style.opacity = '1';
            });
        });
    }

    /* -----------------------------------------------
       Smooth header background on scroll
       ----------------------------------------------- */
    function initHeaderScroll() {
        var header = document.getElementById('masthead');
        if (!header) return;

        var ticking = false;

        function updateHeader() {
            var scrollY = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollY > 50) {
                header.style.background = 'rgba(10, 10, 11, 0.95)';
                header.style.boxShadow = '0 1px 0 rgba(255,255,255,0.06)';
            } else {
                header.style.background = 'rgba(10, 10, 11, 0.85)';
                header.style.boxShadow = 'none';
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
        }, { passive: true });

        updateHeader();
    }

    /* -----------------------------------------------
       Gallery cards reveal
       ----------------------------------------------- */
    function initGalleryReveal() {
        var cards = document.querySelectorAll('.gallery-card');
        if (!cards.length) return;

        if (!('IntersectionObserver' in window)) {
            cards.forEach(function (c) { c.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.05 }
        );

        cards.forEach(function (card) {
            observer.observe(card);
        });
    }

    /* -----------------------------------------------
       Init all on DOM ready
       ----------------------------------------------- */
    function init() {
        initScrollReveal();
        initImageFadeIn();
        initHeaderScroll();
        initGalleryReveal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
