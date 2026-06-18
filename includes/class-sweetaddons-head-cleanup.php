<?php

/**
 * Head Cleanup functionality for Sweet Addons.
 *
 * Hapus bloat dari <head> WordPress: emoji scripts, RSD, WLW, shortlink,
 * REST link, oEmbed, generator meta, dan self-pingback.
 * Melengkapi WP Rocket yang tidak menangani bagian ini.
 *
 * @link       https://websweetstudio.com
 * @since      3.0.22
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sweetaddons_Head_Cleanup
{
    const OPTION_KEY = 'sweetaddons_head_cleanup';

    public function __construct()
    {
        // Hooks hanya di frontend (bukan admin)
        if (is_admin()) {
            return;
        }

        add_action('init', array($this, 'apply_cleanup'));
    }

    /**
     * Ambil config tersimpan.
     */
    public static function get_config()
    {
        $defaults = array(
            'remove_emoji'       => 1,
            'remove_rsd'         => 1,
            'remove_wlw'         => 1,
            'remove_shortlink'   => 1,
            'remove_rest_link'   => 1,
            'remove_oembed'      => 0,
            'remove_generator'   => 1,
            'disable_pingback'   => 1,
        );

        $config = get_option(self::OPTION_KEY, array());
        if (!is_array($config)) {
            $config = array();
        }

        return array_merge($defaults, $config);
    }

    /**
     * Ambil satu key config.
     */
    private function opt($key)
    {
        $config = self::get_config();
        return isset($config[$key]) ? (int) $config[$key] : 0;
    }

    public function apply_cleanup()
    {
        if ($this->opt('disable_pingback')) {
            add_filter('xmlrpc_methods', array($this, 'remove_pingback_method'));
            add_action('pre_ping', array($this, 'disable_self_pingback'));
        }

        if ($this->opt('remove_emoji')) {
            $this->disable_emojis();
        }

        if ($this->opt('remove_rsd')) {
            remove_action('wp_head', 'rsd_link');
        }

        if ($this->opt('remove_wlw')) {
            remove_action('wp_head', 'wlwmanifest_link');
        }

        if ($this->opt('remove_shortlink')) {
            remove_action('wp_head', 'wp_shortlink_wp_head');
            remove_action('template_redirect', 'wp_shortlink_header', 11);
        }

        if ($this->opt('remove_rest_link')) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }

        if ($this->opt('remove_oembed')) {
            $this->disable_oembed();
        }

        if ($this->opt('remove_generator')) {
            remove_action('wp_head', 'wp_generator');
        }
    }

    /* ------------------------------------------------------------------ *
     *  Emoji
     * ------------------------------------------------------------------ */

    private function disable_emojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        add_filter('emoji_svg_url', '__return_false');
        add_filter('tiny_mce_plugins', array($this, 'remove_tinymce_emoji'));
    }

    public function remove_tinymce_emoji($plugins)
    {
        if (!is_array($plugins)) {
            return array();
        }
        return array_diff($plugins, array('wpemoji'));
    }

    /* ------------------------------------------------------------------ *
     *  oEmbed
     * ------------------------------------------------------------------ */

    private function disable_oembed()
    {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        add_filter('embed_oembed_discover', '__return_false');
        add_filter('rewrite_rules_array', array($this, 'remove_oembed_rewrite_rules'));
    }

    public function remove_oembed_rewrite_rules($rules)
    {
        if (!is_array($rules)) {
            return array();
        }
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }

    /* ------------------------------------------------------------------ *
     *  Pingback
     * ------------------------------------------------------------------ */

    public function remove_pingback_method($methods)
    {
        unset($methods['pingback.ping']);
        return $methods;
    }

    public function disable_self_pingback(&$links)
    {
        $home = get_option('home');
        foreach ($links as $l => $link) {
            if (0 === strpos($link, $home)) {
                unset($links[$l]);
            }
        }
    }
}
