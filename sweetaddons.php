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
 * @since             3.1.0
 * @package           sweetaddons
 *
 * @wordpress-plugin
 * Plugin Name:       Sweet Addons
 * Plugin URI:        https://websweetstudio.com
 * Description:       Addon plugin for WebsweetStudio Client
 * Version:           3.1.0
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
 */
define('SWEETADDONS_VERSION', '3.1.0');
define('PLUGIN_DIR', plugin_dir_path(__DIR__));
define('PLUGIN_FILE', plugin_basename(__FILE__));
define('PLUGIN_BASE_NAME', plugin_basename(__DIR__));
define('SWEETADDONS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

// Load PSR-4 autoloader
require plugin_dir_path(__FILE__) . 'includes/autoloader.php';

/**
 * The code that runs during plugin activation.
 */
function activate_Sweetaddons()
{
    \Sweetaddons\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_Sweetaddons()
{
    \Sweetaddons\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_Sweetaddons');
register_deactivation_hook(__FILE__, 'deactivate_Sweetaddons');

/**
 * Begins execution of the plugin.
 */
function run_sweetaddons()
{
    $plugin = new \Sweetaddons\Core();
    $plugin->run();
}
run_sweetaddons();
