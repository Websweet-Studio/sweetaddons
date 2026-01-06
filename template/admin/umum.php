<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">Pengaturan Umum</h1>
    <div class="sad-grid">
        <div class="sad-card">
            <div class="sad-card-title">Pengaturan Utama</div>
            <form method="post" action="options.php" class="sad-form">
                <?php settings_fields('Sweetaddons_umum_group'); ?>
                <?php do_settings_sections('Sweetaddons_umum_group'); ?>

                <table class="form-table">
                    <?php
                    foreach ($umum_fields as $data) :
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
            <table class="widefat striped" style="border:none; box-shadow:none;">
                <thead>
                    <tr style="background-color: #f0f0f1;">
                        <th>Fitur</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($umum_fields as $field) : ?>
                        <tr>
                            <td><?php echo esc_html($field['title']); ?></td>
                            <td><?php echo get_option($field['id']) ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
