<?php
/**
 * Threshold Wellness Assets Enqueue
 */

if (!defined('ABSPATH')) {
    exit;
}

class ThresholdWellnessAssets {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    public function enqueue_styles() {
        // Main CSS with optimization
        // wp_enqueue_style(
        //     'threshold-wellness-main',
        //     THRESHOLD_WELLNESS_URL . '/assets/css/main.css',
        //     [],
        //     THRESHOLD_WELLNESS_VERSION,
        //     'all'
        // );
        
    }
    
    public function enqueue_scripts() {
        // wp_enqueue_script(
        //     'threshold-wellness-main',
        //     THRESHOLD_WELLNESS_URL . '/assets/js/main.js',
        //     ['jquery'],
        //     THRESHOLD_WELLNESS_VERSION,
        //     true
        // );
    }
}