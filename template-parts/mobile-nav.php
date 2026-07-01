<?php
/**
 * Template Part: Mobile Bottom Navigation Bar
 * Visible only on mobile (≤ 767 px) via CSS.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cart_count = function_exists('WC') ? WC()->cart->get_cart_contents_count() : 0;

$nav_items = [
    [
        'label' => 'Shop',
        'url' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop'),
        'active' => is_shop() || is_product_category() || is_product_tag(),
        'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
    ],
    [
        'label' => 'Contact',
        'url' => home_url('/contact'),
        'active' => is_page('contact'),
        'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
    ],
    [
        'label' => 'Home',
        'url' => home_url('/'),
        'active' => is_front_page() || is_home(),
        'highlight' => true,
        'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M9 21V12h6v9" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ],
    [
        'label' => 'Cart',
        'url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/cart'),
        'active' => is_cart() || is_checkout(),
        'badge' => $cart_count,
        'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/><line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M16 10a4 4 0 0 1-8 0" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
    ],
    [
        'label' => 'Login',
        'url' => function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : wp_login_url(),
        'active' => is_account_page(),
        'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
    ],
];
?>

<nav class="gt-mobile-nav" aria-label="<?php esc_attr_e('Mobile navigation', 'gentstime'); ?>">
    <ul class="gt-mobile-nav__list container">
        <?php foreach ($nav_items as $item):
            $is_highlight = !empty($item['highlight']);
            $is_active = !empty($item['active']);
            $item_class = 'gt-mobile-nav__item' . ($is_highlight ? ' gt-mobile-nav__item--highlight' : '');
            $link_class = 'gt-mobile-nav__link' . ($is_active ? ' active' : '');
            ?>
            <li class="<?php echo esc_attr($item_class); ?>">
                <a href="<?php echo esc_url($item['url']); ?>" class="<?php echo esc_attr($link_class); ?>"
                    aria-label="<?php echo esc_attr($item['label']); ?>">
                    <span class="gt-mobile-nav__icon" aria-hidden="true">
                        <?php echo $item['icon']; // SVG is safe — no user input ?>
                    </span>
                    <?php if (isset($item['badge'])): ?>
                        <span class="gt-mobile-nav__badge" data-cart-count-badge <?php echo empty($item['badge']) ? 'style="display:none"' : ''; ?>>
                            <?php echo esc_html($item['badge']); ?>
                        </span>
                    <?php endif; ?>
                    <span><?php echo esc_html($item['label']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>