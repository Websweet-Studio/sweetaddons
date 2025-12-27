<?php

/**
 * SEO functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_SEO
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'output_meta_tags'), 1);
        add_action('wp_head', array($this, 'output_og_tags'), 2);
        add_action('add_meta_boxes', array($this, 'add_seo_meta_boxes'));
        add_action('save_post', array($this, 'save_seo_meta_data'));
        // add_action('init', array($this, 'handle_sitemap_request')); // Removed: incorrect hook usage
        add_action('init', array($this, 'register_sitemap_rewrite'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'template_redirect_sitemap'));
        add_filter('wp_title', array($this, 'custom_title'), 10, 2);
        add_filter('document_title_parts', array($this, 'custom_document_title_parts'));
        add_filter('redirect_canonical', array($this, 'disable_trailing_slash_redirect'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Disable WP Core sitemap if enabled
        add_filter('wp_sitemaps_enabled', array($this, 'disable_wp_sitemaps'));
    }

    public function disable_wp_sitemaps($enabled)
    {
        if (get_option('sweetaddons_seo_enable_sitemap', '1') === '1') {
            return false;
        }
        return $enabled;
    }

    public function output_meta_tags()
    {
        if (is_admin() || is_feed() || is_robots() || is_trackback()) {
            return;
        }

        global $post;

        // Meta description
        $meta_description = $this->get_meta_description();
        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }

        // Meta keywords
        $meta_keywords = $this->get_meta_keywords();
        if ($meta_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
        }

        // Robots meta
        $robots = $this->get_robots_meta();
        if ($robots) {
            echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
        }

        // Canonical URL
        $canonical = $this->get_canonical_url();
        if ($canonical) {
            echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
        }

        // Schema.org markup
        $this->output_schema_markup();
    }

    public function output_og_tags()
    {
        if (is_admin() || is_feed() || is_robots() || is_trackback()) {
            return;
        }

        global $post;

        // Open Graph tags
        echo '<meta property="og:type" content="' . $this->get_og_type() . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($this->get_page_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($this->get_canonical_url()) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

        $og_description = $this->get_meta_description();
        if ($og_description) {
            echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        }

        $og_image = $this->get_og_image();
        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";

            $image_data = wp_get_attachment_image_src(attachment_url_to_postid($og_image), 'full');
            if ($image_data) {
                echo '<meta property="og:image:width" content="' . $image_data[1] . '">' . "\n";
                echo '<meta property="og:image:height" content="' . $image_data[2] . '">' . "\n";
            }
        }

        // Twitter Card tags
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($this->get_page_title()) . '">' . "\n";

        if ($og_description) {
            echo '<meta name="twitter:description" content="' . esc_attr($og_description) . '">' . "\n";
        }

        if ($og_image) {
            echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
        }

        $twitter_site = get_option('sweetaddons_seo_twitter_site');
        if ($twitter_site) {
            echo '<meta name="twitter:site" content="@' . esc_attr($twitter_site) . '">' . "\n";
        }
    }

    private function get_page_title()
    {
        global $post;

        if (is_singular()) {
            $custom_title = get_post_meta($post->ID, '_sweetaddons_seo_title', true);
            if ($custom_title) {
                return $custom_title;
            }
            $site_name = get_bloginfo('name');
            if (get_post_type($post) === 'page') {
                $tpl = get_option('sweetaddons_seo_template_page_title');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'page_title' => get_the_title($post->ID),
                        'site_name' => $site_name
                    ));
                }
            } else {
                $tpl = get_option('sweetaddons_seo_template_single_title');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'post_title' => get_the_title($post->ID),
                        'site_name' => $site_name
                    ));
                }
            }
            return get_the_title($post->ID);
        }

        if (is_home() || is_front_page()) {
            $home_title = get_option('sweetaddons_seo_home_title');
            if ($home_title) {
                return $home_title;
            }
            return get_bloginfo('name') . ' - ' . get_bloginfo('description');
        }

        if (is_category()) {
            $tpl = get_option('sweetaddons_seo_template_category_title');
            if ($tpl) {
                return $this->apply_template($tpl, array(
                    'category_name' => single_cat_title('', false),
                    'site_name' => get_bloginfo('name')
                ));
            }
            return single_cat_title('', false);
        }

        if (is_tag()) {
            $tpl = get_option('sweetaddons_seo_template_tag_title');
            if ($tpl) {
                return $this->apply_template($tpl, array(
                    'tag_name' => single_tag_title('', false),
                    'site_name' => get_bloginfo('name')
                ));
            }
            return single_tag_title('', false);
        }

        if (is_archive()) {
            return strip_tags(get_the_archive_title());
        }

        return wp_get_document_title();
    }

    private function get_meta_description()
    {
        global $post;

        if (is_singular()) {
            $custom_desc = get_post_meta($post->ID, '_sweetaddons_seo_description', true);
            if ($custom_desc) {
                return $custom_desc;
            }

            if ($post->post_excerpt) {
                $excerpt = wp_trim_words($post->post_excerpt, 25);
            } else {
                $excerpt = wp_trim_words(strip_tags($post->post_content), 25);
            }
            if (get_post_type($post) === 'page') {
                $tpl = get_option('sweetaddons_seo_template_page_description');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'excerpt' => $excerpt,
                        'site_tagline' => get_bloginfo('description')
                    ));
                }
            } else {
                $tpl = get_option('sweetaddons_seo_template_single_description');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'excerpt' => $excerpt,
                        'site_tagline' => get_bloginfo('description')
                    ));
                }
            }
            return $excerpt;
        }

        if (is_home() || is_front_page()) {
            $home_desc = get_option('sweetaddons_seo_home_description');
            if ($home_desc) {
                return $home_desc;
            }
            return get_bloginfo('description');
        }

        if (is_category()) {
            $cat_desc = category_description();
            if ($cat_desc) {
                $desc = wp_trim_words(strip_tags($cat_desc), 25);
                $tpl = get_option('sweetaddons_seo_template_category_description');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'category_description' => $desc,
                        'site_tagline' => get_bloginfo('description')
                    ));
                }
                return $desc;
            }
        }

        if (is_tag()) {
            $tag_desc = tag_description();
            if ($tag_desc) {
                $desc = wp_trim_words(strip_tags($tag_desc), 25);
                $tpl = get_option('sweetaddons_seo_template_tag_description');
                if ($tpl) {
                    return $this->apply_template($tpl, array(
                        'tag_description' => $desc,
                        'site_tagline' => get_bloginfo('description')
                    ));
                }
                return $desc;
            }
        }

        return '';
    }

    private function apply_template($template, $context)
    {
        $map = array();
        foreach ($context as $k => $v) {
            $map['{' . $k . '}'] = $v;
        }
        return strtr($template, $map);
    }

    private function get_meta_keywords()
    {
        global $post;

        if (is_singular()) {
            $custom_keywords = get_post_meta($post->ID, '_sweetaddons_seo_keywords', true);
            if ($custom_keywords) {
                return $custom_keywords;
            }

            // Auto-generate from tags
            $tags = get_the_tags($post->ID);
            if ($tags) {
                $keywords = array();
                foreach ($tags as $tag) {
                    $keywords[] = $tag->name;
                }
                return implode(', ', $keywords);
            }
        }

        return '';
    }

    private function get_robots_meta()
    {
        global $post;

        $robots = array();

        if (is_singular()) {
            $noindex = get_post_meta($post->ID, '_sweetaddons_seo_noindex', true);
            $nofollow = get_post_meta($post->ID, '_sweetaddons_seo_nofollow', true);

            if ($noindex) {
                $robots[] = 'noindex';
            } else {
                $robots[] = 'index';
            }

            if ($nofollow) {
                $robots[] = 'nofollow';
            } else {
                $robots[] = 'follow';
            }
        } else {
            $robots[] = 'index';
            $robots[] = 'follow';
        }

        return implode(', ', $robots);
    }

    private function get_canonical_url()
    {
        global $post;

        if (is_singular()) {
            $custom_canonical = get_post_meta($post->ID, '_sweetaddons_seo_canonical', true);
            if ($custom_canonical) {
                return $custom_canonical;
            }
            return get_permalink($post->ID);
        }

        if (is_home()) {
            return home_url('/');
        }

        if (is_category()) {
            return get_category_link(get_queried_object_id());
        }

        if (is_tag()) {
            return get_tag_link(get_queried_object_id());
        }

        if (is_archive()) {
            return get_term_link(get_queried_object());
        }

        return '';
    }

    private function get_og_type()
    {
        if (is_singular('post')) {
            return 'article';
        }

        if (is_front_page() || is_home()) {
            return 'website';
        }

        return 'website';
    }

    private function get_og_image()
    {
        global $post;

        if (is_singular()) {
            // Custom OG image
            $custom_image = get_post_meta($post->ID, '_sweetaddons_seo_og_image', true);
            if ($custom_image) {
                return $custom_image;
            }

            // Featured image
            if (has_post_thumbnail($post->ID)) {
                return get_the_post_thumbnail_url($post->ID, 'large');
            }
        }

        // Default OG image
        $default_image = get_option('sweetaddons_seo_default_og_image');
        if ($default_image) {
            return $default_image;
        }

        return '';
    }

    private function output_schema_markup()
    {
        if (is_singular('post')) {
            $this->output_article_schema();
        } elseif (is_front_page() || is_home()) {
            $this->output_website_schema();
        }
    }

    private function output_article_schema()
    {
        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post->ID),
            'datePublished' => get_the_date('c', $post->ID),
            'dateModified' => get_the_modified_date('c', $post->ID),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            )
        );

        if (has_post_thumbnail($post->ID)) {
            $schema['image'] = get_the_post_thumbnail_url($post->ID, 'large');
        }

        $description = $this->get_meta_description();
        if ($description) {
            $schema['description'] = $description;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
    }

    private function output_website_schema()
    {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'description' => get_bloginfo('description'),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            )
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
    }

    public function enqueue_admin_scripts($hook)
    {
        // Only load on post edit screens
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
        }
    }

    public function add_seo_meta_boxes()
    {
        $post_types = get_post_types(array('public' => true));

        foreach ($post_types as $post_type) {
            add_meta_box(
                'sweetaddons_seo_meta',
                '🔍 SEO Settings',
                array($this, 'seo_meta_box_callback'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    public function seo_meta_box_callback($post)
    {
        wp_nonce_field('sweetaddons_seo_meta_nonce', 'sweetaddons_seo_meta_nonce');

        $title = get_post_meta($post->ID, '_sweetaddons_seo_title', true);
        $description = get_post_meta($post->ID, '_sweetaddons_seo_description', true);
        $keywords = get_post_meta($post->ID, '_sweetaddons_seo_keywords', true);
        $canonical = get_post_meta($post->ID, '_sweetaddons_seo_canonical', true);
        $og_image = get_post_meta($post->ID, '_sweetaddons_seo_og_image', true);
        $noindex = get_post_meta($post->ID, '_sweetaddons_seo_noindex', true);
        $nofollow = get_post_meta($post->ID, '_sweetaddons_seo_nofollow', true);

?>
        <table class="form-table">
            <tr>
                <th><label for="sweetaddons_seo_title">SEO Title</label></th>
                <td>
                    <input type="text" id="sweetaddons_seo_title" name="sweetaddons_seo_title" value="<?php echo esc_attr($title); ?>" class="large-text" />
                    <p class="description">Leave empty to use post title. Recommended length: 50-60 characters.</p>
                    <div id="title-length-counter" style="font-size: 12px; color: #666;"></div>
                </td>
            </tr>
            <tr>
                <th><label for="sweetaddons_seo_description">Meta Description</label></th>
                <td>
                    <textarea id="sweetaddons_seo_description" name="sweetaddons_seo_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                    <p class="description">Leave empty to auto-generate. Recommended length: 150-160 characters.</p>
                    <div id="desc-length-counter" style="font-size: 12px; color: #666;"></div>
                </td>
            </tr>
            <tr>
                <th><label for="sweetaddons_seo_keywords">Meta Keywords</label></th>
                <td>
                    <input type="text" id="sweetaddons_seo_keywords" name="sweetaddons_seo_keywords" value="<?php echo esc_attr($keywords); ?>" class="large-text" />
                    <p class="description">Comma-separated keywords. Leave empty to use post tags.</p>
                </td>
            </tr>
            <tr>
                <th><label for="sweetaddons_seo_canonical">Canonical URL</label></th>
                <td>
                    <input type="url" id="sweetaddons_seo_canonical" name="sweetaddons_seo_canonical" value="<?php echo esc_url($canonical); ?>" class="large-text" />
                    <p class="description">Leave empty to use default permalink.</p>
                </td>
            </tr>
            <tr>
                <th><label for="sweetaddons_seo_og_image">Open Graph Image</label></th>
                <td>
                    <div class="og-image-container">
                        <input type="url" id="sweetaddons_seo_og_image" name="sweetaddons_seo_og_image" value="<?php echo esc_url($og_image); ?>" class="large-text" />
                        <div class="og-image-preview" style="margin: 10px 0;">
                            <?php if ($og_image): ?>
                                <img src="<?php echo esc_url($og_image); ?>" alt="OG Image Preview" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9;" />
                            <?php else: ?>
                                <div style="width: 200px; height: 105px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px; background: #f9f9f9;">
                                    No image selected
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="og-image-buttons">
                            <button type="button" class="button" id="upload-og-image">Choose Image</button>
                            <button type="button" class="button" id="remove-og-image" <?php echo $og_image ? '' : 'style="display:none;"'; ?>>Remove Image</button>
                        </div>
                        <p class="description">Leave empty to use featured image. Recommended size: 1200x630px.</p>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Robots Meta</th>
                <td>
                    <label>
                        <input type="checkbox" name="sweetaddons_seo_noindex" value="1" <?php checked($noindex, '1'); ?> />
                        No Index (prevent search engines from indexing this page)
                    </label><br>
                    <label>
                        <input type="checkbox" name="sweetaddons_seo_nofollow" value="1" <?php checked($nofollow, '1'); ?> />
                        No Follow (prevent search engines from following links on this page)
                    </label>
                </td>
            </tr>
        </table>

        <?php
        $content = isset($post->post_content) ? $post->post_content : '';
        $title_check = $title ? $title : get_the_title($post->ID);
        $desc_check = $description ? $description : ($post->post_excerpt ? wp_trim_words($post->post_excerpt, 25) : wp_trim_words(strip_tags($content), 25));
        $keywords_list = array();
        if ($keywords) {
            foreach (explode(',', $keywords) as $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $keywords_list[] = mb_strtolower($kw);
                }
            }
        } else {
            $tags = get_the_tags($post->ID);
            if ($tags) {
                foreach ($tags as $t) {
                    $keywords_list[] = mb_strtolower($t->name);
                }
            }
        }
        $focus_kw = isset($keywords_list[0]) ? $keywords_list[0] : '';
        $score = 0;
        $title_len = mb_strlen($title_check);
        if ($title_len >= 50 && $title_len <= 60) {
            $score += 10;
        } elseif ($title_len >= 40 && $title_len <= 70) {
            $score += 7;
        }
        $desc_len = mb_strlen($desc_check);
        if ($desc_len >= 150 && $desc_len <= 160) {
            $score += 10;
        } elseif ($desc_len >= 120 && $desc_len <= 180) {
            $score += 7;
        }
        if ($focus_kw) {
            if (mb_stripos($title_check, $focus_kw) !== false) {
                $score += 10;
            }
            if (mb_stripos($desc_check, $focus_kw) !== false) {
                $score += 5;
            }
        }
        $has_og = !empty($og_image) || has_post_thumbnail($post->ID);
        if ($has_og) {
            $score += 10;
        }
        $plain = wp_strip_all_tags($content);
        $word_count = str_word_count($plain);
        if ($word_count >= 700) {
            $score += 15;
        } elseif ($word_count >= 300) {
            $score += 10;
        } elseif ($word_count >= 150) {
            $score += 5;
        }
        $internal_links = 0;
        $external_links = 0;
        $imgs = 0;
        $imgs_with_alt = 0;
        $host = parse_url(home_url(), PHP_URL_HOST);
        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            foreach ($matches[1] as $href) {
                if (strpos($href, 'http') === 0) {
                    if (strpos($href, $host) !== false) {
                        $internal_links++;
                    } else {
                        $external_links++;
                    }
                }
            }
        }
        if ($internal_links > 0) {
            $score += 5;
        }
        if ($external_links > 0) {
            $score += 5;
        }
        $headings = 0;
        if (preg_match_all('/<(h2|h3)[^>]*>/i', $content, $hm)) {
            $headings = count($hm[0]);
        }
        if ($headings >= 3) {
            $score += 10;
        } elseif ($headings >= 1) {
            $score += 5;
        }
        if (preg_match_all('/<img[^>]*>/i', $content, $im)) {
            $imgs = count($im[0]);
            foreach ($im[0] as $tag) {
                if (preg_match('/\balt=["\'][^"\']+["\']/i', $tag)) {
                    $imgs_with_alt++;
                }
            }
        }
        if ($imgs > 0) {
            if ($imgs_with_alt === $imgs) {
                $score += 10;
            } elseif ($imgs_with_alt > 0) {
                $score += 5;
            }
        }
        if ($noindex) {
            $score -= 20;
        }
        if ($score < 0) {
            $score = 0;
        }
        if ($score > 100) {
            $score = 100;
        }
        $grade = 'Needs Work';
        $color = '#d63638';
        if ($score >= 80) {
            $grade = 'Excellent';
            $color = '#00a32a';
        } elseif ($score >= 60) {
            $grade = 'Good';
            $color = '#2271b1';
        } elseif ($score >= 40) {
            $grade = 'Fair';
            $color = '#ff922b';
        }
        ?>
        <div style="margin-top: 16px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background: #fff;">
            <div style="font-weight:600; margin-bottom:8px;">SEO Score</div>
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:72px; height:72px; border-radius:50%; background: #f0f0f1; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700; color: <?php echo esc_attr($color); ?>;"><?php echo esc_html($score); ?></div>
                <div>
                    <div style="font-weight:600; color: <?php echo esc_attr($color); ?>;"><?php echo esc_html($grade); ?></div>
                    <div style="font-size:12px; color:#666;">Title <?php echo esc_html($title_len); ?> chars, Description <?php echo esc_html($desc_len); ?> chars, Words <?php echo esc_html($word_count); ?>, Internal links <?php echo esc_html($internal_links); ?>, External links <?php echo esc_html($external_links); ?>, Headings <?php echo esc_html($headings); ?>, Images alt <?php echo esc_html($imgs_with_alt); ?>/<?php echo esc_html($imgs); ?></div>
                </div>
            </div>
        </div>

        <script>
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

                const titleInput = $('#sweetaddons_seo_title');
                const titleCounter = $('#title-length-counter');
                const descInput = $('#sweetaddons_seo_description');
                const descCounter = $('#desc-length-counter');

                titleInput.on('input', function() {
                    updateCounter(titleInput, titleCounter, 60);
                });

                descInput.on('input', function() {
                    updateCounter(descInput, descCounter, 160);
                });

                // Initial count
                updateCounter(titleInput, titleCounter, 60);
                updateCounter(descInput, descCounter, 160);

                // OG Image preview update
                function updateOGImagePreview(imageUrl) {
                    const previewContainer = $('.og-image-preview');
                    const removeButton = $('#remove-og-image');

                    if (imageUrl) {
                        previewContainer.html('<img src="' + imageUrl + '" alt="OG Image Preview" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: #f9f9f9;" />');
                        removeButton.show();
                    } else {
                        previewContainer.html('<div style="width: 200px; height: 105px; border: 2px dashed #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px; background: #f9f9f9;">No image selected</div>');
                        removeButton.hide();
                    }
                }

                // Media uploader for OG image
                $('#upload-og-image').click(function(e) {
                    e.preventDefault();

                    // Check if wp.media exists
                    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                        alert('WordPress media uploader is not available. Please make sure you are on a post edit screen.');
                        return;
                    }

                    const mediaUploader = wp.media({
                        title: 'Choose Open Graph Image',
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
                        $('#sweetaddons_seo_og_image').val(attachment.url);
                        updateOGImagePreview(attachment.url);
                    });

                    mediaUploader.open();
                });

                // Remove OG image
                $('#remove-og-image').click(function(e) {
                    e.preventDefault();
                    $('#sweetaddons_seo_og_image').val('');
                    updateOGImagePreview('');
                });

                // Manual URL input change
                $('#sweetaddons_seo_og_image').on('input change', function() {
                    updateOGImagePreview($(this).val());
                });
            });
        </script>
<?php
    }

    public function save_seo_meta_data($post_id)
    {
        if (!isset($_POST['sweetaddons_seo_meta_nonce']) || !wp_verify_nonce($_POST['sweetaddons_seo_meta_nonce'], 'sweetaddons_seo_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'sweetaddons_seo_title' => '_sweetaddons_seo_title',
            'sweetaddons_seo_description' => '_sweetaddons_seo_description',
            'sweetaddons_seo_keywords' => '_sweetaddons_seo_keywords',
            'sweetaddons_seo_canonical' => '_sweetaddons_seo_canonical',
            'sweetaddons_seo_og_image' => '_sweetaddons_seo_og_image',
            'sweetaddons_seo_noindex' => '_sweetaddons_seo_noindex',
            'sweetaddons_seo_nofollow' => '_sweetaddons_seo_nofollow'
        );

        foreach ($fields as $field => $meta_key) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
        $this->clear_sitemap_caches();
    }

    public function disable_trailing_slash_redirect($redirect_url)
    {
        if (get_query_var('sweetaddons_sitemap')) {
            return false;
        }
        return $redirect_url;
    }

    public function custom_title($title, $sep)
    {
        if (is_feed()) {
            return $title;
        }

        return $this->get_page_title();
    }

    public function custom_document_title_parts($title)
    {
        $title['title'] = $this->get_page_title();
        return $title;
    }

    public function handle_sitemap_request()
    {
        $qv = get_query_var('sweetaddons_sitemap');
        $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
        if ($enabled !== '1') {
            return;
        }
        if ($qv === 'index') {
            $this->generate_xml_sitemap_index();
            exit;
        } elseif (in_array($qv, array('posts', 'pages', 'categories', 'tags'), true)) {
            $this->generate_xml_sitemap_type($qv);
            exit;
        }
    }

    private function generate_xml_sitemap_index()
    {
        $last_modified = $this->get_latest_modified_timestamp();
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . home_url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $per_page = 2000;
        $totals = array(
            'posts' => (int) wp_count_posts('post')->publish,
            'pages' => (int) wp_count_posts('page')->publish,
            'categories' => (int) wp_count_terms(array('taxonomy' => 'category', 'hide_empty' => true)),
            'tags' => (int) wp_count_terms(array('taxonomy' => 'post_tag', 'hide_empty' => true)),
        );
        foreach ($totals as $type => $total) {
            $pages = max(1, (int) ceil($total / $per_page));
            if ($pages === 1) {
                $loc = home_url('/sitemap-' . $type . '.xml');
                $xml .= '<sitemap>' . "\n";
                $xml .= '<loc>' . esc_url($loc) . '</loc>' . "\n";
                $xml .= '<lastmod>' . date('c', $last_modified ?: time()) . '</lastmod>' . "\n";
                $xml .= '</sitemap>' . "\n";
            } else {
                for ($i = 1; $i <= $pages; $i++) {
                    $loc = home_url('/sitemap-' . $type . '-' . $i . '.xml');
                    $xml .= '<sitemap>' . "\n";
                    $xml .= '<loc>' . esc_url($loc) . '</loc>' . "\n";
                    $xml .= '<lastmod>' . date('c', $last_modified ?: time()) . '</lastmod>' . "\n";
                    $xml .= '</sitemap>' . "\n";
                }
            }
        }
        $xml .= '</sitemapindex>';
        set_transient($this->get_sitemap_cache_key('index'), array(
            'xml' => $xml,
            'last_modified' => $last_modified ?: time()
        ), 12 * HOUR_IN_SECONDS);
        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age=43200');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', ($last_modified ?: time())) . ' GMT');
        $this->maybe_output_304($last_modified ?: time());
        echo $xml;
    }

    private function generate_xml_sitemap_type($type)
    {
        $page = (int) get_query_var('sweetaddons_sitemap_page');
        if ($page < 1) {
            $page = 1;
        }
        $suffix = $type . '-' . $page;
        $cache = get_transient($this->get_sitemap_cache_key($suffix));
        if (is_array($cache) && isset($cache['xml'], $cache['last_modified'])) {
            header('Content-Type: application/xml; charset=utf-8');
            header('Cache-Control: public, max-age=43200');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cache['last_modified']) . ' GMT');
            $this->maybe_output_304($cache['last_modified']);
            echo $cache['xml'];
            return;
        }

        $last_modified = $this->get_latest_modified_timestamp();
        $xml = '';
        $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?xml-stylesheet type="text/xsl" href="' . home_url('/sitemap.xsl') . '"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $per_page = 2000;

        if ($type === 'posts' || $type === 'pages') {
            $post_type = $type === 'posts' ? 'post' : 'page';
            $ids = get_posts(array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => $per_page,
                'offset' => ($page - 1) * $per_page,
                'fields' => 'ids',
                'no_found_rows' => true
            ));
            foreach ($ids as $id) {
                $noindex = get_post_meta($id, '_sweetaddons_seo_noindex', true);
                if ($noindex) {
                    continue;
                }
                $permalink = get_permalink($id);
                $modified = get_post_field('post_modified', $id);
                $xml .= '<url>' . "\n";
                $xml .= '<loc>' . esc_url($permalink) . '</loc>' . "\n";
                $xml .= '<lastmod>' . date('c', strtotime($modified)) . '</lastmod>' . "\n";
                $xml .= '<changefreq>monthly</changefreq>' . "\n";
                $xml .= ($type === 'posts' ? '<priority>0.8</priority>' : '<priority>0.6</priority>') . "\n";
                $xml .= '</url>' . "\n";
            }
        } elseif ($type === 'categories') {
            $terms = get_terms(array('taxonomy' => 'category', 'hide_empty' => true, 'number' => $per_page, 'offset' => ($page - 1) * $per_page));
            foreach ($terms as $term) {
                $xml .= '<url>' . "\n";
                $xml .= '<loc>' . esc_url(get_term_link($term)) . '</loc>' . "\n";
                $xml .= '<changefreq>weekly</changefreq>' . "\n";
                $xml .= '<priority>0.5</priority>' . "\n";
                $xml .= '</url>' . "\n";
            }
        } elseif ($type === 'tags') {
            $terms = get_terms(array('taxonomy' => 'post_tag', 'hide_empty' => true, 'number' => $per_page, 'offset' => ($page - 1) * $per_page));
            foreach ($terms as $term) {
                $xml .= '<url>' . "\n";
                $xml .= '<loc>' . esc_url(get_term_link($term)) . '</loc>' . "\n";
                $xml .= '<changefreq>weekly</changefreq>' . "\n";
                $xml .= '<priority>0.4</priority>' . "\n";
                $xml .= '</url>' . "\n";
            }
        }

        $xml .= '</urlset>';

        set_transient($this->get_sitemap_cache_key($suffix), array(
            'xml' => $xml,
            'last_modified' => $last_modified ?: time()
        ), 12 * HOUR_IN_SECONDS);

        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age=43200');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', ($last_modified ?: time())) . ' GMT');
        $this->maybe_output_304($last_modified ?: time());
        echo $xml;
    }

    private function generate_xsl_stylesheet()
    {
        header('Content-Type: text/xml; charset=utf-8');
        header('Cache-Control: public, max-age=43200');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>XML Sitemap</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style type="text/css">
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                        color: #333;
                        max-width: 75rem;
                        margin: 0 auto;
                        padding: 2rem;
                    }
                    a {
                        color: #0073aa;
                        text-decoration: none;
                    }
                    a:hover {
                        text-decoration: underline;
                    }
                    table {
                        border: none;
                        border-collapse: collapse;
                        width: 100%;
                    }
                    th {
                        text-align: left;
                        padding: 1rem .5rem;
                        border-bottom: 1px solid #ccc;
                    }
                    td {
                        padding: 1rem .5rem;
                        border-bottom: 1px solid #eee;
                        font-size: 14px;
                    }
                    .header {
                        padding-bottom: 1.5rem;
                        border-bottom: 1px solid #ccc;
                        margin-bottom: 1.5rem;
                    }
                    h1 {
                        margin: 0;
                        font-size: 24px;
                        font-weight: normal;
                    }
                    .desc {
                        color: #666;
                        margin-top: 5px;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>XML Sitemap</h1>
                    <p class="desc">
                        This is an XML Sitemap generated by Sweet Addons to make your content more visible for search engines.
                    </p>
                </div>
                <div id="content">
                    <xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &gt; 0">
                        <p class="desc">This XML Sitemap Index file contains <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> sitemaps.</p>
                        <table cellpadding="5">
                            <thead>
                            <tr style="border-bottom:1px black solid;">
                                <th width="75%">Sitemap</th>
                                <th width="25%">Last Modified</th>
                            </tr>
                            </thead>
                            <tbody>
                            <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                <tr>
                                    <td>
                                        <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                    </td>
                                    <td>
                                        <xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(\' \', substring(sitemap:lastmod,12,5)))"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:if>
                    
                    <xsl:if test="count(sitemap:urlset/sitemap:url) &gt; 0">
                        <table cellpadding="5">
                            <thead>
                            <tr style="border-bottom:1px black solid;">
                                <th width="60%">URL</th>
                                <th width="15%">Priority</th>
                                <th width="15%">Change Frequency</th>
                                <th width="25%">Last Modified</th>
                            </tr>
                            </thead>
                            <tbody>
                            <xsl:for-each select="sitemap:urlset/sitemap:url">
                                <tr>
                                    <td>
                                        <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                    </td>
                                    <td>
                                        <xsl:value-of select="concat(sitemap:priority*100,\'%\')"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="sitemap:changefreq"/>
                                    </td>
                                    <td>
                                        <xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(\' \', substring(sitemap:lastmod,12,5)))"/>
                                    </td>
                                </tr>
                            </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:if>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>';
    }

    private function get_latest_modified_timestamp()
    {
        $q = new WP_Query(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'orderby' => 'modified',
            'order' => 'DESC',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'no_found_rows' => true
        ));
        if ($q->have_posts()) {
            $id = $q->posts[0];
            $modified = get_post_field('post_modified', $id);
            return strtotime($modified);
        }
        return time();
    }

    private function get_sitemap_cache_key($suffix = 'index')
    {
        return 'sweetaddons_sitemap_xml_cache_' . $suffix;
    }

    private function maybe_output_304($last_modified_ts)
    {
        $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
        if ($if_modified_since && $last_modified_ts && $if_modified_since >= $last_modified_ts) {
            status_header(304);
            exit;
        }
    }

    public function register_sitemap_rewrite()
    {
        // Note: We handle wp-sitemap.xml redirect manually in template_redirect to avoid rewrite rule overhead/conflicts

        add_rewrite_rule('^sitemap\.xml/?$', 'index.php?sweetaddons_sitemap=index', 'top');
        add_rewrite_rule('^sitemap\.xsl/?$', 'index.php?sweetaddons_sitemap=xsl', 'top');
        add_rewrite_rule('^sitemap-posts\.xml/?$', 'index.php?sweetaddons_sitemap=posts', 'top');
        add_rewrite_rule('^sitemap-pages\.xml/?$', 'index.php?sweetaddons_sitemap=pages', 'top');
        add_rewrite_rule('^sitemap-categories\.xml/?$', 'index.php?sweetaddons_sitemap=categories', 'top');
        add_rewrite_rule('^sitemap-tags\.xml/?$', 'index.php?sweetaddons_sitemap=tags', 'top');
        add_rewrite_rule('^sitemap-posts-([0-9]+)\.xml/?$', 'index.php?sweetaddons_sitemap=posts&sweetaddons_sitemap_page=$matches[1]', 'top');
        add_rewrite_rule('^sitemap-pages-([0-9]+)\.xml/?$', 'index.php?sweetaddons_sitemap=pages&sweetaddons_sitemap_page=$matches[1]', 'top');
        add_rewrite_rule('^sitemap-categories-([0-9]+)\.xml/?$', 'index.php?sweetaddons_sitemap=categories&sweetaddons_sitemap_page=$matches[1]', 'top');
        add_rewrite_rule('^sitemap-tags-([0-9]+)\.xml/?$', 'index.php?sweetaddons_sitemap=tags&sweetaddons_sitemap_page=$matches[1]', 'top');
        
        $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
        $initialized = get_option('sweetaddons_sitemap_rules_initialized');
        
        // Bump version to 4 to ensure clean flush of old problematic rules
        if ($enabled === '1' && $initialized != '4') {
            flush_rewrite_rules();
            update_option('sweetaddons_sitemap_rules_initialized', '4');
        }
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'sweetaddons_sitemap';
        $vars[] = 'sweetaddons_sitemap_page';
        $vars[] = 'sweetaddons_sitemap_redirect';
        return $vars;
    }

    public function template_redirect_sitemap()
    {
        $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
        if ($enabled !== '1') {
            return;
        }

        // Redirect wp-sitemap.xml (Manual check)
        // Check for direct access to wp-sitemap.xml in URL
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($request_uri, '/wp-sitemap.xml') !== false) {
             wp_redirect(home_url('/sitemap.xml'), 301);
             exit;
        }

        // Redirect wp-sitemap.xml (Fallback for query var if cached)
        if (get_query_var('sweetaddons_sitemap_redirect')) {
            wp_redirect(home_url('/sitemap.xml'), 301);
            exit;
        }

        $qv = get_query_var('sweetaddons_sitemap');
        if ($qv === 'xsl') {
            $this->generate_xsl_stylesheet();
            exit;
        }
        if ($qv === 'index') {
            $cache = get_transient($this->get_sitemap_cache_key('index'));
            if (is_array($cache) && isset($cache['xml'], $cache['last_modified'])) {
                header('Content-Type: application/xml; charset=utf-8');
                header('Cache-Control: public, max-age=43200');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cache['last_modified']) . ' GMT');
                $this->maybe_output_304($cache['last_modified']);
                echo $cache['xml'];
                exit;
            }
            $this->generate_xml_sitemap_index();
            exit;
        } elseif (in_array($qv, array('posts', 'pages', 'categories', 'tags'), true)) {
            $this->generate_xml_sitemap_type($qv);
            exit;
        }
    }

    private function clear_sitemap_caches()
    {
        delete_transient($this->get_sitemap_cache_key('index'));
        foreach (array('posts', 'pages', 'categories', 'tags') as $t) {
            delete_transient($this->get_sitemap_cache_key($t));
        }
    }
}
