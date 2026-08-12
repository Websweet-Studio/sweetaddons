<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class Sweetaddons_Maintenance_Mode
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'check_maintenance_mode'), 0);
    }

    private function is_sweetaddons_admin_page()
    {
        if (!is_admin()) {
            return false;
        }

        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        return $page === 'custom_admin_options' || strpos($page, 'Sweetaddons_') === 0;
    }

    public function check_maintenance_mode()
    {
        if (!get_option('maintenance_mode')) {
            return;
        }

        if (is_admin()) {
            return;
        }

        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
            return;
        }

        if (function_exists('wp_doing_cron') && wp_doing_cron()) {
            return;
        }

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        if (function_exists('wp_is_json_request') && wp_is_json_request()) {
            return;
        }

        if (current_user_can('manage_options')) {
            return;
        }

        if (is_page('myaccount')) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        if ($request_uri && strpos($request_uri, 'sweetaddons_captcha=image') !== false) {
            return;
        }

        $opt    = get_option('maintenance_mode_data', []);
        $hd     = isset($opt['header']) && !empty($opt['header']) ? $opt['header'] : 'Maintenance Mode';
        $bd     = isset($opt['body']) && !empty($opt['body']) ? $opt['body'] : '';

        $this->show_maintenance_page($hd, $bd);
    }

    private function show_maintenance_page($title, $message)
    {
        // Get site information
        $site_name = get_bloginfo('name');
        $site_icon_url = get_site_icon_url() ? get_site_icon_url() : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiNGM0Y0RjYiLz4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMjQiIGZpbGw9IiM5Q0EzQUYiLz4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMTYiIGZpbGw9IiNGRkZGRkYiLz4KPC9zdmc+';

        // Set proper HTTP headers
        if (function_exists('nocache_headers')) {
            nocache_headers();
        }
        status_header(503);
        header('Content-Type: text/html; charset=utf-8');
        header('Retry-After: 3600'); // Suggest retry after 1 hour

?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="robots" content="noindex, nofollow">
            <title><?php echo esc_html($title); ?> - <?php echo esc_html($site_name); ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    background: #f8f9fa;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #333;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                }

                h1 {
                    font-size: 2rem;
                    font-weight: 300;
                    margin-bottom: 1rem;
                    text-align: center;
                    color: #000;
                }

                p {
                    font-size: 1rem;
                    text-align: center;
                    max-width: 600px;
                    margin: 0 auto;
                    color: #6c757d;
                    line-height: 1.5;
                }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .lucide-server-crash-icon .sweetaddons-lightning {
                    stroke: #facc15;
                    filter: drop-shadow(0 0 6px rgba(250, 204, 21, 0.95)) drop-shadow(0 0 16px rgba(250, 204, 21, 0.55));
                    transform-origin: 12px 12px;
                    animation: sweetaddonsLightningFlicker 1.35s infinite steps(1, end);
                }

                @keyframes sweetaddonsLightningFlicker {
                    0% {
                        opacity: 1;
                    }

                    3% {
                        opacity: 0.25;
                    }

                    6% {
                        opacity: 1;
                    }

                    8% {
                        opacity: 0.55;
                    }

                    10% {
                        opacity: 1;
                    }

                    55% {
                        opacity: 1;
                    }

                    57% {
                        opacity: 0.1;
                    }

                    60% {
                        opacity: 1;
                    }

                    80% {
                        opacity: 0.6;
                    }

                    83% {
                        opacity: 1;
                    }

                    100% {
                        opacity: 1;
                    }
                }

                /* Mobile Responsive */
                @media (max-width: 640px) {
                    h1 {
                        font-size: 2rem;
                    }

                    p {
                        font-size: 1.1rem;
                        padding: 0 1rem;
                    }
                }
            </style>
        </head>

        <body>
            <div style="text-align: center;">
                <div style="margin-bottom: 2rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="168" height="168" viewBox="0 0 24 24" fill="none" stroke="#a6a6a6" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-server-crash-icon lucide-server-crash">
                        <path d="M6 10H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2" />
                        <path d="M6 14H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2" />
                        <path d="M6 6h.01" />
                        <path d="M6 18h.01" />
                        <path class="sweetaddons-lightning" d="m13 6-4 6h6l-4 6" />
                    </svg>
                </div>

                <h1><?php echo esc_html($title); ?></h1>
                <p><?php echo wp_kses_post($message); ?></p>
            </div>

            <script>
                // Add some interactivity
                document.addEventListener('DOMContentLoaded', function() {
                    // Auto-refresh every 5 minutes
                    setTimeout(function() {
                        window.location.reload();
                    }, 300000);

                    const backToHome = document.querySelector('.back-to-home');
                    if (backToHome) {
                        backToHome.addEventListener('click', function(e) {
                            e.preventDefault();
                            document.body.style.opacity = '0';
                            document.body.style.transition = 'opacity 0.5s ease';
                            setTimeout(() => {
                                window.location.href = this.href;
                            }, 500);
                        });
                    }
                });
            </script>
        </body>

        </html>
<?php
        exit;
    }

    public function qc_maintenance()
    {
        return $this->get_qc_maintenance_content();
    }

    public function get_qc_maintenance_content()
    {
        return
            $this->check_permalink_settings()
            . $this->check_site_icon()
            . $this->check_recaptcha()
            . $this->check_seo()
            . $this->check_domain_extension()
            . $this->check_installed_plugins();
    }

    public function check_domain_extension()
    {
        ob_start();
        // Mendapatkan URL situs saat ini
        $site_url = get_site_url();

        // Menghapus skema (http:// atau https://) dari URL
        $domain = parse_url($site_url, PHP_URL_HOST);

        // Memisahkan nama domain menjadi bagian-bagian
        $domain_parts = explode('.', $domain);

        // Mengambil ekstensi domain (bagian terakhir)
        $extension = array_pop($domain_parts);

        // Memeriksa sub-ekstensi
        $sub_extension = array_pop($domain_parts); // Ambil bagian sebelum ekstensi

        // Daftar ekstensi yang valid
        $valid_extensions = ['go.id', 'desa.id', 'sch.id', 'ac.id'];

        // Memeriksa apakah domain berakhir dengan ekstensi yang valid
        if (in_array($sub_extension . '.' . $extension, $valid_extensions)) {
            echo '<p>Setting Desain By WebsweetStudio => Open New Tab. Linknya Di Warna Sesuai Background, Rata Kiri (Pojok), Saat Hover Jangan Icon Tangan Tapi Icon Panah Sprti Pada Saat Tanpa Hover.</p>';
        }

        return ob_get_clean();
    }

    public function check_permalink_settings()
    {
        ob_start();
        // Mendapatkan pengaturan permalink
        $permalinks = get_option('permalink_structure');
        $linksetting = admin_url('options-permalink.php');

        // Memeriksa apakah permalink tidak diatur
        if (empty($permalinks) || $permalinks != '/%category%/%postname%/') {
            // Menambahkan log peringatan
            echo '<p>Peringatan: Permalink belum disetting. Silakan setting <a href="' . $linksetting . '"><b> disini.</b></a></p>';
        }

        return ob_get_clean();
    }

    public function check_site_icon()
    {
        ob_start();
        $site_icon = get_site_icon_url();
        $linksetting = admin_url('customize.php');

        if (empty($site_icon)) {
            echo '<p>Peringatan: Favicon belum disetting. Silakan setting <a href="' . $linksetting . '"><b> disini.</b></a></p>';
        }
        return ob_get_clean();
    }

    public function check_recaptcha()
    {
        ob_start();
        $linksetting    = admin_url('options-general.php?page=custom_admin_options');
        $check_recaptcha = get_option('captcha_Sweetaddons', []);

        if (!is_array($check_recaptcha)) $check_recaptcha = [];
        $aktif  = isset($check_recaptcha['aktif']) ? $check_recaptcha['aktif'] : false;
        if ($aktif == false) {
            echo '<p>Peringatan: CAPTCHA belum diaktifkan. Silakan atur <a href="' . $linksetting . '"><b> di sini.</b></a></p>';
        }

        return ob_get_clean();
    }

    public function check_seo()
    {
        ob_start();
        $linksetting    = admin_url('admin.php?page=Sweetaddons_seo');
        $home_keywords  = get_option('home_keywords');
        $share_image    = get_option('share_image');

        if (empty($home_keywords) || empty($share_image)) {
            echo '<p>Peringatan: SEO belum disetting. Silakan setting <a href="' . $linksetting . '"><b> disini.</b></a></p>';
        }

        return ob_get_clean();
    }

    public function check_installed_plugins()
    {
        ob_start();

        // Mendapatkan semua plugin yang terinstal
        $plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);

        // Mendapatkan pengaturan auto-update untuk plugin
        $auto_update_plugins = get_site_option('auto_update_plugins', []);

        // Plugin yang dikecualikan
        $excluded_plugins = ['bb-ultimate-addon/bb-ultimate-addon.php', 'WebsweetStudio-toko/WebsweetStudio-toko.php'];

        foreach ($plugins as $plugin_file => $plugin_data) {
            // Hanya proses plugin yang aktif
            if (!in_array($plugin_file, $active_plugins)) {
                continue;
            }
            if (!in_array($plugin_file, $excluded_plugins)) {
                if (!in_array($plugin_file, $auto_update_plugins)) {
                    echo '<p>' . $plugin_data['Name'] . ' belum diaktifkan untuk pembaruan otomatis.</p>';
                }
            }
        }

        return ob_get_clean();
    }
}
