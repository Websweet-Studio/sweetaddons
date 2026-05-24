<?php
namespace Sweetaddons;

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

class DisableGutenberg
{
    public function __construct()
    {
        if (get_option('disable_gutenberg')) {
            add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg'), 10, 2);
        }
    }

    public function disable_gutenberg($use_block_editor, $post_type)
    {
        // if ($post_type === 'post') {
        return false;
        // }
        return $use_block_editor;
    }
}

