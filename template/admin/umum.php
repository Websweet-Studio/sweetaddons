<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">Pengaturan Umum</h1>
    <div class="sad-grid">
        <div class="sad-card">
            <div class="sad-card-title">Pengaturan Utama</div>
            <form method="post" action="" class="sad-form" x-data="umumForm" @submit.prevent="save">

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

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('umumForm', () => ({
            saving: false,
            nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
            async save() {
                this.saving = true;
                try {
                    const payload = {
                        fully_disable_comment: document.getElementById('fully_disable_comment').checked ? '1' : '0',
                        hide_admin_notice: document.getElementById('hide_admin_notice').checked ? '1' : '0',
                        disable_gutenberg: document.getElementById('disable_gutenberg').checked ? '1' : '0',
                        classic_widget_Sweetaddons: document.getElementById('classic_widget_Sweetaddons').checked ? '1' : '0',
                        remove_slug_category_Sweetaddons: document.getElementById('remove_slug_category_Sweetaddons').checked ? '1' : '0'
                    };
                    const res = await fetch('<?php echo esc_url_raw(get_rest_url(null, '/sweetaddons/v1/umum/options')); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': this.nonce
                        },
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) {
                        const errText = await res.text();
                        throw new Error(errText || 'Gagal menyimpan pengaturan.');
                    }
                    alert('✅ Pengaturan Umum berhasil disimpan via REST API!');
                } catch (e) {
                    alert('❌ ' + e.message);
                } finally {
                    this.saving = false;
                }
            }
        }));
    });
</script>
