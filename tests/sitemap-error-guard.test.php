<?php
/**
 * Harness uji: menjalankan generate_xml_sitemap_type() ASLI dari plugin dengan
 * stub WordPress, untuk membuktikan bug & perbaikan secara perilaku (bukan sekadar php -l).
 *
 * Skenario utama: get_term_link() mengembalikan WP_Error (term tidak resolve),
 * persis yang terjadi di produksi (crawler -> archive -> term tidak ada).
 * Karena esc_url() versi WordPress asli memanggil ltrim() pada argumennya,
 * WP_Error di jalur ini = fatal error "ltrim(): Argument #1 must be of type string".
 *
 * Keluar dengan kode 1 bila ada assertion yang gagal -> dipakai sebagai gate CI.
 *
 * Pemakaian:
 *   php tests/sitemap-error-guard.test.php [berkas-seo.php]
 */

define('HOUR_IN_SECONDS', 3600);

// ---------------------------------------------------------------------------
// Fixture: berapa term yang gagal resolve, dan berapa loc yang harus lolos.
// ---------------------------------------------------------------------------
$FIXTURE = array(
    'fail_first'   => true,   // term pertama -> WP_Error
    'term_count'   => 2,
    'expected_loc' => 1,
);

// ---------------------------------------------------------------------------
// Stub WordPress
// ---------------------------------------------------------------------------
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
function delete_transient($k) { return true; }
function get_terms($args)
{
    global $FIXTURE;
    $out = array();
    for ($i = 0; $i < (int) $FIXTURE['term_count']; $i++) {
        $t = new WP_Term();
        $t->term_id = $i + 1;
        $out[] = $t;
    }
    return $out;
}
function get_term_link($term)
{
    global $FIXTURE;
    static $n = 0;
    $n++;
    if ($FIXTURE['fail_first'] && $n === 1) {
        // Jalur fatal di produksi: term pertama tidak resolve.
        return new WP_Error('invalid_term', 'Invalid taxonomy.');
    }
    return 'https://example.com/kategori/ok/' . $n . '/';
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

// ---------------------------------------------------------------------------
// Runner assertion
// ---------------------------------------------------------------------------
$GLOBALS['__checks'] = array();
$GLOBALS['__failed'] = 0;

function check($label, $condition, $detail = '')
{
    $ok = (bool) $condition;
    if (!$ok) {
        $GLOBALS['__failed']++;
    }
    printf("  [%s] %s%s\n", $ok ? 'PASS' : 'FAIL', $label, ($detail !== '' ? ' -> ' . $detail : ''));
    return $ok;
}

// ---------------------------------------------------------------------------
// Muat kelas SEO asli, minus deklarasi kelas WP yang bentrok.
// Path opsional dari argv[1] supaya uji yang sama bisa dijalankan terhadap
// versi lama (pembuktian bahwa uji ini benar-benar mendeteksi bug: RED -> GREEN).
// ---------------------------------------------------------------------------
$target = isset($argv[1]) ? $argv[1] : dirname(__DIR__) . '/includes/class-sweetaddons-seo.php';
echo "Berkas diuji: " . $target . "\n";

if (!is_readable($target)) {
    fwrite(STDERR, "GAGAL: berkas tidak ditemukan\n");
    exit(1);
}

$src = file_get_contents($target);
$src = preg_replace('/^<\?php\s*/', '', $src);
if (!preg_match('/class\s+Sweetaddons_SEO/', $src)) {
    fwrite(STDERR, "GAGAL: kelas Sweetaddons_SEO tidak ditemukan\n");
    exit(1);
}

// Error PHP apa pun diperlakukan sebagai kegagalan uji (menangkap warning
// header() pada konteks CLI, dsb).
$GLOBALS['__php_errors'] = array();
set_error_handler(function ($no, $str, $file, $line) {
    $GLOBALS['__php_errors'][] = sprintf('%s (%s:%d)', $str, $file, $line);
    return true;
});

$seo = null;
try {
    eval($src);
    $seo = new Sweetaddons_SEO();
} catch (Throwable $e) {
    fwrite(STDERR, "GAGAL: " . get_class($e) . ': ' . $e->getMessage() . "\n");
    exit(1);
}

echo "Menjalankan generate_xml_sitemap_type('categories')...\n";
$xml = '';
try {
    $ref = new ReflectionMethod('Sweetaddons_SEO', 'generate_xml_sitemap_type');
    $ref->setAccessible(true);
    ob_start();
    $ref->invoke($seo, 'categories');
    $xml = ob_get_clean();
} catch (Throwable $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    restore_error_handler();
    // Inilah kegagalan yang ingin dideteksi: WP_Error masuk ke esc_url()/ltrim().
    fwrite(STDERR, "FATAL tertangkap: " . get_class($e) . ': ' . $e->getMessage() . "\n");
    fwrite(STDERR, "-> WP_Error tidak dijaga sebelum esc_url()\n");
    exit(1);
}

restore_error_handler();
$php_errors = $GLOBALS['__php_errors'];

$loc_count = substr_count($xml, '<loc>');
preg_match_all('#<loc>(.*?)</loc>#', $xml, $m);
$locs = isset($m[1]) ? $m[1] : array();

echo "\nHasil:\n";
check('Tidak fatal saat term pertama mengembalikan WP_Error', true);
check('Tidak ada warning/notice PHP', count($php_errors) === 0, implode(' | ', array_slice($php_errors, 0, 3)));
check('Jumlah <loc> sesuai harapan (' . $FIXTURE['expected_loc'] . ')', $loc_count === $FIXTURE['expected_loc'], "dapat $loc_count");
check('Panjang XML wajar (> 100 byte)', strlen($xml) > 100, strlen($xml) . ' byte');
check('Semua URL lolos berupa string http(s)', (bool) array_reduce($locs, function ($c, $u) {
    return $c && (bool) preg_match('#^https?://#', $u);
}, true), implode(', ', $locs));

echo "\nRingkasan: " . ($GLOBALS['__failed'] === 0 ? "SEMUA PASS" : $GLOBALS['__failed'] . " GAGAL") . "\n";
echo "XML (" . strlen($xml) . " byte), loc: " . implode(', ', $locs) . "\n";

exit($GLOBALS['__failed'] === 0 ? 0 : 1);
