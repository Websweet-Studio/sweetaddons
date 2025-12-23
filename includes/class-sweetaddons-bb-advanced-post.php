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
            $include_cat = isset($s->include_categories) ? trim($s->include_categories) : '';
            $exclude_cat = isset($s->exclude_categories) ? trim($s->exclude_categories) : '';
            $paged = max(1, get_query_var('paged') ? (int)get_query_var('paged') : (isset($_GET['paged']) ? (int)$_GET['paged'] : 1));

            $tax_query = array();
            if ($post_type === 'post') {
                if ($include_cat !== '') {
                    $ids = array_map('intval', array_filter(array_map('trim', explode(',', $include_cat))));
                    if (!empty($ids)) {
                        $tax_query[] = array(
                            'taxonomy' => 'category',
                            'field' => 'term_id',
                            'terms' => $ids,
                            'operator' => 'IN',
                        );
                    }
                }
                if ($exclude_cat !== '') {
                    $ids = array_map('intval', array_filter(array_map('trim', explode(',', $exclude_cat))));
                    if (!empty($ids)) {
                        $tax_query[] = array(
                            'taxonomy' => 'category',
                            'field' => 'term_id',
                            'terms' => $ids,
                            'operator' => 'NOT IN',
                        );
                    }
                }
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
                    )
                ),
                'display' => array(
                    'title' => 'Display',
                    'fields' => array(
                        'show_image' => array(
                            'type' => 'checkbox',
                            'label' => 'Show Featured Image',
                            'default' => '1'
                        ),
                        'show_title' => array(
                            'type' => 'checkbox',
                            'label' => 'Show Title',
                            'default' => '1'
                        ),
                        'show_excerpt' => array(
                            'type' => 'checkbox',
                            'label' => 'Show Excerpt',
                            'default' => '1'
                        ),
                        'excerpt_length' => array(
                            'type' => 'text',
                            'label' => 'Excerpt Length (words)',
                            'default' => '20'
                        ),
                        'show_meta' => array(
                            'type' => 'checkbox',
                            'label' => 'Show Meta',
                            'default' => '1'
                        ),
                        'show_read_more' => array(
                            'type' => 'checkbox',
                            'label' => 'Show Read More',
                            'default' => '1'
                        ),
                    )
                ),
                'custom' => array(
                    'title' => 'Custom HTML',
                    'fields' => array(
                        'custom_layout_html' => array(
                            'type' => 'textarea',
                            'rows' => '8',
                            'label' => 'Custom Item HTML',
                            'description' => 'Placeholders: {link}, {image}, {image_url}, {title}, {excerpt}, {date}, {author}, {read_more}, {meta}',
                            'default' => ''
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
                        'include_categories' => array(
                            'type' => 'text',
                            'label' => 'Include Categories (IDs, comma separated)',
                            'default' => ''
                        ),
                        'exclude_categories' => array(
                            'type' => 'text',
                            'label' => 'Exclude Categories (IDs, comma separated)',
                            'default' => ''
                        )
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
});
