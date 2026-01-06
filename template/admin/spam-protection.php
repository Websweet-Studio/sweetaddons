<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">Proteksi Spam</h1>
    <div class="sad-grid">
        <div class="sad-card">
            <div class="sad-card-title">Pengaturan Utama</div>
            <form method="post" action="options.php" class="sad-form">
                <?php settings_fields('custom_admin_options_group'); ?>
                <?php do_settings_sections('custom_admin_options_group'); ?>
                <table class="form-table">
                    <?php
                    foreach ($spam_fields as $data) :
                        echo '<tr>';
                        echo '<th scope="row">';
                        echo $data['title'];
                        echo '</th>';
                        echo '<td>';
                        $this->field($data);
                        echo '</td>';
                        echo '</tr>';
                    endforeach;
                    ?>
                </table>
                <div class="sad-actions-row" style="justify-content: flex-end;">
                    <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
                </div>
            </form>
        </div>
        <div class="sad-card">
            <div class="sad-card-title">Ringkasan</div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Batasi Percobaan Login</td>
                        <td><?php echo get_option('limit_login_attempts') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Nonaktifkan XML-RPC</td>
                        <td><?php echo get_option('disable_xmlrpc') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Nonaktifkan REST API</td>
                        <td><?php echo get_option('disable_rest_api') ? '<span class="sad-status-active">Aktif</span>' : '<span class="sad-status-inactive">Nonaktif</span>'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>