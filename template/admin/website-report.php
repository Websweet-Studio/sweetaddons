<div class="sad-grid">

    <!-- Site Information -->
    <div class="sad-card">
        <div class="sad-card-title">🌐 Informasi Website</div>
        <table class="widefat striped">
            <tr>
                <td><strong>Nama Website:</strong></td>
                <td><?php echo esc_html($site_name); ?></td>
            </tr>
            <tr>
                <td><strong>URL:</strong></td>
                <td><a href="<?php echo esc_url($site_url); ?>" target="_blank"><?php echo esc_url($site_url); ?></a></td>
            </tr>
            <tr>
                <td><strong>Deskripsi:</strong></td>
                <td><?php echo esc_html($site_description); ?></td>
            </tr>
            <tr>
                <td><strong>Email Admin:</strong></td>
                <td><?php echo esc_html($admin_email); ?></td>
            </tr>
            <tr>
                <td><strong>WordPress Version:</strong></td>
                <td><?php echo esc_html($wp_version); ?></td>
            </tr>
        </table>
    </div>

    <!-- Content Statistics -->
    <div class="sad-card">
        <div class="sad-card-title">📝 Statistik Konten</div>
        <table class="widefat striped">
            <tr>
                <td><strong>Posts Terpublikasi:</strong></td>
                <td><?php echo esc_html($published_posts); ?></td>
            </tr>
            <tr>
                <td><strong>Draft Posts:</strong></td>
                <td><?php echo esc_html($draft_posts); ?></td>
            </tr>
            <tr>
                <td><strong>Pages Terpublikasi:</strong></td>
                <td><?php echo esc_html($published_pages); ?></td>
            </tr>
            <tr>
                <td><strong>Total Pengguna:</strong></td>
                <td><?php echo esc_html($total_users); ?></td>
            </tr>
        </table>
    </div>

    <!-- Theme & Plugin Information -->
    <div class="sad-card">
        <div class="sad-card-title">🎨 Theme & Plugin</div>
        <table class="widefat striped">
            <tr>
                <td><strong>Active Theme:</strong></td>
                <td><?php echo esc_html($theme_name); ?></td>
            </tr>
            <tr>
                <td><strong>Theme Version:</strong></td>
                <td><?php echo esc_html($theme_version); ?></td>
            </tr>
            <tr>
                <td><strong>Active Plugins:</strong></td>
                <td><?php echo esc_html($active_plugin_count); ?></td>
            </tr>
            <tr>
                <td><strong>Total Plugin:</strong></td>
                <td><?php echo esc_html($total_plugin_count); ?></td>
            </tr>
        </table>
    </div>

    <!-- Server Information -->
    <div class="sad-card">
        <div class="sad-card-title">🖥️ Server Information</div>
        <table class="widefat striped">
            <tr>
                <td><strong>PHP Version:</strong></td>
                <td><?php echo esc_html($php_version); ?></td>
            </tr>
            <tr>
                <td><strong>Memory Limit:</strong></td>
                <td><?php echo esc_html($memory_limit); ?></td>
            </tr>
            <tr>
                <td><strong>Max Execution Time:</strong></td>
                <td><?php echo esc_html($max_execution_time); ?>s</td>
            </tr>
            <tr>
                <td><strong>Ukuran Database:</strong></td>
                <td><?php echo esc_html($db_size); ?> MB</td>
            </tr>
        </table>
    </div>

    <!-- Sweet Addons Status -->
    <div class="sad-card">
        <div class="sad-card-title">⚙️ <?php echo class_exists('Sweetaddons_WhiteLabel') ? esc_html(Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name')) : 'Sweet Addons'; ?> Status</div>
        <table class="widefat striped">
            <tr>
                <td><strong>Disable Comments:</strong></td>
                <td><?php echo get_option('fully_disable_comment') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Hide Admin Notice:</strong></td>
                <td><?php echo get_option('hide_admin_notice') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Maintenance Mode:</strong></td>
                <td><?php echo get_option('maintenance_mode') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Limit Login Attempts:</strong></td>
                <td><?php echo get_option('limit_login_attempts') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
            </tr>
            <tr>
                <td><strong>Block wp-login:</strong></td>
                <td><?php echo get_option('block_wp_login') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
            </tr>
        </table>
    </div>

    <!-- Quick Actions -->
    <div class="sad-card sad-actions">
        <div class="sad-card-title">🚀 Quick Actions</div>
        <div class="sad-actions-row">
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_visitor_stats'); ?>" class="button button-primary">📊 Visitor Statistics</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_seo'); ?>" class="button button-primary">🔍 SEO Settings</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_recaptcha'); ?>" class="button button-primary">🛡️ reCaptcha</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whitelabel'); ?>" class="button button-primary">🏷️ White Label</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_whatsapp'); ?>" class="button button-primary">💬 WhatsApp Chat</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_umum'); ?>" class="button button-secondary">Pengaturan Umum</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_maintenance'); ?>" class="button button-secondary">Maintenance Mode</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_block'); ?>" class="button button-secondary">Block Login</a>
            <a href="<?php echo admin_url('admin.php?page=Sweetaddons_spam'); ?>" class="button button-secondary">Spam Protection</a>
        </div>
    </div>
</div>
