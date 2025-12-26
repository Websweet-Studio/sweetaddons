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
        if (strpos($hook, 'sweetaddons') !== false || strpos($hook, 'custom_admin_options') !== false) {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null);
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
        register_setting('custom_admin_options_group', 'whitelist_country');
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
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_enable_sitemap');
        register_setting('sweetaddons_seo_group', 'sweetaddons_seo_google_search_console');

        // reCaptcha settings
        register_setting('sweetaddons_recaptcha_group', 'captcha_Sweetaddons');

        // White Label settings
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_plugin_name');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_plugin_uri');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_description');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_author');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_author_uri');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_version');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_menu_title');
        register_setting('sweetaddons_whitelabel_group', 'sweetaddons_whitelabel_hide_original');

        // WhatsApp settings
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_enable');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_phone');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_message');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_button_text');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_position');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_color');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_size');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_offset_x');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_offset_y');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_mobile');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_desktop');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_animation');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_bubble_style');
        register_setting('sweetaddons_whatsapp_group', 'sweetaddons_whatsapp_show_tooltip');
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
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">Proteksi Spam</h1>
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
                        <div class="sad-actions-row" style="justify-content: flex-end;">
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
                        </div>
                    </form>
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
                                <td>Batasi Percobaan Login</td>
                                <td><?php echo get_option('limit_login_attempts') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Nonaktifkan XML-RPC</td>
                                <td><?php echo get_option('disable_xmlrpc') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Nonaktifkan REST API</td>
                                <td><?php echo get_option('disable_rest_api') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }

    public function options_page_callback()
    {
    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">Dashboard <?php echo class_exists('Sweetaddons_WhiteLabel') ? esc_html(Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name')) : 'Sweet Addons'; ?></h1>
            <div class="sad-top">
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
                ?>
                <div class="sad-top-left">
                    <div class="sad-row">
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
                    <div class="sad-card">
                        <div class="sad-card-title">Grafik 30 Hari Terakhir</div>
                        <canvas id="sadThirtyChart"></canvas>
                    </div>
                </div>
                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-card-title">System Health</div>
                        <div class="sad-chips">
                            <span class="sad-chip">PHP <?php echo esc_html($php_version); ?></span>
                            <span class="sad-chip">Memory <?php echo esc_html($memory_limit); ?></span>
                            <span class="sad-chip">Max Exec <?php echo esc_html($max_execution_time); ?>s</span>
                        </div>
                    </div>
                    <div class="sad-card">
                        <div class="sad-card-title">Informasi Situs</div>
                        <table class="widefat striped" style="border:none; box-shadow:none;">
                            <thead>
                                <tr style="background-color: #f0f0f1;">
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
                <div class="sad-card">
                    <div class="sad-card-title">Status Fitur</div>
                    <table class="widefat striped" style="border:none; box-shadow:none;">
                        <thead>
                            <tr style="background-color: #f0f0f1;">
                                <th>Fitur</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Disable Comments</td>
                                <td><?php echo get_option('fully_disable_comment') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Hide Admin Notice</td>
                                <td><?php echo get_option('hide_admin_notice') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Maintenance Mode</td>
                                <td><?php echo get_option('maintenance_mode') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Limit Login</td>
                                <td><?php echo get_option('limit_login_attempts') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Block wp-login</td>
                                <td><?php echo get_option('block_wp_login') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Disable XML-RPC</td>
                                <td><?php echo get_option('disable_xmlrpc') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Disable REST API</td>
                                <td><?php echo get_option('disable_rest_api') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>Disable Gutenberg</td>
                                <td><?php echo get_option('disable_gutenberg') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                            <tr>
                                <td>reCaptcha</td>
                                <td><?php echo get_option('captcha_Sweetaddons') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="sad-card sad-actions">
                    <div class="sad-card-title">Aksi Cepat</div>
                    <div class="sad-actions-row">
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_visitor_stats'); ?>" class="button button-primary">Statistik</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_seo'); ?>" class="button button-primary">SEO</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_recaptcha'); ?>" class="button button-primary">reCaptcha</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whitelabel'); ?>" class="button button-primary">White Label</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whatsapp'); ?>" class="button button-primary">WhatsApp</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_umum'); ?>" class="button button-secondary">Umum</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_maintenance'); ?>" class="button button-secondary">Maintenance</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_block'); ?>" class="button button-secondary">Blokir Login</a>
                        <a href="<?php echo admin_url('admin.php?page=Sweetaddons_spam'); ?>" class="button button-secondary">Proteksi Spam</a>
                    </div>
                </div>
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
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Pengunjung Unik',
                                    data: uniqueData,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16,185,129,0.15)',
                                    tension: 0.35,
                                    fill: true
                                },
                                {
                                    label: 'Total Kunjungan',
                                    data: totalData,
                                    borderColor: '#0ea5e9',
                                    backgroundColor: 'rgba(14,165,233,0.1)',
                                    tension: 0.35,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                }
            })();
        </script>
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
                <h3 style="margin-top: 0; color: #23282d;">🌐 Informasi Website</h3>
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
                <h3 style="margin-top: 0; color: #23282d;">📝 Statistik Konten</h3>
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
                <h3 style="margin-top: 0; color: #23282d;">🎨 Theme & Plugin</h3>
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
                <h3 style="margin-top: 0; color: #23282d;">🖥️ Server Information</h3>
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
                <h3 style="margin-top: 0; color: #23282d;">⚙️ <?php echo class_exists('Sweetaddons_WhiteLabel') ? esc_html(Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name')) : 'Sweet Addons'; ?> Status</h3>
                <table class="report-table" style="width: 100%; font-size: 14px;">
                    <tr>
                        <td><strong>Disable Comments:</strong></td>
                        <td><?php echo get_option('fully_disable_comment') ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Hide Admin Notice:</strong></td>
                        <td><?php echo get_option('hide_admin_notice') ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Maintenance Mode:</strong></td>
                        <td><?php echo get_option('maintenance_mode') ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Limit Login Attempts:</strong></td>
                        <td><?php echo get_option('limit_login_attempts') ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Block wp-login:</strong></td>
                        <td><?php echo get_option('block_wp_login') ? '✅ Aktif' : '❌ Nonaktif'; ?></td>
                    </tr>
                </table>
            </div>

            <!-- Quick Actions -->
            <div class="report-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin-top: 0; color: #23282d;">🚀 Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_visitor_stats'); ?>" class="button button-primary">📊 Visitor Statistics</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_seo'); ?>" class="button button-primary">🔍 SEO Settings</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_recaptcha'); ?>" class="button button-primary">🛡️ reCaptcha</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whitelabel'); ?>" class="button button-primary">🏷️ White Label</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whatsapp'); ?>" class="button button-primary">💬 WhatsApp Chat</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_umum'); ?>" class="button button-secondary">Pengaturan Umum</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_maintenance'); ?>" class="button button-secondary">Maintenance Mode</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_block'); ?>" class="button button-secondary">Block Login</a>
                    <a href="<?php echo admin_url('admin.php?page=Sweetaddons_spam'); ?>" class="button button-secondary">Spam Protection</a>
                </div>
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
        $stats_handler = new Sweetaddons_Visitor_Stats();
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null);

        // Handle rebuild request
        $rebuild_message = '';
        if (isset($_POST['rebuild_stats']) && wp_verify_nonce($_POST['_wpnonce'], 'rebuild_stats')) {
            $daily_count = $stats_handler->rebuild_daily_stats();
            $page_count = $stats_handler->rebuild_page_stats();
            $rebuild_message = "<div class='notice notice-success'><p>✅ Statistik berhasil dibangun ulang! Memproses {$daily_count} data harian dan {$page_count} data halaman.</p></div>";
        }

        $summary_stats = $stats_handler->get_summary_stats();
        $daily_stats = $stats_handler->get_daily_stats(30);
        $page_stats = $stats_handler->get_page_stats(30);
        $referer_stats = $stats_handler->get_referer_stats(30);

    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">📊 Statistik Pengunjung</h1>

            <?php echo $rebuild_message; ?>

            <!-- Rebuild Stats Button -->
            <div class="sad-card" style="margin-bottom: 20px;">
                <div class="sad-card-title">Maintenance</div>
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('rebuild_stats'); ?>
                    <input type="hidden" name="rebuild_stats" value="1">
                    <button type="submit" class="button button-secondary" onclick="return confirm('Apakah Anda yakin ingin membangun ulang statistik? Ini akan menghitung ulang semua data dari log yang ada.')">
                        🔄 Bangun Ulang Statistik
                    </button>
                    <span style="margin-left: 10px; color: #666; font-size: 13px;">
                        Gunakan ini jika hitungan pengunjung tampak tidak benar
                    </span>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="sad-grid stats-summary" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">

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

            <!-- Charts Section -->
            <div class="sad-grid charts-section" style="grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">

                <!-- Daily Visits Chart -->
                <div class="sad-card">
                    <div class="sad-card-title">📈 Daily Visits (Last 30 Days)</div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="dailyVisitsChart"></canvas>
                    </div>
                </div>

                <!-- Top Pages Chart -->
                <div class="sad-card">
                    <div class="sad-card-title">📄 Halaman Teratas</div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="topPagesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Data Tables Section -->
            <div class="sad-grid tables-section" style="grid-template-columns: 1fr 1fr; gap: 20px;">

                <!-- Top Pages Table -->
                <div class="sad-card">
                    <div class="sad-card-title">🏆 Halaman Teratas (30 Hari Terakhir)</div>
                    <table class="widefat striped" style="border:none; box-shadow:none;">
                        <thead>
                            <tr style="background-color: #f0f0f1;">
                                <th>Page URL</th>
                                <th>Pengunjung Unik</th>
                                <th>Total Tampilan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($page_stats)): ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; color: #666;">No data available</td>
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
                    <div class="sad-card-title">🔗 Rujukan Teratas (30 Hari Terakhir)</div>
                    <table class="widefat striped" style="border:none; box-shadow:none;">
                        <thead>
                            <tr style="background-color: #f0f0f1;">
                                <th>Referrer</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($referer_stats)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; color: #666;">No data available</td>
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

            <!-- Shortcode Examples -->
            <div class="sad-card" style="margin-top: 20px;">
                <div class="sad-card-title">🔖 Shortcode Statistik</div>
                <p class="sad-subtext" style="margin-bottom: 12px;">Gunakan shortcode berikut untuk menampilkan statistik di halaman atau posting.</p>

                <div class="sad-table">
                    <div>
                        <span>Default</span>
                        <span>
                            <code id="sc-stat-default">[statistic]</code>
                            <button type="button" class="button" onclick="copyShortcode('#sc-stat-default')">Copy</button>
                        </span>
                    </div>
                    <div>
                        <span>Hari ini (minimal, 2 kolom)</span>
                        <span>
                            <code id="sc-stat-today-min">[statistic show="today" style="minimal" columns="2"]</code>
                            <button type="button" class="button" onclick="copyShortcode('#sc-stat-today-min')">Copy</button>
                        </span>
                    </div>
                    <div>
                        <span>Total (cards, 4 kolom)</span>
                        <span>
                            <code id="sc-stat-total-cards">[statistic show="total" style="cards" columns="4"]</code>
                            <button type="button" class="button" onclick="copyShortcode('#sc-stat-total-cards')">Copy</button>
                        </span>
                    </div>
                    <div>
                        <span>Semua (cards, 3 kolom)</span>
                        <span>
                            <code id="sc-stat-all-cards">[statistic show="all" style="cards" columns="3"]</code>
                            <button type="button" class="button" onclick="copyShortcode('#sc-stat-all-cards')">Copy</button>
                        </span>
                    </div>
                </div>

                <div id="copy-success" style="display:none; margin-top:10px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:8px 10px; border-radius:6px;">
                    Shortcode berhasil disalin
                </div>
            </div>
        </div>

        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

            function showCopySuccess() {
                const box = document.getElementById('copy-success');
                if (!box) return;
                box.style.display = 'block';
                box.style.opacity = '1';
                setTimeout(() => {
                    box.style.opacity = '0';
                    box.style.display = 'none';
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

            const dailyCtx = document.getElementById('dailyVisitsChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                            label: 'Pengunjung Unik',
                            data: uniqueVisitsData,
                            borderColor: '#0073aa',
                            backgroundColor: 'rgba(0, 115, 170, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Total Kunjungan',
                            data: totalVisitsData,
                            borderColor: '#00a32a',
                            backgroundColor: 'rgba(0, 163, 42, 0.1)',
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Top Pages Chart
            const pageData = <?php echo json_encode(array_map(function ($page) {
                                    return [
                                        'url' => $page->page_url,
                                        'views' => (int)$page->total_views
                                    ];
                                }, array_slice($page_stats, 0, 8))); ?>;

            const pageLabels = pageData.map(item => item.url);
            const pageViews = pageData.map(item => item.views);

            const pageCtx = document.getElementById('topPagesChart').getContext('2d');
            new Chart(pageCtx, {
                type: 'bar',
                data: {
                    labels: pageLabels,
                    datasets: [{
                        label: 'Page Views',
                        data: pageViews,
                        backgroundColor: [
                            '#0073aa', '#00a32a', '#d63638', '#ff922b',
                            '#7c3aed', '#db2777', '#059669', '#dc2626'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
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
                        }
                    }
                }
            });

            // Copy to clipboard function
            function copyToClipboard(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    // Use modern clipboard API
                    navigator.clipboard.writeText(text).then(function() {
                        showCopySuccess();
                    });
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    textArea.style.top = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        document.execCommand('copy');
                        showCopySuccess();
                    } catch (err) {
                        console.error('Failed to copy text: ', err);
                    }

                    document.body.removeChild(textArea);
                }
            }

            function showCopySuccess() {
                // Create temporary success message
                const message = document.createElement('div');
                message.style.cssText = `
                position: fixed;
                top: 50px;
                right: 20px;
                background: #00a32a;
                color: white;
                padding: 12px 20px;
                border-radius: 6px;
                font-size: 14px;
                z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                transition: all 0.3s ease;
            `;
                message.textContent = '✅ Shortcode copied to clipboard!';
                document.body.appendChild(message);

                // Animate and remove
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        document.body.removeChild(message);
                    }, 300);
                }, 2000);
            }
        </script>

        <style>
            @media (max-width: 768px) {

                .stats-summary,
                .charts-section,
                .tables-section,
                .shortcode-examples {
                    grid-template-columns: 1fr !important;
                }
            }

            .chart-container canvas {
                height: 200px !important;
            }

            .table-container table {
                font-size: 14px;
            }

            .table-container code {
                background: #f1f1f1;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 12px;
            }
        </style>
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
                'sweetaddons_seo_enable_sitemap',
                'sweetaddons_seo_google_search_console',
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
                } else {
                    // For checkboxes that might be unchecked
                    if ($field === 'sweetaddons_seo_enable_sitemap') {
                        update_option($field, '0');
                    }
                }
            }

            echo '<div class="notice notice-success"><p>✅ Pengaturan SEO berhasil disimpan!</p></div>';
        }

        $home_title = get_option('sweetaddons_seo_home_title', '');
        $home_description = get_option('sweetaddons_seo_home_description', '');
        $default_og_image = get_option('sweetaddons_seo_default_og_image', '');
        $twitter_site = get_option('sweetaddons_seo_twitter_site', '');
        $enable_sitemap = get_option('sweetaddons_seo_enable_sitemap', '1');
        $google_search_console = get_option('sweetaddons_seo_google_search_console', '');
        $tpl_single_title = get_option('sweetaddons_seo_template_single_title', '{post_title} | {site_name}');
        $tpl_single_desc = get_option('sweetaddons_seo_template_single_description', '{excerpt}');
        $tpl_page_title = get_option('sweetaddons_seo_template_page_title', '{page_title} | {site_name}');
        $tpl_page_desc = get_option('sweetaddons_seo_template_page_description', '{excerpt}');
        $tpl_cat_title = get_option('sweetaddons_seo_template_category_title', '{category_name} | {site_name}');
        $tpl_cat_desc = get_option('sweetaddons_seo_template_category_description', '{category_description}');
        $tpl_tag_title = get_option('sweetaddons_seo_template_tag_title', '{tag_name} | {site_name}');
        $tpl_tag_desc = get_option('sweetaddons_seo_template_tag_description', '{tag_description}');

    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">🔍 Pengaturan SEO</h1>

            <form method="post" action="" class="sad-form">
                <?php wp_nonce_field('sweetaddons_seo_settings'); ?>

                <div class="sad-top">

                    <!-- Left Column (Main Settings) -->
                    <div class="sad-top-left">

                        <!-- General SEO Settings -->
                        <div class="sad-card" id="seo-general-settings">
                            <div class="sad-card-title">🏠 SEO Halaman Utama</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_home_title">Judul Halaman Utama</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_home_title" name="sweetaddons_seo_home_title" value="<?php echo esc_attr($home_title); ?>" class="large-text" />
                                        <p class="description">Kosongkan untuk menggunakan nama situs dan tagline.</p>
                                        <div id="home-title-counter" style="font-size: 12px; color: #666;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_home_description">Deskripsi Halaman Utama</label></th>
                                    <td>
                                        <textarea id="sweetaddons_seo_home_description" name="sweetaddons_seo_home_description" rows="3" class="large-text"><?php echo esc_textarea($home_description); ?></textarea>
                                        <p class="description">Kosongkan untuk menggunakan tagline situs.</p>
                                        <div id="home-desc-counter" style="font-size: 12px; color: #666;"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="sad-card" id="seo-templates-settings">
                            <div class="sad-card-title">🧩 Template Judul & Deskripsi</div>
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

                        <!-- Social Media Settings -->
                        <div class="sad-card" id="seo-social-settings">
                            <div class="sad-card-title">📱 Social Media</div>
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
                                                        <span>📷 Select Image</span>
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
                                    <th scope="row"><label for="sweetaddons_seo_twitter_site">Twitter Username</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_twitter_site" name="sweetaddons_seo_twitter_site" value="<?php echo esc_attr($twitter_site); ?>" class="regular-text" placeholder="username" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Analytics -->
                        <div class="sad-card">
                            <div class="sad-card-title">📊 Analytics & Tools</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_seo_google_search_console">Search Console</label></th>
                                    <td>
                                        <input type="text" id="sweetaddons_seo_google_search_console" name="sweetaddons_seo_google_search_console" value="<?php echo esc_attr($google_search_console); ?>" class="large-text" placeholder="Verification Code / File Name" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="sad-top-right">

                        <!-- Save Button Card -->
                        <div class="sad-card">
                            <div class="sad-card-title">💾 Simpan Perubahan</div>
                            <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan.</div>
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                        </div>

                        <!-- Technical SEO -->
                        <div class="sad-card" id="seo-technical-settings">
                            <div class="sad-card-title">⚙️ Technical SEO</div>
                            <p>
                                <label>
                                    <input type="checkbox" name="sweetaddons_seo_enable_sitemap" value="1" <?php checked($enable_sitemap, '1'); ?> />
                                    Enable XML Sitemap
                                </label>
                            </p>
                            <?php if ($enable_sitemap): ?>
                                <p class="description">
                                    <a href="<?php echo home_url('/sitemap.xml'); ?>" target="_blank" class="button button-small">View Sitemap</a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Features Info -->
                        <div class="sad-card">
                            <div class="sad-card-title">✨ Fitur SEO</div>
                            <ul style="list-style-type: disc; margin-left: 20px; color: #666;">
                                <li style="margin-bottom: 5px;">Judul & Deskripsi Meta</li>
                                <li style="margin-bottom: 5px;">Open Graph Support</li>
                                <li style="margin-bottom: 5px;">XML Sitemap Generator</li>
                                <li style="margin-bottom: 5px;">Robots.txt Control</li>
                                <li>Schema.org Data</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Character counters
                function updateCounter(input, counter, recommended) {
                    const length = input.val().length;
                    let color = '#666';
                    if (length > recommended + 10) color = '#d63638';
                    else if (length > recommended) color = '#ff922b';
                    else if (length > recommended - 10) color = '#00a32a';

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
                        previewContainer.html('<div style="width: 300px; height: 158px; border: 2px dashed #0073aa; display: flex; align-items: center; justify-content: center; color: #0073aa; font-size: 14px; background: #f9f9f9; border-radius: 4px; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.borderColor=\'#005a87\'; this.style.background=\'#f0f8ff\';" onmouseout="this.style.borderColor=\'#0073aa\'; this.style.background=\'#f9f9f9\';"><div style="text-align: center;"><div style="font-size: 32px; margin-bottom: 8px;">📷</div><div>Click to choose image</div><div style="font-size: 11px; color: #666; margin-top: 4px;">Recommended: 1200x630px</div></div></div>');
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
    <?php
    }

    public function recaptcha_page_callback()
    {
        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_recaptcha_settings')) {
            $captcha_data = array();

            if (isset($_POST['captcha_aktif'])) {
                $captcha_data['aktif'] = sanitize_text_field($_POST['captcha_aktif']);
            }
            if (isset($_POST['captcha_sitekey'])) {
                $captcha_data['sitekey'] = sanitize_text_field($_POST['captcha_sitekey']);
            }
            if (isset($_POST['captcha_secretkey'])) {
                $captcha_data['secretkey'] = sanitize_text_field($_POST['captcha_secretkey']);
            }
            if (isset($_POST['captcha_login'])) {
                $captcha_data['login'] = sanitize_text_field($_POST['captcha_login']);
            }
            if (isset($_POST['captcha_comment'])) {
                $captcha_data['comment'] = sanitize_text_field($_POST['captcha_comment']);
            }
            if (isset($_POST['captcha_register'])) {
                $captcha_data['register'] = sanitize_text_field($_POST['captcha_register']);
            }
            if (isset($_POST['captcha_difficulty'])) {
                $captcha_data['difficulty'] = sanitize_text_field($_POST['captcha_difficulty']);
            }

            update_option('captcha_Sweetaddons', $captcha_data);
            echo '<div class="notice notice-success"><p>✅ Pengaturan reCaptcha berhasil disimpan!</p></div>';
        }

        $captcha_settings = get_option('captcha_Sweetaddons', array());
        $aktif = isset($captcha_settings['aktif']) ? $captcha_settings['aktif'] : '';
        $sitekey = isset($captcha_settings['sitekey']) ? $captcha_settings['sitekey'] : '';
        $secretkey = isset($captcha_settings['secretkey']) ? $captcha_settings['secretkey'] : '';
        $login = isset($captcha_settings['login']) ? $captcha_settings['login'] : '';
        $comment = isset($captcha_settings['comment']) ? $captcha_settings['comment'] : '';
        $register = isset($captcha_settings['register']) ? $captcha_settings['register'] : '';
        $difficulty = isset($captcha_settings['difficulty']) ? $captcha_settings['difficulty'] : 'medium';

    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">🛡️ Pengaturan CAPTCHA Tulisan (Image)</h1>

            <form method="post" action="">
                <?php wp_nonce_field('sweetaddons_recaptcha_settings'); ?>

                <div class="sad-top">
                    <!-- Left Column -->
                    <div class="sad-top-left">

                        <!-- reCaptcha Configuration -->
                        <div class="sad-card" id="recaptcha-general-settings">
                            <div class="sad-card-title">⚙️ Konfigurasi Utama</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Status Fitur</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="captcha_aktif" value="1" <?php checked($aktif, '1'); ?> />
                                            Aktifkan CAPTCHA
                                        </label>
                                        <p class="description">Aktifkan perlindungan CAPTCHA.</p>
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

                        <!-- Protection Areas -->
                        <div class="sad-card" id="recaptcha-protection-settings">
                            <div class="sad-card-title">🔒 Area Perlindungan</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Form Login</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="captcha_login" value="1" <?php checked($login, '1'); ?> />
                                            Lindungi halaman login (wp-login.php)
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Form Registrasi</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="captcha_register" value="1" <?php checked($register, '1'); ?> />
                                            Lindungi form registrasi user baru
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Form Komentar</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="captcha_comment" value="1" <?php checked($comment, '1'); ?> />
                                            Lindungi kolom komentar postingan
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Current Status Details -->
                        <div class="sad-card">
                            <div class="sad-card-title">📊 Detail Status</div>
                            <table class="widefat striped" style="border:none; box-shadow:none;">
                                <thead>
                                    <tr style="background-color: #f0f0f1;">
                                        <th>Fitur</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Generator Lokal</strong></td>
                                        <td>
                                            <?php if ($aktif): ?>
                                                <span style="color: #00a32a; font-weight:bold;">Tersedia</span>
                                            <?php else: ?>
                                                <span style="color: #999;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Pembuatan gambar CAPTCHA di server</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Proteksi Login</strong></td>
                                        <td>
                                            <?php if ($login && $aktif): ?>
                                                <span style="color: #00a32a; font-weight:bold;">Aktif</span>
                                            <?php else: ?>
                                                <span style="color: #999;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Brute force protection</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Proteksi Komentar</strong></td>
                                        <td>
                                            <?php if ($comment && $aktif): ?>
                                                <span style="color: #00a32a; font-weight:bold;">Aktif</span>
                                            <?php else: ?>
                                                <span style="color: #999;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Spam comment protection</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="sad-top-right">

                        <!-- Save Button Card -->
                        <div class="sad-card">
                            <div class="sad-card-title">💾 Simpan Perubahan</div>
                            <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan keys atau area proteksi.</div>
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                        </div>

                        <!-- Setup Instructions -->
                        <div class="sad-card">
                            <div class="sad-card-title">📋 Panduan Setup</div>
                            <h4 style="margin: 10px 0 5px; color: #23282d;">1. Aktivasi</h4>
                            <ul style="list-style-type: disc; margin-left: 20px; color: #666; font-size: 13px; margin-bottom: 15px;">
                                <li>Nyalakan status fitur CAPTCHA</li>
                                <li>Pilih area proteksi: Login, Registrasi, Komentar</li>
                                <li>Tidak memerlukan API key</li>
                            </ul>
                            <h4 style="margin: 10px 0 5px; color: #23282d;">2. Contact Form 7</h4>
                            <p style="font-size: 13px; color: #666; margin-bottom: 5px;">Gunakan tag berikut:</p>
                            <code style="display: block; background: #f0f0f1; padding: 8px; border-radius: 4px; font-size: 12px; margin-bottom: 15px;">[recaptcha]</code>
                            <ul style="list-style-type: disc; margin-left: 20px; color: #666; font-size: 13px;">
                                <li>Logout untuk tes form login</li>
                                <li>Buka postingan untuk tes komentar</li>
                                <li>Pastikan gambar CAPTCHA muncul dan input teks bekerja</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    <?php
    }

    public function whitelabel_page_callback()
    {
        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whitelabel_settings')) {
            $fields = array(
                'sweetaddons_whitelabel_plugin_name',
                'sweetaddons_whitelabel_plugin_uri',
                'sweetaddons_whitelabel_description',
                'sweetaddons_whitelabel_author',
                'sweetaddons_whitelabel_author_uri',
                'sweetaddons_whitelabel_version',
                'sweetaddons_whitelabel_menu_title',
                'sweetaddons_whitelabel_hide_original',
                'sweetaddons_whitelabel_accent_color'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, sanitize_text_field($_POST[$field]));
                } else {
                    // Handle checkbox fields
                    if ($field === 'sweetaddons_whitelabel_hide_original') {
                        delete_option($field);
                    }
                }
            }

            echo '<div class="notice notice-success"><p>✅ Pengaturan White Label berhasil disimpan!</p></div>';
        }

        // Get current settings
        $plugin_name = get_option('sweetaddons_whitelabel_plugin_name', 'Sweet Addons');
        $plugin_uri = get_option('sweetaddons_whitelabel_plugin_uri', 'https://websweetstudio.com');
        $description = get_option('sweetaddons_whitelabel_description', 'Addon plugin for WebsweetStudio Client');
        $author = get_option('sweetaddons_whitelabel_author', 'WebsweetStudio');
        $author_uri = get_option('sweetaddons_whitelabel_author_uri', 'https://websweetstudio.com');
        $version = get_option('sweetaddons_whitelabel_version', '2.2.1');
        $menu_title = get_option('sweetaddons_whitelabel_menu_title', 'Sweet Addons');
        $hide_original = get_option('sweetaddons_whitelabel_hide_original', '');
        $accent_color = get_option('sweetaddons_whitelabel_accent_color', '#2271b1');

        // Get current plugin data for reference
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/sweetaddons/sweetaddons.php');
    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">🏷️ Pengaturan White Label</h1>
            <p>Kustomisasi branding plugin dan informasi yang ditampilkan kepada pengguna.</p>

            <form method="post" action="" class="sad-form">
                <?php wp_nonce_field('sweetaddons_whitelabel_settings'); ?>
                <div class="sad-top">
                    <div class="sad-top-left">
                        <div class="sad-card">
                            <div class="sad-card-title">⚙️ Konfigurasi White Label</div>

                            <!-- Plugin Information -->
                            <h3 style="margin-bottom: 20px;">📋 Informasi Plugin</h3>
                            <p style="margin-bottom: 20px;">Kustomisasi bagaimana plugin muncul di admin WordPress.</p>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_plugin_name">Nama Plugin</label>
                                    </th>
                                    <td>
                                        <input type="text" id="sweetaddons_whitelabel_plugin_name" name="sweetaddons_whitelabel_plugin_name" value="<?php echo esc_attr($plugin_name); ?>" class="large-text" />
                                        <p class="description">Nama yang muncul di daftar plugin dan menu admin.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_description">Deskripsi Plugin</label>
                                    </th>
                                    <td>
                                        <textarea id="sweetaddons_whitelabel_description" name="sweetaddons_whitelabel_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                                        <p class="description">Deskripsi yang ditampilkan di daftar plugin.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_version">Versi</label>
                                    </th>
                                    <td>
                                        <input type="text" id="sweetaddons_whitelabel_version" name="sweetaddons_whitelabel_version" value="<?php echo esc_attr($version); ?>" class="regular-text" />
                                        <p class="description">Nomor versi plugin.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_plugin_uri">Plugin URI</label>
                                    </th>
                                    <td>
                                        <input type="url" id="sweetaddons_whitelabel_plugin_uri" name="sweetaddons_whitelabel_plugin_uri" value="<?php echo esc_url($plugin_uri); ?>" class="large-text" />
                                        <p class="description">URL yang akan dikunjungi pengguna ketika mengklik nama plugin.</p>
                                    </td>
                                </tr>
                            </table>

                            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                            <!-- Author Information -->
                            <h3 style="margin-bottom: 20px;">👤 Informasi Penulis</h3>
                            <p style="margin-bottom: 20px;">Kustomisasi detail penulis yang ditampilkan dalam informasi plugin.</p>

                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_author">Nama Penulis</label>
                                    </th>
                                    <td>
                                        <input type="text" id="sweetaddons_whitelabel_author" name="sweetaddons_whitelabel_author" value="<?php echo esc_attr($author); ?>" class="large-text" />
                                        <p class="description">Nama penulis yang ditampilkan di detail plugin.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_author_uri">URI Penulis</label>
                                    </th>
                                    <td>
                                        <input type="url" id="sweetaddons_whitelabel_author_uri" name="sweetaddons_whitelabel_author_uri" value="<?php echo esc_url($author_uri); ?>" class="large-text" />
                                        <p class="description">The URL users will visit when clicking the author name.</p>
                                    </td>
                                </tr>
                            </table>

                            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                            <!-- Admin Customization -->
                            <h3 style="margin-bottom: 20px;">⚙️ Admin Customization</h3>
                            <p style="margin-bottom: 20px;">Customize the admin interface appearance.</p>

                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="sweetaddons_whitelabel_menu_title">Judul Menu Admin</label>
                                    </th>
                                    <td>
                                        <input type="text" id="sweetaddons_whitelabel_menu_title" name="sweetaddons_whitelabel_menu_title" value="<?php echo esc_attr($menu_title); ?>" class="large-text" />
                                        <p class="description">The title shown in the WordPress admin menu.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Sembunyikan Branding Asli</th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="sweetaddons_whitelabel_hide_original" value="1" <?php checked($hide_original, '1'); ?> />
                                            Hide references to WebsweetStudio in admin interface
                                        </label>
                                        <p class="description">Remove WebsweetStudio branding from admin pages and footers.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="sad-card">
                            <div class="sad-card-title">🎨 Warna Brand</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="sweetaddons_whitelabel_accent_color">Accent Color</label></th>
                                    <td>
                                        <input type="color" id="sweetaddons_whitelabel_accent_color" name="sweetaddons_whitelabel_accent_color" value="<?php echo esc_attr($accent_color); ?>" />
                                        <div class="sad-color-swatches" style="margin-top:10px;">
                                            <?php
                                            $swatches = array('#2271b1', '#00a32a', '#d63638', '#ff922b', '#7c3aed', '#db2777', '#059669', '#dc2626');
                                            foreach ($swatches as $sw) {
                                                echo '<span class="sad-swatch" data-color="' . esc_attr($sw) . '" style="display:inline-block;width:22px;height:22px;border-radius:4px;margin-right:6px;border:1px solid #ccd0d4; background:' . esc_attr($sw) . '; cursor:pointer;"></span>';
                                            }
                                            ?>
                                        </div>
                                        <p class="description">Warna aksen untuk branding admin. Gunakan palet cepat di atas atau pilih manual.</p>
                                    </td>
                                </tr>
                            </table>
                            <script>
                                jQuery(function($) {
                                    $('.sad-swatch').on('click', function() {
                                        var c = $(this).data('color');
                                        $('#sweetaddons_whitelabel_accent_color').val(c);
                                    });
                                });
                            </script>
                        </div>

                        <div class="sad-card">
                            <div class="sad-card-title">📊 Perbandingan Branding</div>
                            <div class="sad-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="sad-card">
                                    <div class="sad-card-title">🔴 Current (Original)</div>
                                    <table class="form-table">
                                        <tr>
                                            <th>Plugin Name</th>
                                            <td><?php echo esc_html($plugin_data['Name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td><?php echo esc_html($plugin_data['Description']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Version</th>
                                            <td><?php echo esc_html($plugin_data['Version']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Author</th>
                                            <td><?php echo esc_html($plugin_data['Author']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Plugin URI</th>
                                            <td><?php echo esc_html($plugin_data['PluginURI']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="sad-card">
                                    <div class="sad-card-title">🟢 New (White Labeled)</div>
                                    <table class="form-table">
                                        <tr>
                                            <th>Plugin Name</th>
                                            <td><?php echo esc_html($plugin_name); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td><?php echo esc_html($description); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Version</th>
                                            <td><?php echo esc_html($version); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Author</th>
                                            <td><?php echo esc_html($author); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Plugin URI</th>
                                            <td><?php echo esc_html($plugin_uri); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sad-top-right">
                        <div class="sad-card">
                            <div class="sad-card-title">💾 Simpan Perubahan</div>
                            <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan.</div>
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }

    public function login_customizer_page_callback()
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
            echo '<div class="notice notice-success"><p>✅ Pengaturan Login Page Customizer berhasil disimpan!</p></div>';
        }

        // Get current settings
        $login_settings = get_option('sweetaddons_login_customizer', array());
        $logo_url = isset($login_settings['logo_url']) ? $login_settings['logo_url'] : '';
        $bg_color = isset($login_settings['bg_color']) ? $login_settings['bg_color'] : '#f1f1f1';
        $bg_image = isset($login_settings['bg_image']) ? $login_settings['bg_image'] : '';
        $btn_color = isset($login_settings['btn_color']) ? $login_settings['btn_color'] : '#2271b1';
        $btn_text_color = isset($login_settings['btn_text_color']) ? $login_settings['btn_text_color'] : '#ffffff';
    ?>
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
    <?php
    }

    public function db_cleaner_page_callback()
    {
        // Handle cleanup
        if (isset($_POST['submit']) && check_admin_referer('sweetaddons_db_cleaner_action', 'sweetaddons_db_cleaner_nonce')) {
            $items = isset($_POST['items']) ? $_POST['items'] : array();

            if (!empty($items)) {
                $cleaner = new Sweetaddons_Database_Cleaner();
                $cleaned_items = $cleaner->clean_items($items);

                $message = '✅ Berhasil membersihkan: ' . implode(', ', $cleaned_items);
                echo '<div class="notice notice-success"><p>' . esc_html($message) . '</p></div>';
            } else {
                echo '<div class="notice notice-warning"><p>⚠️ Tidak ada item yang dipilih untuk dibersihkan.</p></div>';
            }
        }

        $cleaner = new Sweetaddons_Database_Cleaner();
        $stats = $cleaner->get_stats();
    ?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">🧹 Database Cleaner</h1>

            <form method="post" action="">
                <?php wp_nonce_field('sweetaddons_db_cleaner_action', 'sweetaddons_db_cleaner_nonce'); ?>

                <div class="sad-top">
                    <div class="sad-top-left">
                        <div class="sad-card">
                            <div class="sad-card-title">🗑️ Item yang Dapat Diberishkan</div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><input type="checkbox" name="items[]" value="revisions" checked> Post Revisions</th>
                                    <td><span class="sad-badge sad-badge-warning"><?php echo $stats['revisions']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_revisions'])); ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row"><input type="checkbox" name="items[]" value="auto_drafts" checked> Auto Drafts</th>
                                    <td><span class="sad-badge sad-badge-warning"><?php echo $stats['auto_drafts']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_auto_drafts'])); ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row"><input type="checkbox" name="items[]" value="spam_comments" checked> Spam Comments</th>
                                    <td><span class="sad-badge sad-badge-danger"><?php echo $stats['spam_comments']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_spam_comments'])); ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row"><input type="checkbox" name="items[]" value="trashed_comments" checked> Trashed Comments</th>
                                    <td><span class="sad-badge sad-badge-danger"><?php echo $stats['trashed_comments']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_trashed_comments'])); ?></span></td>
                                </tr>
                                <tr>
                                    <th scope="row"><input type="checkbox" name="items[]" value="expired_transients" checked> Expired Transients</th>
                                    <td><span class="sad-badge sad-badge-info"><?php echo $stats['expired_transients']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_expired_transients'])); ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="sad-top-right">
                        <div class="sad-card">
                            <div class="sad-card-title">🚀 Aksi Pembersihan</div>
                            <p>Klik tombol di bawah untuk membersihkan item yang dipilih dari database. Pastikan Anda telah melakukan backup database sebelum melakukan pembersihan.</p>
                            <?php submit_button('Bersihkan Database Sekarang', 'primary', 'submit', false, array('style' => 'width: 100%;', 'onclick' => "return confirm('Apakah Anda yakin ingin membersihkan database? Tindakan ini tidak dapat dibatalkan.');")); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }

    public function whatsapp_page_callback()
    {
        // Handle settings save
        if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'sweetaddons_whatsapp_settings')) {
            $fields = array(
                'sweetaddons_whatsapp_enable',
                'sweetaddons_whatsapp_phone',
                'sweetaddons_whatsapp_message',
                'sweetaddons_whatsapp_button_text',
                'sweetaddons_whatsapp_position',
                'sweetaddons_whatsapp_color',
                'sweetaddons_whatsapp_size',
                'sweetaddons_whatsapp_offset_x',
                'sweetaddons_whatsapp_offset_y',
                'sweetaddons_whatsapp_show_mobile',
                'sweetaddons_whatsapp_show_desktop',
                'sweetaddons_whatsapp_animation',
                'sweetaddons_whatsapp_bubble_style',
                'sweetaddons_whatsapp_show_tooltip'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, sanitize_text_field($_POST[$field]));
                } else {
                    // Handle checkbox fields
                    if (in_array($field, ['sweetaddons_whatsapp_enable', 'sweetaddons_whatsapp_show_mobile', 'sweetaddons_whatsapp_show_desktop', 'sweetaddons_whatsapp_show_tooltip'])) {
                        delete_option($field);
                    }
                }
            }

            echo '<div class="notice notice-success"><p>✅ Pengaturan Chat WhatsApp berhasil disimpan!</p></div>';
        }

        // Get current settings
        $enable = get_option('sweetaddons_whatsapp_enable', '');
        $phone = get_option('sweetaddons_whatsapp_phone', '');
        $message = get_option('sweetaddons_whatsapp_message', 'Halo! Saya butuh bantuan.');
        $button_text = get_option('sweetaddons_whatsapp_button_text', 'Chat dengan kami');
        $position = get_option('sweetaddons_whatsapp_position', 'bottom-right');
        $color = get_option('sweetaddons_whatsapp_color', '#25D366');
        $size = get_option('sweetaddons_whatsapp_size', '60');
        $offset_x = get_option('sweetaddons_whatsapp_offset_x', '20');
        $offset_y = get_option('sweetaddons_whatsapp_offset_y', '20');
        $show_mobile = get_option('sweetaddons_whatsapp_show_mobile', '1');
        $show_desktop = get_option('sweetaddons_whatsapp_show_desktop', '1');
        $animation = get_option('sweetaddons_whatsapp_animation', 'pulse');
        $bubble_style = get_option('sweetaddons_whatsapp_bubble_style', 'circle');
        $show_tooltip = get_option('sweetaddons_whatsapp_show_tooltip', '1');

        // Summary calculations
        $is_active = ($enable === '1');
        $status_text = $is_active ? 'Active' : 'Inactive';
        $status_color = $is_active ? '#28a745' : '#d63638';

        $display_phone = !empty($phone) ? $phone : 'Not Configured';
        $display_position = ucwords(str_replace('-', ' ', $position));
    ?>
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
<?php
    }
}

// Initialize the Pengaturan Admin page
$custom_admin_options_page = new Custom_Admin_Option_Page();
