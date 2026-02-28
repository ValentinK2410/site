(function () {
  'use strict';

  var cfg = window.dekanproGallery || {};
  var filtersEl = document.getElementById('dekanpro-gallery-filters');
  if (!filtersEl) return;

  var searchInput = document.getElementById('gallery-search');
  var categorySelect = document.getElementById('gallery-category');
  var sortSelect = document.getElementById('gallery-sort');
  var tagsWrap = document.getElementById('gallery-tags');
  var viewBtns = filtersEl.querySelectorAll('.gallery-view-btn');

  function getCategory() {
    if (categorySelect && categorySelect.value) {
      return categorySelect.value;
    }
    return cfg.category || 0;
  }

  var container = document.querySelector('.dekanpro-gallery-grid');
  if (!container) {
    container = document.createElement('div');
    container.className = 'dekanpro-gallery-grid gallery-view-grid';
    var mainContent = document.getElementById('content') || document.querySelector('.site-content');
    if (mainContent) {
      var flexRow = mainContent.querySelector('.dekanpro-flex-row');
      if (flexRow) {
        flexRow.style.display = 'none';
        flexRow.parentNode.insertBefore(container, flexRow.nextSibling);
      } else {
        var noneSection = mainContent.querySelector('.no-results');
        if (noneSection) {
          noneSection.style.display = 'none';
          noneSection.parentNode.insertBefore(container, noneSection.nextSibling);
        } else {
          mainContent.appendChild(container);
        }
      }
    }
  }

  var loadMoreWrap = document.createElement('div');
  loadMoreWrap.className = 'gallery-load-more-wrap';
  loadMoreWrap.innerHTML = '<button type="button" class="dekanpro-btn gallery-load-more">' + (cfg.load_more || 'Показать ещё') + '</button>';
  container.parentNode.insertBefore(loadMoreWrap, container.nextSibling);
  var loadMoreBtn = loadMoreWrap.querySelector('.gallery-load-more');

  var state = {
    search: '',
    tag: '',
    sort: 'date-desc',
    page: 1,
    maxPages: 1,
    loading: false,
    view: 'grid'
  };

  var debounceTimer = null;

  function fetchPosts(append) {
    if (state.loading) return;
    state.loading = true;
    container.classList.add('is-loading');
    if (!append) {
      container.innerHTML = '<div class="gallery-loading">' + (cfg.loading || 'Загрузка...') + '</div>';
    } else {
      loadMoreBtn.textContent = cfg.loading || 'Загрузка...';
      loadMoreBtn.disabled = true;
    }

    var data = new FormData();
    data.append('action', 'dekanpro_gallery_filter');
    data.append('nonce', cfg.nonce);
    data.append('category', getCategory());
    data.append('search', state.search);
    data.append('tag', state.tag);
    data.append('sort', state.sort);
    data.append('page', state.page);

    fetch(cfg.ajaxurl, { method: 'POST', body: data })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        state.loading = false;
        container.classList.remove('is-loading');

        if (!res.success) {
          if (!append) container.innerHTML = '<p class="gallery-empty">' + (cfg.no_results || 'Ничего не найдено.') + '</p>';
          return;
        }

        var d = res.data;
        state.maxPages = d.max_pages;

        if (!append) {
          container.innerHTML = d.html || '<p class="gallery-empty">' + (cfg.no_results || 'Ничего не найдено.') + '</p>';
        } else {
          container.insertAdjacentHTML('beforeend', d.html);
        }

        if (state.page >= state.maxPages) {
          loadMoreWrap.style.display = 'none';
        } else {
          loadMoreWrap.style.display = '';
          loadMoreBtn.textContent = cfg.load_more || 'Показать ещё';
          loadMoreBtn.disabled = false;
        }

        animateCards();
      })
      .catch(function () {
        state.loading = false;
        container.classList.remove('is-loading');
      });
  }

  function animateCards() {
    var cards = container.querySelectorAll('.gallery-card');
    cards.forEach(function (card, i) {
      if (!card.classList.contains('is-visible')) {
        card.style.animationDelay = (i * 0.05) + 's';
        card.classList.add('is-visible');
      }
    });
  }

  function resetAndFetch() {
    state.page = 1;
    fetchPosts(false);
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        state.search = searchInput.value.trim();
        resetAndFetch();
      }, 350);
    });
  }

  if (categorySelect) {
    categorySelect.addEventListener('change', resetAndFetch);
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      state.sort = sortSelect.value;
      resetAndFetch();
    });
  }

  if (tagsWrap) {
    tagsWrap.addEventListener('click', function (e) {
      var btn = e.target.closest('.gallery-tag-btn');
      if (!btn) return;
      tagsWrap.querySelectorAll('.gallery-tag-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      state.tag = btn.dataset.tag || '';
      resetAndFetch();
    });
  }

  viewBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      viewBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      state.view = btn.dataset.view;
      container.className = 'dekanpro-gallery-grid gallery-view-' + state.view;
    });
  });

  loadMoreBtn.addEventListener('click', function () {
    state.page++;
    fetchPosts(true);
  });

  resetAndFetch();
})();
