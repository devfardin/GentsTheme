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

// ══════════════════════════════════════════════════════════════════
// Recently Viewed Products
// ══════════════════════════════════════════════════════════════════

/**
 * Track product view via cookie (no login required).
 */
function gt_track_recently_viewed( $product_id ) {
    $viewed = gt_get_recently_viewed();
    $viewed = array_filter( $viewed, function( $id ) use ( $product_id ) { return $id !== $product_id; } );
    array_unshift( $viewed, $product_id );
    $viewed = array_slice( $viewed, 0, 20 );
    setcookie( 'gt_recently_viewed', implode( ',', $viewed ), time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), false );
}

/**
 * Get recently viewed product IDs from cookie.
 */
function gt_get_recently_viewed() {
    if ( empty( $_COOKIE['gt_recently_viewed'] ) ) return [];
    return array_map( 'absint', array_filter( explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['gt_recently_viewed'] ) ) ) ) );
}

// Track on single product page load
add_action( 'template_redirect', function () {
    if ( is_product() ) {
        gt_track_recently_viewed( get_queried_object_id() );
    }
} );

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
add_action( 'template_redirect', function () {

    if ( ! is_cart() ) {
        return;
    }
    if ( WC()->cart && WC()->cart->is_empty() ) {
        wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
        exit;
    }
    wp_safe_redirect( wc_get_checkout_url() );
    exit;

} );

// AJAX: Shop filter + load more
add_action('wp_ajax_shop_ajax_filter', 'gentstime_shop_ajax_filter');
add_action('wp_ajax_nopriv_shop_ajax_filter', 'gentstime_shop_ajax_filter');

function gentstime_shop_ajax_filter()
{
    check_ajax_referer('shop_ajax_nonce', 'nonce');

    $page        = max(1, absint($_POST['page'] ?? 1));
    $orderby     = sanitize_key($_POST['orderby'] ?? 'menu_order');
    $min_p       = isset($_POST['min_price']) ? floatval($_POST['min_price']) : '';
    $max_p       = isset($_POST['max_price']) ? floatval($_POST['max_price']) : '';
    $cat_slug    = sanitize_text_field($_POST['cat'] ?? '');
    $size        = sanitize_text_field($_POST['size'] ?? '');
    $new_arrivals = ! empty($_POST['new_arrivals']) && '1' === $_POST['new_arrivals'];

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
            'value' => array_filter([$min_p, $max_p], function($v) { return $v !== ''; }),
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
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $page,
        'tax_query'      => $tax_query ?: [],
        'meta_query'     => $meta_query ?: [],
    ], $order_args);

    if ($new_arrivals) {
        $args['date_query'] = [[
            'after'     => '30 days ago',
            'inclusive' => true,
        ]];
    }

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
        'product_ids'   => class_exists('GentsTimeHeader') ? GentsTimeHeader::get_cart_product_ids() :
            array_values(array_unique(array_map(function($i) { return (int) $i['product_id']; }, $cart->get_cart()))),
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

/* ══════════════════════════════════════════════════════════════════
   PDF Invoice Download
   ══════════════════════════════════════════════════════════════════ */
add_action('admin_post_gt_download_invoice', 'gt_download_invoice');
add_action('admin_post_nopriv_gt_download_invoice', 'gt_download_invoice');

function gt_download_invoice() {
    $order_id = absint($_POST['order_id'] ?? 0);
    if (!$order_id || !wp_verify_nonce($_POST['nonce'] ?? '', 'gt_invoice_' . $order_id)) {
        wp_die('Invalid request.');
    }

    $order = wc_get_order($order_id);
    if (!$order) wp_die('Order not found.');

    $full_name  = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
    $phone      = $order->get_billing_phone();
    $email      = $order->get_billing_email();
    $address    = implode(', ', array_filter([$order->get_billing_address_1(), $order->get_billing_city(), $order->get_billing_state()]));
    $date       = wc_format_datetime($order->get_date_created());
    $subtotal   = strip_tags(wc_price($order->get_subtotal()));
    $shipping   = (float) $order->get_shipping_total();
    $discount   = (float) $order->get_discount_total();
    $total      = strip_tags(wc_price($order->get_total()));
    $payment    = $order->get_payment_method_title();
    $status     = wc_get_order_status_name($order->get_status());
    $currency   = get_woocommerce_currency_symbol();

    // Build HTML for PDF
    ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #171717; margin: 0; padding: 30px; }
    h1 { font-size: 22px; margin: 0 0 4px; color: #00A486; }
    .sub { font-size: 13px; color: #737373; margin: 0 0 24px; }
    .meta { display: flex; gap: 40px; margin-bottom: 24px; }
    .meta-item label { font-size: 10px; text-transform: uppercase; color: #737373; display: block; margin-bottom: 2px; }
    .meta-item span  { font-weight: 700; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { background: #E5F7F4; padding: 9px 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #737373; }
    td { padding: 10px; border-bottom: 1px solid #E5E5E5; font-size: 13px; }
    .total-row td { font-weight: 800; font-size: 15px; border-top: 2px solid #E5E5E5; border-bottom: none; }
    .address-box { background: #FAFAFA; border: 1px solid #E5E5E5; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; }
    .address-box p { margin: 0 0 6px; font-size: 13px; }
    .footer-note { text-align: center; font-size: 12px; color: #737373; margin-top: 30px; border-top: 1px solid #E5E5E5; padding-top: 16px; }
    .brand { color: #00A486; font-weight: 700; }
</style>
</head>
<body>
<h1>Gents Time</h1>
<p class="sub">Invoice — Order #<?php echo esc_html($order->get_order_number()); ?></p>

<table style="width:100%;margin-bottom:20px;border-collapse:collapse">
<tr>
<td style="width:50%;vertical-align:top;padding:0">
    <div class="address-box">
        <p><strong>Bill To:</strong></p>
        <p><?php echo esc_html($full_name); ?></p>
        <?php if ($phone): ?><p>📞 <?php echo esc_html($phone); ?></p><?php endif; ?>
        <?php if ($email): ?><p>✉️ <?php echo esc_html($email); ?></p><?php endif; ?>
        <?php if ($address): ?><p>📍 <?php echo esc_html($address); ?></p><?php endif; ?>
    </div>
</td>
<td style="width:50%;vertical-align:top;padding:0 0 0 16px">
    <div class="address-box">
        <p><strong>Order Details:</strong></p>
        <p>Date: <?php echo esc_html($date); ?></p>
        <p>Status: <?php echo esc_html($status); ?></p>
        <p>Payment: <?php echo esc_html($payment); ?></p>
    </div>
</td>
</tr>
</table>

<table>
    <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($order->get_items() as $item):
        $product  = $item->get_product();
        $unit_p   = $product ? strip_tags(wc_price($product->get_price())) : strip_tags(wc_price($item->get_subtotal() / max(1, $item->get_quantity())));
        $line_sub = strip_tags(wc_price($item->get_subtotal()));
        $metas    = $item->get_formatted_meta_data('_', true);
        $meta_str = '';
        foreach ($metas as $m) $meta_str .= ' | ' . strip_tags($m->display_key) . ': ' . strip_tags($m->display_value);
    ?>
    <tr>
        <td><?php echo esc_html($item->get_name() . $meta_str); ?></td>
        <td><?php echo esc_html($item->get_quantity()); ?></td>
        <td><?php echo esc_html($unit_p); ?></td>
        <td><?php echo esc_html($line_sub); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><td colspan="3">Subtotal</td><td><?php echo esc_html($subtotal); ?></td></tr>
        <tr><td colspan="3">Delivery</td><td><?php echo $shipping > 0 ? esc_html(strip_tags(wc_price($shipping))) : 'Free'; ?></td></tr>
        <?php if ($discount > 0): ?>
        <tr><td colspan="3">Discount</td><td>-<?php echo esc_html(strip_tags(wc_price($discount))); ?></td></tr>
        <?php endif; ?>
        <tr class="total-row"><td colspan="3">Total</td><td><?php echo esc_html($total); ?></td></tr>
    </tfoot>
</table>

<div class="footer-note">
    Thank you for shopping with <span class="brand">Gents Time</span>!<br>
    Questions? Call +880 1316-049157 or email support@gentstime.com
</div>
</body>
</html>
<?php
    $html = ob_get_clean();

    // Use Dompdf if available, otherwise fallback to plain HTML download
    $dompdf_path = WP_CONTENT_DIR . '/plugins/gent/vendor/dompdf/dompdf/autoload.inc.php';
    if (file_exists($dompdf_path)) {
        require_once $dompdf_path;
        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice-' . $order_id . '.pdf"');
        echo $dompdf->output();
    } else {
        // Fallback: deliver as HTML (browser can print-to-PDF)
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="invoice-' . $order_id . '.html"');
        echo $html;
    }
    exit;
}

/* ── Adjust WooCommerce checkout field requirements ── */
add_filter('woocommerce_checkout_fields', function ($fields) {
    // Make email optional
    $fields['billing']['billing_email']['required'] = false;

    // Remove last name requirement and rename first name label to Full Name
    $fields['billing']['billing_last_name']['required'] = false;
    $fields['billing']['billing_first_name']['label']   = __('Full Name', 'gentstime');

    return $fields;
});

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

/* ── Remove "No shipping method has been selected" error ── */
add_filter('woocommerce_checkout_no_shipping_available_html', '__return_empty_string');
add_filter('woocommerce_no_shipping_available_html', '__return_empty_string');
add_action('woocommerce_after_checkout_validation', function ($data, $errors) {
    $notices = $errors->get_error_messages();
    foreach ($notices as $key => $message) {
        if (strpos($message, 'No shipping method') !== false) {
            $errors->remove('shipping');
        }
    }
}, 10, 2);

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
