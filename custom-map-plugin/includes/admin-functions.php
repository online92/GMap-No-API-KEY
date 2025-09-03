<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Render settings tab với Color Picker đúng cách
function custom_map_render_settings_tab() {
    ?>
    <div class="custom-map-panel">
        <h2 class="panel-title">Cài đặt Hiển thị cho Bản đồ</h2>
        
        <form method="post" action="" class="settings-form">
            <?php wp_nonce_field('custom_map_settings_action', 'custom_map_settings_nonce'); ?>
            
            <div class="settings-section">
                <h3>Tùy chỉnh Giao diện</h3>
                
                <div class="settings-grid">
                    <div class="setting-item">
                        <label for="custom_map_name_color">Màu chữ Tên địa điểm</label>
                        <div class="color-picker-wrapper">
                            <input type="text" 
                                   name="custom_map_name_color" 
                                   id="custom_map_name_color" 
                                   value="<?php echo esc_attr(get_option('custom_map_name_color', '#4CAF50')); ?>" 
                                   class="wp-color-picker" 
                                   data-default-color="#4CAF50" />
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <label for="custom_map_address_color">Màu icon Địa chỉ</label>
                        <div class="color-picker-wrapper">
                            <input type="text" 
                                   name="custom_map_address_color" 
                                   id="custom_map_address_color" 
                                   value="<?php echo esc_attr(get_option('custom_map_address_color', '#2196F3')); ?>" 
                                   class="wp-color-picker" 
                                   data-default-color="#2196F3" />
                        </div>
                    </div>
                    
                    <div class="setting-item">
                        <label for="custom_map_button_color">Màu nền nút "Chỉ đường"</label>
                        <div class="color-picker-wrapper">
                            <input type="text" 
                                   name="custom_map_button_color" 
                                   id="custom_map_button_color" 
                                   value="<?php echo esc_attr(get_option('custom_map_button_color', '#000000')); ?>" 
                                   class="wp-color-picker" 
                                   data-default-color="#000000" />
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="settings-section">
                <h3>Bố cục</h3>
                
                <div class="setting-item">
                    <label for="custom_map_layout">Vị trí bản đồ</label>
                    <select name="custom_map_layout" id="custom_map_layout" class="regular-text">
                        <option value="list-left" <?php selected(get_option('custom_map_layout'), 'list-left'); ?>>
                            Danh sách bên trái - Bản đồ bên phải
                        </option>
                        <option value="list-right" <?php selected(get_option('custom_map_layout'), 'list-right'); ?>>
                            Bản đồ bên trái - Danh sách bên phải
                        </option>
                    </select>
                    <p class="description">Chọn bố cục hiển thị của danh sách địa điểm và bản đồ</p>
                </div>
                
                <div class="setting-item">
                    <label>Căn lề hiển thị địa chỉ</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="custom_map_address_position" value="left" 
                                   <?php checked(get_option('custom_map_address_position', 'left'), 'left'); ?>>
                            <span>Căn trái</span>
                        </label>
                        <label>
                            <input type="radio" name="custom_map_address_position" value="center" 
                                   <?php checked(get_option('custom_map_address_position'), 'center'); ?>>
                            <span>Căn giữa</span>
                        </label>
                        <label>
                            <input type="radio" name="custom_map_address_position" value="right" 
                                   <?php checked(get_option('custom_map_address_position'), 'right'); ?>>
                            <span>Căn phải</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save_settings" class="button button-primary button-large">
                    <span class="dashicons dashicons-saved"></span> Lưu Cài đặt
                </button>
            </div>
        </form>
    </div>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize WordPress Color Picker
        $('.wp-color-picker').wpColorPicker();
    });
    </script>
    <?php
}

// Render locations tab với Media Upload fix
function custom_map_render_locations_tab($locations) {
    ?>
    <div class="custom-map-panel">
        <div class="panel-header">
            <h2>Danh sách Địa điểm</h2>
            <button class="button button-primary button-large" id="addLocationBtn">
                <span class="dashicons dashicons-plus-alt"></span> Thêm Địa điểm mới
            </button>
        </div>
        
        <!-- Location Form với layout cải thiện -->
        <div id="locationFormWrapper" class="location-form-wrapper" style="display: none;">
            <div class="location-form-container">
                <h3 id="formTitle">Thêm địa điểm mới</h3>
                <form method="post" action="" id="locationForm">
                    <?php wp_nonce_field('custom_map_location_action', 'custom_map_location_nonce'); ?>
                    <input type="hidden" name="location_id" id="location_id" value="">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="location_name">
                                <span class="dashicons dashicons-tag"></span> Tên địa điểm
                            </label>
                            <input type="text" name="location_name" id="location_name" class="regular-text" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location_phone">
                                <span class="dashicons dashicons-phone"></span> Số điện thoại
                            </label>
                            <input type="text" name="location_phone" id="location_phone" class="regular-text" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="location_address">
                                <span class="dashicons dashicons-location"></span> Địa chỉ
                            </label>
                            <textarea name="location_address" id="location_address" class="large-text" rows="2" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="location_latitude">
                                <span class="dashicons dashicons-admin-site-alt"></span> Vĩ độ (Latitude)
                            </label>
                            <input type="text" name="location_latitude" id="location_latitude" class="regular-text" 
                                   placeholder="VD: 21.1093000" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location_longitude">
                                <span class="dashicons dashicons-admin-site-alt2"></span> Kinh độ (Longitude)
                            </label>
                            <input type="text" name="location_longitude" id="location_longitude" class="regular-text" 
                                   placeholder="VD: 105.7746180" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="location_map_link">
                                <span class="dashicons dashicons-admin-links"></span> Link Google Maps
                            </label>
                            <input type="url" name="location_map_link" id="location_map_link" class="large-text" 
                                   placeholder="https://maps.app.goo.gl/..." required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="location_image">
                                <span class="dashicons dashicons-format-image"></span> Hình ảnh
                            </label>
                            <div class="image-upload-wrapper">
                                <input type="text" name="location_image" id="location_image" class="regular-text" 
                                       placeholder="URL hình ảnh hoặc click Chọn hình" required>
                                <button type="button" id="upload_image_button" class="button button-secondary">
                                    <span class="dashicons dashicons-upload"></span> Chọn hình
                                </button>
                            </div>
                            <div id="image_preview" class="image-preview"></div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="submit_location" class="button button-primary">
                            <span class="dashicons dashicons-saved"></span> Lưu địa điểm
                        </button>
                        <button type="button" class="button" id="cancelBtn">
                            <span class="dashicons dashicons-no"></span> Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Script cho Media Upload -->
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload_image_button').click(function(e) {
                e.preventDefault();
                
                // If the uploader object has already been created, reopen it
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                // Create the media uploader
                mediaUploader = wp.media({
                    title: 'Chọn hình ảnh cho địa điểm',
                    button: {
                        text: 'Sử dụng hình ảnh này'
                    },
                    multiple: false
                });
                
                // When an image is selected, run a callback
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#location_image').val(attachment.url);
                    $('#image_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; margin-top: 10px; border-radius: 4px;">');
                });
                
                // Open the uploader
                mediaUploader.open();
            });
        });
        </script>
        
        <!-- Danh sách địa điểm... -->
        <?php custom_map_render_locations_table($locations); ?>
    </div>
    <?php
}

// Các functions khác giữ nguyên...
function custom_map_render_locations_table($locations) {
    ?>
    <div class="locations-table-wrapper">
        <?php if (!empty($locations)): ?>
            <table class="wp-list-table widefat fixed striped locations-table">
                <thead>
                    <tr>
                        <th width="25%">TÊN</th>
                        <th width="30%">ĐỊA CHỈ</th>
                        <th width="15%">ĐIỆN THOẠI</th>
                        <th width="20%">TỌA ĐỘ</th>
                        <th width="10%" class="text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $location): ?>
                        <tr data-id="<?php echo $location->id; ?>">
                            <td class="location-name-cell">
                                <?php if ($location->image_url): ?>
                                    <img src="<?php echo esc_url($location->image_url); ?>" class="location-thumb" alt="">
                                <?php endif; ?>
                                <strong><?php echo esc_html($location->name); ?></strong>
                            </td>
                            <td><?php echo esc_html($location->address); ?></td>
                            <td><?php echo esc_html($location->phone); ?></td>
                            <td class="coordinates">
                                <code><?php echo esc_html($location->latitude); ?>, <?php echo esc_html($location->longitude); ?></code>
                            </td>
                            <td class="actions text-center">
                                <button class="button-link edit-location" 
                                        data-id="<?php echo $location->id; ?>"
                                        data-name="<?php echo esc_attr($location->name); ?>"
                                        data-address="<?php echo esc_attr($location->address); ?>"
                                        data-phone="<?php echo esc_attr($location->phone); ?>"
                                        data-lat="<?php echo esc_attr($location->latitude); ?>"
                                        data-lng="<?php echo esc_attr($location->longitude); ?>"
                                        data-link="<?php echo esc_attr($location->map_link); ?>"
                                        data-image="<?php echo esc_attr($location->image_url); ?>">
                                    Sửa
                                </button>
                                <span> | </span>
                                <button class="button-link delete-location" data-id="<?php echo $location->id; ?>">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">
                <p style="font-size: 16px; color: #666;">Chưa có địa điểm nào.</p>
                <button class="button button-primary" id="addFirstLocation">
                    <span class="dashicons dashicons-plus-alt"></span> Thêm địa điểm đầu tiên
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// Render guide tab và admin scripts giữ nguyên...
function custom_map_render_guide_tab() {
    ?>
    <div class="custom-map-panel">
        <h2 class="panel-title">Hướng dẫn sử dụng</h2>
        
        <div class="guide-section">
            <h3>1. Hiển thị bản đồ</h3>
            <p>Để hiển thị bản đồ và danh sách địa điểm trên một trang bất kỳ, hãy chèn shortcode sau vào nội dung của trang đó:</p>
            <div class="shortcode-box">
                <code>[custom_map]</code>
                <button class="copy-btn" data-copy="[custom_map]">
                    <span class="dashicons dashicons-clipboard"></span> Copy
                </button>
            </div>
        </div>
        
        <div class="guide-section">
            <h3>2. Lấy Tọa độ & Link Google Maps</h3>
            
            <div class="guide-item">
                <h4><span class="dashicons dashicons-location"></span> Lấy tọa độ GPS (Vĩ độ, Kinh độ):</h4>
                <p>Mở <a href="https://maps.google.com" target="_blank">Google Maps</a>, tìm địa điểm của bạn. Nhấp chuột phải vào đúng vị trí trên bản đồ, tọa độ sẽ hiện ra ở dòng đầu tiên, chỉ cần nhấp vào để sao chép.</p>
            </div>
            
            <div class="guide-item">
                <h4><span class="dashicons dashicons-admin-links"></span> Lấy link chỉ đường:</h4>
                <p>Trên Google Maps, sau khi đã chọn địa điểm, nhấp vào nút "Chia sẻ" (Share), sau đó chọn "Sao chép liên kết" (Copy link).</p>
            </div>
        </div>
        
        <div class="guide-section">
            <h3>Lời khuyên</h3>
            <div class="tips-box">
                <p><span class="dashicons dashicons-lightbulb"></span> Để có trải nghiệm tốt nhất, bạn nên sử dụng hình ảnh có kích thước tối thiểu 300x300 pixels cho mỗi địa điểm.</p>
                <p><span class="dashicons dashicons-lightbulb"></span> Kiểm tra tọa độ kỹ lưỡng trước khi lưu để đảm bảo bản đồ hiển thị đúng vị trí.</p>
            </div>
        </div>
    </div>
    <?php
}

function custom_map_render_admin_scripts() {
    // Scripts khác được xử lý trong admin.js
}