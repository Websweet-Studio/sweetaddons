<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">💬 Pengaturan Chat WhatsApp</h1>
    <form method="post" action="" class="sad-form">
        <?php wp_nonce_field('sweetaddons_whatsapp_settings'); ?>
        <div class="sad-top">
            <div class="sad-top-left">
                <div class="sad-card">
                    <div class="sad-card-title">⚙️ Pengaturan Dasar</div>

                    <table class="form-table">
                        <tr>
                            <th scope="row">Aktifkan Chat WhatsApp</th>
                            <td>
                                <label>
                                    <input type="checkbox" id="sweetaddons_whatsapp_enable" name="sweetaddons_whatsapp_enable" value="1" <?php checked($enable, '1'); ?> />
                                    Enable floating WhatsApp chat button
                                </label>
                                <p class="description">Show WhatsApp chat widget on your website.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_phone">WhatsApp Number</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whatsapp_phone" name="sweetaddons_whatsapp_phone" value="<?php echo esc_attr($phone); ?>" class="large-text" placeholder="+62812345678901" />
                                <p class="description">Your WhatsApp number with country code (e.g., +62812345678901).</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_message">Pesan Default</label>
                            </th>
                            <td>
                                <textarea id="sweetaddons_whatsapp_message" name="sweetaddons_whatsapp_message" rows="3" class="large-text"><?php echo esc_textarea($message); ?></textarea>
                                <p class="description">Default message that will be pre-filled when users click the chat button.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_button_text">Teks Tombol</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whatsapp_button_text" name="sweetaddons_whatsapp_button_text" value="<?php echo esc_attr($button_text); ?>" class="large-text" />
                                <p class="description">Text shown on the button (for extended style) and tooltip.</p>
                            </td>
                        </tr>
                        <!-- Appearance Section Header -->
                        <tr>
                            <td colspan="2">
                                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" style="padding-left: 0;">
                                <h3 style="margin: 0;">🎨 Pengaturan Tampilan</h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_bubble_style">Button Style</label>
                            </th>
                            <td>
                                <select id="sweetaddons_whatsapp_bubble_style" name="sweetaddons_whatsapp_bubble_style">
                                    <option value="circle" <?php selected($bubble_style, 'circle'); ?>>Circle (Icon Only)</option>
                                    <option value="extended" <?php selected($bubble_style, 'extended'); ?>>Extended (Icon + Text)</option>
                                </select>
                                <p class="description">Choose between circle icon or extended button with text.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_color">Warna Tombol</label>
                            </th>
                            <td>
                                <input type="color" id="sweetaddons_whatsapp_color" name="sweetaddons_whatsapp_color" value="<?php echo esc_attr($color); ?>" />
                                <input type="text" value="<?php echo esc_attr($color); ?>" class="regular-text" readonly />
                                <p class="description">Background color of the WhatsApp button.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_size">Ukuran Tombol</label>
                            </th>
                            <td>
                                <input type="number" id="sweetaddons_whatsapp_size" name="sweetaddons_whatsapp_size" value="<?php echo esc_attr($size); ?>" min="40" max="100" class="small-text" /> px
                                <p class="description">Ukuran tombol chat (40-100px).</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whatsapp_animation">Animasi</label>
                            </th>
                            <td>
                                <select id="sweetaddons_whatsapp_animation" name="sweetaddons_whatsapp_animation">
                                    <option value="none" <?php selected($animation, 'none'); ?>>Tanpa Animasi</option>
                                    <option value="pulse" <?php selected($animation, 'pulse'); ?>>Pulse</option>
                                    <option value="bounce" <?php selected($animation, 'bounce'); ?>>Bounce</option>
                                    <option value="shake" <?php selected($animation, 'shake'); ?>>Shake</option>
                                </select>
                                <p class="description">Efek animasi untuk tombol chat.</p>
                            </td>
                        </tr>
                        <!-- Position Section Header -->
                        <tr>
                            <td colspan="2">
                                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" style="padding-left: 0;">
                                <h3 style="margin: 0;">📍 Pengaturan Posisi</h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">

                                <label for="sweetaddons_whatsapp_position">Posisi Tombol</label>
                            </th>
                            <td>
                                <select id="sweetaddons_whatsapp_position" name="sweetaddons_whatsapp_position">
                                    <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Kanan Bawah</option>
                                    <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Kiri Bawah</option>
                                    <option value="top-right" <?php selected($position, 'top-right'); ?>>Kanan Atas</option>
                                    <option value="top-left" <?php selected($position, 'top-left'); ?>>Kiri Atas</option>
                                    <option value="center-right" <?php selected($position, 'center-right'); ?>>Center Right</option>
                                    <option value="center-left" <?php selected($position, 'center-left'); ?>>Center Left</option>
                                </select>
                                <p class="description">Where to position the chat button on your website.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Jarak Offset</th>
                            <td>
                                <label>
                                    X: <input type="number" id="sweetaddons_whatsapp_offset_x" name="sweetaddons_whatsapp_offset_x" value="<?php echo esc_attr($offset_x); ?>" min="0" max="100" class="small-text" /> px
                                </label>
                                <label style="margin-left: 20px;">
                                    Y: <input type="number" id="sweetaddons_whatsapp_offset_y" name="sweetaddons_whatsapp_offset_y" value="<?php echo esc_attr($offset_y); ?>" min="0" max="100" class="small-text" /> px
                                </label>
                                <p class="description">Distance from screen edges (X = horizontal, Y = vertical).</p>
                            </td>
                        </tr>
                        <!-- Visibility Section Header -->
                        <tr>
                            <td colspan="2">
                                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" style="padding-left: 0;">
                                <h3 style="margin: 0;">👁️ Visibility Settings</h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">Device Visibility</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="sweetaddons_whatsapp_show_mobile" value="1" <?php checked($show_mobile, '1'); ?> />
                                    Tampilkan di perangkat Mobile
                                </label><br>
                                <label>
                                    <input type="checkbox" name="sweetaddons_whatsapp_show_desktop" value="1" <?php checked($show_desktop, '1'); ?> />
                                    Tampilkan di perangkat Desktop
                                </label>
                                <p class="description">Choose on which devices to display the chat button.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Tooltip</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="sweetaddons_whatsapp_show_tooltip" value="1" <?php checked($show_tooltip, '1'); ?> />
                                    Show tooltip on hover
                                </label>
                                <p class="description">Display tooltip text when hovering over the chat button.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="sad-card">
                    <div class="sad-card-title">👁️ Live Preview</div>
                    <p style="margin-bottom: 20px;">This is how your WhatsApp chat button will look:</p>

                    <div style="position: relative; height: 200px; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px; overflow: hidden;">
                        <div style="position: absolute; top: 10px; left: 10px; color: #666; font-size: 12px;">Preview Area</div>

                        <div id="whatsapp-preview-container" style="display: <?php echo ($enable && $phone) ? 'block' : 'none'; ?>;">
                            <div id="whatsapp-preview-bubble" class="sweetaddons-wa-preview" style="position: absolute; <?php echo ($position === 'bottom-right') ? 'bottom: 20px; right: 20px;' : 'bottom: 20px; left: 20px;'; ?>">
                                <div id="whatsapp-preview-inner" style="display: flex; align-items: center; <?php echo ($bubble_style === 'extended') ? 'padding: 12px 20px;' : 'width: 60px; height: 60px; justify-content: center;'; ?> background: <?php echo esc_attr($color); ?>; border-radius: <?php echo ($bubble_style === 'extended') ? '25px' : '50%'; ?>; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);">
                                    <svg viewBox="0 0 24 24" width="24" height="24" style="<?php echo ($bubble_style === 'extended') ? 'margin-right: 8px;' : ''; ?>">
                                        <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                    </svg>
                                    <span id="whatsapp-preview-text" style="font-size: 14px; font-weight: 500; display: <?php echo ($bubble_style === 'extended') ? 'inline' : 'none'; ?>;"><?php echo esc_html($button_text); ?></span>
                                </div>
                            </div>
                        </div>

                        <div id="whatsapp-preview-placeholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #666; display: <?php echo ($enable && $phone) ? 'none' : 'block'; ?>;">
                            <p>Aktifkan WhatsApp dan tambahkan nomor telepon untuk melihat preview</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sad-top-right">
                <div class="sad-card">
                    <div class="sad-card-title">💾 Simpan Perubahan</div>
                    <div class="sad-actions-row" style="justify-content: flex-end;">
                        <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
                    </div>
                </div>

                <div class="sad-card">
                    <div class="sad-card-title">Ringkasan</div>
                    <table class="widefat striped" style="border:none; box-shadow:none;">
                        <thead>
                            <tr style="background-color: #f0f0f1;">
                                <th>Fitur</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Status Widget</td>
                                <td><span style="color: <?php echo $status_color; ?>; font-weight:bold;"><?php echo $status_text; ?></span></td>
                            </tr>
                            <tr>
                                <td>Target Number</td>
                                <td><?php echo esc_html($display_phone); ?></td>
                            </tr>
                            <tr>
                                <td>Position</td>
                                <td><?php echo esc_html($display_position); ?></td>
                            </tr>
                            <tr>
                                <td>Button Style</td>
                                <td><?php echo ucfirst($bubble_style); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        // Color picker sync
        $('#sweetaddons_whatsapp_color').on('change', function() {
            $(this).next('input[type="text"]').val($(this).val());
            updateWhatsAppPreview();
        });

        // Real-time preview update function
        function updateWhatsAppPreview() {
            const enable = $('#sweetaddons_whatsapp_enable').is(':checked');
            const phone = $('#sweetaddons_whatsapp_phone').val().trim();
            const button_text = $('#sweetaddons_whatsapp_button_text').val().trim() || 'Chat dengan kami';
            const position = $('#sweetaddons_whatsapp_position').val();
            const color = $('#sweetaddons_whatsapp_color').val();
            const size = $('#sweetaddons_whatsapp_size').val() || '60';
            const offset_x = $('#sweetaddons_whatsapp_offset_x').val() || '20';
            const offset_y = $('#sweetaddons_whatsapp_offset_y').val() || '20';
            const bubble_style = $('#sweetaddons_whatsapp_bubble_style').val();

            const $container = $('#whatsapp-preview-container');
            const $placeholder = $('#whatsapp-preview-placeholder');
            const $bubble = $('#whatsapp-preview-bubble');
            const $inner = $('#whatsapp-preview-inner');
            const $text = $('#whatsapp-preview-text');

            // Show/hide preview
            if (enable && phone) {
                $container.show();
                $placeholder.hide();

                // Update bubble position
                let positionStyle = '';
                switch (position) {
                    case 'bottom-right':
                        positionStyle = `bottom: ${offset_y}px; right: ${offset_x}px;`;
                        break;
                    case 'bottom-left':
                        positionStyle = `bottom: ${offset_y}px; left: ${offset_x}px;`;
                        break;
                    case 'top-right':
                        positionStyle = `top: ${offset_y}px; right: ${offset_x}px;`;
                        break;
                    case 'top-left':
                        positionStyle = `top: ${offset_y}px; left: ${offset_x}px;`;
                        break;
                    case 'center-right':
                        positionStyle = `top: 50%; transform: translateY(-50%); right: ${offset_x}px;`;
                        break;
                    case 'center-left':
                        positionStyle = `top: 50%; transform: translateY(-50%); left: ${offset_x}px;`;
                        break;
                }
                $bubble.attr('style', 'position: absolute; ' + positionStyle);

                // Update bubble style and content
                let innerStyle = '';
                if (bubble_style === 'extended') {
                    innerStyle = `display: flex; align-items: center; padding: 12px 20px; background: ${color}; border-radius: 25px; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);`;
                    $text.show().text(button_text);
                    $bubble.find('svg').css('margin-right', '8px');
                } else {
                    innerStyle = `width: ${size}px; height: ${size}px; display: flex; align-items: center; justify-content: center; background: ${color}; border-radius: 50%; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);`;
                    $text.hide();
                    $bubble.find('svg').css('margin-right', '0');
                }
                $inner.attr('style', innerStyle);

            } else {
                $container.hide();
                $placeholder.show();
            }
        }

        // Event listeners for all WhatsApp fields
        $('#sweetaddons_whatsapp_enable').on('change', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_phone').on('input', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_button_text').on('input', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_position').on('change', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_color').on('change', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_size').on('input', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_offset_x').on('input', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_offset_y').on('input', updateWhatsAppPreview);
        $('#sweetaddons_whatsapp_bubble_style').on('change', updateWhatsAppPreview);

        // Initialize preview on page load
        updateWhatsAppPreview();
    });
</script>