<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sweetaddons_Snipet
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'output_header'), 999);
        add_action('wp_body_open', array($this, 'output_body'), 5);
        add_action('wp_footer', array($this, 'output_footer'), 999);
        add_action('wp_head', array($this, 'output_css'), 999);
        add_action('wp_footer', array($this, 'output_js'), 999);
        add_action('init', array($this, 'run_php'), 1);
    }

    public function output_header()
    {
        $this->echo_raw_option('sweetaddons_snipet_header');
    }

    public function output_body()
    {
        $this->echo_raw_option('sweetaddons_snipet_body');
    }

    public function output_footer()
    {
        $this->echo_raw_option('sweetaddons_snipet_footer');
    }

    public function output_css()
    {
        $css = trim((string) get_option('sweetaddons_snipet_css', ''));
        if ($css === '') {
            return;
        }

        echo "\n<style id=\"sweetaddons-snipet-css\">\n" . $css . "\n</style>\n";
    }

    public function output_js()
    {
        $js = trim((string) get_option('sweetaddons_snipet_js', ''));
        if ($js === '') {
            return;
        }

        echo "\n<script id=\"sweetaddons-snipet-js\">\n" . $js . "\n</script>\n";
    }

    public function run_php()
    {
        static $executed = false;

        if ($executed || is_admin()) {
            return;
        }

        $code = trim((string) get_option('sweetaddons_snipet_php', ''));
        if ($code === '') {
            return;
        }

        $executed = true;

        try {
            eval($code);
        } catch (Throwable $e) {
            error_log('Sweetaddons Script PHP error: ' . $e->getMessage());
        }
    }

    private function echo_raw_option($option_name)
    {
        $content = trim((string) get_option($option_name, ''));
        if ($content === '') {
            return;
        }

        echo "\n" . $content . "\n";
    }
}
