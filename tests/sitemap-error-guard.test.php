<?php
/**
 * Harness uji: menjalankan generate_xml_sitemap_type() ASLI dari plugin
 * dengan stub WordPress, untuk membuktikan bug & perbaikan secara nyata.
 *
 * Skenario: get_term_link() mengembalikan WP_Error (term tidak resolve),
 * persis yang terjadi di produksi (crawler -> archive -> term tidak ada).
 */

define('HOUR_IN_SECONDS', 3600);

class WP_Error
{
    private $code, $message;
    public function __construct($code = '', $message = '') { $this->code = $code; $this->message = $message; }
    public function get_error_code() { return $this->code; }
    public function get_error_message() { return $this->message; }
}
class WP_Term { public $term_id = 1; public $name = 'Kategori'; public $taxonomy = 'category'; }
class WP_Post { public $ID = 1; }
class WP_Query { public $posts = array(); public function have_posts() { return false; } }

function is_wp_error($t) { return $t instanceof WP_Error; }
function esc_url($u) { return is_wp_error($u) ? ltrim($u) : $u; }   // perilaku WP asli
function esc_attr($s) { return htmlspecialchars((string) $s, ENT_QUOTES); }
function esc_html($s) { return htmlspecialchars((string) $s, ENT_QUOTES); }
function esc_url_raw($u) { return $u; }
function get_query_var($k) { return $k === 'sweetaddons_sitemap_page' ? 1 : ''; }
function get_transient($k) { return false; }
function set_transient($k, $v, $t) { return true; }
function get_terms($args) {
    return array(new WP_Term(), new WP_Term());      // 2 kategori
}
function get_term_link($term) {
    static $n = 0;
    $n++;
    // Term PERTAMA gagal resolve -> inilah jalur fatal di produksi.
    return $n === 1 ? new WP_Error('invalid_term', 'Invalid taxonomy.') : 'https://example.com/kategori/ok/';
}
function get_posts($a) { return array(); }
function get_permalink($id) { return 'https://example.com/p/1/'; }
function get_post_field($f, $id) { return '2026-01-01 00:00:00'; }
function get_post_meta($id, $k, $s) { return ''; }
function status_header($c) {}
function add_action(...$a) {}
function add_filter(...$a) {}
function is_admin() { return false; }
function is_feed() { return false; }
function is_robots() { return false; }
function is_trackback() { return false; }
function is_singular($t = '') { return false; }
function is_home() { return false; }
function is_front_page() { return false; }
function is_category() { return false; }
function is_tag() { return false; }
function is_post_type_archive() { return false; }
function is_tax() { return false; }
function is_author() { return false; }
function is_day() { return false; }
function is_month() { return false; }
function is_year() { return false; }
function get_post_type() { return ''; }
function get_queried_object_id() { return 0; }
function get_queried_object() { return null; }
function get_option($k, $d = false) { return $d; }
function shortcode_atts(...$a) { return array(); }
function home_url($p = '/') { return 'https://example.com' . $p; }
function get_bloginfo($k = '') { return 'Contoh'; }

// Muat kelas SEO asli, minus deklarasi kelas WP yang bentrok.
// Path opsional dari argv[1] supaya uji yang sama bisa dijalankan
// terhadap versi lama (pembuktian bahwa uji ini benar-benar mendeteksi bug).
$target = isset($argv[1]) ? $argv[1] : dirname(__DIR__) . '/includes/class-sweetaddons-seo.php';
echo "Berkas diuji: " . $target . "\n";
$src = file_get_contents($target);
$src = preg_replace('/^<\?php\s*/', '', $src);
if (preg_match('/class\s+Sweetaddons_SEO/', $src) === false) {
    fwrite(STDERR, "GAGAL: kelas Sweetaddons_SEO tidak ditemukan\n");
    exit(2);
}
eval($src);

$seo = new Sweetaddons_SEO();

// Akses method private generate_xml_sitemap_type via reflection.
$ref = new ReflectionMethod('Sweetaddons_SEO', 'generate_xml_sitemap_type');
$ref->setAccessible(true);

echo "Menjalankan generate_xml_sitemap_type('categories')...\n";
ob_start();
$ref->invoke($seo, 'categories');
$xml = ob_get_clean();

echo "TIDAK FATAL. Panjang XML: " . strlen($xml) . " byte\n";
echo "Jumlah <loc>: " . substr_count($xml, '<loc>') . " (harus 1, karena term pertama error)\n";
echo "URL yang lolos: ";
preg_match_all('#<loc>(.*?)</loc>#', $xml, $m);
echo implode(', ', $m[1]) . "\n";
