<?php

/**
 * REST API Controller for SEO options
 *
 * @package sweetaddons
 * @subpackage sweetaddons/src/Api
 */

class Sweetaddons_SEO_Controller
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('sweetaddons/v1', '/seo/options', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_options'),
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                }
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_options'),
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                }
            ),
        ));
    }

    public function get_options(\WP_REST_Request $request)
    {
        $opts = array(
            'sweetaddons_seo_home_title' => get_option('sweetaddons_seo_home_title', ''),
            'sweetaddons_seo_home_description' => get_option('sweetaddons_seo_home_description', ''),
            'sweetaddons_seo_default_og_image' => get_option('sweetaddons_seo_default_og_image', ''),
            'sweetaddons_seo_twitter_site' => get_option('sweetaddons_seo_twitter_site', ''),
            'sweetaddons_seo_enable_sitemap' => get_option('sweetaddons_seo_enable_sitemap', '1'),
            'sweetaddons_seo_google_search_console' => get_option('sweetaddons_seo_google_search_console', ''),
            'sweetaddons_seo_template_single_title' => get_option('sweetaddons_seo_template_single_title', '{post_title} | {site_name}'),
            'sweetaddons_seo_template_single_description' => get_option('sweetaddons_seo_template_single_description', '{excerpt}'),
            'sweetaddons_seo_template_page_title' => get_option('sweetaddons_seo_template_page_title', '{page_title} | {site_name}'),
            'sweetaddons_seo_template_page_description' => get_option('sweetaddons_seo_template_page_description', '{excerpt}'),
            'sweetaddons_seo_template_category_title' => get_option('sweetaddons_seo_template_category_title', '{category_name} | {site_name}'),
            'sweetaddons_seo_template_category_description' => get_option('sweetaddons_seo_template_category_description', '{category_description}'),
            'sweetaddons_seo_template_tag_title' => get_option('sweetaddons_seo_template_tag_title', '{tag_name} | {site_name}'),
            'sweetaddons_seo_template_tag_description' => get_option('sweetaddons_seo_template_tag_description', '{tag_description}')
        );
        return new \WP_REST_Response($opts, 200);
    }

    public function update_options(\WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        if (!is_array($params)) {
            return new \WP_Error('invalid_payload', 'Payload harus berupa JSON', array('status' => 400));
        }

        $fields = array(
            'sweetaddons_seo_home_title',
            'sweetaddons_seo_home_description',
            'sweetaddons_seo_default_og_image',
            'sweetaddons_seo_twitter_site',
            'sweetaddons_seo_enable_sitemap',
            'sweetaddons_seo_google_search_console',
            'sweetaddons_seo_template_single_title',
            'sweetaddons_seo_template_single_description',
            'sweetaddons_seo_template_page_title',
            'sweetaddons_seo_template_page_description',
            'sweetaddons_seo_template_category_title',
            'sweetaddons_seo_template_category_description',
            'sweetaddons_seo_template_tag_title',
            'sweetaddons_seo_template_tag_description'
        );

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                update_option($field, sanitize_text_field($params[$field]));
            } else {
                if ($field === 'sweetaddons_seo_enable_sitemap') {
                    update_option($field, '0');
                }
            }
        }

        return new \WP_REST_Response(array('success' => true), 200);
    }
}
