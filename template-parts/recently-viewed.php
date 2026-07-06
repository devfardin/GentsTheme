<?php
/**
 * Recently Viewed Products Component
 * Usage: get_template_part( 'template-parts/recently-viewed' );
 */
defined('ABSPATH') || exit;

$viewed_ids = gt_get_recently_viewed();

if (!empty($viewed_ids)):
    $query = new WP_Query([
        'post_type' => 'product',
        'post__in' => $viewed_ids,
        'posts_per_page' => count($viewed_ids),
        'orderby' => 'post__in',
        'post_status' => 'publish',
    ]);
    ?>
    <div class="container">
        <div class="products_wrapper woocommerce">
            <?php while ($query->have_posts()):
                $query->the_post();
                wc_get_template_part('content', 'product');
            endwhile;
            wp_reset_postdata(); ?>
        </div>
    </div>


<?php else: ?>
    <div class="container">
        <div class="gt-rv-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <p>You haven't viewed any products yet.</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gt-btn">Browse Products</a>
        </div>
    </div>
<?php endif; ?>