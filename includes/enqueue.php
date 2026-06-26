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

        // Localize script for AJAX
        wp_localize_script('gentstime-header', 'headerAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('header_search_nonce')
        ));
    }
}