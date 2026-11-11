# Project Milestones & Task Checklist

This list follows the academic milestones (P8 - P15) mandated by the project brief.

## Milestone P8: Setup & Foundation
- [x] Analyze requirements and finalize Use Cases.
- [x] Design Database Entity-Relationship Diagram (ERD).
- [x] Initialize public GitHub repository.
- [x] Setup Laravel 12 project and MySQL database connection.
- [x] Implement Authentication (Login/Register/Logout) using Sanctum/Session.

## Milestone P9: Core Entities & Roles
- [x] Create Migrations and Models for Users, Roles, Shipments, Carriers, and Locations.
- [x] Develop Role-Based Access Control (Policies/Gates for Admin, Operator, Customer).
- [x] Implement CRUD for Shipments, Carriers, and Locations.
- [x] Create Seeders for base data.

## Milestone P10: Tracking & Lifecycle
- [x] Create Migrations/Models for Tracking_Events.
- [x] Implement strict business rules for Shipment Status transitions.
- [x] Build the timeline UI (chronological sorting, distinct event types).

## Milestone P11: Business Logic & APIs
- [x] Implement ETA calculation logic (Expected vs Actual).
- [x] Develop Exception Management (Customs Hold, Weather, etc.) and severity levels.
- [x] Finalize standard REST API endpoints with proper JSON formatting and HTTP codes.

## Milestone P12: Maps & External Integrations
- [ ] Integrate Leaflet.js & OpenStreetMap on the Frontend.
- [ ] Map origin, destination, and tracking points with markers and popups.
- [ ] Integrate an external Geocoding or Weather API via Laravel HTTP Client.
- [ ] Implement error handling for external API failures.

## Milestone P13: Dashboard & UX
- [ ] Build Dashboard metrics (Total shipments, delay rates, open exceptions) using Chart.js.
- [ ] Implement Server-side Search, Filtering, and Pagination for shipments.
- [ ] Implement in-app notifications (and optionally email) and Audit Logs for critical actions.

## Milestone P14: Security & Quality Assurance
- [ ] Review mass-assignment protections, SQL injection, XSS, and CSRF.
- [ ] Write Automated Tests for Authentication and Authorization.
- [ ] Write Automated Tests for Shipment CRUD and valid/invalid status transitions.
- [ ] Write Automated Tests for ETA/Delay rules.

## Milestone P15: Finalization & Delivery
- [ ] Deploy application to a live server/VPS/Cloud.
- [ ] Ensure `.env` and API keys are not in the repository. Provide `.env.example`.
- [ ] Write a comprehensive English `README.md` (Setup, Tests, Deployment, Features).
- [ ] Create Postman Collection / API Docs.
- [ ] Prepare for Live Demo and Code Ownership Interview.