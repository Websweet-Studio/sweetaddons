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
 * @since             3.0.22
 * @package           sweetaddons
 *
 * @wordpress-plugin
 * Plugin Name:       Sweet Addons
 * Plugin URI:        https://websweetstudio.com
 * Description:       Addon plugin for WebsweetStudio Client
 * Version:           3.0.22
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
define('SWEETADDONS_VERSION', '3.0.22');
define('PLUGIN_DIR', plugin_dir_path(__DIR__));
define('PLUGIN_FILE', plugin_basename(__FILE__));
define('PLUGIN_BASE_NAME', plugin_basename(__DIR__));
define('SWEETADDONS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

function sweetaddons_build_classmap($persist = true)
{
    $root = plugin_dir_path(__FILE__);
    $dirs = array('includes', 'admin', 'public');
    $map = array();

    foreach ($dirs as $dir) {
        $path = $root . $dir;
        if (!is_dir($path)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileinfo) {
            if (!$fileinfo instanceof SplFileInfo) {
                continue;
            }

            if (!$fileinfo->isFile()) {
                continue;
            }

            if (strtolower($fileinfo->getExtension()) !== 'php') {
                continue;
            }

            $file_path = $fileinfo->getPathname();
            $content = file_get_contents($file_path);
            if ($content === false) {
                continue;
            }

            $tokens = token_get_all($content);
            $count = count($tokens);
            for ($i = 0; $i < $count; $i++) {
                $token = $tokens[$i];
                if (!is_array($token) || $token[0] !== T_CLASS) {
                    continue;
                }

                $j = $i + 1;
                while ($j < $count) {
                    $next = $tokens[$j];
                    if (is_array($next) && $next[0] === T_STRING) {
                        $class_name = $next[1];
                        if (!isset($map[$class_name])) {
                            $map[$class_name] = ltrim(str_replace($root, '', $file_path), '\\/');
                        }
                        break;
                    }
                    if (is_array($next) && $next[0] === T_WHITESPACE) {
                        $j++;
                        continue;
                    }
                    break;
                }
            }
        }
    }

    if ($persist && function_exists('update_site_option')) {
        update_site_option('sweetaddons_autoload_classmap', $map);
        update_site_option('sweetaddons_autoload_classmap_version', defined('SWEETADDONS_VERSION') ? SWEETADDONS_VERSION : '');
    }

    return $map;
}

spl_autoload_register(function ($class) {
    static $classmap_loaded = false;
    static $classmap = array();

    if (!$classmap_loaded) {
        $stored_version = function_exists('get_site_option') ? get_site_option('sweetaddons_autoload_classmap_version', '') : '';
        $stored_map = function_exists('get_site_option') ? get_site_option('sweetaddons_autoload_classmap', array()) : array();

        if (is_array($stored_map) && $stored_version === (defined('SWEETADDONS_VERSION') ? SWEETADDONS_VERSION : '')) {
            $classmap = $stored_map;
        } else {
            $classmap = sweetaddons_build_classmap(true);
        }

        $classmap_loaded = true;
    }

    $fallback = array(
        'Sweetaddons_Activator' => 'includes/class-sweetaddons-activator.php',
        'Sweetaddons_Deactivator' => 'includes/class-sweetaddons-deactivator.php',
        'sweetaddons' => 'includes/class-sweetaddons.php',
        'Sweetaddons_Redis_Cache' => 'includes/class-sweetaddons-redis-cache.php',
        'Sweetaddons_Head_Cleanup' => 'includes/class-sweetaddons-head-cleanup.php',
        'Sweet_Option_Optimasi' => 'admin/class-sweet-option-optimasi.php',
    );

    $rel = isset($classmap[$class]) ? $classmap[$class] : (isset($fallback[$class]) ? $fallback[$class] : '');
    if ($rel === '') {
        return;
    }

    $file = plugin_dir_path(__FILE__) . $rel;
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
