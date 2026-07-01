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

        if (is_shop() || is_product_category() || is_product_tag()) {
            wp_enqueue_script(
                'gentstime-shop',
                get_stylesheet_directory_uri() . '/assets/js/shop.js',
                [],
                GENTSTIME_VERSION,
                true
            );
            wp_localize_script('gentstime-shop', 'shopAjax', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('shop_ajax_nonce'),
            ]);
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