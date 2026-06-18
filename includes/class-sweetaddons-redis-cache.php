<?php

/**
 * Redis Cache integration for Sweet Addons.
 *
 * Bertindak sebagai wrapper di sekitar plugin "Redis Object Cache" (Till Krüss)
 * dengan menambah deteksi otomatis ekstensi Redis, tes koneksi, flush cache,
 * dan statistik hit/miss.
 *
 * @link       https://websweetstudio.com
 * @since      3.0.22
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sweetaddons_Redis_Cache
{
    /**
     * Slug plugin Redis Object Cache di WordPress.org.
     */
    const ROC_SLUG = 'redis-cache';

    /**
     * Option key untuk konfigurasi Redis.
     */
    const OPTION_KEY = 'sweetaddons_redis_config';

    /**
     * Transient key untuk menyimpan snapshot statistik.
     */
    const STATS_TRANSIENT = 'sweetaddons_redis_stats';

    public function __construct()
    {
        // Override konfigurasi default plugin Redis Object Cache (jika aktif)
        add_filter('redis_cache_host', array($this, 'filter_redis_host'));
        add_filter('redis_cache_port', array($this, 'filter_redis_port'));
        add_filter('redis_cache_password', array($this, 'filter_redis_password'));
        add_filter('redis_cache_db', array($this, 'filter_redis_db'));

        // AJAX handlers (hanya untuk admin yang login)
        add_action('wp_ajax_sweetaddons_redis_test', array($this, 'ajax_test_connection'));
        add_action('wp_ajax_sweetaddons_redis_flush', array($this, 'ajax_flush_cache'));
        add_action('wp_ajax_sweetaddons_redis_stats', array($this, 'ajax_get_stats'));
        add_action('wp_ajax_sweetaddons_redis_install_plugin', array($this, 'ajax_install_plugin'));
    }

    /* ------------------------------------------------------------------ *
     *  Deteksi server & plugin
     * ------------------------------------------------------------------ */

    /**
     * Mendeteksi ekstensi Redis yang tersedia di server.
     *
     * @return array {
     *   @type string $driver   'phpredis' | 'predis' | 'none'
     *   @type string $version  versi ekstensi / library
     *   @type bool   $loaded   apakah class dapat di-instantiate
     * }
     */
    public static function detect_server()
    {
        if (extension_loaded('redis') && class_exists('Redis')) {
            return array(
                'driver'  => 'phpredis',
                'version' => defined('Redis::VERSION') ? Redis::VERSION : phpversion('redis'),
                'loaded'  => true,
            );
        }

        // Predis tidak di-bundle, hanya terdeteksi jika user/vendor sudah menyediakan.
        if (class_exists('Predis\Client')) {
            return array(
                'driver'  => 'predis',
                'version' => defined('Predis\\Client::VERSION') ? Predis\Client::VERSION : 'unknown',
                'loaded'  => true,
            );
        }

        return array(
            'driver'  => 'none',
            'version' => '',
            'loaded'  => false,
        );
    }

    /**
     * Status plugin Redis Object Cache.
     *
     * @return array {
     *   @type bool   $installed
     *   @type bool   $active
     *   @type bool   $dropin_present
     *   @type string $version
     * }
     */
    public static function detect_plugin()
    {
        $result = array(
            'installed'      => false,
            'active'         => false,
            'dropin_present' => file_exists(WP_CONTENT_DIR . '/object-cache.php'),
            'version'        => '',
        );

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();
        foreach ($plugins as $plugin_file => $plugin_data) {
            if (false !== strpos($plugin_file, self::ROC_SLUG . '/')) {
                $result['installed'] = true;
                $result['version']   = isset($plugin_data['Version']) ? $plugin_data['Version'] : '';
                if (is_plugin_active($plugin_file)) {
                    $result['active'] = true;
                }
                break;
            }
        }

        return $result;
    }

    /* ------------------------------------------------------------------ *
     *  Konfigurasi (wp_options)
     * ------------------------------------------------------------------ */

    /**
     * Ambil konfigurasi tersimpan, digabung dengan default.
     */
    public static function get_config()
    {
        $defaults = array(
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'password' => '',
            'database' => 0,
        );

        $config = get_option(self::OPTION_KEY, array());
        if (!is_array($config)) {
            $config = array();
        }

        return array_merge($defaults, $config);
    }

    public function filter_redis_host($value)
    {
        $config = self::get_config();
        return !empty($config['host']) ? $config['host'] : $value;
    }

    public function filter_redis_port($value)
    {
        $config = self::get_config();
        return !empty($config['port']) ? (int) $config['port'] : $value;
    }

    public function filter_redis_password($value)
    {
        $config = self::get_config();
        return $config['password'] !== '' ? $config['password'] : $value;
    }

    public function filter_redis_db($value)
    {
        $config = self::get_config();
        return isset($config['database']) ? (int) $config['database'] : $value;
    }

    /* ------------------------------------------------------------------ *
     *  Koneksi & operasi
     * ------------------------------------------------------------------ */

    /**
     * Buat instance Redis dari konfigurasi tersimpan.
     *
     * @return object|\Redis|\Predis\Client
     * @throws Exception jika ekstensi tidak tersedia atau koneksi gagal.
     */
    public static function create_client(array $config = null)
    {
        if (null === $config) {
            $config = self::get_config();
        }

        $server = self::detect_server();
        if ('phpredis' === $server['driver']) {
            $client = new Redis();
            $client->connect($config['host'], (int) $config['port'], 1.5);
            if (!empty($config['password'])) {
                $client->auth($config['password']);
            }
            if (isset($config['database']) && (int) $config['database'] > 0) {
                $client->select((int) $config['database']);
            }
            return $client;
        }

        if ('predis' === $server['driver']) {
            $params = array(
                'scheme' => 'tcp',
                'host'   => $config['host'],
                'port'   => (int) $config['port'],
            );
            if (!empty($config['password'])) {
                $params['password'] = $config['password'];
            }
            if (isset($config['database']) && (int) $config['database'] > 0) {
                $params['database'] = (int) $config['database'];
            }
            return new Predis\Client($params);
        }

        throw new Exception(__('Ekstensi PHP untuk Redis tidak tersedia di server.', 'sweetaddons'));
    }

    /* ------------------------------------------------------------------ *
     *  Statistik (best-effort dari WP_Object_Cache)
     * ------------------------------------------------------------------ */

    /**
     * Kumpulkan statistik cache dari wp_cache jika tersedia.
     */
    public static function collect_stats()
    {
        global $wp_object_cache;

        $stats = array(
            'hits'    => 0,
            'misses'  => 0,
            'ratio'   => 0,
            'driver'  => '',
            'enabled' => false,
        );

        if (isset($wp_object_cache) && is_object($wp_object_cache)) {
            $cache = $wp_object_cache;
            if (property_exists($cache, 'cache_hits')) {
                $stats['hits']   = (int) $cache->cache_hits;
                $stats['misses'] = property_exists($cache, 'cache_misses') ? (int) $cache->cache_misses : 0;
                $total           = $stats['hits'] + $stats['misses'];
                $stats['ratio']  = $total > 0 ? round(($stats['hits'] / $total) * 100, 1) : 0;
                $stats['enabled'] = true;
            }

            // deteksi driver dari class name
            $class = strtolower(get_class($cache));
            if (false !== strpos($class, 'redis')) {
                $stats['driver'] = 'Redis';
            } elseif (false !== strpos($class, 'memcached')) {
                $stats['driver'] = 'Memcached';
            } else {
                $stats['driver'] = 'Default';
            }
        }

        return $stats;
    }

    /* ------------------------------------------------------------------ *
     *  AJAX handlers
     * ------------------------------------------------------------------ */

    private function verify_ajax()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Akses ditolak.', 'sweetaddons')), 403);
        }

        check_ajax_referer('sweetaddons_redis_nonce', 'nonce');
    }

    public function ajax_test_connection()
    {
        $this->verify_ajax();

        $host = isset($_POST['host']) ? sanitize_text_field(wp_unslash($_POST['host'])) : '127.0.0.1';
        $port = isset($_POST['port']) ? absint($_POST['port']) : 6379;

        try {
            $client = self::create_client(array(
                'host'     => $host,
                'port'     => $port,
                'password' => '',
                'database' => 0,
            ));
            $client->ping();
            $info = method_exists($client, 'info') ? $client->info('server') : array();
            $version = is_array($info) && isset($info['redis_version']) ? $info['redis_version'] : '';

            wp_send_json_success(array(
                'message' => __('Koneksi ke Redis berhasil.', 'sweetaddons'),
                'version' => $version,
            ));
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => sprintf(
                    /* translators: %s: error message */
                    __('Koneksi gagal: %s', 'sweetaddons'),
                    $e->getMessage()
                ),
            ));
        }
    }

    public function ajax_flush_cache()
    {
        $this->verify_ajax();

        try {
            $client = self::create_client();
            $client->flushDB();

            // Sinkronkan dengan WP Object Cache jika tersedia
            if (function_exists('wp_cache_flush')) {
                wp_cache_flush();
            }

            wp_send_json_success(array(
                'message' => __('Cache Redis berhasil dikosongkan.', 'sweetaddons'),
            ));
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => sprintf(
                    /* translators: %s: error message */
                    __('Flush gagal: %s', 'sweetaddons'),
                    $e->getMessage()
                ),
            ));
        }
    }

    public function ajax_get_stats()
    {
        $this->verify_ajax();

        $stats = self::collect_stats();
        wp_send_json_success($stats);
    }

    public function ajax_install_plugin()
    {
        $this->verify_ajax();

        if (!current_user_can('install_plugins')) {
            wp_send_json_error(array('message' => __('Tidak punya izin untuk install plugin.', 'sweetaddons')), 403);
        }

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $api = plugins_api('plugin_information', array('slug' => self::ROC_SLUG, 'fields' => array('sections' => false)));
        if (is_wp_error($api)) {
            wp_send_json_error(array('message' => $api->get_error_message()));
        }

        $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
        $result   = $upgrader->install('https://downloads.wordpress.org/plugin/' . self::ROC_SLUG . '.zip');
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        // Aktivasi
        $plugin_file = self::ROC_SLUG . '/' . self::ROC_SLUG . '.php';
        $activate    = activate_plugin($plugin_file);
        if (is_wp_error($activate)) {
            wp_send_json_error(array('message' => $activate->get_error_message()));
        }

        wp_send_json_success(array('message' => __('Plugin Redis Object Cache berhasil diinstall dan diaktifkan.', 'sweetaddons')));
    }
}
