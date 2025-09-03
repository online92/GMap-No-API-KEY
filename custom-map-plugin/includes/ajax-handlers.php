<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// AJAX handler for deleting location
add_action('wp_ajax_delete_location', 'custom_map_delete_location');

function custom_map_delete_location() {
    // Check nonce
    check_ajax_referer('custom_map_ajax_nonce', 'nonce');
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_map_locations';
    $id = intval($_POST['id']);
    
    if ($id <= 0) {
        wp_send_json_error('Invalid ID');
        return;
    }
    
    $result = $wpdb->delete($table_name, ['id' => $id]);
    
    if ($result !== false) {
        wp_send_json_success('Location deleted successfully');
    } else {
        wp_send_json_error('Failed to delete location');
    }
}