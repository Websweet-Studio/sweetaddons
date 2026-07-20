<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

wp_clear_scheduled_hook('sweetaddons_daily_aggregation');

$options = array(
    'fully_disable_comment',
    'hide_admin_notice',
    'limit_login_attempts',
    'maintenance_mode',
    'maintenance_mode_data',
    'license_key',
    'auto_resize_mode',
    'auto_resize_mode_data',
    'disable_xmlrpc',
    'disable_rest_api',
    'disable_gutenberg',
    'block_wp_login',
    'whitelist_block_wp_login',
    'redirect_to',
    'classic_widget_Sweetaddons',
    'remove_slug_category_Sweetaddons',
    'auto_resize_image_Sweetaddons',
    'captcha_Sweetaddons',
    'news_generate',
    'sweetaddons_seo_home_title',
    'sweetaddons_seo_home_description',
    'sweetaddons_seo_default_og_image',
    'sweetaddons_seo_twitter_site',
    'sweetaddons_whitelabel_plugin_name',
    'sweetaddons_whitelabel_plugin_uri',
    'sweetaddons_whitelabel_description',
    'sweetaddons_whitelabel_author',
    'sweetaddons_whitelabel_author_uri',
    'sweetaddons_whitelabel_menu_title',
    'sweetaddons_whitelabel_hide_original',
    'sweetaddons_whatsapp_enable',
    'sweetaddons_whatsapp_phone',
    'sweetaddons_whatsapp_message',
    'sweetaddons_whatsapp_button_text',
    'sweetaddons_whatsapp_position',
    'sweetaddons_whatsapp_color',
    'sweetaddons_whatsapp_split_text_bg',
    'sweetaddons_whatsapp_split_text_color',
    'sweetaddons_whatsapp_show_mobile',
    'sweetaddons_whatsapp_show_desktop',
    'sweetaddons_whatsapp_animation',
    'sweetaddons_whatsapp_bubble_style',
    'sweetaddons_whatsapp_show_tooltip',
    'sweetaddons_whatsapp_show_text_mobile',
    'sweetaddons_whatsapp_agents',
    'sweetaddons_login_customizer',
    'sweetaddons_redis_config',
    'sweetaddons_head_cleanup',
);

foreach ($options as $option_name) {
    delete_option($option_name);
    delete_site_option($option_name);
}

delete_site_option('sweetaddons_autoload_classmap');
delete_site_option('sweetaddons_autoload_classmap_version');

$tables = array(
    $wpdb->prefix . 'sweetaddons_visitor_logs',
    $wpdb->prefix . 'sweetaddons_daily_stats',
    $wpdb->prefix . 'sweetaddons_monthly_stats',
    $wpdb->prefix . 'sweetaddons_page_stats',
    $wpdb->prefix . 'sweetaddons_referrer_stats',
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
}
