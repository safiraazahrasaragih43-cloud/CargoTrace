# CargoTrace — Global Shipment Visibility & Tracking Platform

CargoTrace is an enterprise-ready global cargo visibility, real-time shipment tracking, deterministic ETA delay evaluation, and operational exception management platform built with **Laravel 12**, **PHP 8.2+**, **MySQL / MariaDB**, **Tailwind CSS v4**, **Leaflet.js**, and **Chart.js**.

---

## Live Access & Deployment

| Service | URL |
| :--- | :--- |
| **Production Live Application (Vercel)** | [https://cargo-trace-teal.vercel.app](https://cargo-trace-teal.vercel.app) |
| **GitHub Repository** | [https://github.com/safiraazahrasaragih43-cloud/CargoTrace](https://github.com/safiraazahrasaragih43-cloud/CargoTrace) |
| **REST API v1 Interactive Health Console** | [https://cargo-trace-teal.vercel.app/api/v1/health](https://cargo-trace-teal.vercel.app/api/v1/health) |

---

## Demo Accounts & Role-Based Access Control (RBAC)

The system enforces strict server-side authorization across 3 user roles (password for all demo accounts: `password`):

| Role | Email | Interface Layout | Key Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | Sidebar (Admin Console) | Full CRUD, Carrier/Location/Shipment delete, System Audit Logs, User management |
| **Logistics Operator** | `operator@cargotrace.com` | Sidebar (Operator Console) | Create/update shipments, append tracking checkpoints, manage exceptions & routes |
| **Customer** | `customer@cargotrace.com` | Topbar (Customer Portal) | Read-only scoped strictly to own shipments, view timelines, weather telemetry & notifications |

### Sample Tracking Numbers for Public Demo
- `TRK-EXP-2026-001` (SEA → Europe Corridor: Tanjung Priok to Rotterdam)
- `TRK-SEA-2026-002` (Asia → North America Corridor: Shanghai to Los Angeles)
- `TRK-AIR-2026-003` (SEA → Australia Corridor: Changi to Sydney)

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
- **Append-Only Chronological Tracking Timeline:** Immutable location checkpoints (`TrackingEvent`) with GPS coordinates and live status updates. Past history is never overwritten.
- **Deterministic ETA & Delay Engine (`EtaService`):** Automatically computes delay hours against expected SLA arrival:
  - `0h` = On-Time
  - `1–6h` = LOW Severity
  - `7–24h` = MEDIUM Severity
  - `> 24h` = HIGH Severity
- **Interactive Leaflet Maps with Dual Modes:**
  - **Satellite Mode:** High-resolution realistic satellite imagery (ESRI World Imagery / Google Earth style) with textured oceans and green landmasses.
  - **Default Mode:** Clean OpenStreetMap Carto routing.
  - **Solid Geodesic Route Lines:** Clean continuous maritime shipping lines.
  - **Automatic Haversine GPS Telemetry:** Accurate maritime distance calculation in both Kilometers (km) and Nautical Miles (NM).
- **Full Bilingual Support:** Instant 1-click language switcher with localized Blade views, notifications, and navigation.
- **Live Destination Weather Telemetry:** Real-time port weather integration via **Open-Meteo API** with graceful timeout and fallback handling.
- **Role-Segmented Operational Dashboard:** Live KPI statistics, Chart.js visualizations, server-side multi-filtering, sorting, and pagination.
- **System Audit Logs & In-App Notifications:** Complete before/after change tracking and contextual alerts.

---

## Technical Documentation

- **Entity-Relationship Diagram (ERD) & Data Dictionary:** [`docs/ERD.md`](docs/ERD.md)
- **Architecture Documentation & Attribution:** [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
- **RESTful API v1 Documentation:** [`docs/API_DOCS.md`](docs/API_DOCS.md)
- **Postman Collection:** [`docs/CargoTrace_API_v1.postman_collection.json`](docs/CargoTrace_API_v1.postman_collection.json)
- **Demo & Code Ownership Interview Guide:** [`docs/DEMO_AND_INTERVIEW_GUIDE.md`](docs/DEMO_AND_INTERVIEW_GUIDE.md)

---

## Local Setup & Installation

### Requirements
- **PHP >= 8.2** (extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `sqlite3`)
- **Composer**
- **Node.js 18+ & npm**
- **MySQL 8+ / MariaDB**

### Step-by-Step Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/safiraazahrasaragih43-cloud/CargoTrace.git
   cd CargoTrace
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Set up your database in `.env` (`DB_DATABASE=cargotrace_db`, `DB_USERNAME=root`, `DB_PASSWORD=`).

4. **Run database migrations and seed realistic demo data:**
   ```bash
   php artisan migrate --seed
   ```

5. **Build frontend assets:**
   ```bash
   npm run build
   ```

6. **Start local development server:**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## Automated Testing Suite

All unit and feature tests run cleanly via **PHPUnit** using an isolated in-memory SQLite database configuration:

```bash
php vendor/phpunit/phpunit/phpunit
```

**Verified Test Coverage:**
- **84 Tests, 482 Assertions (100% Passed)**
- Coverage includes: Authentication, RBAC policies, Shipment CRUD, state transitions, ETA delay algorithms, Leaflet views, weather integration fallbacks, FormRequest sanitization, and security vulnerability tests (SQLi, XSS, CSRF, PII protection).

---

## Security Hardening

- **CSRF Protection:** Verified on all state-changing web requests.
- **SQL Injection Prevention:** Enforced via PDO Parameterized Queries and Eloquent ORM.
- **XSS Sanitization:** All dynamic outputs are escaped in Blade templates and Leaflet popups.
- **Role Escalation Protection:** Prevented during public user registration.
- **Defensive HTTP Security Headers:** Enforced `Strict-Transport-Security`, `X-Content-Type-Options: nosniff`, and `X-Frame-Options: SAMEORIGIN`.

---

© 2026 **CargoTrace** — Built for Web Programming 2 Final Examination (UAS Pemrograman Web).
