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
        $config = Sweetaddons_Redis_Cache::get_config();
        $server = Sweetaddons_Redis_Cache::detect_server();
        $plugin = Sweetaddons_Redis_Cache::detect_plugin();
        ?>
        <?php Sweetaddons_Admin_Layout::open('Optimasi', 'Sweetaddons_optimasi'); ?>

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
}
