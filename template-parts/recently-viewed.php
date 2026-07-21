<?php
/**
 * Recently Viewed Products Component
 * Usage: get_template_part( 'template-parts/recently-viewed' );
 */
defined('ABSPATH') || exit;

$viewed_ids = gt_get_recently_viewed();


    $query = new WP_Query([
        'post_type' => 'product',
        'post__in' => $viewed_ids,
        'posts_per_page' => count($viewed_ids),
        'orderby' => 'post__in',
        'post_status' => 'publish',
    ]);
    ?>
    <section class="gt-recently-viewed">
        <div class="container">
            <!-- section title -->
            <div class="gt-section-title__wrap">
                <h2 class="section_title"> Recently Viewed </h2>
            </div>
           <?php if (!empty($viewed_ids)): ?>
            <div class="slider-arrow_wrap">
                <button class="button-prev">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 17 17"
                        height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <g></g>
                        <path d="M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z">
                        </path>
                    </svg>
                </button>
                <button class="button-next">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" version="1.1" viewBox="0 0 17 17"
                        height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <g></g>
                        <path d="M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z">
                        </path>
                    </svg>
                </button>

            </div>
            <!-- Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php while ($query->have_posts()):
                        $query->the_post(); ?>
                        <div class="swiper-slide">
                            <?php wc_get_template_part('content', 'product'); ?>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            </div>

        <?php else: ?>
            <div class="gt-rv-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <p>You haven't viewed any products yet.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gt-btn">Browse Products</a>
            </div>
        <?php endif; ?>

    </div>
</section>