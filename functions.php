<?php
/**
 * GentsTime Theme Functions
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Security Constants
define('GENTSTIME_VERSION', '1.1.0');
define('GENTSTIME_DIR', __DIR__ . '/includes/');
define('GENTSTIME_SHORTCODE_DIR', __DIR__ . '/includes/shortcodes/');

class GentsTimeFunctions {
    public function __construct() {
        $this->load_dependencies();
        $this->init();
    }
    
    public function load_dependencies() {
        require_once( GENTSTIME_DIR . 'enqueue.php');
        require_once( GENTSTIME_DIR . 'header-functions.php');
    }
    
    public function init() {
        new GentsTimeAssets();
    }
}

new GentsTimeFunctions();