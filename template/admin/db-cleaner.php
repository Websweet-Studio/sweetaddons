<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🧹 Database Cleaner</h1>

    <form method="post" action="">
        <?php wp_nonce_field('sweetaddons_db_cleaner_action', 'sweetaddons_db_cleaner_nonce'); ?>

        <div class="sad-top">
            <div class="sad-top-left">
                <div class="sad-card">
                    <div class="sad-card-title">🗑️ Item yang Dapat Diberishkan</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><input type="checkbox" name="items[]" value="revisions" checked> Post Revisions</th>
                            <td><span class="sad-badge sad-badge-warning"><?php echo $stats['revisions']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_revisions'])); ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><input type="checkbox" name="items[]" value="auto_drafts" checked> Auto Drafts</th>
                            <td><span class="sad-badge sad-badge-warning"><?php echo $stats['auto_drafts']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_auto_drafts'])); ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><input type="checkbox" name="items[]" value="spam_comments" checked> Spam Comments</th>
                            <td><span class="sad-badge sad-badge-danger"><?php echo $stats['spam_comments']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_spam_comments'])); ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><input type="checkbox" name="items[]" value="trashed_comments" checked> Trashed Comments</th>
                            <td><span class="sad-badge sad-badge-danger"><?php echo $stats['trashed_comments']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_trashed_comments'])); ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><input type="checkbox" name="items[]" value="expired_transients" checked> Expired Transients</th>
                            <td><span class="sad-badge sad-badge-info"><?php echo $stats['expired_transients']; ?> items · ≈ <?php echo esc_html($cleaner->format_bytes($stats['size_expired_transients'])); ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="sad-top-right">
                <div class="sad-card">
                    <div class="sad-card-title">🚀 Aksi Pembersihan</div>
                    <p>Klik tombol di bawah untuk membersihkan item yang dipilih dari database. Pastikan Anda telah melakukan backup database sebelum melakukan pembersihan.</p>
                    <?php submit_button('Bersihkan Database Sekarang', 'primary', 'submit', false, array('style' => 'width: 100%;', 'onclick' => "return confirm('Apakah Anda yakin ingin membersihkan database? Tindakan ini tidak dapat dibatalkan.');")); ?>
                </div>
            </div>
        </div>
    </form>
</div>
