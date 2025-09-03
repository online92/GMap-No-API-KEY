<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue scripts and styles
function custom_map_plugin_enqueue_scripts() {
    // Frontend styles
    wp_enqueue_style('custom-map-style', CUSTOM_MAP_PLUGIN_URL . 'assets/css/style.css', [], CUSTOM_MAP_VERSION);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
    
    // Frontend scripts
    if (!is_admin()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('custom-map-script', CUSTOM_MAP_PLUGIN_URL . 'assets/js/script.js', ['jquery'], CUSTOM_MAP_VERSION, true);
    }

    // Admin scripts
    if (is_admin()) {
        wp_enqueue_media();
        wp_enqueue_script('custom-map-admin', CUSTOM_MAP_PLUGIN_URL . 'assets/js/admin.js', ['jquery', 'wp-color-picker'], CUSTOM_MAP_VERSION, true);
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('custom-map-admin-style', CUSTOM_MAP_PLUGIN_URL . 'assets/css/admin-style.css', [], CUSTOM_MAP_VERSION);
        
        wp_localize_script('custom-map-admin', 'custom_map_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custom_map_ajax_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'custom_map_plugin_enqueue_scripts');
add_action('admin_enqueue_scripts', 'custom_map_plugin_enqueue_scripts');