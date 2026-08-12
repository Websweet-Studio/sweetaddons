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
        $expired_timeout_names = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d",
            '_transient_timeout_%',
            $time
        ));
        $stats['expired_transients'] = is_array($expired_timeout_names) ? count($expired_timeout_names) : 0;
        $stats['size_expired_transients'] = 0;

        if (!empty($expired_timeout_names)) {
            $value_option_names = array();
            foreach ($expired_timeout_names as $timeout_name) {
                $value_option_names[] = str_replace('_transient_timeout_', '_transient_', $timeout_name);
            }

            $placeholders = implode(', ', array_fill(0, count($value_option_names), '%s'));
            $stats['size_expired_transients'] = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COALESCE(SUM(OCTET_LENGTH(option_value)),0) FROM $wpdb->options WHERE option_name IN ($placeholders)",
                $value_option_names
            ));
        }

        return $stats;
    }

    public function clean_items($items)
    {
        global $wpdb;
        $cleaned = array();

        if (in_array('revisions', $items, true)) {
            $deleted = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'revision'");
            $cleaned['revisions'] = max(0, (int) $deleted);
        }

        if (in_array('auto_drafts', $items, true)) {
            $deleted = $wpdb->query("DELETE FROM $wpdb->posts WHERE post_status = 'auto-draft'");
            $cleaned['auto_drafts'] = max(0, (int) $deleted);
        }

        if (in_array('spam_comments', $items, true)) {
            $deleted = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
            $cleaned['spam_comments'] = max(0, (int) $deleted);
        }

        if (in_array('trashed_comments', $items, true)) {
            $deleted = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
            $cleaned['trashed_comments'] = max(0, (int) $deleted);
        }

        if (in_array('expired_transients', $items, true)) {
            $time = time();
            $expired_timeout_names = $wpdb->get_col($wpdb->prepare(
                "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d",
                '_transient_timeout_%',
                $time
            ));

            $deleted_timeouts = 0;
            $deleted_values = 0;

            if (!empty($expired_timeout_names)) {
                $value_option_names = array();
                foreach ($expired_timeout_names as $timeout_name) {
                    $value_option_names[] = str_replace('_transient_timeout_', '_transient_', $timeout_name);
                }

                $timeout_placeholders = implode(', ', array_fill(0, count($expired_timeout_names), '%s'));
                $value_placeholders = implode(', ', array_fill(0, count($value_option_names), '%s'));

                $deleted_values = $wpdb->query($wpdb->prepare(
                    "DELETE FROM $wpdb->options WHERE option_name IN ($value_placeholders)",
                    $value_option_names
                ));
                $deleted_timeouts = $wpdb->query($wpdb->prepare(
                    "DELETE FROM $wpdb->options WHERE option_name IN ($timeout_placeholders)",
                    $expired_timeout_names
                ));
            }

            $cleaned['expired_transients'] = max(0, (int) $deleted_timeouts) + max(0, (int) $deleted_values);
        }

        return $cleaned;
    }

    public function clean($items)
    {
        return $this->clean_items($items);
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
