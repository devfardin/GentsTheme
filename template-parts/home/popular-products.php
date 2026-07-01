<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gt-popular-products">
    <div class="container">
        <h2 class="section_title"> Popular Products </h2>
        <div class="products_wrapper woocommerce">
            <?php
            $args = [
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'orderby'        => 'meta_value_num',
                'meta_key'       => 'total_sales',
                'order'          => 'DESC',
                'post_status'    => 'publish',
            ];
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
                woocommerce_product_loop_start();
                while ( $query->have_posts() ) :
                    $query->the_post();
                    wc_get_template_part( 'content', 'product' );
                endwhile;
                woocommerce_product_loop_end();
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>