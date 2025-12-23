<?php

add_action('init', function () {
    if (!class_exists('FLBuilderModule') || !class_exists('FLBuilder')) {
        return;
    }

    class Sweetaddons_BB_Advanced_Post extends FLBuilderModule
    {
        public function __construct()
        {
            parent::__construct(array(
                'name' => 'Advanced Posts',
                'description' => 'Advanced post grid/list',
                'category' => 'Sweetaddons',
                'dir' => plugin_dir_path(__FILE__) . 'bb-advanced-post/',
                'url' => plugin_dir_url(__FILE__) . 'bb-advanced-post/',
            ));
        }

        public function render()
        {
            $s = $this->settings;
            $post_type = isset($s->post_type) ? $s->post_type : 'post';
            $per_page = isset($s->posts_per_page) ? (int)$s->posts_per_page : 6;
            $orderby = isset($s->orderby) ? $s->orderby : 'date';
            $order = isset($s->order) ? $s->order : 'DESC';
            $layout = isset($s->layout) ? $s->layout : 'grid';
            $columns = isset($s->columns) ? (int)$s->columns : 3;
            $show_image = !empty($s->show_image);
            $show_title = !empty($s->show_title);
            $show_excerpt = !empty($s->show_excerpt);
            $excerpt_length = isset($s->excerpt_length) ? (int)$s->excerpt_length : 20;
            $show_meta = !empty($s->show_meta);
            $show_read_more = !empty($s->show_read_more);
            $pagination = isset($s->pagination) ? $s->pagination : 'none';
            $paged = max(1, get_query_var('paged') ? (int)get_query_var('paged') : (isset($_GET['paged']) ? (int)$_GET['paged'] : 1));
            $taxonomy = isset($s->taxonomy) ? trim($s->taxonomy) : '';
            $term = isset($s->term) ? (int)$s->term : 0;

            $tax_query = array();
            if ($taxonomy !== '' && $term > 0) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => array($term),
                    'operator' => 'IN',
                );
            }

            $args = array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => $per_page,
                'orderby' => $orderby,
                'order' => $order,
                'paged' => $pagination === 'numbers' ? $paged : 1,
            );
            if (!empty($tax_query)) {
                $args['tax_query'] = $tax_query;
            }
            $q = new WP_Query($args);

            $grid_style = '';
            if ($layout === 'grid') {
                $grid_style = 'display:grid;grid-template-columns:repeat(' . max(1, $columns) . ',1fr);gap:16px;';
            }

            echo '<div class="sweetaddons-bb-adv-post" style="' . esc_attr($grid_style) . '">';
            if ($q->have_posts()) {
                while ($q->have_posts()) {
                    $q->the_post();
                    $link = get_permalink();
                    echo '<article class="sap-card" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">';
                    if ($show_image) {
                        if (has_post_thumbnail()) {
                            $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                            if (!empty($src[0])) {
                                echo '<a href="' . esc_url($link) . '" style="display:block;"><img src="' . esc_url($src[0]) . '" alt="' . esc_attr(get_the_title()) . '" style="width:100%;height:auto;display:block;"></a>';
                            }
                        }
                    }
                    echo '<div class="sap-body" style="padding:14px;">';
                    if ($show_title) {
                        echo '<h3 class="sap-title" style="margin:0 0 8px 0;font-size:18px;line-height:1.3;"><a href="' . esc_url($link) . '" style="text-decoration:none;color:#1f2937;">' . esc_html(get_the_title()) . '</a></h3>';
                    }
                    if ($show_meta) {
                        $date = get_the_date();
                        $author = get_the_author();
                        echo '<div class="sap-meta" style="font-size:12px;color:#6b7280;margin-bottom:8px;">' . esc_html($date) . ' · ' . esc_html($author) . '</div>';
                    }
                    if ($show_excerpt) {
                        $excerpt = get_the_excerpt();
                        $excerpt = wp_trim_words($excerpt, max(1, $excerpt_length));
                        echo '<div class="sap-excerpt" style="font-size:14px;color:#374151;margin-bottom:10px;">' . esc_html($excerpt) . '</div>';
                    }
                    if ($show_read_more) {
                        echo '<a class="sap-read-more" href="' . esc_url($link) . '" style="display:inline-block;font-size:13px;color:#2271b1;">Baca selengkapnya</a>';
                    }
                    echo '</div>';
                    echo '</article>';
                }
                wp_reset_postdata();
            } else {
                echo '<div style="grid-column:1/-1;color:#6b7280;">Tidak ada post ditemukan.</div>';
            }
            echo '</div>';

            if ($pagination === 'numbers' && $q->max_num_pages > 1) {
                $big = 999999999;
                $links = paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, $paged),
                    'total' => $q->max_num_pages,
                    'type' => 'array'
                ));
                if (is_array($links)) {
                    echo '<div class="sap-pagination" style="margin-top:16px;display:flex;flex-wrap:wrap;gap:8px;">';
                    foreach ($links as $l) {
                        echo '<span class="sap-page-link" style="display:inline-block;padding:6px 10px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;">' . $l . '</span>';
                    }
                    echo '</div>';
                }
            }
        }
    }

    $taxonomy_options = array();
    $taxes = get_taxonomies(array('public' => true), 'objects');
    if (is_array($taxes)) {
        foreach ($taxes as $slug => $tax) {
            $taxonomy_options[$slug] = isset($tax->labels->name) ? $tax->labels->name : $slug;
        }
    }
    $default_terms = array();
    $terms = get_terms(array('taxonomy' => 'category', 'hide_empty' => false));
    if (!is_wp_error($terms) && is_array($terms)) {
        foreach ($terms as $t) {
            $default_terms[$t->term_id] = $t->name;
        }
    }

    FLBuilder::register_module('Sweetaddons_BB_Advanced_Post', array(
        'General' => array(
            'title' => 'General',
            'sections' => array(
                'layout' => array(
                    'title' => 'Layout',
                    'fields' => array(
                        'layout' => array(
                            'type' => 'select',
                            'label' => 'Layout',
                            'default' => 'grid',
                            'options' => array(
                                'grid' => 'Grid',
                                'list' => 'List'
                            ),
                            'toggle' => array(
                                'grid' => array(
                                    'fields' => array('columns', 'columns_mobile', 'columns_tablet')
                                ),
                                'list' => array(
                                    'fields' => array()
                                )
                            )
                        ),
                        'columns' => array(
                            'type' => 'select',
                            'label' => 'Columns',
                            'default' => '3',
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6'
                            )
                        ),
                        'columns_mobile' => array(
                            'type' => 'select',
                            'label' => 'Columns (Mobile)',
                            'default' => '1',
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6'
                            )
                        ),
                        'columns_tablet' => array(
                            'type' => 'select',
                            'label' => 'Columns (Tablet)',
                            'default' => '2',
                            'options' => array(
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6'
                            )
                        ),
                    )
                ),
                'custom' => array(
                    'title' => 'Custom HTML',
                    'fields' => array(
                        'custom_layout_html' => array(
                            'type' => 'code',
                            'editor' => 'html',
                            'rows' => '18',
                            'label' => 'Custom Item HTML',
                            'description' => 'Placeholders: [link], [image], [image_url], [title], [excerpt], [date], [author], [read_more], [meta]. Tanggal bisa diformat: [date:Y-m-d], [date:F j, Y], dll.',
                            'default' => '<article class="sap-card card h-100">' . "\n"
                                . '[image]' . "\n"
                                . '<div class="sap-body card-body">' . "\n"
                                . '<h3 class="card-title"><a href="[link]" class="stretched-link text-decoration-none">[title]</a></h3>' . "\n"
                                . '[meta]' . "\n"
                                . '<p class="card-text">[excerpt]</p>' . "\n"
                                . '[read_more]' . "\n"
                                . '</div>' . "\n"
                                . '</article>'
                        )
                    )
                )
            )
        ),
        'Query' => array(
            'title' => 'Query',
            'sections' => array(
                'query' => array(
                    'title' => 'Query',
                    'fields' => array(
                        'post_type' => array(
                            'type' => 'select',
                            'label' => 'Post Type',
                            'default' => 'post',
                            'options' => array(
                                'post' => 'Post',
                                'page' => 'Page'
                            )
                        ),
                        'posts_per_page' => array(
                            'type' => 'text',
                            'label' => 'Posts Per Page',
                            'default' => '6'
                        ),
                        'orderby' => array(
                            'type' => 'select',
                            'label' => 'Order By',
                            'default' => 'date',
                            'options' => array(
                                'date' => 'Date',
                                'title' => 'Title',
                                'modified' => 'Modified',
                                'comment_count' => 'Comment Count',
                                'rand' => 'Random'
                            )
                        ),
                        'order' => array(
                            'type' => 'select',
                            'label' => 'Order',
                            'default' => 'DESC',
                            'options' => array(
                                'DESC' => 'DESC',
                                'ASC' => 'ASC'
                            )
                        ),
                        'taxonomy' => array(
                            'type' => 'select',
                            'label' => 'Taxonomy',
                            'default' => 'category',
                            'options' => $taxonomy_options
                        ),
                        'term' => array(
                            'type' => 'select',
                            'label' => 'Term',
                            'default' => '0',
                            'options' => array('all' => 'All', '0' => '— Select —') + $default_terms
                        ),
                    )
                )
            )
        ),
        'Pagination' => array(
            'title' => 'Pagination',
            'sections' => array(
                'pagination' => array(
                    'title' => 'Pagination',
                    'fields' => array(
                        'pagination' => array(
                            'type' => 'select',
                            'label' => 'Pagination',
                            'default' => 'none',
                            'options' => array(
                                'none' => 'None',
                                'numbers' => 'Numbers'
                            )
                        )
                    )
                )
            )
        )
    ));

    add_action('wp_enqueue_scripts', function () {
        if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_active()) {
            wp_enqueue_style(
                'sap-bb-adv-post-settings',
                plugin_dir_url(__FILE__) . 'bb-advanced-post/includes/settings.css',
                array(),
                defined('SWEETADDONS_VERSION') ? SWEETADDONS_VERSION : '1.0.0'
            );
            wp_enqueue_script(
                'sap-bb-adv-post-settings',
                plugin_dir_url(__FILE__) . 'bb-advanced-post/includes/settings.js',
                array('jquery'),
                defined('SWEETADDONS_VERSION') ? SWEETADDONS_VERSION : '1.0.0',
                true
            );
        }
    });
});
