<?php

/**
 * White Label functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_WhiteLabel
{
    public function __construct()
    {
        add_filter('all_plugins', array($this, 'modify_plugin_info'));
        add_filter('plugin_row_meta', array($this, 'modify_plugin_row_meta'), 10, 4);
        add_action('admin_head', array($this, 'custom_admin_styles'));
        add_filter('admin_footer_text', array($this, 'custom_admin_footer'));
        add_action('admin_menu', array($this, 'update_menu_title'), 999);
    }

    public function modify_plugin_info($plugins)
    {
        $plugin_file = 'sweetaddons/sweetaddons.php';

        if (!isset($plugins[$plugin_file])) {
            return $plugins;
        }

        // Get white label settings
        $plugin_name = get_option('sweetaddons_whitelabel_plugin_name');
        $plugin_uri = get_option('sweetaddons_whitelabel_plugin_uri');
        $description = get_option('sweetaddons_whitelabel_description');
        $author = get_option('sweetaddons_whitelabel_author');
        $author_uri = get_option('sweetaddons_whitelabel_author_uri');

        // Apply white label settings if they exist
        if ($plugin_name) {
            $plugins[$plugin_file]['Name'] = $plugin_name;
            $plugins[$plugin_file]['Title'] = $plugin_name;
        }

        if ($plugin_uri) {
            $plugins[$plugin_file]['PluginURI'] = $plugin_uri;
        }

        if ($description) {
            $plugins[$plugin_file]['Description'] = $description;
        }

        if ($author) {
            $plugins[$plugin_file]['Author'] = $author;
            $plugins[$plugin_file]['AuthorName'] = $author;
        }

        if ($author_uri) {
            $plugins[$plugin_file]['AuthorURI'] = $author_uri;
        }

        return $plugins;
    }

    public function modify_plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
    {
        if ($plugin_file !== 'sweetaddons/sweetaddons.php') {
            return $plugin_meta;
        }

        $hide_original = get_option('sweetaddons_whitelabel_hide_original');

        if ($hide_original) {
            // Remove original author links and references
            $plugin_meta = array_filter($plugin_meta, function ($meta) {
                return !strpos($meta, 'websweetstudio.com') &&
                    !strpos($meta, 'WebsweetStudio');
            });
        }

        return $plugin_meta;
    }

    public function custom_admin_styles()
    {
        $hide_original = get_option('sweetaddons_whitelabel_hide_original');

        if ($hide_original) {
?>
            <style>
                /* Hide WebsweetStudio references in admin */
                .sweetaddons-branding,
                .websweetstudio-link,
                [href*="websweetstudio.com"] {
                    display: none !important;
                }

                /* Custom branding styles */
                .sweetaddons-whitelabel .custom-branding {
                    font-weight: bold;
                    color: #0073aa;
                }
            </style>
<?php
        }
    }

    public function custom_admin_footer($text)
    {
        $hide_original = get_option('sweetaddons_whitelabel_hide_original');

        // Check if we're in admin and current_screen function exists
        if (is_admin() && function_exists('get_current_screen')) {
            $current_screen = get_current_screen();

            // Only modify footer on Sweet Addons pages
            if ($current_screen && strpos($current_screen->id, 'sweetaddons') !== false) {
                if ($hide_original) {
                    $custom_author = get_option('sweetaddons_whitelabel_author', 'Your Company');
                    $custom_uri = get_option('sweetaddons_whitelabel_author_uri', '#');

                    return sprintf(
                        'Thank you for using <strong>%s</strong>. Created by <a href="%s" target="_blank">%s</a>.',
                        get_option('sweetaddons_whitelabel_plugin_name', 'Sweet Addons'),
                        esc_url($custom_uri),
                        esc_html($custom_author)
                    );
                }
            }
        }

        return $text;
    }

    public function update_menu_title()
    {
        global $menu, $submenu;

        $custom_menu_title = get_option('sweetaddons_whitelabel_menu_title');
        if (!$custom_menu_title) {
            $custom_menu_title = get_option('sweetaddons_whitelabel_plugin_name');
        }
        $plugin_name = get_option('sweetaddons_whitelabel_plugin_name');
        if ($custom_menu_title === 'Sweet Addons' && $plugin_name && $plugin_name !== 'Sweet Addons') {
            $custom_menu_title = $plugin_name;
        }

        if ($custom_menu_title) {
            // Update main menu title
            foreach ($menu as $key => $menu_item) {
                if ($menu_item[2] === 'custom_admin_options') {
                    $menu[$key][0] = $custom_menu_title;
                    break;
                }
            }

            // Update submenu titles if needed
            if (isset($submenu['custom_admin_options'])) {
                foreach ($submenu['custom_admin_options'] as $key => $submenu_item) {
                    if ($submenu_item[2] === 'custom_admin_options') {
                        $submenu['custom_admin_options'][$key][0] = $custom_menu_title;
                        break;
                    }
                }
            }
        }
    }

    public static function get_white_labeled_info($key = null)
    {
        $plugin_name = get_option('sweetaddons_whitelabel_plugin_name', 'Sweet Addons');
        $menu_title = get_option('sweetaddons_whitelabel_menu_title', $plugin_name);
        if ($menu_title === 'Sweet Addons' && $plugin_name !== 'Sweet Addons') {
            $menu_title = $plugin_name;
        }
        $white_label_data = array(
            'plugin_name' => $plugin_name,
            'plugin_uri' => get_option('sweetaddons_whitelabel_plugin_uri', 'https://websweetstudio.com'),
            'description' => get_option('sweetaddons_whitelabel_description', 'Addon plugin for WebsweetStudio Client'),
            'author' => get_option('sweetaddons_whitelabel_author', 'WebsweetStudio'),
            'author_uri' => get_option('sweetaddons_whitelabel_author_uri', 'https://websweetstudio.com'),
            'menu_title' => $menu_title,
            'hide_original' => get_option('sweetaddons_whitelabel_hide_original', '')
        );

        if ($key && isset($white_label_data[$key])) {
            return $white_label_data[$key];
        }

        return $white_label_data;
    }

    public static function is_white_labeled()
    {
        return (bool) get_option('sweetaddons_whitelabel_plugin_name');
    }

    public static function get_branding_html()
    {
        $hide_original = get_option('sweetaddons_whitelabel_hide_original');

        if ($hide_original) {
            $plugin_name = self::get_white_labeled_info('plugin_name');
            $author = self::get_white_labeled_info('author');
            $author_uri = self::get_white_labeled_info('author_uri');

            return sprintf(
                '<div class="sweetaddons-whitelabel"><span class="custom-branding">%s</span> by <a href="%s" target="_blank">%s</a></div>',
                esc_html($plugin_name),
                esc_url($author_uri),
                esc_html($author)
            );
        }

        return '<div class="sweetaddons-branding">Sweet Addons by <a href="https://websweetstudio.com" target="_blank">WebsweetStudio</a></div>';
    }
}
