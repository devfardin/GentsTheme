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
?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <h1 class="breadcrumb-title"><?php the_title(); ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <?php
            $terms = get_the_terms( get_the_ID(), 'product_cat' );
            if ( $terms && ! is_wp_error( $terms ) ) :
                $filtered = array_filter( $terms, fn( $t ) => $t->slug !== 'uncategorized' );
                $cat      = $filtered ? reset( $filtered ) : reset( $terms );
            ?>
                <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
                <span class="breadcrumb-sep" aria-hidden="true">&#8250;</span>
            <?php endif; ?>
            <span class="breadcrumb-current" aria-current="page"><?php the_title(); ?></span>
        </nav>
    </div>
</div>

<div class="container gt-single-product">
    <div class="gt-sp-layout">

        <!-- Gallery -->
        <div class="gt-sp-gallery">
            <?php woocommerce_show_product_images(); ?>
        </div>

        <!-- Summary -->
        <div class="gt-sp-summary">

            <?php
            // Category
            if ( ! empty( $cat ) ) : ?>
                <span class="gt-sp-cat">
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
                </span>
            <?php endif; ?>

            <h1 class="gt-sp-title"><?php the_title(); ?></h1>

            <!-- Rating -->
            <?php if ( $product->get_rating_count() > 0 ) : ?>
                <div class="gt-sp-rating">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <svg viewBox="0 0 24 24" class="gt-star<?php echo $i <= round( (float) $product->get_average_rating() ) ? ' gt-star--on' : ''; ?>">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>
                        </svg>
                    <?php endfor; ?>
                    <span class="gt-sp-rating-count">(<?php echo esc_html( $product->get_rating_count() ); ?> reviews)</span>
                </div>
            <?php endif; ?>

            <!-- Price -->
            <div class="gt-sp-price"><?php echo $product->get_price_html(); ?></div>

            <!-- Short Description -->
            <?php if ( $product->get_short_description() ) : ?>
                <div class="gt-sp-short-desc"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
            <?php endif; ?>

            <!-- Add to Cart Form -->
            <div class="gt-sp-form">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>

            <!-- Meta (SKU / Categories) -->
            <div class="gt-sp-meta">
                <?php woocommerce_template_single_meta(); ?>
            </div>

        </div><!-- /.gt-sp-summary -->
    </div><!-- /.gt-sp-layout -->

    <!-- Tabs (Description, Reviews, etc.) -->
    <div class="gt-sp-tabs">
        <?php woocommerce_output_product_data_tabs(); ?>
    </div>

    <!-- Related Products -->
    <?php woocommerce_output_related_products(); ?>

</div><!-- /.container -->

<?php endwhile; ?>

<?php get_footer(); ?>
