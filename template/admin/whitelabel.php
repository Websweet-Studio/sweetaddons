<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🏷️ Pengaturan White Label</h1>
    <p>Kustomisasi branding plugin dan informasi yang ditampilkan kepada pengguna.</p>

    <form method="post" action="" class="sad-form">
        <?php wp_nonce_field('sweetaddons_whitelabel_settings'); ?>
        <div class="sad-top">
            <div class="sad-top-left">
                <div class="sad-card">
                    <div class="sad-card-title">⚙️ Konfigurasi White Label</div>

                    <!-- Plugin Information -->
                    <h3 style="margin-bottom: 20px;">📋 Informasi Plugin</h3>
                    <p style="margin-bottom: 20px;">Kustomisasi bagaimana plugin muncul di admin WordPress.</p>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_plugin_name">Nama Plugin</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whitelabel_plugin_name" name="sweetaddons_whitelabel_plugin_name" value="<?php echo esc_attr($plugin_name); ?>" class="large-text" />
                                <p class="description">Nama yang muncul di daftar plugin dan menu admin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_description">Deskripsi Plugin</label>
                            </th>
                            <td>
                                <textarea id="sweetaddons_whitelabel_description" name="sweetaddons_whitelabel_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                                <p class="description">Deskripsi yang ditampilkan di daftar plugin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_version">Versi</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whitelabel_version" name="sweetaddons_whitelabel_version" value="<?php echo esc_attr($version); ?>" class="regular-text" />
                                <p class="description">Nomor versi plugin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_plugin_uri">Plugin URI</label>
                            </th>
                            <td>
                                <input type="url" id="sweetaddons_whitelabel_plugin_uri" name="sweetaddons_whitelabel_plugin_uri" value="<?php echo esc_url($plugin_uri); ?>" class="large-text" />
                                <p class="description">URL yang akan dikunjungi pengguna ketika mengklik nama plugin.</p>
                            </td>
                        </tr>
                    </table>

                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Author Information -->
                    <h3 style="margin-bottom: 20px;">👤 Informasi Penulis</h3>
                    <p style="margin-bottom: 20px;">Kustomisasi detail penulis yang ditampilkan dalam informasi plugin.</p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_author">Nama Penulis</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whitelabel_author" name="sweetaddons_whitelabel_author" value="<?php echo esc_attr($author); ?>" class="large-text" />
                                <p class="description">Nama penulis yang ditampilkan di detail plugin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_author_uri">URI Penulis</label>
                            </th>
                            <td>
                                <input type="url" id="sweetaddons_whitelabel_author_uri" name="sweetaddons_whitelabel_author_uri" value="<?php echo esc_url($author_uri); ?>" class="large-text" />
                                <p class="description">The URL users will visit when clicking the author name.</p>
                            </td>
                        </tr>
                    </table>

                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                    <!-- Admin Customization -->
                    <h3 style="margin-bottom: 20px;">⚙️ Admin Customization</h3>
                    <p style="margin-bottom: 20px;">Customize the admin interface appearance.</p>

                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="sweetaddons_whitelabel_menu_title">Judul Menu Admin</label>
                            </th>
                            <td>
                                <input type="text" id="sweetaddons_whitelabel_menu_title" name="sweetaddons_whitelabel_menu_title" value="<?php echo esc_attr($menu_title); ?>" class="large-text" />
                                <p class="description">The title shown in the WordPress admin menu.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Sembunyikan Branding Asli</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="sweetaddons_whitelabel_hide_original" value="1" <?php checked($hide_original, '1'); ?> />
                                    Hide references to WebsweetStudio in admin interface
                                </label>
                                <p class="description">Remove WebsweetStudio branding from admin pages and footers.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="sad-card">
                    <div class="sad-card-title">🎨 Warna Brand</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="sweetaddons_whitelabel_accent_color">Accent Color</label></th>
                            <td>
                                <input type="color" id="sweetaddons_whitelabel_accent_color" name="sweetaddons_whitelabel_accent_color" value="<?php echo esc_attr($accent_color); ?>" />
                                <div class="sad-color-swatches" style="margin-top:10px;">
                                    <?php
                                    $swatches = array('#2271b1', '#00a32a', '#d63638', '#ff922b', '#7c3aed', '#db2777', '#059669', '#dc2626');
                                    foreach ($swatches as $sw) {
                                        echo '<span class="sad-swatch" data-color="' . esc_attr($sw) . '" style="display:inline-block;width:22px;height:22px;border-radius:4px;margin-right:6px;border:1px solid #ccd0d4; background:' . esc_attr($sw) . '; cursor:pointer;"></span>';
                                    }
                                    ?>
                                </div>
                                <p class="description">Warna aksen untuk branding admin. Gunakan palet cepat di atas atau pilih manual.</p>
                            </td>
                        </tr>
                    </table>
                    <script>
                        jQuery(function($) {
                            $('.sad-swatch').on('click', function() {
                                var c = $(this).data('color');
                                $('#sweetaddons_whitelabel_accent_color').val(c);
                            });
                        });
                    </script>
                </div>

                <div class="sad-card">
                    <div class="sad-card-title">📊 Perbandingan Branding</div>
                    <div class="sad-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="sad-card">
                            <div class="sad-card-title">🔴 Current (Original)</div>
                            <table class="form-table">
                                <tr>
                                    <th>Plugin Name</th>
                                    <td><?php echo esc_html($plugin_data['Name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?php echo esc_html($plugin_data['Description']); ?></td>
                                </tr>
                                <tr>
                                    <th>Version</th>
                                    <td><?php echo esc_html($plugin_data['Version']); ?></td>
                                </tr>
                                <tr>
                                    <th>Author</th>
                                    <td><?php echo esc_html($plugin_data['Author']); ?></td>
                                </tr>
                                <tr>
                                    <th>Plugin URI</th>
                                    <td><?php echo esc_html($plugin_data['PluginURI']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="sad-card">
                            <div class="sad-card-title">🟢 New (White Labeled)</div>
                            <table class="form-table">
                                <tr>
                                    <th>Plugin Name</th>
                                    <td><?php echo esc_html($plugin_name); ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?php echo esc_html($description); ?></td>
                                </tr>
                                <tr>
                                    <th>Version</th>
                                    <td><?php echo esc_html($version); ?></td>
                                </tr>
                                <tr>
                                    <th>Author</th>
                                    <td><?php echo esc_html($author); ?></td>
                                </tr>
                                <tr>
                                    <th>Plugin URI</th>
                                    <td><?php echo esc_html($plugin_uri); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sad-top-right">
                <div class="sad-card">
                    <div class="sad-card-title">💾 Simpan Perubahan</div>
                    <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan.</div>
                    <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                </div>
            </div>
        </div>
    </form>
</div>
