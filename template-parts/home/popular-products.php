<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="gt-popular-products">
    <div class="container">
        <h2 class="section_title"> Popular Products </h2>
        <div class="products_wrapper woocommerce" id="popular-products-grid">
            <?php
            $args = [
                'post_type' => 'product',
                'posts_per_page' => 12,
                'orderby' => 'meta_value_num',
                'meta_key' => 'total_sales',
                'order' => 'DESC',
            ];
            $query = new WP_Query($args);
            if ($query->have_posts()):
                woocommerce_product_loop_start();
                while ($query->have_posts()):
                    $query->the_post();
                    wc_get_template_part('content-product', 'product');
                endwhile;
                woocommerce_product_loop_end();
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <div class="load-more-wrapper">
            <button id="load-more-products" data-page="2">Load More</button>
        </div>
    </div>
</section>