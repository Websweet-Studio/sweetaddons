<?php

/**
 * The Umum (General) settings page functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 */

class Sweet_Option_Umum
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_submenu_page()
    {
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Umum',                     // Page title
            'Umum',                     // Menu title
            'manage_options',           // Capability
            'Sweetaddons_umum',        // Menu slug
            array($this, 'umum_page_callback') // Callback function
        );
    }

    public function register_settings()
    {
        register_setting('Sweetaddons_umum_group', 'fully_disable_comment');
        register_setting('Sweetaddons_umum_group', 'hide_admin_notice');
        register_setting('Sweetaddons_umum_group', 'disable_gutenberg');
        register_setting('Sweetaddons_umum_group', 'classic_widget_Sweetaddons');
        register_setting('Sweetaddons_umum_group', 'remove_slug_category_Sweetaddons');
    }

    public function field($data)
    {
        $type   = isset($data['type']) ? $data['type'] : '';
        $id     = isset($data['id']) ? $data['id'] : '';
        $std    = isset($data['std']) ? $data['std'] : '';
        $step   = isset($data['step']) ? $data['step'] : '';
        $value  = get_option($id, $std);
        $name   = $id;

        if (isset($data['sub']) && !empty($data['sub'])) {
            $sub    = $data['sub'];
            $value  = isset($value[$sub]) ? $value[$sub] : '';
            $name   = $id . '[' . $sub . ']';
        }

        if ($std && empty($value) && $type != 'checkbox') {
            $value = $std;
        }

        if ($type == 'checkbox') {
            $checked = ($value == 1) ? 'checked' : '';
            echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . $checked . '> ';
        }
        if ($type == 'text') {
            echo '<div><input type="text" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }
        if ($type == 'password') {
            echo '<div><input type="password" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }
        if ($type == 'number') {
            echo '<div><input type="number" step="' . $step . '" min="0" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="small-text"></div>';
        }
        if ($type == 'textarea') {
            echo '<div>';
            echo '<textarea id="' . $id . '" name="' . $name . '" rows="6" cols="50" class="large-text">';
            echo $value;
            echo '</textarea>';
            echo '</div>';
        }

        if (isset($data['label']) && !empty($data['label'])) {
            echo '<label for="' . $id . '">';
            echo '<small>' . $data['label'] . '</small>';
            echo '</label>';
        }

        if (isset($data['desc']) && !empty($data['desc'])) {
            echo '<div>';
            echo '<small>' . $data['desc'] . '</small>';
            echo '</div>';
        }
    }

    public function save_button()
    {
        echo '<button type="submit" name="submit" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;" onmouseenter="this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 4px 12px rgba(37,99,235,0.4)\';" onmouseleave="this.style.transform=\'translateY(0)\';this.style.boxShadow=\'0 2px 6px rgba(37,99,235,0.25)\';"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>Simpan Pengaturan</button>';
    }

    public function umum_page_callback()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
?>
        <?php
        $subnav = Sweetaddons_Admin_Layout::get_umum_subnav();
        Sweetaddons_Admin_Layout::open('Umum', 'Sweetaddons_umum', $subnav);

        if ($current_tab === 'customlogin') {
            $this->render_customlogin_tab();
        } else {
            $this->render_general_tab();
        }

        Sweetaddons_Admin_Layout::close();
    }

    private function render_general_tab()
    {
        $umum_fields = [
            [
                'id'    => 'fully_disable_comment',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan Komentar',
                'std'   => 1,
                'label' => 'Nonaktifkan fitur komentar pada situs.',
            ],
            [
                'id'    => 'hide_admin_notice',
                'type'  => 'checkbox',
                'title' => 'Sembunyikan Pemberitahuan Admin',
                'std'   => 0,
                'label' => 'Sembunyikan pemberitahuan admin di halaman admin. Pemberitahuan admin seringkali muncul untuk memberikan informasi atau peringatan kepada admin situs.',
            ],
            [
                'id'    => 'disable_gutenberg',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan Gutenberg',
                'std'   => 0,
                'label' => 'Aktifkan untuk menggunakan editor klasik WordPress menggantikan Gutenberg.',
            ],
            [
                'id'    => 'classic_widget_Sweetaddons',
                'type'  => 'checkbox',
                'title' => 'Widget Klasik',
                'std'   => 1,
                'label' => 'Aktifkan untuk menggunakan widget klasik.',
            ],
            [
                'id'    => 'remove_slug_category_Sweetaddons',
                'type'  => 'checkbox',
                'title' => 'Hapus Slug Kategori',
                'std'   => 0,
                'label' => 'Aktifkan untuk hapus slug /category/ dari URL.',
            ],
        ];
        ?>
        <form method="post" action="options.php" class="sad-form">
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card" style="margin-bottom: 16px;">
                        <div class="sad-card-title">Pengaturan Utama</div>
                        <?php settings_fields('Sweetaddons_umum_group'); ?>
                        <?php do_settings_sections('Sweetaddons_umum_group'); ?>

                        <table class="form-table">
                            <?php
                            foreach ($umum_fields as $data) :
                                echo '<tr>';
                                echo '<th scope="row">';
                                echo $data['title'];
                                echo '</th>';
                                echo '<td>';
                                $this->field($data);
                                echo '</td>';
                                echo '</tr>';
                            endforeach;
                            ?>
                        </table>
                    </div>
                    <div class="sad-card">
                        <div class="sad-card-title">Update Plugin</div>
                        <?php
                        $checked = isset($_GET['sweetaddons_update_check']) ? sanitize_text_field(wp_unslash($_GET['sweetaddons_update_check'])) : '';
                        if ($checked === '1') {
                            $has_update = isset($_GET['sweetaddons_has_update']) ? sanitize_text_field(wp_unslash($_GET['sweetaddons_has_update'])) : '0';
                            echo '<div class="sad-notice sad-notice-success"><p>';
                            echo $has_update === '1' ? 'Cek update selesai. Update tersedia di halaman Plugins.' : 'Cek update selesai. Tidak ada update terbaru.';
                            echo '</p></div>';
                        }

                        $check_url = wp_nonce_url(
                            admin_url('admin-post.php?action=sweetaddons_check_update'),
                            'sweetaddons_check_update'
                        );
                        ?>
                        <div class="sad-stack" style="gap: 6px; margin-bottom: 12px;">
                            <div>Versi saat ini: <strong><?php echo defined('SWEETADDONS_VERSION') ? esc_html(SWEETADDONS_VERSION) : ''; ?></strong></div>
                            <div><small>Cek update akan mengambil versi terbaru dari GitHub Releases.</small></div>
                        </div>
                        <a href="<?php echo esc_url($check_url); ?>" class="button button-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:5px;margin-top:-2px;width:14px;height:14px;">
                                <path d="M12 13v8l-4-4" />
                                <path d="m12 21 4-4" />
                                <path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284" />
                            </svg>Cek Update</a>
                    </div>
                </div>

                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <?php $this->save_button(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php
    }

    private function render_customlogin_tab()
    {
        // Enqueue media scripts
        wp_enqueue_media();
        $upload_nonce = wp_create_nonce('media-form');

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
        }

        // Get current settings
        $login_settings = get_option('sweetaddons_login_customizer', array());
        $logo_url = isset($login_settings['logo_url']) ? $login_settings['logo_url'] : '';
        if (empty($logo_url) && function_exists('get_site_icon_url')) {
            $site_icon = get_site_icon_url(270);
            if ($site_icon) {
                $logo_url = $site_icon;
            }
        }
        $bg_color = isset($login_settings['bg_color']) ? $login_settings['bg_color'] : '#f1f1f1';
        $bg_image = isset($login_settings['bg_image']) ? $login_settings['bg_image'] : '';
        $btn_color = isset($login_settings['btn_color']) ? $login_settings['btn_color'] : '#2271b1';
        $btn_text_color = isset($login_settings['btn_text_color']) ? $login_settings['btn_text_color'] : '#ffffff';
?>
        <?php
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_login_customizer_settings')) {
            echo '<div class="sad-notice sad-notice-success"><p>Pengaturan Login Page berhasil disimpan.</p></div>';
        }
        ?>

        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_login_customizer_settings'); ?>

            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card">
                        <div class="sad-card-title">Konfigurasi Tampilan</div>
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
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <?php $this->save_button(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            jQuery(document).ready(function($) {

                function initDropZone(zoneId, inputId) {
                    var $zone = $('#' + zoneId);
                    var $input = $('#' + inputId);

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
                                var res = typeof response === 'string' ? JSON.parse(response) : response;

                                if (res.success) {
                                    var attachment = res.data;
                                    var url = attachment.url;
                                    $input.val(url);
                                    renderPreview($zone, url);
                                } else {
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
<?php
    }

}
