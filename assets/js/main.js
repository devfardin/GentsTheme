document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.dot');
    let current  = 0;
    let timer;

    const goTo = (index) => {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
    };

    const autoPlay = () => {
        timer = setInterval(() => goTo(current + 1), 4000);
    };

    document.querySelector('.hero-btn.next')?.addEventListener('click', () => { clearInterval(timer); goTo(current + 1); autoPlay(); });
    document.querySelector('.hero-btn.prev')?.addEventListener('click', () => { clearInterval(timer); goTo(current - 1); autoPlay(); });

    dots.forEach(dot => dot.addEventListener('click', () => {
        clearInterval(timer);
        goTo(+dot.dataset.index);
        autoPlay();
    }));

    autoPlay();
});


// ── Shop Sidebar Filter Toggle (mobile) ──────────────────────────
(function () {
    const toggle  = document.getElementById('gt-filter-toggle');
    const sidebar = document.getElementById('gt-shop-sidebar');
    if ( ! toggle || ! sidebar ) return;

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'gt-sidebar-overlay';
    document.body.appendChild(overlay);

    const open = () => {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    toggle.addEventListener('click', () => {
        sidebar.classList.contains('is-open') ? close() : open();
    });

    overlay.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if ( e.key === 'Escape' ) close();
    });
})();


// ── Mobile Nav Cart Badge – live update ──────────────────────────
// Piggybacks on header.js's fetchCartFragments() which already calls
// gentstime_get_cart_fragments and updates the header badge (.cart-badge).
// We watch that same element with a MutationObserver so there is zero
// duplicated AJAX logic and the mobile badge always stays in sync.
(function () {
    var mobileBadge = document.querySelector('[data-cart-count-badge]');
    if (!mobileBadge) return;

    function syncFromHeaderBadge() {
        var headerBadge = document.querySelector('#cart-sidebar-toggle .cart-badge');
        var count = headerBadge ? (parseInt(headerBadge.textContent, 10) || 0) : 0;
        mobileBadge.textContent = count;
        mobileBadge.style.display = count > 0 ? '' : 'none';
    }

    // Observe the cart toggle button so we react the instant header.js
    // adds / updates / removes the .cart-badge span inside it.
    var cartToggle = document.getElementById('cart-sidebar-toggle');
    if (cartToggle) {
        new MutationObserver(syncFromHeaderBadge).observe(cartToggle, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }

    // Also sync on page load in case the header badge already exists.
    syncFromHeaderBadge();
})();
