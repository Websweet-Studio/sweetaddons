<?php

/**
 * Optimasi settings page functionality.
 *
 * @link       https://websweetstudio.com
 * @since      3.0.22
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sweet_Option_Optimasi
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function add_submenu_page()
    {
        add_submenu_page(
            'custom_admin_options',       // Parent slug
            'Optimasi',                   // Page title
            'Optimasi',                   // Menu title
            'manage_options',             // Capability
            'Sweetaddons_optimasi',       // Menu slug
            array($this, 'page_callback') // Callback
        );
    }

    public function register_settings()
    {
        register_setting('sweetaddons_optimasi_group', Sweetaddons_Redis_Cache::OPTION_KEY, array(
            'type'              => 'array',
            'sanitize_callback' => array($this, 'sanitize_config'),
            'default'           => array(
                'host'     => '127.0.0.1',
                'port'     => 6379,
                'password' => '',
                'database' => 0,
            ),
        ));

        register_setting('sweetaddons_optimasi_group', Sweetaddons_Head_Cleanup::OPTION_KEY, array(
            'type'              => 'array',
            'sanitize_callback' => array($this, 'sanitize_head_cleanup'),
            'default'           => Sweetaddons_Head_Cleanup::get_config(),
        ));
    }

    public function sanitize_head_cleanup($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $allowed = array(
            'remove_emoji', 'remove_rsd', 'remove_wlw', 'remove_shortlink',
            'remove_rest_link', 'remove_oembed', 'remove_generator', 'disable_pingback',
        );

        $output = array();
        foreach ($allowed as $key) {
            $output[$key] = isset($input[$key]) ? 1 : 0;
        }
        return $output;
    }

    public function sanitize_config($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $output = array();
        $output['host']     = isset($input['host']) ? preg_replace('/[^A-Za-z0-9._-]/', '', (string) $input['host']) : '127.0.0.1';
        $output['port']     = isset($input['port']) ? max(1, min(65535, (int) $input['port'])) : 6379;
        $output['password'] = isset($input['password']) ? (string) $input['password'] : '';
        $output['database'] = isset($input['database']) ? max(0, min(15, (int) $input['database'])) : 0;

        return $output;
    }

    public function enqueue_admin_assets($hook)
    {
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if ($page !== 'Sweetaddons_optimasi') {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'sweetaddons-optimasi',
            SWEETADDONS_PLUGIN_DIR_URL . 'assets/admin/js/sweetaddons-optimasi.js',
            array('jquery'),
            SWEETADDONS_VERSION,
            true
        );
        wp_enqueue_style(
            'sweetaddons-optimasi',
            SWEETADDONS_PLUGIN_DIR_URL . 'assets/admin/css/sweetaddons-optimasi.css',
            array(),
            SWEETADDONS_VERSION
        );

        wp_localize_script('sweetaddons-optimasi', 'SweetaddonsRedis', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('sweetaddons_redis_nonce'),
            'i18n'     => array(
                'testing'      => __('Menguji koneksi...', 'sweetaddons'),
                'flushing'     => __('Mengosongkan cache...', 'sweetaddons'),
                'loading'      => __('Memuat statistik...', 'sweetaddons'),
                'confirmFlush' => __('Yakin ingin mengosongkan seluruh cache Redis? Tindakan ini tidak dapat dibatalkan.', 'sweetaddons'),
            ),
        ));
    }

    public function page_callback()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'redis';

        $subnav = Sweetaddons_Admin_Layout::get_optimasi_subnav();
        Sweetaddons_Admin_Layout::open('Optimasi', 'Sweetaddons_optimasi', $subnav);

        if ($current_tab === 'dbcleaner') {
            $this->render_dbcleaner_tab();
            Sweetaddons_Admin_Layout::close();
            return;
        }

        if ($current_tab === 'headcleanup') {
            $this->render_headcleanup_tab();
            Sweetaddons_Admin_Layout::close();
            return;
        }

        $config = Sweetaddons_Redis_Cache::get_config();
        $server = Sweetaddons_Redis_Cache::detect_server();
        $plugin = Sweetaddons_Redis_Cache::detect_plugin();
        ?>

        <form method="post" action="options.php" class="sad-form" id="sweetaddons-redis-config-form">
            <?php settings_fields('sweetaddons_optimasi_group'); ?>

            <div class="sad-top">
                <div class="sad-top-left">

                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title"><?php esc_html_e('Status Server Redis', 'sweetaddons'); ?></div>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e('Driver', 'sweetaddons'); ?></th>
                                <td>
                                    <?php if ('phpredis' === $server['driver']) : ?>
                                        <span class="sad-badge sad-badge--success">phpRedis <?php echo esc_html($server['version']); ?></span>
                                    <?php elseif ('predis' === $server['driver']) : ?>
                                        <span class="sad-badge sad-badge--success">Predis <?php echo esc_html($server['version']); ?></span>
                                    <?php else : ?>
                                        <span class="sad-badge sad-badge--error"><?php esc_html_e('Tidak terdeteksi', 'sweetaddons'); ?></span>
                                        <p class="description"><?php esc_html_e('Server tidak memiliki ekstensi Redis. Hubungi provider hosting untuk mengaktifkan phpRedis atau install Predis.', 'sweetaddons'); ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Plugin Redis Object Cache', 'sweetaddons'); ?></th>
                                <td>
                                    <?php if ($plugin['installed'] && $plugin['active']) : ?>
                                        <span class="sad-badge sad-badge--success"><?php
                                            /* translators: %s: plugin version */
                                            printf(esc_html__('Aktif (v%s)', 'sweetaddons'), esc_html($plugin['version']));
                                        ?></span>
                                        <?php if ($plugin['dropin_present']) : ?>
                                            <span class="sad-badge sad-badge--success"><?php esc_html_e('Drop-in Aktif', 'sweetaddons'); ?></span>
                                        <?php else : ?>
                                            <p class="description"><?php esc_html_e('Plugin aktif, tetapi drop-in object-cache.php belum ada. Buka halaman pengaturan Redis Object Cache untuk mengaktifkannya.', 'sweetaddons'); ?></p>
                                        <?php endif; ?>
                                    <?php elseif ($plugin['installed']) : ?>
                                        <span class="sad-badge sad-badge--warning"><?php esc_html_e('Terinstall, tidak aktif', 'sweetaddons'); ?></span>
                                    <?php else : ?>
                                        <span class="sad-badge sad-badge--warning"><?php esc_html_e('Belum terinstall', 'sweetaddons'); ?></span>
                                        <?php if ($server['loaded']) : ?>
                                            <button type="button" class="button button-secondary" id="sweetaddons-install-redis-plugin" style="margin-left:8px;">
                                                <?php esc_html_e('Install & Aktifkan', 'sweetaddons'); ?>
                                            </button>
                                        <?php else : ?>
                                            <p class="description"><?php esc_html_e('Install plugin Redis Object Cache terlebih dahulu setelah ekstensi Redis tersedia di server.', 'sweetaddons'); ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title"><?php esc_html_e('Konfigurasi Redis', 'sweetaddons'); ?></div>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="sweetaddons-redis-host"><?php esc_html_e('Host', 'sweetaddons'); ?></label></th>
                                <td>
                                    <input type="text" id="sweetaddons-redis-host"
                                        name="<?php echo esc_attr(Sweetaddons_Redis_Cache::OPTION_KEY); ?>[host]"
                                        value="<?php echo esc_attr($config['host']); ?>"
                                        class="regular-text" placeholder="127.0.0.1">
                                    <p class="description"><?php esc_html_e('Biasanya 127.0.0.1 untuk local server, atau alamat internal host.', 'sweetaddons'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons-redis-port"><?php esc_html_e('Port', 'sweetaddons'); ?></label></th>
                                <td>
                                    <input type="number" id="sweetaddons-redis-port" min="1" max="65535"
                                        name="<?php echo esc_attr(Sweetaddons_Redis_Cache::OPTION_KEY); ?>[port]"
                                        value="<?php echo esc_attr($config['port']); ?>"
                                        class="small-text" placeholder="6379">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="sweetaddons-redis-password"><?php esc_html_e('Password (opsional)', 'sweetaddons'); ?></label></th>
                                <td>
                                    <input type="password" id="sweetaddons-redis-password" autocomplete="off"
                                        name="<?php echo esc_attr(Sweetaddons_Redis_Cache::OPTION_KEY); ?>[password]"
                                        value="<?php echo esc_attr($config['password']); ?>"
                                        class="regular-text">
                                    <p class="description"><?php esc_html_e('Kosongkan jika Redis tidak menggunakan auth.', 'sweetaddons'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php if ($server['loaded']) : ?>
                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title"><?php esc_html_e('Aksi', 'sweetaddons'); ?></div>
                        <p>
                            <button type="button" class="button button-secondary" id="sweetaddons-redis-test">
                                <?php esc_html_e('Tes Koneksi', 'sweetaddons'); ?>
                            </button>
                            <button type="button" class="button" id="sweetaddons-redis-flush" style="margin-left:6px;">
                                <?php esc_html_e('Flush Cache', 'sweetaddons'); ?>
                            </button>
                            <button type="button" class="button" id="sweetaddons-redis-refresh-stats" style="margin-left:6px;">
                                <?php esc_html_e('Refresh Statistik', 'sweetaddons'); ?>
                            </button>
                        </p>
                        <div id="sweetaddons-redis-feedback" class="sad-feedback" style="display:none;"></div>
                    </div>

                    <div class="sad-card sad-mb-16">
                        <div class="sad-card-title"><?php esc_html_e('Statistik Cache', 'sweetaddons'); ?></div>
                        <table class="form-table" id="sweetaddons-redis-stats">
                            <tr>
                                <th scope="row"><?php esc_html_e('Driver Aktif', 'sweetaddons'); ?></th>
                                <td><span data-stat="driver">—</span></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Cache Hits', 'sweetaddons'); ?></th>
                                <td><span data-stat="hits">0</span></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Cache Misses', 'sweetaddons'); ?></th>
                                <td><span data-stat="misses">0</span></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Hit Ratio', 'sweetaddons'); ?></th>
                                <td><span data-stat="ratio">0</span>%</td>
                            </tr>
                        </table>
                        <p class="description"><?php esc_html_e('Statistik ini dikumpulkan dari runtime WP_Object_Cache. Tekan "Refresh Statistik" untuk memperbarui.', 'sweetaddons'); ?></p>
                    </div>
                    <?php endif; ?>

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

        <?php Sweetaddons_Admin_Layout::close(); ?>
<?php
    }

    public function save_button()
    {
        echo '<button type="submit" name="submit" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;" onmouseenter="this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 4px 12px rgba(37,99,235,0.4)\';" onmouseleave="this.style.transform=\'translateY(0)\';this.style.boxShadow=\'0 2px 6px rgba(37,99,235,0.25)\';"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>Simpan Pengaturan</button>';
    }

    private function render_headcleanup_tab()
    {
        $config = Sweetaddons_Head_Cleanup::get_config();

        $fields = array(
            'remove_emoji'       => array('title' => 'Hapus Emoji Scripts',       'label' => 'Hapus wp-emoji-release.min.js dan inline CSS emoji dari halaman. Mengurangi 1-2 HTTP request.'),
            'remove_rsd'         => array('title' => 'Hapus RSD Link',            'label' => 'Hapus link Really Simple Discovery dari <head>. Tidak diperlukan kecuali pakai layanan ping eksternal.'),
            'remove_wlw'         => array('title' => 'Hapus WLW Manifest',        'label' => 'Hapus Windows Live Writer manifest link. Tidak lagi relevan.'),
            'remove_shortlink'   => array('title' => 'Hapus Shortlink',           'label' => 'Hapus <link rel="shortlink"> dari header HTTP dan HTML. Tidak perlu untuk SEO.'),
            'remove_rest_link'   => array('title' => 'Hapus REST API Link',       'label' => 'Hapus link REST API URL dari <head>. Halaman tetap bisa akses API — hanya link header yang dihapus.'),
            'remove_oembed'      => array('title' => 'Hapus oEmbed Links',        'label' => 'Hapus oEmbed discovery links dan rewrite rules. Mencegah embedding konten situs dari platform eksternal.'),
            'remove_generator'   => array('title' => 'Hapus Generator Meta',      'label' => 'Hapus <meta name="generator"> yang menampilkan versi WordPress. Tambahan keamanan ringan.'),
            'disable_pingback'   => array('title' => 'Nonaktifkan Self Pingback', 'label' => 'Blokir pingback dari URL sendiri. Kurangi bloat database dan beban XML-RPC.'),
        );
?>
        <form method="post" action="options.php" class="sad-form">
            <?php settings_fields('sweetaddons_optimasi_group'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card">
                        <div class="sad-card-title" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <span>Pengaturan Head Cleanup</span>
                            <button type="button" id="sweetaddons-hc-toggle" class="button" style="margin:0;">
                                <span class="sweetaddons-hc-toggle-label">Pilih Semua</span>
                            </button>
                        </div>
                        <p class="description" style="margin-bottom:12px;">Pilih item yang ingin dihapus dari <code>&lt;head&gt;</code>. Semua direkomendasikan aktif untuk performa maksimal.</p>
                        <table class="form-table">
                            <?php foreach ($fields as $key => $field) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($field['title']); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox"
                                            name="<?php echo esc_attr(Sweetaddons_Head_Cleanup::OPTION_KEY); ?>[<?php echo esc_attr($key); ?>]"
                                            value="1"
                                            <?php checked(1, isset($config[$key]) ? (int) $config[$key] : 1); ?>>
                                        <?php echo esc_html($field['label']); ?>
                                    </label>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
        (function() {
            var btn = document.getElementById('sweetaddons-hc-toggle');
            var form = btn.closest('form');
            var checkboxes = form.querySelectorAll('input[type="checkbox"]');
            var labelEl = btn.querySelector('.sweetaddons-hc-toggle-label');
            var allOn = true;

            function updateLabel() {
                allOn = true;
                checkboxes.forEach(function(cb) { if (!cb.checked) allOn = false; });
                labelEl.textContent = allOn ? 'Batal Pilih Semua' : 'Pilih Semua';
            }

            btn.addEventListener('click', function() {
                allOn = !allOn;
                checkboxes.forEach(function(cb) { cb.checked = allOn; });
                labelEl.textContent = allOn ? 'Batal Pilih Semua' : 'Pilih Semua';
            });

            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updateLabel);
            });
            updateLabel();
        })();
        </script>
<?php
    }

    private function render_dbcleaner_tab()
    {
        require_once dirname(plugin_dir_path(__FILE__)) . '/includes/class-sweetaddons-database-cleaner.php';
    ?>
        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_db_cleaner_action', 'sweetaddons_db_cleaner_nonce'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card">
                        <div class="sad-card-title" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <span>Item yang Dapat Diberishkan</span>
                            <button type="button" id="sweetaddons-dbc-toggle" class="button" style="margin:0;">
                                <span class="sweetaddons-dbc-toggle-label">Pilih Semua</span>
                            </button>
                        </div>
                        <?php
                        $cleaner = new Sweetaddons_Database_Cleaner();
                        $stats = $cleaner->get_stats();
                        $items = array(
                            'revisions'          => 'Revisi Postingan',
                            'auto_drafts'        => 'Draft Otomatis',
                            'spam_comments'      => 'Komentar Spam',
                            'trashed_comments'   => 'Komentar di Trash',
                            'expired_transients' => 'Transien Kadaluarsa',
                        );
                        foreach ($items as $key => $label) :
                            $count = (int) ($stats[$key] ?? 0);
                            $size  = (int) ($stats['size_' . $key] ?? 0);
                            if ($count === 0) {
                                continue;
                            }
                        ?>
                            <div class="sad-checkbox" style="margin-bottom: 12px;">
                                <input type="checkbox" id="clean_<?php echo esc_attr($key); ?>" name="clean_items[]" value="<?php echo esc_attr($key); ?>">
                                <label for="clean_<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($label); ?>
                                    <span class="sad-count">(<?php echo esc_html(number_format($count)); ?><?php if ($size > 0) : ?> · <?php echo esc_html($cleaner->format_bytes($size)); ?><?php endif; ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <button type="submit" name="sweetaddons_db_cleaner_clean" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;" onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.4)';" onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 6px rgba(37,99,235,0.25)';">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 22-1-4"/><path d="M19 14a1 1 0 0 0 1-1v-1a2 2 0 0 0-2-2h-3a1 1 0 0 1-1-1V4a2 2 0 0 0-4 0v5a1 1 0 0 1-1 1H6a2 2 0 0 0-2 2v1a1 1 0 0 0 1 1"/><path d="M19 14H5l-1.973 6.767A1 1 0 0 0 4 22h16a1 1 0 0 0 .973-1.233z"/><path d="m8 22 1-4"/></svg>
                                Bersihkan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <script>
        (function() {
            var btn = document.getElementById('sweetaddons-dbc-toggle');
            var form = btn.closest('form');
            var checkboxes = form.querySelectorAll('input[name="clean_items[]"]');
            var labelEl = btn.querySelector('.sweetaddons-dbc-toggle-label');
            var allOn = true;

            function updateLabel() {
                allOn = true;
                checkboxes.forEach(function(cb) { if (!cb.checked) allOn = false; });
                labelEl.textContent = allOn ? 'Batal Pilih Semua' : 'Pilih Semua';
            }

            btn.addEventListener('click', function() {
                allOn = !allOn;
                checkboxes.forEach(function(cb) { cb.checked = allOn; });
                labelEl.textContent = allOn ? 'Batal Pilih Semua' : 'Pilih Semua';
            });

            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updateLabel);
            });
            updateLabel();
        })();
        </script>
<?php
        if (
            isset($_POST['sweetaddons_db_cleaner_clean'], $_POST['sweetaddons_db_cleaner_nonce'])
            && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sweetaddons_db_cleaner_nonce'])), 'sweetaddons_db_cleaner_action')
        ) {
            $items_to_clean = isset($_POST['clean_items']) ? array_map('sanitize_text_field', wp_unslash($_POST['clean_items'])) : array();
            if (!empty($items_to_clean)) {
                $cleaner = new Sweetaddons_Database_Cleaner();
                $cleaned = $cleaner->clean($items_to_clean);
                if (!empty($cleaned)) {
                    $total_cleaned = array_sum($cleaned);
                    echo '<div class="sad-notice sad-notice-success"><p>Database berhasil dibersihkan. Total ' . $total_cleaned . ' item dihapus.</p></div>';
                } else {
                    echo '<div class="sad-notice sad-notice-warning"><p>Tidak ada item yang berhasil dibersihkan.</p></div>';
                }
            } else {
                echo '<div class="sad-notice sad-notice-warning"><p>Tidak ada item yang dipilih untuk dibersihkan.</p></div>';
            }
        }
    }
}
