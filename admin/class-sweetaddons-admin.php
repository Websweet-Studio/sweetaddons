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
class Sweetaddons_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	private function is_sweetaddons_admin_page($hook)
	{
		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		$hook = (string) $hook;

		return stripos($hook, 'sweetaddons') !== false
			|| stripos($hook, 'custom_admin_options') !== false
			|| stripos($page, 'sweetaddons') !== false
			|| $page === 'custom_admin_options';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook)
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sweetaddons_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sweetaddons_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if (!$this->is_sweetaddons_admin_page($hook)) {
			return;
		}

		wp_enqueue_style($this->plugin_name, SWEETADDONS_PLUGIN_DIR_URL . 'assets/admin/css/sweetaddons-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook)
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sweetaddons_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sweetaddons_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if (!$this->is_sweetaddons_admin_page($hook)) {
			return;
		}
	}
}
