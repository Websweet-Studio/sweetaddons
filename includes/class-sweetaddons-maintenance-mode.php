<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    Sweetaddons
 * @subpackage Sweetaddons/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Sweetaddons
 * @subpackage Sweetaddons/includes
 * @author     WebsweetStudio <websweetstudio@gmail.com>
 */
class Sweetaddons_Maintenance_Mode
{
    public function __construct()
    {
        if (get_option('maintenance_mode')) {
            add_action('wp', array($this, 'check_maintenance_mode'));
            add_action('admin_notices', [$this, 'qc_maintenance']);
        }
    }

    public function check_maintenance_mode()
    {
        if (!current_user_can('manage_options') && !is_admin() && !is_page('myaccount')) {
            $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            if (strpos($request_uri, 'sweetaddons_captcha=image') !== false) {
                return;
            }
            $opt    = get_option('maintenance_mode_data', []);
            $hd     = isset($opt['header']) && !empty($opt['header']) ? $opt['header'] : 'Maintenance Mode';
            $bd     = isset($opt['body']) && !empty($opt['body']) ? $opt['body'] : '';

            $this->show_maintenance_page($hd, $bd);
        }
    }

    private function show_maintenance_page($title, $message)
    {
        // Get site information
        $site_name = get_bloginfo('name');
        $site_icon_url = get_site_icon_url() ? get_site_icon_url() : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiNGM0Y0RjYiLz4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMjQiIGZpbGw9IiM5Q0EzQUYiLz4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMTYiIGZpbGw9IiNGRkZGRkYiLz4KPC9zdmc+';

        // Set proper HTTP headers
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

                    // Add click sound effect (optional)
                    const clickSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi6Gy/LaizsJHWi98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');

                    document.querySelector('.back-to-home').addEventListener('click', function(e) {
                        e.preventDefault();
                        // Smooth fade out
                        document.body.style.opacity = '0';
                        document.body.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 500);
                    });
                });
            </script>
        </body>

        </html>
<?php
        exit;
    }

    public function qc_maintenance()
    {
        echo '<div class="notice notice-warning notice-alt">';
        echo $this->check_permalink_settings();
        echo $this->check_site_icon();
        echo $this->check_recaptcha();
        echo $this->check_seo();
        echo $this->check_domain_extension();
        echo $this->check_installed_plugins();
        echo '</div>';
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
        echo '<p>Pastikan Copy Right Sesuai Tahun!</p>';

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
        $linksetting    = admin_url('admin.php?page=sweet_seo_settings');
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

        // Mendapatkan pengaturan auto-update untuk plugin
        $auto_update_plugins = get_site_option('auto_update_plugins', []);

        // Plugin yang dikecualikan
        $excluded_plugins = ['bb-ultimate-addon/bb-ultimate-addon.php', 'WebsweetStudio-toko/WebsweetStudio-toko.php'];

        foreach ($plugins as $plugin_file => $plugin_data) {
            // Mengambil slug dari plugin
            $plugin_slug = $plugin_file; // Contoh: 'plugin-directory/plugin-file.php'
            if (!in_array($plugin_slug, $excluded_plugins)) {
                if (!in_array($plugin_slug, $auto_update_plugins)) {
                    echo '<p>' . $plugin_data['Name'] . ' belum diaktifkan untuk pembaruan otomatis.</p>';
                }
            }
        }

        return ob_get_clean();
    }
}

// Inisialisasi class Sweetaddons_Maintenance_Mode
$sweet_maintenance_mode = new Sweetaddons_Maintenance_Mode();
