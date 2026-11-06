# Architecture Diagram & Guidelines

## 1. Application Layering (MVC + Service Pattern)
To ensure scalability and maintainability, the application strictly separates HTTP routing from business logic.

*   **Routes & Middleware:** Handles incoming HTTP requests, enforces Authentication (Sanctum) and basic Authorization.
*   **Controllers:** Extremely thin. Responsible only for receiving requests, calling the appropriate Service, and formatting the JSON/Blade response. No database logic here.
*   **Service Layer:** The core of the application. 
    *   `ShipmentService`: Handles CRUD and lifecycle validations.
    *   `TrackingService`: Validates timeline continuity and coordinates.
    *   `EtaService`: Calculates delays against thresholds.
    *   `ExceptionService`: Manages blockages or issues.
*   **Models (Eloquent):** Handles data persistence, accessors, mutators, and relationships.

## 2. Data Flow Example (Adding a Tracking Event)
1.  **Client:** Sends `POST /api/shipments/{id}/tracking` with Location and Event Status.
2.  **Controller:** Validates input via `StoreTrackingRequest`.
3.  **Controller:** Calls `TrackingService->addEvent($shipmentId, $data)`.
4.  **TrackingService:** 
    *   Checks if the transition is allowed based on current status.
    *   Triggers `EtaService` to check if this new event causes a delay.
    *   Saves the event to the database.
5.  **Controller:** Returns `201 Created` with the updated Shipment resource.

## 3. External API Integration
*   Use `Illuminate\Support\Facades\Http`.
*   Implement `timeout()` and `retry()` for resilience.
*   Never hardcode API keys; always use `config/services.php` mapped to `.env`.