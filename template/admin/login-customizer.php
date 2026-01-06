<style>
    .sad-drop-zone {
        border: 2px dashed #b4b9be;
        padding: 20px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
        border-radius: 4px;
    }

    .sad-drop-zone:hover,
    .sad-drop-zone.drag-over {
        border-color: #2271b1;
        background: #f0f6fc;
    }

    .sad-drop-zone img {
        max-width: 100%;
        height: auto;
        max-height: 150px;
        display: block;
        margin: 0 auto 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .sad-drop-zone .sad-placeholder {
        color: #646970;
        padding: 20px 0;
    }

    .sad-drop-zone .sad-remove {
        margin-top: 10px;
        color: #d63638;
        cursor: pointer;
        display: inline-block;
        font-size: 13px;
        text-decoration: underline;
    }
</style>

<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🎨 Login Page Customizer</h1>

    <form method="post" action="">
        <?php wp_nonce_field('sweetaddons_login_customizer_settings'); ?>

        <div class="sad-top">
            <div class="sad-top-left">
                <div class="sad-card">
                    <div class="sad-card-title">⚙️ Konfigurasi Tampilan</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Logo URL</th>
                            <td>
                                <div class="sad-drop-zone" id="logo-drop-zone">
                                    <input type="hidden" name="login_logo_url" id="login_logo_url" value="<?php echo esc_attr($logo_url); ?>">
                                    <div class="sad-preview">
                                        <?php if ($logo_url): ?>
                                            <img src="<?php echo esc_url($logo_url); ?>">
                                            <span class="sad-remove">Hapus Logo</span>
                                        <?php else: ?>
                                            <div class="sad-placeholder">
                                                <span class="dashicons dashicons-upload" style="font-size: 32px; width: 32px; height: 32px;"></span><br>
                                                <strong>Drag & Drop Logo</strong><br>atau klik untuk memilih
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="description">Upload atau masukkan URL logo untuk halaman login.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Warna Background</th>
                            <td>
                                <input type="color" name="login_bg_color" value="<?php echo esc_attr($bg_color); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Gambar Background</th>
                            <td>
                                <div class="sad-drop-zone" id="bg-drop-zone">
                                    <input type="hidden" name="login_bg_image" id="login_bg_image" value="<?php echo esc_attr($bg_image); ?>">
                                    <div class="sad-preview">
                                        <?php if ($bg_image): ?>
                                            <img src="<?php echo esc_url($bg_image); ?>">
                                            <span class="sad-remove">Hapus Background</span>
                                        <?php else: ?>
                                            <div class="sad-placeholder">
                                                <span class="dashicons dashicons-upload" style="font-size: 32px; width: 32px; height: 32px;"></span><br>
                                                <strong>Drag & Drop Background</strong><br>atau klik untuk memilih
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <p class="description">Upload atau masukkan URL gambar background.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Warna Tombol</th>
                            <td>
                                <input type="color" name="login_btn_color" value="<?php echo esc_attr($btn_color); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Warna Teks Tombol</th>
                            <td>
                                <input type="color" name="login_btn_text_color" value="<?php echo esc_attr($btn_text_color); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="sad-top-right">
                <div class="sad-card">
                    <div class="sad-card-title">💾 Simpan Perubahan</div>
                    <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                </div>
            </div>
        </div>
    </form>

    <script>
        jQuery(document).ready(function($) {

            // Reusable function for handling drop zones
            function initDropZone(zoneId, inputId) {
                var $zone = $('#' + zoneId);
                var $input = $('#' + inputId);

                // Click to open media frame
                $zone.on('click', function(e) {
                    if ($(e.target).hasClass('sad-remove')) {
                        e.preventDefault();
                        e.stopPropagation();
                        $input.val('');
                        renderPreview($zone, '');
                        return;
                    }

                    e.preventDefault();

                    var frame = wp.media({
                        title: 'Pilih Gambar',
                        button: {
                            text: 'Gunakan Gambar Ini'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $input.val(attachment.url);
                        renderPreview($zone, attachment.url);
                    });

                    frame.open();
                });

                // Drag & Drop events
                $zone.on('dragover dragenter', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $zone.addClass('drag-over');
                });

                $zone.on('dragleave dragend drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $zone.removeClass('drag-over');
                });

                $zone.on('drop', function(e) {
                    var files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        uploadFile(files[0], $zone, $input);
                    }
                });
            }

            function renderPreview($zone, url) {
                var html = '';
                if (url) {
                    html = '<img src="' + url + '"><span class="sad-remove">Hapus Gambar</span>';
                } else {
                    html = '<div class="sad-placeholder"><span class="dashicons dashicons-upload" style="font-size: 32px; width: 32px; height: 32px;"></span><br><strong>Drag & Drop Gambar</strong><br>atau klik untuk memilih</div>';
                }
                $zone.find('.sad-preview').html(html);
            }

            function uploadFile(file, $zone, $input) {
                // Show loading state
                $zone.find('.sad-preview').html('<div class="sad-placeholder">Mengupload...</div>');

                var formData = new FormData();
                formData.append('action', 'upload-attachment');
                formData.append('async-upload', file);
                formData.append('name', file.name);
                formData.append('_wpnonce', '<?php echo $upload_nonce; ?>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            // WordPress AJAX response handling
                            var res = typeof response === 'string' ? JSON.parse(response) : response;

                            if (res.success) {
                                var attachment = res.data;
                                var url = attachment.url;
                                $input.val(url);
                                renderPreview($zone, url);
                            } else {
                                // Fallback logic for some WP versions
                                if (res.data && res.data.url) {
                                    $input.val(res.data.url);
                                    renderPreview($zone, res.data.url);
                                } else {
                                    alert('Upload gagal: ' + (res.data.message || 'Unknown error'));
                                    renderPreview($zone, $input.val());
                                }
                            }
                        } catch (e) {
                            console.error('Upload Error:', e);
                            alert('Terjadi kesalahan saat upload.');
                            renderPreview($zone, $input.val());
                        }
                    },
                    error: function() {
                        alert('Upload gagal. Silakan coba lagi.');
                        renderPreview($zone, $input.val());
                    }
                });
            }

            initDropZone('logo-drop-zone', 'login_logo_url');
            initDropZone('bg-drop-zone', 'login_bg_image');
        });
    </script>
</div>
