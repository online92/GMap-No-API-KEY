<?php
// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode
add_shortcode('custom_map', 'custom_map_plugin_shortcode');

function custom_map_plugin_shortcode($atts = []) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_map_locations';
    $locations = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");

    if (empty($locations)) {
        return '<p style="text-align: center; padding: 20px;">Không có địa điểm nào để hiển thị. Vui lòng thêm địa điểm trong trang quản trị.</p>';
    }

    // Get settings
    $name_color = get_option('custom_map_name_color', '#4CAF50');
    $address_color = get_option('custom_map_address_color', '#2196F3');
    $button_color = get_option('custom_map_button_color', '#000000');
    $layout_class = get_option('custom_map_layout', 'list-left');
    $address_position = get_option('custom_map_address_position', 'left');

    // Generate unique ID
    static $instance = 0;
    $instance++;
    $map_id = 'custom-map-' . $instance;

    ob_start();
    ?>
    
    <!-- Inline Styles với responsive cải thiện -->
    <style>
        #<?php echo $map_id; ?> {
            --location-name-color: <?php echo esc_attr($name_color); ?>;
            --location-address-color: <?php echo esc_attr($address_color); ?>;
            --directions-btn-bg-color: <?php echo esc_attr($button_color); ?>;
        }
        
        /* Container địa điểm */
        #<?php echo $map_id; ?> .location-item {
            cursor: pointer;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            position: relative;
            min-height: 100px;
        }
        
        #<?php echo $map_id; ?> .location-item:hover {
            background-color: #f8fafb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        #<?php echo $map_id; ?> .location-item.active {
            background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
            border-color: var(--location-name-color);
            box-shadow: 0 3px 10px rgba(76,175,80,0.15);
        }
        
        #<?php echo $map_id; ?> .location-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--location-name-color);
            border-radius: 8px 0 0 8px;
        }
        
        /* Hình ảnh - Responsive */
        #<?php echo $map_id; ?> .location-image {
            flex-shrink: 0;
            width: 90px;
            height: 90px;
            position: relative;
            overflow: hidden;
            border-radius: 6px;
        }
        
        #<?php echo $map_id; ?> .location-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        #<?php echo $map_id; ?> .location-image .no-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 28px;
        }
        
        /* Thông tin - Flexible layout */
        #<?php echo $map_id; ?> .location-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        /* Tên địa điểm */
        #<?php echo $map_id; ?> .location-name {
            margin: 0 0 4px 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--location-name-color);
            line-height: 1.2;
            text-align: <?php echo esc_attr($address_position); ?>;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        
        /* Địa chỉ */
        #<?php echo $map_id; ?> .location-address {
            margin: 0;
            font-size: 12px;
            line-height: 1.3;
            color: #666;
            text-align: <?php echo esc_attr($address_position); ?>;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }
        
        #<?php echo $map_id; ?> .location-address i {
            color: var(--location-address-color);
            font-size: 11px;
            margin-right: 4px;
            vertical-align: middle;
        }
        
        /* CONTACT ROW - Flexible layout */
        #<?php echo $map_id; ?> .location-contact {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
            flex-wrap: wrap; /* Cho phép wrap khi cần */
        }
        
        /* Số điện thoại */
        #<?php echo $map_id; ?> .location-phone {
            margin: 0;
            font-size: 12px;
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        #<?php echo $map_id; ?> .location-phone i {
            color: var(--location-address-color);
            font-size: 11px;
            margin-right: 4px;
        }
        
        #<?php echo $map_id; ?> .location-phone a {
            color: #43A047;
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Nút chỉ đường - Compact design */
        #<?php echo $map_id; ?> .directions-btn {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            background: var(--directions-btn-bg-color);
            color: white !important;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        
        #<?php echo $map_id; ?> .directions-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        
        #<?php echo $map_id; ?> .directions-btn i {
            font-size: 10px;
            margin-right: 4px;
            color: white;
        }
        
        /* Map display */
        #<?php echo $map_id; ?> .map-display {
            min-height: 400px;
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        #<?php echo $map_id; ?> .map-display iframe {
            width: 100%;
            height: 400px;
            border: 0;
        }
        
        /* Location list */
        #<?php echo $map_id; ?> .location-list {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px;
            background: #fafafa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        /* Scrollbar */
        #<?php echo $map_id; ?> .location-list::-webkit-scrollbar {
            width: 4px;
        }
        
        #<?php echo $map_id; ?> .location-list::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 4px;
        }
        
        /* RESPONSIVE - Container nhỏ */
        @media (max-width: 600px), (max-width: 100%) {
            /* Khi container quá nhỏ */
            #<?php echo $map_id; ?> .location-list {
                width: 100%;
            }
            
            /* Giảm padding và gap */
            #<?php echo $map_id; ?> .location-item {
                padding: 8px;
                gap: 8px;
                min-height: 80px;
            }
            
            /* Hình ảnh nhỏ hơn */
            #<?php echo $map_id; ?> .location-image {
                width: 70px;
                height: 70px;
            }
            
            /* Font size nhỏ hơn */
            #<?php echo $map_id; ?> .location-name {
                font-size: 14px;
            }
            
            #<?php echo $map_id; ?> .location-address {
                font-size: 11px;
            }
            
            #<?php echo $map_id; ?> .location-phone {
                font-size: 11px;
            }
            
            /* Nút chỉ đường - chỉ icon */
            #<?php echo $map_id; ?> .directions-btn {
                padding: 4px 8px;
                font-size: 10px;
            }
            
            /* Ẩn text, chỉ giữ icon khi quá nhỏ */
            @media (max-width: 400px) {
                #<?php echo $map_id; ?> .directions-btn span {
                    display: none;
                }
                
                #<?php echo $map_id; ?> .directions-btn i {
                    margin-right: 0;
                    font-size: 14px;
                }
                
                #<?php echo $map_id; ?> .directions-btn {
                    padding: 6px 8px;
                    border-radius: 50%;
                }
            }
        }
        
        /* Container check - JavaScript will add class */
        #<?php echo $map_id; ?>.narrow-container .location-item {
            padding: 8px;
            gap: 8px;
        }
        
        #<?php echo $map_id; ?>.narrow-container .location-image {
            width: 70px;
            height: 70px;
        }
        
        #<?php echo $map_id; ?>.narrow-container .location-contact {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
        
        #<?php echo $map_id; ?>.narrow-container .directions-btn {
            align-self: flex-end;
        }
    </style>
    
    <!-- Map Container -->
    <div id="<?php echo $map_id; ?>" class="map-container <?php echo esc_attr($layout_class); ?>">
        <div class="location-list">
            <?php foreach ($locations as $index => $location) : ?>
                <div class="location-item location-item-<?php echo $index; ?>" 
                    data-index="<?php echo $index; ?>"
                    data-lat="<?php echo esc_attr($location->latitude); ?>" 
                    data-lng="<?php echo esc_attr($location->longitude); ?>"
                    data-name="<?php echo esc_attr($location->name); ?>">
                    
                    <!-- Hình ảnh -->
                    <div class="location-image">
                        <?php if ($location->image_url) : ?>
                            <img src="<?php echo esc_url($location->image_url); ?>" 
                                 alt="<?php echo esc_attr($location->name); ?>"
                                 loading="lazy">
                        <?php else : ?>
                            <div class="no-image">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thông tin với layout flexible -->
                    <div class="location-info">
                        <!-- Tên có căn lề -->
                        <h3 class="location-name"><?php echo esc_html($location->name); ?></h3>
                        
                        <!-- Địa chỉ có căn lề -->
                        <p class="location-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo esc_html($location->address); ?>
                        </p>
                        
                        <!-- Phone và Button cùng dòng -->
                        <div class="location-contact">
                            <p class="location-phone">
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo esc_attr($location->phone); ?>">
                                    <?php echo esc_html($location->phone); ?>
                                </a>
                            </p>
                            
                            <?php if ($location->map_link) : ?>
                            <a href="<?php echo esc_url($location->map_link); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="directions-btn">
                                <i class="fas fa-directions"></i>
                                <span>Chỉ đường</span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="map-display" id="map-display-<?php echo $instance; ?>"></div>
    </div>
    
    <!-- JavaScript với container width detection -->
    <script type="text/javascript">
    (function() {
        'use strict';
        
        function initMap<?php echo $instance; ?>() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initMap<?php echo $instance; ?>, 100);
                return;
            }
            
            jQuery(function($) {
                console.log('Initializing map instance <?php echo $instance; ?>');
                
                var container = $('#<?php echo $map_id; ?>');
                var mapDisplay = $('#map-display-<?php echo $instance; ?>');
                var locationItems = container.find('.location-item');
                
                // Check container width and add class if narrow
                function checkContainerWidth() {
                    var containerWidth = container.width();
                    if (containerWidth < 500) {
                        container.addClass('narrow-container');
                    } else {
                        container.removeClass('narrow-container');
                    }
                }
                
                // Check on load and resize
                checkContainerWidth();
                $(window).on('resize', checkContainerWidth);
                
                if (!mapDisplay.length || !locationItems.length) {
                    console.error('Map <?php echo $instance; ?>: Missing elements');
                    return;
                }
                
                function updateMap(lat, lng, name) {
                    lat = parseFloat(lat);
                    lng = parseFloat(lng);
                    
                    console.log('Map <?php echo $instance; ?>: Showing', name, 'at', lat, lng);
                    
                    if (isNaN(lat) || isNaN(lng)) {
                        console.error('Map <?php echo $instance; ?>: Invalid coordinates');
                        return;
                    }
                    
                    var mapUrl = 'https://maps.google.com/maps?q=' + lat + ',' + lng;
                    mapUrl += '&z=15&output=embed&hl=vi';
                    
                    var iframeHtml = '<iframe src="' + mapUrl + '" ';
                    iframeHtml += 'width="100%" height="400" ';
                    iframeHtml += 'style="border:0; border-radius: 8px;" ';
                    iframeHtml += 'allowfullscreen="" loading="lazy" ';
                    iframeHtml += 'referrerpolicy="no-referrer-when-downgrade"></iframe>';
                    
                    mapDisplay.html(iframeHtml);
                }
                
                // Bind click events
                locationItems.each(function(index) {
                    var item = $(this);
                    var itemLat = item.attr('data-lat');
                    var itemLng = item.attr('data-lng');
                    var itemName = item.attr('data-name');
                    
                    item.data('coordinates', {
                        lat: itemLat,
                        lng: itemLng,
                        name: itemName
                    });
                    
                    item.off('click.custommap').on('click.custommap', function(e) {
                        if ($(e.target).is('a') || $(e.target).closest('a').length) {
                            return;
                        }
                        
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var coords = $(this).data('coordinates');
                        
                        console.log('Map <?php echo $instance; ?>: Clicked', coords.name);
                        
                        locationItems.removeClass('active');
                        $(this).addClass('active');
                        
                        updateMap(coords.lat, coords.lng, coords.name);
                    });
                    
                    if (index === 0) {
                        item.addClass('active');
                        updateMap(itemLat, itemLng, itemName);
                    }
                });
                
                console.log('Map <?php echo $instance; ?>: Ready with', locationItems.length, 'locations');
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap<?php echo $instance; ?>);
        } else {
            initMap<?php echo $instance; ?>();
        }
    })();
    </script>
    
    <?php
    return ob_get_clean();
}