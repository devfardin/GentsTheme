<?php
/**
 * GentsTime Theme Functions
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Security Constants
define('GENTSTIME_VERSION', '1.1.0');
define('GENTSTIME_DIR', __DIR__ . '/includes/');
define('GENTSTIME_SHORTCODE_DIR', __DIR__ . '/includes/shortcodes/');

class GentsTimeFunctions {
    public function __construct() {
        $this->load_dependencies();
        $this->init();
    }
    
    public function load_dependencies() {
        require_once( GENTSTIME_DIR . 'enqueue.php');
        require_once( GENTSTIME_DIR . 'header-functions.php');
    }
    
    public function init() {
        new GentsTimeAssets();
    }
}

new GentsTimeFunctions();

// AJAX: Load more popular products
add_action('wp_ajax_load_more_products', 'gentstime_load_more_products');
add_action('wp_ajax_nopriv_load_more_products', 'gentstime_load_more_products');

function gentstime_load_more_products() {
    check_ajax_referer('load_more_products_nonce', 'nonce');

    $page = max(1, intval($_POST['page'] ?? 1));

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'paged'          => $page,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'total_sales',
        'order'          => 'DESC',
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        wp_send_json_success(['html' => '', 'has_more' => false]);
    }

    ob_start();
    woocommerce_product_loop_start();
    while ($query->have_posts()) : $query->the_post();
        wc_get_template_part('content', 'product');
    endwhile;
    woocommerce_product_loop_end();
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success([
        'html'     => $html,
        'has_more' => $page < $query->max_num_pages,
    ]);
}