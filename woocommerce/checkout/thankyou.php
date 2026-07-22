<?php
/**
 * GentsTime — Thank You Page
 * Override: yourtheme/woocommerce/checkout/thankyou.php
 *
 * @var WC_Order $order
 */
defined('ABSPATH') || exit;
?>

<div class="woocommerce-order">
<?php if ($order): ?>

    <?php if ($order->has_status('failed')): ?>
        <p class="woocommerce-notice woocommerce-notice--error">
            <?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?>
        </p>
        <p>
            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
        </p>
    <?php else: ?>

    <?php
    do_action('woocommerce_before_thankyou', $order->get_id());

    $full_name      = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
    $phone          = $order->get_billing_phone();
    $email          = $order->get_billing_email();
    $address        = implode(', ', array_filter([$order->get_billing_address_1(), $order->get_billing_city(), $order->get_billing_state()]));
    $order_date     = wc_format_datetime($order->get_date_created());
    $order_number   = $order->get_order_number();
    $order_status   = wc_get_order_status_name($order->get_status());
    $items          = $order->get_items();
    $subtotal       = $order->get_subtotal();
    $shipping       = (float) $order->get_shipping_total();
    $discount       = (float) $order->get_discount_total();
    $total          = $order->get_total();
    $payment_method = $order->get_payment_method_title();


    $gent_general = get_option('gent_general');
    ?>

    <div class="gt-ty-wrap" id="gt-thankyou-page">
        <div class="container">

            <!-- ── Header ── -->
            <div class="gt-ty-header">
                <div class="gt-ty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                </div>
                <h1 class="gt-ty-title">Thank You!</h1>
                <p class="gt-ty-subtitle">Your order has been confirmed</p>
                <p class="gt-ty-greeting">
                    Dear <?php echo esc_html($full_name ?: 'Valued Customer'); ?>,<br>
                    We have successfully received your order and it is now being <?php echo esc_html($order_status); ?>. Our team is preparing your order with care, and one of our customer representatives will contact you shortly to confirm the details. We appreciate your trust in us and look forward to delivering your order as quickly as possible.
                </p>
            </div>

            <!-- ── Order Meta Bar ── -->
            <div class="gt-ty-meta-bar">
                <div class="gt-ty-meta-item">
                    <span class="gt-ty-meta-label">Order Number</span>
                    <span class="gt-ty-meta-val">#<?php echo esc_html($order_number); ?></span>
                </div>
                <div class="gt-ty-meta-item">
                    <span class="gt-ty-meta-label">Date</span>
                    <span class="gt-ty-meta-val"><?php echo esc_html($order_date); ?></span>
                </div>
                <div class="gt-ty-meta-item">
                    <span class="gt-ty-meta-label">Status</span>
                    <span class="gt-ty-meta-val gt-ty-status"><?php echo esc_html($order_status); ?></span>
                </div>
                <div class="gt-ty-meta-item">
                    <span class="gt-ty-meta-label">Payment</span>
                    <span class="gt-ty-meta-val"><?php echo esc_html($payment_method); ?></span>
                </div>
            </div>

            <div class="gt-ty-body">

                <!-- ── Order Items + Summary ── -->
                <div class="gt-ty-card">
                    <h2 class="gt-ty-card-heading">Order Items</h2>

                    <div class="gt-ty-items-head">
                        <span>Product</span>
                        <span>Price</span>
                        <span>Qty</span>
                        <span>Subtotal</span>
                    </div>

                    <?php foreach ($items as $item_id => $item):
                        $product   = $item->get_product();
                        $img       = $product ? $product->get_image('thumbnail') : '';
                        $name      = $item->get_name();
                        $qty       = $item->get_quantity();
                        $line_sub  = wc_price($item->get_subtotal());
                        $unit_p    = $product ? wc_price($product->get_price()) : wc_price($item->get_subtotal() / max(1, $qty));
                        $meta_data = $item->get_formatted_meta_data('_', true);
                    ?>
                    <div class="gt-ty-item-row">
                        <div class="gt-ty-item-product">
                            <?php if ($img): ?>
                                <div class="gt-ty-item-img"><?php echo $img; ?></div>
                            <?php endif; ?>
                            <div class="gt-ty-item-info">
                                <p class="gt-ty-item-name"><?php echo esc_html($name); ?></p>
                                <?php foreach ($meta_data as $meta): ?>
                                    <span class="gt-ty-item-var"><?php echo wp_kses_post($meta->display_key); ?>: <?php echo wp_kses_post($meta->display_value); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="gt-ty-item-price"><?php echo $unit_p; ?></div>
                        <div class="gt-ty-item-qty">× <?php echo esc_html($qty); ?></div>
                        <div class="gt-ty-item-sub"><?php echo $line_sub; ?></div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Order Summary -->
                    <div class="gt-ty-summary">
                        <div class="gt-ty-sum-row">
                            <span>Subtotal</span>
                            <span><?php echo wc_price($subtotal); ?></span>
                        </div>
                        <div class="gt-ty-sum-row">
                            <span>Delivery</span>
                            <span><?php echo $shipping > 0 ? wc_price($shipping) : '<span class="gt-ty-free">Free</span>'; ?></span>
                        </div>
                        <?php if ($discount > 0): ?>
                        <div class="gt-ty-sum-row gt-ty-discount">
                            <span>Discount</span>
                            <span>-<?php echo wc_price($discount); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="gt-ty-sum-sep"></div>
                        <div class="gt-ty-sum-row gt-ty-sum-total">
                            <span>Total</span>
                            <span><?php echo wc_price($total); ?></span>
                        </div>
                    </div>
                </div>

                <!-- ── Billing Address ── -->
                <div class="gt-ty-card">
                    <h2 class="gt-ty-card-heading">Billing Address</h2>
                    <div class="gt-ty-address">
                        <?php if ($full_name): ?><p><strong><?php echo esc_html($full_name); ?></strong></p><?php endif; ?>
                        <?php if ($phone): ?><p>📞 <?php echo esc_html($phone); ?></p><?php endif; ?>
                        <?php if ($email): ?><p>✉️ <?php echo esc_html($email); ?></p><?php endif; ?>
                        <?php if ($address): ?><p>📍 <?php echo esc_html($address); ?></p><?php endif; ?>
                    </div>
                </div>

            </div><!-- /gt-ty-body -->

            <!-- ── Thank You Message ── -->
            <div class="gt-ty-message">
                <p>🙏 Thank you for shopping with <strong>Gents Time</strong>! We truly appreciate your trust in us. If you have any questions about your order, feel free to reach out to our team — we're always happy to help.</p>
            </div>

            <!-- ── Contact Info ── -->
            <div class="gt-ty-contact">
                <h3 class="gt-ty-contact-heading">Need Help?</h3>
                <div class="gt-ty-contact-grid">
                    <?php  if (!empty($gent_general['phone_number'])): ?>
                    <div class="gt-ty-contact-item">
                        <span class="gt-ty-contact-icon">📞</span>
                        <div>
                            <p class="gt-ty-contact-label">Phone / WhatsApp</p>
                            <a href="tel:<?php echo esc_attr($gent_general['phone_number']); ?>" class="gt-ty-contact-val">
                                <?php echo esc_html($gent_general['phone_number']); ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($gent_general['company_email']): ?>
                    <div class="gt-ty-contact-item">
                        <span class="gt-ty-contact-icon">✉️</span>
                        <div>
                            <p class="gt-ty-contact-label">Email</p>
                            <a href="mailto:<?php echo esc_attr($gent_general['company_email']); ?>" class="gt-ty-contact-val">
                                <?php echo esc_html($gent_general['company_email']); ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="gt-ty-contact-item">
                        <span class="gt-ty-contact-icon">🕐</span>
                        <div>
                            <p class="gt-ty-contact-label">Support Hours</p>
                            <p class="gt-ty-contact-val">Sat – Thu, 10am – 8pm</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Action Buttons ── -->
            <div class="gt-ty-actions">
                <button type="button" class="gt-ty-btn gt-ty-btn-outline" onclick="window.print()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print Details
                </button>
                <button type="button" class="gt-ty-btn gt-ty-btn-primary" id="gt-download-invoice"
                    data-order="<?php echo esc_attr($order->get_id()); ?>"
                    data-nonce="<?php echo esc_attr(wp_create_nonce('gt_invoice_' . $order->get_id())); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Invoice (PDF)
                </button>

                <a href="<?php echo esc_url(site_url( "order-tracking/?phone=$phone")); ?>" class="gt-ty-btn gt-ty-btn-ghost">
                   Track Order 
                </a>
            </div>

        </div><!-- /container -->
    </div><!-- /gt-ty-wrap -->

    <script>
    document.getElementById('gt-download-invoice')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Generating…';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo esc_url(admin_url('admin-post.php')); ?>';
        [['action','gt_download_invoice'],['order_id', btn.dataset.order],['nonce', btn.dataset.nonce]].forEach(([n,v]) => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = n; i.value = v;
            form.appendChild(i);
        });
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download Invoice (PDF)`;
        }, 3000);
    });
    </script>

    <?php endif; ?>

<?php endif; ?>
</div>