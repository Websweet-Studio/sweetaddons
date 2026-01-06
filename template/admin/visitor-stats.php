<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">📊 Statistik Pengunjung</h1>

    <?php echo $rebuild_message; ?>

    <!-- Rebuild Stats Button -->
    <div class="sad-card" style="margin-bottom: 20px;">
        <div class="sad-card-title">Maintenance</div>
        <form method="post" style="display: inline;">
            <?php wp_nonce_field('rebuild_stats'); ?>
            <input type="hidden" name="rebuild_stats" value="1">
            <button type="submit" class="button button-secondary" onclick="return confirm('Apakah Anda yakin ingin membangun ulang statistik? Ini akan menghitung ulang semua data dari log yang ada.')">
                🔄 Bangun Ulang Statistik
            </button>
            <span style="margin-left: 10px; color: #666; font-size: 13px;">
                Gunakan ini jika hitungan pengunjung tampak tidak benar
            </span>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="sad-grid stats-summary" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">

        <div class="sad-card sad-stat">
            <div class="sad-card-title">Hari Ini</div>
            <div class="sad-card-value"><?php echo $summary_stats['today']->unique_visitors ?: 0; ?></div>
            <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['today']->total_visits ?: 0; ?></div>
        </div>

        <div class="sad-card sad-stat">
            <div class="sad-card-title">Minggu Ini</div>
            <div class="sad-card-value"><?php echo $summary_stats['this_week']->unique_visitors ?: 0; ?></div>
            <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['this_week']->total_visits ?: 0; ?></div>
        </div>

        <div class="sad-card sad-stat">
            <div class="sad-card-title">Bulan Ini</div>
            <div class="sad-card-value"><?php echo $summary_stats['this_month']->unique_visitors ?: 0; ?></div>
            <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['this_month']->total_visits ?: 0; ?></div>
        </div>

        <div class="sad-card sad-stat">
            <div class="sad-card-title">All Time</div>
            <div class="sad-card-value"><?php echo $summary_stats['all_time']->unique_visitors ?: 0; ?></div>
            <div class="sad-subtext">Kunjungan: <?php echo $summary_stats['all_time']->total_visits ?: 0; ?></div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="sad-grid charts-section" style="grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">

        <!-- Daily Visits Chart -->
        <div class="sad-card">
            <div class="sad-card-title">📈 Daily Visits (Last 30 Days)</div>
            <div style="height: 300px; position: relative;">
                <canvas id="dailyVisitsChart"></canvas>
            </div>
        </div>

        <!-- Top Pages Chart -->
        <div class="sad-card">
            <div class="sad-card-title">📄 Halaman Teratas</div>
            <div style="height: 300px; position: relative;">
                <canvas id="topPagesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="sad-grid tables-section" style="grid-template-columns: 1fr 1fr; gap: 20px;">

        <!-- Top Pages Table -->
        <div class="sad-card">
            <div class="sad-card-title">🏆 Halaman Teratas (30 Hari Terakhir)</div>
            <table class="widefat striped" style="border:none; box-shadow:none;">
                <thead>
                    <tr style="background-color: #f0f0f1;">
                        <th>Page URL</th>
                        <th>Pengunjung Unik</th>
                        <th>Total Tampilan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($page_stats)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #666;">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($page_stats as $page): ?>
                            <tr>
                                <td><code><?php echo esc_html($page->page_url); ?></code></td>
                                <td><?php echo esc_html($page->unique_visitors); ?></td>
                                <td><?php echo esc_html($page->total_views); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Referrers Table -->
        <div class="sad-card">
            <div class="sad-card-title">🔗 Rujukan Teratas (30 Hari Terakhir)</div>
            <table class="widefat striped" style="border:none; box-shadow:none;">
                <thead>
                    <tr style="background-color: #f0f0f1;">
                        <th>Referrer</th>
                        <th>Visits</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($referer_stats)): ?>
                        <tr>
                            <td colspan="2" style="text-align: center; color: #666;">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($referer_stats as $referer): ?>
                            <tr>
                                <td><code><?php echo esc_html(parse_url($referer->referer, PHP_URL_HOST) ?: $referer->referer); ?></code></td>
                                <td><?php echo esc_html($referer->visits); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Shortcode Examples -->
    <div class="sad-card" style="margin-top: 20px;">
        <div class="sad-card-title">🔖 Shortcode Statistik</div>
        <p class="sad-subtext" style="margin-bottom: 12px;">Gunakan shortcode berikut untuk menampilkan statistik di halaman atau posting.</p>

        <div class="sad-table">
            <div>
                <span>Default</span>
                <span>
                    <code id="sc-stat-default">[statistic]</code>
                    <button type="button" class="button" onclick="copyShortcode('#sc-stat-default')">Copy</button>
                </span>
            </div>
            <div>
                <span>Hari ini (minimal, 2 kolom)</span>
                <span>
                    <code id="sc-stat-today-min">[statistic show="today" style="minimal" columns="2"]</code>
                    <button type="button" class="button" onclick="copyShortcode('#sc-stat-today-min')">Copy</button>
                </span>
            </div>
            <div>
                <span>Total (cards, 4 kolom)</span>
                <span>
                    <code id="sc-stat-total-cards">[statistic show="total" style="cards" columns="4"]</code>
                    <button type="button" class="button" onclick="copyShortcode('#sc-stat-total-cards')">Copy</button>
                </span>
            </div>
            <div>
                <span>Semua (cards, 3 kolom)</span>
                <span>
                    <code id="sc-stat-all-cards">[statistic show="all" style="cards" columns="3"]</code>
                    <button type="button" class="button" onclick="copyShortcode('#sc-stat-all-cards')">Copy</button>
                </span>
            </div>
        </div>

        <div id="copy-success" style="display:none; margin-top:10px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:8px 10px; border-radius:6px;">
            Shortcode berhasil disalin
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function copyShortcode(selector) {
        const el = document.querySelector(selector);
        const text = el ? el.textContent : '';
        if (!text) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showCopySuccess);
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
            } catch (e) {}
            document.body.removeChild(ta);
            showCopySuccess();
        }
    }

    function showCopySuccess() {
        const box = document.getElementById('copy-success');
        if (!box) return;
        box.style.display = 'block';
        box.style.opacity = '1';
        setTimeout(() => {
            box.style.opacity = '0';
            box.style.display = 'none';
        }, 1500);
    }
    // Daily Visits Chart
    const dailyData = <?php echo json_encode(array_map(function ($stat) {
                            return [
                                'date' => $stat->visit_date,
                                'unique_visits' => (int)$stat->unique_visits,
                                'total_visits' => (int)$stat->total_visits
                            ];
                        }, $daily_stats)); ?>;

    const dailyLabels = dailyData.map(item => item.date);
    const uniqueVisitsData = dailyData.map(item => item.unique_visits);
    const totalVisitsData = dailyData.map(item => item.total_visits);

    const dailyCtx = document.getElementById('dailyVisitsChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                    label: 'Pengunjung Unik',
                    data: uniqueVisitsData,
                    borderColor: '#0073aa',
                    backgroundColor: 'rgba(0, 115, 170, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Total Kunjungan',
                    data: totalVisitsData,
                    borderColor: '#00a32a',
                    backgroundColor: 'rgba(0, 163, 42, 0.1)',
                    tension: 0.4,
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

    // Top Pages Chart
    const pageData = <?php echo json_encode(array_map(function ($page) {
                            return [
                                'url' => $page->page_url,
                                'views' => (int)$page->total_views
                            ];
                        }, array_slice($page_stats, 0, 8))); ?>;

    const pageLabels = pageData.map(item => item.url);
    const pageViews = pageData.map(item => item.views);

    const pageCtx = document.getElementById('topPagesChart').getContext('2d');
    new Chart(pageCtx, {
        type: 'bar',
        data: {
            labels: pageLabels,
            datasets: [{
                label: 'Page Views',
                data: pageViews,
                backgroundColor: [
                    '#0073aa', '#00a32a', '#d63638', '#ff922b',
                    '#7c3aed', '#db2777', '#059669', '#dc2626'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substring(0, 20) + '...' : label;
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

<style>
    @media (max-width: 768px) {

        .stats-summary,
        .charts-section,
        .tables-section,
        .shortcode-examples {
            grid-template-columns: 1fr !important;
        }
    }

    .chart-container canvas {
        height: 200px !important;
    }

    .table-container table {
        font-size: 14px;
    }

    .table-container code {
        background: #f1f1f1;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
    }
</style>