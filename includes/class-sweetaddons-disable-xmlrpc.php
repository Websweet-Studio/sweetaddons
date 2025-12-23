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
class Sweetaddons_Disable_Xmlrpc
{
    public function __construct()
    {
        if (get_option('disable_xmlrpc')) {
            add_filter('xmlrpc_enabled', '__return_false');
        }
    }
}

// Inisialisasi class Sweetaddons_Disable_Xmlrpc
$sweet_disable_xmlrpc = new Sweetaddons_Disable_Xmlrpc();
