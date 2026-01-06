<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🔒 Pengaturan Blokir Login</h1>

    <div class="sad-grid">
        <div class="sad-card">
            <div class="sad-card-title">Pengaturan Utama</div>
            <form method="post" action="options.php" class="sad-form">
                <?php settings_fields('Sweetaddons_block_group'); ?>
                <?php do_settings_sections('Sweetaddons_block_group'); ?>

                <table class="form-table">
                    <?php
                    foreach ($block_fields as $data) :
                        echo '<tr>';
                        echo '<th scope="row">' . $data['title'] . '</th>';
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
            <table class="widefat striped" style="border:none; box-shadow:none;">
                <thead>
                    <tr style="background-color: #f0f0f1;">
                        <th>Fitur</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Status Blokir</td>
                        <td><?php echo $block_active ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>IP Whitelist</td>
                        <td><?php echo $whitelist_count > 0 ? $whitelist_count . ' IP' : '-'; ?></td>
                    </tr>
                    <tr>
                        <td>Negara Diizinkan</td>
                        <td><?php echo esc_html(get_option('whitelist_country', 'ID')); ?></td>
                    </tr>
                    <tr>
                        <td>Redirect URL</td>
                        <td><a href="<?php echo esc_url(get_option('redirect_to', 'http://127.0.0.1')); ?>" target="_blank">Cek Link</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
