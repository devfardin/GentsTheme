<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get price range for slider
$min_price = (int) ( wc_get_price_decimals() ? floor( (float) WC()->session?->get('min_price', 0) ) : 0 );
$price_results = $wpdb->get_row("SELECT MIN(CAST(meta_value AS DECIMAL(10,2))), MAX(CAST(meta_value AS DECIMAL(10,2))) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''");
$price_min = $price_results ? (int) floor((float) $price_results->{'MIN(CAST(meta_value AS DECIMAL(10,2)))'}) : 0;
$price_max = $price_results ? (int) ceil((float)  $price_results->{'MAX(CAST(meta_value AS DECIMAL(10,2)))'}) : 10000;

// Get product categories
$categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0]);
?>

<div class="gt-archive-wrap">
    <div class="container">
        <div class="gt-archive-layout">

            <!-- Sidebar -->
            <aside class="gt-shop-sidebar" id="gt-shop-sidebar">

                <!-- Sort (mobile only inside sidebar) -->
                <div class="gt-sidebar-widget">
                    <p class="gt-sidebar-heading">SORT BY</p>
                    <select id="gt-orderby" class="gt-sort-select" style="width:100%;height:38px;padding:0 10px;border:1px solid var(--border);border-radius:8px;font-size:13.5px;color:var(--text-primary);background:var(--bg-body);outline:none;">
                        <option value="date">Newest First</option>
                        <option value="popularity">Most Popular</option>
                        <option value="rating">Top Rated</option>
                        <option value="price">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                    </select>
                </div>

                <!-- Categories -->
                <?php if (!is_wp_error($categories) && $categories) : ?>
                <div class="gt-sidebar-widget">
                    <p class="gt-sidebar-heading">CATEGORIES</p>
                    <ul class="gt-sidebar-cats">
                        <li>
                            <button class="gt-cat-btn active" data-cat="">
                                All
                                <span class="gt-cat-count"><?php echo esc_html(wp_count_posts('product')->publish); ?></span>
                            </button>
                        </li>
                        <?php foreach ($categories as $cat) : ?>
                        <li>
                            <button class="gt-cat-btn" data-cat="<?php echo esc_attr($cat->slug); ?>">
                                <?php echo esc_html($cat->name); ?>
                                <span class="gt-cat-count"><?php echo esc_html($cat->count); ?></span>
                            </button>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Price Range -->
                <div class="gt-sidebar-widget">
                    <p class="gt-sidebar-heading">PRICE RANGE</p>
                    <div class="gt-price-labels">
                        <span>৳<span id="gt-price-min-val"><?php echo esc_html($price_min); ?></span></span>
                        <span>৳<span id="gt-price-max-val"><?php echo esc_html($price_max); ?></span></span>
                    </div>
                    <div class="gt-price-slider-wrap">
                        <div class="gt-price-track">
                            <div class="gt-price-range-fill" id="gt-price-fill"></div>
                        </div>
                        <input type="range" class="gt-price-range" id="gt-min-price" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" value="<?php echo esc_attr($price_min); ?>">
                        <input type="range" class="gt-price-range" id="gt-max-price" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" value="<?php echo esc_attr($price_max); ?>">
                    </div>
                    <button class="gt-price-apply" id="gt-price-apply">Apply</button>
                </div>

            </aside>

            <!-- Main content -->
            <div>
                <!-- Toolbar -->
                <div class="gt-archive-toolbar">
                    <button class="gt-filter-toggle" id="gt-filter-toggle" aria-expanded="false">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                        Filters
                    </button>
                    <span class="gt-result-count" id="gt-result-count"></span>
                    <div class="gt-sort-wrap gt-sort-desktop">
                        <select id="gt-orderby-desktop" style="height:40px;padding:0 14px;border:1px solid var(--border);border-radius:10px;font-size:13.5px;color:var(--text-primary);background:var(--bg-body);outline:none;cursor:pointer;">
                            <option value="date">Newest First</option>
                            <option value="popularity">Most Popular</option>
                            <option value="rating">Top Rated</option>
                            <option value="price">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                        </select>
                    </div>
                    <button class="gt-clear-filters" id="gt-clear-filters">✕ Clear Filters</button>
                </div>

                <!-- Product grid -->
                <div id="gt-shop-grid" class="woocommerce"></div>

                <!-- Load more -->
                <div class="gt-load-more-wrap" id="gt-load-more-wrap" style="display:none;">
                    <button class="gt-load-more-btn" id="gt-load-more">Load More</button>
                </div>
            </div>

        </div><!-- .gt-archive-layout -->
    </div>
</div>

<!-- Mobile sidebar overlay -->
<div class="gt-sidebar-overlay" id="gt-sidebar-overlay"></div>

<script>
// Sync desktop sort select with sidebar sort select
document.addEventListener('DOMContentLoaded', function () {
    const sidebarSel  = document.getElementById('gt-orderby');
    const desktopSel  = document.getElementById('gt-orderby-desktop');
    if (!sidebarSel || !desktopSel) return;
    desktopSel.addEventListener('change', () => {
        sidebarSel.value = desktopSel.value;
        sidebarSel.dispatchEvent(new Event('change'));
    });
    sidebarSel.addEventListener('change', () => {
        desktopSel.value = sidebarSel.value;
    });
});
</script>
