<?php

/**
 * Visitor Statistics functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_Visitor_Stats
{
    private $logs_table;
    private $daily_stats_table;
    private $monthly_stats_table;
    private $page_stats_table;
    private $referrer_stats_table;
    private $tables_checked = false;

    public function __construct($register_hooks = true)
    {
        global $wpdb;
        $this->logs_table = $wpdb->prefix . 'sweetaddons_visitor_logs';
        $this->daily_stats_table = $wpdb->prefix . 'sweetaddons_daily_stats';
        $this->monthly_stats_table = $wpdb->prefix . 'sweetaddons_monthly_stats';
        $this->page_stats_table = $wpdb->prefix . 'sweetaddons_page_stats';
        $this->referrer_stats_table = $wpdb->prefix . 'sweetaddons_referrer_stats';

        if ($register_hooks) {
            add_action('wp', array($this, 'track_visitor'));
            add_action('sweetaddons_daily_aggregation', array($this, 'run_daily_aggregation'));
            add_shortcode('statistic', array($this, 'statistics_shortcode'));
        }
    }

    public function track_visitor()
    {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
            return;
        }

        if ($this->should_skip_tracking()) {
            return;
        }

        $this->ensure_tables_exist();

        global $wpdb;

        $visitor_ip = $this->get_visitor_ip();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        $page_url = isset($_SERVER['REQUEST_URI']) ? sanitize_url(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $referer = isset($_SERVER['HTTP_REFERER']) ? sanitize_url(wp_unslash($_SERVER['HTTP_REFERER'])) : '';
        $visit_date = current_time('Y-m-d');
        $visit_time = current_time('H:i:s');

        if ($visitor_ip === '0.0.0.0' || $page_url === '') {
            return;
        }

        $existing_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT page_url FROM {$this->logs_table} WHERE visitor_ip = %s AND visit_date = %s",
            $visitor_ip,
            $visit_date
        ));

        $is_unique_today = empty($existing_rows);
        $already_tracked_page = false;

        foreach ($existing_rows as $existing_row) {
            if (isset($existing_row->page_url) && $existing_row->page_url === $page_url) {
                $already_tracked_page = true;
                break;
            }
        }

        if ($already_tracked_page) {
            return;
        }

        $wpdb->insert(
            $this->logs_table,
            array(
                'visitor_ip' => $visitor_ip,
                'user_agent' => $user_agent,
                'page_url' => $page_url,
                'referer' => $referer,
                'visit_date' => $visit_date,
                'visit_time' => $visit_time,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        $this->update_daily_stats($visit_date, $is_unique_today);
        $this->update_page_stats($page_url, $visit_date, true);
        $this->update_referrer_stats($referer, $visit_date);
    }

    private function should_skip_tracking()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if ($user instanceof WP_User && array_intersect(array('administrator', 'editor'), (array) $user->roles)) {
                return true;
            }
        }

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }

        if (is_feed() || is_trackback() || is_preview()) {
            return true;
        }

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower((string) wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        if ($user_agent === '') {
            return true;
        }

        $bot_signatures = array('bot', 'crawl', 'slurp', 'spider', 'facebookexternalhit', 'whatsapp', 'telegrambot', 'pingdom', 'uptime');
        foreach ($bot_signatures as $signature) {
            if (strpos($user_agent, $signature) !== false) {
                return true;
            }
        }

        return false;
    }

    private function get_visitor_ip()
    {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', (string) $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
    }

    private function update_daily_stats($visit_date, $is_unique_visitor)
    {
        global $wpdb;

        $is_unique_today = $is_unique_visitor ? 1 : 0;

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
        if (empty($referer)) {
            return;
        }

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

        $wpdb->query(
            "DELETE FROM {$this->logs_table} 
             WHERE visit_date < DATE_SUB(CURDATE(), INTERVAL 90 DAY)"
        );
    }

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

        $today = $wpdb->get_row(
            "SELECT 
                unique_visitors,
                total_pageviews as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date = CURDATE()"
        );

        $this_week = $wpdb->get_row(
            "SELECT 
                SUM(unique_visitors) as unique_visitors,
                SUM(total_pageviews) as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
        );

        $this_month = $wpdb->get_row(
            "SELECT 
                SUM(unique_visitors) as unique_visitors,
                SUM(total_pageviews) as total_visits
             FROM {$this->daily_stats_table} 
             WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        );

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
            'today' => $today ?: (object) array('unique_visitors' => 0, 'total_visits' => 0),
            'this_week' => $this_week ?: (object) array('unique_visitors' => 0, 'total_visits' => 0),
            'this_month' => $this_month ?: (object) array('unique_visitors' => 0, 'total_visits' => 0),
            'all_time' => $all_time ?: (object) array('unique_visitors' => 0, 'total_visits' => 0),
        );
    }

    public function rebuild_daily_stats()
    {
        $this->ensure_tables_exist();
        global $wpdb;

        $wpdb->query("TRUNCATE TABLE {$this->daily_stats_table}");

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
                    'total_pageviews' => $data->total_pageviews,
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

        $wpdb->query("TRUNCATE TABLE {$this->page_stats_table}");

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
                    'total_views' => $data->total_views,
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
            'style' => 'default',
            'period' => '30',
            'show_chart' => 'true',
            'show_summary' => 'true',
            'show_pages' => 'false'
        ), $atts);

        $style = sanitize_text_field($atts['style']);
        $days = intval($atts['period']);
        $show_chart = filter_var($atts['show_chart'], FILTER_VALIDATE_BOOLEAN);
        $show_summary = filter_var($atts['show_summary'], FILTER_VALIDATE_BOOLEAN);
        $show_pages = filter_var($atts['show_pages'], FILTER_VALIDATE_BOOLEAN);

        $daily_stats = $this->get_daily_stats($days);
        $page_stats = $show_pages ? $this->get_page_stats($days) : array();
        $summary = $show_summary ? $this->get_summary_stats() : array();

        ob_start();

        $this->add_shortcode_styles();

        echo '<div class="sweetaddons-statistics-widget sweetaddons-statistics-' . esc_attr($style) . '">';

        if ($show_summary && !empty($summary)) {
            echo '<div class="sweetaddons-summary-grid">';
            echo '<div class="stat-card"><h4>Hari Ini</h4><div class="stat-number">' . number_format((int) $summary['today']->total_visits) . '</div><div class="stat-label">Kunjungan</div></div>';
            echo '<div class="stat-card"><h4>7 Hari</h4><div class="stat-number">' . number_format((int) $summary['this_week']->total_visits) . '</div><div class="stat-label">Kunjungan</div></div>';
            echo '<div class="stat-card"><h4>30 Hari</h4><div class="stat-number">' . number_format((int) $summary['this_month']->total_visits) . '</div><div class="stat-label">Kunjungan</div></div>';
            echo '<div class="stat-card"><h4>Total</h4><div class="stat-number">' . number_format((int) $summary['all_time']->total_visits) . '</div><div class="stat-label">Kunjungan</div></div>';
            echo '</div>';
        }

        if ($show_chart && !empty($daily_stats)) {
            echo '<div class="chart-container">';
            echo '<canvas id="sweetaddons-chart-' . wp_rand(1000, 9999) . '" width="400" height="200"></canvas>';
            echo '</div>';
        }

        if ($show_pages && !empty($page_stats)) {
            echo '<div class="page-stats"><h4>Halaman Terpopuler</h4><ul>';
            foreach ($page_stats as $page) {
                echo '<li><span class="page-url">' . esc_html($page->page_url) . '</span><span class="page-views">' . number_format((int) $page->total_views) . ' views</span></li>';
            }
            echo '</ul></div>';
        }

        echo '</div>';

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
            }
        </style>
<?php
    }
}
