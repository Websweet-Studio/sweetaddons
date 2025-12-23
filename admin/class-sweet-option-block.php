<?php

/**
 * The Block Login settings page functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    Sweetaddons
 * @subpackage Sweetaddons/admin
 */

class Sweet_Option_Block
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_submenu_page()
    {
        add_submenu_page(
            'custom_admin_options',     // Parent slug
            'Blokir Login',              // Page title
            'Blokir Login',              // Menu title
            'manage_options',           // Capability
            'Sweetaddons_block',       // Menu slug
            array($this, 'block_page_callback') // Callback function
        );
    }

    public function register_settings()
    {
        register_setting('Sweetaddons_block_group', 'block_wp_login');
        register_setting('Sweetaddons_block_group', 'whitelist_block_wp_login');
        register_setting('Sweetaddons_block_group', 'whitelist_country');
        register_setting('Sweetaddons_block_group', 'redirect_to');
    }

    public function field($data)
    {
        $type   = isset($data['type']) ? $data['type'] : '';
        $id     = isset($data['id']) ? $data['id'] : '';
        $std    = isset($data['std']) ? $data['std'] : '';
        $step   = isset($data['step']) ? $data['step'] : '';
        $value  = get_option($id, $std);
        $name   = $id;

        // jika ada sub, sub array dari Value
        if (isset($data['sub']) && !empty($data['sub'])) {
            $sub    = $data['sub'];
            $value  = isset($value[$sub]) ? $value[$sub] : '';
            $name   = $id . '[' . $sub . ']';
        }

        if ($std && empty($value) && $type != 'checkbox') {
            $value = $std;
        }

        //jika field checkbox
        if ($type == 'checkbox') {
            $checked = ($value == 1) ? 'checked' : '';
            echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . $checked . '> ';
        }
        //jika field text
        if ($type == 'text') {
            echo '<div><input type="text" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }

        if ($type == 'password') {
            echo '<div><input type="password" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }

        //jika field number
        if ($type == 'number') {
            echo '<div><input type="number" step="' . $step . '" min="0" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="small-text"></div>';
        }
        //jika field textarea
        if ($type == 'textarea') {
            echo '<div>';
            echo '<textarea id="' . $id . '" name="' . $name . '" rows="6" cols="50" class="large-text">';
            echo $value;
            echo '</textarea>';
            echo '</div>';
        }

        ///tampil label
        if (isset($data['label']) && !empty($data['label'])) {
            echo '<label for="' . $id . '">';
            echo '<small>' . $data['label'] . '</small>';
            echo '</label>';
        }

        ///tampil deskripsi
        if (isset($data['desc']) && !empty($data['desc'])) {
            echo '<div>';
            echo '<small>' . $data['desc'] . '</small>';
            echo '</div>';
        }
    }

    public function block_page_callback()
    {
        $block_fields = [
            [
                'id'    => 'block_wp_login',
                'type'  => 'checkbox',
                'title' => 'Blokir wp-login.php',
                'std'   => 0,
                'label' => 'Aktifkan pemblokiran akses ke file wp-login.php pada situs.',
            ],
            [
                'id'    => 'whitelist_block_wp_login',
                'type'  => 'text',
                'title' => 'IP Whitelist',
                'std'   => '',
                'label' => 'Daftar IP yang dikecualikan (pisahkan dengan koma).',
            ],
            [
                'id'    => 'whitelist_country',
                'type'  => 'text',
                'title' => 'Negara Diizinkan',
                'std'   => 'ID',
                'label' => 'Kode negara (ID, MY, US) yang diizinkan mengakses.',
            ],
            [
                'id'    => 'redirect_to',
                'type'  => 'text',
                'title' => 'Redirect URL',
                'std'   => 'http://127.0.0.1',
                'label' => 'Tujuan redirect jika diblokir.',
            ],
        ];

        // Get current values for summary
        $block_active = get_option('block_wp_login', 0);
        $whitelist_count = 0;
        $wl_ip = get_option('whitelist_block_wp_login', '');
        if (!empty($wl_ip)) {
            $whitelist_count = count(explode(',', $wl_ip));
        }
?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">🔒 Pengaturan Blokir Login</h1>

            <!-- Status Summary Cards -->
            <div class="sad-grid" style="margin-bottom: 20px;">
                <div class="sad-card" style="text-align: center; border-bottom: 4px solid #0073aa;">
                    <h3 style="margin: 0 0 10px 0; color: #0073aa;">Status Blokir</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #23282d;">
                        <?php echo $block_active ? '<span style="color:#00a32a">AKTIF</span>' : '<span style="color:#d63638">NONAKTIF</span>'; ?>
                    </div>
                    <div style="color: #666; font-size: 14px;">Proteksi wp-login.php</div>
                </div>

                <div class="sad-card" style="text-align: center; border-bottom: 4px solid #0073aa;">
                    <h3 style="margin: 0 0 10px 0; color: #0073aa;">IP Whitelist</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #23282d;">
                        <?php echo $whitelist_count; ?>
                    </div>
                    <div style="color: #666; font-size: 14px;">IP Terdaftar</div>
                </div>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('Sweetaddons_block_group'); ?>

                <div class="sad-top">
                    <!-- Left Column -->
                    <div class="sad-top-left">
                        <div class="sad-card">
                            <div class="sad-card-title">⚙️ Konfigurasi Utama</div>
                            <table class="form-table">
                                <?php
                                foreach ($block_fields as $data) :
                                    echo '<tr>';
                                    echo '<th scope="row">' . $data['title'] . '</th>';
                                    echo '<td>';
                                    $this->field($data);
                                    echo '</td>';
                                    echo '</tr>';
                                endforeach;
                                ?>
                            </table>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="sad-top-right">
                        <div class="sad-card" style="text-align: center; background: #f0f8ff; border-color: #0073aa;">
                            <h3 style="margin-top: 0;">💾 Simpan Perubahan</h3>
                            <p style="margin-bottom: 15px; color: #666;">Jangan lupa simpan pengaturan Anda.</p>
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false, array('style' => 'width: 100%;')); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
}

// Initialize the Block page
$sweet_option_block = new Sweet_Option_Block();
