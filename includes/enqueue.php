<?php
/**
 * GentsTime Assets Enqueue
 */

if (!defined('ABSPATH')) {
    exit;
}
class GentsTimeAssets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_styles()
    {
       
        wp_enqueue_style(
            'gentstime-main',
            get_stylesheet_directory_uri() . '/assets/css/main.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );

        wp_enqueue_style(
            'gentstime-header',
            get_stylesheet_directory_uri() . '/assets/css/header.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );

        wp_enqueue_style(
            'gentstime-containder',
            get_stylesheet_directory_uri() . '/assets/css/container.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-hero-slider',
            get_stylesheet_directory_uri() . '/assets/css/heroslider.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-footer',
            get_stylesheet_directory_uri() . '/assets/css/footer.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );

        wp_enqueue_style(
            'gentstime-woocommerce',
            get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-categories',
            get_stylesheet_directory_uri() . '/assets/css/categories.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-single-product',
            get_stylesheet_directory_uri() . '/assets/css/single-product.css',
            [],
            GENTSTIME_VERSION,
            'all'
        ); 

        wp_enqueue_style(
            'gentstime-mobile-nav',
            get_stylesheet_directory_uri() . '/assets/css/mobile-nav.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-extend',
            get_stylesheet_directory_uri() . '/assets/css/extend.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'content-product',
            get_stylesheet_directory_uri() . '/assets/css/content-procuct.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );
        wp_enqueue_style(
            'gentstime-cta',
            get_stylesheet_directory_uri() . '/assets/css/cta.css',
            [],
            GENTSTIME_VERSION,
            'all'
        );

            // Swiper CDN
            wp_enqueue_style(
                'swiper-CDN',
                '//cdn.jsdelivr.net/npm/swiper@14.0.1/swiper-bundle.min.css',
                [],
                GENTSTIME_VERSION,
                'all'
            );
             // Swiper CDN
            wp_enqueue_style(
                'swiper-main',
                get_stylesheet_directory_uri() . '/assets/css/swiper.css',
                [],
                GENTSTIME_VERSION,
                'all'
            );

            wp_enqueue_style(
                'gentstime-recently-viewed',
                get_stylesheet_directory_uri() . '/assets/css/recently-viewed.css',
                [],
                GENTSTIME_VERSION,
                'all'
            );
     

        if (is_page('about-us') || is_page('about')) {
            wp_enqueue_style(
                'gentstime-about',
                get_stylesheet_directory_uri() . '/assets/css/about.css',
                [],
                GENTSTIME_VERSION,
                'all'
            );
        }

        if (is_checkout() || is_cart()) {
            wp_enqueue_style(
                'gentstime-checkout',
                get_stylesheet_directory_uri() . '/assets/css/checkout.css',
                [],
                GENTSTIME_VERSION,
                'all'
            );
        }
    }

    public function enqueue_scripts()
    {
        // swiper js
        
        wp_enqueue_script(
            'swiper-cdn',
            '//cdn.jsdelivr.net/npm/swiper@14.0.1/swiper-bundle.min.js',
            [],
            GENTSTIME_VERSION,
            true
        );
         wp_enqueue_script(
            'swiper-main',
            get_stylesheet_directory_uri() . '/assets/js/swiper.js',
            ['swiper-cdn'],
            GENTSTIME_VERSION,
            true
        );
        
        wp_enqueue_script(
            'gentstime-main',
            get_stylesheet_directory_uri() . '/assets/js/main.js',
            [],
            GENTSTIME_VERSION,
            true
        );

        wp_enqueue_script(
            'gentstime-header',
            get_stylesheet_directory_uri() . '/assets/js/header.js',
            [],
            GENTSTIME_VERSION,
            true
        );
        wp_localize_script('gentstime-header', 'headerAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('header_search_nonce'),
            'cart_nonce' => wp_create_nonce('gentstime_cart_nonce'),
            'cart_ids' => function_exists('WC') ? GentsTimeHeader::get_cart_product_ids() : [],
        ]);

        if ( is_product() ) {
            wp_enqueue_script(
                'gentstime-single-product',
                get_stylesheet_directory_uri() . '/assets/js/single-product.js',
                [],
                GENTSTIME_VERSION,
                true
            );
        }

        if (is_shop() || is_product_category() || is_product_tag() || is_page('new-arrivals')) {
            wp_enqueue_script(
                'gentstime-shop',
                get_stylesheet_directory_uri() . '/assets/js/shop.js',
                [],
                GENTSTIME_VERSION,
                true
            );

            global $wpdb;
            $price_row = $wpdb->get_row("SELECT MIN(CAST(meta_value AS DECIMAL(10,2))) as mn, MAX(CAST(meta_value AS DECIMAL(10,2))) as mx FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''");
            $price_min = $price_row ? (int) floor((float) $price_row->mn) : 0;
            $price_max = $price_row ? (int) ceil((float) $price_row->mx)  : 10000;

            $current_term = is_tax() ? get_queried_object() : null;
            wp_localize_script('gentstime-shop', 'gtShopData', [
                'ajaxurl'     => admin_url('admin-ajax.php'),
                'nonce'       => wp_create_nonce('shop_ajax_nonce'),
                'initCat'     => $current_term ? $current_term->slug : '',
                'priceMin'    => $price_min,
                'priceMax'    => $price_max,
                'newArrivals' => is_page('new-arrivals') ? 1 : 0,
            ]);
        }

        if ( is_product() ) {
            wp_enqueue_script(
                'gentstime-single-product',
                get_stylesheet_directory_uri() . '/assets/js/single-product.js',
                [ 'jquery' ],
                GENTSTIME_VERSION,
                true
            );
        }

        if (is_checkout() || is_cart()) {
            wp_enqueue_script(
                'gentstime-bd-locations',
                get_stylesheet_directory_uri() . '/assets/js/bd-locations.js',
                [],
                GENTSTIME_VERSION,
                true
            );
            wp_enqueue_script(
                'gentstime-checkout',
                get_stylesheet_directory_uri() . '/assets/js/checkout.js',
                ['gentstime-bd-locations'],
                GENTSTIME_VERSION,
                true
            );
            wp_localize_script('gentstime-checkout', 'gtCheckout', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('gt_cart_nonce'),
                'notices' => array_values(array_map(function ($n) {
                    return [
                        'type'    => $n['notice_type'] ?? 'error',
                        'message' => wp_strip_all_tags($n['notice']),
                    ];
                }, wc_get_notices())),
            ]);
            // Clear so WC doesn't also render them as default HTML
            wc_clear_notices();
        }
    }
}