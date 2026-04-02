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

class Sweetaddons_Standar_Editor
{
    public function __construct()
    {
        if (get_option('standar_editor_Sweetaddons')) {
            add_filter('tiny_mce_before_init', array($this, 'tags_tinymce_fix'), 99);
            add_filter('mce_buttons', array($this, 'mce_buttons'), 5);
            add_filter('mce_buttons_2', array($this, 'mce_buttons_2'), 5);
            add_filter('mce_external_plugins', array($this, 'mce_register_plugin'), 5);
        }
    }

    public function tags_tinymce_fix($init)
    {
        $init["forced_root_block"] = false;
        $init["force_br_newlines"] = true;
        $init["force_p_newlines"] = false;
        $init["convert_newlines_to_brs"] = true;
        $init['menubar'] = true;
        $init['wpautop'] = true;
        return $init;
    }

    public function mce_buttons($buttons)
    {
        $buttons = [
            'formatselect',
            'bold',
            'italic',
            'underline',
            'blockquote',
            'bullist',
            'numlist',
            'alignleft',
            'aligncenter',
            'alignright',
            'alignjustify',
            'link',
            'unlink',
            'undo',
            'redo'
        ];
        return $buttons;
    }

    public function mce_buttons_2($buttons)
    {
        $buttons = [
            'fontselect',
            'fontsizeselect',
            'outdent',
            'indent',
            'pastetext',
            'removeformat',
            'charmap',
            'wp_more',
            'forecolor',
            'table',
            'wp_help'
        ];
        return $buttons;
    }

    function mce_register_plugin($plugin_array)
    {
        $plugin_array['table'] = SWEETADDONS_PLUGIN_DIR_URL . 'assets/admin/js/tinymce-table-plugin.min.js';
        return $plugin_array;
    }
}
// Initialize the Sweetaddons_Standar_Editor class
$Sweetaddons_standar_editor = new Sweetaddons_Standar_Editor();
