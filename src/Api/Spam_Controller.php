<?php
class Sweetaddons_Spam_Controller
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('sweetaddons/v1', '/spam/options', array(
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
            'limit_login_attempts' => get_option('limit_login_attempts') ? '1' : '0',
            'disable_xmlrpc' => get_option('disable_xmlrpc') ? '1' : '0',
            'disable_rest_api' => get_option('disable_rest_api') ? '1' : '0',
        );
        return new \WP_REST_Response($opts, 200);
    }

    public function update_options(\WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        $limit_login_attempts = isset($params['limit_login_attempts']) && in_array($params['limit_login_attempts'], array('1', 1, true), true) ? '1' : '0';
        $disable_xmlrpc = isset($params['disable_xmlrpc']) && in_array($params['disable_xmlrpc'], array('1', 1, true), true) ? '1' : '0';
        $disable_rest_api = isset($params['disable_rest_api']) && in_array($params['disable_rest_api'], array('1', 1, true), true) ? '1' : '0';

        update_option('limit_login_attempts', $limit_login_attempts);
        update_option('disable_xmlrpc', $disable_xmlrpc);
        update_option('disable_rest_api', $disable_rest_api);

        return new \WP_REST_Response(array('status' => 'success'), 200);
    }
}
