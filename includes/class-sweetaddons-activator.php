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

		// Mengarahkan pengguna ke halaman custom_admin_options saat plugin diaktifkan
		// wp_redirect(admin_url('options-general.php?page=custom_admin_options'));
		// exit;
	}
}
