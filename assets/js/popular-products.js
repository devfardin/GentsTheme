document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('load-more-products');
    const grid = document.getElementById('popular-products-grid');

    if (!btn || !grid) return;

    btn.addEventListener('click', () => {
        const page = parseInt(btn.dataset.page, 10);

        btn.disabled    = true;
        btn.textContent = 'Loading...';

        const formData = new FormData();
        formData.append('action', 'load_more_products');
        formData.append('nonce',  popularProductsAjax.nonce);
        formData.append('page',   page);

        fetch(popularProductsAjax.ajaxurl, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(({ success, data }) => {
                if (!success || !data.html) {
                    btn.remove();
                    return;
                }

                const tmp = document.createElement('div');
                tmp.innerHTML = data.html;
                tmp.querySelectorAll('.product').forEach(item => {
                    grid.querySelector('ul.products')?.appendChild(item);
                });

                if (data.has_more) {
                    btn.dataset.page = page + 1;
                    btn.disabled     = false;
                    btn.textContent  = 'Load More';
                } else {
                    btn.remove();
                }
            });
    });
});
