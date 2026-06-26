<?php
/**
 * GentsTime — Custom Mini Cart Template
 * Overrides: woocommerce/templates/cart/mini-cart.php
 */
defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart');
?>

<?php if (WC()->cart && !WC()->cart->is_empty()): ?>

    <ul class="gt-mini-cart-list">
        <?php
        do_action('woocommerce_before_mini_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0)
                continue;
            if (!apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key))
                continue;

            $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
            $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
            $remove_url = esc_url(wc_get_cart_remove_url($cart_item_key));
            ?>
            <li class="gt-mini-cart-item woocommerce-mini-cart-item mini_cart_item">

                <!-- Product image -->
                <div class="gt-cart-item-img">
                    <?php if ($product_permalink): ?>
                        <a href="<?php echo esc_url($product_permalink); ?>" tabindex="-1">
                            <?php echo $thumbnail; ?>
                        </a>
                    <?php else: ?>
                        <?php echo $thumbnail; ?>
                    <?php endif; ?>
                </div>

                <!-- Product details -->
                <div class="gt-cart-item-details">
                    <div class="gt-cart-item-top">
                        <?php if ($product_permalink): ?>
                            <a class="gt-cart-item-name" href="<?php echo esc_url($product_permalink); ?>">
                                <?php echo wp_kses_post($product_name); ?>
                            </a>
                        <?php else: ?>
                            <span class="gt-cart-item-name"><?php echo wp_kses_post($product_name); ?></span>
                        <?php endif; ?>

                        <!-- Remove button -->
                        <a role="button" href="<?php echo $remove_url; ?>" class="gt-cart-remove remove remove_from_cart_button"
                            aria-label="<?php echo esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))); ?>"
                            data-product_id="<?php echo esc_attr($product_id); ?>"
                            data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>"
                            data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"
                                fill="currentColor">
                                <path
                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                        </a>
                    </div>

                    <!-- Variation data (size, colour, etc.) -->
                    <?php echo wc_get_formatted_cart_item_data($cart_item); ?>

                    <!-- Qty × Price -->
                    <div class="gt-cart-item-meta">
                        <span class="gt-cart-item-qty"><?php echo esc_html($cart_item['quantity']); ?></span>
                        <span class="gt-cart-item-sep">×</span>
                        <span class="gt-cart-item-price"><?php echo $product_price; ?></span>
                    </div>
                </div>

            </li>
            <?php
        endforeach;
        do_action('woocommerce_mini_cart_contents');
        ?>
    </ul>

    <!-- Subtotal -->
    <div class="gt-mini-cart-subtotal">
        <span class="gt-subtotal-label"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
        <span class="gt-subtotal-value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
    </div>

<?php else: ?>

    <div class="gt-mini-cart-empty">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
        </svg>
        <p><?php esc_html_e('Your cart is empty', 'woocommerce'); ?></p>
        <a href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"
            class="gt-cart-shop-link">
            <?php esc_html_e('Browse products', 'woocommerce'); ?>
        </a>
    </div>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>