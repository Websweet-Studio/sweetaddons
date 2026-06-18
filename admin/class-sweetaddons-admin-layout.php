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
        'page'  => 'Sweetaddons_whatsapp',
        'label' => 'WhatsApp',
      ),
      array(
        'page'  => 'Sweetaddons_optimasi',
        'label' => 'Optimasi',
      ),
    );
  }

  public static function get_proteksi_subnav()
  {
    return array(
      array('tab' => 'protect', 'label' => 'Proteksi'),
      array('tab' => 'maintenance', 'label' => 'Maintenance'),
      array('tab' => 'recaptcha', 'label' => 'reCaptcha'),
      array('tab' => 'block', 'label' => 'Blokir Login'),
      array('tab' => 'whitelabel', 'label' => 'White Label'),
    );
  }

  public static function get_umum_subnav()
  {
    return array(
      array('tab' => 'general', 'label' => 'Umum'),
      array('tab' => 'customlogin', 'label' => 'Custom Login'),
    );
  }

  public static function get_optimasi_subnav()
  {
    return array(
      array('tab' => 'redis', 'label' => 'Redis'),
      array('tab' => 'dbcleaner', 'label' => 'DB Cleaner'),
      array('tab' => 'headcleanup', 'label' => 'Head Cleanup'),
    );
  }

  public static function get_whatsapp_subnav()
  {
    return array(
      array('tab' => 'pengaturan', 'label' => 'Pengaturan'),
      array('tab' => 'style', 'label' => 'Style'),
    );
  }

  public static function get_whatsapp_tab_url($tab)
  {
    return admin_url('admin.php?page=Sweetaddons_whatsapp&subtab=' . $tab);
  }

  public static function get_seo_subnav()
  {
    return array(
      array('tab' => 'general', 'label' => 'General'),
      array('tab' => 'social', 'label' => 'Social Media'),
    );
  }

  public static function get_seo_tab_url($tab)
  {
    return admin_url('admin.php?page=Sweetaddons_seo&subtab=' . $tab);
  }

  public static function get_proteksi_tab_url($tab)
  {
    return admin_url('admin.php?page=Sweetaddons_protect&tab=' . $tab);
  }

  public static function get_umum_tab_url($tab)
  {
    return admin_url('admin.php?page=Sweetaddons_umum&tab=' . $tab);
  }

  public static function get_optimasi_tab_url($tab)
  {
    return admin_url('admin.php?page=Sweetaddons_optimasi&tab=' . $tab);
  }

  public static function open($page_title, $active_page, $subnav = array())
  {
    $plugin_name = self::get_plugin_name();
    $pages = self::get_pages();
?>
    <div class="wrap vd-ons sweetaddons-dashboard sweetaddons">
      <div class="sweetaddons__globalnav">
        <div class="sweetaddons__brand">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cable">
            <path d="M17 19a1 1 0 0 1-1-1v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a1 1 0 0 1-1 1z" />
            <path d="M17 21v-2" />
            <path d="M19 14V6.5a1 1 0 0 0-7 0v11a1 1 0 0 1-7 0V10" />
            <path d="M21 21v-2" />
            <path d="M3 5V3" />
            <path d="M4 10a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2a2 2 0 0 1-2 2z" />
            <path d="M7 5V3" />
          </svg>
          <span><?php echo esc_html($plugin_name); ?></span>
        </div>
        <nav class="sweetaddons__nav" aria-label="Sweet Addons Navigation">
          <?php foreach ($pages as $item) : ?>
            <?php
            $url = admin_url('admin.php?page=' . $item['page']);
            $is_active = ($active_page === $item['page']);
            ?>
            <a class="sweetaddons__navlink <?php echo $is_active ? 'is-active' : ''; ?>" href="<?php echo esc_url($url); ?>">
              <?php echo esc_html($item['label']); ?>
            </a>
          <?php endforeach; ?>
        </nav>
      </div>
      <div class="sweetaddons__subnav">
        <div class="sweetaddons__subnav-title"><?php echo esc_html($page_title); ?></div>
        <?php if (!empty($subnav)) : ?>
          <nav class="sweetaddons__subnav-tabs" aria-label="Sub Navigation">
            <?php foreach ($subnav as $tab) : ?>
              <?php
              if (isset($tab['page'])) {
                $tab_url = admin_url('admin.php?page=' . $tab['page']);
              } elseif ($active_page === 'Sweetaddons_protect') {
                $tab_url = self::get_proteksi_tab_url($tab['tab']);
                $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
              } elseif ($active_page === 'Sweetaddons_whatsapp') {
                $tab_url = self::get_whatsapp_tab_url($tab['tab']);
                $current_tab = isset($_GET['subtab']) ? sanitize_key($_GET['subtab']) : '';
              } elseif ($active_page === 'Sweetaddons_seo') {
                $tab_url = self::get_seo_tab_url($tab['tab']);
                $current_tab = isset($_GET['subtab']) ? sanitize_key($_GET['subtab']) : '';
              } elseif ($active_page === 'Sweetaddons_optimasi') {
                $tab_url = self::get_optimasi_tab_url($tab['tab']);
                $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
              } else {
                $tab_url = self::get_umum_tab_url($tab['tab']);
                $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
              }
              if (!isset($current_tab)) {
                $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';
              }
              $default_tab = $subnav[0]['tab'];
              $tab_active = ($current_tab === $tab['tab']) || (empty($current_tab) && $tab['tab'] === $default_tab);
              ?>
              <a class="sweetaddons__subnav-tab <?php echo $tab_active ? 'is-active' : ''; ?>" href="<?php echo esc_url($tab_url); ?>">
                <?php echo esc_html($tab['label']); ?>
              </a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
      </div>
      <div class="sweetaddons__content">
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

      // Proteksi tab redirects
      $proteksi_redirects = array(
        'Sweetaddons_maintenance' => 'maintenance',
        'Sweetaddons_recaptcha'   => 'recaptcha',
        'Sweetaddons_block'       => 'block',
        'Sweetaddons_whitelabel'  => 'whitelabel',
      );

      if (isset($_GET['page']) && isset($proteksi_redirects[$_GET['page']])) {
        $tab = $proteksi_redirects[$_GET['page']];
        wp_safe_redirect(admin_url('admin.php?page=Sweetaddons_protect&tab=' . $tab));
        exit;
      }

      // Umum tab redirects
      $umum_redirects = array(
        'Sweetaddons_login_customizer' => 'customlogin',
      );

      if (isset($_GET['page']) && isset($umum_redirects[$_GET['page']])) {
        $tab = $umum_redirects[$_GET['page']];
        wp_safe_redirect(admin_url('admin.php?page=Sweetaddons_umum&tab=' . $tab));
        exit;
      }

      // Optimasi tab redirects
      $optimasi_redirects = array(
        'Sweetaddons_db_cleaner' => 'dbcleaner',
      );

      if (isset($_GET['page']) && isset($optimasi_redirects[$_GET['page']])) {
        $tab = $optimasi_redirects[$_GET['page']];
        wp_safe_redirect(admin_url('admin.php?page=Sweetaddons_optimasi&tab=' . $tab));
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
      if ($plugin_page === 'Sweetaddons_umum') {
        $parent_file = 'custom_admin_options';
      }
      if ($plugin_page === 'Sweetaddons_optimasi') {
        $parent_file = 'custom_admin_options';
      }

      return $parent_file;
    }

    /**
     * Fix WordPress admin submenu active state
     */
    public static function hide_proteksi_submenu_items($submenu_file)
    {
      global $plugin_page;

      if ($plugin_page === 'Sweetaddons_protect') {
        $submenu_file = 'Sweetaddons_protect';
      }
      if ($plugin_page === 'Sweetaddons_umum') {
        $submenu_file = 'Sweetaddons_umum';
      }
      if ($plugin_page === 'Sweetaddons_optimasi') {
        $submenu_file = 'Sweetaddons_optimasi';
      }

      return $submenu_file;
    }

    /**
     * Remove submenu items from sidebar that are managed by tabs
     */
    public static function remove_admin_submenus()
    {
      global $submenu;

      $hidden_pages = array('Sweetaddons_maintenance', 'Sweetaddons_recaptcha', 'Sweetaddons_block', 'Sweetaddons_whitelabel', 'Sweetaddons_db_cleaner', 'Sweetaddons_login_customizer');

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
