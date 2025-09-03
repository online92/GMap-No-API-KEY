<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Display any messages
settings_errors('custom_map_messages');
?>

<div class="wrap custom-map-admin">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-location-alt"></span> 
        Quản lý Bản đồ Địa điểm
    </h1>
    <div class="custom-plugin-badge">
        <span class="dashicons dashicons-location"></span> My Custom Map Plugin
    </div>
    
    <!-- Tab Navigation -->
    <nav class="nav-tab-wrapper">
        <a href="?page=custom-map-settings&tab=locations" 
           class="nav-tab <?php echo $active_tab == 'locations' ? 'nav-tab-active' : ''; ?>">
            <span class="dashicons dashicons-admin-site"></span> Quản lý Địa điểm
        </a>
        <a href="?page=custom-map-settings&tab=settings" 
           class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">
            <span class="dashicons dashicons-admin-appearance"></span> Cài đặt Hiển thị
        </a>
        <a href="?page=custom-map-settings&tab=guide" 
           class="nav-tab <?php echo $active_tab == 'guide' ? 'nav-tab-active' : ''; ?>">
            <span class="dashicons dashicons-editor-help"></span> Hướng dẫn & Shortcode
        </a>
    </nav>

    <div class="tab-content">
        <?php 
        switch($active_tab) {
            case 'locations':
                custom_map_render_locations_tab($locations);
                break;
            case 'settings':
                custom_map_render_settings_tab();
                break;
            case 'guide':
                custom_map_render_guide_tab();
                break;
        }
        ?>
    </div>
</div>

<?php custom_map_render_admin_scripts(); ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    console.log('Binding admin events...');
    
    // Thêm địa điểm
    $('#addLocationBtn, #addFirstLocation').off('click').on('click', function(e) {
        e.preventDefault();
        $('#locationFormWrapper').slideDown();
        $('#formTitle').text('Thêm địa điểm mới');
        $('#locationForm')[0].reset();
        $('#location_id').val('');
        $('#image_preview').empty();
    });
    
    // Hủy form
    $('#cancelBtn').off('click').on('click', function(e) {
        e.preventDefault();
        $('#locationFormWrapper').slideUp();
        $('#locationForm')[0].reset();
    });
    
    // SỬA địa điểm
    $(document).on('click', '.edit-location', function(e) {
        e.preventDefault();
        var btn = $(this);
        
        // Lấy data từ attributes
        $('#location_id').val(btn.attr('data-id'));
        $('#location_name').val(btn.attr('data-name'));
        $('#location_address').val(btn.attr('data-address'));
        $('#location_phone').val(btn.attr('data-phone'));
        $('#location_latitude').val(btn.attr('data-lat'));
        $('#location_longitude').val(btn.attr('data-lng'));
        $('#location_map_link').val(btn.attr('data-link'));
        $('#location_image').val(btn.attr('data-image'));
        
        // Hiển thị ảnh preview
        if (btn.attr('data-image')) {
            $('#image_preview').html('<img src="' + btn.attr('data-image') + '" style="max-width: 200px; margin-top: 10px;">');
        }
        
        // Hiển thị form
        $('#formTitle').text('Chỉnh sửa địa điểm');
        $('#locationFormWrapper').slideDown();
        
        // Scroll đến form
        $('html, body').animate({
            scrollTop: $('#locationFormWrapper').offset().top - 100
        }, 500);
    });
    
    // XÓA địa điểm
    $(document).on('click', '.delete-location', function(e) {
        e.preventDefault();
        
        if (!confirm('Bạn có chắc chắn muốn xóa địa điểm này?')) {
            return false;
        }
        
        var btn = $(this);
        var row = btn.closest('tr');
        var id = btn.attr('data-id');
        
        btn.text('Đang xóa...');
        
        $.ajax({
            url: custom_map_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_location',
                id: id,
                nonce: custom_map_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    row.fadeOut(300, function() {
                        $(this).remove();
                        if ($('.locations-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert('Lỗi: ' + (response.data || 'Không thể xóa'));
                    btn.text('Xóa');
                }
            },
            error: function() {
                alert('Lỗi kết nối. Vui lòng thử lại!');
                btn.text('Xóa');
            }
        });
    });
    
    // Upload image
    $('#upload_image_button').off('click').on('click', function(e) {
        e.preventDefault();
        
        var mediaUploader = wp.media({
            title: 'Chọn hình ảnh',
            button: {
                text: 'Sử dụng hình này'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#location_image').val(attachment.url);
            $('#image_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; margin-top: 10px;">');
        });
        
        mediaUploader.open();
    });
    
    // Copy shortcode
    $('.copy-btn').off('click').on('click', function(e) {
        e.preventDefault();
        var textToCopy = $(this).attr('data-copy');
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(textToCopy).select();
        document.execCommand('copy');
        temp.remove();
        
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<span class="dashicons dashicons-yes"></span> Đã copy!');
        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    });
    
    console.log('Admin events bound successfully!');
});
</script>