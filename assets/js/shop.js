document.addEventListener('DOMContentLoaded', () => {

    /* ── Mobile sidebar (always runs, no dependency on gtShopData) ── */
    const sidebar = document.getElementById('gt-shop-sidebar');
    const toggle  = document.getElementById('gt-filter-toggle');

    if (toggle && sidebar) {
        const closeBtn = document.getElementById('gt-sidebar-close-btn');

        function openSidebar() {
            sidebar.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('is-open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                closeSidebar();
            }
        });
    }

    /* ── Everything below needs gtShopData ─────────────────────── */
    const cfg = window.gtShopData;
    if (!cfg) return;

    const grid     = document.getElementById('gt-shop-grid');
    const loadWrap = document.getElementById('gt-load-more-wrap');
    const loadBtn  = document.getElementById('gt-load-more');
    const countEl  = document.getElementById('gt-result-count');
    const orderSel = document.getElementById('gt-orderby');
    const clearBtn = document.getElementById('gt-clear-filters');

    let state = {
        cat:       cfg.initCat,
        size:      '',
        min_price: cfg.priceMin,
        max_price: cfg.priceMax,
        orderby:   'menu_order',
        page:      1,
        loading:   false,
        append:    false,
    };

    /* ── Clear button visibility ────────────────────────────────── */
    function isFiltered() {
        return state.cat !== cfg.initCat
            || state.size !== ''
            || state.min_price !== cfg.priceMin
            || state.max_price !== cfg.priceMax;
    }

    function syncClearBtn() {
        if (clearBtn) clearBtn.style.display = isFiltered() ? 'inline-flex' : 'none';
    }

    clearBtn?.addEventListener('click', () => {
        state.cat       = cfg.initCat;
        state.size      = '';
        state.min_price = cfg.priceMin;
        state.max_price = cfg.priceMax;

        if (minRange) minRange.value = cfg.priceMin;
        if (maxRange) maxRange.value = cfg.priceMax;
        updateFill();

        document.querySelectorAll('.gt-size-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.gt-cat-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.cat === cfg.initCat);
        });

        syncClearBtn();
        resetAndFetch();
    });

    /* ── Price slider ───────────────────────────────────────────── */
    const minRange = document.getElementById('gt-min-price');
    const maxRange = document.getElementById('gt-max-price');
    const minVal   = document.getElementById('gt-price-min-val');
    const maxVal   = document.getElementById('gt-price-max-val');
    const fill     = document.getElementById('gt-price-fill');

    function updateFill() {
        if (!minRange || !maxRange || !fill || !minVal || !maxVal) return;
        const mn  = parseInt(minRange.value);
        const mx  = parseInt(maxRange.value);
        const rng = cfg.priceMax - cfg.priceMin || 1;
        fill.style.left  = ((mn - cfg.priceMin) / rng * 100) + '%';
        fill.style.right = ((cfg.priceMax - mx) / rng * 100) + '%';
        minVal.textContent = mn;
        maxVal.textContent = mx;
    }

    minRange?.addEventListener('input', () => {
        if (parseInt(minRange.value) > parseInt(maxRange.value)) minRange.value = maxRange.value;
        updateFill();
    });

    maxRange?.addEventListener('input', () => {
        if (parseInt(maxRange.value) < parseInt(minRange.value)) maxRange.value = minRange.value;
        updateFill();
    });

    document.getElementById('gt-price-apply')?.addEventListener('click', () => {
        state.min_price = parseInt(minRange.value);
        state.max_price = parseInt(maxRange.value);
        syncClearBtn();
        resetAndFetch();
    });

    /* ── Size buttons ───────────────────────────────────────────── */
    document.querySelectorAll('.gt-size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const val = btn.dataset.size;
            state.size = (state.size === val) ? '' : val;
            document.querySelectorAll('.gt-size-btn').forEach(b => b.classList.toggle('active', b.dataset.size === state.size));
            syncClearBtn();
            resetAndFetch();
        });
    });

    /* ── Category buttons ───────────────────────────────────────── */
    document.querySelectorAll('.gt-cat-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            state.cat = btn.dataset.cat;
            document.querySelectorAll('.gt-cat-btn').forEach(b => b.classList.toggle('active', b === btn));
            syncClearBtn();
            resetAndFetch();
        });
    });

    /* ── Sort ───────────────────────────────────────────────────── */
    orderSel?.addEventListener('change', () => {
        state.orderby = orderSel.value;
        resetAndFetch();
    });

    /* ── Load more ──────────────────────────────────────────────── */
    loadBtn?.addEventListener('click', () => {
        state.page++;
        state.append = true;
        doFetch();
    });

    /* ── Fetch ──────────────────────────────────────────────────── */
    function resetAndFetch() {
        state.page   = 1;
        state.append = false;
        doFetch();
    }

    function doFetch() {
        if (state.loading) return;
        state.loading = true;

        if (!state.append) {
            grid.classList.add('gt-loading');
        } else {
            loadBtn.disabled    = true;
            loadBtn.textContent = 'Loading…';
        }

        const body = new FormData();
        body.append('action',       'shop_ajax_filter');
        body.append('nonce',        cfg.nonce);
        body.append('page',         state.page);
        body.append('orderby',      state.orderby);
        body.append('cat',          state.cat);
        body.append('size',         state.size);
        body.append('min_price',    state.min_price);
        body.append('max_price',    state.max_price);
        body.append('new_arrivals', cfg.newArrivals || 0);

        fetch(cfg.ajaxurl, { method: 'POST', body })
            .then(r => r.json())
            .then(({ success, data }) => {
                if (!success) return;

                if (state.append) {
                    const tmp = document.createElement('div');
                    tmp.innerHTML = data.html;
                    const existingUl = grid.querySelector('ul.products');
                    const newItems   = tmp.querySelectorAll('li');
                    if (existingUl && newItems.length) {
                        newItems.forEach(li => existingUl.appendChild(li));
                    }
                } else {
                    grid.innerHTML = data.html;
                    grid.classList.remove('gt-loading');
                }

                if (countEl) {
                    const from = (state.page - 1) * 12 + 1;
                    const to   = Math.min(state.page * 12, data.found);
                    countEl.textContent = data.found > 0
                        ? `Showing ${from}–${to} of ${data.found} results`
                        : 'No products found';
                }

                if (data.has_more) {
                    loadWrap.style.display = 'flex';
                    loadBtn.disabled    = false;
                    loadBtn.textContent = 'Load More';
                } else {
                    loadWrap.style.display = 'none';
                }

                state.append  = false;
                state.loading = false;
            })
            .catch(() => { state.loading = false; });
    }

    /* ── Init ───────────────────────────────────────────────────── */
    updateFill();
    syncClearBtn();
    doFetch();
});
