# CargoTrace — Global Shipment Visibility & Tracking Platform

CargoTrace is an enterprise-ready global cargo visibility, real-time shipment tracking, deterministic ETA delay evaluation, and operational exception management platform built with **Laravel 12**, **PHP 8.2+**, **MySQL / MariaDB**, **Tailwind CSS v4**, **Leaflet.js (OpenStreetMap)**, and **Chart.js**.

---

## Key Features & Configured Worldwide Corridors

CargoTrace implements an **Individual All-Routes Configuration** combining three international logistics corridors:

| Corridor | Origin Region | Destination Focus | Delay Threshold | Primary Exception | Core KPI Focus |
| :--- | :--- | :--- | :---: | :--- | :--- |
| **SEA → Europe** | Indonesia / Singapore / SEA | Netherlands / Germany / UK | **12 Hours** | `CUSTOMS_HOLD` | On-Time Delivery Rate |
| **Asia → North America** | China / Japan / Korea / SEA | United States / Canada | **18 Hours** | `WEATHER_DELAY` | Average Delay Duration |
| **SEA → Australia** | Singapore / Indonesia / SEA | Australia / New Zealand | **24 Hours** | `DOCUMENTATION_ISSUE` | Exception Resolution Time |

### Core Capabilities
- **Strict Shipment Lifecycle State Machine:** Enforces sequential transitions (`CREATED → BOOKED → PICKED_UP → IN_TRANSIT → OUT_FOR_DELIVERY → DELIVERED`), exception recovery (`IN_TRANSIT → EXCEPTION → IN_TRANSIT`), and immutable terminal states (`DELIVERED`, `CANCELLED`).
- **Append-Only Chronological Tracking Timeline:** Immutable location checkpoints (`TrackingEvent`) with GPS coordinates and live status updates.
- **Deterministic ETA & Delay Engine (`EtaService`):** Automatically computes delay hours and maps severity tiers (`0h = ON_TIME`, `1–6h = LOW`, `7–24h = MEDIUM`, `>24h = HIGH`).
- **Interactive Maps & External Integrations:** Interactive Leaflet.js OpenStreetMap route visualization + live destination weather telemetry (**Open-Meteo**) and UN/LOCODE port lookup (**Nominatim OpenStreetMap**) via Laravel HTTP Client with graceful failure handling.
- **Role-Segmented UI:**
  - **Public Landing Page:** Soft Earthy Glassmorphism UI with an interactive **Hero Corridor Selector** and a **12-Column Bento Grid**.
  - **Admin & Staff Operators:** Glassmorphic **Left Sidebar Layout** (`Konsol Admin` / `Konsol Staff`) with full operational tooling and **Chart.js** database analytics.
  - **Customers:** Clean **Glassmorphic Topbar Layout** (`Portal Klien`) scoped strictly to their own shipments and notifications.
- **Server-Side Search, Multi-Filter & Sorting:** Search by tracking number/customer/carrier plus filters for status, carrier, origin, destination, delayed status, date ranges, and sorting (`latest`, `oldest`, `eta_asc`, `eta_desc`, `delay_desc`).
- **In-App Notifications & Admin Audit Logs:** Automatic stakeholder alerts on shipment creation, status updates, ETA delays, and exceptions, alongside full before/after change audit trails restricted exclusively to Administrators.
- **Security Hardening:** Role escalation prevention on registration, FormRequest validation, parameterized queries with SQL wildcard escaping and bounded pagination, HTML escaping on DOM/Leaflet sinks (`window.escapeHtml`), PII stripping on public tracking endpoints, CSRF tokens, Sanctum Bearer tokens with scoped abilities, and defensive HTTP security headers.

---

## Architecture Overview

CargoTrace follows an **MVC + Service Layer Pattern** cleanly separating HTTP handling from domain rules:

- `app/Http/Controllers/Web/*` & `Api/V1/*` — Thin HTTP controllers delegating authorization to Policies/FormRequests and domain execution to Services.
- `app/Services/*` — Dedicated domain services (`AuthService`, `ShipmentService`, `TrackingService`, `EtaService`, `ExceptionService`, `CarrierService`, `LocationService`, `NotificationService`, `AuditLogService`, `WeatherService`, `GeocodingService`).
- `app/Policies/*` — Granular authorization policies (`ShipmentPolicy`, `CarrierPolicy`, `LocationPolicy`, `TrackingEventPolicy`, `ShipmentExceptionPolicy`).
- `app/Traits/*` — `ApiResponseTrait` (standardized JSON envelope) and `SanitizesQueriesTrait` (LIKE wildcard escaping + bounded pagination).
- `app/Http/Middleware/*` — `RoleMiddleware` and `SecurityHeadersMiddleware`.

---

## Role Matrix & Demo Credentials

After running the database seeders, the following accounts are available (password for all accounts: `password`):

| Role | Email | Layout | Key Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | Glass Sidebar (`Konsol Admin`) | Full CRUD, Carriers/Locations delete, Shipments delete, System Audit Logs |
| **Logistics Operator** | `operator@cargotrace.com` | Glass Sidebar (`Konsol Staff`) | Create/update shipments, append tracking events, update ETA, manage exceptions, add locations |
| **Customer** | `customer@cargotrace.com` | Glass Topbar (`Portal Klien`) | Read own shipments, view timelines & weather, view/read own notifications |

### Sample Seeded Tracking Numbers
- `CT-20261101-9821` (SEA → Europe Corridor)
- `CT-20261105-4412` (Asia → North America Corridor)
- `CT-20261020-1109` (SEA → Australia Corridor)

---

## Local Setup & Installation

### Requirements
- **PHP >= 8.2** (with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo` extensions)
- **Composer**
- **MySQL 8+ / MariaDB 10.4+**
- **Node.js 18+ & npm**

### Step-by-Step Setup

1. **Clone the repository and install dependencies:**
   ```bash
   git clone <your-repo-url> cargotrace
   cd cargotrace
   composer install
   npm install
   ```

2. **Configure environment variables:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` to match your local MySQL credentials (`DB_DATABASE=cargotrace_db`, `DB_USERNAME=root`, `DB_PASSWORD=`). Ensure the database `cargotrace_db` exists in MySQL.

3. **Run database migrations & seed realistic demo data:**
   ```bash
   php artisan migrate --seed
   ```

4. **Build frontend assets (Tailwind CSS v4, Leaflet, Chart.js):**
   ```bash
   npm run build
   ```

5. **Start the local development server:**
   ```bash
   php artisan serve
   ```
   *(Windows helper scripts `serve.bat` and `artisan.bat` are also included if multiple PHP versions are installed.)*

Open `http://127.0.0.1:8000` in your browser.

---

## Running Automated Tests

All unit and feature suites run cleanly via PHPUnit using an isolated in-memory SQLite testing configuration (`phpunit.xml`):

```bash
php vendor/phpunit/phpunit/phpunit
```

**Verified Test Coverage (77 tests, 394 assertions):**
- Authentication & Sanctum API tokens (`AuthTest`, `ApiAuthTest`)
- Role Middleware & RBAC Policies (`RoleMiddlewareTest`, `CarrierCrudTest`, `LocationCrudTest`)
- Shipment CRUD & State Machine (`ShipmentCrudTest`, `TrackingTimelineTest`)
- Deterministic ETA, Delay & Exceptions (`EtaAndDelayRulesTest`, `ExceptionManagementTest`)
- External Integrations & Map Views (`ExternalIntegrationsTest`)
- Web Navigation, Chart.js Dashboard, Search/Sort, Notifications & Audit Logs (`WebPagesNavigationTest`, `P13DashboardSearchNotificationAuditTest`)
- P14 Security QA — Mass Assignment, XSS, SQLi/Wildcards, PII & Full Authorization/ETA Matrix (`P14SecurityAndQualityAssuranceTest`)

---

## API Documentation & Postman Collection

All REST API endpoints are prefixed under `/api/v1` and return standard JSON responses:
- **Postman Collection:** Import [`docs/CargoTrace_API_v1.postman_collection.json`](docs/CargoTrace_API_v1.postman_collection.json) directly into Postman.
- **Full Endpoint Reference:** See [`docs/API_DOCS.md`](docs/API_DOCS.md).
- **Live Demo & Ownership Guide:** See [`docs/DEMO_AND_INTERVIEW_GUIDE.md`](docs/DEMO_AND_INTERVIEW_GUIDE.md).

---

## Production Deployment Checklist

1. Set production `.env` variables:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```
2. Install optimized dependencies and build assets:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```
3. Run migrations cleanly and optimize Laravel caches:
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. Point Nginx/Apache document root to `public/` and configure HTTPS/TLS termination.
5. Never commit `.env` or private secrets (already excluded via `.gitignore`).
