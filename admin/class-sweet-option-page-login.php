<?php

    public function login_customizer_page_callback()
    {
        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_login_customizer_settings')) {
            $login_data = array();

            if (isset($_POST['login_logo_url'])) {
                $login_data['logo_url'] = sanitize_text_field($_POST['login_logo_url']);
            }
            if (isset($_POST['login_bg_color'])) {
                $login_data['bg_color'] = sanitize_text_field($_POST['login_bg_color']);
            }
            if (isset($_POST['login_bg_image'])) {
                $login_data['bg_image'] = sanitize_text_field($_POST['login_bg_image']);
            }
            if (isset($_POST['login_btn_color'])) {
                $login_data['btn_color'] = sanitize_text_field($_POST['login_btn_color']);
            }
            if (isset($_POST['login_btn_text_color'])) {
                $login_data['btn_text_color'] = sanitize_text_field($_POST['login_btn_text_color']);
            }

            update_option('sweetaddons_login_customizer', $login_data);
            echo '<div class="sad-notice sad-notice-success"><p>✅ Pengaturan Login Page Customizer berhasil disimpan!</p></div>';
        }

        // Get current settings
        $login_settings = get_option('sweetaddons_login_customizer', array());
        $logo_url = isset($login_settings['logo_url']) ? $login_settings['logo_url'] : '';
        $bg_color = isset($login_settings['bg_color']) ? $login_settings['bg_color'] : '#f1f1f1';
        $bg_image = isset($login_settings['bg_image']) ? $login_settings['bg_image'] : '';
        $btn_color = isset($login_settings['btn_color']) ? $login_settings['btn_color'] : '#2271b1';
        $btn_text_color = isset($login_settings['btn_text_color']) ? $login_settings['btn_text_color'] : '#ffffff';
    ?>
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
                                        <input type="text" name="login_logo_url" id="login_logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text" />
                                        <button type="button" class="button button-secondary" id="upload_logo_button">Upload Logo</button>
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
                                        <input type="text" name="login_bg_image" id="login_bg_image" value="<?php echo esc_attr($bg_image); ?>" class="regular-text" />
                                        <button type="button" class="button button-secondary" id="upload_bg_button">Upload Background</button>
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
            jQuery(document).ready(function($){
                var mediaUploader;
                
                $('#upload_logo_button').click(function(e) {
                    e.preventDefault();
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }
                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: 'Pilih Logo',
                        button: {
                            text: 'Pilih Logo'
                        },
                        multiple: false
                    });
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#login_logo_url').val(attachment.url);
                    });
                    mediaUploader.open();
                });

                var bgUploader;
                $('#upload_bg_button').click(function(e) {
                    e.preventDefault();
                    if (bgUploader) {
                        bgUploader.open();
                        return;
                    }
                    bgUploader = wp.media.frames.file_frame = wp.media({
                        title: 'Pilih Background',
                        button: {
                            text: 'Pilih Background'
                        },
                        multiple: false
                    });
                    bgUploader.on('select', function() {
                        var attachment = bgUploader.state().get('selection').first().toJSON();
                        $('#login_bg_image').val(attachment.url);
                    });
                    bgUploader.open();
                });
            });
            </script>
        </div>
    <?php
    }
