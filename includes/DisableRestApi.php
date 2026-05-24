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

class DisableRestApi
{
    public function __construct()
    {
        // Only run if functions are available and option is enabled
        if (function_exists('get_option') && get_option('disable_rest_api')) {
            add_filter('rest_authentication_errors', array($this, 'disable_rest_api'), 99);
            add_filter('rest_enabled', '__return_false');
            add_filter('rest_jsonp_enabled', '__return_false');
            // add_action('init', array($this, 'block_rest_api'), 1);
        }
    }

    public function disable_rest_api($access)
    {
        global $wp;

        // Check if this is a widget API request - allow widget endpoints
        if (isset($wp->query_vars['rest_route'])) {
            $rest_route = $wp->query_vars['rest_route'];
            if (strpos($rest_route, '/wp/v2/widget-types/') !== false) {
                return $access; // Allow widget API requests
            }
        }

        // Check request URI for widget endpoints (backup method)
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($request_uri, '/wp/v2/widget-types/') !== false) {
            return $access; // Allow widget API requests
        }

        // For all other REST API requests, disable
        return new WP_Error('rest_disabled', __('The REST API is disabled on this site.'), array('status' => rest_authorization_required_code()));
    }

    public function block_rest_api()
    {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/%category%/');
    }
}

if (function_exists('get_option') && get_option('disable_rest_api')) {
}