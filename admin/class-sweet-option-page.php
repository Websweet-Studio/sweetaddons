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
            wp_enqueue_script('alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), null);
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

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/spam-protection.php';
    }

    public function options_page_callback()
    {
        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/dashboard.php';
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

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/website-report.php';

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

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/visitor-stats.php';
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

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/seo.php';
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

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/recaptcha.php';
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
        $description = get_option('sweetaddons_whitelabel_description', 'Plugin pendukung tema.');
        $author = get_option('sweetaddons_whitelabel_author', 'WebsweetStudio');
        $author_uri = get_option('sweetaddons_whitelabel_author_uri', 'https://websweetstudio.com');
        $version = get_option('sweetaddons_whitelabel_version', '2.2.1');
        $menu_title = get_option('sweetaddons_whitelabel_menu_title', 'Sweet Addons');
        $hide_original = get_option('sweetaddons_whitelabel_hide_original', '');
        $accent_color = get_option('sweetaddons_whitelabel_accent_color', '#2271b1');

        // Get current plugin data for reference
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/sweetaddons/sweetaddons.php');

        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/whitelabel.php';
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
        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/login-customizer.php';
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
        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/db-cleaner.php';
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
        include plugin_dir_path(dirname(__FILE__)) . 'template/admin/whatsapp.php';
    }
}

// Initialize the Pengaturan Admin page
$custom_admin_options_page = new Custom_Admin_Option_Page();
