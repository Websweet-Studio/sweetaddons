<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sweet_Option_Snipet
{
    private $allowed_tabs = array('header', 'footer', 'body', 'php', 'css', 'js');

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_code_editor'));
    }

    public function add_submenu_page()
    {
        add_submenu_page(
            'custom_admin_options',
            'Script',
            'Script',
            'manage_options',
            'Sweetaddons_snipet',
            array($this, 'page_callback')
        );
    }

    public function register_settings()
    {
        foreach ($this->allowed_tabs as $tab) {
            register_setting('sweetaddons_snipet_group', $this->get_option_name($tab), array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_snippet'),
                'default' => '',
            ));
        }
    }

    public function sanitize_snippet($value)
    {
        return is_string($value) ? $value : '';
    }

    public function enqueue_code_editor()
    {
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if ($page !== 'Sweetaddons_snipet') {
            return;
        }

        $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'header';
        $settings = wp_enqueue_code_editor(array(
            'type' => $this->get_editor_type($tab),
        ));

        if ($settings === false) {
            return;
        }

        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');

        wp_add_inline_script(
            'wp-theme-plugin-editor',
            'jQuery(function(){if(window.wp&&wp.codeEditor){wp.codeEditor.initialize(document.getElementById("sweetaddons-snipet-editor"), ' . wp_json_encode($settings) . ');}});'
        );
    }

    public function page_callback()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'header';
        if (!in_array($current_tab, $this->allowed_tabs, true)) {
            $current_tab = 'header';
        }

        $subnav = Sweetaddons_Admin_Layout::get_snipet_subnav();
        Sweetaddons_Admin_Layout::open('Script', 'Sweetaddons_snipet', $subnav);

        $this->render_editor_tab($current_tab);

        Sweetaddons_Admin_Layout::close();
    }

    private function render_editor_tab($tab)
    {
        $option_name = $this->get_option_name($tab);
        $value = get_option($option_name, '');
        $config = $this->get_tab_config($tab);
        ?>
        <form method="post" action="options.php" class="sad-form">
            <?php settings_fields('sweetaddons_snipet_group'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card" style="margin-bottom:16px;">
                        <div class="sad-card-title"><?php echo esc_html($config['title']); ?></div>
                        <p class="sad-subtext" style="margin-bottom:12px;"><?php echo esc_html($config['description']); ?></p>
                        <textarea
                            id="sweetaddons-snipet-editor"
                            name="<?php echo esc_attr($option_name); ?>"
                            rows="18"
                            class="large-text code"
                            style="min-height:360px;"
                            placeholder="<?php echo esc_attr($config['placeholder']); ?>"><?php echo esc_textarea($value); ?></textarea>
                    </div>

                    <div class="sad-card">
                        <div class="sad-card-title">Contoh Penggunaan</div>
                        <div class="sad-subtext"><?php echo wp_kses_post($config['example']); ?></div>
                    </div>
                </div>

                <div class="sad-top-right">
                    <div class="sad-card" style="margin-bottom:16px;">
                        <div class="sad-card-title">Catatan</div>
                        <ul style="margin:0; padding-left:18px; display:flex; flex-direction:column; gap:6px;">
                            <li>Script aktif setelah disimpan.</li>
                            <li>Gunakan untuk script kecil, shortcode sederhana, CSS, atau JS tambahan.</li>
                            <li>Script PHP dijalankan lewat <code>init</code>. Pastikan kode aman.</li>
                        </ul>
                    </div>
                    <div class="sad-card">
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <button type="submit" name="submit" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;">Simpan Script</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }

    private function get_option_name($tab)
    {
        return 'sweetaddons_snipet_' . $tab;
    }

    private function get_editor_type($tab)
    {
        if ($tab === 'css') {
            return 'text/css';
        }

        if ($tab === 'js') {
            return 'application/javascript';
        }

        if ($tab === 'php') {
            return 'application/x-httpd-php';
        }

        return 'text/html';
    }

    private function get_tab_config($tab)
    {
        $configs = array(
            'header' => array(
                'title' => 'Script Header',
                'description' => 'Output ke wp_head. Cocok untuk meta tag, verifikasi, atau gtag di bagian head.',
                'placeholder' => '<script>console.log("header snippet");</script>',
                'example' => '<code>&lt;script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXX"&gt;&lt;/script&gt;</code>',
            ),
            'footer' => array(
                'title' => 'Script Footer',
                'description' => 'Output ke wp_footer. Cocok untuk script tambahan sebelum penutup body.',
                'placeholder' => '<script>console.log("footer snippet");</script>',
                'example' => '<code>&lt;script&gt;console.log("custom footer");&lt;/script&gt;</code>',
            ),
            'body' => array(
                'title' => 'Script Body',
                'description' => 'Output ke wp_body_open. Cocok untuk GTM noscript, pixel, atau markup tepat setelah body dibuka.',
                'placeholder' => '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXX" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>',
                'example' => '<code>&lt;noscript&gt;...&lt;/noscript&gt;</code>',
            ),
            'php' => array(
                'title' => 'Script PHP',
                'description' => 'Kode PHP kecil. Bisa untuk add_shortcode, add_filter, atau add_action sederhana.',
                'placeholder' => 'add_shortcode(\'tahun\', function () {
    return date(\'Y\');
});',
                'example' => '<code>add_shortcode(\'tahun\', function () { return date(\'Y\'); });</code>',
            ),
            'css' => array(
                'title' => 'Script CSS',
                'description' => 'CSS tambahan. Plugin akan membungkus otomatis dengan tag <style>.',
                'placeholder' => 'body {
    scroll-behavior: smooth;
}',
                'example' => '<code>.site-header { position: sticky; top: 0; }</code>',
            ),
            'js' => array(
                'title' => 'Script JS',
                'description' => 'JavaScript tambahan. Plugin akan membungkus otomatis dengan tag <script>.',
                'placeholder' => 'document.addEventListener(\'DOMContentLoaded\', function () {
    console.log(\'hello\');
});',
                'example' => '<code>document.querySelectorAll(\'.btn\').forEach(function (el) { /* ... */ });</code>',
            ),
        );

        return isset($configs[$tab]) ? $configs[$tab] : $configs['header'];
    }
}
