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
        if (is_admin() || (defined('DOING_CRON') && DOING_CRON)) {
            new Sweetaddons_Auto_Updater(
                plugin_dir_path(dirname(__FILE__)) . 'sweetaddons.php',
                $this->version
            );
        }
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
        $this->loader = new Sweetaddons_Loader();
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

        if (!is_admin()) {
            return;
        }

        new Sweetaddons_Fully_Disable_Comment();
        new Sweetaddons_Hide_Admin_Notice();
        new Sweetaddons_Disable_Gutenberg();
        new Sweetaddons_Classic_Widget();
        new Custom_Admin_Option_Page();
        new Sweet_Option_Umum();
        new Sweet_Option_Maintenance();
        new Sweet_Option_Block();
        new Sweet_Option_Optimasi();
        new Sweet_Option_Snipet();
        new Sweetaddons_Redis_Cache();
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
        if (get_option('sweetaddons_seo_enable', 1)) {
            new Sweetaddons_SEO();
        }

        if (is_admin()) {
            new Sweetaddons_WhiteLabel();
            return;
        }

        new Sweetaddons_Fully_Disable_Comment();
        new Sweetaddons_Limit_Login_Attempts();
        new Sweetaddons_Maintenance_Mode();
        new Sweetaddons_Disable_Xmlrpc();
        new Sweetaddons_Disable_Rest_Api();
        new Sweetaddons_Disable_Gutenberg();
        new Sweetaddons_Block_Wp_Login();
        new Sweetaddons_Captcha();
        new Sweetaddons_Remove_Slug_Category();

        new Sweetaddons_Visitor_Stats();
        new Sweetaddons_Snipet();

        $enable_whatsapp = get_option('sweetaddons_whatsapp_enable');
        $whatsapp_phone = get_option('sweetaddons_whatsapp_phone');
        $whatsapp_agents = get_option('sweetaddons_whatsapp_agents', array());
        if ($enable_whatsapp && ($whatsapp_phone || (is_array($whatsapp_agents) && !empty($whatsapp_agents)))) {
            new Sweetaddons_WhatsApp();
        }

        new Sweetaddons_Breadcrumb();
        new Sweetaddons_Login_Customizer();
        new Sweetaddons_Head_Cleanup();
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
