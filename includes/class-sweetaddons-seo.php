<?php

/**
 * SEO functionality for Sweet Addons
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    Sweetaddons
 * @subpackage Sweetaddons/includes
 */

class Sweetaddons_SEO
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'output_meta_tags'), 1);
        add_action('wp_head', array($this, 'output_og_tags'), 2);
        add_action('add_meta_boxes', array($this, 'add_seo_meta_boxes'));
        add_action('save_post', array($this, 'save_seo_meta_data'));
        add_action('init', array($this, 'handle_sitemap_request'));
        add_action('init', array($this, 'register_sitemap_rewrite'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'template_redirect_sitemap'));
        add_filter('wp_title', array($this, 'custom_title'), 10, 2);
        add_filter('document_title_parts', array($this, 'custom_document_title_parts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
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
            return single_cat_title('', false);
        }

        if (is_tag()) {
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

            // Auto-generate from excerpt or content
            if ($post->post_excerpt) {
                return wp_trim_words($post->post_excerpt, 25);
            }

            return wp_trim_words(strip_tags($post->post_content), 25);
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
                return wp_trim_words(strip_tags($cat_desc), 25);
            }
        }

        if (is_tag()) {
            $tag_desc = tag_description();
            if ($tag_desc) {
                return wp_trim_words(strip_tags($tag_desc), 25);
            }
        }

        return '';
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
        delete_transient($this->get_sitemap_cache_key());
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
        if ((isset($_GET['sweetaddons_sitemap']) && $_GET['sweetaddons_sitemap'] === 'xml') || ($qv === 'xml')) {
            $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
            if ($enabled === '1') {
                $this->generate_xml_sitemap();
                exit;
            }
        }
    }

    private function generate_xml_sitemap()
    {
        $cache = get_transient($this->get_sitemap_cache_key());
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
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= '<url>' . "\n";
        $xml .= '<loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        $xml .= '<lastmod>' . date('c', $last_modified ?: time()) . '</lastmod>' . "\n";
        $xml .= '<changefreq>daily</changefreq>' . "\n";
        $xml .= '<priority>1.0</priority>' . "\n";
        $xml .= '</url>' . "\n";

        // Posts and pages (lightweight IDs only)
        $ids = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
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
            $type = get_post_type($id);

            $xml .= '<url>' . "\n";
            $xml .= '<loc>' . esc_url($permalink) . '</loc>' . "\n";
            $xml .= '<lastmod>' . date('c', strtotime($modified)) . '</lastmod>' . "\n";
            $xml .= '<changefreq>monthly</changefreq>' . "\n";
            $xml .= ($type === 'post' ? '<priority>0.8</priority>' : '<priority>0.6</priority>') . "\n";
            $xml .= '</url>' . "\n";
        }

        $xml .= '</urlset>';

        set_transient($this->get_sitemap_cache_key(), array(
            'xml' => $xml,
            'last_modified' => $last_modified ?: time()
        ), 12 * HOUR_IN_SECONDS);

        header('Content-Type: application/xml; charset=utf-8');
        header('Cache-Control: public, max-age=43200');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', ($last_modified ?: time())) . ' GMT');
        $this->maybe_output_304($last_modified ?: time());
        echo $xml;
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

    private function get_sitemap_cache_key()
    {
        return 'sweetaddons_sitemap_xml_cache';
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
        add_rewrite_rule('^sitemap\.xml/?$', 'index.php?sweetaddons_sitemap=xml', 'top');
        $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
        $initialized = get_option('sweetaddons_sitemap_rules_initialized');
        if ($enabled === '1' && $initialized !== '2') {
            flush_rewrite_rules();
            update_option('sweetaddons_sitemap_rules_initialized', 2);
        }
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'sweetaddons_sitemap';
        return $vars;
    }

    public function template_redirect_sitemap()
    {
        $enabled = get_option('sweetaddons_seo_enable_sitemap', '1');
        if ($enabled !== '1') {
            return;
        }
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $path = strtok($request_uri, '?');
        if (rtrim($path, '/') === '/sitemap.xml') {
            $this->generate_xml_sitemap();
            exit;
        }
    }
}
