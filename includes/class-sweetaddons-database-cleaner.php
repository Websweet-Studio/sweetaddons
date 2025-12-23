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
    }

    public function get_stats()
    {
        global $wpdb;

        $stats = array();

        $stats['revisions'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'revision'");
        $stats['size_revisions'] = (int) $wpdb->get_var("SELECT COALESCE(SUM(OCTET_LENGTH(post_content)+OCTET_LENGTH(post_title)+OCTET_LENGTH(post_excerpt)),0) FROM $wpdb->posts WHERE post_type = 'revision'");

        $stats['auto_drafts'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'auto-draft'");
        $stats['size_auto_drafts'] = (int) $wpdb->get_var("SELECT COALESCE(SUM(OCTET_LENGTH(post_content)+OCTET_LENGTH(post_title)+OCTET_LENGTH(post_excerpt)),0) FROM $wpdb->posts WHERE post_status = 'auto-draft'");

        $stats['spam_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'");
        $stats['size_spam_comments'] = (int) $wpdb->get_var("SELECT COALESCE(SUM(OCTET_LENGTH(comment_content)+OCTET_LENGTH(comment_author)+OCTET_LENGTH(comment_author_email)),0) FROM $wpdb->comments WHERE comment_approved = 'spam'");

        $stats['trashed_comments'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'");
        $stats['size_trashed_comments'] = (int) $wpdb->get_var("SELECT COALESCE(SUM(OCTET_LENGTH(comment_content)+OCTET_LENGTH(comment_author)+OCTET_LENGTH(comment_author_email)),0) FROM $wpdb->comments WHERE comment_approved = 'trash'");

        $time = time();
        $stats['expired_transients'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_transient_timeout%' AND option_value < '$time'");
        $stats['size_expired_transients'] = (int) $wpdb->get_var("
            SELECT COALESCE(SUM(OCTET_LENGTH(o.option_value)),0)
            FROM $wpdb->options o
            WHERE o.option_name LIKE '_transient_%'
              AND o.option_name NOT LIKE '_transient_timeout_%'
              AND CONCAT('_transient_timeout_', SUBSTRING(o.option_name, 12)) IN (
                SELECT option_name FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_%' AND option_value < '$time'
              )
        ");

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
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_name NOT LIKE '_transient_timeout%' AND CONCAT('_transient_timeout_', SUBSTRING(option_name, 12)) NOT IN (SELECT option_name FROM $wpdb->options)");
            $cleaned[] = 'Expired Transients';
        }

        return $cleaned;
    }

    public function format_bytes($bytes)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return number_format($bytes, 2) . ' ' . $units[$pow];
    }
}
