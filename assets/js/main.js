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

