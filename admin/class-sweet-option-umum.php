<?php

/**
 * The Umum (General) settings page functionality
 *
 * @link       https://websweetstudio.com
 * @since      1.0.0
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/admin
 */

class Sweet_Option_Umum
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
            'Umum',                     // Page title
            'Umum',                     // Menu title
            'manage_options',           // Capability
            'Sweetaddons_umum',        // Menu slug
            array($this, 'umum_page_callback') // Callback function
        );
    }

    public function register_settings()
    {
        register_setting('Sweetaddons_umum_group', 'fully_disable_comment');
        register_setting('Sweetaddons_umum_group', 'hide_admin_notice');
        register_setting('Sweetaddons_umum_group', 'disable_gutenberg');
        register_setting('Sweetaddons_umum_group', 'classic_widget_Sweetaddons');
        register_setting('Sweetaddons_umum_group', 'remove_slug_category_Sweetaddons');
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

    public function umum_page_callback()
    {
        $umum_fields = [
            [
                'id'    => 'fully_disable_comment',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan Komentar',
                'std'   => 1,
                'label' => 'Nonaktifkan fitur komentar pada situs.',
            ],
            [
                'id'    => 'hide_admin_notice',
                'type'  => 'checkbox',
                'title' => 'Sembunyikan Pemberitahuan Admin',
                'std'   => 0,
                'label' => 'Sembunyikan pemberitahuan admin di halaman admin. Pemberitahuan admin seringkali muncul untuk memberikan informasi atau peringatan kepada admin situs.',
            ],
            [
                'id'    => 'disable_gutenberg',
                'type'  => 'checkbox',
                'title' => 'Nonaktifkan Gutenberg',
                'std'   => 0,
                'label' => 'Aktifkan untuk menggunakan editor klasik WordPress menggantikan Gutenberg.',
            ],
            [
                'id'    => 'classic_widget_Sweetaddons',
                'type'  => 'checkbox',
                'title' => 'Widget Klasik',
                'std'   => 1,
                'label' => 'Aktifkan untuk menggunakan widget klasik.',
            ],
            [
                'id'    => 'remove_slug_category_Sweetaddons',
                'type'  => 'checkbox',
                'title' => 'Hapus Slug Kategori',
                'std'   => 0,
                'label' => 'Aktifkan untuk hapus slug /category/ dari URL.',
            ],
        ];

?>
        <div class="wrap vd-ons sweetaddons-dashboard">
            <h1 class="sad-title">Pengaturan Umum</h1>
            <div class="sad-grid">
                <div class="sad-card">
                    <div class="sad-card-title">Pengaturan Utama</div>
                    <form method="post" action="options.php" class="sad-form">
                        <?php settings_fields('Sweetaddons_umum_group'); ?>
                        <?php do_settings_sections('Sweetaddons_umum_group'); ?>

                        <table class="form-table">
                            <?php
                            foreach ($umum_fields as $data) :
                                echo '<tr>';
                                echo '<th scope="row">';
                                echo $data['title'];
                                echo '</th>';
                                echo '<td>';
                                $this->field($data);
                                echo '</td>';
                                echo '</tr>';
                            endforeach;
                            ?>
                        </table>

                        <div class="sad-actions-row" style="justify-content: flex-end;">
                            <?php submit_button('Simpan Pengaturan', 'primary', 'submit', false); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php
    }
}

// Initialize the Umum page
$sweet_option_umum = new Sweet_Option_Umum();
