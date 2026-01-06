<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class sweetaddons
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sweetaddons_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('SWEETADDONS_VERSION')) {
            $this->version = SWEETADDONS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'sweetaddons';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Sweetaddons_Loader. Orchestrates the hooks of the plugin.
     * - Sweetaddons_i18n. Defines internationalization functionality.
     * - Sweetaddons_Admin. Defines all hooks for the admin area.
     * - Sweetaddons_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-i18n.php';

        /**
         * Berisi Class untuk mematikan fungsi komentar di wordpress.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-disable-comments.php';

        /**
         * Berisi Class untuk mematikan semua notice di wp-admin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-hide-admin-notice.php';

        /**
         * Berisi Class untuk membatasi gagal login.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-limit-login-attempts.php';

        /**
         * Berisi Class untuk fungsi maintenance mode.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-maintenance-mode.php';

        /**
         * Berisi Class untuk disable XML RPC.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-disable-xmlrpc.php';

        /**
         * Berisi Class untuk disable REST API.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-disable-rest-api.php';

        /**
         * Berisi Class untuk disable visual editor.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-disable-gutenberg.php';

        /**
         * Berisi Class untuk block akses ke wp-admin berdasarkan kode negara.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-block-wp-login.php';

        /**
         * Berisi Class untuk auto update plugin
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-auto-updater.php';

        /**
         * Berisi Class untuk Classic Widget
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-classic-widget.php';

        /**
         * Berisi Class untuk standar Editor
         */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-standar-editor.php';

        /**
         * Berisi Class untuk hapus slug category
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-remove-slug-category.php';

        /**
         * Berisi Class untuk handle captcha
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-captcha.php';

        /**
         * Berisi Class untuk visitor statistics
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-visitor-stats.php';

        /**
         * Berisi Class untuk SEO functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-seo.php';

        /**
         * Berisi Class untuk White Label functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweetaddons-whitelabel.php';

        /**
         * Berisi Class untuk WhatsApp functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-whatsapp.php';

        /**
         * Berisi Class untuk Breadcrumb functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-breadcrumb.php';

        /**
         * Berisi Class untuk Login Customizer functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-login-customizer.php';

        /**
         * Berisi Class untuk Database Cleaner functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sweetaddons-database-cleaner.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweetaddons-admin.php';


        /**
         * Class untuk menambah option page untuk Admin Option
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweet-option-page.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweet-option-umum.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweet-option-maintenance.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sweet-option-block.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-sweetaddons-public.php';

        $this->loader = new Sweetaddons_Loader();
    }

    /**
     * Load REST API controllers and other new architecture components
     */
    private function load_rest_controllers()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'src/Api/SEO_Controller.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'src/Api/Spam_Controller.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'src/Api/Umum_Controller.php';
    }
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Sweetaddons_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Sweetaddons_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Sweetaddons_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Sweetaddons_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Initialize visitor statistics
        new Sweetaddons_Visitor_Stats();

        // Initialize SEO functionality
        new Sweetaddons_SEO();
        // Initialize REST API controllers
        $this->load_rest_controllers();
        new Sweetaddons_SEO_Controller();
        new Sweetaddons_Spam_Controller();
        new Sweetaddons_Umum_Controller();

        // Initialize White Label functionality (only in admin)
        if (is_admin()) {
            new Sweetaddons_WhiteLabel();
        }

        // Initialize WhatsApp functionality
        new Sweetaddons_WhatsApp();

        // Initialize Breadcrumb functionality
        new Sweetaddons_Breadcrumb();

        // Initialize Login Customizer
        new Sweetaddons_Login_Customizer();

        // Initialize Database Cleaner
        new Sweetaddons_Database_Cleaner();
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Sweetaddons_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
