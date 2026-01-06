<?php
class Sweetaddons_Umum_Controller
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('sweetaddons/v1', '/umum/options', array(
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
            'fully_disable_comment' => get_option('fully_disable_comment') ? '1' : '0',
            'hide_admin_notice' => get_option('hide_admin_notice') ? '1' : '0',
            'disable_gutenberg' => get_option('disable_gutenberg') ? '1' : '0',
            'classic_widget_Sweetaddons' => get_option('classic_widget_Sweetaddons') ? '1' : '0',
            'remove_slug_category_Sweetaddons' => get_option('remove_slug_category_Sweetaddons') ? '1' : '0',
        );
        return new \WP_REST_Response($opts, 200);
    }

    public function update_options(\WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        $map = array(
            'fully_disable_comment',
            'hide_admin_notice',
            'disable_gutenberg',
            'classic_widget_Sweetaddons',
            'remove_slug_category_Sweetaddons',
        );
        foreach ($map as $field) {
            $val = (isset($params[$field]) && in_array($params[$field], array('1', 1, true), true)) ? '1' : '0';
            update_option($field, $val);
        }
        return new \WP_REST_Response(array('status' => 'success'), 200);
    }
}

