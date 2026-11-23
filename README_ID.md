# CargoTrace — Platform Visibilitas & Pelacakan Pengiriman Global

> **Pilihan Bahasa:** [Versi Bahasa Indonesia (Halaman Ini)](#) | [Read English Documentation in README.md](README.md)

**CargoTrace** adalah aplikasi pelacakan kargo internasional real-time (*global cargo visibility*), evaluasi keterlambatan estimasi kedatangan (*deterministic ETA delay evaluation*), manajemen anomali logistik (*operational exception management*), serta visualisasi rute maritim berbasis peta interaktif. 

Aplikasi ini dibangun menggunakan arsitektur **Laravel 12**, **PHP 8.2+**, **Tailwind CSS v4**, **Leaflet.js**, dan **Chart.js** untuk memenuhi standar proyek UAS Pemrograman Web tingkat lanjut.

---

## Akses Live & Tautan Deployment

| Layanan | Tautan / URL |
| :--- | :--- |
| **Website Produksi (Vercel)** | [https://cargo-trace-teal.vercel.app](https://cargo-trace-teal.vercel.app) |
| **Repository GitHub** | [https://github.com/safiraazahrasaragih43-cloud/CargoTrace](https://github.com/safiraazahrasaragih43-cloud/CargoTrace) |
| **Status REST API v1** | [https://cargo-trace-teal.vercel.app/api/v1/health](https://cargo-trace-teal.vercel.app/api/v1/health) |

---

## Akun Demo & Matriks Hak Akses (RBAC)

Sistem menerapkan pembagian hak akses bertingkat (*Role-Based Access Control* / RBAC) dengan akun bawaan (kata sandi untuk semua akun: `password`):

| Role | Email | Tampilan Antarmuka | Hak Akses & Fitur Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | Bilah Sisi (Konsol Admin) | Akses penuh CRUD, Hapus data pelabuhan & kargo, Log Audit Sistem, Manajemen Pengguna |
| **Operator Logistik** | `operator@cargotrace.com` | Bilah Sisi (Konsol Operator) | Input kargo baru, update status & pos pemeriksaan (checkpoints), kelola kendala/exception, tambah rute |
| **Pelanggan / Klien** | `customer@cargotrace.com` | Navigasi Atas (Portal Klien) | Melacak kargo milik pribadi, melihat timeline riwayat, cek kondisi cuaca pelabuhan tujuan, notifikasi |

### Contoh Nomor Resi untuk Uji Pelacakan Publik:
- `TRK-EXP-2026-001` — Koridor Asia Tenggara → Eropa (Tanjung Priok ke Rotterdam)
- `TRK-SEA-2026-002` — Koridor Asia → Amerika Utara (Shanghai ke Los Angeles)
- `TRK-AIR-2026-003` — Koridor Asia Tenggara → Australia (Changi ke Sydney)

---

## Fitur-Fitur Utama

### 1. Pelacakan Kargo Publik (Quick Track)
- Pelanggan dapat melacak status kargo kapan saja tanpa perlu login cukup dengan memasukkan nomor resi.
- Menampilkan timeline pos pemeriksaan kronologis (*append-only chronological tracking timeline*).
- Informasi terperinci mengenai kapal/maskapai, estimasi kedatangan (ETA), serta integrasi **data cuaca real-time** di pelabuhan tujuan via Open-Meteo API.

### 2. Peta Interaktif 2 Mode (Satelit & Default)
- **Mode Satelit (Citra Satelit Nyata):** Menampilkan pemandangan permukaan bumi yang realistis dengan laut biru bertekstur dan daratan hijau asri.
- **Mode Default:** Peta jalan bersih berstandar OpenStreetMap.
- **Rute Maritim Padat:** Menghubungkan pelabuhan asal dan tujuan dengan garis lurus yang tegas.
- **Telemetri Jarak GPS Otomatis:** Menghitung jarak tempuh maritim akurat menggunakan **Formula Matematis Haversine** dalam satuan Kilometer (km) dan Mil Laut (Nautical Miles / NM).

### 3. Dukungan Dua Bahasa (Bilingual: Indonesia & English)
- Tombol pengganti bahasa 1-klik di bagian atas navigasi.
- Seluruh antarmuka—mulai dari Beranda, Form Pencarian, Login, Dashboard, Manifest, Peta, hingga Footer—sepenuhnya diterjemahkan secara dinamis.

### 4. Siklus Hidup Pengiriman & Evaluasi Keterlambatan (ETA Engine)
- Menjalankan mesin status (*State Machine*) berurutan: `CREATED → BOOKED → PICKED_UP → IN_TRANSIT → OUT_FOR_DELIVERY → DELIVERED`.
- Penanganan anomali operasional: `IN_TRANSIT → EXCEPTION → IN_TRANSIT`.
- Penghitungan otomatis keterlambatan kedatangan:
  - `0 jam` : Tepat Waktu (*On-Time*)
  - `1–6 jam` : Keterlambatan Rendah (*Low*)
  - `7–24 jam` : Keterlambatan Sedang (*Medium*)
  - `> 24 jam` : Keterlambatan Tinggi (*High*)

### 5. Koridor Logistik Internasional Terkonfigurasi
| Koridor | Wilayah Asal | Wilayah Tujuan | Ambang Delay | Exception Utama | Fokus KPI |
| :--- | :--- | :--- | :---: | :--- | :--- |
| **SEA → Eropa** | Indonesia / Singapura / Asia Tenggara | Belanda / Jerman / Inggris | **12 Jam** | `CUSTOMS_HOLD` (Bea Cukai) | Ketepatan Waktu (On-Time) |
| **Asia → Amerika Utara** | Tiongkok / Jepang / Korea | Amerika Serikat / Kanada | **18 Jam** | `WEATHER_DELAY` (Cuaca Ekstrem) | Rata-rata Durasi Delay |
| **SEA → Australia** | Singapura / Indonesia | Australia / Selandia Baru | **24 Jam** | `DOCUMENTATION_ISSUE` (Dokumen) | Kecepatan Resolusi Kendala |

### 6. Layanan REST API v1
- Dilengkapi endpoint publik dan terproteksi token Bearer (*Laravel Sanctum*).
- Halaman status interaktif di `/api/v1/health` untuk uji coba pemanggilan API langsung melalui browser.

---

## Arsitektur & Dokumentasi Teknis

- **Dokumentasi ERD & Kamus Data:** Lihat [`docs/ERD.md`](docs/ERD.md).
- **Dokumentasi Arsitektur & Atribusi:** Lihat [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md).
- **Dokumentasi API & Endpoint:** Lihat [`docs/API_DOCS.md`](docs/API_DOCS.md).
- **Koleksi Postman:** File siap pakai di [`docs/CargoTrace_API_v1.postman_collection.json`](docs/CargoTrace_API_v1.postman_collection.json).

---

## Panduan Instalasi Lokal

### Kebutuhan Sistem
- **PHP >= 8.2** (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `sqlite3`)
- **Composer**
- **Node.js 18+ & npm**
- **MySQL 8+ / MariaDB**

### Langkah-Langkah Instalasi

1. **Clone repository dan masuk ke direktori proyek:**
   ```bash
   git clone https://github.com/safiraazahrasaragih43-cloud/CargoTrace.git
   cd CargoTrace
   ```

2. **Pasang dependensi PHP dan JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`):**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi database lokal pada file `.env` (misalnya: `DB_DATABASE=cargotrace_db`, `DB_USERNAME=root`, `DB_PASSWORD=`).

4. **Jalankan migrasi database dan pengisian data contoh (seeder):**
   ```bash
   php artisan migrate --seed
   ```

5. **Kompilasi asset antarmuka (Tailwind CSS v4 & Leaflet):**
   ```bash
   npm run build
   ```

6. **Jalankan server lokal:**
   ```bash
   php artisan serve
   ```
   Buka peramban (*browser*) dan akses `http://127.0.0.1:8000`.

---

## Pengujian Otomatis (Automated Testing)

Seluruh pengujian unit dan fitur dijalankan menggunakan **PHPUnit** dengan konfigurasi database SQLite in-memory:

```bash
php vendor/phpunit/phpunit/phpunit
```

**Status Pengujian:**
- **84 Tests, 482 Assertions (Lulus 100% / Passed)**
- Cakupan uji meliputi: Autentikasi token, Role Middleware, Hak Akses Policy, Siklus Hidup Kargo, Perhitungan Delay ETA, Peta & Integrasi Cuaca Eksternal, Form Validation, serta Pencegahan Kerentanan Keamanan (SQLi, XSS, CSRF, PII Leaks).

---

## Koleksi Postman & Dokumentasi API

- **Koleksi Postman:** File siap pakai dapat diimpor langsung dari [`docs/CargoTrace_API_v1.postman_collection.json`](docs/CargoTrace_API_v1.postman_collection.json).
- **Dokumentasi Lengkap Endpoint:** Tersedia pada file [`docs/API_DOCS.md`](docs/API_DOCS.md).

---

## Standar Keamanan Aplikasi

- **CSRF Token:** Seluruh form dilindungi dari serangan Cross-Site Request Forgery.
- **SQL Injection Prevention:** Menggunakan PDO Parameterized Queries dan Eloquent ORM.
- **Sanitasi Output (Anti-XSS):** Semua data dinamis di-escape dengan aman pada Blade maupun DOM/Leaflet.
- **Proteksi Akses Role:** Pencegahan manipulasi role saat registrasi akun baru.
- **Header Keamanan HTTP:** Menegakkan `Strict-Transport-Security`, `X-Content-Type-Options: nosniff`, dan `X-Frame-Options: SAMEORIGIN`.

---

© 2026 **CargoTrace** — Dibuat untuk pemenuhan Ujian Akhir Semester (UAS) Pemrograman Web.
