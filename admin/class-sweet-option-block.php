<?php

/**
 * The Block Login settings page functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 */

class Sweet_Option_Block
{
    public function __construct()
    {
        // Menu registration removed - now handled via tabs in Sweetaddons_protect
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings()
    {
        register_setting('Sweetaddons_block_group', 'block_wp_login');
        register_setting('Sweetaddons_block_group', 'whitelist_block_wp_login');
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

    public function save_button()
    {
        echo '<button type="submit" name="submit" class="button button-primary" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:5px;margin-top:-2px;width:14px;height:14px;"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>Simpan Pengaturan</button>';
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
        <?php
        $subnav = Sweetaddons_Admin_Layout::get_proteksi_subnav();
        Sweetaddons_Admin_Layout::open('Blokir Login', 'Sweetaddons_block', $subnav); ?>
        <div class="sad-grid">
            <div class="sad-card">
                <div class="sad-card-title">Pengaturan Utama</div>
                <form method="post" action="options.php" class="sad-form">
                    <?php settings_fields('Sweetaddons_block_group'); ?>
                    <?php do_settings_sections('Sweetaddons_block_group'); ?>

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

                    <div class="sad-actions-row sad-actions-row--end">
                        <?php $this->save_button(); ?>
                    </div>
                </form>
            </div>
        </div>
        <?php Sweetaddons_Admin_Layout::close(); ?>
<?php
    }
}
