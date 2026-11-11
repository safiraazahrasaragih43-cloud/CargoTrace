# CargoTrace REST API v1 Documentation

Base URL (Local): `http://127.0.0.1:8000/api/v1`

All protected endpoints use **Laravel Sanctum** (`Authorization: Bearer <token>`) and require `Accept: application/json`.

## Standard Response Envelope

```json
{
  "success": true,
  "message": "Action completed successfully",
  "data": {}
}
```

Error responses strictly omit stack traces and return standardized status codes (`401`, `403`, `404`, `405`, `422`, `500`).

---

## 1. Public & Authentication Endpoints

| Method | Endpoint | Auth | Description |
| :--- | :--- | :---: | :--- |
| `GET` | `/health` | No | Service health and timestamp |
| `GET` | `/tracking/{trackingNumber}` | No | Public shipment tracking (excludes customer private PII) |
| `POST` | `/auth/register` | No | Register a new Customer account (`role` is strictly forced to `customer`) |
| `POST` | `/auth/login` | No | Authenticate and issue Sanctum Bearer token |
| `GET` | `/auth/me` | Yes | Return authenticated user profile |
| `POST` | `/auth/logout` | Yes | Revoke current access token |

---

## 2. Shipments, ETA & Analytics

| Method | Endpoint | Roles | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/shipments` | All Auth | List shipments (Customers see own only). Filters: `search`, `status`, `carrier_id`, `origin_id`, `destination_id`, `is_delayed`, `date_from`, `date_to`, `sort_by` (`latest`, `oldest`, `eta_asc`, `eta_desc`, `delay_desc`), `per_page` (`1–100`) |
| `POST` | `/shipments` | Admin, Operator | Create shipment (`CT-YYYYMMDD-XXXX`) with optional nested `items[]` |
| `GET` | `/shipments/{shipment}` | Owner, Admin, Operator | View shipment details |
| `PUT/PATCH` | `/shipments/{shipment}` | Admin, Operator | Update shipment & validate strict status state machine |
| `DELETE` | `/shipments/{shipment}` | Admin | Soft-delete shipment |
| `GET` | `/shipments/{shipment}/eta` | Owner, Admin, Operator | Evaluate corridor threshold & delay severity |
| `PATCH` | `/shipments/{shipment}/eta` | Admin, Operator | Update `estimated_delivery_at` and recalculate delay metrics |
| `GET` | `/analytics/kpis` | All Auth | Retrieve combined global KPIs (`on_time_delivery_rate`, `average_delay_hours`, `avg_exception_resolution_hours`, `open_exceptions_count`) |

---

## 3. Tracking Timeline & Operational Exceptions

| Method | Endpoint | Roles | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/shipments/{shipment}/tracking` | Owner, Admin, Operator | Retrieve chronological `TrackingEvent` history |
| `POST` | `/shipments/{shipment}/tracking` | Admin, Operator | Append immutable checkpoint (`status`, `location_id`/`location_name`, `description`, optional `latitude`/`longitude`) |
| `GET` | `/shipments/{shipment}/exceptions` | Owner, Admin, Operator | List exceptions for a shipment |
| `POST` | `/shipments/{shipment}/exceptions` | Admin, Operator | Report exception (`type`: `CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`, `DAMAGED`, `MISROUTED`, `DOCUMENTATION_ISSUE`; `severity`: `LOW`, `MEDIUM`, `HIGH`, `CRITICAL`) |
| `PUT/PATCH` | `/exceptions/{exception}` | Admin, Operator | Investigate/resolve exception (`status`, `resolution_notes`, optional `restore_status`) |

---

## 4. Carriers, Locations & External API Integrations

| Method | Endpoint | Roles | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/carriers` | Admin, Operator | List & search carriers |
| `POST/PUT/DELETE` | `/carriers` | Admin, Operator (Delete: Admin) | Manage carrier partners |
| `GET` | `/locations` | All Auth | List & search port hubs (`PORT`, `AIRPORT`, `WAREHOUSE`, `DISTRIBUTION_CENTER`, `CUSTOMS`, `CUSTOMER_ADDRESS`) |
| `POST/PUT/DELETE` | `/locations` | Admin, Operator (Delete: Admin) | Manage port locations & GPS coordinates |
| `GET` | `/shipments/{shipment}/weather` | Owner, Admin, Operator | Fetch live destination/current hub weather telemetry via **Open-Meteo** |
| `GET` | `/integrations/geocode?q=...` | All Auth | Query port/city coordinates via **Nominatim OpenStreetMap** |

---

## 5. Notifications & Audit Logs

| Method | Endpoint | Roles | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/notifications` | All Auth | Retrieve current user's paginated notifications + `unread_count` |
| `PATCH` | `/notifications/{notification}/read` | Owner | Mark single notification read (`403` for non-owners) |
| `POST` | `/notifications/read-all` | All Auth | Mark all current user's notifications read |
| `GET` | `/audit-logs` | **Admin Only** | Filter system audit trails (`search`, `action`, `entity_type`), returns `403` for non-admins |
