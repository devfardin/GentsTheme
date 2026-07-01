<?php
/**
 * GentsTime — Merged Cart + Checkout Page
 */
if (!defined('ABSPATH'))
    exit;

$checkout = WC()->checkout();
?>

<div class="gt-checkout-wrap">

    <?php if (WC()->cart->is_empty()): ?>
        <div class="gt-empty-cart">
            <div class="container">
                <p><?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?></p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gt-btn-continue">Continue Shopping</a>
            </div>
        </div>
    <?php else: ?>

        <div class="gt-checkout-body">
            <div class="container">

                <form name="checkout" method="post" class="gt-checkout-form checkout woocommerce-checkout"
                    action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

                    <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>

                    <div class="gt-checkout-grid">

                        <!-- ══════════════════════════════
                             LEFT — Cart Items + Customer Info + Delivery
                             ══════════════════════════════ -->
                        <div class="gt-col-left">

                            <!-- ─── Cart Items ─── -->
                            <div class="gt-card">
                                <h2 class="gt-card-heading">Cart Items</h2>

                                <div class="gt-cart-table">
                                    <div class="gt-cart-table-head">
                                        <span>Product</span>
                                        <span>Price</span>
                                        <span>Quantity</span>
                                        <span>Subtotal</span>
                                        <span></span>
                                    </div>

                                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):
                                        $product = $cart_item['data'];
                                        if (!$product || !$product->exists() || $cart_item['quantity'] === 0) continue;
                                        $product_id   = $cart_item['product_id'];
                                        $quantity     = $cart_item['quantity'];
                                        $unit_price   = wc_price($product->get_price());
                                        $subtotal     = WC()->cart->get_product_subtotal($product, $quantity);
                                        $remove_url   = wc_get_cart_remove_url($cart_item_key);
                                    ?>
                                        <div class="gt-cart-row" data-key="<?php echo esc_attr($cart_item_key); ?>">

                                            <!-- Product image + title -->
                                            <div class="gt-cart-product">
                                                <div class="gt-cart-img">
                                                    <?php echo $product->get_image('thumbnail'); ?>
                                                </div>
                                                <div class="gt-cart-meta">
                                                    <p class="gt-cart-title"><?php echo esc_html($product->get_name()); ?></p>
                                                    <?php if (!empty($cart_item['variation'])):
                                                        foreach ($cart_item['variation'] as $attr => $val): ?>
                                                            <span class="gt-cart-var"><?php echo esc_html(wc_attribute_label(str_replace('attribute_', '', $attr))); ?>: <?php echo esc_html($val); ?></span>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </div>
                                            </div>

                                            <!-- Unit price -->
                                            <div class="gt-cart-price">
                                                <?php echo $unit_price; ?>
                                            </div>

                                            <!-- Quantity update -->
                                            <div class="gt-cart-qty">
                                                <button type="button" class="gt-qty-btn gt-qty-minus" data-key="<?php echo esc_attr($cart_item_key); ?>">&#8722;</button>
                                                <input type="number"
                                                    name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]"
                                                    class="gt-qty-input"
                                                    value="<?php echo esc_attr($quantity); ?>"
                                                    min="1"
                                                    data-key="<?php echo esc_attr($cart_item_key); ?>">
                                                <button type="button" class="gt-qty-btn gt-qty-plus" data-key="<?php echo esc_attr($cart_item_key); ?>">&#43;</button>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="gt-cart-subtotal">
                                                <?php echo $subtotal; ?>
                                            </div>

                                            <!-- Remove -->
                                            <div class="gt-cart-remove">
                                                <button type="button" class="gt-remove-btn"
                                                    data-key="<?php echo esc_attr($cart_item_key); ?>">Remove</button>
                                            </div>

                                        </div>
                                    <?php endforeach; ?>
                                </div><!-- /gt-cart-table -->

                                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gt-continue-btn">
                                    Continue Shopping
                                </a>
                            </div><!-- /Cart Items -->


                            <!-- ─── Customer Information ─── -->
                            <div class="gt-card">
                                <h2 class="gt-card-heading">Customer Information</h2>

                                <div class="gt-fields-grid">

                                    <div class="gt-field-wrap gt-field-full">
                                        <label for="billing_full_name">Full Name <span class="required">*</span></label>
                                        <input type="text"
                                            id="billing_full_name"
                                            name="billing_full_name"
                                            class="gt-input"
                                            placeholder="Enter your full name"
                                            value="<?php echo esc_attr($checkout->get_value('billing_first_name') . ' ' . $checkout->get_value('billing_last_name')); ?>"
                                            required>
                                        <!-- Hidden fields WooCommerce needs -->
                                        <input type="hidden" name="billing_first_name" id="billing_first_name_hidden">
                                        <input type="hidden" name="billing_last_name"  id="billing_last_name_hidden">
                                    </div>

                                    <div class="gt-field-wrap">
                                        <label for="billing_phone">Phone Number <span class="required">*</span></label>
                                        <input type="tel"
                                            id="billing_phone"
                                            name="billing_phone"
                                            class="gt-input"
                                            placeholder="Enter your phone number"
                                            value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>"
                                            required>
                                    </div>

                                    <div class="gt-field-wrap">
                                        <label for="billing_email">Email Address <span class="gt-optional">(Optional)</span></label>
                                        <input type="email"
                                            id="billing_email"
                                            name="billing_email"
                                            class="gt-input"
                                            placeholder="Enter your email address"
                                            value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>">
                                    </div>

                                </div>
                            </div><!-- /Customer Information -->


                            <!-- ─── Delivery Location ─── -->
                            <div class="gt-card">
                                <h2 class="gt-card-heading">Delivery Location</h2>

                                <div class="gt-fields-grid">

                                    <div class="gt-field-wrap">
                                        <label for="billing_district">District <span class="required">*</span></label>
                                        <div class="gt-select-wrap">
                                            <select id="billing_district"
                                                name="billing_district"
                                                class="gt-input gt-select"
                                                required>
                                                <option value="">Select your district</option>
                                            </select>
                                            <span class="gt-select-arrow">&#8964;</span>
                                        </div>
                                        <input type="hidden" name="billing_state" id="billing_state_hidden"
                                            value="<?php echo esc_attr($checkout->get_value('billing_state')); ?>">
                                    </div>

                                    <div class="gt-field-wrap">
                                        <label for="billing_thana">Thana <span class="required">*</span></label>
                                        <div class="gt-select-wrap">
                                            <select id="billing_thana"
                                                name="billing_thana"
                                                class="gt-input gt-select"
                                                required
                                                disabled>
                                                <option value="">Select district first</option>
                                            </select>
                                            <span class="gt-select-arrow">&#8964;</span>
                                        </div>
                                        <input type="hidden" name="billing_city" id="billing_city_hidden"
                                            value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>">
                                        <div class="gt-delivery-charge-hint" id="gt-delivery-charge-hint" style="display:none;">
                                            <span class="gt-dch-label">Delivery charge:</span>
                                            <span class="gt-dch-cost" id="gt-dch-cost"></span>
                                        </div>
                                    </div>

                                    <div class="gt-field-wrap gt-field-full">
                                        <label for="billing_address_1">Full Address <span class="required">*</span></label>
                                        <textarea
                                            id="billing_address_1"
                                            name="billing_address_1"
                                            class="gt-input gt-textarea"
                                            placeholder="House / Road / Area"
                                            rows="3"
                                            required><?php echo esc_textarea($checkout->get_value('billing_address_1')); ?></textarea>
                                    </div>

                                    <div class="gt-field-wrap gt-field-full">
                                        <label for="order_comments">Order Notes <span class="gt-optional">(Optional)</span></label>
                                        <textarea
                                            id="order_comments"
                                            name="order_comments"
                                            class="gt-input gt-textarea"
                                            placeholder="Any special instructions for your order..."
                                            rows="3"><?php echo esc_textarea($checkout->get_value('order_comments')); ?></textarea>
                                    </div>

                                </div>

                                <!-- Hidden required WooCommerce fields -->
                                <input type="hidden" name="billing_country"  value="BD">
                                <!-- <input type="hidden" name="billing_postcode" value="0000"> -->
                                <input type="hidden" name="billing_address_2" value="">

                            </div><!-- /Delivery Location -->

                        </div><!-- /gt-col-left -->


                        <!-- ══════════════════════════════
                             RIGHT — Order Summary + Payment
                             ══════════════════════════════ -->
                        <div class="gt-col-right">

                            <!-- ─── Order Summary ─── -->
                            <div class="gt-card">
                                <h2 class="gt-card-heading">Order Summary</h2>

                                <div class="gt-summary-row">
                                    <span>Subtotal</span>
                                    <span class="gt-summary-val" id="gt-summary-subtotal"><?php echo wc_price(WC()->cart->get_subtotal()); ?></span>
                                </div>

                                <div class="gt-summary-row">
                                    <span>Delivery Cost</span>
                                    <span class="gt-summary-val" id="gt-summary-shipping">
                                        <?php
                                        $saved_district = $checkout->get_value('billing_state');
                                        if ($saved_district && WC()->cart->get_shipping_total() > 0):
                                            echo wc_price(WC()->cart->get_shipping_total());
                                        elseif ($saved_district):
                                            echo '<span class="gt-free-delivery">Free</span>';
                                        else:
                                            echo '<span class="gt-zone-pending">Select zone to calculate</span>';
                                        endif;
                                        ?>
                                    </span>
                                </div>

                                <div id="gt-discount-rows">
                                <?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
                                    <div class="gt-summary-row gt-discount" data-coupon="<?php echo esc_attr($code); ?>">
                                        <span>
                                            Coupon (<?php echo esc_html(strtoupper($code)); ?>)
                                            <button type="button" class="gt-remove-coupon-btn" data-code="<?php echo esc_attr($code); ?>" title="Remove coupon">&#10005;</button>
                                        </span>
                                        <span class="gt-summary-val gt-discount-val">-<?php echo wc_price(WC()->cart->get_coupon_discount_amount($code)); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                </div>

                                <div class="gt-summary-sep"></div>

                                <div class="gt-summary-row gt-total">
                                    <span>Total</span>
                                    <span class="gt-summary-val gt-total-val" id="gt-summary-total"><?php echo wc_price(WC()->cart->get_total('raw')); ?></span>
                                </div>

                                <!-- Coupon -->
                                <div class="gt-coupon-row">
                                    <input type="text"
                                        id="gt_coupon_code"
                                        class="gt-coupon-input"
                                        placeholder="Enter Coupon Code"
                                        autocomplete="off">
                                    <button type="button" class="gt-coupon-btn" id="gt-apply-coupon">Apply</button>
                                </div>
                                <div class="gt-coupon-message" id="gt-coupon-message"></div>

                            </div><!-- /Order Summary -->


                            <!-- ─── Payment Method ─── -->
                            <div class="gt-card">
                                <h2 class="gt-card-heading">Payment Method</h2>

                                <?php
                                $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
                                $chosen_gateway     = WC()->session ? WC()->session->get('chosen_payment_method') : '';
                                if (!$chosen_gateway && !empty($available_gateways)) {
                                    $chosen_gateway = array_key_first($available_gateways);
                                }

                                ?>

                                

                                <div class="gt-payment-methods">
                                    <?php foreach ($available_gateways as $gateway_id => $gateway): ?>
                                        <label class="gt-payment-option">
                                            <input type="radio"
                                                name="payment_method"
                                                value="<?php echo esc_attr($gateway_id); ?>"
                                                class="gt-payment-radio"
                                                <?php checked($gateway_id, $chosen_gateway); ?>>
                                            <span class="gt-payment-label">
                                                <?php if ($gateway->get_icon()): ?>
                                                    <span class="gt-payment-icon"><?php echo $gateway->get_icon(); ?></span>
                                                <?php endif; ?>
                                                <?php echo esc_html($gateway->get_title()); ?>
                                                <?php if ($gateway->get_description()): ?>
                                                    <small class="gt-payment-desc"><?php echo wp_kses_post($gateway->get_description()); ?></small>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <?php do_action('woocommerce_review_order_before_submit'); ?>

                                <!-- Place Order Button -->
                                <button type="submit"
                                    class="gt-place-order-btn alt btn-primary"
                                    name="woocommerce_checkout_place_order"
                                    id="place_order"
                                    value="Place order"
                                    data-value="Place order">
                                    Confirm Order
                                </button>

                                <p class="gt-secure-text">Your information is safe &amp; secure</p>

                            </div><!-- /Payment Method -->

                            <?php if (wc_get_page_id('terms') > 0): ?>
                                <div class="gt-terms">
                                    <?php wc_get_template('checkout/terms.php'); ?>
                                </div>
                            <?php endif; ?>

                        </div><!-- /gt-col-right -->

                    </div><!-- /gt-checkout-grid -->

                    <?php do_action('woocommerce_checkout_after_order_review'); ?>

                </form>

            </div><!-- /container -->
        </div><!-- /gt-checkout-body -->

    <?php endif; ?>

</div><!-- /gt-checkout-wrap -->

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>