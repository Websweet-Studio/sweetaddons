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
            'page'  => 'Sweetaddons_spam',
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
        return array(
          array('page' => 'Sweetaddons_spam', 'label' => 'Proteksi'),
          array('page' => 'Sweetaddons_maintenance', 'label' => 'Maintenance'),
          array('page' => 'Sweetaddons_recaptcha', 'label' => 'reCaptcha'),
          array('page' => 'Sweetaddons_block', 'label' => 'Blokir Login'),
        );
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
              $tab_url = admin_url('admin.php?page=' . $tab['page']);
              $tab_active = ($active_page === $tab['page']);
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
  }
