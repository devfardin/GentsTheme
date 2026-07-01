<?php
/**
 * GentsTime Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GENTSTIME_VERSION', '1.1.0');
define('GENTSTIME_DIR', __DIR__ . '/includes/');
define('GENTSTIME_SHORTCODE_DIR', __DIR__ . '/includes/shortcodes/');

class GentsTimeFunctions
{

    public function __construct()
    {
        $this->load_dependencies();
        $this->init();
        add_action('after_setup_theme', [$this, 'theme_setup']);
    }

    public function theme_setup()
    {
        // WooCommerce support
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');

    }

    public function load_dependencies()
    {
        require_once(GENTSTIME_DIR . 'enqueue.php');
        require_once(GENTSTIME_DIR . 'header-functions.php');
        require_once(GENTSTIME_DIR . 'shipping.php');
    }

    public function init()
    {
        new GentsTimeAssets();
    }
}

new GentsTimeFunctions();

// ── Single product: remove duplicate default hooks ──────────────────────────
add_action('wp', function () {
    if (!is_product())
        return;
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
});

// Dequeue select2 CSS that WC re-enqueues late on checkout/account pages.
// Priority 200 ensures it fires after WC's own enqueue.
add_action('wp_print_styles', function () {
    wp_dequeue_style('select2');
    wp_deregister_style('select2');
}, 200);

// Redirect cart page to checkout (merged page)
add_action('template_redirect', function () {
    if (is_cart()) {
        wp_safe_redirect(wc_get_checkout_url(), 301);
        exit;
        if(is_checkout() || WC()->cart->empty_cart()){
            wp_safe_redirect(get_permalink(home_url('/shop')), 301);
            exit;
        }
    }
});

// AJAX: Shop filter + load more
add_action('wp_ajax_shop_ajax_filter', 'gentstime_shop_ajax_filter');
add_action('wp_ajax_nopriv_shop_ajax_filter', 'gentstime_shop_ajax_filter');

function gentstime_shop_ajax_filter()
{
    check_ajax_referer('shop_ajax_nonce', 'nonce');

    $page = max(1, absint($_POST['page'] ?? 1));
    $orderby = sanitize_key($_POST['orderby'] ?? 'menu_order');
    $min_p = isset($_POST['min_price']) ? floatval($_POST['min_price']) : '';
    $max_p = isset($_POST['max_price']) ? floatval($_POST['max_price']) : '';
    $cat_slug = sanitize_text_field($_POST['cat'] ?? '');
    $size = sanitize_text_field($_POST['size'] ?? '');

    $tax_query = [];
    $meta_query = [];

    if ($cat_slug) {
        $tax_query[] = ['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat_slug];
    }

    if ($size) {
        $tax_query[] = ['taxonomy' => 'pa_size', 'field' => 'slug', 'terms' => $size];
    }

    if ($min_p !== '' || $max_p !== '') {
        $meta_query[] = [
            'key' => '_price',
            'value' => array_filter([$min_p, $max_p], fn($v) => $v !== ''),
            'compare' => ($min_p !== '' && $max_p !== '') ? 'BETWEEN' : ($min_p !== '' ? '>=' : '<='),
            'type' => 'NUMERIC',
        ];
    }

    $order_map = [
        'popularity' => ['orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC'],
        'rating' => ['orderby' => 'meta_value_num', 'meta_key' => '_wc_average_rating', 'order' => 'DESC'],
        'date' => ['orderby' => 'date', 'order' => 'DESC'],
        'price' => ['orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'ASC'],
        'price-desc' => ['orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'DESC'],
        'menu_order' => ['orderby' => 'menu_order title', 'order' => 'ASC'],
    ];

    $order_args = $order_map[$orderby] ?? $order_map['menu_order'];

    $args = array_merge([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => $page,
        'tax_query' => $tax_query ?: [],
        'meta_query' => $meta_query ?: [],
    ], $order_args);

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        woocommerce_product_loop_start();
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
        woocommerce_product_loop_end();
    } else {
        echo '<div class="gt-no-products"><h3>No products found</h3><p>Try adjusting your filters.</p></div>';
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success([
        'html' => $html,
        'has_more' => $page < $query->max_num_pages,
        'found' => (int) $query->found_posts,
        'page' => $page,
    ]);
}

/* ══════════════════════════════════════════════
   GentsTime — Checkout AJAX helpers
   ══════════════════════════════════════════════ */

/**
 * Shared helper: returns updated totals + header fragment payload.
 */
function gt_cart_totals_payload()
{
    WC()->cart->calculate_totals();
    $cart          = WC()->cart;
    $shipping_cost = (float) $cart->get_shipping_total();
    $district      = WC()->session ? WC()->session->get('billing_district', '') : '';

    if ($shipping_cost > 0) {
        $shipping_html = wc_price($shipping_cost);
    } elseif ($district) {
        // District is known but cost is 0 — genuinely free
        $shipping_html = '<span class="gt-free-delivery">Free</span>';
    } else {
        // No district selected yet
        $shipping_html = '<span class="gt-zone-pending">Select zone to calculate</span>';
    }

    $coupons = [];
    foreach ($cart->get_coupons() as $code => $coupon_obj) {
        $coupons[] = [
            'code'     => strtoupper($code),
            'discount' => '-' . wc_price($cart->get_coupon_discount_amount($code, $cart->display_cart_ex_tax)),
        ];
    }

    // Build mini-cart HTML for header sidebar
    ob_start();
    woocommerce_mini_cart();
    $mini_cart_html = ob_get_clean();

    return [
        'subtotal'      => wc_price($cart->get_subtotal()),
        'shipping'      => $shipping_html,
        'total'         => wc_price($cart->get_total('raw')),
        'coupons'       => $coupons,
        'cart_is_empty' => $cart->is_empty(),
        'cart_count'    => (int) $cart->get_cart_contents_count(),
        'mini_cart'     => $mini_cart_html,
        'product_ids'   => function_exists('GentsTimeHeader') ? GentsTimeHeader::get_cart_product_ids() :
            array_values(array_unique(array_map(fn($i) => (int) $i['product_id'], $cart->get_cart()))),
    ];
}

/* ── 1. Update cart item quantity ── */
add_action('wp_ajax_gt_update_cart_item', 'gt_update_cart_item');
add_action('wp_ajax_nopriv_gt_update_cart_item', 'gt_update_cart_item');

function gt_update_cart_item()
{
    check_ajax_referer('gt_cart_nonce', 'nonce');

    $cart_item_key = sanitize_text_field(wp_unslash($_POST['cart_item_key'] ?? ''));
    $qty = absint($_POST['qty'] ?? 1);

    if (!$cart_item_key) {
        wp_send_json_error(['message' => 'Invalid cart item.']);
    }

    WC()->cart->set_quantity($cart_item_key, max(1, $qty), true);

    $cart_items = WC()->cart->get_cart();
    $item_subtotal = '';
    if (isset($cart_items[$cart_item_key])) {
        $product = $cart_items[$cart_item_key]['data'];
        $item_subtotal = WC()->cart->get_product_subtotal($product, $cart_items[$cart_item_key]['quantity']);
    }

    wp_send_json_success(array_merge(
        ['message' => 'Cart updated.', 'item_subtotal' => $item_subtotal],
        gt_cart_totals_payload()
    ));
}

/* ── 2. Remove cart item ── */
add_action('wp_ajax_gt_remove_cart_item', 'gt_remove_cart_item');
add_action('wp_ajax_nopriv_gt_remove_cart_item', 'gt_remove_cart_item');

function gt_remove_cart_item()
{
    check_ajax_referer('gt_cart_nonce', 'nonce');

    $cart_item_key = sanitize_text_field(wp_unslash($_POST['cart_item_key'] ?? ''));

    if (!$cart_item_key) {
        wp_send_json_error(['message' => 'Invalid cart item.']);
    }

    WC()->cart->remove_cart_item($cart_item_key);

    wp_send_json_success(array_merge(
        ['message' => 'Item removed from cart.'],
        gt_cart_totals_payload()
    ));
}

/* ── 3. Apply coupon ── */
add_action('wp_ajax_gt_apply_coupon', 'gt_apply_coupon_ajax');
add_action('wp_ajax_nopriv_gt_apply_coupon', 'gt_apply_coupon_ajax');

function gt_apply_coupon_ajax()
{
    check_ajax_referer('gt_cart_nonce', 'nonce');

    $code = sanitize_text_field(wp_unslash($_POST['coupon_code'] ?? ''));

    if (!$code) {
        wp_send_json_error(['message' => 'Please enter a coupon code.']);
    }

    if (WC()->cart->has_discount($code)) {
        wp_send_json_error(['message' => 'Coupon "' . esc_html(strtoupper($code)) . '" is already applied.']);
    }

    $coupon = new WC_Coupon($code);
    if (!$coupon->get_id()) {
        wp_send_json_error(['message' => 'Invalid coupon code. Please try again.']);
    }

    WC()->session->set('wc_notices', []);
    $result = WC()->cart->apply_coupon($code);

    if ($result) {
        wp_send_json_success(array_merge(
            ['message' => 'Coupon "' . esc_html(strtoupper($code)) . '" applied successfully!'],
            gt_cart_totals_payload()
        ));
    } else {
        $notices = wc_get_notices('error');
        $msg = !empty($notices) ? wp_strip_all_tags($notices[0]['notice']) : 'Coupon could not be applied.';
        wc_clear_notices();
        wp_send_json_error(['message' => $msg]);
    }
}

/* ── 4. Remove coupon ── */
add_action('wp_ajax_gt_remove_coupon', 'gt_remove_coupon_ajax');
add_action('wp_ajax_nopriv_gt_remove_coupon', 'gt_remove_coupon_ajax');

function gt_remove_coupon_ajax()
{
    check_ajax_referer('gt_cart_nonce', 'nonce');

    $code = sanitize_text_field(wp_unslash($_POST['coupon_code'] ?? ''));

    if (!$code) {
        wp_send_json_error(['message' => 'No coupon specified.']);
    }

    WC()->cart->remove_coupon($code);

    wp_send_json_success(array_merge(
        ['message' => 'Coupon "' . esc_html(strtoupper($code)) . '" removed.'],
        gt_cart_totals_payload()
    ));
}

/* ── Remove WC's built-in BD state list so our custom districts pass validation ── */
add_filter('woocommerce_states', function ($states) {
    if (isset($states['BD'])) {
        unset($states['BD']);
    }
    return $states;
});

/* ── Suppress default WC notice HTML on checkout — we show toasts instead ── */
add_action('wp', function () {
    if (is_checkout()) {
        remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
    }
});

/* ── Copy billing_district into billing_state before WC processes the order ── */
add_action('woocommerce_checkout_process', function () {
    if (!empty($_POST['billing_district'])) {
        $_POST['billing_state'] = sanitize_text_field(wp_unslash($_POST['billing_district']));
    }

    // Validate Bangladesh phone number: 01XXXXXXXXX (11 digits, operator digit 3-9)
    $phone = isset($_POST['billing_phone']) ? trim(wp_unslash($_POST['billing_phone'])) : '';
    // Strip any spaces or dashes the user may have typed
    $phone = preg_replace('/[\s\-]/', '', $phone);
    if ($phone === '' || !preg_match('/^01[3-9]\d{8}$/', $phone)) {
        wc_add_notice(__('Please enter a valid Bangladesh phone number (e.g. 01316049157).', 'gentstime'), 'error');
    } else {
        // Write back the cleaned number so WC saves it without spaces
        $_POST['billing_phone'] = $phone;
    }
});

/* ── 5. Update shipping district ── */
add_action('wp_ajax_gt_update_shipping_district', 'gt_update_shipping_district');
add_action('wp_ajax_nopriv_gt_update_shipping_district', 'gt_update_shipping_district');

function gt_update_shipping_district()
{
    check_ajax_referer('gt_cart_nonce', 'nonce');

    $district = sanitize_text_field(wp_unslash($_POST['billing_district'] ?? ''));

    if (!$district) {
        wp_send_json_error(['message' => 'No district provided.']);
    }

    // Save to session so the shipping method can read it
    WC()->session->set('billing_district', $district);

    // Also set WC customer state so packages get the right destination
    WC()->customer->set_billing_state($district);
    WC()->customer->set_shipping_state($district);

    // Clear cached shipping rates so WC is forced to recalculate
    WC()->shipping()->reset_shipping();

    // Recalculate everything fresh
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();

    wp_send_json_success(gt_cart_totals_payload());
}
