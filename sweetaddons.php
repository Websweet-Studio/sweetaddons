<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://websweetstudio.com
 * @since             3.0.1
 * @package           sweetaddons
 *
 * @wordpress-plugin
 * Plugin Name:       Sweet Addons
 * Plugin URI:        https://websweetstudio.com
 * Description:       Addon plugin for WebsweetStudio Client
 * Version:           3.0.1
 * Author:            WebsweetStudio
 * Author URI:        https://websweetstudio.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sweetaddons
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SWEETADDONS_VERSION', '3.0.1');
define('PLUGIN_DIR', plugin_dir_path(__DIR__));
define('PLUGIN_FILE', plugin_basename(__FILE__));
define('PLUGIN_BASE_NAME', plugin_basename(__DIR__));
define('SWEETADDONS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

spl_autoload_register(function ($class) {
    static $map = array(
        'Sweetaddons_Activator' => 'includes/class-sweetaddons-activator.php',
        'Sweetaddons_Deactivator' => 'includes/class-sweetaddons-deactivator.php',
        'Sweetaddons_Loader' => 'includes/class-sweetaddons-loader.php',
        'Sweetaddons_i18n' => 'includes/class-sweetaddons-i18n.php',
        'Sweetaddons_Admin' => 'admin/class-sweetaddons-admin.php',
        'Sweetaddons_Admin_Layout' => 'admin/class-sweetaddons-admin-layout.php',
        'Custom_Admin_Option_Page' => 'admin/class-sweet-option-page.php',
        'Sweet_Option_Umum' => 'admin/class-sweet-option-umum.php',
        'Sweet_Option_Maintenance' => 'admin/class-sweet-option-maintenance.php',
        'Sweet_Option_Block' => 'admin/class-sweet-option-block.php',
        'Sweetaddons_Public' => 'public/class-sweetaddons-public.php',
        'Sweetaddons_Auto_Updater' => 'includes/class-sweetaddons-auto-updater.php',
        'Sweetaddons_Fully_Disable_Comment' => 'includes/class-sweetaddons-disable-comments.php',
        'Sweetaddons_Hide_Admin_Notice' => 'includes/class-sweetaddons-hide-admin-notice.php',
        'Sweetaddons_Limit_Login_Attempts' => 'includes/class-sweetaddons-limit-login-attempts.php',
        'Sweetaddons_Maintenance_Mode' => 'includes/class-sweetaddons-maintenance-mode.php',
        'Sweetaddons_Disable_Xmlrpc' => 'includes/class-sweetaddons-disable-xmlrpc.php',
        'Sweetaddons_Disable_Rest_Api' => 'includes/class-sweetaddons-disable-rest-api.php',
        'Sweetaddons_Disable_Gutenberg' => 'includes/class-sweetaddons-disable-gutenberg.php',
        'Sweetaddons_Block_Wp_Login' => 'includes/class-sweetaddons-block-wp-login.php',
        'Sweetaddons_Classic_Widget' => 'includes/class-sweetaddons-classic-widget.php',
        'Sweetaddons_Remove_Slug_Category' => 'includes/class-sweetaddons-remove-slug-category.php',
        'Sweetaddons_Captcha' => 'includes/class-sweetaddons-captcha.php',
        'Sweetaddons_Visitor_Stats' => 'includes/class-sweetaddons-visitor-stats.php',
        'Sweetaddons_SEO' => 'includes/class-sweetaddons-seo.php',
        'Sweetaddons_WhiteLabel' => 'includes/class-sweetaddons-whitelabel.php',
        'Sweetaddons_WhatsApp' => 'includes/class-sweetaddons-whatsapp.php',
        'Sweetaddons_Breadcrumb' => 'includes/class-sweetaddons-breadcrumb.php',
        'Sweetaddons_Login_Customizer' => 'includes/class-sweetaddons-login-customizer.php',
        'Sweetaddons_Database_Cleaner' => 'includes/class-sweetaddons-database-cleaner.php',
    );

    if (!isset($map[$class])) {
        return;
    }

    $file = plugin_dir_path(__FILE__) . $map[$class];
    if (is_readable($file)) {
        require_once $file;
    }
});

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sweetaddons-activator.php
 */
function activate_Sweetaddons()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sweetaddons-activator.php';
    Sweetaddons_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sweetaddons-deactivator.php
 */
function deactivate_Sweetaddons()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sweetaddons-deactivator.php';
    Sweetaddons_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_Sweetaddons');
register_deactivation_hook(__FILE__, 'deactivate_Sweetaddons');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sweetaddons.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sweetaddons()
{

    $plugin = new sweetaddons();
    $plugin->run();
}
run_sweetaddons();
