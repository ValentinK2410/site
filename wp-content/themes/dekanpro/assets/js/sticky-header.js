/**
 * Smart Sticky Header for DekanPro Theme
 * Хедер становится фиксированным только после прокрутки на его высоту
 */

(function() {
    'use strict';

    function initStickyHeader() {
        const header = document.getElementById('masthead') || document.querySelector('.site-header');
        
        if (!header) return;

        // Создаём плейсхолдер для компенсации высоты
        const placeholder = document.createElement('div');
        placeholder.className = 'header-placeholder';
        header.parentNode.insertBefore(placeholder, header.nextSibling);

        let headerHeight = header.offsetHeight;
        let isSticky = false;

        // Обновляем высоту при ресайзе
        window.addEventListener('resize', function() {
            if (!isSticky) {
                headerHeight = header.offsetHeight;
            }
        });

        // Обработчик скролла
        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > headerHeight && !isSticky) {
                // Прокрутили больше высоты хедера — фиксируем
                isSticky = true;
                header.classList.add('is-sticky');
                placeholder.classList.add('is-active');
                placeholder.style.height = headerHeight + 'px';
            } else if (scrollTop <= headerHeight && isSticky) {
                // Вернулись наверх — открепляем
                isSticky = false;
                header.classList.remove('is-sticky');
                placeholder.classList.remove('is-active');
                placeholder.style.height = '0';
            }
        }

        // Слушаем скролл с оптимизацией
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Проверяем начальное состояние
        handleScroll();
    }

    // Запускаем после загрузки DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStickyHeader);
    } else {
        initStickyHeader();
    }
})();
