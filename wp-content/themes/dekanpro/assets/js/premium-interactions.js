/**
 * DekanPro — Premium Interactions
 * Apple Newsroom style. Theme-aware header.
 */
(function () {
    'use strict';

    function initScrollReveal() {
        var elements = document.querySelectorAll('.dp-reveal');
        if (!elements.length) return;
        if (!('IntersectionObserver' in window)) {
            elements.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        elements.forEach(function (el, i) {
            el.style.transitionDelay = (i % 6) * 80 + 'ms';
            observer.observe(el);
        });
    }

    function initImageFadeIn() {
        var images = document.querySelectorAll('.entry-media img, .post-thumbnail img, .gallery-card-thumb img');
        if (!images.length) return;
        images.forEach(function (img) {
            if (img.complete && img.naturalHeight > 0) return;
            img.style.opacity = '0';
            img.style.transition = 'opacity .5s ease';
            img.addEventListener('load', function () { img.style.opacity = '1'; });
            img.addEventListener('error', function () { img.style.opacity = '1'; });
        });
    }

    function isDark() {
        return document.documentElement.getAttribute('data-darkmode') === 'dark';
    }

    function initHeaderScroll() {
        var header = document.getElementById('masthead');
        if (!header) return;
        var ticking = false;

        function update() {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            var dark = isDark();
            if (y > 10) {
                header.style.background = dark
                    ? 'rgba(29,29,31,.92)'
                    : 'rgba(251,251,253,.92)';
                header.style.borderBottomColor = dark
                    ? 'rgba(255,255,255,.1)'
                    : 'rgba(0,0,0,.1)';
            } else {
                header.style.background = dark
                    ? 'rgba(29,29,31,.8)'
                    : 'rgba(251,251,253,.8)';
                header.style.borderBottomColor = dark
                    ? 'rgba(255,255,255,.1)'
                    : 'rgba(0,0,0,.1)';
            }
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });

        update();

        if (typeof MutationObserver !== 'undefined') {
            new MutationObserver(update).observe(
                document.documentElement,
                { attributes: true, attributeFilter: ['data-darkmode'] }
            );
        }
    }

    function initGalleryReveal() {
        var cards = document.querySelectorAll('.gallery-card');
        if (!cards.length) return;
        if (!('IntersectionObserver' in window)) {
            cards.forEach(function (c) { c.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });
        cards.forEach(function (card) { observer.observe(card); });
    }

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
