# Sweet Addons Plugin

Plugin ini menambahkan rangkaian fitur keamanan, utilitas, SEO, statistik, dan UI untuk membantu mengelola serta menyesuaikan situs WordPress Anda.

## Fitur Utama

- Keamanan & Anti-Spam

  - Batasi percobaan login per IP (limit login attempts)
  - Blokir akses `wp-login.php` berdasarkan whitelist IP/negara dan redirect
  - CAPTCHA gambar teks: login, komentar, lostpassword, register, integrasi Contact Form 7
  - Tingkat kesulitan CAPTCHA yang dapat diatur: Mudah, Sedang, Sulit
  - Nonaktifkan XML-RPC
  - Nonaktifkan REST API (dengan pengecualian endpoint widget)

- Maintenance

  - Maintenance Mode (halaman 503 custom untuk pengunjung non-admin)
  - Pemeriksaan cepat: permalink, site icon, reCaptcha, pengaturan SEO, domain, auto-update plugin

- SEO

  - Meta tags: description, keywords, robots, canonical
  - Open Graph & Twitter Card
  - Schema.org: Article & WebSite
  - Meta box SEO di editor konten
  - Sitemap XML di `/sitemap.xml` dengan cache dan dukungan 304

- Statistik Pengunjung

  - Logging kunjungan, agregasi harian/bulanan
  - Statistik halaman dan referrer
  - Halaman admin dengan grafik dan tabel, tombol “Bangun Ulang Statistik”

- UI/UX

  - Widget WhatsApp mengambang: posisi, warna, ukuran, tooltip, gaya bubble
  - Breadcrumb shortcode dengan beberapa gaya tampilan

- Utilitas
  - Sembunyikan Admin Notices
  - Nonaktifkan komentar
  - Nonaktifkan Gutenberg dan aktifkan Classic Widgets
  - Hapus slug `category` dari permalink posting

## Shortcode

- `[statistic]` — menampilkan statistik pengunjung.
- `[breadcrumb]` — menampilkan breadcrumb navigasi (opsi: `separator`, `home_text`, `show_home`, `show_current`, `style`).
- `[sweet_recaptcha]` — menampilkan reCaptcha v2 sebagai shortcode.
- `[sweet_captcha]` — menampilkan CAPTCHA gambar teks sebagai shortcode.
- Contact Form 7: tag `recaptcha`

## Pengaturan Admin

- Dashboard Sweet Addons: status fitur, ringkasan konten/server, dan quick actions.
- Submenu: Umum, Maintenance Mode, Blokir Login, Proteksi Spam, Statistik, SEO, reCaptcha, White Label, WhatsApp.

## Pemasangan

- Unduh plugin dari repositori ini.
- Unggah direktori plugin ke `wp-content/plugins/` di situs WordPress Anda.
- Aktifkan plugin melalui menu “Plugins” di dasbor WordPress.

## Rekomendasi Plugin

Untuk memudahkan pemasangan dan pengelolaan plugin lain, Anda dapat menggunakan plugin [TGM Plugin Activation](https://github.com/TGMPA/TGM-Plugin-Activation) yang direkomendasikan.

## Lisensi

Plugin ini dirilis di bawah lisensi GPL-2.0+ - [Baca lebih lanjut](http://www.gnu.org/licenses/gpl-2.0.txt).
