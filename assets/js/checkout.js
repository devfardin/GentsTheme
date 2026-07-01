document.addEventListener('DOMContentLoaded', function () {

    var AJAX_URL = gtCheckout.ajaxurl;
    var NONCE    = gtCheckout.nonce;

    /* ══════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════ */

    function postAjax(action, params, callback) {
        var body = 'action=' + encodeURIComponent(action) + '&nonce=' + encodeURIComponent(NONCE);
        Object.keys(params).forEach(function (k) {
            body += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);
        });
        fetch(AJAX_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        })
        .then(function (r) { return r.json(); })
        .then(callback)
        .catch(function () { showToast('Something went wrong. Please try again.', 'error'); });
    }

    /* Floating toast notification */
    function showToast(msg, type) {
        var existing = document.getElementById('gt-toast');
        if (existing) existing.remove();
        var toast = document.createElement('div');
        toast.id        = 'gt-toast';
        toast.className = 'gt-toast gt-toast-' + (type || 'info');
        toast.innerHTML = msg;
        document.body.appendChild(toast);
        setTimeout(function () { toast.classList.add('gt-toast-show'); }, 10);
        setTimeout(function () {
            toast.classList.remove('gt-toast-show');
            setTimeout(function () { toast.remove(); }, 400);
        }, 3500);
    }

    /* Show server-side WC notices (validation errors etc.) as toasts on page load */
    if (gtCheckout.notices && gtCheckout.notices.length) {
        gtCheckout.notices.forEach(function (n, i) {
            var type = n.type === 'success' ? 'success' : (n.type === 'notice' ? 'info' : 'error');
            setTimeout(function () { showToast(n.message, type); }, i * 800);
        });
    }

    /* Update header cart badge + mini-cart sidebar + mobile nav badge */
    function updateHeaderCart(data) {
        var d = data.data;
        if (typeof d.cart_count === 'undefined') return;

        var count = d.cart_count;

        /* ── 1. Header desktop cart badge (.cart-badge inside #cart-sidebar-toggle) ── */
        var cartToggleBtn = document.getElementById('cart-sidebar-toggle');
        if (cartToggleBtn) {
            var badge = cartToggleBtn.querySelector('.cart-badge');
            if (count > 0) {
                if (badge) {
                    badge.textContent = count;
                } else {
                    badge = document.createElement('span');
                    badge.className   = 'cart-badge';
                    badge.textContent = count;
                    cartToggleBtn.appendChild(badge);
                }
            } else {
                if (badge) badge.remove();
            }
        }

        /* ── 2. Mobile nav cart badge ([data-cart-count-badge]) ── */
        var mobileBadge = document.querySelector('[data-cart-count-badge]');
        if (mobileBadge) {
            mobileBadge.textContent    = count > 0 ? count : '';
            mobileBadge.style.display  = count > 0 ? '' : 'none';
        }

        /* ── 3. Cart sidebar body (mini-cart HTML) ── */
        if (d.mini_cart) {
            var cartSidebarBody = document.querySelector('#cart-sidebar .cart-sidebar-body');
            if (cartSidebarBody) cartSidebarBody.innerHTML = d.mini_cart;
        }

        /* ── 4. Cart sidebar footer — hide when empty ── */
        var cartSidebarFooter = document.querySelector('#cart-sidebar .cart-sidebar-footer');
        if (cartSidebarFooter) {
            cartSidebarFooter.style.display = count > 0 ? '' : 'none';
        }
    }

    /* Build a single coupon discount row with remove button */
    function buildCouponRow(code, discount) {
        var row = document.createElement('div');
        row.className        = 'gt-summary-row gt-discount';
        row.dataset.coupon   = code.toLowerCase();
        row.innerHTML =
            '<span>' +
                'Coupon (' + code.toUpperCase() + ')' +
                ' <button type="button" class="gt-remove-coupon-btn" data-code="' + code.toLowerCase() + '" title="Remove coupon">&#10005;</button>' +
            '</span>' +
            '<span class="gt-summary-val gt-discount-val">' + discount + '</span>';
        return row;
    }

    /* Update the Order Summary totals block in the DOM */
    function updateSummary(data) {
        var d = data.data;

        var subEl = document.getElementById('gt-summary-subtotal');
        if (subEl) subEl.innerHTML = d.subtotal;

        var shipEl = document.getElementById('gt-summary-shipping');
        if (shipEl && d.shipping !== undefined) shipEl.innerHTML = d.shipping;

        var totalEl = document.getElementById('gt-summary-total');
        if (totalEl) totalEl.innerHTML = d.total;

        var discountWrap = document.getElementById('gt-discount-rows');
        if (discountWrap) {
            discountWrap.innerHTML = '';
            if (d.coupons && d.coupons.length) {
                d.coupons.forEach(function (c) {
                    discountWrap.appendChild(buildCouponRow(c.code, c.discount));
                });
            }
        }

        updateHeaderCart(data);
    }

    /* Set the shipping cell to the "pending zone" placeholder */
    function setShippingPending() {
        var shipEl = document.getElementById('gt-summary-shipping');
        if (shipEl) shipEl.innerHTML = '<span class="gt-zone-pending">Select zone to calculate</span>';
    }

    /* Disable / re-enable a button during AJAX */
    function setLoading(btn, loading) {
        btn.disabled = loading;
        btn.classList.toggle('gt-loading', loading);
    }

    /* ══════════════════════════════════════════
       1. QUANTITY +/- BUTTONS
    ══════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gt-qty-minus, .gt-qty-plus');
        if (!btn) return;

        var key   = btn.dataset.key;
        var input = document.querySelector('.gt-qty-input[data-key="' + key + '"]');
        if (!input) return;

        var val = parseInt(input.value, 10) || 1;
        if (btn.classList.contains('gt-qty-minus') && val > 1) val--;
        if (btn.classList.contains('gt-qty-plus')) val++;
        input.value = val;

        var row = document.querySelector('.gt-cart-row[data-key="' + key + '"]');
        setLoading(btn, true);

        postAjax('gt_update_cart_item', { cart_item_key: key, qty: val }, function (data) {
            setLoading(btn, false);
            if (data.success) {
                var subtotalCell = row ? row.querySelector('.gt-cart-subtotal') : null;
                if (subtotalCell && data.data.item_subtotal) {
                    subtotalCell.innerHTML = data.data.item_subtotal;
                }
                updateSummary(data);
                showToast('Cart updated.', 'success');
            } else {
                showToast(data.data.message || 'Could not update cart.', 'error');
            }
        });
    });

    /* ══════════════════════════════════════════
       2. REMOVE ITEM BUTTON
    ══════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gt-remove-btn');
        if (!btn) return;

        var key = btn.dataset.key;
        var row = document.querySelector('.gt-cart-row[data-key="' + key + '"]');

        setLoading(btn, true);

        postAjax('gt_remove_cart_item', { cart_item_key: key }, function (data) {
            if (data.success) {
                if (row) {
                    row.style.transition = 'opacity .3s, max-height .3s';
                    row.style.opacity    = '0';
                    row.style.maxHeight  = '0';
                    row.style.overflow   = 'hidden';
                    setTimeout(function () { row.remove(); }, 320);
                }
                updateSummary(data);
                showToast('Item removed from cart.', 'success');

                if (data.data.cart_is_empty) {
                    setTimeout(function () { location.reload(); }, 700);
                }
            } else {
                setLoading(btn, false);
                showToast(data.data.message || 'Could not remove item.', 'error');
            }
        });
    });

    /* ══════════════════════════════════════════
       3. APPLY COUPON
    ══════════════════════════════════════════ */

    var couponBtn = document.getElementById('gt-apply-coupon');
    var couponMsg = document.getElementById('gt-coupon-message');

    function setCouponMessage(msg, type) {
        if (!couponMsg) return;
        couponMsg.textContent   = msg;
        couponMsg.className     = 'gt-coupon-message gt-coupon-msg-' + type;
        couponMsg.style.display = msg ? 'block' : 'none';
    }

    if (couponBtn) {
        couponBtn.addEventListener('click', function () {
            var code = (document.getElementById('gt_coupon_code').value || '').trim();
            if (!code) {
                setCouponMessage('Please enter a coupon code.', 'error');
                return;
            }
            setLoading(couponBtn, true);
            setCouponMessage('', '');

            postAjax('gt_apply_coupon', { coupon_code: code }, function (data) {
                setLoading(couponBtn, false);
                if (data.success) {
                    setCouponMessage(data.data.message, 'success');
                    updateSummary(data);
                    document.getElementById('gt_coupon_code').value = '';
                    showToast(data.data.message, 'success');
                } else {
                    setCouponMessage(data.data.message || 'Invalid coupon.', 'error');
                    showToast(data.data.message || 'Invalid coupon.', 'error');
                }
            });
        });

        var couponInput = document.getElementById('gt_coupon_code');
        if (couponInput) {
            couponInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); couponBtn.click(); }
            });
        }
    }

    /* ══════════════════════════════════════════
       4. REMOVE COUPON
    ══════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gt-remove-coupon-btn');
        if (!btn) return;

        var code = btn.dataset.code;
        if (!code) return;

        setLoading(btn, true);

        postAjax('gt_remove_coupon', { coupon_code: code }, function (data) {
            if (data.success) {
                updateSummary(data);
                showToast(data.data.message, 'success');
                setCouponMessage('', '');
            } else {
                setLoading(btn, false);
                showToast(data.data.message || 'Could not remove coupon.', 'error');
            }
        });
    });

    /* ══════════════════════════════════════════
       5. FORM FIELD SYNC (hidden WC fields)
    ══════════════════════════════════════════ */

    var fullNameInput = document.getElementById('billing_full_name');
    if (fullNameInput) {
        function syncName() {
            var parts = fullNameInput.value.trim().split(/\s+/);
            document.getElementById('billing_first_name_hidden').value = parts[0] || '';
            document.getElementById('billing_last_name_hidden').value  = parts.slice(1).join(' ') || parts[0] || '';
        }
        fullNameInput.addEventListener('input', syncName);
        syncName();
    }

    /* ══════════════════════════════════════════
       5. DISTRICT → THANA DROPDOWNS
    ══════════════════════════════════════════ */

    /* Tell WooCommerce to recalculate shipping when district changes */
    function triggerShippingUpdate() {
        var district = districtSelect ? districtSelect.value : '';
        if (!district) {
            setShippingPending();
            hideDeliveryHint();
            return;
        }

        postAjax('gt_update_shipping_district', { billing_district: district }, function (data) {
            if (data.success) {
                var shipEl = document.getElementById('gt-summary-shipping');
                if (shipEl && data.data.shipping !== undefined) shipEl.innerHTML = data.data.shipping;
                var totalEl = document.getElementById('gt-summary-total');
                if (totalEl && data.data.total) totalEl.innerHTML = data.data.total;
                /* Only show the hint when the response contains a real price (not a placeholder span) */
                var rawShipping = data.data.shipping || '';
                if (rawShipping && rawShipping.indexOf('gt-zone-pending') === -1 && rawShipping.indexOf('gt-free-delivery') === -1) {
                    showDeliveryHint(rawShipping);
                } else if (rawShipping.indexOf('gt-free-delivery') !== -1) {
                    showDeliveryHint('Free');
                } else {
                    hideDeliveryHint();
                }
            }
        });
    }

    function showDeliveryHint(costHtml) {
        var hint = document.getElementById('gt-delivery-charge-hint');
        var cost = document.getElementById('gt-dch-cost');
        if (!hint || !cost) return;
        cost.innerHTML   = costHtml;
        hint.style.display = 'flex';
    }

    function hideDeliveryHint() {
        var hint = document.getElementById('gt-delivery-charge-hint');
        if (hint) hint.style.display = 'none';
    }

    var districtSelect = document.getElementById('billing_district');
    var thanaSelect    = document.getElementById('billing_thana');
    var stateHidden    = document.getElementById('billing_state_hidden');
    var cityHidden     = document.getElementById('billing_city_hidden');

    if (districtSelect && typeof BD_LOCATIONS !== 'undefined') {

        /* Populate district options */
        Object.keys(BD_LOCATIONS).sort().forEach(function (district) {
            var opt    = document.createElement('option');
            opt.value  = district;
            opt.text   = district;
            districtSelect.appendChild(opt);
        });

        /* Pre-select saved value (edit order / failed order) */
        var savedDistrict = stateHidden ? stateHidden.value : '';
        if (savedDistrict) {
            districtSelect.value = savedDistrict;
            populateThanas(savedDistrict);
            triggerShippingUpdate();
        } else {
            setShippingPending();
            hideDeliveryHint();
        }

        districtSelect.addEventListener('change', function () {
            var district = this.value;
            if (stateHidden) stateHidden.value = district;
            populateThanas(district);
            if (district) {
                triggerShippingUpdate();
            } else {
                setShippingPending();
                hideDeliveryHint();
            }
        });
    }

    function populateThanas(district) {
        if (!thanaSelect) return;

        /* Reset thana dropdown */
        thanaSelect.innerHTML = '';
        var placeholder = document.createElement('option');

        if (!district || !BD_LOCATIONS[district]) {
            placeholder.value = '';
            placeholder.text  = 'Select district first';
            thanaSelect.appendChild(placeholder);
            thanaSelect.disabled = true;
            if (cityHidden) cityHidden.value = '';
            return;
        }

        placeholder.value = '';
        placeholder.text  = 'Select your thana';
        thanaSelect.appendChild(placeholder);

        BD_LOCATIONS[district].forEach(function (thana) {
            var opt   = document.createElement('option');
            opt.value = thana;
            opt.text  = thana;
            thanaSelect.appendChild(opt);
        });

        thanaSelect.disabled = false;

        /* Pre-select saved thana */
        var savedThana = cityHidden ? cityHidden.value : '';
        if (savedThana) thanaSelect.value = savedThana;

        /* Sync hidden field */
        if (cityHidden) cityHidden.value = thanaSelect.value;

        thanaSelect.addEventListener('change', function () {
            if (cityHidden) cityHidden.value = this.value;
        });
    }

    /* ══════════════════════════════════════════
       6. PHONE VALIDATION
    ══════════════════════════════════════════ */

    var phoneInput = document.getElementById('billing_phone');
    var phoneError = document.createElement('div');
    phoneError.className     = 'gt-phone-error';
    phoneError.style.display = 'none';
    if (phoneInput) phoneInput.parentNode.appendChild(phoneError);

    function validatePhone(val) {
        return /^01[3-9]\d{8}$/.test(val.replace(/[\s\-]/g, ''));
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            if (this.value && !validatePhone(this.value)) {
                phoneError.textContent   = 'Enter a valid BD number, e.g. 01316049157';
                phoneError.style.display = 'block';
                phoneInput.classList.add('gt-input-error');
            } else {
                phoneError.style.display = 'none';
                phoneInput.classList.remove('gt-input-error');
            }
        });
    }

    var checkoutForm = document.querySelector('.gt-checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            if (phoneInput && !validatePhone(phoneInput.value)) {
                e.preventDefault();
                phoneError.textContent   = 'Enter a valid BD number, e.g. 01316049157';
                phoneError.style.display = 'block';
                phoneInput.classList.add('gt-input-error');
                phoneInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

});
