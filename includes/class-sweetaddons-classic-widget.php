<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */

class Sweetaddons_Classic_Widget
{
    public function __construct()
    {
        // Only run in admin context and when functions are available
        if (is_admin() && function_exists('get_option') && get_option('classic_widget_Sweetaddons')) {
            // Disables the block editor from managing widgets in the Gutenberg plugin.
            if (function_exists('add_filter')) {
                add_filter('gutenberg_use_widgets_block_editor', '__return_false');
                // Disables the block editor from managing widgets.
                add_filter('use_widgets_block_editor', '__return_false');
            }
        }
    }

    public static function init()
    {
        // Initialize the Sweetaddons_Classic_Widget class safely
        if (is_admin()) {
            return new self();
        }
        return null;
    }
}
// Initialize the Sweetaddons_Classic_Widget class safely
if (is_admin()) {
    $Sweetaddons_classic_widget = new Sweetaddons_Classic_Widget();
}
