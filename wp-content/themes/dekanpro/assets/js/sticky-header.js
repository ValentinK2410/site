/**
 * Smart Sticky Header & Sidebar for DekanPro Theme
 * Хедер и сайдбар становятся фиксированными при прокрутке
 */

(function() {
    'use strict';

    function initStickyElements() {
        const header = document.getElementById('masthead') || document.querySelector('.site-header');
        const dekanproHeader = document.getElementById('dekanpro-header');
        const sidebar = document.getElementById('secondary') || document.querySelector('.dekanpro-sidebar-container');
        
        if (!header) return;

        // Создаём плейсхолдер для компенсации высоты хедера
        const placeholder = document.createElement('div');
        placeholder.className = 'header-placeholder';
        header.parentNode.insertBefore(placeholder, header.nextSibling);

        let headerHeight = header.offsetHeight;
        let dekanproHeaderHeight = dekanproHeader ? dekanproHeader.offsetHeight : headerHeight;
        let isHeaderSticky = false;
        
        // Сохраняем оригинальные размеры и позицию сайдбара
        let sidebarWidth = 0;
        let sidebarLeft = 0;
        let isSidebarSticky = false;

        function updateSidebarDimensions() {
            if (sidebar && !isSidebarSticky) {
                const rect = sidebar.getBoundingClientRect();
                sidebarWidth = rect.width;
                sidebarLeft = rect.left + window.pageXOffset;
            }
        }

        // Обновляем размеры при загрузке
        updateSidebarDimensions();

        // Обновляем высоту при ресайзе
        window.addEventListener('resize', function() {
            if (!isHeaderSticky) {
                headerHeight = header.offsetHeight;
                dekanproHeaderHeight = dekanproHeader ? dekanproHeader.offsetHeight : headerHeight;
            }
            // Обновляем размеры сайдбара если он не sticky
            if (!isSidebarSticky) {
                updateSidebarDimensions();
            }
            // Обновляем позицию для sticky сайдбара
            if (sidebar && isSidebarSticky) {
                sidebar.style.top = dekanproHeaderHeight + 20 + 'px';
            }
        });

        // Обработчик скролла
        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Sticky Header
            if (scrollTop > headerHeight && !isHeaderSticky) {
                isHeaderSticky = true;
                header.classList.add('is-sticky');
                placeholder.classList.add('is-active');
                placeholder.style.height = headerHeight + 'px';
            } else if (scrollTop <= headerHeight && isHeaderSticky) {
                isHeaderSticky = false;
                header.classList.remove('is-sticky');
                placeholder.classList.remove('is-active');
                placeholder.style.height = '0';
            }

            // Sticky Sidebar
            if (sidebar) {
                if (scrollTop > headerHeight && !isSidebarSticky) {
                    // Сохраняем размеры перед фиксацией
                    updateSidebarDimensions();
                    isSidebarSticky = true;
                    sidebar.classList.add('is-sticky');
                    sidebar.style.top = dekanproHeaderHeight + 20 + 'px';
                    sidebar.style.width = sidebarWidth + 'px';
                    sidebar.style.left = sidebarLeft + 'px';
                } else if (scrollTop <= headerHeight && isSidebarSticky) {
                    isSidebarSticky = false;
                    sidebar.classList.remove('is-sticky');
                    sidebar.style.top = '';
                    sidebar.style.width = '';
                    sidebar.style.left = '';
                }
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
        document.addEventListener('DOMContentLoaded', initStickyElements);
    } else {
        initStickyElements();
    }
})();
