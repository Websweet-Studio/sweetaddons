<?php

namespace Sweetaddons;


/**
 * Fired during plugin deactivation
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// Clear scheduled cron jobs
		$timestamp = wp_next_scheduled('sweetaddons_daily_aggregation');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'sweetaddons_daily_aggregation');
		}
	}
}
