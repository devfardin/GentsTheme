<?php
/**
 * GentsTime — Product Card
 * Overrides: woocommerce/templates/content-product.php
 */
defined('ABSPATH') || exit;

global $product;
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) return;

$product_id     = $product->get_id();
$permalink      = get_permalink( $product_id );
$title          = get_the_title( $product_id );
$rating_count   = $product->get_rating_count();
$average_rating = (float) $product->get_average_rating();
$is_on_sale     = $product->is_on_sale();
$is_featured    = $product->is_featured();
$purchasable    = $product->is_purchasable() && $product->is_in_stock();
$is_simple      = $product->get_type() === 'simple';

/* ── Date created safely ───────────────────────────────────────── */
$date_created = $product->get_date_created();
$is_new       = $date_created && ( time() - $date_created->getTimestamp() ) < ( 30 * DAY_IN_SECONDS );

/* ── Badge ─────────────────────────────────────────────────────── */
$badge_label = '';
$badge_class = '';
if ( $is_on_sale ) {
    $badge_class = 'gt-badge--sale';
    $badge_label = 'Sale';
    if ( $is_simple ) {
        $regular = (float) $product->get_regular_price();
        $sale    = (float) $product->get_sale_price();
        if ( $regular > 0 ) {
            $badge_label = 'Sale &minus;' . round( ( ( $regular - $sale ) / $regular ) * 100 ) . '%';
        }
    }
} elseif ( $is_featured ) {
    $badge_label = 'Hot';
    $badge_class = 'gt-badge--hot';
} elseif ( $is_new ) {
    $badge_label = 'New';
    $badge_class = 'gt-badge--new';
}

/* ── Category ──────────────────────────────────────────────────── */
$cat_name = '';
$terms    = get_the_terms( $product_id, 'product_cat' );
if ( $terms && ! is_wp_error( $terms ) ) {
    $filtered = array_filter( $terms, fn( $t ) => $t->slug !== 'uncategorized' );
    $cat      = $filtered ? reset( $filtered ) : reset( $terms );
    $cat_name = $cat->name;
}

/* ── Checkout URL ──────────────────────────────────────────────── */
$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
?>
<li <?php wc_product_class( 'gt-product-card', $product ); ?> data-product_id="<?php echo esc_attr( $product_id ); ?>">

    <!-- IMAGE ─────────────────────────────────────────────────── -->
    <div class="gt-card-media">

        <a href="<?php echo esc_url( $permalink ); ?>" class="gt-card-img-link" tabindex="-1">
            <?php echo $product->get_image( 'woocommerce_medium', [ 'class' => 'gt-card-img' ] ); ?>
        </a>

        <?php if ( $badge_label ) : ?>
            <span class="gt-card-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo wp_kses_post( $badge_label ); ?></span>
        <?php endif; ?>

        <!-- ACTION TRAY ────────────────────────────────────────── -->
        <?php if ( $purchasable && $is_simple ) : ?>
        <div class="gt-card-actions">

            <!-- Add to Cart -->
            <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
               class="gt-atc-btn ajax_add_to_cart add_to_cart_button button"
               data-product_id="<?php echo esc_attr( $product_id ); ?>"
               data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
               data-quantity="1"
               aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
               rel="nofollow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM5.21 4l-.94-2H1v2h2l3.6 7.59-1.35 2.44C5.16 14.36 5 14.96 5 15.5c0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0023.25 3H5.21z"/></svg>
                <span class="gt-atc-label">Add to Cart</span>
            </a>

            <!-- Buy Now -->
            <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
               class="gt-buy-btn"
               data-product_id="<?php echo esc_attr( $product_id ); ?>"
               data-checkout_url="<?php echo esc_url( $checkout_url ); ?>"
               aria-label="Buy <?php echo esc_attr( $title ); ?> now"
               rel="nofollow">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                <span>Buy Now</span>
            </a>

        </div>
        <?php elseif ( ! $purchasable || ! $is_simple ) : ?>
        <div class="gt-card-actions gt-card-actions--single">
            <a href="<?php echo esc_url( $permalink ); ?>" class="gt-view-btn">
                <span>View Product</span>
            </a>
        </div>
        <?php endif; ?>

    </div><!-- /.gt-card-media -->

    <!-- BODY ──────────────────────────────────────────────────── -->
    <div class="gt-card-body">

        <?php if ( $cat_name ) : ?>
            <span class="gt-card-cat"><?php echo esc_html( $cat_name ); ?></span>
        <?php endif; ?>

        <a href="<?php echo esc_url( $permalink ); ?>" class="gt-card-title">
            <?php echo esc_html( $title ); ?>
        </a>

        <?php if ( $rating_count > 0 ) : ?>
            <div class="gt-card-rating">
                <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                    <svg viewBox="0 0 24 24" class="gt-star<?php echo ( $i <= round( $average_rating ) ) ? ' gt-star--on' : ''; ?>">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>
                    </svg>
                <?php endfor; ?>
                <span class="gt-card-rating-count">(<?php echo esc_html( $rating_count ); ?>)</span>
            </div>
        <?php endif; ?>

        <div class="gt-card-price"><?php echo $product->get_price_html(); ?></div>

    </div>

</li>
