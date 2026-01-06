<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🛡️ Pengaturan CAPTCHA Tulisan (Image)</h1>

    <form method="post" action="">
        <?php wp_nonce_field('sweetaddons_recaptcha_settings'); ?>

        <div class="sad-top">
            <!-- Left Column -->
            <div class="sad-top-left">

                <!-- reCaptcha Configuration -->
                <div class="sad-card" id="recaptcha-general-settings">
                    <div class="sad-card-title">⚙️ Konfigurasi Utama</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Status Fitur</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="captcha_aktif" value="1" <?php checked($aktif, '1'); ?> />
                                    Aktifkan CAPTCHA
                                </label>
                                <p class="description">Aktifkan perlindungan CAPTCHA.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Tingkat Kesulitan</th>
                            <td>
                                <select name="captcha_difficulty" id="captcha_difficulty">
                                    <option value="easy" <?php selected($difficulty, 'easy'); ?>>Mudah (4 Angka)</option>
                                    <option value="medium" <?php selected($difficulty, 'medium'); ?>>Sedang (5 Karakter)</option>
                                    <option value="hard" <?php selected($difficulty, 'hard'); ?>>Sulit (6 Karakter + Noise)</option>
                                </select>
                                <p class="description">Atur kompleksitas kode dan visual CAPTCHA.</p>

                                <div id="captcha-preview-container" style="margin-top: 15px;">
                                    <strong>Preview:</strong><br>
                                    <img id="captcha-preview-img" src="<?php echo add_query_arg(array('sweetaddons_captcha' => 'preview', 'difficulty' => $difficulty), home_url('/')); ?>" alt="Captcha Preview" style="border:1px solid #d0d4d9; height:50px; width:160px; background:#f5f6fa; border-radius:4px; margin-top: 5px;">
                                    <p><a href="#" id="refresh-captcha-preview" class="button button-small">Refresh Preview</a></p>
                                </div>

                                <script>
                                    jQuery(document).ready(function($) {
                                        function updateCaptchaPreview() {
                                            var difficulty = $('#captcha_difficulty').val();
                                            var src = '<?php echo home_url('/'); ?>?sweetaddons_captcha=preview&difficulty=' + difficulty + '&t=' + new Date().getTime();
                                            $('#captcha-preview-img').attr('src', src);
                                        }

                                        $('#captcha_difficulty').on('change', function() {
                                            updateCaptchaPreview();
                                        });

                                        $('#refresh-captcha-preview').on('click', function(e) {
                                            e.preventDefault();
                                            updateCaptchaPreview();
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Protection Areas -->
                <div class="sad-card" id="recaptcha-protection-settings">
                    <div class="sad-card-title">🔒 Area Perlindungan</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Form Login</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="captcha_login" value="1" <?php checked($login, '1'); ?> />
                                    Lindungi halaman login (wp-login.php)
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Form Registrasi</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="captcha_register" value="1" <?php checked($register, '1'); ?> />
                                    Lindungi form registrasi user baru
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Form Komentar</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="captcha_comment" value="1" <?php checked($comment, '1'); ?> />
                                    Lindungi kolom komentar postingan
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Current Status Details -->
                <div class="sad-card">
                    <div class="sad-card-title">📊 Detail Status</div>
                    <table class="widefat striped" style="border:none; box-shadow:none;">
                        <thead>
                            <tr style="background-color: #f0f0f1;">
                                <th>Fitur</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Generator Lokal</strong></td>
                                <td>
                                    <?php if ($aktif): ?>
                                        <span style="color: #00a32a; font-weight:bold;">Tersedia</span>
                                    <?php else: ?>
                                        <span style="color: #999;">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>Pembuatan gambar CAPTCHA di server</td>
                            </tr>
                            <tr>
                                <td><strong>Proteksi Login</strong></td>
                                <td>
                                    <?php if ($login && $aktif): ?>
                                        <span style="color: #00a32a; font-weight:bold;">Aktif</span>
                                    <?php else: ?>
                                        <span style="color: #999;">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>Brute force protection</td>
                            </tr>
                            <tr>
                                <td><strong>Proteksi Komentar</strong></td>
                                <td>
                                    <?php if ($comment && $aktif): ?>
                                        <span style="color: #00a32a; font-weight:bold;">Aktif</span>
                                    <?php else: ?>
                                        <span style="color: #999;">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>Spam comment protection</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Right Column -->
            <div class="sad-top-right">

                <!-- Save Button Card -->
                <div class="sad-card">
                    <div class="sad-card-title">💾 Simpan Perubahan</div>
                    <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan keys atau area proteksi.</div>
                    <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                </div>

                <!-- Setup Instructions -->
                <div class="sad-card">
                    <div class="sad-card-title">📋 Panduan Setup</div>
                    <h4 style="margin: 10px 0 5px; color: #23282d;">1. Aktivasi</h4>
                    <ul style="list-style-type: disc; margin-left: 20px; color: #666; font-size: 13px; margin-bottom: 15px;">
                        <li>Nyalakan status fitur CAPTCHA</li>
                        <li>Pilih area proteksi: Login, Registrasi, Komentar</li>
                        <li>Tidak memerlukan API key</li>
                    </ul>
                    <h4 style="margin: 10px 0 5px; color: #23282d;">2. Contact Form 7</h4>
                    <p style="font-size: 13px; color: #666; margin-bottom: 5px;">Gunakan tag berikut:</p>
                    <code style="display: block; background: #f0f0f1; padding: 8px; border-radius: 4px; font-size: 12px; margin-bottom: 15px;">[recaptcha]</code>
                    <ul style="list-style-type: disc; margin-left: 20px; color: #666; font-size: 13px;">
                        <li>Logout untuk tes form login</li>
                        <li>Buka postingan untuk tes komentar</li>
                        <li>Pastikan gambar CAPTCHA muncul dan input teks bekerja</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</div>
