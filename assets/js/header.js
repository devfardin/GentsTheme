/**
 * Header JS
 * Handles: scroll shadow, off-canvas menu, AJAX search (desktop + off-canvas)
 */
(function () {
    'use strict';

    const header   = document.querySelector('.site-header');
    const toggle   = document.querySelector('.mobile-menu-toggle');
    const panel    = document.getElementById('offcanvas-menu');
    const overlay  = document.getElementById('offcanvas-overlay');
    const closeBtn = document.querySelector('.offcanvas-close');

    /* ── Sticky scroll shadow ───────────────────────────────────── */
    window.addEventListener('scroll', function () {
        header.classList.toggle('is-scrolled', window.scrollY > 40);
    }, { passive: true });

    /* ── Off-canvas open ────────────────────────────────────────── */
    function openPanel() {
        panel.classList.add('is-open');
        overlay.classList.add('is-visible');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden'; /* prevent page scroll */
    }

    /* ── Off-canvas close ───────────────────────────────────────── */
    function closePanel() {
        panel.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (toggle)   toggle.addEventListener('click', openPanel);
    if (closeBtn) closeBtn.addEventListener('click', closePanel);
    if (overlay)  overlay.addEventListener('click', closePanel);

    /* Close on Escape key */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel.classList.contains('is-open')) {
            closePanel();
        }
    });

    /* Close panel when a nav link inside it is tapped */
    if (panel) {
        panel.querySelectorAll('.offcanvas-nav a').forEach(function (link) {
            link.addEventListener('click', closePanel);
        });
    }

    /* ── AJAX search ────────────────────────────────────────────── */
    /* Works for both: desktop input and off-canvas input */
    var searchPairs = [
        {
            input:   document.querySelector('.header-search .search-input'),
            results: document.querySelector('.header-search .search-results')
        },
        {
            input:   document.querySelector('.offcanvas-search-input'),
            results: document.querySelector('.offcanvas-search-results')
        }
    ];

    searchPairs.forEach(function (pair) {
        if (!pair.input || !pair.results) return;

        var timer = null;

        pair.input.addEventListener('input', function () {
            clearTimeout(timer);
            var query = this.value.trim();

            if (query.length < 2) {
                pair.results.classList.remove('active');
                return;
            }

            /* Debounce — 300ms after user stops typing */
            timer = setTimeout(function () {
                runSearch(query, pair.results);
            }, 300);
        });

        /* Close results when clicking outside this search pair */
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.header-search') && !e.target.closest('.offcanvas-search')) {
                pair.results.classList.remove('active');
            }
        });
    });

    /* Send AJAX request and render into the given results container */
    function runSearch(query, resultsEl) {
        resultsEl.innerHTML = '<div class="search-no-results">Searching…</div>';
        resultsEl.classList.add('active');

        fetch(headerAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'gentstime_product_search',
                nonce:  headerAjax.nonce,
                query:  query
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { renderResults(data, resultsEl); })
        .catch(function () {
            resultsEl.innerHTML = '<div class="search-no-results">Something went wrong.</div>';
        });
    }

    /* Build product result HTML */
    function renderResults(data, resultsEl) {
        if (!data.success || !data.data || !data.data.length) {
            resultsEl.innerHTML = '<div class="search-no-results">No products found.</div>';
            return;
        }

        resultsEl.innerHTML = data.data.map(function (p) {
            return (
                '<a href="' + p.url + '" class="search-result-item">' +
                    '<img src="' + p.image + '" alt="' + p.name + '" class="search-result-image">' +
                    '<div class="search-result-info">' +
                        '<div class="search-result-name">' + p.name + '</div>' +
                        '<div class="search-result-price">' + p.price + '</div>' +
                    '</div>' +
                '</a>'
            );
        }).join('');

        resultsEl.classList.add('active');
    }

})();
