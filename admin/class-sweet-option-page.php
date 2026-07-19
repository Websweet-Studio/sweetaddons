<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */

class Custom_Admin_Option_Page
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts($hook)
    {
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $is_sweetaddons_page = stripos($hook, 'sweetaddons') !== false
            || stripos($hook, 'custom_admin_options') !== false
            || stripos($page, 'sweetaddons') !== false
            || $page === 'custom_admin_options';

        if ($is_sweetaddons_page) {
            wp_enqueue_script('jquery');
            if ($page === 'custom_admin_options' || $page === 'Sweetaddons_visitor_stats') {
                wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null);
            }
        }
    }

    public function add_options_page()
    {
        $plugin_name = class_exists('Sweetaddons_WhiteLabel') ? Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name') : 'Sweet Addons';
        $menu_title = class_exists('Sweetaddons_WhiteLabel') ? Sweetaddons_WhiteLabel::get_white_labeled_info('menu_title') : 'Sweet Addons';

        add_menu_page(
            $plugin_name,       // Judul halaman
            $menu_title,       // Judul menu
            'manage_options',           // Hak akses yang dibutuhkan
            'custom_admin_options',     // Slug menu
            array($this, 'options_page_callback'), // Callback untuk halaman pengaturan
            'dashicons-admin-plugins',                    // URL icon (biarkan kosong atau tambahkan URL icon)
            30                         // Posisi menu (semakin kecil angkanya semakin tinggi posisinya)
        );

        // Add Proteksi submenu (main)
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Proteksi',            // Page title
            'Proteksi',            // Menu title
            'manage_options',           // Capability
            'Sweetaddons_protect',        // Menu slug
            array($this, 'spam_page_callback') // Callback function
        );

        // Add visitor statistics submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Statistik Pengunjung',     // Page title
            'Statistik Pengunjung',     // Menu title
            'manage_options',           // Capability
            'Sweetaddons_visitor_stats', // Menu slug
            array($this, 'visitor_stats_page_callback') // Callback function
        );

        // Add SEO submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Pengaturan SEO',           // Page title
            'Pengaturan SEO',           // Menu title
            'manage_options',           // Capability
            'Sweetaddons_seo',         // Menu slug
            array($this, 'seo_page_callback') // Callback function
        );

        // Add WhatsApp submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Chat WhatsApp',            // Page title
            'Chat WhatsApp',            // Menu title
            'manage_options',           // Capability
            'Sweetaddons_whatsapp',    // Menu slug
            array($this, 'whatsapp_page_callback') // Callback function
        );
    }


    public function register_settings()
    {
        register_setting('custom_admin_options_group', 'fully_disable_comment');
        register_setting('custom_admin_options_group', 'hide_admin_notice');
        register_setting('custom_admin_options_group', 'limit_login_attempts');
        register_setting('custom_admin_options_group', 'maintenance_mode');
        register_setting('custom_admin_options_group', 'maintenance_mode_data');
        register_setting('custom_admin_options_group', 'license_key');
        register_setting('custom_admin_options_group', 'auto_resize_mode');
        register_setting('custom_admin_options_group', 'auto_resize_mode_data');
        register_setting('custom_admin_options_group', 'disable_xmlrpc');
        register_setting('custom_admin_options_group', 'disable_rest_api');
        register_setting('custom_admin_options_group', 'disable_gutenberg');
        register_setting('custom_admin_options_group', 'block_wp_login');
        register_setting('custom_admin_options_group', 'whitelist_block_wp_login');
        register_setting('custom_admin_options_group', 'redirect_to');
        // register_setting('custom_admin_options_group', 'standar_editor_Sweetaddons');
        register_setting('custom_admin_options_group', 'classic_widget_Sweetaddons');
        register_setting('custom_admin_options_group', 'remove_slug_category_Sweetaddons');
        register_setting('custom_admin_options_group', 'auto_resize_image_Sweetaddons');
        register_setting('custom_admin_options_group', 'captcha_Sweetaddons');
        register_setting('custom_admin_options_group', 'news_generate');

        // SEO settings
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_home_title');
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_home_description');
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_default_og_image');
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_twitter_site');

        // reCaptcha settings
        register_setting('sweetaddons_recaptcha_group', 'captcha_Sweetaddons');

        // White Label settings
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_plugin_name');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_plugin_uri');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_description');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_author');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_author_uri');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_menu_title');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_hide_original');

        // WhatsApp settings
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_enable');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_message');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_button_text');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_position');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_color');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_mobile');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_desktop');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_animation');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_bubble_style');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_tooltip');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_text_mobile');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_agents');
    }

    public function field($data)
    {
        $type   = isset($data['type']) ? $data['type'] : '';
        $id     = isset($data['id']) ? $data['id'] : '';
        $std    = isset($data['std']) ? $data['std'] : '';
        $step   = isset($data['step']) ? $data['step'] : '';
        $value  = get_option($id, $std);
        $name   = $id;

        // jika ada sub, sub array dari Value
        if (isset($data['sub']) && !empty($data['sub'])) {
            $sub    = $data['sub'];
            $value  = isset($value[$sub]) ? $value[$sub] : '';
            $name   = $id . '[' . $sub . ']';
        }

        if ($std && empty($value) && $type != 'checkbox') {
            $value = $std;
        }

        //jika field checkbox
        if ($type == 'checkbox') {
            $checked = ($value == 1) ? 'checked' : '';
            echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . $checked . '> ';
        }
        //jika field text
        if ($type == 'text') {
            echo '<div><input type="text" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }

        if ($type == 'password') {
            echo '<div><input type="password" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }

        //jika field number
        if ($type == 'number') {
            echo '<div><input type="number" step="' . $step . '" min="0" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="small-text"></div>';
        }
        //jika field textarea
        if ($type == 'textarea') {
            echo '<div>';
            echo '<textarea id="' . $id . '" name="' . $name . '" rows="6" cols="50" class="large-text">';
            echo $value;
            echo '</textarea>';
            echo '</div>';
        }

        ///tampil label
        if (isset($data['label']) && !empty($data['label'])) {
            echo '<label for="' . $id . '">';
            echo '<small>' . $data['label'] . '</small>';
            echo '</label>';
        }

        ///tampil deskripsi
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

    public function spam_page_callback()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'protect';
?>
        <?php
        $subnav = Sweetaddons_Admin_Layout::get_proteksi_subnav();
        Sweetaddons_Admin_Layout::open('Proteksi', 'Sweetaddons_protect', $subnav);

        switch ($current_tab) {
            case 'maintenance':
                $this->render_maintenance_tab();
                break;
            case 'recaptcha':
                $this->render_recaptcha_tab();
                break;
            case 'block':
                $this->render_block_tab();
                break;
            case 'whitelabel':
                $this->render_whitelabel_tab();
                break;
            default:
                $this->render_protect_tab();
        }

        Sweetaddons_Admin_Layout::close();
    }

    private function render_protect_tab()
    {
        $spam_fields = [
            [
                'id'    => 'limit_login_attempts',
                'type'  => 'checkbox',
                'title' => 'Batasi Percobaan Login',
                'std'   => 1,
                'label' => 'Batasi jumlah percobaan login yang diizinkan untuk pengguna, ketika pengguna melakukan percobaan login yang melebihi 5X dalam 24 Jam, mereka akan diblokir untuk sementara waktu sebagai tindakan keamanan.',
            ],
            [
                'id'    => 'disable_xmlrpc',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan XML-RPC',
                'std'   => 1,
                'label' => 'Nonaktifkan protokol XML-RPC pada situs. XML-RPC digunakan oleh beberapa aplikasi atau layanan pihak ketiga untuk berinteraksi dengan situs WordPress.',
            ],
            [
                'id'    => 'disable_rest_api',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan REST API / JSON',
                'std'   => 0,
                'label' => 'Nonaktifkan akses ke REST API untuk keperluan keamanan atau privasi.',
            ],
        ];
        ?>
        <form method="post" action="options.php" class="sad-form">
            <?php settings_fields('custom_admin_options_group'); ?>
            <?php do_settings_sections('custom_admin_options_group'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title">Pengaturan Utama</div>
                        <table class="form-table">
                            <?php
                            foreach ($spam_fields as $data) :
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

    private function render_maintenance_tab()
    {
        $maintenance_fields = [
            [
                'id'    => 'maintenance_mode',
                'type'  => 'checkbox',
                'title' => 'Mode Maintenance',
                'std'   => 0,
                'label' => 'Aktifkan mode maintenance untuk situs Anda.',
            ],
            [
                'id'    => 'maintenance_mode_data',
                'sub'   => 'title',
                'type'  => 'text',
                'title' => 'Judul Halaman',
                'std'   => 'Segera Kembali',
            ],
            [
                'id'    => 'maintenance_mode_data',
                'sub'   => 'body',
                'type'  => 'textarea',
                'title' => 'Isi Pesan',
                'std'   => 'Kami sedang melakukan perawatan sistem. Silakan kembali lagi nanti.',
            ]
        ];
    ?>
        <form method="post" action="options.php" class="sad-form">
            <?php settings_fields('Sweetaddons_maintenance_group'); ?>
            <?php do_settings_sections('Sweetaddons_maintenance_group'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title">Pengaturan Maintenance</div>
                        <table class="form-table">
                            <?php
                            foreach ($maintenance_fields as $data) :
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

    private function render_recaptcha_tab()
    {
        if (isset($_POST['submit'])) {
            check_admin_referer('sweetaddons_recaptcha_settings');
            $difficulty = isset($_POST['captcha_difficulty']) ? sanitize_key(wp_unslash($_POST['captcha_difficulty'])) : 'medium';
            if (!in_array($difficulty, array('easy', 'medium', 'hard'), true)) {
                $difficulty = 'medium';
            }
            $captcha_data = array(
                'aktif'      => isset($_POST['captcha_aktif']) ? '1' : '',
                'login'      => isset($_POST['captcha_login']) ? '1' : '',
                'comment'    => isset($_POST['captcha_comment']) ? '1' : '',
                'register'   => isset($_POST['captcha_register']) ? '1' : '',
                'difficulty' => $difficulty,
            );
            update_option('captcha_Sweetaddons', $captcha_data);
        }

        $captcha_settings = get_option('captcha_Sweetaddons', array());
        $aktif = isset($captcha_settings['aktif']) ? $captcha_settings['aktif'] : '';
        $login = isset($captcha_settings['login']) ? $captcha_settings['login'] : '';
        $comment = isset($captcha_settings['comment']) ? $captcha_settings['comment'] : '';
        $register = isset($captcha_settings['register']) ? $captcha_settings['register'] : '';
        $difficulty = isset($captcha_settings['difficulty']) ? $captcha_settings['difficulty'] : 'medium';
    ?>
        <?php if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_recaptcha_settings')) : ?>
            <div class="sad-notice sad-notice-success">
                <p>Pengaturan CAPTCHA berhasil disimpan.</p>
            </div>
        <?php endif; ?>
        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_recaptcha_settings'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card sad-mb-16" id="recaptcha-general-settings">
                        <div class="sad-card-title">Konfigurasi Utama</div>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Status Fitur</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="captcha_aktif" value="1" <?php checked($aktif, '1'); ?> />
                                        Aktifkan CAPTCHA
                                    </label>

                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Tingkat Kesulitan</th>
                                <td>
                                    <select name="captcha_difficulty" id="captcha_difficulty">
                                        <option value="easy" <?php selected($difficulty, 'easy'); ?>>Mudah (4 Angka)</option>
                                        <option value="medium" <?php selected($difficulty, 'medium'); ?>>Sedang (5 Karakter)</option>
                                        <option value="hard" <?php selected($difficulty, 'hard'); ?>>Sulit (6 Karakter + Noise)</option>
                                    </select>
                                    <p class="description">Atur kompleksitas kode dan visual CAPTCHA.</p>
                                    <div id="captcha-preview-container" style="margin-top: 15px;">
                                        <strong>Preview:</strong><br>
                                        <img id="captcha-preview-img" src="<?php echo add_query_arg(array('sweetaddons_captcha' => 'preview', 'difficulty' => $difficulty), home_url('/')); ?>" alt="Captcha Preview" style="border:1px solid #d0d4d9; height:50px; width:160px; background:#f5f6fa; border-radius:4px; margin-top: 5px;">
                                        <p><a href="#" id="refresh-captcha-preview" class="button button-small">Refresh Preview</a></p>
                                    </div>
                                    <script>
                                        jQuery(document).ready(function($) {
                                            function updateCaptchaPreview() {
                                                var difficulty = $('#captcha_difficulty').val();
                                                var src = '<?php echo home_url('/'); ?>?sweetaddons_captcha=preview&difficulty=' + difficulty + '&t=' + new Date().getTime();
                                                $('#captcha-preview-img').attr('src', src);
                                            }
                                            $('#captcha_difficulty').on('change', function() {
                                                updateCaptchaPreview();
                                            });
                                            $('#refresh-captcha-preview').on('click', function(e) {
                                                e.preventDefault();
                                                updateCaptchaPreview();
                                            });
                                        });
                                    </script>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="sad-top-right">
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title">Lokasi Aktif</div>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Form Login</th>
                                <td><label><input type="checkbox" name="captcha_login" value="1" <?php checked($login, '1'); ?> /> Aktifkan</label></td>
                            </tr>
                            <tr>
                                <th scope="row">Form Komentar</th>
                                <td><label><input type="checkbox" name="captcha_comment" value="1" <?php checked($comment, '1'); ?> /> Aktifkan</label></td>
                            </tr>
                            <tr>
                                <th scope="row">Form Registrasi</th>
                                <td><label><input type="checkbox" name="captcha_register" value="1" <?php checked($register, '1'); ?> /> Aktifkan</label></td>
                            </tr>
                        </table>
                    </div>
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

    private function render_block_tab()
    {
        $block_fields = [
            [
                'id'    => 'block_wp_login',
                'type'  => 'checkbox',
                'title' => 'Blokir wp-login.php',
                'std'   => 0,
                'label' => 'Aktifkan pemblokiran akses ke file wp-login.php pada situs.',
            ],
            [
                'id'    => 'whitelist_block_wp_login',
                'type'  => 'text',
                'title' => 'IP Whitelist',
                'std'   => '',
                'label' => 'Daftar IP yang dikecualikan (pisahkan dengan koma).',
            ],
            [
                'id'    => 'redirect_to',
                'type'  => 'text',
                'title' => 'Redirect URL',
                'std'   => 'http://127.0.0.1',
                'label' => 'Tujuan redirect jika diblokir.',
            ],
        ];
    ?>
        <form method="post" action="options.php" class="sad-form">
            <?php settings_fields('Sweetaddons_block_group'); ?>
            <?php do_settings_sections('Sweetaddons_block_group'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title">Pengaturan Utama</div>
                        <table class="form-table">
                            <?php
                            foreach ($block_fields as $data) :
                                echo '<tr>';
                                echo '<th scope="row">' . $data['title'] . '</th>';
                                echo '<td>';
                                $this->field($data);
                                echo '</td>';
                                echo '</tr>';
                            endforeach;
                            ?>
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
    <?php
    }

    public function options_page_callback()
    {
    ?>
        <?php Sweetaddons_Admin_Layout::open('Dashboard', 'custom_admin_options'); ?>
        <div class="sad-top sad-top--dashboard">
            <?php
            global $wpdb;
            $prefix = $wpdb->prefix;
            $today = $wpdb->get_row("SELECT unique_visitors as uv, total_pageviews as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date = CURDATE()");
            $this_week = $wpdb->get_row("SELECT SUM(unique_visitors) as uv, SUM(total_pageviews) as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
            $this_month = $wpdb->get_row("SELECT SUM(unique_visitors) as uv, SUM(total_pageviews) as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $daily_stats = $wpdb->get_results($wpdb->prepare("SELECT stat_date as visit_date, unique_visitors as unique_visits, total_pageviews as total_visits FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY) ORDER BY stat_date ASC", 30));
            $site_url = get_site_url();
            $site_name = get_bloginfo('name');
            $admin_email = get_option('admin_email');
            $wp_version = get_bloginfo('version');
            $php_version = phpversion();
            $memory_limit = ini_get('memory_limit');
            $max_execution_time = ini_get('max_execution_time');
            $qc_content = '';
            if (class_exists('Sweetaddons_Maintenance_Mode')) {
                global $sweet_maintenance_mode;
                if ($sweet_maintenance_mode instanceof Sweetaddons_Maintenance_Mode) {
                    $qc_content = $sweet_maintenance_mode->qc_maintenance();
                } else {
                    $qc_content = (new Sweetaddons_Maintenance_Mode())->qc_maintenance();
                }
            }
            ?>
            <div class="sad-top-left sad-stack">
                <div class="sad-row sad-row--stats">
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Hari Ini</div>
                        <div class="sad-card-value"><?php echo number_format($today ? (int)$today->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan &bull; Pengunjung: <?php echo number_format($today ? (int)$today->uv : 0); ?></div>
                    </div>
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Minggu Ini</div>
                        <div class="sad-card-value"><?php echo number_format($this_week ? (int)$this_week->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan &bull; Pengunjung: <?php echo number_format($this_week ? (int)$this_week->uv : 0); ?></div>
                    </div>
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Bulan Ini</div>
                        <div class="sad-card-value"><?php echo number_format($this_month ? (int)$this_month->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan &bull; Pengunjung: <?php echo number_format($this_month ? (int)$this_month->uv : 0); ?></div>
                    </div>
                </div>
                <div class="sad-card sad-card--chart">
                    <div class="sad-card-title">Grafik 30 Hari Terakhir</div>
                    <div class="sad-chartbox sad-chartbox--dashboard">
                        <canvas id="sadThirtyChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="sad-top-right sad-stack">
                <div class="sad-card sad-card--health">
                    <div class="sad-card-title">System Health</div>
                    <div class="sad-chips">
                        <span class="sad-chip">PHP <?php echo esc_html($php_version); ?></span>
                        <span class="sad-chip">Memory <?php echo esc_html($memory_limit); ?></span>
                        <span class="sad-chip">Max Exec <?php echo esc_html($max_execution_time); ?>s</span>
                    </div>
                </div>
                <div class="sad-card sad-card--site">
                    <div class="sad-card-title">Informasi Situs</div>
                    <table class="widefat striped sad-widefat sad-widefat--plain">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nama</td>
                                <td><?php echo esc_html($site_name); ?></td>
                            </tr>
                            <tr>
                                <td>URL</td>
                                <td><a href="<?php echo esc_url($site_url); ?>" target="_blank"><?php echo esc_url($site_url); ?></a></td>
                            </tr>
                            <tr>
                                <td>Email Admin</td>
                                <td><?php echo esc_html($admin_email); ?></td>
                            </tr>
                            <tr>
                                <td>WordPress</td>
                                <td><?php echo esc_html($wp_version); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="sad-grid">
            <div class="sad-card sad-card--qc">
                <div class="sad-card-title">QC</div>
                <?php if (!empty(trim(wp_strip_all_tags($qc_content)))) : ?>
                    <div class="sad-qc-list">
                        <?php echo wp_kses_post($qc_content); ?>
                    </div>
                <?php else : ?>
                    <p class="sad-qc-empty">Tidak ada catatan QC saat ini.</p>
                <?php endif; ?>
            </div>
        </div>
        <script>
            (function() {
                var data = <?php echo json_encode(array_map(function ($stat) {
                                return array(
                                    'date' => $stat->visit_date,
                                    'unique' => (int)$stat->unique_visits,
                                    'total' => (int)$stat->total_visits
                                );
                            }, $daily_stats ?: array())); ?>;
                var labels = data.map(function(i) {
                    return i.date;
                });
                var uniqueData = data.map(function(i) {
                    return i.unique;
                });
                var totalData = data.map(function(i) {
                    return i.total;
                });
                var ctx = document.getElementById('sadThirtyChart');
                if (ctx && window.Chart) {
                    var dCtx = ctx.getContext('2d');

                    var dGrad1 = dCtx.createLinearGradient(0, 0, 0, 280);
                    dGrad1.addColorStop(0, 'rgba(0, 102, 204, 0.35)');
                    dGrad1.addColorStop(1, 'rgba(0, 102, 204, 0.0)');
                    var dGrad2 = dCtx.createLinearGradient(0, 0, 0, 280);
                    dGrad2.addColorStop(0, 'rgba(41, 151, 255, 0.25)');
                    dGrad2.addColorStop(1, 'rgba(41, 151, 255, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Pengunjung Unik',
                                    data: uniqueData,
                                    borderColor: '#0066cc',
                                    backgroundColor: dGrad1,
                                    borderWidth: 2.5,
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#0066cc',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#0066cc',
                                    pointHoverBorderWidth: 3,
                                },
                                {
                                    label: 'Total Kunjungan',
                                    data: totalData,
                                    borderColor: '#2997ff',
                                    backgroundColor: dGrad2,
                                    borderWidth: 2.5,
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#2997ff',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#2997ff',
                                    pointHoverBorderWidth: 3,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    },
                                    ticks: {
                                        padding: 10,
                                        font: {
                                            size: 11
                                        }
                                    },
                                    border: {
                                        dash: [4, 4],
                                        display: false
                                    },
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        padding: 8,
                                        font: {
                                            size: 10
                                        },
                                        maxTicksLimit: 12
                                    },
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 24,
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(20,20,30,0.92)',
                                    titleFont: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 11
                                    },
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    boxPadding: 4,
                                }
                            }
                        }
                    });
                }
            })();
        </script>
        <?php Sweetaddons_Admin_Layout::close(); ?>
    <?php
    }

    public function generate_website_report()
    {
        ob_start();

        // Get site information
        $site_url = get_site_url();
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        $wp_version = get_bloginfo('version');
        $theme = wp_get_theme();
        $admin_email = get_option('admin_email');

        // Get user counts
        $user_count = count_users();
        $total_users = $user_count['total_users'];

        // Get post counts
        $post_counts = wp_count_posts();
        $published_posts = $post_counts->publish;
        $draft_posts = $post_counts->draft;

        // Get page counts
        $page_counts = wp_count_posts('page');
        $published_pages = $page_counts->publish;

        // Get plugin information
        $active_plugins = get_option('active_plugins');
        $all_plugins = get_plugins();
        $active_plugin_count = count($active_plugins);
        $total_plugin_count = count($all_plugins);

        // Get theme information
        $theme_name = $theme->get('Name');
        $theme_version = $theme->get('Version');

        // Get database information
        global $wpdb;
        $db_size = $wpdb->get_var("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema='{$wpdb->dbname}'");

        // Get server information
        $php_version = phpversion();
        $max_execution_time = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');

    ?>
        <div class="websweet-report-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">

            <!-- Site Information -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;">Ã°Å¸Å’Â Informasi Website</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>Nama Website:</strong></td>
                        <td><?php echo esc_html($site_name); ?></td>
                    </tr>
                    <tr>
                        <td><strong>URL:</strong></td>
                        <td><a href="<?php echo esc_url($site_url); ?>" target="_blank"><?php echo esc_url($site_url); ?></a></td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi:</strong></td>
                        <td><?php echo esc_html($site_description); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email Admin:</strong></td>
                        <td><?php echo esc_html($admin_email); ?></td>
                    </tr>
                    <tr>
                        <td><strong>WordPress Version:</strong></td>
                        <td><?php echo esc_html($wp_version); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Content Statistics -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;">Ã°Å¸â€œÂ Statistik Konten</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>Posts Terpublikasi:</strong></td>
                        <td><?php echo esc_html($published_posts); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Draft Posts:</strong></td>
                        <td><?php echo esc_html($draft_posts); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pages Terpublikasi:</strong></td>
                        <td><?php echo esc_html($published_pages); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Pengguna:</strong></td>
                        <td><?php echo esc_html($total_users); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Theme & Plugin Information -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;"> Theme & Plugin</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>Active Theme:</strong></td>
                        <td><?php echo esc_html($theme_name); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Theme Version:</strong></td>
                        <td><?php echo esc_html($theme_version); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Active Plugins:</strong></td>
                        <td><?php echo esc_html($active_plugin_count); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Plugin:</strong></td>
                        <td><?php echo esc_html($total_plugin_count); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Server Information -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;">Ã°Å¸â€“Â¥Ã¯Â¸Â Server Information</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>PHP Version:</strong></td>
                        <td><?php echo esc_html($php_version); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Memory Limit:</strong></td>
                        <td><?php echo esc_html($memory_limit); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Max Execution Time:</strong></td>
                        <td><?php echo esc_html($max_execution_time); ?>s</td>
                    </tr>
                    <tr>
                        <td><strong>Ukuran Database:</strong></td>
                        <td><?php echo esc_html($db_size); ?> MB</td>
                    </tr>
                </table>
            </div>

            <!-- Sweet Addons Status -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;"><?php echo class_exists('Sweetaddons_WhiteLabel') ? esc_html(Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name')) : 'Sweet Addons'; ?> Status</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>Disable Comments:</strong></td>
                        <td><?php echo get_option('fully_disable_comment') ? 'Ã¢Å“â€¦ Aktif' : 'Ã¢ÂÅ’ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Hide Admin Notice:</strong></td>
                        <td><?php echo get_option('hide_admin_notice') ? 'Ã¢Å“â€¦ Aktif' : 'Ã¢ÂÅ’ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Maintenance Mode:</strong></td>
                        <td><?php echo get_option('maintenance_mode') ? 'Ã¢Å“â€¦ Aktif' : 'Ã¢ÂÅ’ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Limit Login Attempts:</strong></td>
                        <td><?php echo get_option('limit_login_attempts') ? 'Ã¢Å“â€¦ Aktif' : 'Ã¢ÂÅ’ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Block wp-login:</strong></td>
                        <td><?php echo get_option('block_wp_login') ? 'Ã¢Å“â€¦ Aktif' : 'Ã¢ÂÅ’ Nonaktif'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <style>
            .report-table td {
                padding: 8px 0;
                border-bottom: 1px solid #f1f1f1;
            }

            .report-table td:first-child {
                width: 50%;
                padding-right: 10px;
            }

            .report-card h3 {
                border-bottom: 2px solid #0073aa;
                padding-bottom: 10px;
            }

            @media (max-width: 768px) {
                .websweet-report-grid {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    <?php

        return ob_get_clean();
    }

    public function visitor_stats_page_callback()
    {
        $stats_handler = new Sweetaddons_Visitor_Stats(false);

        // Handle rebuild request
        $rebuild_message = '';
        if (isset($_POST['rebuild_stats']) && wp_verify_nonce($_POST['_wpnonce'], 'rebuild_stats')) {
            $daily_count = $stats_handler->rebuild_daily_stats();
            $page_count = $stats_handler->rebuild_page_stats();
            $rebuild_message = "<div class='sad-notice sad-notice-success'><p>Statistik berhasil dibangun ulang. Memproses {$daily_count} data harian dan {$page_count} data halaman.</p></div>";
        }

        $current_tab = isset($_GET['subtab']) ? sanitize_key($_GET['subtab']) : 'statistic';
        $summary_stats = $stats_handler->get_summary_stats();
        $daily_stats = $stats_handler->get_daily_stats(30);
        $page_stats = $stats_handler->get_page_stats(30);
        $referer_stats = $stats_handler->get_referer_stats(30);

    ?>
        <?php Sweetaddons_Admin_Layout::open('Statistik Pengunjung', 'Sweetaddons_visitor_stats', Sweetaddons_Admin_Layout::get_visitor_subnav()); ?>

        <?php echo $rebuild_message; ?>

        <?php if ($current_tab === 'statistic') : ?>
            <!-- Summary Cards -->
            <div class="sad-grid stats-summary sad-grid--spaced">

                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Hari Ini</div>
                    <div class="sad-card-value"><?php echo $summary_stats['today']->unique_visitors ?: 0; ?></div>
                    <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['today']->total_visits ?: 0; ?></div>
                </div>

                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Minggu Ini</div>
                    <div class="sad-card-value"><?php echo $summary_stats['this_week']->unique_visitors ?: 0; ?></div>
                    <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['this_week']->total_visits ?: 0; ?></div>
                </div>

                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Bulan Ini</div>
                    <div class="sad-card-value"><?php echo $summary_stats['this_month']->unique_visitors ?: 0; ?></div>
                    <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['this_month']->total_visits ?: 0; ?></div>
                </div>

                <div class="sad-card sad-stat">
                    <div class="sad-card-title">All Time</div>
                    <div class="sad-card-value"><?php echo $summary_stats['all_time']->unique_visitors ?: 0; ?></div>
                    <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['all_time']->total_visits ?: 0; ?></div>
                </div>
            </div>

            <div class="sad-grid charts-section sad-grid--spaced sad-grid--charts">

                <!-- Daily Visits Chart -->
                <div class="sad-card">
                    <div class="sad-card-title">Kunjungan Harian (30 Hari Terakhir)</div>
                    <div class="sad-chartbox">
                        <canvas id="dailyVisitsChart"></canvas>
                    </div>
                </div>

                <!-- Top Pages Chart -->
                <div class="sad-card">
                    <div class="sad-card-title">Halaman Teratas</div>
                    <div class="sad-chartbox">
                        <canvas id="topPagesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Data Tables Section -->
            <div class="sad-grid tables-section sad-grid--tables">

                <!-- Top Pages Table -->
                <div class="sad-card">
                    <div class="sad-card-title">Halaman Teratas (30 Hari Terakhir)</div>
                    <table class="widefat striped sad-widefat">
                        <thead>
                            <tr>
                                <th>Page URL</th>
                                <th>Pengunjung</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($page_stats)): ?>
                                <tr>
                                    <td colspan="3" class="sad-empty">No data available</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($page_stats as $page): ?>
                                    <tr>
                                        <td><code><?php echo esc_html($page->page_url); ?></code></td>
                                        <td><?php echo esc_html($page->unique_visitors); ?></td>
                                        <td><?php echo esc_html($page->total_views); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Top Referrers Table -->
                <div class="sad-card">
                    <div class="sad-card-title">Rujukan Teratas (30 Hari Terakhir)</div>
                    <table class="widefat striped sad-widefat">
                        <thead>
                            <tr>
                                <th>Referrer</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($referer_stats)): ?>
                                <tr>
                                    <td colspan="2" class="sad-empty">No data available</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($referer_stats as $referer): ?>
                                    <tr>
                                        <td><code><?php echo esc_html(parse_url($referer->referer, PHP_URL_HOST) ?: $referer->referer); ?></code></td>
                                        <td><?php echo esc_html($referer->visits); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <?php if ($current_tab === 'shortcode') : ?>
            <!-- Shortcode Examples -->
            <div class="sad-card sad-card--spaced">
                <div class="sad-card-title">Shortcode Statistik</div>
                <p class="sad-subtext sad-mb-12">Gunakan shortcode di bawah untuk menampilkan statistik di halaman atau posting.</p>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div class="sad-sc-item" style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:#fafbfc; border:1px solid #eef0f3; border-radius:8px; transition:all 0.2s ease;">
                        <span style="font-size:12px; font-weight:600; color:#475569; min-width:34px;">Default</span>
                        <code id="sc-stat-default" style="font-family:monospace; font-size:12px; color:#1e293b; background:#fff; border:1px solid #e2e8f0; padding:5px 10px; border-radius:5px; flex:1; min-width:0; overflow-x:auto; white-space:nowrap;">[statistic]</code>
                        <button type="button" class="sad-sc-copy" data-target="#sc-stat-default" style="border:1px solid #e2e8f0; cursor:pointer; padding:6px 12px; border-radius:6px; background:#fff; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s ease; flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy
                        </button>
                    </div>

                    <div class="sad-sc-item" style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:#fafbfc; border:1px solid #eef0f3; border-radius:8px; transition:all 0.2s ease;">
                        <span style="font-size:12px; font-weight:600; color:#475569; min-width:34px;">Hari Ini</span>
                        <code id="sc-stat-today-min" style="font-family:monospace; font-size:12px; color:#1e293b; background:#fff; border:1px solid #e2e8f0; padding:5px 10px; border-radius:5px; flex:1; min-width:0; overflow-x:auto; white-space:nowrap;">[statistic show="today" style="minimal" columns="2"]</code>
                        <button type="button" class="sad-sc-copy" data-target="#sc-stat-today-min" style="border:1px solid #e2e8f0; cursor:pointer; padding:6px 12px; border-radius:6px; background:#fff; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s ease; flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy
                        </button>
                    </div>

                    <div class="sad-sc-item" style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:#fafbfc; border:1px solid #eef0f3; border-radius:8px; transition:all 0.2s ease;">
                        <span style="font-size:12px; font-weight:600; color:#475569; min-width:34px;">Total</span>
                        <code id="sc-stat-total-cards" style="font-family:monospace; font-size:12px; color:#1e293b; background:#fff; border:1px solid #e2e8f0; padding:5px 10px; border-radius:5px; flex:1; min-width:0; overflow-x:auto; white-space:nowrap;">[statistic show="total" style="cards" columns="4"]</code>
                        <button type="button" class="sad-sc-copy" data-target="#sc-stat-total-cards" style="border:1px solid #e2e8f0; cursor:pointer; padding:6px 12px; border-radius:6px; background:#fff; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s ease; flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy
                        </button>
                    </div>

                    <div class="sad-sc-item" style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:#fafbfc; border:1px solid #eef0f3; border-radius:8px; transition:all 0.2s ease;">
                        <span style="font-size:12px; font-weight:600; color:#475569; min-width:34px;">Semua</span>
                        <code id="sc-stat-all-cards" style="font-family:monospace; font-size:12px; color:#1e293b; background:#fff; border:1px solid #e2e8f0; padding:5px 10px; border-radius:5px; flex:1; min-width:0; overflow-x:auto; white-space:nowrap;">[statistic show="all" style="cards" columns="3"]</code>
                        <button type="button" class="sad-sc-copy" data-target="#sc-stat-all-cards" style="border:1px solid #e2e8f0; cursor:pointer; padding:6px 12px; border-radius:6px; background:#fff; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s ease; flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy
                        </button>
                    </div>
                </div>

                <div id="copy-success" class="sad-copy-success" hidden>Shortcode berhasil disalin</div>
            </div>
        <?php endif; ?>

        <?php if ($current_tab !== 'shortcode') : ?>
            <!-- Rebuild Stats Button -->
            <div class="sad-card sad-card--spaced sad-mb-16">
                <form method="post" class="sad-inline" style="display:flex; align-items:center; gap:10px;">
                    <?php wp_nonce_field('rebuild_stats'); ?>
                    <input type="hidden" name="rebuild_stats" value="1">
                    <button type="submit" style="border:1px solid #e2e8f0; cursor:pointer; padding:6px 12px; border-radius:6px; background:#fff; color:#475569; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s ease;" onmouseenter="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8';" onmouseleave="this.style.background='#fff';this.style.borderColor='#e2e8f0';" onclick="return confirm('Apakah Anda yakin ingin mereset statistik?')">
                        Reset Statistik
                    </button>
                    <span class="sad-muted" style="font-size:11px;">
                        Gunakan ini jika hitungan pengunjung tampak tidak benar
                    </span>
                </form>
            </div>
        <?php endif; ?>

        <script>
            function copyShortcode(selector) {
                const el = document.querySelector(selector);
                const text = el ? el.textContent : '';
                if (!text) return;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(showCopySuccess);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                    } catch (e) {}
                    document.body.removeChild(ta);
                    showCopySuccess();
                }
            }

            // Bind modern copy buttons (data-target)
            document.querySelectorAll('.sad-sc-copy').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    copyShortcode(target);
                    const original = this.innerHTML;
                    this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Tersalin';
                    setTimeout(() => {
                        this.innerHTML = original;
                    }, 1500);
                });
                btn.addEventListener('mouseenter', function() {
                    this.style.background = '#f1f5f9';
                    this.style.borderColor = '#94a3b8';
                });
                btn.addEventListener('mouseleave', function() {
                    this.style.background = '#fff';
                    this.style.borderColor = '#e2e8f0';
                });
            });

            // Item hover
            document.querySelectorAll('.sad-sc-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.background = '#f1f5f9';
                    this.style.borderColor = '#cbd5e1';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.background = '#fafbfc';
                    this.style.borderColor = '#eef0f3';
                });
            });

            function showCopySuccess() {
                const box = document.getElementById('copy-success');
                if (!box) return;
                box.hidden = false;
                setTimeout(() => {
                    box.hidden = true;
                }, 1500);
            }
            // Daily Visits Chart
            const dailyData = <?php echo json_encode(array_map(function ($stat) {
                                    return [
                                        'date' => $stat->visit_date,
                                        'unique_visits' => (int)$stat->unique_visits,
                                        'total_visits' => (int)$stat->total_visits
                                    ];
                                }, $daily_stats)); ?>;

            const dailyLabels = dailyData.map(item => item.date);
            const uniqueVisitsData = dailyData.map(item => item.unique_visits);
            const totalVisitsData = dailyData.map(item => item.total_visits);

            const dailyCanvas = document.getElementById('dailyVisitsChart');
            const pageCanvas = document.getElementById('topPagesChart');

            if (!window.Chart || !dailyCanvas || !pageCanvas) {
                console.warn('Sweet Addons: Chart.js gagal dimuat atau elemen canvas tidak ditemukan.');
            } else {
                const dailyCtx = dailyCanvas.getContext('2d');

                // Daily gradient fills
                const dGrad1 = dailyCtx.createLinearGradient(0, 0, 0, 280);
                dGrad1.addColorStop(0, 'rgba(0, 102, 204, 0.35)');
                dGrad1.addColorStop(1, 'rgba(0, 102, 204, 0.0)');
                const dGrad2 = dailyCtx.createLinearGradient(0, 0, 0, 280);
                dGrad2.addColorStop(0, 'rgba(41, 151, 255, 0.25)');
                dGrad2.addColorStop(1, 'rgba(41, 151, 255, 0.0)');

                new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: dailyLabels,
                        datasets: [{
                                label: 'Pengunjung Unik',
                                data: uniqueVisitsData,
                                borderColor: '#0066cc',
                                backgroundColor: dGrad1,
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#0066cc',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#0066cc',
                                pointHoverBorderWidth: 3,
                            },
                            {
                                label: 'Total Kunjungan',
                                data: totalVisitsData,
                                borderColor: '#2997ff',
                                backgroundColor: dGrad2,
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#2997ff',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#2997ff',
                                pointHoverBorderWidth: 3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    padding: 10,
                                    font: {
                                        size: 11
                                    }
                                },
                                border: {
                                    dash: [4, 4],
                                    display: false
                                },
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    padding: 8,
                                    font: {
                                        size: 10
                                    },
                                    maxTicksLimit: 12
                                },
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 24,
                                    boxWidth: 8,
                                    boxHeight: 8,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(20,20,30,0.92)',
                                titleFont: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 11
                                },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: true,
                                boxPadding: 4,
                            }
                        }
                    }
                });

                // Top Pages Chart
                const pageCtx = pageCanvas.getContext('2d');

                const pGrad = pageCtx.createLinearGradient(0, 0, 0, 280);
                pGrad.addColorStop(0, 'rgba(37, 211, 102, 0.75)');
                pGrad.addColorStop(1, 'rgba(37, 211, 102, 0.25)');

                const pGradHover = pageCtx.createLinearGradient(0, 0, 0, 280);
                pGradHover.addColorStop(0, 'rgba(37, 211, 102, 0.95)');
                pGradHover.addColorStop(1, 'rgba(37, 211, 102, 0.40)');

                const pageData = <?php echo json_encode(array_map(function ($page) {
                                        return [
                                            'url' => $page->page_url,
                                            'views' => (int)$page->total_views
                                        ];
                                    }, array_slice($page_stats, 0, 8))); ?>;

                const pageLabels = pageData.map(item => item.url);
                const pageViews = pageData.map(item => item.views);

                new Chart(pageCtx, {
                    type: 'bar',
                    data: {
                        labels: pageLabels,
                        datasets: [{
                            label: 'Page Views',
                            data: pageViews,
                            backgroundColor: pGrad,
                            hoverBackgroundColor: pGradHover,
                            borderColor: '#1da851',
                            borderWidth: 0,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)'
                                },
                                ticks: {
                                    padding: 10,
                                    font: {
                                        size: 11
                                    }
                                },
                                border: {
                                    dash: [4, 4],
                                    display: false
                                },
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 0,
                                    padding: 8,
                                    font: {
                                        size: 10
                                    },
                                    callback: function(value, index, values) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 20 ? label.substring(0, 20) + '...' : label;
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(20,20,30,0.92)',
                                titleFont: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 11
                                },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                            }
                        }
                    }
                });
            }
        </script>
        <?php Sweetaddons_Admin_Layout::close(); ?>
    <?php
    }

    public function seo_page_callback()
    {
        // Enqueue media uploader scripts
        wp_enqueue_media();

        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_seo_settings')) {
            $fields = array(
                'sweetaddons_seo_home_title',
                'sweetaddons_seo_home_description',
                'sweetaddons_seo_default_og_image',
                'sweetaddons_seo_twitter_site',
                'sweetaddons_seo_template_single_title',
                'sweetaddons_seo_template_single_description',
                'sweetaddons_seo_template_page_title',
                'sweetaddons_seo_template_page_description',
                'sweetaddons_seo_template_category_title',
                'sweetaddons_seo_template_category_description',
                'sweetaddons_seo_template_tag_title',
                'sweetaddons_seo_template_tag_description'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, sanitize_text_field($_POST[$field]));
                }
            }
        }

        $home_title = get_option('sweetaddons_seo_home_title', '');
        $home_description = get_option('sweetaddons_seo_home_description', '');
        $default_og_image = get_option('sweetaddons_seo_default_og_image', '');
        if (empty($default_og_image) && function_exists('get_site_icon_url')) {
            $site_icon_og = get_site_icon_url(1200);
            if ($site_icon_og) {
                $default_og_image = $site_icon_og;
            }
        }
        $twitter_site = get_option('sweetaddons_seo_twitter_site', '');
        $tpl_single_title = get_option('sweetaddons_seo_template_single_title', '{post_title} | {site_name}');
        $tpl_single_desc = get_option('sweetaddons_seo_template_single_description', '{excerpt}');
        $tpl_page_title = get_option('sweetaddons_seo_template_page_title', '{page_title} | {site_name}');
        $tpl_page_desc = get_option('sweetaddons_seo_template_page_description', '{excerpt}');
        $tpl_cat_title = get_option('sweetaddons_seo_template_category_title', '{category_name} | {site_name}');
        $tpl_cat_desc = get_option('sweetaddons_seo_template_category_description', '{category_description}');
        $tpl_tag_title = get_option('sweetaddons_seo_template_tag_title', '{tag_name} | {site_name}');
        $tpl_tag_desc = get_option('sweetaddons_seo_template_tag_description', '{tag_description}');

    ?>
        <?php
        $seo_subnav = Sweetaddons_Admin_Layout::get_seo_subnav();
        Sweetaddons_Admin_Layout::open('Pengaturan SEO', 'Sweetaddons_seo', $seo_subnav);
        $seo_active_tab = isset($_GET['subtab']) ? sanitize_key($_GET['subtab']) : 'general';
        if (!in_array($seo_active_tab, array('general', 'social'), true)) {
            $seo_active_tab = 'general';
        }
        ?>
        <?php
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_seo_settings')) {
            echo '<div class="sad-notice sad-notice-success"><p>Pengaturan SEO berhasil disimpan.</p></div>';
        }
        ?>

        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_seo_settings'); ?>

            <div class="sad-top">

                <!-- Left Column (Main Settings) -->
                <div class="sad-top-left">

                    <div id="seo-tab-general" class="seo-tab-content" style="display:<?php echo $seo_active_tab === 'general' ? 'block' : 'none'; ?>;">

                        <!-- General SEO Settings -->
                        <div class="sad-card sad-mb-16" id="seo-general-settings">
                            <div class="sad-card-title">SEO Halaman Utama</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_home_title">Judul Halaman Utama</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_home_title" name="sweetaddons_seo_home_title" value="<?php echo esc_attr($home_title); ?>" class="large-text" />
                                        <p class="description">Kosongkan untuk menggunakan nama situs dan tagline.</p>
                                        <div id="home-title-counter" class="sad-counter"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_home_description">Deskripsi Halaman Utama</label></th>
                                    <td>
                                        <textarea id="sweetaddons_seo_home_description" name="sweetaddons_seo_home_description" rows="3" class="large-text"><?php echo esc_textarea($home_description); ?></textarea>
                                        <p class="description">Kosongkan untuk menggunakan tagline situs.</p>
                                        <div id="home-desc-counter" class="sad-counter"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="sad-card sad-mb-16" id="seo-templates-settings">
                            <div class="sad-card-title">Template Judul & Deskripsi</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_single_title">Template Title (Single)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_single_title" name="sweetaddons_seo_template_single_title" value="<?php echo esc_attr($tpl_single_title); ?>" class="large-text" />
                                        <p class="description">Placeholders: {post_title}, {site_name}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_single_description">Template Description (Single)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_single_description" name="sweetaddons_seo_template_single_description" value="<?php echo esc_attr($tpl_single_desc); ?>" class="large-text" />
                                        <p class="description">Placeholders: {excerpt}, {site_tagline}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_page_title">Template Title (Page)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_page_title" name="sweetaddons_seo_template_page_title" value="<?php echo esc_attr($tpl_page_title); ?>" class="large-text" />
                                        <p class="description">Placeholders: {page_title}, {site_name}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_page_description">Template Description (Page)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_page_description" name="sweetaddons_seo_template_page_description" value="<?php echo esc_attr($tpl_page_desc); ?>" class="large-text" />
                                        <p class="description">Placeholders: {excerpt}, {site_tagline}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_category_title">Template Title (Category)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_category_title" name="sweetaddons_seo_template_category_title" value="<?php echo esc_attr($tpl_cat_title); ?>" class="large-text" />
                                        <p class="description">Placeholders: {category_name}, {site_name}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_category_description">Template Description (Category)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_category_description" name="sweetaddons_seo_template_category_description" value="<?php echo esc_attr($tpl_cat_desc); ?>" class="large-text" />
                                        <p class="description">Placeholders: {category_description}, {site_tagline}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_tag_title">Template Title (Tag)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_tag_title" name="sweetaddons_seo_template_tag_title" value="<?php echo esc_attr($tpl_tag_title); ?>" class="large-text" />
                                        <p class="description">Placeholders: {tag_name}, {site_name}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_template_tag_description">Template Description (Tag)</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_template_tag_description" name="sweetaddons_seo_template_tag_description" value="<?php echo esc_attr($tpl_tag_desc); ?>" class="large-text" />
                                        <p class="description">Placeholders: {tag_description}, {site_tagline}</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                    <!-- /seo-tab-general -->

                    <!-- seo-tab-social -->
                    <div id="seo-tab-social" class="seo-tab-content" style="display:<?php echo $seo_active_tab === 'social' ? 'block' : 'none'; ?>;">

                        <!-- Social Media Settings -->
                        <div class="sad-card sad-mb-16" id="seo-social-settings">
                            <div class="sad-card-title">Social Media</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_default_og_image">Default OG Image</label></th>
                                    <td>
                                        <div class="og-image-container">
                                            <input type="url" id="sweetaddons_seo_default_og_image" name="sweetaddons_seo_default_og_image" value="<?php echo esc_url($default_og_image); ?>" style="display: none;" />
                                            <div class="og-image-preview" style="margin: 10px 0; cursor: pointer;" onclick="document.getElementById('upload-default-og-image').click()">
                                                <?php if ($default_og_image): ?>
                                                    <div style="position: relative; display: inline-block;">
                                                        <img src="<?php echo esc_url($default_og_image); ?>" alt="Preview" style="max-width: 100%; height: auto; border-radius: 4px;" />
                                                        <div style="position: absolute; top: 5px; right: 5px; background: #23282d; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px; opacity: 0.8;">Click to change</div>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="width: 100%; height: 150px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999; background: #f9f9f9; border-radius: 4px;">
                                                        <span>Select Image</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="og-image-buttons" style="margin-top: 10px;">
                                                <button type="button" class="button" id="upload-default-og-image">Choose Image</button>
                                                <?php if ($default_og_image): ?>
                                                    <button type="button" class="button" id="remove-default-og-image" style="margin-left: 8px;">Remove Image</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_seo_twitter_site">Twitter Username</label>
                                        <p class="description" style="font-weight:400; margin-top:4px;">Akun Twitter resmi untuk Twitter Card attribution. Isi dengan format <code>@username</code>. Biarkan kosong jika tidak punya akun Twitter.</p>
                                    </th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_twitter_site" name="sweetaddons_seo_twitter_site" value="<?php echo esc_attr($twitter_site); ?>" class="regular-text" placeholder="username" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                    <!-- /seo-tab-social -->

                </div>

                <!-- Right Column (Sidebar) -->
                <div class="sad-top-right">

                    <!-- Save Button Card -->
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
                // Character counters
                function updateCounter(input, counter, recommended) {
                    const length = input.val().length;
                    let color = '#7a7a7a';
                    if (length > recommended + 10) color = '#d63638';
                    else if (length > recommended) color = '#ff922b';
                    else if (length > recommended - 10) color = '#0066cc';

                    counter.html(length + ' characters').css('color', color);
                }

                const homeTitleInput = $('#sweetaddons_seo_home_title');
                const homeTitleCounter = $('#home-title-counter');
                const homeDescInput = $('#sweetaddons_seo_home_description');
                const homeDescCounter = $('#home-desc-counter');

                homeTitleInput.on('input', function() {
                    updateCounter(homeTitleInput, homeTitleCounter, 60);
                });

                homeDescInput.on('input', function() {
                    updateCounter(homeDescInput, homeDescCounter, 160);
                });

                // Initial count
                updateCounter(homeTitleInput, homeTitleCounter, 60);
                updateCounter(homeDescInput, homeDescCounter, 160);

                // OG Image preview update for default OG image
                function updateDefaultOGImagePreview(imageUrl) {
                    const previewContainer = $('#sweetaddons_seo_default_og_image').siblings('.og-image-preview');
                    const removeButton = $('#remove-default-og-image');
                    const buttonsContainer = $('.og-image-buttons');

                    if (imageUrl) {
                        previewContainer.html('<div style="position: relative; display: inline-block;"><img src="' + imageUrl + '" alt="Default OG Image Preview" style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9; border-radius: 4px;" /><div style="position: absolute; top: 5px; right: 5px; background: #23282d; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px; opacity: 0.8;">Click to change</div></div>');
                        previewContainer.attr('onclick', 'document.getElementById(\'upload-default-og-image\').click()');
                        buttonsContainer.html('<button type="button" class="button" id="upload-default-og-image">Choose Image</button><button type="button" class="button" id="remove-default-og-image" style="margin-left: 8px;">Remove Image</button>');
                    } else {
                        previewContainer.html('<div style="width: 300px; height: 158px; border: 2px dashed #0073aa; display: flex; align-items: center; justify-content: center; color: #0073aa; font-size: 14px; background: #f9f9f9; border-radius: 4px; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.borderColor=\'#005a87\'; this.style.background=\'#f0f8ff\';" onmouseout="this.style.borderColor=\'#0073aa\'; this.style.background=\'#f9f9f9\';"><div style="text-align: center;"><div style="font-size: 32px; margin-bottom: 8px;">Ã°Å¸â€œÂ·</div><div>Click to choose image</div><div style="font-size: 11px; color: #666; margin-top: 4px;">Recommended: 1200x630px</div></div></div>');
                        previewContainer.attr('onclick', 'document.getElementById(\'upload-default-og-image\').click()');
                        buttonsContainer.html('<button type="button" class="button" id="upload-default-og-image">Choose Image</button>');
                    }
                }

                // Media uploader for default OG image
                $(document).on('click', '#upload-default-og-image', function(e) {
                    e.preventDefault();

                    // Check if wp.media exists
                    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                        alert('WordPress media uploader is not available. Please make sure you are on a settings page.');
                        return;
                    }

                    const mediaUploader = wp.media({
                        title: 'Choose Default Open Graph Image',
                        button: {
                            text: 'Use This Image'
                        },
                        multiple: false,
                        library: {
                            type: 'image'
                        }
                    });

                    mediaUploader.on('select', function() {
                        const attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#sweetaddons_seo_default_og_image').val(attachment.url);
                        updateDefaultOGImagePreview(attachment.url);
                    });

                    mediaUploader.open();
                });

                // Remove default OG image
                $(document).on('click', '#remove-default-og-image', function(e) {
                    e.preventDefault();
                    $('#sweetaddons_seo_default_og_image').val('');
                    updateDefaultOGImagePreview('');
                });

                // Manual URL input change for default OG image
                $('#sweetaddons_seo_default_og_image').on('input change', function() {
                    updateDefaultOGImagePreview($(this).val());
                });
            });
        </script>
        <?php Sweetaddons_Admin_Layout::close(); ?>
    <?php
    }


    private function render_whitelabel_tab()
    {
        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whitelabel_settings')) {
            $fields = array(
                'sweetaddons_whitelabel_plugin_name',
                'sweetaddons_whitelabel_plugin_uri',
                'sweetaddons_whitelabel_description',
                'sweetaddons_whitelabel_author',
                'sweetaddons_whitelabel_author_uri',
                'sweetaddons_whitelabel_menu_title',
                'sweetaddons_whitelabel_hide_original',
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, sanitize_text_field($_POST[$field]));
                } else {
                    if ($field === 'sweetaddons_whitelabel_hide_original') {
                        delete_option($field);
                    }
                }
            }

            delete_option('sweetaddons_whitelabel_version');
            delete_option('sweetaddons_whitelabel_accent_color');
        }

        $plugin_name = get_option('sweetaddons_whitelabel_plugin_name', 'Sweet Addons');
        $plugin_uri = get_option('sweetaddons_whitelabel_plugin_uri', 'https://websweetstudio.com');
        $description = get_option('sweetaddons_whitelabel_description', 'Addon plugin for WebsweetStudio Client');
        $author = get_option('sweetaddons_whitelabel_author', 'WebsweetStudio');
        $author_uri = get_option('sweetaddons_whitelabel_author_uri', 'https://websweetstudio.com');
        $menu_title = get_option('sweetaddons_whitelabel_menu_title', $plugin_name);
        if ($menu_title === 'Sweet Addons' && $plugin_name !== 'Sweet Addons') {
            $menu_title = $plugin_name;
        }
        $hide_original = get_option('sweetaddons_whitelabel_hide_original', '');

        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/sweetaddons/sweetaddons.php');
    ?>
        <?php
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whitelabel_settings')) {
            echo '<div class="sad-notice sad-notice-success"><p>Pengaturan White Label berhasil disimpan.</p></div>';
        }
        ?>

        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_whitelabel_settings'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title">Konfigurasi White Label</div>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_plugin_name">Nama Plugin</label></th>
                                <td>
                                    <input type="text" id="sweetaddons_whitelabel_plugin_name" name="sweetaddons_whitelabel_plugin_name" value="<?php echo esc_attr($plugin_name); ?>" class="large-text" />
                                    <p class="description">Nama yang muncul di daftar plugin dan menu admin.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_description">Deskripsi Plugin</label></th>
                                <td>
                                    <textarea id="sweetaddons_whitelabel_description" name="sweetaddons_whitelabel_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_plugin_uri">Plugin URI</label></th>
                                <td>
                                    <input type="url" id="sweetaddons_whitelabel_plugin_uri" name="sweetaddons_whitelabel_plugin_uri" value="<?php echo esc_url($plugin_uri); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_author">Nama Penulis</label></th>
                                <td>
                                    <input type="text" id="sweetaddons_whitelabel_author" name="sweetaddons_whitelabel_author" value="<?php echo esc_attr($author); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_author_uri">URI Penulis</label></th>
                                <td>
                                    <input type="url" id="sweetaddons_whitelabel_author_uri" name="sweetaddons_whitelabel_author_uri" value="<?php echo esc_url($author_uri); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons_whitelabel_menu_title">Judul Menu Admin</label></th>
                                <td>
                                    <input type="text" id="sweetaddons_whitelabel_menu_title" name="sweetaddons_whitelabel_menu_title" value="<?php echo esc_attr($menu_title); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Sembunyikan Branding Asli</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="sweetaddons_whitelabel_hide_original" value="1" <?php checked($hide_original, '1'); ?> />
                                        Hide references to WebsweetStudio in admin interface
                                    </label>
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
    <?php
    }


    public function whatsapp_page_callback()
    {
        wp_enqueue_media();

        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whatsapp_settings')) {
            if (isset($_POST['sweetaddons_whatsapp_agents']) && is_array($_POST['sweetaddons_whatsapp_agents'])) {
                $agents = array();
                foreach ($_POST['sweetaddons_whatsapp_agents'] as $agent) {
                    if (!is_array($agent)) {
                        continue;
                    }

                    $name = isset($agent['name']) ? sanitize_text_field($agent['name']) : '';
                    $phone_raw = isset($agent['phone']) ? sanitize_text_field($agent['phone']) : '';
                    $phone = preg_replace('/[^0-9]/', '', $phone_raw);
                    $role = isset($agent['role']) ? sanitize_text_field($agent['role']) : '';
                    $note = isset($agent['note']) ? sanitize_text_field($agent['note']) : '';
                    $status = isset($agent['status']) ? sanitize_text_field($agent['status']) : 'online';
                    $avatar = isset($agent['avatar']) ? esc_url_raw($agent['avatar']) : '';

                    if (empty($phone)) {
                        continue;
                    }

                    if (!in_array($status, array('online', 'offline'), true)) {
                        $status = 'online';
                    }

                    $agents[] = array(
                        'name'   => $name,
                        'phone'  => $phone,
                        'role'   => $role,
                        'note'   => $note,
                        'status' => $status,
                        'avatar' => $avatar,
                    );
                }

                if (!empty($agents)) {
                    update_option('sweetaddons_whatsapp_agents', $agents);
                    delete_option('sweetaddons_whatsapp_phone');
                } else {
                    delete_option('sweetaddons_whatsapp_agents');
                }
            } else {
                delete_option('sweetaddons_whatsapp_agents');
            }

            $fields = array(
                'sweetaddons_whatsapp_enable',
                'sweetaddons_whatsapp_message',
                'sweetaddons_whatsapp_button_text',
                'sweetaddons_whatsapp_position',
                'sweetaddons_whatsapp_color',
                'sweetaddons_whatsapp_show_mobile',
                'sweetaddons_whatsapp_show_desktop',
                'sweetaddons_whatsapp_animation',
                'sweetaddons_whatsapp_bubble_style',
                'sweetaddons_whatsapp_show_tooltip',
                'sweetaddons_whatsapp_show_text_mobile'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, sanitize_text_field($_POST[$field]));
                } else {
                    // Handle checkbox fields
                    if (in_array($field, ['sweetaddons_whatsapp_enable', 'sweetaddons_whatsapp_show_mobile', 'sweetaddons_whatsapp_show_desktop', 'sweetaddons_whatsapp_show_tooltip', 'sweetaddons_whatsapp_show_text_mobile'])) {
                        delete_option($field);
                    }
                }
            }
        }

        // Get current settings
        $enable = get_option('sweetaddons_whatsapp_enable', '');
        $phone = get_option('sweetaddons_whatsapp_phone', '');
        $message = get_option('sweetaddons_whatsapp_message', 'Halo! Saya butuh bantuan.');
        $button_text = get_option('sweetaddons_whatsapp_button_text', 'Chat dengan kami');
        $position = get_option('sweetaddons_whatsapp_position', 'bottom-right');
        $color = get_option('sweetaddons_whatsapp_color', '#25D366');
        $show_mobile = get_option('sweetaddons_whatsapp_show_mobile', '1');
        $show_desktop = get_option('sweetaddons_whatsapp_show_desktop', '1');
        $animation = get_option('sweetaddons_whatsapp_animation', 'none');
        $bubble_style = get_option('sweetaddons_whatsapp_bubble_style', 'circle');
        $show_tooltip = get_option('sweetaddons_whatsapp_show_tooltip', '');
        $show_text_mobile = get_option('sweetaddons_whatsapp_show_text_mobile', '');
        $agents = get_option('sweetaddons_whatsapp_agents', array());
        if (!is_array($agents)) {
            $agents = array();
        }

        if (empty($agents) && !empty($phone)) {
            $agents = array(
                array(
                    'name'   => '',
                    'phone'  => $phone,
                    'role'   => 'Customer Service',
                    'note'   => '',
                    'status' => 'online',
                    'avatar' => '',
                ),
            );
        }

        $has_agents = false;
        foreach ($agents as $agent) {
            if (is_array($agent) && !empty($agent['phone'])) {
                $has_agents = true;
                break;
            }
        }

        $display_position = ucwords(str_replace('-', ' ', $position));
    ?>
        <?php
        $wa_subnav = Sweetaddons_Admin_Layout::get_whatsapp_subnav();
        Sweetaddons_Admin_Layout::open('WhatsApp', 'Sweetaddons_whatsapp', $wa_subnav);
        ?>
        <?php
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whatsapp_settings')) {
            echo '<div class="sad-notice sad-notice-success"><p>Pengaturan WhatsApp berhasil disimpan.</p></div>';
        }
        ?>
        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_whatsapp_settings'); ?>
            <?php
            $wa_active_tab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : 'pengaturan';
            if (!in_array($wa_active_tab, array('pengaturan', 'style'), true)) {
                $wa_active_tab = 'pengaturan';
            }
            ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <!-- wa-tab-pengaturan -->
                    <div id="wa-tab-pengaturan" class="wa-tab-content" style="display:<?php echo $wa_active_tab === 'pengaturan' ? 'block' : 'none'; ?>;">
                        <div class="sad-card sad-mb-16">
                            <div class="sad-card-title">Pengaturan Dasar</div>

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
                                    <th scope="row">Multi Agen</th>
                                    <td>
                                        <div id="sweetaddons-wa-agents">
                                            <?php
                                            $agent_index = 0;
                                            foreach ($agents as $agent) :
                                                if (!is_array($agent)) {
                                                    continue;
                                                }
                                                $agent_name = isset($agent['name']) ? $agent['name'] : '';
                                                $agent_phone = isset($agent['phone']) ? $agent['phone'] : '';
                                                $agent_role = isset($agent['role']) ? $agent['role'] : '';
                                                $agent_note = isset($agent['note']) ? $agent['note'] : '';
                                                $agent_status = isset($agent['status']) ? $agent['status'] : 'online';
                                                $agent_avatar = isset($agent['avatar']) ? $agent['avatar'] : '';
                                            ?>
                                                <div class="sweetaddons-wa-agent-row" style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 12px; margin: 0 0 12px; background: #fff;">
                                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                        <div style="flex: 1; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Nama</label>
                                                            <input type="text" name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][name]" value="<?php echo esc_attr($agent_name); ?>" class="regular-text" style="width: 100%;" placeholder="Nama Agen" />
                                                        </div>
                                                        <div style="flex: 1; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Nomor WhatsApp</label>
                                                            <input type="text" name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][phone]" value="<?php echo esc_attr($agent_phone); ?>" class="regular-text" style="width: 100%;" placeholder="62812345678901" />
                                                        </div>
                                                        <div style="flex: 1; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Role</label>
                                                            <input type="text" name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][role]" value="<?php echo esc_attr($agent_role); ?>" class="regular-text" style="width: 100%;" placeholder="Customer Service" />
                                                        </div>
                                                        <div style="flex: 1; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Status</label>
                                                            <select name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][status]" style="width: 100%;">
                                                                <option value="online" <?php selected($agent_status, 'online'); ?>>Online</option>
                                                                <option value="offline" <?php selected($agent_status, 'offline'); ?>>Offline</option>
                                                            </select>
                                                        </div>
                                                        <div style="flex: 1 1 100%; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Note</label>
                                                            <input type="text" name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][note]" value="<?php echo esc_attr($agent_note); ?>" class="regular-text" style="width: 100%;" placeholder="Contoh: Pesan akan dibalas pada jam kerja 08.00 - 15.00" />
                                                        </div>
                                                        <div style="flex: 1 1 100%; min-width: 160px;">
                                                            <label style="display:block; margin: 0 0 6px;">Avatar URL (opsional)</label>
                                                            <div style="display:flex; gap: 8px; align-items: center;">
                                                                <div class="sweetaddons-wa-avatar-preview" style="width:36px; height:36px; border-radius:50%; overflow:hidden; border:2px solid #e5e7eb; flex-shrink:0; background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                                                                    <?php if (!empty($agent_avatar)) : ?>
                                                                        <img src="<?php echo esc_url($agent_avatar); ?>" alt="Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.parentElement.innerHTML='<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'18\' height=\'18\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#94a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\'/><circle cx=\'12\' cy=\'7\' r=\'4\'/></svg>';" />
                                                                    <?php else : ?>
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                                                            <circle cx="12" cy="7" r="4" />
                                                                        </svg>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <input type="text" name="sweetaddons_whatsapp_agents[<?php echo esc_attr($agent_index); ?>][avatar]" value="<?php echo esc_attr($agent_avatar); ?>" class="regular-text sweetaddons-wa-avatar-input" style="width: 100%;" placeholder="https://..." />
                                                                <button type="button" class="button sweetaddons-wa-upload-avatar">Upload</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="margin-top: 8px;">
                                                        <button type="button" class="button-link-delete sweetaddons-wa-remove-agent">Hapus</button>
                                                    </div>
                                                </div>
                                            <?php
                                                $agent_index++;
                                            endforeach;
                                            ?>
                                        </div>
                                        <div style="margin-top: 8px;">
                                            <button type="button" class="button" id="sweetaddons-wa-add-agent">Tambah Agen</button>
                                            <p class="description" style="margin: 4px 0 0 0;">Agen pertama menjadi default. Jika lebih dari satu agen, widget menampilkan daftar pilihan agen.</p>
                                        </div>

                                        <script type="text/template" id="sweetaddons-wa-agent-template">
                                            <div class="sweetaddons-wa-agent-row" style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 12px; margin: 0 0 12px; background: #fff;">
                                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                                <div style="flex: 1; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Nama</label>
                                                    <input type="text" name="sweetaddons_whatsapp_agents[__i__][name]" value="" class="regular-text" style="width: 100%;" placeholder="Nama Agen" />
                                                </div>
                                                <div style="flex: 1; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Nomor WhatsApp</label>
                                                    <input type="text" name="sweetaddons_whatsapp_agents[__i__][phone]" value="" class="regular-text" style="width: 100%;" placeholder="62812345678901" />
                                                </div>
                                                <div style="flex: 1; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Role</label>
                                                    <input type="text" name="sweetaddons_whatsapp_agents[__i__][role]" value="Customer Service" class="regular-text" style="width: 100%;" placeholder="Customer Service" />
                                                </div>
                                                <div style="flex: 1; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Status</label>
                                                    <select name="sweetaddons_whatsapp_agents[__i__][status]" style="width: 100%;">
                                                        <option value="online">Online</option>
                                                        <option value="offline">Offline</option>
                                                    </select>
                                                </div>
                                                <div style="flex: 1 1 100%; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Note</label>
                                                    <input type="text" name="sweetaddons_whatsapp_agents[__i__][note]" value="" class="regular-text" style="width: 100%;" placeholder="Contoh: Pesan akan dibalas pada jam kerja 08.00 - 15.00" />
                                                </div>
                                                <div style="flex: 1 1 100%; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Avatar URL (opsional)</label>
                                                    <div style="display:flex; gap: 8px; align-items: center;">
                                                        <div class="sweetaddons-wa-avatar-preview" style="width:36px; height:36px; border-radius:50%; overflow:hidden; border:2px solid #e5e7eb; flex-shrink:0; background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                        </div>
                                                        <input type="text" name="sweetaddons_whatsapp_agents[__i__][avatar]" value="" class="regular-text sweetaddons-wa-avatar-input" style="width: 100%;" placeholder="https://..." />
                                                        <button type="button" class="button sweetaddons-wa-upload-avatar">Upload</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="margin-top: 8px;">
                                                <button type="button" class="button-link-delete sweetaddons-wa-remove-agent">Hapus</button>
                                            </div>
                                        </div>
                                    </script>
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
                            </table>
                        </div>
                    </div>
                    <!-- /wa-tab-pengaturan -->

                    <!-- wa-tab-style -->
                    <div id="wa-tab-style" class="wa-tab-content" style="display:<?php echo $wa_active_tab === 'style' ? 'block' : 'none'; ?>;">
                        <div class="sad-card sad-mb-16">
                            <div class="sad-card-title">Pengaturan Tampilan</div>
                            <table class="form-table">
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
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <input type="color" id="sweetaddons_whatsapp_color" name="sweetaddons_whatsapp_color" value="<?php echo esc_attr($color); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color); ?>" class="regular-text" readonly style="width:auto;" />
                                        </div>
                                        <p class="description">Background color of the WhatsApp button.</p>
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
                                <tr>
                                    <td colspan="2" style="padding:0;">
                                        <div class="sad-subsection" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee;">
                                            <h3 style="font-size: 14px; font-weight: 600; margin: 0 0 10px 0; color: #334155;">Pengaturan Posisi</h3>
                                        </div>
                                    </td>
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
                                    <td colspan="2" style="padding:0;">
                                        <div class="sad-subsection" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee;">
                                            <h3 style="font-size: 14px; font-weight: 600; margin: 0 0 10px 0; color: #334155;">Visibility Settings</h3>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Device Visibility</th>
                                    <td>
                                        <label class="sad-form-checkbox">
                                            <input type="checkbox" name="sweetaddons_whatsapp_show_mobile" value="1" <?php checked($show_mobile, '1'); ?> />
                                            Tampilkan di perangkat Mobile
                                        </label><br>
                                        <label class="sad-form-checkbox">
                                            <input type="checkbox" name="sweetaddons_whatsapp_show_desktop" value="1" <?php checked($show_desktop, '1'); ?> />
                                            Tampilkan di perangkat Desktop
                                        </label>
                                        <p class="description">Choose on which devices to display the chat button.</p>
                                        <label class="sad-form-checkbox" style="margin-top:8px;display:block;">
                                            <input type="checkbox" name="sweetaddons_whatsapp_show_text_mobile" value="1" <?php checked($show_text_mobile, '1'); ?> />
                                            Tampilkan teks di perangkat Mobile
                                        </label>
                                        <p class="description" style="margin-left:24px;">Jika diaktifkan, teks tombol tetap terlihat di layar mobile pada mode <strong>Extended</strong>.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Tooltip</th>
                                    <td>
                                        <label class="sad-form-checkbox">
                                            <input type="checkbox" name="sweetaddons_whatsapp_show_tooltip" value="1" <?php checked($show_tooltip, '1'); ?> />
                                            Show tooltip on hover
                                        </label>
                                        <p class="description">Display tooltip text when hovering over the chat button.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="sad-card">
                            <div class="sad-card-title">Live Preview</div>
                            <style>
                                #whatsapp-preview-stage .sweetaddons-wa-widget {
                                    position: absolute;
                                    z-index: 1;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-bubble {
                                    position: relative;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-link {
                                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                                    position: relative;
                                    overflow: hidden;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-icon,
                                #whatsapp-preview-stage .sweetaddons-wa-text {
                                    position: relative;
                                    z-index: 1;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-tooltip {
                                    position: absolute;
                                    background: #333;
                                    color: #fff;
                                    padding: 8px 12px;
                                    border-radius: 6px;
                                    font-size: 12px;
                                    white-space: nowrap;
                                    opacity: 0;
                                    visibility: hidden;
                                    transition: all 0.3s ease;
                                    pointer-events: none;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-widget:hover .sweetaddons-wa-tooltip {
                                    opacity: 1;
                                    visibility: visible;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-bottom-right .sweetaddons-wa-tooltip,
                                #whatsapp-preview-stage .sweetaddons-wa-bottom-left .sweetaddons-wa-tooltip {
                                    bottom: calc(100% + 10px);
                                    left: 50%;
                                    transform: translateX(-50%);
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-top-right .sweetaddons-wa-tooltip,
                                #whatsapp-preview-stage .sweetaddons-wa-top-left .sweetaddons-wa-tooltip {
                                    top: calc(100% + 10px);
                                    left: 50%;
                                    transform: translateX(-50%);
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-center-right .sweetaddons-wa-tooltip {
                                    right: calc(100% + 10px);
                                    top: 50%;
                                    transform: translateY(-50%);
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-center-left .sweetaddons-wa-tooltip {
                                    left: calc(100% + 10px);
                                    top: 50%;
                                    transform: translateY(-50%);
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-widget[data-animation='pulse'] .sweetaddons-wa-link {
                                    animation: sweetaddons-wa-pulse 2s infinite;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-widget[data-animation='bounce'] .sweetaddons-wa-link {
                                    animation: sweetaddons-wa-bounce 2s infinite;
                                }

                                #whatsapp-preview-stage .sweetaddons-wa-widget[data-animation='shake'] .sweetaddons-wa-link {
                                    animation: sweetaddons-wa-shake 3s infinite;
                                }

                                @keyframes sweetaddons-wa-pulse {
                                    0% {
                                        transform: scale(1);
                                    }

                                    50% {
                                        transform: scale(1.02);
                                    }

                                    100% {
                                        transform: scale(1);
                                    }
                                }

                                @keyframes sweetaddons-wa-bounce {

                                    0%,
                                    20%,
                                    50%,
                                    80%,
                                    100% {
                                        transform: translateY(0);
                                    }

                                    40% {
                                        transform: translateY(-10px);
                                    }

                                    60% {
                                        transform: translateY(-5px);
                                    }
                                }

                                @keyframes sweetaddons-wa-shake {

                                    0%,
                                    100% {
                                        transform: translateX(0);
                                    }

                                    10%,
                                    30%,
                                    50%,
                                    70%,
                                    90% {
                                        transform: translateX(-2px);
                                    }

                                    20%,
                                    40%,
                                    60%,
                                    80% {
                                        transform: translateX(2px);
                                    }
                                }
                            </style>
                            <div id="whatsapp-preview-stage" style="position: relative; height: 200px; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px; overflow: hidden;">
                                <div id="whatsapp-preview-container" style="display: <?php echo ($enable && $has_agents) ? 'block' : 'none'; ?>;">
                                    <div id="whatsapp-preview-bubble" class="sweetaddons-wa-widget sweetaddons-wa-preview sweetaddons-wa-<?php echo esc_attr($position); ?>" data-animation="<?php echo esc_attr($animation); ?>" style="position: absolute; <?php echo ($position === 'bottom-right') ? 'bottom: 20px; right: 20px;' : 'bottom: 20px; left: 20px;'; ?>">
                                        <div id="whatsapp-preview-shell" class="sweetaddons-wa-bubble sweetaddons-wa-<?php echo esc_attr($bubble_style); ?>">
                                            <div id="whatsapp-preview-inner" class="sweetaddons-wa-link" style="display: flex; align-items: center; <?php echo ($bubble_style === 'extended') ? 'padding: 12px 20px;' : 'width: 60px; height: 60px; justify-content: center;'; ?> background: <?php echo esc_attr($color); ?>; border-radius: <?php echo ($bubble_style === 'extended') ? '25px' : '50%'; ?>; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);">
                                                <div class="sweetaddons-wa-icon">
                                                    <svg viewBox="0 0 24 24" width="24" height="24" style="<?php echo ($bubble_style === 'extended') ? 'margin-right: 8px;' : ''; ?>">
                                                        <path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                                    </svg>
                                                </div>
                                                <span id="whatsapp-preview-text" class="sweetaddons-wa-text" style="font-size: 14px; font-weight: 500; display: <?php echo ($bubble_style === 'extended') ? 'inline' : 'none'; ?>;"><?php echo esc_html($button_text); ?></span>
                                            </div>
                                            <div id="whatsapp-preview-tooltip" class="sweetaddons-wa-tooltip" style="display: <?php echo ($show_tooltip === '1') ? 'block' : 'none'; ?>;">
                                                <?php echo esc_html($button_text); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="whatsapp-preview-placeholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #666; display: <?php echo ($enable && $has_agents) ? 'none' : 'block'; ?>;">
                                    <p>Aktifkan WhatsApp dan tambahkan agen untuk melihat preview</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /wa-tab-style -->

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
                function syncAgentIndices() {
                    $('#sweetaddons-wa-agents .sweetaddons-wa-agent-row').each(function(i) {
                        $(this).find('input, select, textarea').each(function() {
                            const name = $(this).attr('name');
                            if (!name) {
                                return;
                            }
                            $(this).attr('name', name.replace(/sweetaddons_whatsapp_agents\[\d+\]/, 'sweetaddons_whatsapp_agents[' + i + ']'));
                        });
                    });
                }

                $('#sweetaddons-wa-add-agent').on('click', function() {
                    const template = $('#sweetaddons-wa-agent-template').html() || '';
                    const nextIndex = $('#sweetaddons-wa-agents .sweetaddons-wa-agent-row').length;
                    const html = template.replace(/__i__/g, String(nextIndex));
                    $('#sweetaddons-wa-agents').append(html);
                    syncAgentIndices();
                    updateWhatsAppPreview();
                });

                $(document).on('click', '.sweetaddons-wa-remove-agent', function() {
                    $(this).closest('.sweetaddons-wa-agent-row').remove();
                    syncAgentIndices();
                    updateWhatsAppPreview();
                });

                $(document).on('click', '.sweetaddons-wa-upload-avatar', function(e) {
                    e.preventDefault();

                    var $row = $(this).closest('.sweetaddons-wa-agent-row');
                    var $input = $row.find('.sweetaddons-wa-avatar-input').first();
                    if (!$input.length || typeof wp === 'undefined' || !wp.media) {
                        return;
                    }

                    var frame = wp.media({
                        title: 'Pilih Avatar',
                        button: {
                            text: 'Gunakan gambar'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        if (attachment && attachment.url) {
                            $input.val(attachment.url).trigger('input');
                            updateAvatarPreview($row, attachment.url);
                        }
                    });

                    frame.open();
                });

                function updateAvatarPreview($row, url) {
                    var $preview = $row.find('.sweetaddons-wa-avatar-preview');
                    if (!$preview.length) return;
                    if (url) {
                        $preview.html('<img src="' + url + '" alt="Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display=\'none\'; this.parentElement.innerHTML=\'<svg xmlns=\\\'http://www.w3.org/2000/svg\\\' width=\\\'18\\\' height=\\\'18\\\' viewBox=\\\'0 0 24 24\\\' fill=\\\'none\\\' stroke=\\\'#94a3b8\\\' stroke-width=\\\'1.5\\\' stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\'><path d=\\\'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\\\'/><circle cx=\\\'12\\\' cy=\\\'7\\\' r=\\\'4\\\'/></svg>\';" />');
                    } else {
                        $preview.html('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>');
                    }
                }

                // Live preview avatar when URL is typed
                $(document).on('input', '.sweetaddons-wa-avatar-input', function() {
                    var url = $(this).val().trim();
                    var $row = $(this).closest('.sweetaddons-wa-agent-row');
                    updateAvatarPreview($row, url);
                });

                // Color picker sync
                $('#sweetaddons_whatsapp_color').on('change', function() {
                    $(this).next('input[type="text"]').val($(this).val());
                    updateWhatsAppPreview();
                });

                // Real-time preview update function
                function updateWhatsAppPreview() {
                    const enable = $('#sweetaddons_whatsapp_enable').is(':checked');
                    const has_agents = $('#sweetaddons-wa-agents input[name*="[phone]"]').filter(function() {
                        return ($(this).val() || '').trim() !== '';
                    }).length > 0;
                    const button_text = $('#sweetaddons_whatsapp_button_text').val().trim() || 'Chat dengan kami';
                    const position = $('#sweetaddons_whatsapp_position').val();
                    const color = $('#sweetaddons_whatsapp_color').val();
                    const animation = $('#sweetaddons_whatsapp_animation').val();
                    const size = '60';
                    const offset_x = '20';
                    const offset_y = '20';
                    const bubble_style = $('#sweetaddons_whatsapp_bubble_style').val();
                    const show_tooltip = $('input[name="sweetaddons_whatsapp_show_tooltip"]').is(':checked');

                    const $container = $('#whatsapp-preview-container');
                    const $placeholder = $('#whatsapp-preview-placeholder');
                    const $bubble = $('#whatsapp-preview-bubble');
                    const $shell = $('#whatsapp-preview-shell');
                    const $inner = $('#whatsapp-preview-inner');
                    const $text = $('#whatsapp-preview-text');
                    const $tooltip = $('#whatsapp-preview-tooltip');
                    const $icon = $bubble.find('.sweetaddons-wa-icon svg');

                    // Show/hide preview
                    if (enable && has_agents) {
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
                        $bubble.attr('data-animation', animation);
                        $bubble
                            .removeClass('sweetaddons-wa-bottom-right sweetaddons-wa-bottom-left sweetaddons-wa-top-right sweetaddons-wa-top-left sweetaddons-wa-center-right sweetaddons-wa-center-left')
                            .addClass(`sweetaddons-wa-${position}`);

                        // Update bubble style and content
                        let innerStyle = '';
                        $shell.removeClass('sweetaddons-wa-circle sweetaddons-wa-extended').addClass(`sweetaddons-wa-${bubble_style}`);
                        if (bubble_style === 'extended') {
                            innerStyle = `display: flex; align-items: center; padding: 12px 20px; background: ${color}; border-radius: 25px; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);`;
                            $text.show().text(button_text);
                            $icon.css('margin-right', '8px');
                        } else {
                            innerStyle = `width: ${size}px; height: ${size}px; display: flex; align-items: center; justify-content: center; background: ${color}; border-radius: 50%; color: white; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);`;
                            $text.hide();
                            $icon.css('margin-right', '0');
                        }
                        $inner.attr('style', innerStyle);
                        $tooltip.text(button_text).toggle(show_tooltip);

                    } else {
                        $container.hide();
                        $placeholder.show();
                    }
                }

                // Event listeners for all WhatsApp fields
                $('#sweetaddons_whatsapp_enable').on('change', updateWhatsAppPreview);
                $('#sweetaddons_whatsapp_button_text').on('input', updateWhatsAppPreview);
                $('#sweetaddons_whatsapp_position').on('change', updateWhatsAppPreview);
                $('#sweetaddons_whatsapp_color').on('change', updateWhatsAppPreview);
                $('#sweetaddons_whatsapp_animation').on('change', updateWhatsAppPreview);
                $('#sweetaddons_whatsapp_bubble_style').on('change', updateWhatsAppPreview);
                $('input[name="sweetaddons_whatsapp_show_tooltip"]').on('change', updateWhatsAppPreview);
                $(document).on('input change', '#sweetaddons-wa-agents input, #sweetaddons-wa-agents select, #sweetaddons-wa-agents textarea', updateWhatsAppPreview);

                // Initialize preview on page load
                updateWhatsAppPreview();
            });
        </script>
        <?php Sweetaddons_Admin_Layout::close(); ?>
<?php
    }
}
