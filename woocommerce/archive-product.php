<?php
/**
 * GentsTime — Archive / Shop Page (AJAX filtered)
 */
defined( 'ABSPATH' ) || exit;

get_header();

$current_term = is_tax() ? get_queried_object() : null;
$page_title   = $current_term ? $current_term->name : woocommerce_page_title( false );

/* ── Size terms ─────────────────────────────────────────────────── */
$size_terms = get_terms( [ 'taxonomy' => 'pa_size', 'hide_empty' => true ] );

/* ── Price range ────────────────────────────────────────────────── */
global $wpdb;
$price_min = (int) $wpdb->get_var( "SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) FROM {$wpdb->postmeta} WHERE meta_key='_price' AND meta_value != ''" );
$price_max = (int) $wpdb->get_var( "SELECT MAX(CAST(meta_value AS DECIMAL(10,2))) FROM {$wpdb->postmeta} WHERE meta_key='_price' AND meta_value != ''" );

/* ── Categories ─────────────────────────────────────────────────── */
$uncategorized_id = get_term_by( 'slug', 'uncategorized', 'product_cat' )->term_id ?? 0;
$categories = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => $uncategorized_id ] );

/* ── Active filter values (initial page load) ───────────────────── */
$active_cat  = $current_term ? $current_term->slug : '';
?>

<!-- Breadcrumb Bar -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title"><?php echo esc_html( $page_title ); ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <?php if ( $current_term ) : ?>
                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">Shop</a>
                <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
                <span class="breadcrumb-current" aria-current="page"><?php echo esc_html( $current_term->name ); ?></span>
            <?php else : ?>
                <span class="breadcrumb-current" aria-current="page">Shop</span>
            <?php endif; ?>
        </nav>
    </div>
</div>

<!-- Main Shop Layout -->
<div class="container gt-archive-wrap">
    <div class="gt-archive-layout">

        <!-- ── SIDEBAR ── -->
        <aside class="gt-shop-sidebar" id="gt-shop-sidebar" aria-label="Shop filters">

            <!-- 1. Size Filter -->
            <?php if ( $size_terms && ! is_wp_error( $size_terms ) ) : ?>
            <div class="gt-sidebar-widget">
                <h3 class="gt-sidebar-heading">Filter by Size</h3>
                <div class="gt-size-grid">
                    <?php foreach ( $size_terms as $sz ) : ?>
                        <button class="gt-size-btn" data-size="<?php echo esc_attr( $sz->slug ); ?>">
                            <?php echo esc_html( $sz->name ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 2. Price Filter -->
            <div class="gt-sidebar-widget">
                <h3 class="gt-sidebar-heading">Filter by Price</h3>
                <div class="gt-price-slider-wrap">
                    <div class="gt-price-track">
                        <div class="gt-price-range-fill" id="gt-price-fill"></div>
                    </div>
                    <input type="range" id="gt-min-price" class="gt-price-range" min="<?php echo esc_attr( $price_min ); ?>" max="<?php echo esc_attr( $price_max ); ?>" value="<?php echo esc_attr( $price_min ); ?>" step="1">
                    <input type="range" id="gt-max-price" class="gt-price-range" min="<?php echo esc_attr( $price_min ); ?>" max="<?php echo esc_attr( $price_max ); ?>" value="<?php echo esc_attr( $price_max ); ?>" step="1">
                </div>
                <div class="gt-price-labels">
                    <span><?php echo get_woocommerce_currency_symbol(); ?><span id="gt-price-min-val"><?php echo esc_html( $price_min ); ?></span></span>
                    <span><?php echo get_woocommerce_currency_symbol(); ?><span id="gt-price-max-val"><?php echo esc_html( $price_max ); ?></span></span>
                </div>
                <button class="gt-price-apply" id="gt-price-apply">Apply</button>
            </div>

            <!-- 3. Category Filter -->
            <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
            <div class="gt-sidebar-widget">
                <h3 class="gt-sidebar-heading">Categories</h3>
                <ul class="gt-sidebar-cats">
                    <li>
                        <button class="gt-cat-btn<?php echo ! $active_cat ? ' active' : ''; ?>" data-cat="">
                            All Products
                        </button>
                    </li>
                    <?php foreach ( $categories as $cat ) : ?>
                        <li>
                            <button class="gt-cat-btn<?php echo ( $active_cat === $cat->slug ) ? ' active' : ''; ?>" data-cat="<?php echo esc_attr( $cat->slug ); ?>">
                                <?php echo esc_html( $cat->name ); ?>
                                <span class="gt-cat-count"><?php echo esc_html( $cat->count ); ?></span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

        </aside>
        <!-- /.gt-shop-sidebar -->

        <!-- ── MAIN CONTENT ── -->
        <div class="gt-archive-main">

            <!-- Toolbar -->
            <div class="gt-archive-toolbar">
                <button class="gt-filter-toggle" id="gt-filter-toggle" aria-expanded="false" aria-controls="gt-shop-sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/></svg>
                    Filters
                </button>
                <p class="gt-result-count" id="gt-result-count">Loading…</p>
                <button class="gt-clear-filters" id="gt-clear-filters" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    Clear Filters
                </button>
                <div class="gt-sort-wrap">
                    <select id="gt-orderby">
                        <option value="menu_order">Default sorting</option>
                        <option value="popularity">Sort by popularity</option>
                        <option value="rating">Sort by rating</option>
                        <option value="date">Sort by latest</option>
                        <option value="price">Sort by price: low to high</option>
                        <option value="price-desc">Sort by price: high to low</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="gt-shop-grid" class="woocommerce">
                <!-- Populated via AJAX -->
            </div>

            <!-- Load More -->
            <div class="gt-load-more-wrap" id="gt-load-more-wrap" style="display:none;">
                <button id="gt-load-more" class="gt-load-more-btn">Load More</button>
            </div>

        </div>
        <!-- /.gt-archive-main -->

    </div>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="gt-sidebar-overlay" id="gt-sidebar-overlay"></div>

<script>
window.gtShopData = {
    ajaxurl: '<?php echo esc_js( admin_url( "admin-ajax.php" ) ); ?>',
    nonce:   '<?php echo esc_js( wp_create_nonce( "shop_ajax_nonce" ) ); ?>',
    initCat: '<?php echo esc_js( $active_cat ); ?>',
    priceMin: <?php echo (int) $price_min; ?>,
    priceMax: <?php echo (int) $price_max; ?>,
};
</script>

<?php get_footer(); ?>
