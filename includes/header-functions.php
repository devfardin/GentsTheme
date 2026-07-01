<?php
/**
 * Header Functions - AJAX Search and Menu Registration
 */

if (!defined('ABSPATH')) {
    exit;
}

class GentsTimeHeader
{
    public function __construct()
    {
        // Register menu
        add_action('after_setup_theme', [$this, 'register_menus']);

        // AJAX search for logged in and non-logged in users
        add_action('wp_ajax_gentstime_product_search', [$this, 'ajax_product_search']);
        add_action('wp_ajax_nopriv_gentstime_product_search', [$this, 'ajax_product_search']);

        // AJAX cart fragments (badge + mini-cart refresh)
        add_action('wp_ajax_gentstime_get_cart_fragments', [$this, 'ajax_get_cart_fragments']);
        add_action('wp_ajax_nopriv_gentstime_get_cart_fragments', [$this, 'ajax_get_cart_fragments']);

        // AJAX remove cart item
        add_action('wp_ajax_gentstime_remove_cart_item', [$this, 'ajax_remove_cart_item']);
        add_action('wp_ajax_nopriv_gentstime_remove_cart_item', [$this, 'ajax_remove_cart_item']);
    }

    /**
     * Register navigation menu
     */
    public function register_menus()
    {
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'gentstime'),
        ));
    }

    /**
     * AJAX Product Search Handler
     */
    public function ajax_product_search()
    {
        // Verify nonce
        check_ajax_referer('header_search_nonce', 'nonce');

        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

        if (empty($query)) {
            wp_send_json_error();
        }

        // WooCommerce product search
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 8,
            's' => $query,
            'post_status' => 'publish',
        );

        $search_query = new WP_Query($args);
        $products = array();

        if ($search_query->have_posts()) {
            while ($search_query->have_posts()) {
                $search_query->the_post();
                $product_id = get_the_ID();
                $product = wc_get_product($product_id);

                // Get product image
                $image_id = $product->get_image_id();
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src();

                $products[] = array(
                    'name' => get_the_title(),
                    'url' => get_permalink(),
                    'image' => $image_url,
                    'price' => $product->get_price_html(),
                );
            }
            wp_reset_postdata();
        }

        if (empty($products)) {
            wp_send_json_error();
        }

        wp_send_json_success($products);
    }

    /**
     * AJAX — return fresh badge count + mini-cart HTML + in-cart product IDs
     */
    public function ajax_get_cart_fragments()
    {
        check_ajax_referer('gentstime_cart_nonce', 'nonce');

        if ( ! function_exists('WC') || ! WC()->cart ) {
            wp_send_json_error('Cart unavailable');
        }

        ob_start();
        woocommerce_mini_cart();
        $mini_cart_html = ob_get_clean();

        wp_send_json_success(array(
            'count'       => (int) WC()->cart->get_cart_contents_count(),
            'mini_cart'   => $mini_cart_html,
            'product_ids' => self::get_cart_product_ids(),
        ));
    }

    /**
     * AJAX — remove a single item from cart then return fragments
     */
    public function ajax_remove_cart_item()
    {
        check_ajax_referer('gentstime_cart_nonce', 'nonce');

        if ( ! function_exists('WC') || ! WC()->cart ) {
            wp_send_json_error('Cart unavailable');
        }

        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_key($_POST['cart_item_key']) : '';

        if ( empty($cart_item_key) ) {
            wp_send_json_error('Missing cart item key');
        }

        /* Confirm the key actually exists in this session's cart */
        if ( ! WC()->cart->get_cart_item($cart_item_key) ) {
            wp_send_json_error('Invalid cart item key');
        }

        WC()->cart->remove_cart_item($cart_item_key);

        ob_start();
        woocommerce_mini_cart();
        $mini_cart_html = ob_get_clean();

        wp_send_json_success(array(
            'count'       => (int) WC()->cart->get_cart_contents_count(),
            'mini_cart'   => $mini_cart_html,
            'product_ids' => self::get_cart_product_ids(),
        ));
    }

    /**
     * Return array of product IDs currently in the cart
     */
    public static function get_cart_product_ids()
    {
        if (!function_exists('WC') || !WC()->cart) return [];
        return array_values(array_unique(
            array_map(fn($item) => (int) $item['product_id'], WC()->cart->get_cart())
        ));
    }

    /**
     * Get cart count
     */
    public static function get_cart_count()
    {
        if (function_exists('WC')) {
            return WC()->cart->get_cart_contents_count();
        }
        return 0;
    }
}

// Initialize
new GentsTimeHeader();
