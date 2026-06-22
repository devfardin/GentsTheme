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
            'gentstime-containder',
            get_stylesheet_directory_uri() . '/assets/css/container.css',
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
    }
}