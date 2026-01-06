<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">Maintenance Mode</h1>
    <div class="sad-grid">
        <div class="sad-card">
            <div class="sad-card-title">Pengaturan Maintenance</div>
            <form method="post" action="options.php" class="sad-form">
                <?php settings_fields('Sweetaddons_maintenance_group'); ?>
                <?php do_settings_sections('Sweetaddons_maintenance_group'); ?>

                <table class="form-table">
                    <?php
                    foreach ($maintenance_fields as $data) :
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
                    <tr>
                        <td>Maintenance Mode</td>
                        <td><?php echo get_option('maintenance_mode') ? '<span style="color:#00a32a; font-weight:bold;">Aktif</span>' : '<span style="color:#999;">Nonaktif</span>'; ?></td>
                    </tr>
                    <tr>
                        <td>Judul</td>
                        <td><?php
                            $data = get_option('maintenance_mode_data');
                            echo isset($data['header']) && !empty($data['header']) ? esc_html($data['header']) : '<em>Default</em>';
                            ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
