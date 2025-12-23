<?php

/**
 * Login Customizer functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    Sweetaddons
 * @subpackage Sweetaddons/includes
 */

class Sweetaddons_Login_Customizer
{
    public function __construct()
    {
        add_action('login_enqueue_scripts', array($this, 'custom_login_styles'));
        add_filter('login_headerurl', array($this, 'custom_login_header_url'));
        add_filter('login_headertext', array($this, 'custom_login_header_text'));
    }

    public function custom_login_styles()
    {
        $login_settings = get_option('sweetaddons_login_customizer', array());
        $logo_url = isset($login_settings['logo_url']) ? $login_settings['logo_url'] : '';
        $bg_color = isset($login_settings['bg_color']) ? $login_settings['bg_color'] : '#f1f1f1';
        $bg_image = isset($login_settings['bg_image']) ? $login_settings['bg_image'] : '';
        $btn_color = isset($login_settings['btn_color']) ? $login_settings['btn_color'] : '#2271b1';
        $btn_text_color = isset($login_settings['btn_text_color']) ? $login_settings['btn_text_color'] : '#ffffff';

        echo '<style type="text/css">';
        
        echo 'body.login { background-color: ' . esc_attr($bg_color) . ' !important; }';
        
        if ($bg_image) {
            echo 'body.login { background-image: url(' . esc_url($bg_image) . ') !important; background-size: cover; background-position: center; background-repeat: no-repeat; }';
        }

        if ($logo_url) {
            echo '.login h1 a { background-image: url(' . esc_url($logo_url) . ') !important; background-size: contain; width: 100%; height: 80px; }';
        }

        echo '.wp-core-ui .button-primary { background: ' . esc_attr($btn_color) . ' !important; border-color: ' . esc_attr($btn_color) . ' !important; color: ' . esc_attr($btn_text_color) . ' !important; text-shadow: none; box-shadow: none; }';
        echo '.wp-core-ui .button-primary:hover { background: ' . esc_attr($this->adjust_brightness($btn_color, -20)) . ' !important; border-color: ' . esc_attr($this->adjust_brightness($btn_color, -20)) . ' !important; }';
        
        echo '</style>';
    }

    public function custom_login_header_url()
    {
        return home_url();
    }

    public function custom_login_header_text()
    {
        return get_bloginfo('name');
    }

    private function adjust_brightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
        $color_parts = str_split($hex, 2);
        $return = '#';
        foreach ($color_parts as $color) {
            $color = hexdec($color);
            $color = max(0, min(255, $color + $steps));
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
        }
        return $return;
    }
}
