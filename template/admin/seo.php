<div class="wrap vd-ons sweetaddons-dashboard">
    <h1 class="sad-title">🔍 Pengaturan SEO</h1>

    <form method="post" action="" class="sad-form" x-data="seoForm" @submit.prevent="save">
        <?php wp_nonce_field('sweetaddons_seo_settings'); ?>

        <div class="sad-top">

            <!-- Left Column (Main Settings) -->
            <div class="sad-top-left">

                <!-- General SEO Settings -->
                <div class="sad-card" id="seo-general-settings">
                    <div class="sad-card-title">🏠 SEO Halaman Utama</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_home_title">Judul Halaman Utama</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_home_title" name="sweetaddons_seo_home_title" value="<?php echo esc_attr($home_title); ?>" class="large-text" />
                                <p class="description">Kosongkan untuk menggunakan nama situs dan tagline.</p>
                                <div id="home-title-counter" style="font-size: 12px; color: #666;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_home_description">Deskripsi Halaman Utama</label></th>
                            <td>
                                <textarea id="sweetaddons_seo_home_description" name="sweetaddons_seo_home_description" rows="3" class="large-text"><?php echo esc_textarea($home_description); ?></textarea>
                                <p class="description">Kosongkan untuk menggunakan tagline situs.</p>
                                <div id="home-desc-counter" style="font-size: 12px; color: #666;"></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="sad-card" id="seo-templates-settings">
                    <div class="sad-card-title">🧩 Template Judul & Deskripsi</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_single_title">Template Title (Single)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_single_title" name="sweetaddons_seo_template_single_title" value="<?php echo esc_attr($tpl_single_title); ?>" class="large-text" />
                                <p class="description">Placeholders: {post_title}, {site_name}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_single_description">Template Description (Single)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_single_description" name="sweetaddons_seo_template_single_description" value="<?php echo esc_attr($tpl_single_desc); ?>" class="large-text" />
                                <p class="description">Placeholders: {excerpt}, {site_tagline}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_page_title">Template Title (Page)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_page_title" name="sweetaddons_seo_template_page_title" value="<?php echo esc_attr($tpl_page_title); ?>" class="large-text" />
                                <p class="description">Placeholders: {page_title}, {site_name}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_page_description">Template Description (Page)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_page_description" name="sweetaddons_seo_template_page_description" value="<?php echo esc_attr($tpl_page_desc); ?>" class="large-text" />
                                <p class="description">Placeholders: {excerpt}, {site_tagline}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_category_title">Template Title (Category)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_category_title" name="sweetaddons_seo_template_category_title" value="<?php echo esc_attr($tpl_cat_title); ?>" class="large-text" />
                                <p class="description">Placeholders: {category_name}, {site_name}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_category_description">Template Description (Category)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_category_description" name="sweetaddons_seo_template_category_description" value="<?php echo esc_attr($tpl_cat_desc); ?>" class="large-text" />
                                <p class="description">Placeholders: {category_description}, {site_tagline}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_tag_title">Template Title (Tag)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_tag_title" name="sweetaddons_seo_template_tag_title" value="<?php echo esc_attr($tpl_tag_title); ?>" class="large-text" />
                                <p class="description">Placeholders: {tag_name}, {site_name}</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_template_tag_description">Template Description (Tag)</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_template_tag_description" name="sweetaddons_seo_template_tag_description" value="<?php echo esc_attr($tpl_tag_desc); ?>" class="large-text" />
                                <p class="description">Placeholders: {tag_description}, {site_tagline}</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Social Media Settings -->
                <div class="sad-card" id="seo-social-settings">
                    <div class="sad-card-title">📱 Social Media</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_default_og_image">Default OG Image</label></th>
                            <td>
                                <div class="og-image-container">
                                    <input type="url" id="sweetaddons_seo_default_og_image" name="sweetaddons_seo_default_og_image" value="<?php echo esc_url($default_og_image); ?>" style="display: none;" />
                                    <div class="og-image-preview" style="margin: 10px 0; cursor: pointer;" onclick="document.getElementById('upload-default-og-image').click()">
                                        <?php if ($default_og_image): ?>
                                            <div style="position: relative; display: inline-block;">
                                                <img src="<?php echo esc_url($default_og_image); ?>" alt="Preview" style="max-width: 100%; height: auto; border-radius: 4px;" />
                                                <div style="position: absolute; top: 5px; right: 5px; background: #23282d; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px; opacity: 0.8;">Click to change</div>
                                            </div>
                                        <?php else: ?>
                                            <div style="width: 100%; height: 150px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999; background: #f9f9f9; border-radius: 4px;">
                                                <span>📷 Select Image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="og-image-buttons" style="margin-top: 10px;">
                                        <button type="button" class="button" id="upload-default-og-image">Choose Image</button>
                                        <?php if ($default_og_image): ?>
                                            <button type="button" class="button" id="remove-default-og-image" style="margin-left: 8px;">Remove Image</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_twitter_site">Twitter Username</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_twitter_site" name="sweetaddons_seo_twitter_site" value="<?php echo esc_attr($twitter_site); ?>" class="regular-text" placeholder="username" />
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Analytics -->
                <div class="sad-card">
                    <div class="sad-card-title">📊 Analytics & Tools</div>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="sweetaddons_seo_google_search_console">Search Console</label></th>
                            <td>
                                <input type="text" id="sweetaddons_seo_google_search_console" name="sweetaddons_seo_google_search_console" value="<?php echo esc_attr($google_search_console); ?>" class="large-text" placeholder="Verification Code / File Name" />
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- Right Column (Sidebar) -->
            <div class="sad-top-right">

                <!-- Save Button Card -->
                <div class="sad-card">
                    <div class="sad-card-title">💾 Simpan Perubahan</div>
                    <div class="sad-subtext" style="margin-bottom: 15px;">Pastikan untuk menyimpan pengaturan setelah melakukan perubahan.</div>
                    <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                </div>

                <!-- Technical SEO -->
                <div class="sad-card" id="seo-technical-settings">
                    <div class="sad-card-title">⚙️ Technical SEO</div>
                    <p>
                        <label>
                            <input type="checkbox" name="sweetaddons_seo_enable_sitemap" value="1" <?php checked($enable_sitemap, '1'); ?> />
                            Enable XML Sitemap
                        </label>
                    </p>
                    <?php if ($enable_sitemap): ?>
                        <p class="description">
                            <a href="<?php echo home_url('/sitemap.xml'); ?>" target="_blank" class="button button-small">View Sitemap</a>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Features Info -->
                <div class="sad-card">
                    <div class="sad-card-title">✨ Fitur SEO</div>
                    <ul style="list-style-type: disc; margin-left: 20px; color: #666;">
                        <li style="margin-bottom: 5px;">Judul & Deskripsi Meta</li>
                        <li style="margin-bottom: 5px;">Open Graph Support</li>
                        <li style="margin-bottom: 5px;">XML Sitemap Generator</li>
                        <li style="margin-bottom: 5px;">Robots.txt Control</li>
                        <li>Schema.org Data</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('seoForm', () => ({
            saving: false,
            nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
            async save() {
                this.saving = true;
                try {
                    const payload = {
                        sweetaddons_seo_home_title: document.getElementById('sweetaddons_seo_home_title').value,
                        sweetaddons_seo_home_description: document.getElementById('sweetaddons_seo_home_description').value,
                        sweetaddons_seo_default_og_image: document.getElementById('sweetaddons_seo_default_og_image').value,
                        sweetaddons_seo_twitter_site: document.getElementById('sweetaddons_seo_twitter_site').value,
                        sweetaddons_seo_google_search_console: document.getElementById('sweetaddons_seo_google_search_console').value,
                        sweetaddons_seo_template_single_title: document.getElementById('sweetaddons_seo_template_single_title').value,
                        sweetaddons_seo_template_single_description: document.getElementById('sweetaddons_seo_template_single_description').value,
                        sweetaddons_seo_template_page_title: document.getElementById('sweetaddons_seo_template_page_title').value,
                        sweetaddons_seo_template_page_description: document.getElementById('sweetaddons_seo_template_page_description').value,
                        sweetaddons_seo_template_category_title: document.getElementById('sweetaddons_seo_template_category_title').value,
                        sweetaddons_seo_template_category_description: document.getElementById('sweetaddons_seo_template_category_description').value,
                        sweetaddons_seo_template_tag_title: document.getElementById('sweetaddons_seo_template_tag_title').value,
                        sweetaddons_seo_template_tag_description: document.getElementById('sweetaddons_seo_template_tag_description').value,
                        sweetaddons_seo_enable_sitemap: document.querySelector('input[name="sweetaddons_seo_enable_sitemap"]').checked ? '1' : '0'
                    };
                    const res = await fetch('<?php echo esc_url_raw(get_rest_url(null, '/sweetaddons/v1/seo/options')); ?>', {
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
                    alert('✅ Pengaturan SEO berhasil disimpan via REST API!');
                } catch (e) {
                    alert('❌ ' + e.message);
                } finally {
                    this.saving = false;
                }
            }
        }));
    });
    jQuery(document).ready(function($) {
        // Character counters
        function updateCounter(input, counter, recommended) {
            const length = input.val().length;
            let color = '#666';
            if (length > recommended + 10) color = '#d63638';
            else if (length > recommended) color = '#ff922b';
            else if (length > recommended - 10) color = '#00a32a';

            counter.html(length + ' characters').css('color', color);
        }

        const homeTitleInput = $('#sweetaddons_seo_home_title');
        const homeTitleCounter = $('#home-title-counter');
        const homeDescInput = $('#sweetaddons_seo_home_description');
        const homeDescCounter = $('#home-desc-counter');

        homeTitleInput.on('input', function() {
            updateCounter(homeTitleInput, homeTitleCounter, 60);
        });

        homeDescInput.on('input', function() {
            updateCounter(homeDescInput, homeDescCounter, 160);
        });

        // Initial count
        updateCounter(homeTitleInput, homeTitleCounter, 60);
        updateCounter(homeDescInput, homeDescCounter, 160);

        // OG Image preview update for default OG image
        function updateDefaultOGImagePreview(imageUrl) {
            const previewContainer = $('#sweetaddons_seo_default_og_image').siblings('.og-image-preview');
            const removeButton = $('#remove-default-og-image');
            const buttonsContainer = $('.og-image-buttons');

            if (imageUrl) {
                previewContainer.html('<div style="position: relative; display: inline-block;"><img src="' + imageUrl + '" alt="Default OG Image Preview" style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9; border-radius: 4px;" /><div style="position: absolute; top: 5px; right: 5px; background: #23282d; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px; opacity: 0.8;">Click to change</div></div>');
                previewContainer.attr('onclick', 'document.getElementById(\'upload-default-og-image\').click()');
                buttonsContainer.html('<button type="button" class="button" id="upload-default-og-image">Choose Image</button><button type="button" class="button" id="remove-default-og-image" style="margin-left: 8px;">Remove Image</button>');
            } else {
                previewContainer.html('<div style="width: 300px; height: 158px; border: 2px dashed #0073aa; display: flex; align-items: center; justify-content: center; color: #0073aa; font-size: 14px; background: #f9f9f9; border-radius: 4px; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.borderColor=\'#005a87\'; this.style.background=\'#f0f8ff\';" onmouseout="this.style.borderColor=\'#0073aa\'; this.style.background=\'#f9f9f9\';"><div style="text-align: center;"><div style="font-size: 32px; margin-bottom: 8px;">📷</div><div>Click to choose image</div><div style="font-size: 11px; color: #666; margin-top: 4px;">Recommended: 1200x630px</div></div></div>');
                previewContainer.attr('onclick', 'document.getElementById(\'upload-default-og-image\').click()');
                buttonsContainer.html('<button type="button" class="button" id="upload-default-og-image">Choose Image</button>');
            }
        }

        // Media uploader for default OG image
        $(document).on('click', '#upload-default-og-image', function(e) {
            e.preventDefault();

            // Check if wp.media exists
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                alert('WordPress media uploader is not available. Please make sure you are on a settings page.');
                return;
            }

            const mediaUploader = wp.media({
                title: 'Choose Default Open Graph Image',
                button: {
                    text: 'Use This Image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#sweetaddons_seo_default_og_image').val(attachment.url);
                updateDefaultOGImagePreview(attachment.url);
            });

            mediaUploader.open();
        });

        // Remove default OG image
        $(document).on('click', '#remove-default-og-image', function(e) {
            e.preventDefault();
            $('#sweetaddons_seo_default_og_image').val('');
            updateDefaultOGImagePreview('');
        });

        // Manual URL input change for default OG image
        $('#sweetaddons_seo_default_og_image').on('input change', function() {
            updateDefaultOGImagePreview($(this).val());
        });
    });
</script>