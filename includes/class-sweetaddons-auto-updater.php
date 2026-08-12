<?php

/**
 * GitHub release updater for Sweet Addons.
 *
 * @link       https://websweetstudio.com
 * @since      2.2.2
 *
 * @package    sweetaddons
 * @subpackage sweetaddons/includes
 */

class Sweetaddons_Auto_Updater
{
  /**
   * Plugin main file absolute path.
   *
   * @var string
   */
  private $plugin_file;

  /**
   * Plugin basename, e.g. sweetaddons/sweetaddons.php.
   *
   * @var string
   */
  private $plugin_basename;

  /**
   * Plugin slug, e.g. sweetaddons.
   *
   * @var string
   */
  private $plugin_slug;

  /**
   * Current installed version.
   *
   * @var string
   */
  private $current_version;

  /**
   * GitHub API endpoint for latest release.
   *
   * @var string
   */
  private $release_api_url = 'https://api.github.com/repos/Websweet-Studio/sweetaddons/releases/latest';

  /**
   * Public releases page.
   *
   * @var string
   */
  private $release_page_url = 'https://github.com/Websweet-Studio/sweetaddons/releases';

  /**
   * Site transient key for cached release data.
   *
   * @var string
   */
  private $cache_key = 'sweetaddons_github_latest_release';

  /**
   * Cache lifetime in seconds.
   *
   * @var int
   */
  private $cache_ttl = 21600;

  /**
   * Register updater hooks.
   *
   * @param string $plugin_file Absolute path to the main plugin file.
   * @param string $current_version Current installed plugin version.
   */
  public function __construct($plugin_file, $current_version)
  {
    $this->plugin_file = $plugin_file;
    $this->plugin_basename = plugin_basename($plugin_file);
    $this->plugin_slug = dirname($this->plugin_basename);
    $this->current_version = $this->normalize_version($current_version);

    add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
    add_filter('plugins_api', array($this, 'plugin_information'), 20, 3);
    add_action('upgrader_process_complete', array($this, 'purge_cache'), 10, 2);
    add_action('admin_post_sweetaddons_check_update', array($this, 'handle_manual_check_update'));
  }

  public function handle_manual_check_update()
  {
    if (!current_user_can('manage_options')) {
      wp_die(esc_html__('You do not have permission to perform this action.', 'sweetaddons'));
    }

    check_admin_referer('sweetaddons_check_update');

    delete_site_transient($this->cache_key);
    delete_site_transient('update_plugins');

    if (function_exists('wp_clean_plugins_cache')) {
      wp_clean_plugins_cache(true);
    }

    if (function_exists('wp_update_plugins')) {
      wp_update_plugins();
    }

    $transient = get_site_transient('update_plugins');
    $has_update = is_object($transient) && isset($transient->response) && is_array($transient->response) && isset($transient->response[$this->plugin_basename]);

    $redirect = wp_get_referer();
    if (!$redirect) {
      $redirect = admin_url('admin.php?page=Sweetaddons_umum');
    }

    $redirect = remove_query_arg(array('sweetaddons_update_check', 'sweetaddons_has_update'), $redirect);
    $redirect = add_query_arg(
      array(
        'sweetaddons_update_check' => '1',
        'sweetaddons_has_update' => $has_update ? '1' : '0',
      ),
      $redirect
    );

    wp_safe_redirect($redirect);
    exit;
  }

  /**
   * Inject update metadata into WordPress update transient.
   *
   * @param object $transient WordPress update transient.
   * @return object
   */
  public function check_for_updates($transient)
  {
    if (!is_object($transient) || empty($transient->checked)) {
      return $transient;
    }

    if (!isset($transient->checked[$this->plugin_basename])) {
      return $transient;
    }

    $release = $this->get_latest_release();
    if (!$release) {
      return $transient;
    }

    $release_version = $this->get_release_version($release);
    if (!$release_version) {
      return $transient;
    }

    if (version_compare($release_version, $this->current_version, '>')) {
      $update = $this->build_update_item($release, $release_version);

      if ($update) {
        $transient->response[$this->plugin_basename] = $update;
        unset($transient->no_update[$this->plugin_basename]);
      }
    } else {
      $transient->no_update[$this->plugin_basename] = $this->build_no_update_item($release, $release_version);
      unset($transient->response[$this->plugin_basename]);
    }

    return $transient;
  }

  /**
   * Provide plugin info modal content.
   *
   * @param false|object|array $result Current result.
   * @param string             $action API action name.
   * @param object             $args Request arguments.
   * @return false|object|array
   */
  public function plugin_information($result, $action, $args)
  {
    if ($action !== 'plugin_information' || empty($args->slug) || $args->slug !== $this->plugin_slug) {
      return $result;
    }

    $release = $this->get_latest_release();
    if (!$release) {
      return $result;
    }

    $release_version = $this->get_release_version($release);
    $download_url = $this->get_release_package_url($release);
    $release_notes = isset($release['body']) ? wp_kses_post(wpautop(make_clickable($release['body']))) : '';

    return (object) array(
      'name' => 'Sweet Addons',
      'slug' => $this->plugin_slug,
      'version' => $release_version ? $release_version : $this->current_version,
      'author' => '<a href="https://websweetstudio.com">WebsweetStudio</a>',
      'author_profile' => 'https://websweetstudio.com',
      'homepage' => $this->release_page_url,
      'download_link' => $download_url,
      'requires' => '',
      'tested' => '',
      'requires_php' => '',
      'last_updated' => isset($release['published_at']) ? $release['published_at'] : '',
      'sections' => array(
        'description' => '<p>Sweet Addons update metadata is served from GitHub Releases.</p>',
        'changelog' => $release_notes ? $release_notes : '<p>No release notes available.</p>',
      ),
      'external' => true,
    );
  }

  /**
   * Clear cached release metadata after a plugin update.
   *
   * @param WP_Upgrader $upgrader_object Upgrader instance.
   * @param array       $options Upgrader options.
   * @return void
   */
  public function purge_cache($upgrader_object, $options)
  {
    if (empty($options['action']) || $options['action'] !== 'update') {
      return;
    }

    if (empty($options['type']) || $options['type'] !== 'plugin') {
      return;
    }

    if (empty($options['plugins']) || !is_array($options['plugins'])) {
      return;
    }

    if (in_array($this->plugin_basename, $options['plugins'], true)) {
      delete_site_transient($this->cache_key);
    }
  }

  /**
   * Get the latest release metadata from cache or GitHub.
   *
   * @return array|null
   */
  private function get_latest_release()
  {
    $cached_release = get_site_transient($this->cache_key);
    if (is_array($cached_release) && !empty($cached_release['tag_name'])) {
      return $cached_release;
    }

    $response = wp_remote_get(
      $this->release_api_url,
      array(
        'timeout' => 20,
        'headers' => array(
          'Accept' => 'application/vnd.github+json',
          'User-Agent' => 'SweetAddons/' . $this->current_version,
        ),
      )
    );

    if (is_wp_error($response)) {
      return null;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
      return null;
    }

    $release = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($release) || empty($release['tag_name'])) {
      return null;
    }

    if (!empty($release['draft']) || !empty($release['prerelease'])) {
      return null;
    }

    set_site_transient($this->cache_key, $release, $this->cache_ttl);

    return $release;
  }

  /**
   * Build update item consumed by WordPress core.
   *
   * @param array  $release GitHub release payload.
   * @param string $release_version Normalized version number.
   * @return object|null
   */
  private function build_update_item($release, $release_version)
  {
    $download_url = $this->get_release_package_url($release);
    if (!$download_url) {
      return null;
    }

    return (object) array(
      'id' => $this->release_page_url,
      'slug' => $this->plugin_slug,
      'plugin' => $this->plugin_basename,
      'new_version' => $release_version,
      'url' => isset($release['html_url']) ? $release['html_url'] : $this->release_page_url,
      'package' => $download_url,
      'icons' => array(),
      'banners' => array(),
      'banners_rtl' => array(),
      'tested' => '',
      'requires_php' => '',
      'compatibility' => new stdClass(),
    );
  }

  /**
   * Build no-update item for WordPress core.
   *
   * @param array  $release GitHub release payload.
   * @param string $release_version Normalized version number.
   * @return object
   */
  private function build_no_update_item($release, $release_version)
  {
    return (object) array(
      'id' => $this->release_page_url,
      'slug' => $this->plugin_slug,
      'plugin' => $this->plugin_basename,
      'new_version' => $release_version,
      'url' => isset($release['html_url']) ? $release['html_url'] : $this->release_page_url,
      'package' => '',
      'icons' => array(),
      'banners' => array(),
      'banners_rtl' => array(),
      'tested' => '',
      'requires_php' => '',
      'compatibility' => new stdClass(),
    );
  }

  /**
   * Pick the first uploaded ZIP asset from the release.
   *
   * @param array $release GitHub release payload.
   * @return string
   */
  private function get_release_package_url($release)
  {
    if (empty($release['assets']) || !is_array($release['assets'])) {
      return '';
    }

    foreach ($release['assets'] as $asset) {
      if (empty($asset['browser_download_url']) || empty($asset['name'])) {
        continue;
      }

      if (!empty($asset['state']) && $asset['state'] !== 'uploaded') {
        continue;
      }

      if (substr(strtolower($asset['name']), -4) === '.zip') {
        return $asset['browser_download_url'];
      }
    }

    return '';
  }

  /**
   * Normalize release tag to a semantic version string.
   *
   * @param array $release GitHub release payload.
   * @return string
   */
  private function get_release_version($release)
  {
    if (empty($release['tag_name'])) {
      return '';
    }

    return $this->normalize_version($release['tag_name']);
  }

  /**
   * Strip common Git tag prefixes.
   *
   * @param string $version Raw version string.
   * @return string
   */
  private function normalize_version($version)
  {
    return ltrim(trim((string) $version), "vV \t\n\r\0\x0B");
  }
}
