<?php
class Sweetaddons_Captcha
{
    public $difficulty;
    public $active;
    private $areas = array(
        'login' => '1',
        'comment' => '1',
        'register' => '1'
    );

    public function __construct()
    {
        $opt = get_option('captcha_Sweetaddons', array());
        $this->difficulty = isset($opt['difficulty']) ? $opt['difficulty'] : 'medium';
        $this->active = !empty($opt['aktif']);
        if (isset($opt['login'])) $this->areas['login'] = $opt['login'];
        if (isset($opt['comment'])) $this->areas['comment'] = $opt['comment'];
        if (isset($opt['register'])) $this->areas['register'] = $opt['register'];

        if ($this->active) {
            if ($this->areas['login'] === '1') {
                add_action('login_form', array($this, 'display'));
                add_filter('wp_authenticate_user', array($this, 'verify_login_form'), 10, 2);
            }
            if ($this->areas['comment'] === '1') {
                add_action('comment_form_after_fields', array($this, 'display'));
                add_action('pre_comment_on_post', array($this, 'verify_comment_form'), 10, 1);
            }
            if ($this->areas['register'] === '1') {
                add_action('register_form', array($this, 'display'));
                add_filter('registration_errors', array($this, 'verify_register_form'), 10, 3);
            }
            add_action('lostpassword_form', array($this, 'display'));
            add_action('lostpassword_post', array($this, 'lostpassword_post'));

            add_filter('query_vars', array($this, 'add_query_vars'));
            add_action('init', array($this, 'maybe_render_image'), 1);

            if (class_exists('WPCF7')) {
                add_action('wpcf7_init', array($this, 'wpcf7_form_captcha'));
            }

            add_shortcode('sweet_captcha', array($this, 'shortcode'));
            add_shortcode('sweet_recaptcha', array($this, 'shortcode'));
        }
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'sweetaddons_captcha';
        $vars[] = 'token';
        return $vars;
    }

    public function maybe_render_image()
    {
        // Use $_GET directly because this runs on 'init' (priority 1),
        // before get_query_var() is populated by the main query.
        if (!isset($_GET['sweetaddons_captcha'])) {
            return;
        }

        $qv = sanitize_text_field(wp_unslash($_GET['sweetaddons_captcha']));
        $token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';

        // Handle preview mode
        if ($qv === 'preview') {
            if (!current_user_can('manage_options')) {
                wp_die('Access denied');
            }

            $difficulty = isset($_GET['difficulty']) ? sanitize_text_field(wp_unslash($_GET['difficulty'])) : 'medium';
            $this->difficulty = $difficulty;

            $code = $this->generate_code();
            $this->render_image($code);
            exit;
        }

        if ($qv === 'image' && $token) {
            $code = get_transient('sweetaddons_captcha_' . $token);
            if (!$code) {
                status_header(410);
                exit;
            }
            $this->render_image($code);
            exit;
        }
    }

    private function render_image($code)
    {
        $w = 270;
        $h = 60;
        $img = imagecreatetruecolor($w, $h);
        imageantialias($img, true);
        $bg = imagecolorallocate($img, 245, 246, 250);
        $fg = imagecolorallocate($img, 30, 30, 30);
        $noise1 = imagecolorallocate($img, 200, 200, 200);
        $noise2 = imagecolorallocate($img, 180, 180, 180);
        imagefilledrectangle($img, 0, 0, $w, $h, $bg);
        for ($i = 0; $i < 40; $i++) {
            imageline($img, mt_rand(0, $w), mt_rand(0, $h), mt_rand(0, $w), mt_rand(0, $h), $noise1);
        }
        for ($i = 0; $i < 150; $i++) {
            imagesetpixel($img, mt_rand(0, $w), mt_rand(0, $h), $noise2);
        }
        $x = 20;
        $len = strlen($code);
        $spacing = ($w - 40) / max($len, 1);
        for ($i = 0; $i < $len; $i++) {
            $y = mt_rand(22, 30);
            imagestring($img, 5, (int) $x, $y, $code[$i], $fg);
            $x += $spacing;
        }
        header('Content-Type: image/png');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        imagepng($img);
        imagedestroy($img);
    }

    private function generate_code()
    {
        $len = 5;
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        if ($this->difficulty === 'easy') {
            $len = 4;
            $chars = '123456789';
        } elseif ($this->difficulty === 'hard') {
            $len = 6;
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        }

        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    private function make_block()
    {
        $token = wp_generate_password(16, false, false);
        $code = $this->generate_code();
        set_transient('sweetaddons_captcha_' . $token, $code, 15 * MINUTE_IN_SECONDS);
        $src = add_query_arg(array('sweetaddons_captcha' => 'image', 'token' => $token, 'v' => time()), home_url('/index.php'));
        $html = '<div class="sweetaddons-captcha" style="margin:10px 0; width:100%;">';
        $html .= '<img src="' . esc_url($src) . '" alt="Captcha" style="display:block; border:1px solid #d0d4d9; width:100%; height:auto; background:#f5f6fa; border-radius:4px; box-sizing:border-box;" />';
        $html .= '<input type="text" name="sweetaddons_captcha_input" placeholder="Masukkan teks di gambar" required style="margin-top:8px; padding:8px 10px; width:100%; font-size:13px; border:1px solid #d0d4d9; border-radius:4px; box-sizing:border-box;" />';
        $html .= '<input type="hidden" name="sweetaddons_captcha_token" value="' . esc_attr($token) . '" />';
        $html .= '</div>';
        return $html;
    }

    public function display()
    {
        echo $this->make_block();
    }

    public function shortcode()
    {
        return $this->make_block();
    }

    private function verify_pair()
    {
        $token = isset($_POST['sweetaddons_captcha_token']) ? sanitize_text_field(wp_unslash($_POST['sweetaddons_captcha_token'])) : '';
        $input = isset($_POST['sweetaddons_captcha_input']) ? sanitize_text_field(wp_unslash($_POST['sweetaddons_captcha_input'])) : '';
        if (!$token || !$input) {
            return array('success' => false, 'message' => 'Harap masukkan teks pada gambar');
        }
        $code = get_transient('sweetaddons_captcha_' . $token);
        if (!$code) {
            return array('success' => false, 'message' => 'Captcha kadaluarsa, muat ulang halaman');
        }
        delete_transient('sweetaddons_captcha_' . $token);
        if (strcasecmp($code, $input) === 0) {
            return array('success' => true, 'message' => 'Captcha valid');
        }
        return array('success' => false, 'message' => 'Captcha salah');
    }

    public function verify_login_form($user, $password)
    {
        $v = $this->verify_pair();
        if (!$v['success']) {
            return new WP_Error('captcha_invalid', __($v['message']));
        }
        return $user;
    }

    public function verify_comment_form($comment_data)
    {
        $v = $this->verify_pair();
        if (!$v['success']) {
            wp_die($v['message']);
        }
        return $comment_data;
    }

    public function lostpassword_post()
    {
        if (!is_user_logged_in()) {
            $v = $this->verify_pair();
            if (!$v['success']) {
                wp_die($v['message']);
            }
        }
    }

    public function verify_register_form($errors, $sanitized_user_login, $user_email)
    {
        $v = $this->verify_pair();
        if (!$v['success']) {
            $errors->add('captcha_error', __($v['message']));
        }
        return $errors;
    }

    public function wpcf7_form_captcha()
    {
        wpcf7_add_form_tag('recaptcha', array($this, 'wpcf7_display_captcha'));
    }

    public function wpcf7_display_captcha()
    {
        return $this->make_block();
    }
}
