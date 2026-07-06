document.addEventListener('DOMContentLoaded', () => {

    // ── Hero Slider ───────────────────────────────────────────────
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.dot');
    let current  = 0;
    let timer;

    if (slides.length) {
        const goTo = (index) => {
            slides[current].classList.remove('active');
            dots[current]?.classList.remove('active');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('active');
            dots[current]?.classList.add('active');
        };
        const autoPlay = () => { timer = setInterval(() => goTo(current + 1), 4000); };
        document.querySelector('.hero-btn.next')?.addEventListener('click', () => { clearInterval(timer); goTo(current + 1); autoPlay(); });
        document.querySelector('.hero-btn.prev')?.addEventListener('click', () => { clearInterval(timer); goTo(current - 1); autoPlay(); });
        dots.forEach(dot => dot.addEventListener('click', () => { clearInterval(timer); goTo(+dot.dataset.index); autoPlay(); }));
        autoPlay();
    }

    // ── Shop Sidebar Filter Toggle (mobile) ───────────────────────
    const toggle  = document.getElementById('gt-filter-toggle');
    const sidebar = document.getElementById('gt-shop-sidebar');
    if (toggle && sidebar) {
        const overlay = document.createElement('div');
        overlay.className = 'gt-sidebar-overlay';
        document.body.appendChild(overlay);

        const openSidebar  = () => { sidebar.classList.add('is-open');    overlay.classList.add('is-open');    toggle.setAttribute('aria-expanded', 'true');  document.body.style.overflow = 'hidden'; };
        const closeSidebar = () => { sidebar.classList.remove('is-open'); overlay.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; };

        toggle.addEventListener('click', () => sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar());
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
    }

    // ── Mobile Nav Cart Badge ─────────────────────────────────────
    const mobileBadge = document.querySelector('[data-cart-count-badge]');
    if (mobileBadge) {
        const syncBadge = () => {
            const h = document.querySelector('#cart-sidebar-toggle .cart-badge');
            const n = h ? (parseInt(h.textContent, 10) || 0) : 0;
            mobileBadge.textContent   = n;
            mobileBadge.style.display = n > 0 ? '' : 'none';
        };
        const cartToggle = document.getElementById('cart-sidebar-toggle');
        if (cartToggle) new MutationObserver(syncBadge).observe(cartToggle, { childList: true, subtree: true, characterData: true });
        syncBadge();
    }

    // ── Variable Product Popup ────────────────────────────────────
    const popup = document.createElement('div');
    popup.id = 'gt-var-popup';
    popup.innerHTML = `
        <div class="gt-var-backdrop"></div>
        <div class="gt-var-modal" role="dialog" aria-modal="true">
            <button class="gt-var-close" aria-label="Close">&times;</button>
            <div class="gt-var-body"></div>
        </div>`;
    document.body.appendChild(popup);

    const backdrop  = popup.querySelector('.gt-var-backdrop');
    const modalBody = popup.querySelector('.gt-var-body');

    let intent      = 'cart';
    let checkoutUrl = '';
    let productId   = null;
    let attrs       = [];   // { key, name, terms, required }
    let selected    = {};   // { key: slug }
    let currentVar  = null;

    const openPopup  = () => { popup.classList.add('is-open');    document.body.style.overflow = 'hidden'; };
    const closePopup = () => { popup.classList.remove('is-open'); document.body.style.overflow = ''; };

    backdrop.addEventListener('click', closePopup);
    popup.querySelector('.gt-var-close').addEventListener('click', closePopup);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePopup(); });

    // taxonomy attr → "attribute_pa_color", custom attr → "attribute_size"
    function attrKey(attr) {
        return 'attribute_' + (attr.taxonomy || attr.name.toLowerCase().replace(/\s+/g, '_'));
    }

    // Only attrs with has_variations:true must be selected before resolving
    function requiredSelected() {
        return attrs.filter(a => a.required).every(a => selected[a.key] !== undefined);
    }

    function resolveVariation(cb) {
        if (!requiredSelected()) { cb(null); return; }
        const fd = new FormData();
        fd.append('product_id', productId);
        // Send all attrs: selected value or empty string (means "any") for WC
        attrs.forEach(a => fd.append(a.key, selected[a.key] || ''));
        fetch('/?wc-ajax=get_variation', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => cb(d && d.variation_id && d.is_purchasable !== false ? d : null))
            .catch(() => cb(null));
    }

    function fmtPrice(v) {
        if (v.price_html) return v.price_html;
        const sym = v.currency_symbol || '';
        if (v.display_price != v.display_regular_price) {
            return `<del><span class="woocommerce-Price-amount">${sym}${v.display_regular_price}</span></del> <ins><span class="woocommerce-Price-amount">${sym}${v.display_price}</span></ins>`;
        }
        return `<span class="woocommerce-Price-amount">${sym}${v.display_price}</span>`;
    }

    function syncConfirm() {
        const btn = modalBody.querySelector('.gt-var-confirm');
        if (!btn) return;
        if (!requiredSelected())     { btn.disabled = true;  btn.textContent = 'Select options'; return; }
        if (!currentVar)             { btn.disabled = true;  btn.textContent = 'Unavailable';    return; }
        if (!currentVar.is_in_stock) { btn.disabled = true;  btn.textContent = 'Out of Stock';   return; }
        btn.disabled    = false;
        btn.textContent = intent === 'buy' ? 'Buy Now' : 'Add to Cart';
    }

    function showPopupToast(type, msg) {
        let el = modalBody.querySelector('.gt-popup-notice');
        if (!el) {
            el = document.createElement('div');
            el.className = 'gt-popup-notice';
            const confirm = modalBody.querySelector('.gt-var-confirm');
            if (confirm) confirm.insertAdjacentElement('beforebegin', el);
            else modalBody.appendChild(el);
        }
        el.textContent = msg;
        el.dataset.type = type;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 4000);
    }

    function render(product) {
        const productName = product.name;
        // Show ALL attributes that have terms — mark required only if has_variations:true
        attrs = (product.attributes || [])
            .filter(a => a.terms && a.terms.length > 0)
            .map(a => ({
                key:      attrKey(a),
                name:     a.name,
                taxonomy: a.taxonomy || null,
                terms:    a.terms,
                required: !!a.has_variations,
            }));
        selected   = {};
        currentVar = null;

        const img = product.images?.[0];
        let html = `<div class="gt-var-img">${img ? `<img src="${img.src}" alt="${img.alt || ''}">` : ''}</div>`;
        html += `<div class="gt-var-info">`;
        html += `<h3 class="gt-var-title">${product.name}</h3>`;
        html += `<div class="gt-var-price" id="gt-var-price">${product.price_html || ''}</div>`;

        attrs.forEach(attr => {
            html += `<div class="gt-var-attr">`;
            html += `<span class="gt-var-attr-label">${attr.name}${attr.required ? '' : ' <em>(any)</em>'}:</span>`;
            html += `<div class="gt-var-swatches">`;
            attr.terms.forEach(t => {
                html += `<button class="gt-swatch" data-key="${attr.key}" data-slug="${t.slug}">${t.name}</button>`;
            });
            html += `</div></div>`;
        });

        html += `<button class="gt-var-confirm" disabled>Select options</button></div>`;
        modalBody.innerHTML = html;

        // Pre-check availability for each term in required attrs
        attrs.filter(a => a.required).forEach(attr => {
            attr.terms.forEach(t => {
                const fd = new FormData();
                fd.append('product_id', productId);
                attrs.forEach(a => fd.append(a.key, a.key === attr.key ? t.slug : ''));
                fetch('/?wc-ajax=get_variation', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(d => {
                        if (!d || !d.variation_id || !d.is_in_stock || !d.is_purchasable) {
                            const btn = modalBody.querySelector(`.gt-swatch[data-key="${attr.key}"][data-slug="${t.slug}"]`);
                            if (btn) btn.classList.add('gt-swatch--unavailable');
                        }
                    })
                    .catch(() => {});
            });
        });

        modalBody.addEventListener('click', e => {
            // Swatch click
            const swatch = e.target.closest('.gt-swatch');
            if (swatch) {
                if (swatch.classList.contains('gt-swatch--unavailable')) return;
                const { key, slug } = swatch.dataset;
                if (selected[key] === slug) {
                    delete selected[key];
                    swatch.classList.remove('active');
                    currentVar = null;
                    syncConfirm();
                } else {
                    selected[key] = slug;
                    modalBody.querySelectorAll(`.gt-swatch[data-key="${key}"]`).forEach(b => b.classList.remove('active'));
                    swatch.classList.add('active');
                    resolveVariation(v => {
                        currentVar = v;
                        if (v) {
                            const priceEl = modalBody.querySelector('#gt-var-price');
                            if (priceEl) priceEl.innerHTML = fmtPrice(v);
                        }
                        syncConfirm();
                    });
                }
                return;
            }

            // Confirm button click
            const confirm = e.target.closest('.gt-var-confirm');
            if (!confirm || confirm.disabled || !currentVar?.variation_id) return;

            confirm.disabled    = true;
            confirm.textContent = '…';

            const storeRoot = (typeof wpApiSettings !== 'undefined' && wpApiSettings.root)
                ? wpApiSettings.root : '/wp-json/';

            // Build variation array: taxonomy attrs use taxonomy slug, custom attrs use name
            const variationData = attrs
                .filter(a => selected[a.key])
                .map(a => ({
                    attribute: a.taxonomy || a.name,
                    value:     selected[a.key],
                }));

            fetch(`${storeRoot}wc/store/v1/cart`, { credentials: 'include' })
                .then(r => {
                    const nonce = r.headers.get('Nonce');
                    return fetch(`${storeRoot}wc/store/v1/cart/add-item`, {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json', 'Nonce': nonce || '' },
                        body: JSON.stringify({
                            id:        parseInt(currentVar.variation_id),
                            quantity:  1,
                            variation: variationData,
                        }),
                    });
                })
                .then(r => r.json())
                .then(data => {
                    if (data.code) {
                        syncConfirm();
                        showPopupToast('error', 'Could not add to cart. Please try again.');
                        return;
                    }
                    closePopup();
                    // Refresh cart badge + mini-cart sidebar
                    if (window.gtCart) {
                        window.gtCart.fetchFragments();
                        if (intent !== 'buy') window.gtCart.showToast(productName);
                    }
                    if (intent === 'buy') window.location.href = checkoutUrl;
                })
                .catch(() => {
                    syncConfirm();
                    showPopupToast('error', 'Something went wrong. Please try again.');
                });
        });
    }

    // Delegated trigger — works for AJAX-injected cards too
    document.addEventListener('click', e => {
        const trigger = e.target.closest('.gt-var-trigger');
        if (!trigger) return;
        e.preventDefault();

        intent      = trigger.dataset.intent || 'cart';
        checkoutUrl = trigger.dataset.checkoutUrl || '';
        productId   = trigger.dataset.productId;

        modalBody.innerHTML = '<div class="gt-var-loading"><span></span></div>';
        openPopup();

        const root = (typeof wpApiSettings !== 'undefined' && wpApiSettings.root) ? wpApiSettings.root : '/wp-json/';
        fetch(`${root}wc/store/v1/products/${productId}`)
            .then(r => r.json())
            .then(p => {
                if (!p || p.code) throw new Error();
                render(p);
            })
            .catch(() => {
                modalBody.innerHTML = `<p class="gt-var-err">Could not load options. <a href="${trigger.href}">View product</a></p>`;
            });
    });

});
