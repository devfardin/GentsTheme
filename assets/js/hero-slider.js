(function () {
    'use strict';

    function initHeroSlider(hero) {
        const slides = hero.querySelectorAll('.hs-slide');
        const dots   = hero.querySelectorAll('.hs-dot');
        const speed  = parseInt(hero.dataset.speed, 10) || 5000;
        const total  = slides.length;

        if (total < 2) return;

        // Build a per-slide content map: slideIndex -> .hs-content element (or null)
        const contentMap = [];
        slides.forEach(function (_, i) {
            // dot carries data-index matching slide index; find matching content by same position
            contentMap[i] = hero.querySelector('.hs-content[data-slide="' + i + '"]') || null;
        });

        // Fallback: if no data-slide attrs used, map by DOM order
        const allContents = Array.from(hero.querySelectorAll('.hs-content'));
        if (allContents.length && contentMap.every(function (c) { return c === null; })) {
            slides.forEach(function (slide, i) {
                contentMap[i] = allContents[i] || null;
            });
        }

        let current  = 0;
        let elapsed  = 0;
        let lastTick = null;

        // Progress bar
        const bar = document.createElement('div');
        bar.className = 'hs-progress';
        bar.style.width = '0%';
        hero.appendChild(bar);

        function setActive(index) {
            // Deactivate current
            slides[current].classList.remove('hs-slide--active');
            if (contentMap[current]) contentMap[current].classList.remove('hs-content--active');
            if (dots[current])       dots[current].classList.remove('hs-dot--active');

            current = (index + total) % total;

            // Activate next
            slides[current].classList.add('hs-slide--active');
            if (contentMap[current]) contentMap[current].classList.add('hs-content--active');
            if (dots[current])       dots[current].classList.add('hs-dot--active');

            elapsed = 0;
            bar.style.width = '0%';
        }

        function tick(timestamp) {
            if (!lastTick) lastTick = timestamp;
            const delta = timestamp - lastTick;
            lastTick = timestamp;

            elapsed += delta;
            bar.style.width = Math.min((elapsed / speed) * 100, 100) + '%';

            if (elapsed >= speed) {
                setActive(current + 1);
            }

            requestAnimationFrame(tick);
        }

        // Arrows
        const prevBtn = hero.querySelector('.hs-arrow--prev');
        const nextBtn = hero.querySelector('.hs-arrow--next');
        if (prevBtn) prevBtn.addEventListener('click', function () { setActive(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { setActive(current + 1); });

        // Dots
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                setActive(parseInt(dot.dataset.index, 10));
            });
        });

        // Swipe
        let touchStartX = 0;
        hero.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].clientX;
        }, { passive: true });
        hero.addEventListener('touchend', function (e) {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) setActive(diff > 0 ? current + 1 : current - 1);
        }, { passive: true });

        requestAnimationFrame(tick);
    }

    function init() {
        document.querySelectorAll('.hs-hero').forEach(initHeroSlider);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
