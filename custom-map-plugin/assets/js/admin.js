/**
 * Custom Map Plugin - Admin JavaScript
 * Version: 3.0
 */

jQuery(document).ready(function($) {
    'use strict';
    
    console.log('Custom Map Admin JS: Initializing...');
    
    // Initialize color pickers
    if ($.fn.wpColorPicker) {
        $('.color-picker').each(function() {
            var $this = $(this);
            var $preview = $this.siblings('.color-preview');
            
            $this.wpColorPicker({
                change: function(event, ui) {
                    $preview.css('background-color', ui.color.toString());
                },
                clear: function() {
                    $preview.css('background-color', '');
                }
            });
        });
        
        // Update preview on load
        $('.color-picker').each(function() {
            var color = $(this).val();
            if (color) {
                $(this).closest('.color-picker-wrapper').find('.color-preview').css('background-color', color);
            }
        });
    }
    
    // Media uploader for location image
    var mediaUploader;
    
    $('#upload_image_button').on('click', function(e) {
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
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // When an image is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Set the image URL to the input field
            $('#location_image').val(attachment.url);
            
            // Show preview
            var previewHtml = '<img src="' + attachment.url + '" style="max-width: 200px; margin-top: 10px; border-radius: 4px; border: 1px solid #ddd;">';
            $('#image_preview').html(previewHtml);
        });
        
        // Open the uploader
        mediaUploader.open();
    });
    
    // Clear image button (if needed)
    $(document).on('click', '#clear_image_button', function(e) {
        e.preventDefault();
        $('#location_image').val('');
        $('#image_preview').empty();
    });
    
    // Form validation
    $('#locationForm').on('submit', function(e) {
        var isValid = true;
        var errors = [];
        
        // Validate required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                isValid = false;
                errors.push($(this).prev('label').text());
            } else {
                $(this).removeClass('error');
            }
        });
        
        // Validate latitude
        var lat = parseFloat($('#location_latitude').val());
        if (isNaN(lat) || lat < -90 || lat > 90) {
            $('#location_latitude').addClass('error');
            errors.push('Vĩ độ phải trong khoảng -90 đến 90');
            isValid = false;
        }
        
        // Validate longitude
        var lng = parseFloat($('#location_longitude').val());
        if (isNaN(lng) || lng < -180 || lng > 180) {
            $('#location_longitude').addClass('error');
            errors.push('Kinh độ phải trong khoảng -180 đến 180');
            isValid = false;
        }
        
        // Validate phone number (basic)
        var phone = $('#location_phone').val();
        if (phone && !phone.match(/^[0-9\-\+\s\(\)]+$/)) {
            $('#location_phone').addClass('error');
            errors.push('Số điện thoại không hợp lệ');
            isValid = false;
        }
        
        // Validate URL
        var mapLink = $('#location_map_link').val();
        if (mapLink && !mapLink.match(/^https?:\/\/.+/)) {
            $('#location_map_link').addClass('error');
            errors.push('Link Google Maps phải bắt đầu với http:// hoặc https://');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Show error message
            var errorHtml = '<div class="notice notice-error is-dismissible"><p><strong>Lỗi:</strong> Vui lòng kiểm tra lại các trường sau:<ul>';
            errors.forEach(function(error) {
                errorHtml += '<li>' + error + '</li>';
            });
            errorHtml += '</ul></p></div>';
            
            // Remove old errors
            $('.location-form-wrapper .notice').remove();
            
            // Add new error
            $('.location-form-container').prepend(errorHtml);
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $('#locationFormWrapper').offset().top - 50
            }, 500);
            
            return false;
        }
    });
    
    // Remove error class on input
    $('#locationForm input, #locationForm textarea').on('input', function() {
        $(this).removeClass('error');
    });
    
    // Add error styling
    $('<style>')
        .text('.error { border-color: #dc3545 !important; }')
        .appendTo('head');
    
    // Auto-format phone number
    $('#location_phone').on('input', function() {
        var value = $(this).val();
        // Remove all non-digits
        value = value.replace(/[^\d]/g, '');
        
        // Format as needed (example: 024-3950-0888)
        if (value.length > 3 && value.length <= 7) {
            value = value.slice(0, 3) + '-' + value.slice(3);
        } else if (value.length > 7) {
            value = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
        }
        
        // Update input if different
        if (value !== $(this).val()) {
            $(this).val(value);
        }
    });
    
    // Coordinates helper - Get from Google Maps link
    $('#location_map_link').on('blur', function() {
        var url = $(this).val();
        if (!url) return;
        
        // Try to extract coordinates from Google Maps URL
        // Example: https://maps.app.goo.gl/... or https://www.google.com/maps/@21.109,105.774,15z
        var match = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (match) {
            $('#location_latitude').val(match[1]);
            $('#location_longitude').val(match[2]);
            
            // Show success message
            var msg = $('<span class="dashicons dashicons-yes" style="color: #4CAF50; margin-left: 10px;"></span>');
            $(this).after(msg);
            setTimeout(function() {
                msg.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
    
    // Tab persistence using localStorage
    if (typeof(Storage) !== "undefined") {
        // Check if there's a saved tab
        var savedTab = localStorage.getItem('custom_map_active_tab');
        if (savedTab && $('.nav-tab[href*="' + savedTab + '"]').length) {
            // Update URL without reloading
            var url = window.location.href.split('&tab=')[0] + '&tab=' + savedTab;
            window.history.replaceState({}, '', url);
        }
        
        // Save tab when clicked
        $('.nav-tab').on('click', function() {
            var tab = $(this).attr('href').split('tab=')[1];
            if (tab) {
                localStorage.setItem('custom_map_active_tab', tab);
            }
        });
    }
    
    // Tooltip for coordinates
    $('.coordinates').each(function() {
        $(this).attr('title', 'Click để copy tọa độ');
        $(this).css('cursor', 'pointer');
        
        $(this).on('click', function() {
            var coords = $(this).text();
            
            // Create temporary input
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(coords).select();
            document.execCommand('copy');
            tempInput.remove();
            
            // Show feedback
            var $this = $(this);
            var originalText = $this.text();
            $this.text('Đã copy!').css('color', '#4CAF50');
            setTimeout(function() {
                $this.text(originalText).css('color', '#666');
            }, 2000);
        });
    });
    
    // Search/filter locations if table has many rows
    if ($('.locations-table tbody tr').length > 5) {
        // Add search box
        var searchBox = $('<div class="search-wrapper" style="margin-bottom: 15px;">' +
                       '<input type="text" id="locationSearch" placeholder="Tìm kiếm địa điểm..." ' +
                       'style="padding: 8px 12px; width: 300px; border: 1px solid #ddd; border-radius: 4px;">' +
                       '</div>');
        
        $('.locations-table-wrapper').prepend(searchBox);
        
        // Search functionality
        $('#locationSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.locations-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    }
    
    // Auto-save draft for location form
    var autoSaveTimer;
    var draftKey = 'custom_map_location_draft';
    
    // Save draft on input
    $('#locationForm input, #locationForm textarea').on('input', function() {
        clearTimeout(autoSaveTimer);
        
        // Don't save if editing existing location
        if ($('#location_id').val()) {
            return;
        }
        
        autoSaveTimer = setTimeout(function() {
            var formData = {};
            $('#locationForm').serializeArray().forEach(function(item) {
                if (item.name !== 'location_id' && item.value) {
                    formData[item.name] = item.value;
                }
            });
            
            if (Object.keys(formData).length > 0) {
                localStorage.setItem(draftKey, JSON.stringify(formData));
                
                // Show auto-save indicator
                var indicator = $('#autoSaveIndicator');
                if (!indicator.length) {
                    indicator = $('<span id="autoSaveIndicator" style="color: #4CAF50; font-size: 12px; margin-left: 10px;">✓ Đã lưu nháp</span>');
                    $('#formTitle').after(indicator);
                }
                indicator.fadeIn(200).delay(2000).fadeOut(200);
            }
        }, 1000);
    });
    
    // Restore draft when opening add form
    $('#addLocationBtn, #addFirstLocation').on('click', function() {
        var draft = localStorage.getItem(draftKey);
        if (draft && !$('#location_id').val()) {
            draft = JSON.parse(draft);
            
            if (Object.keys(draft).length > 0 && confirm('Bạn có muốn khôi phục bản nháp đã lưu không?')) {
                Object.keys(draft).forEach(function(key) {
                    var field = $('[name="' + key + '"]');
                    if (field.length) {
                        field.val(draft[key]);
                    }
                });
                
                // Show image preview if exists
                if (draft.location_image) {
                    $('#image_preview').html('<img src="' + draft.location_image + '" style="max-width: 200px; margin-top: 10px; border-radius: 4px;">');
                }
            }
        }
    });
    
    // Clear draft when form is submitted successfully
    $('#locationForm').on('submit', function() {
        if (!$(this).hasClass('has-error')) {
            localStorage.removeItem(draftKey);
        }
    });
    
    // Clear draft when cancel
    $('#cancelBtn').on('click', function() {
        if (confirm('Bạn có muốn xóa bản nháp không?')) {
            localStorage.removeItem(draftKey);
        }
    });
    
    // Bulk actions (if needed in future)
    var bulkActionBtn = $('<button class="button" id="bulkDeleteBtn" style="display: none; margin-left: 10px;">Xóa đã chọn</button>');
    $('.panel-header').append(bulkActionBtn);
    
    // Add checkboxes to table
    if ($('.locations-table tbody tr').length > 0) {
        // Add header checkbox
        $('.locations-table thead tr').prepend('<th width="3%"><input type="checkbox" id="selectAll"></th>');
        
        // Add row checkboxes
        $('.locations-table tbody tr').each(function() {
            $(this).prepend('<td><input type="checkbox" class="location-checkbox" value="' + $(this).data('id') + '"></td>');
        });
        
        // Select all functionality
        $('#selectAll').on('change', function() {
            $('.location-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkActions();
        });
        
        // Individual checkbox
        $('.location-checkbox').on('change', function() {
            toggleBulkActions();
        });
        
        // Toggle bulk actions button
        function toggleBulkActions() {
            var checkedCount = $('.location-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#bulkDeleteBtn').show().text('Xóa ' + checkedCount + ' mục đã chọn');
            } else {
                $('#bulkDeleteBtn').hide();
            }
        }
        
        // Bulk delete
        $('#bulkDeleteBtn').on('click', function() {
            var selectedIds = [];
            $('.location-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                return;
            }
            
            if (!confirm('Bạn có chắc chắn muốn xóa ' + selectedIds.length + ' địa điểm đã chọn?')) {
                return;
            }
            
            // Process bulk delete (would need server-side handler)
            alert('Chức năng xóa hàng loạt sẽ được thêm trong phiên bản sau');
        });
    }
    
    // Settings page enhancements
    if ($('.settings-form').length) {
        // Preview color changes in real-time
        $('.color-picker').on('change', function() {
            var colorType = $(this).attr('id');
            var color = $(this).val();
            
            // Update preview based on which color is changing
            if (colorType === 'custom_map_name_color') {
                $('.location-name').css('color', color);
            } else if (colorType === 'custom_map_address_color') {
                $('.location-address i, .location-phone i').css('color', color);
            } else if (colorType === 'custom_map_button_color') {
                $('.directions-btn').css('background-color', color);
            }
        });
        
        // Reset colors button
        var resetBtn = $('<button type="button" class="button" style="margin-left: 10px;">Đặt lại màu mặc định</button>');
        $('.settings-form .form-actions').append(resetBtn);
        
        resetBtn.on('click', function() {
            if (confirm('Đặt lại tất cả màu về mặc định?')) {
                $('#custom_map_name_color').val('#4CAF50').trigger('change');
                $('#custom_map_address_color').val('#2196F3').trigger('change');
                $('#custom_map_button_color').val('#000000').trigger('change');
            }
        });
    }
    
    // Add keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl+S to save form
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            if ($('#locationForm:visible').length) {
                $('#locationForm').submit();
            } else if ($('.settings-form:visible').length) {
                $('.settings-form').submit();
            }
        }
        
        // ESC to close form
        if (e.key === 'Escape') {
            if ($('#locationFormWrapper:visible').length) {
                $('#cancelBtn').click();
            }
        }
    });
    
    // Performance: Lazy load images in table
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        $('.location-thumb[data-src]').each(function() {
            imageObserver.observe(this);
        });
    }
    
    console.log('Custom Map Admin JS: Ready');
});