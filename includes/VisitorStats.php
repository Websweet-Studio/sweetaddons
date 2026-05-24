<?php
namespace Sweetaddons;

/**
 * Visitor Statistics functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class VisitorStats
{
    private $logs_table;
    private $daily_stats_table;
    private $monthly_stats_table;
    private $page_stats_table;
    private $referrer_stats_table;
    private $tables_checked = false;

    public function __construct()
    {
        global $wpdb;
        $this->logs_table = $wpdb->prefix . 'sweetaddons_visitor_logs';
        $this->daily_stats_table = $wpdb->prefix . 'sweetaddons_daily_stats';
        $this->monthly_stats_table = $wpdb->prefix . 'sweetaddons_monthly_stats';
        $this->page_stats_table = $wpdb->prefix . 'sweetaddons_page_stats';
        $this->referrer_stats_table = $wpdb->prefix . 'sweetaddons_referrer_stats';

        add_action('wp', array($this, 'track_visitor'));
        add_action('sweetaddons_daily_aggregation', array($this, 'run_daily_aggregation'));
        add_shortcode('statistic', array($this, 'statistics_shortcode'));
    }

    public function track_visitor()
    {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
            return;
        }

        $this->ensure_tables_exist();

        global $wpdb;

        $visitor_ip = $this->get_visitor_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
        $page_url = sanitize_url($_SERVER['REQUEST_URI']);
        $referer = isset($_SERVER['HTTP_REFERER']) ? sanitize_url($_SERVER['HTTP_REFERER']) : '';
        $visit_date = current_time('Y-m-d');
        $visit_time = current_time('H:i:s');

        // Check if this visitor has already been recorded today for this page
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->logs_table}
             WHERE visitor_ip = %s AND page_url = %s AND visit_date = %s",
            $visitor_ip,
            $page_url,
            $visit_date
        ));

        // Check if this is a unique visitor for today (before inserting)
        $is_unique_today = !$wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->logs_table}
             WHERE visitor_ip = %s AND visit_date = %s",
            $visitor_ip,
            $visit_date
        ));

        // Check if this is a unique visitor for this page today (before inserting)
        $is_unique_page_today = !$wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$this->logs_table}
             WHERE visitor_ip = %s AND page_url = %s AND visit_date = %s",
            $visitor_ip,
            $page_url,
            $visit_date
        ));

        if (!$existing) {
            // Insert into logs table
            $wpdb->insert(
                $this->logs_table,
                array(
                    'visitor_ip' => $visitor_ip,
                    'user_agent' => $user_agent,
                    'page_url' => $page_url,
                    'referer' => $referer,
                    'visit_date' => $visit_date,
                    'visit_time' => $visit_time
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );

            // Real-time update aggregation tables (untuk hari ini)
            $this->update_daily_stats($visit_date, $is_unique_today);
            $this->update_page_stats($page_url, $visit_date, $is_unique_page_today);
            $this->update_referrer_stats($referer, $visit_date);
        }
    }

    private function get_visitor_ip()
    {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
    }

    private function update_daily_stats($visit_date, $is_unique_visitor)
    {
        global $wpdb;

        $is_unique_today = $is_unique_visitor ? 1 : 0;

        // Update or insert daily stats
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$this->daily_stats_table} (stat_date, unique_visitors, total_pageviews)
             VALUES (%s, %d, 1)
             ON DUPLICATE KEY UPDATE
             unique_visitors = CASE
                 WHEN %d = 1 THEN unique_visitors + 1
                 ELSE unique_visitors
             END,
             total_pageviews = total_pageviews + 1",
            $visit_date,
            $is_unique_today,
            $is_unique_today
        ));
    }

    private function update_page_stats($page_url, $visit_date, $is_unique_visitor)
    {
        global $wpdb;

        $is_unique_page_today = $is_unique_visitor ? 1 : 0;

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$this->page_stats_table} (page_url, stat_date, unique_visitors, total_views)
             VALUES (%s, %s, %d, 1)
             ON DUPLICATE KEY UPDATE
             unique_visitors = CASE
                 WHEN %d = 1 THEN unique_visitors + 1
                 ELSE unique_visitors
             END,
             total_views = total_views + 1",
            $page_url,
            $visit_date,
            $is_unique_page_today,
            $is_unique_page_today
        ));
    }

    private function update_referrer_stats($referer, $visit_date)
    {
        if (empty($referer)) return;

        global $wpdb;

        $referrer_domain = parse_url($referer, PHP_URL_HOST) ?: $referer;

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$this->referrer_stats_table} (referrer_domain, stat_date, total_visits)
             VALUES (%s, %s, 1)
             ON DUPLICATE KEY UPDATE
             total_visits = total_visits + 1",
            $referrer_domain,
            $visit_date
        ));
    }

    public function run_daily_aggregation()
    {
        $this->ensure_tables_exist();
        $this->aggregate_monthly_stats();
        $this->cleanup_old_logs();
    }

    private function aggregate_monthly_stats()
    {
        global $wpdb;

        $current_month = date('n');
        $current_year = date('Y');

        // Aggregate current month data
        $monthly_data = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                SUM(unique_visitors) as unique_visitors,
                SUM(total_pageviews) as total_pageviews,
                AVG(bounce_rate) as avg_bounce_rate
             FROM {$this->daily_stats_table}
             WHERE MONTH(stat_date) = %d AND YEAR(stat_date) = %d",
            $current_month,
            $current_year
        ));

        if ($monthly_data) {
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$this->monthly_stats_table} (stat_year, stat_month, unique_visitors, total_pageviews, avg_bounce_rate)
                 VALUES (%d, %d, %d, %d, %f)
                 ON DUPLICATE KEY UPDATE
                 unique_visitors = %d,
                 total_pageviews = %d,
                 avg_bounce_rate = %f",
                $current_year,
                $current_month,
                $monthly_data->unique_visitors,
                $monthly_data->total_pageviews,
                $monthly_data->avg_bounce_rate,
                $monthly_data->unique_visitors,
                $monthly_data->total_pageviews,
                $monthly_data->avg_bounce_rate
            ));
        }
    }

    private function cleanup_old_logs()
    {
        global $wpdb;

        // Keep only last 90 days of raw logs (untuk performance)
        $wpdb->query(
            "DELETE FROM {$this->logs_table} 
             WHERE visit_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY)"
        );
    }

    // Optimized methods using aggregated tables
    public function get_daily_stats($days = 30)
    {
        $this->ensure_tables_exist();
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT 
                stat_date as visit_date,
                unique_visitors as unique_visits,
                total_pageviews as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             ORDER BY stat_date ASC",
            $days
        ));
    }

    public function get_page_stats($days = 30)
    {
        $this->ensure_tables_exist();
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT 
                page_url,
                SUM(unique_visitors) as unique_visitors,
                SUM(total_views) as total_views
             FROM {$this->page_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             GROUP BY page_url 
             ORDER BY total_views DESC
             LIMIT 10",
            $days
        ));
    }

    public function get_referer_stats($days = 30)
    {
        $this->ensure_tables_exist();
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT 
                referrer_domain as referer,
                SUM(total_visits) as visits
             FROM {$this->referrer_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             GROUP BY referrer_domain 
             ORDER BY visits DESC
             LIMIT 10",
            $days
        ));
    }

    public function get_summary_stats()
    {
        $this->ensure_tables_exist();
        global $wpdb;

        // Today's stats from daily aggregation
        $today = $wpdb->get_row(
            "SELECT 
                unique_visitors,
                total_pageviews as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date = CURDATE()"
        );

        // This week's stats
        $this_week = $wpdb->get_row(
            "SELECT 
                SUM(unique_visitors) as unique_visitors,
                SUM(total_pageviews) as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
        );

        // This month's stats
        $this_month = $wpdb->get_row(
            "SELECT 
                SUM(unique_visitors) as unique_visitors,
                SUM(total_pageviews) as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        );

        // All time stats from monthly aggregation + current month daily
        $all_time = $wpdb->get_row(
            "SELECT 
                (SELECT COALESCE(SUM(unique_visitors), 0) FROM {$this->monthly_stats_table}) +
                (SELECT COALESCE(SUM(unique_visitors), 0) FROM {$this->daily_stats_table} 
                 WHERE MONTH(stat_date) = MONTH(CURDATE()) AND YEAR(stat_date) = YEAR(CURDATE())) as unique_visitors,
                (SELECT COALESCE(SUM(total_pageviews), 0) FROM {$this->monthly_stats_table}) +
                (SELECT COALESCE(SUM(total_pageviews), 0) FROM {$this->daily_stats_table} 
                 WHERE MONTH(stat_date) = MONTH(CURDATE()) AND YEAR(stat_date) = YEAR(CURDATE())) as total_visits"
        );

        return array(
            'today' => $today ?: (object)['unique_visitors' => 0, 'total_visits' => 0],
            'this_week' => $this_week ?: (object)['unique_visitors' => 0, 'total_visits' => 0],
            'this_month' => $this_month ?: (object)['unique_visitors' => 0, 'total_visits' => 0],
            'all_time' => $all_time ?: (object)['unique_visitors' => 0, 'total_visits' => 0]
        );
    }

    public function rebuild_daily_stats()
    {
        $this->ensure_tables_exist();
        global $wpdb;

        // Clear existing daily stats
        $wpdb->query("TRUNCATE TABLE {$this->daily_stats_table}");

        // Rebuild from logs
        $daily_data = $wpdb->get_results(
            "SELECT 
                visit_date,
                COUNT(DISTINCT visitor_ip) as unique_visitors,
                COUNT(*) as total_pageviews
             FROM {$this->logs_table}
             GROUP BY visit_date
             ORDER BY visit_date"
        );

        foreach ($daily_data as $data) {
            $wpdb->insert(
                $this->daily_stats_table,
                array(
                    'stat_date' => $data->visit_date,
                    'unique_visitors' => $data->unique_visitors,
                    'total_pageviews' => $data->total_pageviews
                ),
                array('%s', '%d', '%d')
            );
        }

        return count($daily_data);
    }

    public function rebuild_page_stats()
    {
        $this->ensure_tables_exist();
        global $wpdb;

        // Clear existing page stats
        $wpdb->query("TRUNCATE TABLE {$this->page_stats_table}");

        // Rebuild from logs
        $page_data = $wpdb->get_results(
            "SELECT 
                page_url,
                visit_date,
                COUNT(DISTINCT visitor_ip) as unique_visitors,
                COUNT(*) as total_views
             FROM {$this->logs_table}
             GROUP BY page_url, visit_date
             ORDER BY visit_date, page_url"
        );

        foreach ($page_data as $data) {
            $wpdb->insert(
                $this->page_stats_table,
                array(
                    'page_url' => $data->page_url,
                    'stat_date' => $data->visit_date,
                    'unique_visitors' => $data->unique_visitors,
                    'total_views' => $data->total_views
                ),
                array('%s', '%s', '%d', '%d')
            );
        }

        return count($page_data);
    }

    public function statistics_shortcode($atts)
    {
        $this->ensure_tables_exist();
        $atts = shortcode_atts(array(
            'style' => 'default', // default, minimal, cards
            'show' => 'all', // all, today, total
            'columns' => '4' // 1, 2, 3, 4
        ), $atts, 'statistic');

        $stats = $this->get_summary_stats();

        ob_start();

        $style_class = 'sweetaddons-stats-' . sanitize_html_class($atts['style']);
        $columns_class = 'sweetaddons-stats-cols-' . sanitize_html_class($atts['columns']);

        echo '<div class="sweetaddons-statistics-widget ' . $style_class . ' ' . $columns_class . '">';

        if ($atts['show'] === 'all' || $atts['show'] === 'today') {
            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . number_format($stats['today']->total_visits) . '</div>';
            echo '<div class="stat-label">Kunjungan Hari Ini</div>';
            echo '</div>';

            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . number_format($stats['today']->unique_visitors) . '</div>';
            echo '<div class="stat-label">Pengunjung Hari Ini</div>';
            echo '</div>';
        }

        if ($atts['show'] === 'all' || $atts['show'] === 'total') {
            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . number_format($stats['all_time']->total_visits) . '</div>';
            echo '<div class="stat-label">Total Kunjungan</div>';
            echo '</div>';

            echo '<div class="stat-item">';
            echo '<div class="stat-number">' . number_format($stats['all_time']->unique_visitors) . '</div>';
            echo '<div class="stat-label">Total Pengunjung</div>';
            echo '</div>';
        }

        echo '</div>';

        // Add CSS if not already added
        if (!wp_style_is('sweetaddons-stats-shortcode', 'enqueued')) {
            $this->add_shortcode_styles();
        }

        return ob_get_clean();
    }

    private function ensure_tables_exist()
    {
        if ($this->tables_checked) {
            return;
        }

        global $wpdb;

        $tables = array(
            $this->logs_table,
            $this->daily_stats_table,
            $this->monthly_stats_table,
            $this->page_stats_table,
            $this->referrer_stats_table,
        );

        $missing = false;
        foreach ($tables as $table) {
            $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
            if ($exists !== $table) {
                $missing = true;
                break;
            }
        }

        if ($missing) {
            $this->create_tables();
        }

        $this->tables_checked = true;
    }

    private function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql1 = "CREATE TABLE {$this->logs_table} (
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

        $sql2 = "CREATE TABLE {$this->daily_stats_table} (
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

        $sql3 = "CREATE TABLE {$this->monthly_stats_table} (
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

        $sql4 = "CREATE TABLE {$this->page_stats_table} (
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

        $sql5 = "CREATE TABLE {$this->referrer_stats_table} (
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

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
        dbDelta($sql5);
    }

    private function add_shortcode_styles()
    {
?>
        <style>
            .sweetaddons-statistics-widget {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                margin: 20px 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }

            .sweetaddons-statistics-widget .stat-item {
                flex: 1;
                min-width: 150px;
                text-align: center;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 1px solid #e9ecef;
                transition: all 0.3s ease;
            }

            .sweetaddons-statistics-widget .stat-item:hover {
                background: #e9ecef;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .sweetaddons-statistics-widget .stat-number {
                font-size: 28px;
                font-weight: bold;
                color: #0073aa;
                margin-bottom: 8px;
                line-height: 1;
            }

            .sweetaddons-statistics-widget .stat-label {
                font-size: 14px;
                color: #666;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Style variations */
            .sweetaddons-stats-minimal .stat-item {
                background: transparent;
                border: none;
                padding: 10px;
            }

            .sweetaddons-stats-minimal .stat-item:hover {
                background: #f8f9fa;
                transform: none;
                box-shadow: none;
            }

            .sweetaddons-stats-cards .stat-item {
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                border: none;
            }

            .sweetaddons-stats-cards .stat-item:hover {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            }

            /* Column variations */
            .sweetaddons-stats-cols-1 {
                flex-direction: column;
                max-width: 200px;
            }

            .sweetaddons-stats-cols-2 .stat-item {
                flex-basis: calc(50% - 7.5px);
            }

            .sweetaddons-stats-cols-3 .stat-item {
                flex-basis: calc(33.333% - 10px);
            }

            .sweetaddons-stats-cols-4 .stat-item {
                flex-basis: calc(25% - 11.25px);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .sweetaddons-statistics-widget {
                    flex-direction: column;
                }

                .sweetaddons-statistics-widget .stat-item {
                    flex-basis: auto !important;
                }

                .sweetaddons-statistics-widget .stat-number {
                    font-size: 24px;
                }
            }

            @media (max-width: 480px) {
                .sweetaddons-statistics-widget .stat-item {
                    padding: 15px;
                    min-width: auto;
                }

                .sweetaddons-statistics-widget .stat-number {
                    font-size: 20px;
                }

                .sweetaddons-statistics-widget .stat-label {
                    font-size: 12px;
                }
            }
        </style>
<?php
    }
}