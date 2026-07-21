<?php
/**
 * GentsTime — Single Product Page
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    $product = wc_get_product( get_the_ID() );
    if ( ! $product ) continue;

    // Gallery images
    $attachment_ids = $product->get_gallery_image_ids();
    $featured_id    = $product->get_image_id();
    $all_image_ids  = array_merge( [ $featured_id ], $attachment_ids );

    // Meta
    $brand_terms  = wc_get_product_terms( get_the_ID(), 'product_brand', [ 'fields' => 'names' ] );
    $brand        = ! empty( $brand_terms ) ? implode( ', ', $brand_terms ) : ( $product->get_attribute( 'brand' ) ?: '' );
    $sku          = $product->get_sku();
    $stock_status = $product->get_stock_status();
    $availability = $stock_status === 'instock' ? 'In Stock' : ( $stock_status === 'onbackorder' ? 'On Backorder' : 'Out of Stock' );

    // Category
    $terms    = get_the_terms( get_the_ID(), 'product_cat' );
    $filtered = $terms && ! is_wp_error( $terms ) ? array_filter( $terms, function( $t ) { return $t->slug !== 'uncategorized'; } ) : [];
    $cat      = $filtered ? reset( $filtered ) : ( $terms ? reset( $terms ) : null );

    // Size options + per-variation stock for variable products
    $size_options    = [];
    $size_attr_key   = '';
    $size_stock_map  = []; // slug => bool (in stock)

    // Color/swatch options
    $color_options   = [];
    $color_attr_key  = '';
    $color_stock_map = []; // slug => bool (in stock)

    if ( $product->is_type( 'variable' ) ) {
        $variation_attributes = $product->get_variation_attributes();
        if ( isset( $variation_attributes['pa_size'] ) ) {
            $size_attr_key = 'pa_size';
            $size_options  = $variation_attributes['pa_size'];
        } elseif ( isset( $variation_attributes['size'] ) ) {
            $size_attr_key = 'size';
            $size_options  = $variation_attributes['size'];
        }

        if ( isset( $variation_attributes['pa_color'] ) ) {
            $color_attr_key = 'pa_color';
            $color_options  = $variation_attributes['pa_color'];
        } elseif ( isset( $variation_attributes['pa_colour'] ) ) {
            $color_attr_key = 'pa_colour';
            $color_options  = $variation_attributes['pa_colour'];
        } elseif ( isset( $variation_attributes['color'] ) ) {
            $color_attr_key = 'color';
            $color_options  = $variation_attributes['color'];
        } elseif ( isset( $variation_attributes['colour'] ) ) {
            $color_attr_key = 'colour';
            $color_options  = $variation_attributes['colour'];
        }

        // Build stock maps using ALL variation children (including OOS)
        // get_available_variations() skips OOS variations, causing ?? true to wrongly mark them in-stock
        $all_variation_ids = $product->get_children();
        foreach ( $all_variation_ids as $var_id ) {
            $variation = wc_get_product( $var_id );
            if ( ! $variation ) continue;
            $in_stock = $variation->is_in_stock() && $variation->is_purchasable();
            $var_attrs = $variation->get_variation_attributes(); // keyed as attribute_pa_size etc.

            if ( $size_attr_key ) {
                $key  = 'attribute_' . $size_attr_key;
                $slug = $var_attrs[ $key ] ?? '';
                if ( $slug !== '' ) {
                    if ( ! isset( $size_stock_map[ $slug ] ) ) $size_stock_map[ $slug ] = false;
                    if ( $in_stock ) $size_stock_map[ $slug ] = true;
                }
            }
            if ( $color_attr_key ) {
                $key  = 'attribute_' . $color_attr_key;
                $slug = $var_attrs[ $key ] ?? '';
                if ( $slug !== '' ) {
                    if ( ! isset( $color_stock_map[ $slug ] ) ) $color_stock_map[ $slug ] = false;
                    if ( $in_stock ) $color_stock_map[ $slug ] = true;
                }
            }
        }
    }
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title"><?php the_title(); ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <?php if ( $cat ) : ?>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
                <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <?php endif; ?>
            <span class="breadcrumb-current" aria-current="page"><?php the_title(); ?></span>
        </nav>
    </div>
</div>

<div class="container gt-single-product">

    <!-- ── Top: Gallery + Summary ──────────────────────────────── -->
    <div class="gt-sp-layout">

        <!-- LEFT: Gallery -->
        <div class="gt-sp-gallery-wrap">

            <div class="gt-sp-thumbs" id="gtSpThumbs">
                <?php foreach ( $all_image_ids as $i => $img_id ) :
                    $thumb = wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' );
                    $full  = wp_get_attachment_image_url( $img_id, 'woocommerce_single' );
                    if ( ! $thumb ) continue;
                ?>
                <button class="gt-sp-thumb<?php echo $i === 0 ? ' active' : ''; ?>"
                        data-full="<?php echo esc_url( $full ); ?>"
                        data-index="<?php echo $i; ?>"
                        aria-label="Product image <?php echo $i + 1; ?>">
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy">
                </button>
                <?php endforeach; ?>
            </div>

            <div class="gt-sp-main-img" id="gtSpMainImg">
                <?php
                $first_full  = wp_get_attachment_image_url( $featured_id, 'woocommerce_single' );
                $first_large = wp_get_attachment_image_url( $featured_id, 'full' );
                ?>
                <div class="gt-sp-zoom-wrap" id="gtSpZoomWrap"
                     data-zoom="<?php echo esc_url( $first_large ); ?>">
                    <img id="gtSpFeatured"
                         src="<?php echo esc_url( $first_full ); ?>"
                         alt="<?php echo esc_attr( get_the_title() ); ?>"
                         class="gt-sp-featured-img">
                </div>
                <!-- lens sits inside gt-sp-main-img (position:relative) -->
                <div class="gt-sp-zoom-lens" id="gtSpZoomLens"></div>
                <?php if ( $product->is_on_sale() ) : ?>
                    <span class="gt-sp-badge-sale">Sale</span>
                <?php endif; ?>
            </div>

        </div><!-- /.gt-sp-gallery-wrap -->

        <!-- RIGHT: Summary -->
        <div class="gt-sp-summary">

            <h1 class="gt-sp-title"><?php the_title(); ?></h1>

            <!-- Rating -->
            <div class="gt-sp-rating">
                <?php
                $avg   = (float) $product->get_average_rating();
                $count = (int)   $product->get_rating_count();
                for ( $i = 1; $i <= 5; $i++ ) :
                    $cls = $i <= $avg ? 'gt-star--on' : ( $i - 0.5 <= $avg ? 'gt-star--half' : '' );
                ?>
                <svg viewBox="0 0 24 24" class="gt-star <?php echo esc_attr( $cls ); ?>">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>
                </svg>
                <?php endfor; ?>
                <span class="gt-sp-rating-count"><?php echo $count > 0 ? "({$count} reviews)" : '(No reviews yet)'; ?></span>
            </div>

            <!-- Price -->
            <div class="gt-sp-price"><?php echo $product->get_price_html(); ?></div>

            <!-- Meta: Brand / Availability / SKU -->
            <div class="gt-sp-meta-info">
                <?php if ( $brand ) : ?>
                <div class="gt-sp-meta-row">
                    <span class="gt-sp-meta-label">Brand</span>
                    <span class="gt-sp-meta-val gt-sp-meta-brand"><?php echo esc_html( $brand ); ?></span>
                </div>
                <?php endif; ?>
                <div class="gt-sp-meta-row">
                    <span class="gt-sp-meta-label">Availability</span>
                    <span class="gt-sp-meta-val gt-sp-stock gt-sp-stock--<?php echo esc_attr( $stock_status ); ?>"><?php echo esc_html( $availability ); ?></span>
                </div>
                <?php if ( $sku ) : ?>
                <div class="gt-sp-meta-row">
                    <span class="gt-sp-meta-label">SKU</span>
                    <span class="gt-sp-meta-val"><?php echo esc_html( $sku ); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Form -->
            <div class="gt-sp-form">

                <?php if ( ! empty( $size_options ) ) : ?>
                <!-- Size label + Size Chart button -->
                <div class="gt-sp-size-header">
                    <span class="gt-sp-size-label">Size: <strong id="gtSelectedSizeLabel"></strong></span>
                </div>

                <!-- Size button options -->
                <?php
                // Build variation price map: size_slug => price_html
                $size_price_map = [];
                foreach ( $all_variation_ids as $var_id ) {
                    $v = wc_get_product( $var_id );
                    if ( ! $v ) continue;
                    $v_attrs = $v->get_variation_attributes();
                    $slug    = $v_attrs[ 'attribute_' . $size_attr_key ] ?? '';
                    if ( $slug === '' ) continue;
                    // Only store if not already set (prefer in-stock variation price)
                    if ( ! isset( $size_price_map[ $slug ] ) || ( $v->is_in_stock() && $v->is_purchasable() ) ) {
                        $size_price_map[ $slug ] = $v->get_price_html();
                    }
                }
                ?>
                <div class="gt-sp-size-options" id="gtSizeOptions"
                     data-prices="<?php echo esc_attr( wp_json_encode( $size_price_map ) ); ?>">
                    <?php foreach ( $size_options as $size_slug ) :
                        $size_term    = get_term_by( 'slug', $size_slug, 'pa_size' );
                        $size_label   = $size_term ? $size_term->name : $size_slug;
                        $in_stock     = $size_stock_map[ $size_slug ] ?? false;
                    ?>
                    <button type="button"
                            class="gt-size-opt<?php echo ! $in_stock ? ' gt-size-opt--oos' : ''; ?>"
                            data-size="<?php echo esc_attr( $size_slug ); ?>"
                            data-attr="attribute_<?php echo esc_attr( $size_attr_key ); ?>"
                            data-in-stock="<?php echo $in_stock ? '1' : '0'; ?>"
                            <?php if ( ! $in_stock ) : ?>aria-disabled="true" title="Out of stock"<?php endif; ?>>
                        <span class="gt-size-opt-label"><?php echo esc_html( $size_label ); ?></span>
                        <?php if ( ! $in_stock ) : ?>
                        <span class="gt-size-oos-line" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <g clip-path="url(#clip0_282_3)">
                                    <path d="M1 1L19 19" stroke="#EF4444" stroke-width="3" stroke-linecap="round"/>
                                    <path d="M19 1L1 19" stroke="#EF4444" stroke-width="3" stroke-linecap="round"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_282_3">
                                        <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="gt-size-chart-btn" id="gtSizeChartBtn" aria-haspopup="dialog">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="16" height="10" rx="2"/><path d="M6 5v10M10 5v10M14 5v10"/></svg> Size Chart
                </button>
                <?php endif; ?>

                <?php if ( ! empty( $color_options ) ) : ?>
                <!-- Color swatches -->
                <div class="gt-sp-color-header">
                    <span class="gt-sp-size-label">Color: <strong id="gtSelectedColorLabel"></strong></span>
                </div>
                <div class="gt-sp-color-options" id="gtColorOptions">
                    <?php foreach ( $color_options as $color_slug ) :
                        $color_term  = get_term_by( 'slug', $color_slug, $color_attr_key === 'pa_color' ? 'pa_color' : ( $color_attr_key === 'pa_colour' ? 'pa_colour' : $color_attr_key ) );
                        $color_label = $color_term ? $color_term->name : $color_slug;
                        $color_hex   = get_term_meta( $color_term ? $color_term->term_id : 0, 'product_attribute_color', true );
                        $in_stock    = $color_stock_map[ $color_slug ] ?? false;
                    ?>
                    <button type="button"
                            class="gt-color-swatch<?php echo ! $in_stock ? ' gt-color-swatch--oos' : ''; ?>"
                            data-color="<?php echo esc_attr( $color_slug ); ?>"
                            data-attr="attribute_<?php echo esc_attr( $color_attr_key ); ?>"
                            data-in-stock="<?php echo $in_stock ? '1' : '0'; ?>"
                            title="<?php echo esc_attr( $color_label . ( ! $in_stock ? ' (Out of stock)' : '' ) ); ?>"
                            <?php if ( ! $in_stock ) : ?>aria-disabled="true"<?php endif; ?>>
                        <?php if ( $color_hex ) : ?>
                            <span class="gt-color-swatch-inner" style="background:<?php echo esc_attr( $color_hex ); ?>"></span>
                        <?php else : ?>
                            <span class="gt-color-swatch-label"><?php echo esc_html( $color_label ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! $in_stock ) : ?>
                        <span class="gt-size-oos-line" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" preserveAspectRatio="none">
                                <line x1="2" y1="2" x2="38" y2="38" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
                                <line x1="38" y1="2" x2="2" y2="38" stroke="#ef4444" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- WC hidden form (variations) — we override the UI above -->
                <div class="gt-sp-wc-form">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                </div>

                <!-- Quantity + Add to Cart row -->
                <div class="gt-sp-atc-row">
                    <div class="gt-qty-wrap">
                        <button type="button" class="gt-qty-btn" id="gtQtyMinus" aria-label="Decrease quantity">&#8722;</button>
                        <input type="number" class="gt-qty-input" id="gtQtyInput" value="1" min="1" max="99" readonly>
                        <button type="button" class="gt-qty-btn" id="gtQtyPlus" aria-label="Increase quantity">&#43;</button>
                    </div>
                    <button type="button" class="gt-add-to-cart-btn " id="gtAddToCart"
                            data-is-variable="<?php echo $product->is_type('variable') ? '1' : '0'; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        Add to Cart
                    </button>
                </div>

                <!-- Buy Now -->
                <button type="button" class="gt-buy-now-btn" id="gtBuyNow"
                        data-is-variable="<?php echo $product->is_type('variable') ? '1' : '0'; ?>">Buy Now</button>

            </div><!-- /.gt-sp-form -->

            <!-- Accordions -->
        <div class="gt-sp-accordions">

            <div class="gt-accordion">
                <button class="gt-accordion-trigger" aria-expanded="true">
                    Product Description
                    <svg class="gt-accordion-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 8l5 5 5-5"/></svg>
                </button>
                <div class="gt-accordion-panel is-open">
                    <div class="gt-accordion-body">
                        <?php
                        $desc  = $product->get_description();
                        $short = $product->get_short_description();
                        echo wp_kses_post( $desc ?: $short ?: '<p>No description available.</p>' );
                        ?>
                    </div>
                </div>
            </div>

            <div class="gt-accordion">
                <button class="gt-accordion-trigger" aria-expanded="false">
                    Returns &amp; Exchange Information
                    <svg class="gt-accordion-icon" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 8l5 5 5-5"/></svg>
                </button>
                <div class="gt-accordion-panel">
                    <div class="gt-accordion-body">
                        <ul>
                            <li>Easy returns within <strong> 7 days</strong> of delivery.</li>
                            <li>Item must be unused, unwashed, and in original packaging.</li>
                            <li>Exchange is subject to stock availability.</li>
                            <li>Contact us via WhatsApp or email to initiate a return.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div><!-- /.gt-sp-accordions -->

        </div><!-- /.gt-sp-summary -->
    </div><!-- /.gt-sp-layout -->

    <!-- ── Bottom: Accordions + Related ───────────────────────── -->
    <div class="gt-sp-bottom">

        <!-- Related Products -->
        <?php
        $related_ids = wc_get_related_products( get_the_ID(), 8 );
        if ( $related_ids ) :
            $related_query = new WP_Query( [
                'post_type'      => 'product',
                'post__in'       => $related_ids,
                'posts_per_page' => 8,
                'orderby'        => 'rand',
            ] );
        ?>
        <section class="gt-sp-related">
            <h2 class="gt-sp-section-title">Related Products</h2>
            <div class="gt-sp-related-grid">
                <?php while ( $related_query->have_posts() ) : $related_query->the_post();
                    wc_get_template_part( 'content', 'product' );
                endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- /.gt-sp-bottom -->

</div><!-- /.container -->

<!-- Size Chart Modal -->
<div class="gt-size-modal" id="gtSizeModal" role="dialog" aria-modal="true" aria-labelledby="gtSizeModalTitle" hidden>
    <div class="gt-size-modal-backdrop" id="gtSizeModalBackdrop"></div>
    <div class="gt-size-modal-box">
        <div class="gt-size-modal-header">
            <h3 id="gtSizeModalTitle">Size Chart</h3>
            <button class="gt-size-modal-close" id="gtSizeModalClose" aria-label="Close">&times;</button>
        </div>
        <div class="gt-size-modal-body">
            <div class="gt-size-modal-note">
                <?php
                    $desc  = $product->get_description();
                    $short = $product->get_short_description();
                    echo wp_kses_post( $desc ?: $short ?: '<p>No description available.</p>' ); ?>
            </div>
            
            <div class="gt-size-table-wrap">
                <p class="gt-size-table-title">
                    Shirt Size Chart
                </p>
                <table class="gt-size-table">
                    <thead>
                        <tr><th>Size</th><th>Chest</th><th>Length</th><th>Sleeve</th><th>Collar</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>S</td><td>40</td><td>27</td><td>24</td><td>15.5</td></tr>
                        <tr><td>M</td><td>42</td><td>28</td><td>24.5</td><td>15.5</td></tr>
                        <tr><td>L</td><td>44</td><td>29</td><td>25</td><td>16.5</td></tr>
                        <tr><td>XL</td><td>46</td><td>30</td><td>25.5</td><td>16.5</td></tr>
                        <tr><td>XXL</td><td>48</td><td>30.5</td><td>26</td><td>17.5</td></tr>
                        <tr><td>3XL</td><td>50</td><td>31</td><td>26.5</td><td>18</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php endwhile; ?>

<!-- Zoom result panel: position:fixed, appended near body end -->
<div class="gt-sp-zoom-result" id="gtSpZoomResult"></div>

<?php get_footer(); ?>
