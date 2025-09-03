<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
add_action('admin_menu', 'custom_map_plugin_admin_menu');

function custom_map_plugin_admin_menu() {
    add_menu_page(
        'Quản lý Bản đồ Địa điểm',
        'Bản đồ Địa điểm',
        'manage_options',
        'custom-map-settings',
        'custom_map_plugin_settings_page',
        'dashicons-location-alt',
        30
    );
}

// Register settings
add_action('admin_init', 'custom_map_plugin_register_settings');

function custom_map_plugin_register_settings() {
    register_setting('custom_map_options_group', 'custom_map_name_color', 'sanitize_hex_color');
    register_setting('custom_map_options_group', 'custom_map_address_color', 'sanitize_hex_color');
    register_setting('custom_map_options_group', 'custom_map_button_color', 'sanitize_hex_color');
    register_setting('custom_map_options_group', 'custom_map_layout', 'sanitize_text_field');
    register_setting('custom_map_options_group', 'custom_map_address_position', 'sanitize_text_field');
}

// Main admin page
function custom_map_plugin_settings_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_map_locations';
    
    // Get current tab
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'locations';
    
    // Handle form submissions
    custom_map_handle_form_submissions();
    
    // Get all locations
    $locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");
    
    // Include the admin template
    require_once CUSTOM_MAP_PLUGIN_DIR . 'templates/admin-page.php';
}

// Handle form submissions
function custom_map_handle_form_submissions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_map_locations';
    
    // Handle location add/edit
    if (isset($_POST['submit_location']) && check_admin_referer('custom_map_location_action', 'custom_map_location_nonce')) {
        $id = isset($_POST['location_id']) ? intval($_POST['location_id']) : 0;
        
        $data = [
            'name'      => sanitize_text_field($_POST['location_name']),
            'address'   => sanitize_textarea_field($_POST['location_address']),
            'phone'     => sanitize_text_field($_POST['location_phone']),
            'latitude'  => floatval($_POST['location_latitude']),
            'longitude' => floatval($_POST['location_longitude']),
            'map_link'  => esc_url_raw($_POST['location_map_link']),
            'image_url' => esc_url_raw($_POST['location_image']),
        ];

        if ($id > 0) {
            $result = $wpdb->update($table_name, $data, ['id' => $id]);
            if ($result !== false) {
                add_settings_error('custom_map_messages', 'location_updated', 'Địa điểm đã được cập nhật thành công!', 'success');
            }
        } else {
            $result = $wpdb->insert($table_name, $data);
            if ($result !== false) {
                add_settings_error('custom_map_messages', 'location_added', 'Địa điểm mới đã được thêm thành công!', 'success');
            }
        }
    }
    
    // Handle settings save
    if (isset($_POST['save_settings']) && check_admin_referer('custom_map_settings_action', 'custom_map_settings_nonce')) {
        update_option('custom_map_name_color', sanitize_hex_color($_POST['custom_map_name_color']));
        update_option('custom_map_address_color', sanitize_hex_color($_POST['custom_map_address_color']));
        update_option('custom_map_button_color', sanitize_hex_color($_POST['custom_map_button_color']));
        update_option('custom_map_layout', sanitize_text_field($_POST['custom_map_layout']));
        update_option('custom_map_address_position', sanitize_text_field($_POST['custom_map_address_position']));
        
        add_settings_error('custom_map_messages', 'settings_saved', 'Cài đặt đã được lưu thành công!', 'success');
    }
}