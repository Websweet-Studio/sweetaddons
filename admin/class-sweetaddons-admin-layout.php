<?php

class Sweetaddons_Admin_Layout
{
  public static function get_plugin_name()
  {
    if (class_exists('Sweetaddons_WhiteLabel')) {
      $name = Sweetaddons_WhiteLabel::get_white_labeled_info('plugin_name');
      if (!empty($name)) {
        return $name;
      }
    }

    return 'Sweet Addons';
  }

  public static function get_pages()
    {
        return array(
          array(
            'page'  => 'custom_admin_options',
            'label' => 'Dashboard',
          ),
          array(
            'page'  => 'Sweetaddons_umum',
            'label' => 'Umum',
          ),
          array(
            'page'  => 'Sweetaddons_protect',
            'label' => 'Proteksi',
          ),
          array(
            'page'  => 'Sweetaddons_visitor_stats',
            'label' => 'Statistik',
          ),
          array(
            'page'  => 'Sweetaddons_seo',
            'label' => 'SEO',
          ),
          array(
            'page'  => 'Sweetaddons_whitelabel',
            'label' => 'White Label',
          ),
          array(
            'page'  => 'Sweetaddons_whatsapp',
            'label' => 'WhatsApp',
          ),
          array(
            'page'  => 'Sweetaddons_login_customizer',
            'label' => 'Login',
          ),
          array(
            'page'  => 'Sweetaddons_db_cleaner',
            'label' => 'DB Cleaner',
          ),
        );
    }

  public static function get_proteksi_subnav()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'protect';
        return array(
          array('tab' => 'protect', 'label' => 'Proteksi'),
          array('tab' => 'maintenance', 'label' => 'Maintenance'),
          array('tab' => 'recaptcha', 'label' => 'reCaptcha'),
          array('tab' => 'block', 'label' => 'Blokir Login'),
        );
    }

  public static function get_proteksi_tab_url($tab)
    {
        return admin_url('admin.php?page=Sweetaddons_protect&tab=' . $tab);
    }

  public static function open($page_title, $active_page, $subnav = array())
    {
        $plugin_name = self::get_plugin_name();
        $pages = self::get_pages();
?>
    <div class="wrap vd-ons sweetaddons-dashboard sad-apple">
      <div class="sad-apple__globalnav">
        <div class="sad-apple__brand"><?php echo esc_html($plugin_name); ?></div>
        <nav class="sad-apple__nav" aria-label="Sweet Addons Navigation">
          <?php foreach ($pages as $item) : ?>
            <?php
            $url = admin_url('admin.php?page=' . $item['page']);
            $is_active = ($active_page === $item['page']);
            ?>
            <a class="sad-apple__navlink <?php echo $is_active ? 'is-active' : ''; ?>" href="<?php echo esc_url($url); ?>">
              <?php echo esc_html($item['label']); ?>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
      <div class="sad-apple__subnav">
        <div class="sad-apple__subnav-title"><?php echo esc_html($page_title); ?></div>
        <?php if (!empty($subnav)) : ?>
          <nav class="sad-apple__subnav-tabs" aria-label="Sub Navigation">
            <?php foreach ($subnav as $tab) : ?>
              <?php
              $tab_url = self::get_proteksi_tab_url($tab['tab']);
              $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'protect';
              $tab_active = ($current_tab === $tab['tab']);
              ?>
              <a class="sad-apple__subnav-tab <?php echo $tab_active ? 'is-active' : ''; ?>" href="<?php echo esc_url($tab_url); ?>">
                <?php echo esc_html($tab['label']); ?>
              </a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
      </div>
      <div class="sad-apple__content">
      <?php
    }

    public static function close()
    {
      ?>
      </div>
    </div>
<?php
    }

    /**
     * Redirect old separate pages to tab-based URLs
     */
    public static function redirect_old_pages()
    {
        global $pagenow;
        
        if ($pagenow !== 'admin.php') {
            return;
        }
        
        $old_pages = array(
            'Sweetaddons_maintenance' => 'maintenance',
            'Sweetaddons_recaptcha'   => 'recaptcha',
            'Sweetaddons_block'       => 'block',
        );
        
        if (isset($_GET['page']) && isset($old_pages[$_GET['page']])) {
            $tab = $old_pages[$_GET['page']];
            wp_safe_redirect(admin_url('admin.php?page=Sweetaddons_protect&tab=' . $tab));
            exit;
        }
    }

    /**
     * Fix WordPress admin menu active state for Proteksi pages
     */
    public static function fix_parent_file($parent_file)
    {
        global $plugin_page;
        
        if ($plugin_page === 'Sweetaddons_protect') {
            $parent_file = 'custom_admin_options';
        }
        
        return $parent_file;
    }

    /**
     * Fix WordPress admin submenu active state for Proteksi pages
     */
    public static function hide_proteksi_submenu_items($submenu_file)
    {
        global $plugin_page;
        
        if ($plugin_page === 'Sweetaddons_protect') {
            $submenu_file = 'Sweetaddons_protect';
        }
        
        return $submenu_file;
    }

    /**
     * Remove submenu items from sidebar that are managed by tabs
     */
    public static function remove_admin_submenus()
    {
        global $submenu;
        
        // Remove individual items that are shown as tabs under Proteksi
        // But keep them accessible (just hidden from sidebar)
        $hidden_pages = array('Sweetaddons_maintenance', 'Sweetaddons_recaptcha', 'Sweetaddons_block');
        
        foreach ($hidden_pages as $page) {
            if (isset($submenu['custom_admin_options'])) {
                foreach ($submenu['custom_admin_options'] as $key => $item) {
                    if (isset($item[2]) && $item[2] === $page) {
                        unset($submenu['custom_admin_options'][$key]);
                        break;
                    }
                }
            }
        }
    }
}

// Register actions and filters
add_action('admin_init', array('Sweetaddons_Admin_Layout', 'redirect_old_pages'));
add_filter('parent_file', array('Sweetaddons_Admin_Layout', 'fix_parent_file'));
add_filter('submenu_file', array('Sweetaddons_Admin_Layout', 'hide_proteksi_submenu_items'));
add_action('admin_menu', array('Sweetaddons_Admin_Layout', 'remove_admin_submenus'), 999);
