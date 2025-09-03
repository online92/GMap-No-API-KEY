<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

function custom_map_plugin_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_map_locations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        address text NOT NULL,
        phone varchar(20) NOT NULL,
        latitude decimal(10,7) NOT NULL,
        longitude decimal(10,7) NOT NULL,
        map_link text NOT NULL,
        image_url text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Thêm các tùy chọn mặc định
    add_option('custom_map_name_color', '#4CAF50');
    add_option('custom_map_address_color', '#2196F3');
    add_option('custom_map_button_color', '#000000');
    add_option('custom_map_layout', 'list-left');
    add_option('custom_map_address_position', 'left');
}