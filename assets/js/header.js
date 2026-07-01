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

    /* ── Cart Sidebar ───────────────────────────────────────────── */
    var cartToggle  = document.getElementById('cart-sidebar-toggle');
    var cartSidebar = document.getElementById('cart-sidebar');
    var cartOverlay = document.getElementById('cart-sidebar-overlay');
    var cartClose   = document.getElementById('cart-sidebar-close');

    function openCart() {
        cartSidebar.classList.add('is-open');
        cartOverlay.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    }

    function closeCart() {
        cartSidebar.classList.remove('is-open');
        cartOverlay.classList.remove('is-visible');
        document.body.style.overflow = '';
    }

    if (cartToggle) cartToggle.addEventListener('click', openCart);
    if (cartClose)  cartClose.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && cartSidebar && cartSidebar.classList.contains('is-open')) {
            closeCart();
        }
    });

    /* ── Cart fragment helpers ───────────────────────────────────────── */
    var cartBody  = cartSidebar ? cartSidebar.querySelector('.cart-sidebar-body') : null;
    var cartBadge = document.querySelector('.cart-badge');
    var cartFooter = cartSidebar ? cartSidebar.querySelector('.cart-sidebar-footer') : null;

    /* Update badge count in the header icon */
    function updateBadge(count) {
        var btn = document.getElementById('cart-sidebar-toggle');
        if (!btn) return;
        var existing = btn.querySelector('.cart-badge');
        if (count > 0) {
            if (existing) {
                existing.textContent = count;
            } else {
                var badge = document.createElement('span');
                badge.className = 'cart-badge';
                badge.textContent = count;
                btn.appendChild(badge);
            }
        } else {
            if (existing) existing.remove();
        }
    }

    /* Replace mini-cart HTML and re-bind remove listeners */
    function updateSidebarHTML(html) {
        if (!cartBody) return;
        cartBody.innerHTML = html;
        bindRemoveButtons();
    }

    /* Fetch fresh fragments from the server */
    function fetchCartFragments(callback) {
        fetch(headerAjax.ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'gentstime_get_cart_fragments',
                nonce:  headerAjax.cart_nonce
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                updateBadge(data.data.count);
                updateSidebarHTML(data.data.mini_cart);
                if (cartFooter) {
                    cartFooter.style.display = data.data.count > 0 ? '' : 'none';
                }
                markCartButtons(data.data.product_ids || []);
            }
            if (typeof callback === 'function') callback();
        })
        .catch(function () {
            if (typeof callback === 'function') callback();
        });
    }

    /* ── Mark / unmark cards that are already in cart ──────────────── */
    function markCartButtons(cartIds) {
        document.querySelectorAll('.gt-product-card').forEach(function (card) {
            var pid = parseInt(card.dataset.product_id, 10);
            var btn = card.querySelector('.gt-atc-btn');
            if (!btn) return;
            var inCart = cartIds.indexOf(pid) !== -1;
            btn.classList.toggle('gt-in-cart', inCart);
            var label = btn.querySelector('.gt-atc-label');
            if (label) label.textContent = inCart ? 'In Cart' : 'Add to Cart';
        });
    }

    /* Mark on page load using server-side data */
    if (headerAjax.cart_ids && headerAjax.cart_ids.length) {
        markCartButtons(headerAjax.cart_ids.map(Number));
    }

    /* ── AJAX Add-to-Cart (intercept click on .gt-atc-btn) ────────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gt-atc-btn');
        if (!btn) return;
        if (btn.classList.contains('gt-in-cart') || btn.classList.contains('gt-loading')) return;

        e.preventDefault();
        var pid = btn.dataset.product_id;
        if (!pid) return;

        /* Loading state */
        btn.classList.add('gt-loading');
        var label = btn.querySelector('.gt-atc-label');
        if (label) label.textContent = '...';

        fetch('/?wc-ajax=add_to_cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ product_id: pid, quantity: 1 })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            btn.classList.remove('gt-loading');
            if (data.error) {
                if (label) label.textContent = 'Add to Cart';
                window.location = btn.href; /* fallback: redirect */
                return;
            }
            /* Success */
            var card = btn.closest('.gt-product-card');
            var name = card ? (card.querySelector('.gt-card-title') || {}).textContent || '' : '';
            fetchCartFragments();
            showToast(name.trim());
            /* Suppress WC's injected "View cart" link next to button */
            var viewLink = btn.parentNode.querySelector('.added_to_cart');
            if (viewLink) viewLink.remove();
        })
        .catch(function () {
            btn.classList.remove('gt-loading');
            if (label) label.textContent = 'Add to Cart';
        });
    });

    /* ── Buy Now (add to cart then redirect to checkout) ───────── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gt-buy-btn');
        if (!btn) return;
        e.preventDefault();
        var pid          = btn.dataset.product_id;
        var checkoutUrl  = btn.dataset.checkout_url;
        if (!pid) return;

        btn.classList.add('gt-loading');
        var span = btn.querySelector('span');
        if (span) span.textContent = '...';

        fetch('/?wc-ajax=add_to_cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ product_id: pid, quantity: 1 })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.error) {
                btn.classList.remove('gt-loading');
                if (span) span.textContent = 'Buy Now';
                window.location = btn.href;
            } else {
                window.location = checkoutUrl;
            }
        })
        .catch(function () {
            window.location = checkoutUrl;
        });
    });

    /* Suppress WC's auto-inserted "View cart" anchor after any AJAX add */
    jQuery(document.body).on('added_to_cart wc_fragment_refresh', function () {
        document.querySelectorAll('.added_to_cart.wc-forward').forEach(function (el) { el.remove(); });
    });

    /* ── Remove item handler ─────────────────────────────────────────── */
    function bindRemoveButtons() {
        if (!cartBody) return;
        cartBody.querySelectorAll('.gt-cart-remove').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var key = this.dataset.cart_item_key;
                if (!key) return;

                /* Fade out the item row immediately for snappy feel */
                var row = this.closest('.gt-mini-cart-item');
                if (row) {
                    row.style.transition = 'opacity 0.2s ease';
                    row.style.opacity = '0.3';
                    row.style.pointerEvents = 'none';
                }

                fetch(headerAjax.ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action:        'gentstime_remove_cart_item',
                        nonce:         headerAjax.cart_nonce,
                        cart_item_key: key
                    })
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        updateBadge(data.data.count);
                        updateSidebarHTML(data.data.mini_cart);
                        if (cartFooter) {
                            cartFooter.style.display = data.data.count > 0 ? '' : 'none';
                        }
                        markCartButtons(data.data.product_ids || []);
                    }
                });
            });
        });
    }

    /* Initial bind on page load */
    bindRemoveButtons();

    /* ── Toast notification ──────────────────────────────────────────── */
    var toastEl = null;
    var toastTimer = null;

    function showToast(productName) {
        /* Create once, reuse */
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'gt-cart-toast';
            toastEl.innerHTML =
                '<span class="gt-toast-icon">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
                '</span>' +
                '<div class="gt-toast-text">' +
                    '<strong class="gt-toast-title">Added to cart!</strong>' +
                    '<span class="gt-toast-name"></span>' +
                '</div>' +
                '<button class="gt-toast-view" type="button">View Cart</button>';

            toastEl.querySelector('.gt-toast-view').addEventListener('click', function () {
                dismissToast();
                openCart();
            });

            document.body.appendChild(toastEl);
        }

        /* Update product name */
        toastEl.querySelector('.gt-toast-name').textContent = productName || '';

        /* Reset animation by removing then re-adding class */
        toastEl.classList.remove('gt-toast-show');
        void toastEl.offsetWidth; /* reflow */
        toastEl.classList.add('gt-toast-show');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(dismissToast, 4000);
    }

    function dismissToast() {
        if (toastEl) toastEl.classList.remove('gt-toast-show');
        clearTimeout(toastTimer);
    }

    /* ── WooCommerce added_to_cart (fired by WC's own JS or our fetch) ── */
    jQuery(document.body).on('added_to_cart', function (e, fragments, cart_hash, $btn) {
        /* suppress WC's view-cart link injection */
        document.querySelectorAll('.added_to_cart.wc-forward').forEach(function (el) { el.remove(); });
        fetchCartFragments();
    });
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

            if (query.length < 1) {
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

    /* Escape a string for safe use inside HTML text / attribute values */
    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    function escAttr(str) {
        return escHtml(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* Build product result HTML — all values escaped before insertion */
    function renderResults(data, resultsEl) {
        if (!data.success || !data.data || !data.data.length) {
            resultsEl.innerHTML = '<div class="search-no-results">No products found.</div>';
            return;
        }

        var fragment = document.createDocumentFragment();

        data.data.forEach(function (p) {
            var a   = document.createElement('a');
            var img = document.createElement('img');
            var info = document.createElement('div');
            var nameEl  = document.createElement('div');
            var priceEl = document.createElement('div');

            /* Safe URL — only allow http/https */
            var safeUrl = /^https?:\/\//.test(p.url) ? p.url : '#';
            a.href      = safeUrl;
            a.className = 'search-result-item';

            img.src       = escAttr(p.image);
            img.alt       = escAttr(p.name);
            img.className = 'search-result-image';

            info.className  = 'search-result-info';
            nameEl.className  = 'search-result-name';
            nameEl.textContent = p.name;  /* textContent — no XSS possible */

            /* price comes as WC HTML (e.g. <span class="woocommerce-Price-amount">)
               sanitise to only allow the safe subset WC always produces */
            priceEl.className = 'search-result-price';
            priceEl.innerHTML = p.price
                .replace(/<(?!\/?(?:span|bdi|ins|del|abbr)(\s[^>]*)?>)[^>]+>/gi, '');

            info.appendChild(nameEl);
            info.appendChild(priceEl);
            a.appendChild(img);
            a.appendChild(info);
            fragment.appendChild(a);
        });

        resultsEl.innerHTML = '';
        resultsEl.appendChild(fragment);
        resultsEl.classList.add('active');
    }

})();
