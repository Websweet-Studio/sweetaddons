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
- Contact Form 7: tag `recaptcha` — menampilkan reCaptcha pada form Contact Form 7.

## Pengaturan Admin

- Dashboard Sweet Addons: status fitur, ringkasan konten/server, dan quick actions.
- Submenu: Umum, Maintenance Mode, Blokir Login, Proteksi Spam, Statistik, SEO, reCaptcha, White Label, WhatsApp.

## Pemasangan

- Unduh plugin dari repositori ini.
- Unggah direktori plugin ke `wp-content/plugins/` di situs WordPress Anda.
- Aktifkan plugin melalui menu “Plugins” di dasbor WordPress.

## Pengembangan

Perintah lokal:

```bash
bash scripts/lint.sh   # php -l semua berkas PHP
bash scripts/test.sh   # jalankan semua harness di tests/*.test.php
```

Harness uji menjalankan kelas plugin yang sebenarnya di atas stub WordPress dan
mengembalikan exit code != 0 bila ada assertion gagal — jadi bisa dipakai sebagai gate.
Contoh: `php tests/sitemap-error-guard.test.php` (tambahkan path berkas sebagai argumen
untuk menguji revisi lain, misal versi sebelum perbaikan).

Alur rilis: ubah `Version:` pada `sweetaddons.php`, lalu push ke `main`.
Workflow `auto-release.yml` akan:

1. `verify` — PHP lint + semua harness uji. **Rilis dibatalkan bila gagal.**
2. `release` — build ZIP (`npm run zip`), verifikasi isi paket (tidak ada berkas
   pengembangan yang bocor, versi header & konstanta sinkron), lalu buat GitHub Release.

## Rekomendasi Plugin

Untuk memudahkan pemasangan dan pengelolaan plugin lain, Anda dapat menggunakan plugin [TGM Plugin Activation](https://github.com/TGMPA/TGM-Plugin-Activation) yang direkomendasikan.

## Lisensi

Plugin ini dirilis di bawah lisensi GPL-2.0+ - [Baca lebih lanjut](http://www.gnu.org/licenses/gpl-2.0.txt).
