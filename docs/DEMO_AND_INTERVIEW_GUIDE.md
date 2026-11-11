# CargoTrace — Live Demo Script & Code Ownership Interview Guide

## 1. Live Demo Flow (5–7 Minutes)

1. **Public Glassmorphism Landing Page (`/`):**
   - Demonstrate the **Hero Corridor Selection** (`SEA → Eropa 12j`, `Asia → Amerika 18j`, `SEA → Australia 24j`) dynamically updating the Glass Showcase preview card and calling `/api/v1/tracking/{trackingNumber}` directly.
   - Walk through the **12-Column Bento Grid** showcasing Leaflet maps, `EtaService`, RBAC Role Matrix, and Chart.js analytics.

2. **Role-Differentiated Layouts & RBAC:**
   - Log in as **Customer** (`customer@cargotrace.com` / `password`): highlight the clean **Glass Topbar Layout** (`Portal Klien`), scoped `Pengiriman Saya`, notifications, and inability to access `/carriers` or `/audit-logs`.
   - Log in as **Operator** (`operator@cargotrace.com` / `password`): highlight the **Glass Sidebar Layout** (`Konsol Staff`) with full shipment operations, map & Chart.js dashboard.
   - Log in as **Admin** (`admin@cargotrace.com` / `password`): highlight `Konsol Admin` including the exclusive **Log Audit Sistem** (`/audit-logs`).

3. **Core Logistics Workflow (`/shipments` & `/shipments/{trackingNumber}`):**
   - Filter shipments by origin, destination, delayed flag (`is_delayed`), date range, and sort order (`eta_asc`).
   - Open `CT-20261101-9821`: showcase the interactive **Leaflet.js OpenStreetMap route map**, **Open-Meteo live weather widget**, deterministic **ETA Delay Evaluation**, append-only **Tracking Timeline**, and **Exception Management** resolution flow.

4. **Observability & Compliance (`/dashboard`, `/notifications`, `/audit-logs`):**
   - Show database-driven **Chart.js** widgets updating with real shipment/carrier/exception counts.
   - Open `/notifications` to inspect automatic stakeholder alerts triggered by shipment creation, tracking checkpoints, and exceptions.
   - Open `/audit-logs` as Admin to inspect immutable `old_values` vs `new_values`, actor identity, and IP addresses.

---

## 2. Anticipated Code Ownership Questions & Technical Answers

- **Why use the Service Layer (`app/Services/*`) alongside Controllers?**
  Thin controllers handle HTTP/FormRequest validation and Policies, while all business rules (DB transactions, strict state machine transitions, ETA math, audit logging, and notification dispatch) live cleanly inside reusable Service classes shared across both Web and REST API endpoints.

- **How are shipment status transitions protected against invalid jumps?**
  `App\Models\Shipment::ALLOWED_TRANSITIONS` defines an explicit directed state machine (`canTransitionTo()`), verified before persisting inside `ShipmentService::updateShipment()` and `TrackingService::addEvent()`, rejecting jumps like `CREATED -> DELIVERED` or mutations on terminal states (`DELIVERED`, `CANCELLED`) with HTTP `422`.

- **How does deterministic ETA & severity evaluation work across the three combined corridors?**
  `EtaService::detectCorridor()` inspects destination countries against `config/cargotrace.php` (`SEA_EUROPE` 12h, `ASIA_NORTH_AMERICA` 18h, `SEA_AUSTRALIA` 24h) and compares `expected_delivery_at` vs `estimated_delivery_at`/`actual_delivery_at`. `determineDelaySeverity()` maps `delay_hours` deterministically (`0 = ON_TIME`, `1–6h = LOW`, `7–24h = MEDIUM`, `>24h = HIGH`).

- **What concrete security controls were reviewed & implemented in P14?**
  - **Mass Assignment:** `AuthService::register()` hard-codes `User::ROLE_CUSTOMER`, preventing role escalation; `UpdateShipmentRequest` omits internal `is_delayed`/`delay_hours`/`tracking_number` fields.
  - **XSS:** Blade escapes with `{{ }}` and JS DOM sinks/Leaflet popups use `window.escapeHtml()` in `resources/js/app.js`.
  - **SQL Injection / DoS:** All queries use bound Eloquent parameters, `SanitizesQueriesTrait::escapeLike()` escapes LIKE wildcards on MySQL/MariaDB/PgSQL, `sort_by` uses a strict `match` allowlist, and `clampPerPage()` caps page size to `100`.
  - **Authorization & PII:** Policies enforce RBAC & tenant ownership; `GET /api/v1/tracking/{trackingNumber}` omits `customer` email/phone; guest detail view masks private contact email.
  - **CSRF & Headers:** Web forms enforce `@csrf` and `SecurityHeadersMiddleware` adds `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, and `Permissions-Policy`.

- **How are external API failures handled?**
  `WeatherService` (Open-Meteo) and `GeocodingService` (Nominatim) enforce configurable timeouts (`EXTERNAL_API_TIMEOUT=5`), catch HTTP/network errors safely, and return structured fallback payloads (`available: false`) without crashing the application or leaking stack traces.
