<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts: Playfair Display (brand) + Inter (UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- =============================================
         TOP BAR — black, centered promotional notice
         ============================================= -->
    <div class="header-topbar">
        <div class="topbar-inner">
            <span class="topbar-notice">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9 1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                Free shipping on orders over <strong>৳999</strong> &nbsp;·&nbsp; Use code <strong>GENTS10</strong> for 10% off
            </span>
        </div>
    </div>

    <!-- =============================================
         MAIN HEADER
         ============================================= -->
    <header id="masthead" class="site-header">
        <div class="header-container">

            <!-- LOGO -->
            <div class="header-logo">
                <?php
                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '">';
                    echo '<span class="logo-brand">' . get_bloginfo('name') . '</span>';
                    echo '</a>';
                }
                ?>
            </div>

            <!-- DESKTOP NAVIGATION -->
            <nav class="header-nav" aria-label="Primary navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>

            <!-- DESKTOP SEARCH BAR -->
            <div class="header-search">
                <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    
                    <input type="text" class="search-input" placeholder="Search for products..." name="s" autocomplete="off">
                    <div class="search-results" aria-live="polite"></div>
                </form>
            </div>

            <!-- ACTION ICONS — Cart, WhatsApp, Account -->
            <div class="header-icons">

                <!-- Cart -->
                <a href="<?php echo function_exists('wc_get_cart_url') ? esc_url(wc_get_cart_url()) : '#'; ?>" class="header-icon-btn" aria-label="Shopping cart">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    <?php if (function_exists('WC') && WC()->cart->get_cart_contents_count() > 0) : ?>
                        <span class="cart-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    <?php endif; ?>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/1234567890" target="_blank" rel="noopener noreferrer" class="header-icon-btn whatsapp-btn" aria-label="WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>

                <!-- Account -->
                <a href="<?php echo function_exists('wc_get_account_endpoint_url') ? esc_url(wc_get_account_endpoint_url('dashboard')) : wp_login_url(); ?>" class="header-icon-btn" aria-label="My account">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </a>

            </div>

            <!-- HAMBURGER — visible on tablet/mobile only -->
            <button class="mobile-menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="offcanvas-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

        </div>
    </header>

    <!-- =============================================
         OFF-CANVAS OVERLAY (dark backdrop)
         ============================================= -->
    <div class="offcanvas-overlay" id="offcanvas-overlay" aria-hidden="true"></div>

    <!-- =============================================
         OFF-CANVAS PANEL
         Order: 1. Logo  2. Search  3. Nav menu
         ============================================= -->
    <div class="offcanvas-panel" id="offcanvas-menu" role="dialog" aria-modal="true" aria-label="Navigation menu">

        <!-- Panel header: logo + close button -->
        <div class="offcanvas-header">
            <div class="offcanvas-logo">
                <?php
                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url(home_url('/')) . '">';
                    echo '<span class="logo-brand">' . get_bloginfo('name') . '</span>';
                    echo '</a>';
                }
                ?>
            </div>
            <!-- Close (×) button -->
            <button class="offcanvas-close" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>

        <!-- Search input inside off-canvas -->
        <div class="offcanvas-search">
            <form class="search-form offcanvas-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <span class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                </span>
                <input type="text" class="search-input offcanvas-search-input" placeholder="Search for products..." name="s" autocomplete="off">
                <div class="search-results offcanvas-search-results" aria-live="polite"></div>
            </form>
        </div>

        <!-- Navigation menu inside off-canvas -->
        <nav class="offcanvas-nav" aria-label="Off-canvas navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'offcanvas-primary-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
        </nav>

    </div>
    <!-- /off-canvas -->

    <main id="inner-wrap" class="wrap kt-clear" role="main">
