# Architecture Documentation — CargoTrace

Dokumentasi arsitektur perangkat lunak, pola desain (*design patterns*), integrasi layanan eksternal, dan atribusi pustaka untuk platform **CargoTrace** sesuai spesifikasi Slide 14, 15, 18 UAS Pemrograman Web 2.

---

## 1. Diagram Alur Arsitektur (Layered MVC + Service Layer)

CargoTrace memisahkan logika HTTP dari logika bisnis secara ketat menggunakan pola **Service Layer**:

```mermaid
flowchart TD
    Client["Klien Web / Browser & REST API Consumer"] -->|HTTP / HTTPS Requests| Route["Routing & Middleware Layer<br/>(RoleMiddleware, SecurityHeaders, Sanctum, CSRF)"]
    
    subgraph Presentation ["Presentation Layer"]
        Route -->|Authorized Request| Controller["Controllers<br/>(Web Controllers & Api/V1 Controllers)"]
        Controller -->|Delegasi Form Validation| FormRequest["FormRequest Classes<br/>(ShipmentRequest, TrackingEventRequest, etc.)"]
        Controller -->|Pemeriksaan Otorisasi| Policies["Granular Policies<br/>(ShipmentPolicy, CarrierPolicy, etc.)"]
    end

    subgraph Domain ["Business Domain & Service Layer"]
        Controller -->|Memanggil Logika Bisnis| Services["Service Layer"]
        Services --> ShipmentService["ShipmentService<br/>(State Machine Lifecycle)"]
        Services --> TrackingService["TrackingService<br/>(Append-Only Timeline)"]
        Services --> EtaService["EtaService<br/>(Deterministic Delays & Corridors)"]
        Services --> ExceptionService["ExceptionService<br/>(Exceptions & Resolution)"]
        Services --> WeatherService["WeatherService<br/>(Open-Meteo Telemetry)"]
        Services --> GeocodingService["GeocodingService<br/>(Nominatim UN/LOCODE)"]
    end

    subgraph DataAccess ["Data Persistence & External Services"]
        Services --> Eloquent["Eloquent ORM Models<br/>(Relations, Scopes, Accessors)"]
        Eloquent --> Database[("Database<br/>MySQL 8 / SQLite Serverless")]
        WeatherService -->|HTTP Client (Timeout & Fallback)| OpenMeteo["Open-Meteo Weather API"]
        GeocodingService -->|HTTP Client (Attribution & Fallback)| Nominatim["Nominatim Geocoding API"]
    end
```

---

## 2. Struktur Modul & Tanggung Jawab

| Lapisan / Komponen | Lokasi Direktori | Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **Controllers (Web)** | `app/Http/Controllers/Web/` | Menerima request dari browser, memanggil Service, merender Blade View. |
| **Controllers (API)** | `app/Http/Controllers/Api/V1/` | Mengelola request JSON REST API, validasi token Sanctum, merespons format standar API. |
| **Business Services** | `app/Services/` | Menampung aturan bisnis (validasi siklus status, perhitungan jam delay, kalkulasi jarak Haversine). |
| **Policies** | `app/Policies/` | Otorisasi berbasis hak akses (Customer hanya bisa melihat kargo miliknya). |
| **Middleware** | `app/Http/Middleware/` | Sanitasi header keamanan, pembatasan role pengguna (`admin`, `operator`, `customer`). |
| **Eloquent Models** | `app/Models/` | Definisi relasi database (One-to-Many, BelongsTo), casts data, dan scope query. |

---

## 3. Integrasi Layanan Eksternal & Peta

1. **Leaflet.js & Tile Providers:**
   - **Mode Satelit:** Citra satelit permukaan bumi resolusi tinggi dari ESRI World Imagery & Google Earth Satellite.
   - **Mode Default:** OpenStreetMap Carto Standard.
   - **Pencegahan Error:** Fallback otomatis jika salah satu tile server mengalami latensi jaringan.
2. **Open-Meteo Weather API:**
   - Diintegrasikan via `Http::timeout(5)->get(...)` untuk mengambil suhu, kelembapan, kecepatan angin, dan kondisi cuaca di pelabuhan tujuan.
   - Dilengkapi *graceful error handling* (jika API eksternal offline, sistem tetap menyajikan informasi kargo dengan disclaimer tanpa memicu crash).
3. **Nominatim Geocoding API:**
   - Pencarian koordinat pelabuhan global berstandar UN/LOCODE dengan penanganan rate limiting dan User-Agent yang sesuai kebijakan penyedia.

---

## 4. Atribusi Pustaka & Lisensi (Attribution)

- **Laravel 12 Framework:** Lisensi MIT.
- **Tailwind CSS v4:** Lisensi MIT (Utility-first CSS Framework).
- **Leaflet.js:** Lisensi BSD-2-Clause (Interactive Maps Library).
- **Chart.js:** Lisensi MIT (Dashboard Analytics Visualizer).
- **Open-Meteo:** Lisensi Creative Commons Attribution 4.0 International (Cuaca Terbuka).
- **OpenStreetMap:** &copy; Kontributor OpenStreetMap, ODbL (Open Database License).

---

## 5. Batasan Sistem (*Known Limitations*) & Pengembangan Mendatang

### Batasan Saat Ini:
1. Tidak terhubung langsung ke hardware sensor IoT GPS fisik armada kapal di laut lepas (menggunakan simulasi koordinat checkpoint berbasis pos pelabuhan singgah).
2. Tidak mengimplementasikan pembayaran gateway nyata (menggunakan flow B2B logistics manifest).

### Rencana Pengembangan Mendatang (*Future Work*):
1. Integrasi Webhook dan Notifikasi WhatsApp/Email otomatis saat status kargo berubah.
2. Penerapan Machine Learning untuk rekomendasi optimasi rute pelayaran maritim berdasarkan histori cuaca badai musiman.
3. Dukungan multi-mata uang (*multi-currency*) untuk freight invoice.
