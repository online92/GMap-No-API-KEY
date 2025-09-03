<?php
/*
Plugin Name: Custom Map Plugin
Description: Plugin hiển thị bản đồ địa điểm sử dụng tọa độ Google Maps không cần API
Version: 3.0
Author: Your Name
Text Domain: custom-map-plugin
*/

// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CUSTOM_MAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_MAP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CUSTOM_MAP_VERSION', '3.0');

// Include required files
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/activation.php';
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/enqueue.php';
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/shortcode.php';
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/admin-menu.php';
require_once CUSTOM_MAP_PLUGIN_DIR . 'includes/admin-functions.php';

// Register activation hook
register_activation_hook(__FILE__, 'custom_map_plugin_activate');

// Register deactivation hook
register_deactivation_hook(__FILE__, 'custom_map_plugin_deactivate');

function custom_map_plugin_deactivate() {
    // Clean up if needed
    flush_rewrite_rules();
}