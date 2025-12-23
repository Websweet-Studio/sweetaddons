<?php

/**
 * Database Cleaner functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_Database_Cleaner
{
    public function __construct()
    {
        // Constructor left empty for future hooks
    }

    public function get_stats()
    {
        global $wpdb;

        $stats = array();

        // Post revisions
        $stats['revisions'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");

        // Auto drafts
        $stats['auto_drafts'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'");

        // Spam comments
        $stats['spam_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'");

        // Trashed comments
        $stats['trashed_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'");

        // Expired transients
        $time = time();
        $stats['expired_transients'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_transient_timeout%' AND option_value < '$time'");

        return $stats;
    }

    public function clean_items($items)
    {
        global $wpdb;
        $cleaned = array();

        if (in_array('revisions', $items)) {
            $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
            $cleaned[] = 'Post Revisions';
        }

        if (in_array('auto_drafts', $items)) {
            $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            $cleaned[] = 'Auto Drafts';
        }

        if (in_array('spam_comments', $items)) {
            $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
            $cleaned[] = 'Spam Comments';
        }

        if (in_array('trashed_comments', $items)) {
            $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
            $cleaned[] = 'Trashed Comments';
        }

        if (in_array('expired_transients', $items)) {
            $time = time();
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout%' AND option_value < '$time'");
            // Also delete the transient data
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_name NOT LIKE '_transient_timeout%' AND CONCAT('_transient_timeout_', SUBSTRING(option_name, 12)) NOT IN (SELECT option_name FROM $wpdb->options)");
            $cleaned[] = 'Expired Transients';
        }

        return $cleaned;
    }
}
