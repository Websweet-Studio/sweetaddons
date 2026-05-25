<?php
namespace Sweetaddons\Admin;

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

class OptionPage
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
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null);
        }
    }

    public function add_options_page()
    {
        $plugin_name = class_exists('\Sweetaddons\WhiteLabel') ? \Sweetaddons\WhiteLabel::get_white_labeled_info('plugin_name') : 'Sweet Addons';
        $menu_title = class_exists('\Sweetaddons\WhiteLabel') ? \Sweetaddons\WhiteLabel::get_white_labeled_info('menu_title') : 'Sweet Addons';

        add_menu_page(
            $plugin_name,       // Judul halaman
            $menu_title,       // Judul menu
            'manage_options',           // Hak akses yang dibutuhkan
            'custom_admin_options',     // Slug menu
            array($this, 'options_page_callback'), // Callback untuk halaman pengaturan
            'dashicons-chart-pie',                         // URL icon (biarkan kosong atau tambahkan URL icon)
            70                         // Posisi menu (semakin kecil angkanya semakin tinggi posisinya)
        );

        // Add spam submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Proteksi Spam',            // Page title
            'Proteksi Spam',            // Menu title
            'manage_options',           // Capability
            'Sweetaddons_spam',        // Menu slug
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

        // Add reCaptcha submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Pengaturan reCaptcha',     // Page title
            'reCaptcha',               // Menu title
            'manage_options',           // Capability
            'Sweetaddons_recaptcha',   // Menu slug
            array($this, 'recaptcha_page_callback') // Callback function
        );

        // Add White Label submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'White Label',              // Page title
            'White Label',              // Menu title
            'manage_options',           // Capability
            'Sweetaddons_whitelabel',  // Menu slug
            array($this, 'whitelabel_page_callback') // Callback function
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

        // Add Login Customizer submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Login Customizer',         // Page title
            'Login Page',               // Menu title
            'manage_options',           // Capability
            'Sweetaddons_login_customizer', // Menu slug
            array($this, 'login_customizer_page_callback') // Callback function
        );

        // Add Database Cleaner submenu
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Database Cleaner',         // Page title
            'DB Cleaner',               // Menu title
            'manage_options',           // Capability
            'Sweetaddons_db_cleaner',   // Menu slug
            array($this, 'db_cleaner_page_callback') // Callback function
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

    public function spam_page_callback()
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
        <?php AdminLayout::open('Proteksi Spam', 'Sweetaddons_spam'); ?>
        <div class="sad-grid">
            <div class="sad-card">
                <div class="sad-card-title">Pengaturan Utama</div>
                <form method="post" action="options.php" class="sad-form">
                    <?php settings_fields('custom_admin_options_group'); ?>
                    <?php do_settings_sections('custom_admin_options_group'); ?>
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
                    <div class="sad-actions-row sad-actions-row--end">
                        <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php AdminLayout::close(); ?>
    <?php
    }

    public function options_page_callback()
    {
    ?>
        <?php AdminLayout::open('Dashboard', 'custom_admin_options'); ?>
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
            if (class_exists('\Sweetaddons\MaintenanceMode')) {
                    $qc_content = (new \Sweetaddons\MaintenanceMode())->qc_maintenance();
            }
            ?>
            <div class="sad-top-left sad-stack">
                <div class="sad-row sad-row--stats">
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Hari Ini</div>
                        <div class="sad-card-value"><?php echo number_format($today ? (int)$today->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($today ? (int)$today->uv : 0); ?></div>
                    </div>
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Minggu Ini</div>
                        <div class="sad-card-value"><?php echo number_format($this_week ? (int)$this_week->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($this_week ? (int)$this_week->uv : 0); ?></div>
                    </div>
                    <div class="sad-card sad-stat">
                        <div class="sad-card-title">Bulan Ini</div>
                        <div class="sad-card-value"><?php echo number_format($this_month ? (int)$this_month->pv : 0); ?></div>
                        <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($this_month ? (int)$this_month->uv : 0); ?></div>
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
        <

... [OUTPUT TRUNCATED - 104751 chars omitted out of 154751 total] ...

                     <label style="display:block; margin: 0 0 6px;">Note</label>
                                                    <input type="text" name="sweetaddons_whatsapp_agents[__i__][note]" value="" class="regular-text" style="width: 100%;" placeholder="Contoh: Saya akan kembali dalam 4 jam" />
                                                </div>
                                                <div style="flex: 1 1 100%; min-width: 160px;">
                                                    <label style="display:block; margin: 0 0 6px;">Avatar URL (opsional)</label>
                                                    <div style="display:flex; gap: 8px; align-items: center;">
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
                            <!-- Appearance Section Header -->
                            <tr>
                                <td colspan="2">
                                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2" style="padding-left: 0;">
                                    <h3 style="margin: 0;"> Pengaturan Tampilan</h3>
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
                                    <h3 style="margin: 0;">Pengaturan Posisi</h3>
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
                            <!-- Visibility Section Header -->
                            <tr>
                                <td colspan="2">
                                    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2" style="padding-left: 0;">
                                    <h3 style="margin: 0;">Visibility Settings</h3>
                                </th>
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

                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-card-title">Simpan Perubahan</div>
                        <div class="sad-actions-row sad-actions-row--end">
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
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
                        }
                    });

                    frame.open();
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
        <?php AdminLayout::close(); ?>
<?php
}

}
