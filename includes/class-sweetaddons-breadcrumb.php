<?php

/**
 * Breadcrumb functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_Breadcrumb
{
    public function __construct()
    {
        add_shortcode('breadcrumb', array($this, 'breadcrumb_shortcode'));
    }

    public function breadcrumb_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'separator' => '/', // separator between breadcrumb items
            'home_text' => 'Home', // text for home link
            'show_home' => 'true', // show home link or not
            'show_current' => 'true', // show current page or not
            'style' => 'default' // default, minimal, arrow
        ), $atts, 'breadcrumb');

        if (!is_front_page()) {
            ob_start();

            $style_class = 'sweetaddons-breadcrumb-' . sanitize_html_class($atts['style']);
            echo '<nav class="sweetaddons-breadcrumb ' . $style_class . '" aria-label="Breadcrumb">';
            echo '<ol class="breadcrumb-list">';

            // Home link
            if ($atts['show_home'] === 'true') {
                echo '<li class="breadcrumb-item breadcrumb-home">';
                echo '<a href="' . home_url('/') . '">' . esc_html($atts['home_text']) . '</a>';
                echo '</li>';
            }

            if (is_category()) {
                $this->render_category_breadcrumb($atts);
            } elseif (is_tag()) {
                $this->render_tag_breadcrumb($atts);
            } elseif (is_author()) {
                $this->render_author_breadcrumb($atts);
            } elseif (is_date()) {
                $this->render_date_breadcrumb($atts);
            } elseif (is_search()) {
                $this->render_search_breadcrumb($atts);
            } elseif (is_404()) {
                $this->render_404_breadcrumb($atts);
            } elseif (is_single()) {
                $this->render_single_breadcrumb($atts);
            } elseif (is_page()) {
                $this->render_page_breadcrumb($atts);
            } elseif (is_home()) {
                $this->render_blog_breadcrumb($atts);
            }

            echo '</ol>';
            echo '</nav>';

            // Add CSS if not already added
            if (!wp_style_is('sweetaddons-breadcrumb', 'enqueued')) {
                $this->add_breadcrumb_styles();
            }

            return ob_get_clean();
        }

        return '';
    }

    private function render_category_breadcrumb($atts)
    {
        $category = get_queried_object();

        // Show parent categories
        if ($category->parent != 0) {
            $parent_categories = array();
            $parent = $category->parent;

            while ($parent) {
                $parent_cat = get_category($parent);
                $parent_categories[] = $parent_cat;
                $parent = $parent_cat->parent;
            }

            $parent_categories = array_reverse($parent_categories);

            foreach ($parent_categories as $parent_cat) {
                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-category">';
                echo '<a href="' . get_category_link($parent_cat->term_id) . '">' . esc_html($parent_cat->name) . '</a>';
                echo '</li>';
            }
        }

        // Current category
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . esc_html($category->name) . '</li>';
        }
    }

    private function render_tag_breadcrumb($atts)
    {
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . esc_html(single_tag_title('', false)) . '</li>';
        }
    }

    private function render_author_breadcrumb($atts)
    {
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . esc_html(get_the_author()) . '</li>';
        }
    }

    private function render_date_breadcrumb($atts)
    {
        if (is_year()) {
            if ($atts['show_current'] === 'true') {
                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . get_the_date('Y') . '</li>';
            }
        } elseif (is_month()) {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-date">';
            echo '<a href="' . get_year_link(get_the_date('Y')) . '">' . get_the_date('Y') . '</a>';
            echo '</li>';

            if ($atts['show_current'] === 'true') {
                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . get_the_date('F') . '</li>';
            }
        } elseif (is_day()) {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-date">';
            echo '<a href="' . get_year_link(get_the_date('Y')) . '">' . get_the_date('Y') . '</a>';
            echo '</li>';

            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-date">';
            echo '<a href="' . get_month_link(get_the_date('Y'), get_the_date('m')) . '">' . get_the_date('F') . '</a>';
            echo '</li>';

            if ($atts['show_current'] === 'true') {
                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . get_the_date('j') . '</li>';
            }
        }
    }

    private function render_search_breadcrumb($atts)
    {
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">Search: ' . esc_html(get_search_query()) . '</li>';
        }
    }

    private function render_404_breadcrumb($atts)
    {
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">404 - Page Not Found</li>';
        }
    }

    private function render_single_breadcrumb($atts)
    {
        $post = get_queried_object();

        // Show categories for posts
        if ($post->post_type === 'post') {
            $categories = get_the_category();
            if (!empty($categories)) {
                $category = $categories[0];

                // Show parent categories
                if ($category->parent != 0) {
                    $parent_categories = array();
                    $parent = $category->parent;

                    while ($parent) {
                        $parent_cat = get_category($parent);
                        $parent_categories[] = $parent_cat;
                        $parent = $parent_cat->parent;
                    }

                    $parent_categories = array_reverse($parent_categories);

                    foreach ($parent_categories as $parent_cat) {
                        echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                        echo '<li class="breadcrumb-item breadcrumb-category">';
                        echo '<a href="' . get_category_link($parent_cat->term_id) . '">' . esc_html($parent_cat->name) . '</a>';
                        echo '</li>';
                    }
                }

                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-category">';
                echo '<a href="' . get_category_link($category->term_id) . '">' . esc_html($category->name) . '</a>';
                echo '</li>';
            }
        }

        // Current post/page
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . esc_html(get_the_title()) . '</li>';
        }
    }

    private function render_page_breadcrumb($atts)
    {
        $post = get_queried_object();

        // Show parent pages
        if ($post->post_parent) {
            $parent_pages = array();
            $parent = $post->post_parent;

            while ($parent) {
                $parent_page = get_post($parent);
                $parent_pages[] = $parent_page;
                $parent = $parent_page->post_parent;
            }

            $parent_pages = array_reverse($parent_pages);

            foreach ($parent_pages as $parent_page) {
                echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
                echo '<li class="breadcrumb-item breadcrumb-page">';
                echo '<a href="' . get_permalink($parent_page->ID) . '">' . esc_html($parent_page->post_title) . '</a>';
                echo '</li>';
            }
        }

        // Current page
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">' . esc_html(get_the_title()) . '</li>';
        }
    }

    private function render_blog_breadcrumb($atts)
    {
        if ($atts['show_current'] === 'true') {
            echo '<li class="breadcrumb-item breadcrumb-separator">' . esc_html($atts['separator']) . '</li>';
            echo '<li class="breadcrumb-item breadcrumb-current" aria-current="page">Blog</li>';
        }
    }

    private function add_breadcrumb_styles()
    {
?>
        <style>
            .sweetaddons-breadcrumb {
                margin: 20px 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }

            .sweetaddons-breadcrumb .breadcrumb-list {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                list-style: none;
                margin: 0;
                padding: 0;
                font-size: 14px;
            }

            .sweetaddons-breadcrumb .breadcrumb-item {
                display: flex;
                align-items: center;
            }

            .sweetaddons-breadcrumb .breadcrumb-separator {
                margin: 0 8px;
                color: #666;
                opacity: 0.7;
            }

            .sweetaddons-breadcrumb a {
                color: #0073aa;
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .sweetaddons-breadcrumb a:hover {
                color: #005177;
                text-decoration: underline;
            }

            .sweetaddons-breadcrumb .breadcrumb-current {
                color: #666;
                font-weight: 500;
            }

            /* Style variations */
            .sweetaddons-breadcrumb-minimal {
                margin: 10px 0;
            }

            .sweetaddons-breadcrumb-minimal .breadcrumb-list {
                font-size: 13px;
            }

            .sweetaddons-breadcrumb-minimal .breadcrumb-separator {
                margin: 0 6px;
            }

            .sweetaddons-breadcrumb-arrow .breadcrumb-separator::before {
                content: '→';
                margin: 0 6px;
            }

            .sweetaddons-breadcrumb-arrow .breadcrumb-separator {
                margin: 0;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .sweetaddons-breadcrumb .breadcrumb-list {
                    font-size: 13px;
                }

                .sweetaddons-breadcrumb .breadcrumb-separator {
                    margin: 0 6px;
                }
            }

            @media (max-width: 480px) {
                .sweetaddons-breadcrumb {
                    margin: 15px 0;
                }

                .sweetaddons-breadcrumb .breadcrumb-list {
                    font-size: 12px;
                }

                .sweetaddons-breadcrumb .breadcrumb-separator {
                    margin: 0 4px;
                }
            }
        </style>
<?php
    }
}
