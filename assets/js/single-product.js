/* GentsTime — Single Product Page */
document.addEventListener('DOMContentLoaded', function () {

    /* ================================================================
       TOAST  (top-right)
    ================================================================ */
    var toastEl    = null;
    var toastTimer = null;

    function showToast(msg, type) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'gt-sp-toast';
            toastEl.innerHTML =
                '<div class="gt-sp-toast-icon"></div>' +
                '<div class="gt-sp-toast-body">' +
                    '<strong class="gt-sp-toast-title"></strong>' +
                    '<span class="gt-sp-toast-msg"></span>' +
                '</div>' +
                '<button class="gt-sp-toast-close" aria-label="Dismiss">×</button>' +
                '<div class="gt-sp-toast-progress"></div>';
            toastEl.querySelector('.gt-sp-toast-close').addEventListener('click', function () {
                clearTimeout(toastTimer);
                toastEl.classList.remove('gt-sp-toast--show');
            });
            document.body.appendChild(toastEl);
        }

        var isErr = !type || type === 'error';
        toastEl.dataset.type = isErr ? 'error' : 'success';
        toastEl.querySelector('.gt-sp-toast-title').textContent = isErr ? 'Something went wrong' : 'Success';
        toastEl.querySelector('.gt-sp-toast-msg').textContent   = msg;
        toastEl.querySelector('.gt-sp-toast-icon').innerHTML    = isErr
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="#07bc0c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12.5l3 3 5-6"/></svg>';

        /* restart progress bar */
        var bar = toastEl.querySelector('.gt-sp-toast-progress');
        bar.style.animation = 'none';
        void bar.offsetWidth;
        bar.style.animation = '';

        toastEl.classList.remove('gt-sp-toast--show');
        void toastEl.offsetWidth;
        toastEl.classList.add('gt-sp-toast--show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toastEl.classList.remove('gt-sp-toast--show');
        }, 4000);
    }

    /* ================================================================
       GALLERY THUMBNAILS
    ================================================================ */
    var thumbs      = document.querySelectorAll('.gt-sp-thumb');
    var featuredImg = document.getElementById('gtSpFeatured');
    var zoomWrapEl  = document.getElementById('gtSpZoomWrap');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');
            if (featuredImg) featuredImg.src = thumb.dataset.full;
            if (zoomWrapEl)  zoomWrapEl.dataset.zoom = thumb.dataset.full;
        });
    });

    /* ================================================================
       IMAGE ZOOM
       - Lens appended to body (position:fixed) — never clipped
       - Result box position:fixed — JS positions it right of image
       - Scroll disabled while hovering the image
       - Lens center tracks cursor exactly
    ================================================================ */
    var zoomLens   = document.getElementById('gtSpZoomLens');
    var zoomResult = document.getElementById('gtSpZoomResult');
    var zoomImg    = document.getElementById('gtSpFeatured');

    if (zoomWrapEl && zoomLens && zoomResult && zoomImg) {

        /* Move lens to <body> so no parent clips it */
        document.body.appendChild(zoomLens);

        var zoomActive = false;

        /* Disable page scroll while hovering the image */
        function preventScroll(e) { e.preventDefault(); }

        zoomWrapEl.addEventListener('mouseenter', function () {
            if (window.innerWidth < 1200) return;
            zoomActive = true;

            var r = zoomWrapEl.getBoundingClientRect();

            /* Recalculate lens size based on current image dimensions */
            lw = LENS_W;
            lh = LENS_H;
            zoomLens.style.width  = lw + 'px';
            zoomLens.style.height = lh + 'px';

            /* Place result box flush with image top, immediately to its right */
            zoomResult.style.top    = r.top + 'px';
            zoomResult.style.left   = (r.right + 16) + 'px';
            zoomResult.style.height = r.height + 'px';
            zoomResult.style.width  = '600px';

            zoomLens.style.display   = 'block';
            zoomResult.style.display = 'block';

            /* Disable scroll */
            window.addEventListener('wheel', preventScroll, { passive: false });
        });

        zoomWrapEl.addEventListener('mouseleave', function () {
            zoomActive = false;
            zoomLens.style.display   = 'none';
            zoomResult.style.display = 'none';
            window.removeEventListener('wheel', preventScroll);
        });

        /* ── Zoom level (higher = more zoom) ── */
        var ZOOM_SCALE = 3;
        /* lens size — small lens = precise selection */
        var LENS_W = 180;
        var LENS_H = 220;
        var lw = LENS_W;
        var lh = LENS_H;
        
        zoomLens.style.width  = lw + 'px';
        zoomLens.style.height = lh + 'px';
        

        zoomWrapEl.addEventListener('mousemove', function (e) {
            if (!zoomActive || window.innerWidth < 1200) return;

            var r  = zoomWrapEl.getBoundingClientRect();

            /* Raw cursor offset inside the image */
            var rawX = e.clientX - r.left;
            var rawY = e.clientY - r.top;

            /* Lens top-left so the lens CENTER sits under the cursor,
               clamped so the lens never leaves the image boundary */
            var lx = Math.max(0, Math.min(rawX - lw / 2, r.width  - lw));
            var ly = Math.max(0, Math.min(rawY - lh / 2, r.height - lh));

            /* Lens is position:fixed — viewport coordinates */
            zoomLens.style.left = (r.left + lx) + 'px';
            zoomLens.style.top  = (r.top  + ly) + 'px';

            /* Zoom ratio */
            var rw = zoomResult.offsetWidth  || 460;
            var rh = zoomResult.offsetHeight || r.height;
            var rx = rw / lw;
            var ry = rh / lh;

            /* Background position: use lx/ly (top-left of lens relative to image)
               so the zoomed region exactly matches what the lens covers */
            var src = zoomWrapEl.dataset.zoom || zoomImg.src;
            zoomResult.style.backgroundImage    = 'url("' + src + '")';
            zoomResult.style.backgroundSize     = (r.width * rx) + 'px ' + (r.height * ry) + 'px';
            zoomResult.style.backgroundPosition = '-' + (lx * rx) + 'px -' + (ly * ry) + 'px';
        });
    }

    /* ================================================================
       QUANTITY CONTROLS
    ================================================================ */
    var qtyInput = document.getElementById('gtQtyInput');
    var qtyMinus = document.getElementById('gtQtyMinus');
    var qtyPlus  = document.getElementById('gtQtyPlus');

    if (qtyMinus && qtyInput) {
        qtyMinus.addEventListener('click', function () {
            var v = parseInt(qtyInput.value, 10) || 1;
            if (v > 1) qtyInput.value = v - 1;
        });
    }
    if (qtyPlus && qtyInput) {
        qtyPlus.addEventListener('click', function () {
            var v   = parseInt(qtyInput.value, 10) || 1;
            var max = parseInt(qtyInput.getAttribute('max'), 10) || 99;
            if (v < max) qtyInput.value = v + 1;
        });
    }

    /* ================================================================
       COLOR SWATCH SELECTION
    ================================================================ */
    var colorWrap    = document.getElementById('gtColorOptions');
    var colorButtons = colorWrap ? Array.prototype.slice.call(colorWrap.querySelectorAll('.gt-color-swatch')) : [];
    var colorLabelEl = document.getElementById('gtSelectedColorLabel');
    var selectedColor = null;
    var selectedColorAttr = null;

    colorButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.dataset.inStock === '0') {
                showToast('This color is out of stock. Please choose another.', 'error');
                return;
            }
            if (btn.classList.contains('selected')) {
                btn.classList.remove('selected');
                selectedColor = null;
                selectedColorAttr = null;
                if (colorLabelEl) colorLabelEl.textContent = '';
                return;
            }
            colorButtons.forEach(function (b) { b.classList.remove('selected'); });
            btn.classList.add('selected');
            selectedColor = btn.dataset.color;
            selectedColorAttr = btn.dataset.attr;
            if (colorLabelEl) colorLabelEl.textContent = btn.title.replace(' (Out of stock)', '');
        });
    });

    /* ================================================================
       SIZE SELECTION
    ================================================================ */
    var sizeWrap    = document.getElementById('gtSizeOptions');
    var sizeButtons = sizeWrap ? Array.prototype.slice.call(sizeWrap.querySelectorAll('.gt-size-opt')) : [];
    var sizeLabelEl = document.getElementById('gtSelectedSizeLabel');
    var selectedSize = null;
    var selectedAttr = null;

    var priceEl      = document.querySelector('.gt-sp-price');
    var origPriceHTML = priceEl ? priceEl.innerHTML : '';
    var sizePrices   = sizeWrap ? JSON.parse(sizeWrap.dataset.prices || '{}') : {};

    function updatePrice(slug) {
        if (!priceEl) return;
        priceEl.innerHTML = (slug && sizePrices[slug]) ? sizePrices[slug] : origPriceHTML;
    }

    sizeButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.dataset.inStock === '0') {
                showToast('This size is out of stock. Please choose another size.', 'error');
                return;
            }
            if (btn.classList.contains('selected')) {
                btn.classList.remove('selected');
                selectedSize = null;
                selectedAttr = null;
                if (sizeLabelEl) sizeLabelEl.textContent = '';
                updatePrice(null);
                return;
            }
            sizeButtons.forEach(function (b) { b.classList.remove('selected'); });
            btn.classList.add('selected');
            selectedSize = btn.dataset.size;
            selectedAttr = btn.dataset.attr;
            if (sizeLabelEl) sizeLabelEl.textContent = btn.firstChild.textContent.trim();
            updatePrice(selectedSize);
        });
    });

    /* ================================================================
       VALIDATE SIZE
    ================================================================ */
    function validateSize(isVariable) {
        if (!isVariable || sizeButtons.length === 0 || selectedSize) return true;
        showToast('Please select a size before continuing.', 'error');
        if (sizeWrap) {
            sizeWrap.classList.remove('gt-size-shake');
            void sizeWrap.offsetWidth;
            sizeWrap.classList.add('gt-size-shake');
            sizeWrap.addEventListener('animationend', function () {
                sizeWrap.classList.remove('gt-size-shake');
            }, { once: true });
        }
        return false;
    }

    /* ================================================================
       ADD TO CART
    ================================================================ */
    var atcBtn = document.getElementById('gtAddToCart');
    if (atcBtn) {
        atcBtn.addEventListener('click', function () {
            if (!validateSize(atcBtn.dataset.isVariable === '1')) return;
            doAction(false);
        });
    }

    /* ================================================================
       BUY NOW
    ================================================================ */
    var buyNowBtn = document.getElementById('gtBuyNow');
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function () {
            if (!validateSize(buyNowBtn.dataset.isVariable === '1')) return;
            doAction(true);
        });
    }

    /* ================================================================
       doAction — resolve variation → add to cart via Store API
    ================================================================ */
    function doAction(buyNow) {
        var qty      = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
        var isVar    = (atcBtn && atcBtn.dataset.isVariable === '1') || (buyNowBtn && buyNowBtn.dataset.isVariable === '1');
        var btn      = buyNow ? buyNowBtn : atcBtn;
        var origHTML = btn.innerHTML;

        /* Get product ID from WC hidden form */
        var productId = 0;
        var wcForm    = document.querySelector('.gt-sp-wc-form form');
        if (wcForm) {
            var addInput = wcForm.querySelector('[name="add-to-cart"]');
            if (addInput) productId = parseInt(addInput.value, 10);
        }
        if (!productId) {
            var m = document.body.className.match(/postid-(\d+)/);
            if (m) productId = parseInt(m[1], 10);
        }
        if (!productId) { showToast('Could not identify product. Please refresh.', 'error'); return; }

        btn.disabled    = true;
        btn.textContent = '…';

        if (isVar && selectedSize) {
            var fd = new FormData();
            fd.append('product_id', productId);
            fd.append(selectedAttr, selectedSize);
            fetch('/?wc-ajax=get_variation', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (!d || !d.variation_id || !d.is_purchasable || !d.is_in_stock) {
                        btn.disabled  = false;
                        btn.innerHTML = origHTML;
                        showToast('This variation is unavailable. Please try another size.', 'error');
                        return;
                    }
                    addToCart(d.variation_id, qty, buyNow, btn, origHTML);
                })
                .catch(function () {
                    btn.disabled  = false;
                    btn.innerHTML = origHTML;
                    showToast('Something went wrong. Please try again.', 'error');
                });
        } else {
            addToCart(productId, qty, buyNow, btn, origHTML);
        }
    }

    function addToCart(itemId, qty, buyNow, btn, origHTML) {
        var root = (typeof wpApiSettings !== 'undefined' && wpApiSettings.root) ? wpApiSettings.root : '/wp-json/';
        fetch(root + 'wc/store/v1/cart', { credentials: 'include' })
            .then(function (r) {
                var nonce = r.headers.get('Nonce') || r.headers.get('X-WC-Store-API-Nonce') || '';
                return fetch(root + 'wc/store/v1/cart/add-item', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json', 'Nonce': nonce },
                    body: JSON.stringify({ id: itemId, quantity: qty }),
                });
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled  = false;
                btn.innerHTML = origHTML;
                if (data.code) { showToast(data.message || 'Could not add to cart.', 'error'); return; }
                if (window.gtCart) {
                    window.gtCart.fetchFragments();
                    if (!buyNow) window.gtCart.showToast((document.querySelector('.gt-sp-title') || {}).textContent || '');
                }
                if (buyNow) {
                    var url = '/checkout/';
                    if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.cart_url) {
                        url = wc_add_to_cart_params.cart_url.replace(/\/cart\/?$/, '/checkout/');
                    }
                    window.location.href = url;
                }
            })
            .catch(function () {
                btn.disabled  = false;
                btn.innerHTML = origHTML;
                showToast('Something went wrong. Please try again.', 'error');
            });
    }

    /* ================================================================
       SIZE CHART MODAL
    ================================================================ */
    var sizeChartBtn   = document.getElementById('gtSizeChartBtn');
    var sizeModal      = document.getElementById('gtSizeModal');
    var sizeModalClose = document.getElementById('gtSizeModalClose');
    var sizeModalBack  = document.getElementById('gtSizeModalBackdrop');

    if (sizeChartBtn) sizeChartBtn.addEventListener('click', function () {
        sizeModal.hidden = false; document.body.style.overflow = 'hidden';
    });
    if (sizeModalClose) sizeModalClose.addEventListener('click', function () {
        sizeModal.hidden = true; document.body.style.overflow = '';
    });
    if (sizeModalBack) sizeModalBack.addEventListener('click', function () {
        sizeModal.hidden = true; document.body.style.overflow = '';
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sizeModal && !sizeModal.hidden) {
            sizeModal.hidden = true; document.body.style.overflow = '';
        }
    });

    /* ================================================================
       ACCORDIONS
    ================================================================ */
    var allTriggers = document.querySelectorAll('.gt-accordion-trigger');
    allTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var expanded = trigger.getAttribute('aria-expanded') === 'true';
            /* close all others */
            allTriggers.forEach(function (t) {
                t.setAttribute('aria-expanded', 'false');
                var panel = t.nextElementSibling;
                panel.style.maxHeight = '0';
                panel.classList.remove('is-open');
            });
            /* toggle clicked */
            if (!expanded) {
                trigger.setAttribute('aria-expanded', 'true');
                var panel = trigger.nextElementSibling;
                panel.classList.add('is-open');
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }
        });
    });

    /* set initial open panel height */
    document.querySelectorAll('.gt-accordion-panel.is-open').forEach(function (panel) {
        panel.style.maxHeight = panel.scrollHeight + 'px';
    });

});
