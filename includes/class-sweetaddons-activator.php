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

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class Sweetaddons_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Table untuk raw visitor data (tetap untuk detail tracking)
		$visitor_logs_table = $wpdb->prefix . 'sweetaddons_visitor_logs';
		$sql1 = "CREATE TABLE $visitor_logs_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			visitor_ip varchar(45) NOT NULL,
			user_agent text,
			page_url varchar(255) NOT NULL,
			referer varchar(255),
			visit_date date NOT NULL,
			visit_time time NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY visit_date (visit_date),
			KEY visitor_ip (visitor_ip),
			KEY page_url (page_url)
		) $charset_collate;";

		// Table untuk daily aggregation (untuk performa)
		$daily_stats_table = $wpdb->prefix . 'sweetaddons_daily_stats';
		$sql2 = "CREATE TABLE $daily_stats_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			stat_date date NOT NULL,
			unique_visitors int(11) DEFAULT 0,
			total_pageviews int(11) DEFAULT 0,
			bounce_rate decimal(5,2) DEFAULT 0.00,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY stat_date (stat_date)
		) $charset_collate;";

		// Table untuk monthly aggregation
		$monthly_stats_table = $wpdb->prefix . 'sweetaddons_monthly_stats';
		$sql3 = "CREATE TABLE $monthly_stats_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			stat_year int(4) NOT NULL,
			stat_month int(2) NOT NULL,
			unique_visitors int(11) DEFAULT 0,
			total_pageviews int(11) DEFAULT 0,
			avg_bounce_rate decimal(5,2) DEFAULT 0.00,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY stat_year_month (stat_year, stat_month)
		) $charset_collate;";

		// Table untuk page statistics
		$page_stats_table = $wpdb->prefix . 'sweetaddons_page_stats';
		$sql4 = "CREATE TABLE $page_stats_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			page_url varchar(255) NOT NULL,
			stat_date date NOT NULL,
			unique_visitors int(11) DEFAULT 0,
			total_views int(11) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY page_date (page_url, stat_date),
			KEY page_url (page_url),
			KEY stat_date (stat_date)
		) $charset_collate;";

		// Table untuk referrer statistics
		$referrer_stats_table = $wpdb->prefix . 'sweetaddons_referrer_stats';
		$sql5 = "CREATE TABLE $referrer_stats_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			referrer_domain varchar(255) NOT NULL,
			stat_date date NOT NULL,
			total_visits int(11) DEFAULT 0,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY referrer_date (referrer_domain, stat_date),
			KEY referrer_domain (referrer_domain),
			KEY stat_date (stat_date)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql1);
		dbDelta($sql2);
		dbDelta($sql3);
		dbDelta($sql4);
		dbDelta($sql5);

		// Schedule daily aggregation cron job
		if (!wp_next_scheduled('sweetaddons_daily_aggregation')) {
			wp_schedule_event(time(), 'daily', 'sweetaddons_daily_aggregation');
		}

		// ================================================================
		// Auto-save semua default option saat aktivasi (tidak overwrite)
		// ================================================================

		// Umum
		add_option('fully_disable_comment', 1);
		add_option('hide_admin_notice', 0);
		add_option('disable_gutenberg', 0);
		add_option('classic_widget_Sweetaddons', 1);
		add_option('remove_slug_category_Sweetaddons', 0);

		// Proteksi / Security
		add_option('limit_login_attempts', 1);
		add_option('disable_xmlrpc', 1);
		add_option('disable_rest_api', 0);
		add_option('captcha_Sweetaddons', 1);

		// Maintenance
		add_option('maintenance_mode', 0);
		add_option('maintenance_mode_data', array(
			'title' => 'Segera Kembali',
			'body'  => 'Kami sedang melakukan perawatan sistem. Silakan kembali lagi nanti.',
		));

		// Block wp-login
		add_option('block_wp_login', 0);
		add_option('whitelist_block_wp_login', '');
		add_option('redirect_to', 'http://127.0.0.1');

		// Auto resize image
		add_option('auto_resize_mode', 0);
		add_option('auto_resize_mode_data', array());

		// License / News
		add_option('license_key', '');
		add_option('news_generate', 0);

		// SEO
		add_option('sweetaddons_seo_enable', 1);
		add_option('sweetaddons_seo_home_title', '');
		add_option('sweetaddons_seo_home_description', '');
		add_option('sweetaddons_seo_default_og_image', '');
		add_option('sweetaddons_seo_twitter_site', '');

		// White Label
		add_option('sweetaddons_whitelabel_plugin_name', '');
		add_option('sweetaddons_whitelabel_plugin_uri', '');
		add_option('sweetaddons_whitelabel_description', '');
		add_option('sweetaddons_whitelabel_author', '');
		add_option('sweetaddons_whitelabel_author_uri', '');
		add_option('sweetaddons_whitelabel_menu_title', '');
		add_option('sweetaddons_whitelabel_hide_original', 0);

		// WhatsApp
		add_option('sweetaddons_whatsapp_enable', '');
		add_option('sweetaddons_whatsapp_phone', '');
		add_option('sweetaddons_whatsapp_message', 'Halo! Saya butuh bantuan.');
		add_option('sweetaddons_whatsapp_button_text', 'Chat dengan kami');
		add_option('sweetaddons_whatsapp_position', 'bottom-right');
		add_option('sweetaddons_whatsapp_color', '#25D366');
		add_option('sweetaddons_whatsapp_show_mobile', '1');
		add_option('sweetaddons_whatsapp_show_desktop', '1');
		add_option('sweetaddons_whatsapp_animation', 'none');
		add_option('sweetaddons_whatsapp_bubble_style', 'circle');
		add_option('sweetaddons_whatsapp_show_tooltip', '1');
		add_option('sweetaddons_whatsapp_show_text_mobile', '');
		add_option('sweetaddons_whatsapp_agents', array());

		// Login Customizer
		add_option('sweetaddons_login_customizer', array(
			'logo_url'       => '',
			'bg_color'       => '#f1f1f1',
			'bg_image'       => '',
			'btn_color'      => '#2271b1',
			'btn_text_color' => '#ffffff',
		));

		// Optimasi — Redis
		add_option('sweetaddons_redis_config', array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'password' => '',
			'database' => 0,
		));

		// Optimasi — Head Cleanup
		add_option('sweetaddons_head_cleanup', array(
			'remove_emoji'     => 1,
			'remove_rsd'       => 1,
			'remove_wlw'       => 1,
			'remove_shortlink' => 1,
			'remove_rest_link' => 1,
			'remove_oembed'    => 1,
			'remove_generator' => 1,
			'disable_pingback' => 1,
		));
	}
}
