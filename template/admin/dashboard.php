<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">Dashboard <?php echo class_exists('Sweetaddons_WhiteLabel') ? esc_html(Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name')) : 'Sweet Addons'; ?></h1>
    <div class="sad-top">
        <?php
        global $wpdb;
        $prefix = $wpdb->prefix;
        $today = $wpdb->get_row("SELECT unique_visitors as uv, total_pageviews as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date = CURDATE()");
        $this_week = $wpdb->get_row("SELECT SUM(unique_visitors) as uv, SUM(total_pageviews) as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $this_month = $wpdb->get_row("SELECT SUM(unique_visitors) as uv, SUM(total_pageviews) as pv FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        $daily_stats = $wpdb->get_results($wpdb->prepare("SELECT stat_date as visit_date, unique_visitors as unique_visits, total_pageviews as total_visits FROM {$prefix}sweetaddons_daily_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL %d DAY) ORDER BY stat_date ASC", 30));
        
        // Fetch detailed stats for new cards
        $stats_handler = new Sweetaddons_Visitor_Stats();
        $top_pages = $stats_handler->get_page_stats(5); // Top 5
        $top_referrers = $stats_handler->get_referer_stats(5); // Top 5

        // Fetch Blocked IPs
        $login_attempts = get_option('login_attempts', array());
        $blocked_ips_list = [];
        if (is_array($login_attempts)) {
            $current_time = current_time('timestamp');
            foreach ($login_attempts as $ip => $count) {
                if (strpos($ip, '_time') !== false) continue;
                if ($count >= 5) {
                    $last_time = isset($login_attempts[$ip . '_time']) ? $login_attempts[$ip . '_time'] : 0;
                    if (($current_time - $last_time) < (24 * 60 * 60)) {
                        $blocked_ips_list[$ip] = ['count' => $count, 'time' => $last_time];
                    }
                }
            }
        }

        $site_url = get_site_url();
        $site_name = get_bloginfo('name');
        $admin_email = get_option('admin_email');
        $wp_version = get_bloginfo('version');
        $php_version = phpversion();
        $memory_limit = ini_get('memory_limit');
        $max_execution_time = ini_get('max_execution_time');
        ?>
        <div class="sad-top-left">
            <div class="sad-row">
                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Hari Ini</div>
                    <div class="sad-card-value"><?php echo number_format($today ? (int)$today->pv : 0); ?></div>
                    <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($today ? (int)$today->uv : 0); ?></div>
                </div>
                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Minggu Ini</div>
                    <div class="sad-card-value"><?php echo number_format($this_week ? (int)$this_week->pv : 0); ?></div>
                    <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($this_week ? (int)$this_week->uv : 0); ?></div>
                </div>
                <div class="sad-card sad-stat">
                    <div class="sad-card-title">Bulan Ini</div>
                    <div class="sad-card-value"><?php echo number_format($this_month ? (int)$this_month->pv : 0); ?></div>
                    <div class="sad-subtext">Kunjungan • Pengunjung: <?php echo number_format($this_month ? (int)$this_month->uv : 0); ?></div>
                </div>
            </div>
            <div class="sad-card">
                <div class="sad-card-title">Grafik 30 Hari Terakhir</div>
                <canvas id="sadThirtyChart"></canvas>
            </div>
        </div>
        <div class="sad-top-right">
            <div class="sad-card">
                <div class="sad-card-title">System Health</div>
                <div class="sad-chips">
                    <span class="sad-chip">PHP <?php echo esc_html($php_version); ?></span>
                    <span class="sad-chip">Memory <?php echo esc_html($memory_limit); ?></span>
                    <span class="sad-chip">Max Exec <?php echo esc_html($max_execution_time); ?>s</span>
                </div>
            </div>
            <div class="sad-card">
                <div class="sad-card-title">Informasi Situs</div>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nama</td>
                            <td><?php echo esc_html($site_name); ?></td>
                        </tr>
                        <tr>
                            <td>URL</td>
                            <td><a href="<?php echo esc_url($site_url); ?>" target="_blank"><?php echo esc_url($site_url); ?></a></td>
                        </tr>
                        <tr>
                            <td>Email Admin</td>
                            <td><?php echo esc_html($admin_email); ?></td>
                        </tr>
                        <tr>
                            <td>WordPress</td>
                            <td><?php echo esc_html($wp_version); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="sad-grid">
        <!-- Top Content -->
        <div class="sad-card">
            <div class="sad-card-title">Konten Terpopuler (30 Hari)</div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Halaman</th>
                        <th>Views</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_pages)) : ?>
                        <?php foreach ($top_pages as $page) : ?>
                            <tr>
                                <td><a href="<?php echo esc_url(home_url($page->page_url)); ?>" target="_blank" title="<?php echo esc_attr($page->page_url); ?>"><?php echo esc_html(substr($page->page_url, 0, 25)) . (strlen($page->page_url) > 25 ? '...' : ''); ?></a></td>
                                <td><?php echo number_format($page->total_views); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="2">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Referrers -->
        <div class="sad-card">
            <div class="sad-card-title">Sumber Lalu Lintas (30 Hari)</div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Sumber</th>
                        <th>Kunjungan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_referrers)) : ?>
                        <?php foreach ($top_referrers as $ref) : ?>
                            <tr>
                                <td><?php echo esc_html($ref->referer); ?></td>
                                <td><?php echo number_format($ref->visits); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="2">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Blocked IPs (Conditional) -->
        <?php if (!empty($blocked_ips_list)) : ?>
        <div class="sad-card">
            <div class="sad-card-title">IP Terblokir (Login)</div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Percobaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocked_ips_list as $ip => $data) : ?>
                        <tr>
                            <td><?php echo esc_html($ip); ?></td>
                            <td><?php echo esc_html($data['count']); ?> (<?php echo human_time_diff($data['time']); ?> yll)</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="sad-card">
            <div class="sad-card-title">Status Fitur</div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Disable Comments</td>
                        <td><?php echo get_option('fully_disable_comment') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Hide Admin Notice</td>
                        <td><?php echo get_option('hide_admin_notice') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Maintenance Mode</td>
                        <td><?php echo get_option('maintenance_mode') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Limit Login</td>
                        <td><?php echo get_option('limit_login_attempts') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Block wp-login</td>
                        <td><?php echo get_option('block_wp_login') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Disable XML-RPC</td>
                        <td><?php echo get_option('disable_xmlrpc') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Disable REST API</td>
                        <td><?php echo get_option('disable_rest_api') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Disable Gutenberg</td>
                        <td><?php echo get_option('disable_gutenberg') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>reCaptcha</td>
                        <td><?php echo get_option('captcha_Sweetaddons') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="sad-card sad-actions">
            <div class="sad-card-title">Aksi Cepat</div>
            <div class="sad-actions-row">
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_visitor_stats'); ?>" class="button button-primary">Statistik</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_seo'); ?>" class="button button-primary">SEO</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_recaptcha'); ?>" class="button button-primary">reCaptcha</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whitelabel'); ?>" class="button button-primary">White Label</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whatsapp'); ?>" class="button button-primary">WhatsApp</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_umum'); ?>" class="button button-secondary">Umum</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_maintenance'); ?>" class="button button-secondary">Maintenance</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_block'); ?>" class="button button-secondary">Blokir Login</a>
                <a href="<?php echo admin_url('admin.php?page=Sweetaddons_spam'); ?>" class="button button-secondary">Proteksi Spam</a>
            </div>
        </div>
    </div>
</div>
<script>
    (function() {
        var data = <?php echo json_encode(array_map(function ($stat) {
                        return array(
                            'date' => $stat->visit_date,
                            'unique' => (int)$stat->unique_visits,
                            'total' => (int)$stat->total_visits
                        );
                    }, $daily_stats ?: array())); ?>;
        var labels = data.map(function(i) {
            return i.date;
        });
        var uniqueData = data.map(function(i) {
            return i.unique;
        });
        var totalData = data.map(function(i) {
            return i.total;
        });
        var ctx = document.getElementById('sadThirtyChart');
        if (ctx && window.Chart) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Pengunjung Unik',
                            data: uniqueData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.15)',
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Total Kunjungan',
                            data: totalData,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14,165,233,0.1)',
                            tension: 0.35,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }
    })();
</script>