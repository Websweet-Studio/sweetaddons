<?php
namespace Sweetaddons;

use Sweetaddons\Admin\Admin;
use Sweetaddons\Admin\AdminLayout;
use Sweetaddons\Admin\OptionPage;
use Sweetaddons\Admin\OptionUmum;
use Sweetaddons\Admin\OptionMaintenance;
use Sweetaddons\Admin\OptionBlock;
use Sweetaddons\Public\SweetaddonsPublic;

/**
 * The core plugin class.
 *
 * @since      3.1.0
 * @package    Core
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class Core
{
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct()
    {
        if (defined('SWEETADDONS_VERSION')) {
            $this->version = SWEETADDONS_VERSION;
        } else {
            $this->version = '3.1.0';
        }
        $this->plugin_name = 'Core';

        $this->load_dependencies();

        // Auto updater
        new AutoUpdater(
            plugin_dir_path(dirname(__FILE__)) . 'Core.php',
            $this->version
        );

        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        // Initialize admin option pages (only in admin)
        if (is_admin()) {
            new \Sweetaddons\Admin\OptionUmum();
            new \Sweetaddons\Admin\OptionMaintenance();
            new \Sweetaddons\Admin\OptionBlock();
            new \Sweetaddons\Admin\OptionPage();
        }
    }

    /**
     * Load dependencies via PSR-4 autoloader only.
     * No more manual require_once statements.
     */
    private function load_dependencies()
    {
        $this->loader = new Loader();
        $this->loader->add_action('init', $this, 'redirect_legacy_admin_paths', 0);
    }

    private function set_locale()
    {
        $plugin_i18n = new I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_head', $plugin_admin, 'define_global_js_variable');
    }

    private function define_public_hooks()
    {
        $plugin_public = new SweetaddonsPublic($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Initialize all feature modules
        new VisitorStats();
        new SEO();
        new WhatsApp();
        new Breadcrumb();
        new LoginCustomizer();
        new DatabaseCleaner();

        // These previously instantiated themselves at file bottom
        new DisableComments();
        new HideAdminNotice();
        new LimitLoginAttempts();
        new MaintenanceMode();
        new DisableXmlrpc();
        new DisableRestApi();
        new DisableGutenberg();
        new BlockWpLogin();
        new ClassicWidget();
        new RemoveSlugCategory();
        new Captcha();
        new StandarEditor();

        // WhiteLabel — admin only
        if (is_admin()) {
            new WhiteLabel();
        }
    }

    public function run()
    {
        $this->loader->run();
    }

    public function redirect_legacy_admin_paths()
    {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($request_uri === '') {
            return;
        }

        $path = wp_parse_url($request_uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            return;
        }

        $path = rtrim($path, '/');

        $admin_page_by_path = array(
            '/wp-admin/Sweetaddons_umum' => 'Sweetaddons_umum',
            '/wp-admin/custom_admin_options' => 'custom_admin_options',
        );

        if (isset($admin_page_by_path[$path])) {
            wp_safe_redirect(admin_url('admin.php?page=' . $admin_page_by_path[$path]));
            exit;
        }
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }
}
