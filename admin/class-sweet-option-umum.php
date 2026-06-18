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

        if (isset($data['sub']) && !empty($data['sub'])) {
            $sub    = $data['sub'];
            $value  = isset($value[$sub]) ? $value[$sub] : '';
            $name   = $id . '[' . $sub . ']';
        }

        if ($std && empty($value) && $type != 'checkbox') {
            $value = $std;
        }

        if ($type == 'checkbox') {
            $checked = ($value == 1) ? 'checked' : '';
            echo '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1" ' . $checked . '> ';
        }
        if ($type == 'text') {
            echo '<div><input type="text" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }
        if ($type == 'password') {
            echo '<div><input type="password" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="regular-text"></div>';
        }
        if ($type == 'number') {
            echo '<div><input type="number" step="' . $step . '" min="0" id="' . $id . '" name="' . $name . '" value="' . $value . '" class="small-text"></div>';
        }
        if ($type == 'textarea') {
            echo '<div>';
            echo '<textarea id="' . $id . '" name="' . $name . '" rows="6" cols="50" class="large-text">';
            echo $value;
            echo '</textarea>';
            echo '</div>';
        }

        if (isset($data['label']) && !empty($data['label'])) {
            echo '<label for="' . $id . '">';
            echo '<small>' . $data['label'] . '</small>';
            echo '</label>';
        }

        if (isset($data['desc']) && !empty($data['desc'])) {
            echo '<div>';
            echo '<small>' . $data['desc'] . '</small>';
            echo '</div>';
        }
    }

    public function save_button()
    {
        echo '<button type="submit" name="submit" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;" onmouseenter="this.style.transform=\'translateY(-1px)\';this.style.boxShadow=\'0 4px 12px rgba(37,99,235,0.4)\';" onmouseleave="this.style.transform=\'translateY(0)\';this.style.boxShadow=\'0 2px 6px rgba(37,99,235,0.25)\';"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>Simpan Pengaturan</button>';
    }

    public function umum_page_callback()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
?>
        <?php
        $subnav = Sweetaddons_Admin_Layout::get_umum_subnav();
        Sweetaddons_Admin_Layout::open('Umum', 'Sweetaddons_umum', $subnav);

        if ($current_tab === 'dbcleaner') {
            $this->render_dbcleaner_tab();
        } else {
            $this->render_general_tab();
        }

        Sweetaddons_Admin_Layout::close();
    }

    private function render_general_tab()
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
        <form method="post" action="options.php" class="sad-form">
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card" style="margin-bottom: 16px;">
                        <div class="sad-card-title">Pengaturan Utama</div>
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
                    </div>
                    <div class="sad-card">
                        <div class="sad-card-title">Update Plugin</div>
                        <?php
                        $checked = isset($_GET['sweetaddons_update_check']) ? sanitize_text_field(wp_unslash($_GET['sweetaddons_update_check'])) : '';
                        if ($checked === '1') {
                            $has_update = isset($_GET['sweetaddons_has_update']) ? sanitize_text_field(wp_unslash($_GET['sweetaddons_has_update'])) : '0';
                            echo '<div class="sad-notice sad-notice-success"><p>';
                            echo $has_update === '1' ? 'Cek update selesai. Update tersedia di halaman Plugins.' : 'Cek update selesai. Tidak ada update terbaru.';
                            echo '</p></div>';
                        }

                        $check_url = wp_nonce_url(
                            admin_url('admin-post.php?action=sweetaddons_check_update'),
                            'sweetaddons_check_update'
                        );
                        ?>
                        <div class="sad-stack" style="gap: 6px; margin-bottom: 12px;">
                            <div>Versi saat ini: <strong><?php echo defined('SWEETADDONS_VERSION') ? esc_html(SWEETADDONS_VERSION) : ''; ?></strong></div>
                            <div><small>Cek update akan mengambil versi terbaru dari GitHub Releases.</small></div>
                        </div>
                        <a href="<?php echo esc_url($check_url); ?>" class="button button-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-right:5px;margin-top:-2px;width:14px;height:14px;">
                                <path d="M12 13v8l-4-4" />
                                <path d="m12 21 4-4" />
                                <path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284" />
                            </svg>Cek Update</a>
                    </div>
                </div>

                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <?php $this->save_button(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php
    }

    private function render_dbcleaner_tab()
    {
        require_once dirname(plugin_dir_path(__FILE__)) . '/includes/class-sweetaddons-database-cleaner.php';
    ?>
        <form method="post" action="" class="sad-form">
            <?php wp_nonce_field('sweetaddons_db_cleaner_action', 'sweetaddons_db_cleaner_nonce'); ?>
            <div class="sad-top">
                <div class="sad-top-left">
                    <div class="sad-card">
                        <div class="sad-card-title">Item yang Dapat Diberishkan</div>
                        <?php
                        $cleaner = new Sweetaddons_Database_Cleaner();
                        $stats = $cleaner->get_stats();
                        foreach ($stats as $key => $value) :
                            if ($value === 0) {
                                continue;
                            }
                            $labels = array(
                                'revisions'   => 'Revisi Postingan',
                                'auto_drafts' => 'Draft Otomatis',
                                'trash_posts' => 'Postingan di Trash',
                                'spam_comments' => 'Komentar Spam',
                                'trash_comments' => 'Komentar di Trash',
                                'transients'  => 'Transien',
                            );
                        ?>
                            <div class="sad-checkbox">
                                <input type="checkbox" id="clean_<?php echo esc_attr($key); ?>" name="clean_items[]" value="<?php echo esc_attr($key); ?>">
                                <label for="clean_<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($labels[$key] ?? $key); ?>
                                    <span class="sad-count">(<?php echo esc_html($value); ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="sad-top-right">
                    <div class="sad-card">
                        <div class="sad-actions-row" style="justify-content:center; text-align:center;">
                            <button type="submit" name="sweetaddons_db_cleaner_clean" style="border:none; cursor:pointer; padding:8px 16px; border-radius:8px; background:linear-gradient(135deg, #2563eb, #1e40af); color:#fff; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(37,99,235,0.25); transition:all 0.2s ease;" onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.4)';" onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 6px rgba(37,99,235,0.25)';">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 22-1-4"/><path d="M19 14a1 1 0 0 0 1-1v-1a2 2 0 0 0-2-2h-3a1 1 0 0 1-1-1V4a2 2 0 0 0-4 0v5a1 1 0 0 1-1 1H6a2 2 0 0 0-2 2v1a1 1 0 0 0 1 1"/><path d="M19 14H5l-1.973 6.767A1 1 0 0 0 4 22h16a1 1 0 0 0 .973-1.233z"/><path d="m8 22 1-4"/></svg>
                                Bersihkan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
<?php
        if (isset($_POST['sweetaddons_db_cleaner_clean']) && wp_verify_nonce($_POST['sweetaddons_db_cleaner_nonce'], 'sweetaddons_db_cleaner_action')) {
            $items_to_clean = isset($_POST['clean_items']) ? array_map('sanitize_text_field', $_POST['clean_items']) : array();
            if (!empty($items_to_clean)) {
                $cleaner = new Sweetaddons_Database_Cleaner();
                $cleaned = $cleaner->clean($items_to_clean);
                if (!empty($cleaned)) {
                    $total_cleaned = array_sum($cleaned);
                    echo '<div class="sad-notice sad-notice-success"><p>Database berhasil dibersihkan. Total ' . $total_cleaned . ' item dihapus.</p></div>';
                } else {
                    echo '<div class="sad-notice sad-notice-warning"><p>Tidak ada item yang berhasil dibersihkan.</p></div>';
                }
            } else {
                echo '<div class="sad-notice sad-notice-warning"><p>Tidak ada item yang dipilih untuk dibersihkan.</p></div>';
            }
        }
    }
}
