# Product Requirements Document (PRD): CargoTrace

## 1. Project Overview
**Name:** CargoTrace - Global Shipment Visibility & Tracking Platform
**Domain:** Logistics and Supply Chain
**Objective:** Build a shipment visibility platform for logistics companies and customers to track shipments, monitor statuses, detect delays, and manage operational exceptions.

## 2. Problem Statement
* Shipment information is scattered and hard to monitor.
* Customers lack real-time visibility into their cargo's current status and journey history.
* Delays and operational exceptions are not centralized, causing reactive management.
* Operators need a unified dashboard to prioritize follow-ups on critical shipments.

## 3. Scope
**In Scope:**
* Shipment lifecycle management and strict status transitions.
* Tracking timeline and interactive map locations.
* Deterministic ETA, delay rules, and exception management.
* Role-based access control (RBAC).
* REST API, external API integration, dashboard, automated testing, and deployment.

**Out of Scope:**
* Direct integration with real ports, customs, or actual carriers.
* Real financial transactions or payments.
* Real-time GPS tracking from physical IoT devices.
* Complex machine learning or AI predictions.
* Microservices architecture.

## 4. User Roles & Permissions
1.  **Administrator:** Manages users, carriers, locations, system configurations. Accesses full dashboard and audit logs.
2.  **Logistics Operator:** Creates/updates shipments, adds tracking events, manages exceptions, views operational data.
3.  **Customer:** Views only their own shipments, status, ETA, timeline, map route, and relevant notifications.

## 5. Core Features
*   **Shipment Lifecycle:** Strict validation for transitions (e.g., CREATED -> BOOKED -> PICKED_UP -> IN_TRANSIT -> DELIVERED).
*   **Tracking & Timeline:** Chronological event logging with coordinates and descriptions. Never overwrite past events.
*   **ETA, Delay & Exceptions:** Deterministic logic to compare ETA vs. Expected Arrival. Categorized exceptions (e.g., CUSTOMS_HOLD, WEATHER_DELAY).
*   **Map & External APIs:** Leaflet.js map with OpenStreetMap. Geocoding/Weather integration via external APIs.
*   **Dashboard & Search:** Real-time metrics (Total shipments, delay rates) and advanced filtering (pagination, date range, status).

## 6. Acceptance Criteria
* All roles function as intended with strict backend authorization.
* Automated testing covers authentication, CRUD, and business rules.
* API implemented using JSON, Sanctum auth, and proper status codes.
* Secure repository (no exposed `.env` or API keys).
* Application is live, deployed, and populated with realistic seed data.