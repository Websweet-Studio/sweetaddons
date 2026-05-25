<?php

/**
 * Fired during plugin activation
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_Block_Wp_Login
{
    public function __construct()
    {
        if (get_option('block_wp_login')) {
            add_action('init', array($this, 'block_wp_login'));
        }
    }

    public function block_wp_login()
    {
        if ('wp-login.php' === $GLOBALS['pagenow']) {
            $ip                     = $_SERVER['REMOTE_ADDR'];
            $ip_white               = false;
            $whitelist_ip           = get_option('whitelist_block_wp_login', '');

            if ($whitelist_ip) {
                $whitelist_ip = array_map('trim', explode(',', $whitelist_ip));
                if (in_array($ip, $whitelist_ip)) {
                    $ip_white = true;
                }
            }

            if (!$ip_white) {
                $redirect_to = get_option('redirect_to');
                if (empty($redirect_to)) {
                    $redirect_to = home_url('/');
                }
                wp_redirect($redirect_to);
                exit;
            }
        }
    }
}
