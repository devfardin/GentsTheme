<?php
/**
 * Threshold Wellness Theme Functions
 */

// Security Constants
define('THRESHOLD_WELLNESS_VERSION', '1.0.0');
define('THRESHOLD_WELLNESS_DIR', __DIR__ . '/includes/');
define('THRESHOLD_WELLNESS_SHORTCODE_DIR', __DIR__ . '/includes/shortcodes/');

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ThresholdWellnessFunctions {
    public function __construct() {
        $this->load_dependencies();
        $this->init();
    }
    
    public function load_dependencies() {
        require_once( THRESHOLD_WELLNESS_DIR . 'enqueue.php');
    }
    
    public function init() {
        new ThresholdWellnessAssets();
    }
}

new ThresholdWellnessFunctions();