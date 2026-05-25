<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */

class Sweetaddons_Hide_Admin_Notice
{
    public function __construct()
    {
        if (!is_admin()) {
            return;
        }

        add_action('admin_notices', array($this, 'hide_admin_notice'));
    }

    private function is_sweetaddons_dashboard()
    {
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        return $page === 'custom_admin_options' || strpos($page, 'Sweetaddons_') === 0;
    }

    public function hide_admin_notice()
    {
        $hide_admin_notice_value = get_option('hide_admin_notice');

        if ($hide_admin_notice_value || $this->is_sweetaddons_dashboard()) {
            global $wp_filter;
            remove_all_actions('admin_notices');
            echo '<style>.notice, .update-nag, div.updated, div.error { display: none !important; }</style>';
        }
    }
}

// Initialize the Sweetaddons_Hide_Admin_Notice class
$hide_admin_notice = new Sweetaddons_Hide_Admin_Notice();
