# Parkly — Technical Requirements Document (TRD)

**Project:** Parkly: An AI-Powered Parking Discovery and Booking Platform  
**Course:** SE 331 — Software Engineering Design Capstone Project  
**Institution:** Department of Software Engineering, Daffodil International University  
**Document version:** 1.0  
**Status:** Technical blueprint / draft for team approval  
**Target delivery:** 12 weeks  
**Primary product:** Responsive web application  
**Companion document:** `Parkly_PRD.md`  

---

## 1. Purpose and How to Use This Document

This TRD defines the proposed technical architecture, stack, conventions, data model, API boundaries, external integrations, security requirements, testing strategy, and implementation sequence for Parkly. It is intended to be read by all three developers and by AI coding agents before they generate or modify code.

The Product Requirements Document (PRD) describes **what Parkly must do**. This document describes **how the team should implement it**.

### 1.1 Source-of-truth order

When implementation details conflict, use this order:

1. Explicit decisions approved by the project team and supervisor.
2. `Parkly_PRD.md` for product scope and required behavior.
3. `Parkly_TRD.md` for the technical blueprint.
4. Existing implementation and comments, only when they agree with the approved documents.

Do not silently broaden the scope. A coding agent must identify a contradiction or missing decision, explain the impact, and ask for confirmation when the decision changes product behavior, data semantics, payment behavior, or security.

### 1.2 Requirements versus recommendations

This document distinguishes between:

- **Confirmed scope:** Requirements already described by the proposal/PRD.
- **Recommended technical default:** A practical engineering approach for the prototype. Implement it unless it changes product behavior or needs a decision listed in Section 22.
- **Open decision:** A matter the proposal does not settle. Do not treat it as a confirmed business rule until the team approves it.

### 1.3 Core product boundaries

Parkly is a web-based parking discovery and booking platform for a limited set of registered parking facilities. It supports GPS or manual location search, map and list results, facility details, vehicle and duration selection, fee calculation, booking, a payment prototype, digital invoice/confirmation, administrative management, manual availability updates, and Gemini-powered natural-language requirement extraction/recommendations.

The system **must not** claim that Google Maps or Gemini supplies authoritative parking-slot availability. Recorded availability and prices come from Parkly database records maintained by an authorized administrator or participating operator.

The current scope excludes camera/CCTV vehicle detection, hardware occupancy sensors, a custom-trained machine-learning model, automatic gates, nationwide facility integration, mandatory QR scanning, ratings/reviews, and a native mobile app.

---

## 2. Architecture at a Glance

### 2.1 Recommended architecture

Use a **separated frontend and backend** in one Git repository (monorepo):

- **Frontend:** Vue 3 + Vite + JavaScript + Tailwind CSS.
- **Backend:** PHP + Laravel REST API.
- **Database:** MySQL-compatible relational database. For local development, reuse the database included with XAMPP, which is typically MariaDB. Laravel connects through its MySQL-compatible driver; test against the actual installed version.
- **ORM and schema:** Laravel Eloquent ORM and Laravel migrations.
- **HTTP client:** Axios in the frontend; Laravel HTTP Client for server-side external API calls.
- **Maps:** Google Maps JavaScript API for map rendering; Places API where needed for place autocomplete/search. Use Parkly database coordinates for registered facility markers.
- **AI:** Google Gemini API called from Laravel, never directly from browser code.
- **Payments:** SSLCOMMERZ sandbox, if account and sandbox access are available. Server-side validation is required.
- **Invoice:** HTML invoice/confirmation view plus a downloadable PDF if a compatible Laravel PDF package is adopted.
- **Version control:** Git and GitHub.

### 2.2 Logical data flow

```text
Browser (Vue 3)
   |-- GPS permission / manual location / normal search
   |-- Google Maps JavaScript API (map visualization)
   |-- Axios HTTPS/JSON requests
   v
Laravel REST API
   |-- Request validation and authorization
   |-- Search and deterministic matching rules
   |-- Booking, availability, pricing, and payment state logic
   |-- Gemini API call for natural-language extraction only
   |-- Payment provider validation / callback processing
   |-- Invoice data generation
   v
MySQL-compatible database (XAMPP MariaDB locally)
   |-- Registered parking facilities and coordinates
   |-- Supported vehicle types and pricing records
   |-- Recorded availability and update history
   |-- Bookings, payment references, admin records, notifications

External services:
   Google Maps Platform / Places API
   Google Gemini API
   SSLCOMMERZ sandbox (if feasible)
```

### 2.3 Separation-of-responsibility rules

1. Vue is responsible for presentation, user input, loading/error states, map visualization, and calling the backend API.
2. Laravel is responsible for validation, access control, authoritative business rules, fee calculation, availability validation, booking state changes, and external secret-bearing API calls.
3. The database is the source of truth for registered facilities, configured prices, recorded availability, booking details, and payment state.
4. Gemini interprets natural-language input into structured requirements. Laravel performs the actual facility lookup and matching.
5. Google Maps provides mapping and place/geocoding functions; it must not be used as the source of Parkly availability or price records.
6. The payment provider's validated server-side response, not the browser's return URL alone, determines whether a payment is successful.
7. The frontend must never be trusted to submit a valid price or available-slot count. The backend re-calculates/re-validates these values.

---

## 3. Technology Stack and Compatibility Policy

The team must check installed versions before generating the project. Keep `package-lock.json` and `composer.lock` committed so the three developers can reproduce the same dependency versions.

| Layer | Technology | Purpose | Status |
|---|---|---|---|
| Language | JavaScript (ES modules) | Vue frontend and browser behavior | Confirmed |
| Markup/styling | HTML5, CSS, Tailwind CSS | Semantic page structure and responsive styling | Confirmed |
| Frontend framework | Vue 3 | Component-based user and admin interface | Confirmed |
| Frontend build tool | Vite | Local dev server and optimized build | Confirmed with Vue 3 setup |
| Frontend routing | Vue Router | Public, booking, invoice and admin screens | Recommended default |
| Frontend HTTP | Axios | Communicate with Laravel JSON API | Recommended default |
| Optional small state store | Pinia | Shared session/search state only if needed | Optional; do not add without need |
| Backend language | PHP | Application server logic | Confirmed |
| Backend framework | Laravel | REST API, validation, authorization, ORM, migrations | Confirmed |
| API format | REST over HTTP with JSON | Frontend/backend integration | Recommended default |
| Authentication | Laravel session auth + Sanctum for first-party SPA | Protect admin/operator API routes | Recommended default |
| Database access | Eloquent ORM | Model and query layer | Confirmed |
| Database schema | Laravel migrations and seeders | Versioned schema and demo data | Confirmed |
| Local database | XAMPP's MariaDB (usually) | Development database compatible with Laravel's `mysql` driver | Reuse existing installation; verify version |
| Production database | MySQL-compatible database | Hosting target | Open decision; avoid untested engine-specific SQL |
| Maps | Google Maps JavaScript API | Interactive map and facility markers | Confirmed intent |
| Place lookup | Google Places API (New) where useful | Text search/autocomplete/place details | Proposed integration detail |
| AI | Google Gemini API | Natural-language request understanding | Confirmed |
| Payments | SSLCOMMERZ sandbox | Prototype payment flow if accessible | Conditional |
| Invoice | HTML receipt and optional server-generated PDF | Confirmation and staff lookup details | Confirmed purpose; PDF package is implementation choice |
| Tests | PHPUnit or Pest for Laravel; Vitest + Vue Test Utils for frontend | Unit/integration tests | Recommended default; confirm packages on setup |
| API testing | Postman | Endpoint/manual integration validation | Confirmed in proposal |
| Source control | Git/GitHub | Collaboration, review and history | Confirmed |
| Editor | VS Code | Development environment | Confirmed by team |

### 3.1 PHP and Laravel compatibility

- Laravel 13 is a viable target only when the selected PHP runtime meets Laravel 13's minimum requirement (PHP 8.3 or later according to the current release notes).
- First run `php -v`, `composer --version`, `node -v`, `npm -v`, and `git --version` in the actual terminal used by VS Code.
- Check `where php` on Windows to confirm which PHP executable is running. Do not assume the command-line PHP matches the XAMPP Apache PHP.
- If XAMPP's PHP version is incompatible with the intended Laravel version, do not silently overwrite XAMPP. The team must either select a Laravel release compatible with the installed runtime or set up a separately managed compatible PHP runtime and configure the CLI/web server deliberately.
- Verify required PHP extensions before `composer install`, including at least the extensions required by the chosen Laravel version and database driver. Common required capabilities include PDO, the MySQL PDO driver, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, Fileinfo, and cURL; confirm exact requirements in the selected Laravel release documentation.
- Choose the Laravel version based on current official support and the available PHP runtime, then pin dependency versions with `composer.lock`.

### 3.2 Node.js and frontend compatibility

- Install an active Node.js LTS release supported by the chosen Vite release.
- Use npm and commit `package-lock.json`.
- Prefer the current stable Vue 3 and Tailwind CSS versions compatible with Vite. Follow the official Tailwind installation guide for the selected major version; Tailwind v4 setup differs from v3 setup.
- Do not mix Tailwind v3 instructions/packages with a Tailwind v4 project.
- A successful `npm run build` is required before a frontend change is marked complete.

### 3.3 Database engine: MySQL versus XAMPP MariaDB

The proposal calls the database MySQL. The team's local environment already uses XAMPP, whose bundled database is typically MariaDB. These are compatible in many Laravel use cases but are not identical products.

Implementation policy:

1. Inspect the actual local engine and version via phpMyAdmin or the SQL command `SELECT VERSION();`.
2. Use Laravel's `DB_CONNECTION=mysql` for the PDO MySQL-compatible driver, with the host/port/database credentials set in `.env`.
3. Prefer portable SQL and Laravel migrations. Avoid features specific to a particular MySQL or MariaDB release unless approved and tested.
4. Test migrations, indexes, transaction/locking behavior, and seeders on the actual XAMPP engine.
5. Document the actual local engine in `README.md` and decide whether the final host will use MySQL or MariaDB before deployment.

---

## 4. Repository Layout and Application Structure

### 4.1 Monorepo layout

Use a single GitHub repository with independent frontend and backend folders:

```text
parkly-parking-platform/
├── README.md
├── PRD.md
├── TRD.md
├── .gitignore
├── docs/
│   ├── architecture.md
│   ├── api-contract.md
│   ├── database-schema.md
│   └── decisions/
├── frontend/
│   ├── package.json
│   ├── package-lock.json
│   ├── .env.example
│   ├── index.html
│   ├── vite.config.js
│   └── src/
│       ├── main.js
│       ├── App.vue
│       ├── router/
│       │   └── index.js
│       ├── assets/
│       ├── components/
│       │   ├── common/
│       │   ├── map/
│       │   ├── parking/
│       │   ├── booking/
│       │   └── admin/
│       ├── layouts/
│       ├── pages/
│       │   ├── public/
│       │   ├── booking/
│       │   └── admin/
│       ├── services/
│       │   ├── apiClient.js
│       │   ├── parkingService.js
│       │   ├── bookingService.js
│       │   ├── aiSearchService.js
│       │   └── adminService.js
│       ├── composables/
│       ├── stores/          # only if Pinia is adopted
│       ├── utils/
│       └── styles/
└── backend/
    ├── app/
    │   ├── Http/Controllers/Api/V1/
    │   ├── Http/Requests/
    │   ├── Http/Resources/
    │   ├── Models/
    │   ├── Policies/
    │   ├── Services/
    │   ├── Actions/          # optional, for complex use cases
    │   ├── Jobs/             # only if async jobs are used
    │   └── Enums/            # if supported by chosen PHP version
    ├── bootstrap/
    ├── config/
    ├── database/
    │   ├── migrations/
    │   ├── factories/
    │   └── seeders/
    ├── routes/
    │   └── api.php
    ├── tests/
    │   ├── Feature/
    │   └── Unit/
    ├── .env.example
    ├── composer.json
    └── composer.lock
```

This is a suggested layout, not a requirement to create every empty directory immediately. Keep the architecture simple; create folders when there is real code to put in them.

### 4.2 Frontend conventions

- Use Vue 3 Composition API and single-file components (`.vue`). Prefer `<script setup>` for component logic.
- Keep page-level components focused on page orchestration. Reusable form controls, parking cards, map markers and booking summaries belong in reusable components.
- Keep HTTP calls in `src/services/`, not duplicated throughout UI components.
- Keep business-critical calculations out of frontend code. The frontend may show a live estimate for UX, but Laravel must return the authoritative quote.
- Use Vue Router for public pages and admin routes. Protect the admin experience with route guards for UX, but always enforce access in Laravel too.
- Use semantic HTML, keyboard-accessible controls, visible labels, focus states and understandable validation messages.
- Use Tailwind utility classes consistently. Avoid building a large bespoke CSS framework on top of Tailwind.
- Keep loading, empty, validation-error, network-error and success states explicit for all asynchronous screens.
- Never put secret values in Vite variables. Any `VITE_*` variable is exposed to browser code.

### 4.3 Backend conventions

- Use Laravel routes, Form Request validation, Eloquent models/relationships, API Resources and focused service classes.
- Controllers should coordinate HTTP requests and responses, not hold all pricing, payment, AI or booking business logic.
- Proposed services: `ParkingSearchService`, `PricingService`, `AvailabilityService`, `BookingService`, `PaymentService`, `InvoiceService`, `GeminiRequestParser`, and `RecommendationService`.
- Use database transactions for booking/availability changes and payment state updates that must be atomic.
- Apply authorization middleware/policies to every admin or operator management endpoint.
- Use pagination for administrative lists and reasonable limits for public search.
- Keep all integration keys and credentials in backend environment configuration.
- Use API Resources to return deliberate public fields only; do not serialize internal models and secrets directly.

---

## 5. Frontend Technical Design

### 5.1 Required libraries

Minimum frontend dependencies:

- `vue` — interface framework.
- `vue-router` — route navigation.
- `axios` — API client.
- `vite` and its Vue plugin — development/build tooling.
- Tailwind CSS packages appropriate for the selected Tailwind major version.

Recommended development dependencies include the Vue language tooling extension in VS Code and a test runner such as Vitest with Vue Test Utils. Add a library only when it solves a concrete need and confirm it works with the selected stack.

### 5.2 Page/route map

| Route | Page | Access | Purpose |
|---|---|---|---|
| `/` | Landing page | Public | Introduce Parkly and start a search |
| `/search` | Parking search | Public | Choose location and view map/list results |
| `/parking/:id` | Parking detail | Public | Review details, price, vehicle support and recorded availability |
| `/booking/:id/review` | Booking review | Public/guest flow | Select vehicle/duration, display authoritative quote, enter required details |
| `/booking/:id/payment` | Payment status/redirect flow | Public booking context | Continue payment or view result; never trust redirect alone |
| `/booking/confirmation/:bookingReference` | Booking confirmation | Limited booking access | Present confirmed booking details and invoice |
| `/admin/login` | Administrator sign-in | Public login route | Authenticate authorized admin |
| `/admin` | Admin dashboard | Admin only | Overview of facilities, updates and recent bookings |
| `/admin/facilities` | Facility list/management | Admin or approved operator | Create/update registered facility records |
| `/admin/facilities/:id` | Facility editor | Admin or approved operator | Edit location, supported vehicle types, rates and status |
| `/admin/availability` | Availability management | Admin or approved operator | Update recorded available-space counts |
| `/admin/bookings` | Booking management | Admin or approved operator | Review bookings and update allowed status |
| `/admin/bookings/:id` | Booking detail | Admin or approved operator | Inspect booking/payment state and confirmation information |

Route names can change, but public versus protected access boundaries must remain.

### 5.3 Frontend API client

Create a central Axios instance. Its base URL must come from `VITE_API_BASE_URL` and default to the local Laravel API URL during development. Add common JSON headers, handle expected API error formats centrally, and avoid logging secrets or sensitive user information.

Do not automatically retry non-idempotent booking/payment requests without an idempotency strategy. For authentication, use Laravel Sanctum's first-party SPA cookie flow if adopted; configure credentials, CSRF, CORS and stateful domains consistently. Keep frontend route guards as a convenience only, not a security boundary.

### 5.4 Location and map behavior

- Browser geolocation is requested only after the user chooses the GPS option.
- Manual location is always available as an alternative.
- Use registered facility latitude/longitude from Parkly's own database to place bookable parking markers.
- Places API can help users search for/resolve a location, but an external place is not a bookable Parkly facility unless it has been registered and approved in the Parkly database.
- Use a lazy-loaded map component so non-map pages do not need to initialize Google Maps.
- Display a clear fallback if the Maps API is unavailable or the key/quota is invalid. Search results should remain usable as a list when possible.
- Restrict the Maps browser key by allowed referrers and enabled APIs. Never use a server API key in browser code.

### 5.5 Frontend state

For the prototype, use local component state for page-specific form state and URL query parameters for shareable search criteria where sensible. Introduce Pinia only if state must be shared across many distant components, such as a multi-step booking flow. Avoid storing secrets or treating frontend state as authoritative booking state.

---

## 6. Backend Technical Design

### 6.1 API versioning and route organization

Use JSON REST endpoints under `/api/v1`. Keep route definitions grouped by public search, booking/payment, and protected administration. Use explicit route names and middleware.

Recommended API response pattern:

```json
{
  "data": {},
  "meta": {},
  "message": "Optional user-facing message"
}
```

For validation errors, follow Laravel's conventional JSON validation response or standardize a documented error shape. Do not return stack traces, SQL details, secrets or provider payloads to end users.

### 6.2 Request validation

Create Laravel Form Request classes for non-trivial writes/search operations. Validate:

- Required types and lengths.
- Valid facility and vehicle-type IDs.
- Positive duration within supported limits.
- Valid location coordinates and bounded search radius if a radius is implemented.
- Supported sort/filter values and maximum page/result sizes.
- Numeric currency amounts only from server-side calculation; reject client-supplied totals as authoritative.
- Admin-only input changes using authorization, not validation alone.
- Idempotency/reference fields where applicable.

### 6.3 Business services

Suggested responsibilities:

- `ParkingSearchService`: reads registered facility records, filters by location/vehicle/availability as available, calculates distance, and applies stable ordering.
- `PricingService`: identifies the applicable rate and returns a server-calculated quote; stores an immutable price snapshot on booking.
- `AvailabilityService`: reads/updates recorded availability, applies stale-data rules once approved, and handles safe reservation/decrement logic.
- `BookingService`: validates a booking request, coordinates availability and pricing under a transaction, creates/updates booking state, and handles allowed transitions.
- `PaymentService`: creates provider sessions, stores payment references, validates server-side provider responses and processes callbacks idempotently.
- `InvoiceService`: creates invoice data from saved booking/payment records, not from browser-submitted values.
- `GeminiRequestParser`: calls Gemini using server-side credentials and validates a strict structured response.
- `RecommendationService`: turns parsed filters plus database results into a ranked list using deterministic application logic.

These services can be fewer classes if the implementation remains clear. Do not build an over-engineered service layer for trivial logic.

### 6.4 Timeouts, failures and logging

- Set explicit connection and request timeouts for external services.
- Handle maps, Gemini and payment-provider outages without crashing unrelated functionality.
- Log a correlation/request ID with server-side errors so the team can trace a failed demo.
- Do not log API keys, passwords, session cookies, full payment credentials, or unnecessary precise-location data.
- Return helpful user-facing error messages while keeping provider details in safe server logs.
- Make AI-assisted search optional to ordinary location search: if Gemini fails, regular search should still work.

---

## 7. Proposed Relational Data Model

This schema is a technical baseline derived from the PRD, not a replacement for the team's final ER diagram. Use Laravel migrations as the source of schema truth. Add foreign keys, unique constraints, indexes and deletion rules explicitly.

### 7.1 Relationship summary

```text
users
  |-- manages (created_by / updated_by) --> parking_facilities
  |-- records --> availability_updates
  |-- receives --> admin_notifications

parking_facilities
  |-- has many --> facility_vehicle_rates
  |-- has many --> facility_availabilities
  |-- has many --> bookings

vehicle_types
  |-- has many --> facility_vehicle_rates
  |-- has many --> facility_availabilities
  |-- referenced by --> bookings

bookings
  |-- has many --> payments (or one current payment plus an audit history)
  |-- has one / many --> invoice records (implementation dependent)
  |-- may be referenced by --> admin_notifications
```

### 7.2 `users`

Purpose: administrator accounts and any approved operator accounts. Public browsing does not require an account.

Suggested fields:

- `id` (primary key)
- `name`
- `email` (unique)
- `password` (hashed by Laravel; never plain text)
- `role` or approved role/permission relation (`admin`, optionally `operator`)
- `is_active`
- `email_verified_at` if email verification is adopted
- timestamps

Do not build end-user customer accounts unless that requirement is approved. If the implementation chooses only administrators, keep user roles simple. If operators receive accounts, enforce facility-level permissions before they can update records.

### 7.3 `locations` / administrative areas

Purpose: support manual Division > District > Thana/Upazila > Area selection.

Possible fields:

- `id`, `name`, `type` (division, district, thana/upazila, area), `parent_id`, optional normalized code, timestamps.

A hierarchical parent relation avoids duplicating every level in each facility row. The exact dataset and whether Area is a formal administrative level or a user-defined locality must be decided. If a reliable structured dataset is not available within the schedule, begin with a controlled, manually seeded location dataset for the demo and document its limits.

### 7.4 `parking_facilities`

Purpose: store the registered facilities that can appear as Parkly bookable results.

Suggested fields:

- `id`
- `name`
- `description` (optional)
- `address_text`
- `location_id` (nullable foreign key to administrative area, if adopted)
- `latitude` and `longitude` (decimal fields with documented precision)
- `is_active` (whether facility is available for search/bookings)
- `created_by`, `updated_by` (nullable foreign keys to users)
- timestamps and optional `deleted_at` if soft deletion is needed

Rules:

- An active/bookable facility should have enough required data to identify its location and pricing.
- Latitude must be in `[-90, 90]` and longitude in `[-180, 180]`.
- Disabling a facility must not erase historical bookings.
- Keep coordinates separate from a Google Place ID; one identifies Parkly's registered facility record, the other identifies an external mapping-service place when used.

### 7.5 `vehicle_types`

Purpose: maintain canonical vehicle categories supported by the prototype.

Suggested fields: `id`, `name`, `slug`, `is_active`, timestamps.

The final category list (for example, car, motorcycle, other vehicle types) must be approved. Do not infer that all facilities support every type.

### 7.6 `facility_vehicle_rates`

Purpose: define which vehicle types a facility accepts and the price rule for each combination.

Suggested fields:

- `id`
- `parking_facility_id`
- `vehicle_type_id`
- `rate_amount` as `DECIMAL`, not floating point
- `rate_unit` (e.g. hour) or a reference to a pricing-rule definition
- `currency` defaulting to `BDT` for the initial Bangladesh-focused prototype
- `is_active`
- timestamps

A unique constraint on `(parking_facility_id, vehicle_type_id, rate_unit)` may be appropriate if only one active rate per combination is allowed. If more complex rates are required, design a separate pricing-rules table only after rules are approved.

**Open decision:** The proposal does not define hourly increments, rounding, minimum charges, grace periods, overtime, tax or service fees. Do not invent them invisibly. Select and document a simple MVP rule before implementation.

### 7.7 `facility_availabilities`

Purpose: store manually reported available-space information.

**Recommended MVP model:** Count-based availability per facility and vehicle type, rather than individual physical bay IDs. This is simpler for a limited prototype. Team/supervisor approval is required because the proposal leaves the data model open.

Suggested fields:

- `id`
- `parking_facility_id`
- `vehicle_type_id`
- `total_slots`
- `available_slots`
- `last_updated_by` (user foreign key where appropriate)
- `last_updated_at`
- timestamps

Rules:

- `total_slots >= 0`.
- `available_slots >= 0` and `available_slots <= total_slots`.
- Update the availability record transactionally.
- Record update time and who performed the update.
- Search results must not describe the count as guaranteed live occupancy.
- If availability is missing, represent it as unknown rather than silently setting it to zero or to the total capacity.

A separate `availability_updates` audit table is recommended if the team needs to retain a history of manual edits. It should record old/new counts, actor, timestamp and optional reason; do not store unnecessary user-location data.

### 7.8 `bookings`

Purpose: store the authoritative booking and the amount agreed at booking time.

Suggested fields:

- `id`
- `booking_reference` (unique, non-sequential public reference)
- `parking_facility_id`
- `vehicle_type_id`
- required customer contact fields selected by the team (do not require a customer login unless scope changes)
- `duration_minutes` or equivalent normalized duration representation
- `planned_start_at` if start time is part of booking; the proposal does not fully specify scheduled start time, so confirm before adoption
- `price_amount` as `DECIMAL`, copied from the backend quote at booking time
- `currency`
- `booking_status`
- `payment_status`
- `availability_snapshot` or sufficient linked/snapshot fields for auditing, if approved
- `confirmed_at`, `cancelled_at`, and timestamps where relevant

Rules:

- Store the agreed price snapshot; a future rate change must not silently change a confirmed booking invoice.
- Public `booking_reference` is not an authentication secret by itself; restrict which customer data can be retrieved from booking lookups.
- Use an explicit status-transition policy; never permit arbitrary status changes from any API caller.
- Do not create a confirmed booking based only on a frontend claim that payment succeeded.

**Open decision:** Required name/contact fields, booking start-time model, cancellation behavior, and whether payment-pending bookings reserve availability need approval.

### 7.9 `payments`

Purpose: track provider interaction without storing raw payment credentials.

Suggested fields:

- `id`
- `booking_id`
- `provider` (e.g. `sslcommerz_sandbox`, `mock_demo` only if approved)
- `provider_transaction_id` or provider reference, unique where appropriate
- `amount`, `currency`
- `payment_status` (`initiated`, `pending`, `paid`, `failed`, `cancelled`, or provider-relevant states)
- provider response reference/limited safe metadata only
- `verified_at`, timestamps

Rules:

- Never store full card numbers, CVV, or provider login credentials.
- Verify callback/IPN status server-side before marking a payment paid.
- Callback processing must be idempotent.
- Preserve payment attempts for debugging/audit rather than overwriting all history in one field, where reasonable for the prototype.

### 7.10 `admin_notifications`

Purpose: show an administrator that a new booking has arrived.

Suggested fields: `id`, `recipient_user_id`, `booking_id`, `type`, `message` or reference data, `read_at`, timestamps.

For the MVP, an in-app notification list or dashboard counter is enough unless email/push notifications are separately approved. Do not silently add email/SMS service dependencies.

### 7.11 `invoices` (optional separate table)

If invoices are generated on demand from immutable booking/payment snapshots, a separate invoice table may not be necessary for the first prototype. If invoice numbers, issue times, or rendered-file references must be persisted, use an `invoices` table with a unique invoice number, booking foreign key, issue timestamp and immutable amount summary. Avoid duplicate invoices when payment callbacks are repeated.

### 7.12 Indexes and database integrity

Recommended baseline indexes:

- `parking_facilities(is_active)`
- Facility coordinates for filtering/ordering as supported by the chosen DB; for a small dataset, a simple bounding-box query plus Haversine distance in application logic may suffice.
- `facility_availabilities(parking_facility_id, vehicle_type_id)` unique if one record per combination is intended.
- `facility_vehicle_rates(parking_facility_id, vehicle_type_id)`
- `bookings(booking_reference)` unique
- `bookings(parking_facility_id, booking_status)`
- `bookings(created_at)`
- `payments(provider_transaction_id)` unique if provider semantics allow it
- `admin_notifications(recipient_user_id, read_at)`

Use foreign keys and appropriate delete/update rules. Prefer deactivation/soft deletion for facilities with historical bookings instead of destructive cascading deletes.

---

## 8. API Contract

All endpoint examples are proposed; exact routes can be adjusted if the final contract stays documented and consistent between frontend, backend, tests and this file. Prefix endpoints with `/api/v1`.

### 8.1 Public endpoints

| Method | Endpoint | Purpose | Input / access |
|---|---|---|---|
| `GET` | `/api/v1/health` | Basic API availability check | Public; return no secret/config details |
| `GET` | `/api/v1/locations` | Return seeded location hierarchy or search locations | Optional `q`, `parent_id`, `type` |
| `GET` | `/api/v1/parking-facilities` | Search registered facilities | `lat`, `lng` or `location_id`/query; optional `vehicle_type_id`, `limit`, `page` |
| `GET` | `/api/v1/parking-facilities/{id}` | Facility detail | Public active facility only |
| `GET` | `/api/v1/parking-facilities/{id}/quote` | Calculate an indicative fee | `vehicle_type_id`, duration fields; backend remains authoritative |
| `POST` | `/api/v1/ai/parse-parking-request` | Parse natural-language requirements and return recommendations | Public but rate-limited; input length capped |
| `POST` | `/api/v1/bookings/quote` | Validate selection and calculate final quote | Public; backend validates all fields |
| `POST` | `/api/v1/bookings` | Create booking or payment-pending booking | Public; strict validation and duplicate-submission protection |
| `GET` | `/api/v1/bookings/{reference}/confirmation` | Retrieve limited confirmation for a booking | Limited disclosure; require sufficient verification if contact details are exposed |
| `POST` | `/api/v1/payments/initialize` | Create provider payment session | Public booking context; server confirms amount |
| `POST` | `/api/v1/payments/sslcommerz/ipn` | Process provider IPN/callback | Provider callback; validate with provider/server-side verification |
| `GET` | `/api/v1/payments/{reference}/status` | Return minimal payment state | Reference access must be constrained |

### 8.2 Protected administrative endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| `POST` | `/api/v1/admin/login` or Laravel auth route | Administrator sign-in; exact auth implementation follows Sanctum setup |
| `POST` | `/api/v1/admin/logout` | End admin session |
| `GET` | `/api/v1/admin/me` | Current authenticated admin/role |
| `GET` | `/api/v1/admin/facilities` | Paginated facility list |
| `POST` | `/api/v1/admin/facilities` | Create facility |
| `GET` | `/api/v1/admin/facilities/{id}` | Load editable facility details |
| `PUT/PATCH` | `/api/v1/admin/facilities/{id}` | Update facility |
| `PATCH` | `/api/v1/admin/facilities/{id}/status` | Activate/deactivate facility, if implemented |
| `PUT/PATCH` | `/api/v1/admin/facilities/{id}/rates` | Update supported vehicle types and pricing |
| `PUT/PATCH` | `/api/v1/admin/facilities/{id}/availability` | Update recorded availability counts |
| `GET` | `/api/v1/admin/bookings` | Filter/paginate bookings |
| `GET` | `/api/v1/admin/bookings/{id}` | View booking details |
| `PATCH` | `/api/v1/admin/bookings/{id}/status` | Change only permitted booking statuses |
| `GET` | `/api/v1/admin/notifications` | View notifications |
| `PATCH` | `/api/v1/admin/notifications/{id}/read` | Mark notification read |

The `/admin` prefix is illustrative. Laravel auth endpoints can be supplied by the chosen auth setup; do not define duplicate login logic without understanding Sanctum/session configuration.

### 8.3 Search request example

```http
GET /api/v1/parking-facilities?lat=23.8041&lng=90.4152&vehicle_type_id=1&limit=5
Accept: application/json
```

Illustrative response:

```json
{
  "data": [
    {
      "id": 12,
      "name": "Registered Demo Parking",
      "address": "Example Road, Dhaka",
      "latitude": 23.8041,
      "longitude": 90.4152,
      "distance_km": 0.6,
      "vehicle_types": ["car"],
      "price": {
        "amount": "60.00",
        "currency": "BDT",
        "unit": "hour"
      },
      "availability": {
        "available_slots": 8,
        "last_updated_at": "2026-10-13T10:00:00Z",
        "freshness": "recorded"
      }
    }
  ],
  "meta": {"limit": 5},
  "message": "Nearby registered parking facilities"
}
```

Data above is an example only. Never hardcode the sample facility, price, distance or availability as real Parkly data.

### 8.4 Booking quote example

```http
POST /api/v1/bookings/quote
Content-Type: application/json
Accept: application/json

{
  "parking_facility_id": 12,
  "vehicle_type_id": 1,
  "duration_minutes": 180
}
```

Illustrative response:

```json
{
  "data": {
    "parking_facility_id": 12,
    "vehicle_type_id": 1,
    "duration_minutes": 180,
    "unit_rate": "60.00",
    "unit": "hour",
    "total_amount": "180.00",
    "currency": "BDT",
    "quote_expires_at": null
  }
}
```

The actual amount must be computed by Laravel using the configured pricing rules. Quote expiry can remain absent until the team defines quote-expiration policy.

### 8.5 AI parse request/response example

Request:

```json
{
  "request_text": "I need affordable parking in Mirpur for a car for 3 hours",
  "location_context": null
}
```

Validated parser output (example):

```json
{
  "data": {
    "location_text": "Mirpur",
    "vehicle_type_slug": "car",
    "duration_minutes": 180,
    "budget_amount_bdt": null,
    "missing_requirements": [],
    "interpretation_confidence": "medium"
  },
  "meta": {
    "recommendations_generated_from_database": true
  }
}
```

The actual recommendation response should include only IDs and display fields for registered database records. If no result matches, return an empty result with a clear message, not an invented facility.

### 8.6 HTTP status behavior

Use ordinary HTTP status codes consistently:

- `200 OK`: successful read/update when appropriate.
- `201 Created`: a new booking or resource was created.
- `204 No Content`: successful operation with no response body if used consistently.
- `400 Bad Request`: malformed request.
- `401 Unauthorized`: sign-in required or session missing.
- `403 Forbidden`: authenticated user lacks permission.
- `404 Not Found`: unknown/non-visible resource.
- `409 Conflict`: insufficient recorded availability, duplicate/conflicting state, or invalid transition.
- `422 Unprocessable Entity`: validation errors.
- `429 Too Many Requests`: rate limited AI/search request.
- `502/503`: external service unavailable when appropriate.

Never expose internal exception traces or SQL errors to the public frontend.

---

## 9. Location Search and Distance Calculation

### 9.1 Search input

The search service can receive:

- GPS coordinates from browser geolocation, when permission is granted; or
- a manually selected location ID and/or resolved coordinates; or
- ordinary typed location text that has been resolved to a location context.

Do not send the user's precise GPS position to Gemini when only local deterministic search is needed. The AI should receive the minimum context needed to understand the text request.

### 9.2 Matching sequence

Recommended deterministic process:

1. Validate and normalize query parameters.
2. Resolve the search center from coordinates or the approved manual location flow.
3. Query active registered `parking_facilities` with sufficient location data.
4. Apply hard filters for supported vehicle type and known availability when those are required by the user/flow.
5. Calculate approximate distance using the Haversine formula or a database-supported spatial query if warranted by the dataset size.
6. Sort by distance and apply a bounded result limit (target up to five initially).
7. Attach the current configured rate and recorded availability data from the database.
8. Label availability unknown or stale according to the finalized policy.

For a small prototype dataset, compute distance using stored latitude/longitude and application logic. Do not introduce PostGIS or another spatial database only for a small demonstration dataset unless performance or scope justifies it.

### 9.3 Google Maps responsibilities

- Render the map and markers for Parkly facility records.
- Optionally use Places API for autocomplete, place resolution, and location-related search support.
- Keep Google API key restrictions and billing/quota awareness in the setup docs.
- Respect the provider's current rules for storing/caching and displaying place data.
- Never assume a Google Maps place is registered, bookable, or has available slots merely because it appears on the map.

---

## 10. AI Integration Design

### 10.1 Responsibility boundary

Gemini has one bounded role: **understand natural-language parking requests and extract structured search requirements**. The application's own code filters and ranks registered Parkly records. Gemini does not create facility records, fabricate slot counts/prices, calculate authoritative payment amounts, confirm a booking, or change database state by itself.

### 10.2 Server-side integration

- Call Gemini from Laravel using Laravel's HTTP client or a compatible client supported by the selected API and PHP setup.
- Keep `GEMINI_API_KEY` and `GEMINI_MODEL` in the backend `.env`; never put them in `VITE_*` variables or expose them to the browser.
- Set request timeout and bounded retry policy. Avoid retrying indefinitely.
- Use a current supported Gemini model selected from official documentation at implementation time; keep the model name configurable so it can be changed without code edits.
- Ask Gemini for structured JSON output using a schema. Do not parse arbitrary prose with brittle substring rules.
- Validate every returned field on the server and map recognized vehicle names to known `vehicle_types` rows. Reject unknown types or ask the user to clarify instead of inventing a category.

### 10.3 Proposed structured output schema

```json
{
  "location_text": "string or null",
  "vehicle_type_slug": "known slug or null",
  "duration_minutes": "integer or null",
  "budget_amount_bdt": "number or null",
  "additional_constraints": [],
  "missing_requirements": [],
  "clarification_question": "string or null"
}
```

This is an application contract, not a claim that Gemini will always return valid values. Laravel must validate types, lengths, enum membership and reasonable bounds after receiving the output.

### 10.4 AI recommendation algorithm

1. Receive request text and minimal optional location context.
2. Call Gemini to extract structured requirements.
3. Validate and normalize extracted values.
4. Resolve the parsed vehicle type and location against known app records/location services.
5. Query registered active facilities from MySQL/MariaDB.
6. Apply hard constraints: vehicle compatibility, registered facility state, known available count when the user requires a currently available space, and explicit budget when provided.
7. Rank remaining results deterministically. Suggested first version: distance first, then price for similar distances; if the user explicitly prioritizes affordability, prioritize price while keeping other hard constraints.
8. Return actual records with their stored IDs, prices and availability timestamps.
9. If no results match, return a no-match result and optional clarification question.

Do not use an unexplained AI-generated score as the only ranking mechanism. Document any later scoring formula and test it against fixture data.

### 10.5 Failure behavior and fallback

- If Gemini times out, returns invalid JSON, is rate limited, or is unavailable, show that AI search is temporarily unavailable.
- Keep ordinary GPS/manual search operational whenever possible.
- Never show a generated facility that did not come from Parkly's database.
- If availability is missing or old, communicate the limitation; do not claim the space is definitely available.
- Rate-limit the endpoint and cap request text length to prevent abuse/cost spikes.
- Avoid logging raw natural-language text if it includes personal information; use safe structured diagnostic logging.

---

## 11. Pricing, Availability and Booking Integrity

This is a critical area: booking state must remain consistent even if two visitors try to book at nearly the same time.

### 11.1 Fee calculation

The backend must be authoritative. Given facility, vehicle category, duration and active rate configuration, `PricingService` returns a quote. The frontend can display the response but must not submit a trusted total price.

Use `DECIMAL` storage and decimal-safe handling for monetary values. Use currency `BDT` for the initial scope. Keep rate, duration, total, currency, and the selected rule snapshot with the booking to make the invoice reproducible.

### 11.2 Availability source and freshness

Availability is operator/admin-maintained data. Store `last_updated_at` and actor when available. The project must choose a freshness threshold and message before implementation; do not invent a promise such as “live availability”. If a record is stale or absent, show “last reported” or “availability not confirmed” according to the UI decision.

### 11.3 Prevent overbooking

Recommended transaction pattern:

1. Begin a database transaction.
2. Lock or atomically update the relevant availability record using a database-safe operation supported by the target engine.
3. Re-check the available count using current database state.
4. Revalidate the rate and vehicle compatibility.
5. Create a booking/payment-pending record and reserve/decrement availability according to the approved reservation policy.
6. Commit the transaction.

If any step fails, roll back and return an appropriate conflict/validation response. Do not read an availability number in one request and later trust that stale number during booking creation.

**Reservation policy is an open business decision:** For payment-pending bookings, the team must decide whether a slot is held and for how long, when it is restored after cancellation/failure, and how expirations run. A short hold (for example, 10 minutes) can be considered but must not be implemented as an approved business rule without team approval. If no hold is implemented, design carefully to prevent two successful bookings exceeding recorded capacity.

### 11.4 Booking status model

Proposed initial booking statuses:

- `pending_payment`
- `confirmed`
- `cancelled`
- `completed`

Payment statuses may be separate:

- `initiated`
- `pending`
- `paid`
- `failed`
- `cancelled`

Keep booking status and payment status distinct. A payment failure is not the same concept as a user's booking cancellation. Define allowed transitions in a service/policy and test every transition. Final statuses must be confirmed by the team.

---

## 12. Payment Integration

### 12.1 Payment scope

Use SSLCOMMERZ sandbox only if access is available and the core workflow is stable. Treat live payment collection as out of scope for the prototype unless the supervisor explicitly approves an expanded scope.

### 12.2 Required flow

1. Frontend submits a booking intent; Laravel creates the appropriate pending state and calculates the amount.
2. Laravel creates a provider payment session using server-side credentials and the authoritative amount.
3. Frontend redirects to the provider's sandbox checkout using the returned checkout URL/reference.
4. Provider returns the browser to success/fail/cancel routes and may send server-to-server/IPN messages according to its documented protocol.
5. Laravel validates the provider response using the provider's required server-side validation mechanism.
6. Process the result idempotently and update payment/booking state in a transaction.
7. Generate the paid invoice only when server-verified payment success meets the approved booking rules.
8. A duplicate callback must not create duplicate invoices or double-decrement availability.

### 12.3 Security and fallback

- Store provider credentials only in backend environment variables.
- Do not store raw card number, CVV, or payment login details.
- Do not mark a booking paid only because the user landed at `/success`.
- Validate callback amount, currency, transaction reference and booking association.
- Log safe transaction references and outcome, not secrets.
- If sandbox credentials or approval are unavailable, document the blocker and ask the supervisor whether a clearly labelled mock/demo payment mode is acceptable. Never silently represent a mock as real payment integration.

---

## 13. Invoice and Facility Verification

### 13.1 Invoice contents

Generate invoice/confirmation data from the persisted booking and verified payment record. At minimum include:

- Parkly branding.
- Unique booking reference.
- Facility name/address.
- Vehicle type.
- Parking duration and any approved timing details.
- Confirmed total amount and currency.
- Booking/payment state and confirmation timestamp.

Do not expose internal database IDs as the only public identifier. Do not expose customer phone/email to unrelated users.

### 13.2 HTML and PDF

The MVP should first produce a correct HTML confirmation view. If the PDF is included, generate it server-side from the same invoice data and store a file only if storage/retention is defined. Do not maintain two separate implementations of amount calculation or booking details for HTML and PDF.

### 13.3 Manual staff verification

The proposal requires a user to present the invoice and booking details to parking staff before entry. It does not mandate QR scanning or a separate staff account. The technical implementation needs an agreed way for authorized staff to check a booking reference against the record, such as a restricted admin lookup screen for the prototype. Do not expose a public booking lookup that leaks customer details just because someone knows a reference.

---

## 14. Authentication and Authorization

### 14.1 Access classes

- **Public visitor:** Can browse/search registered active facilities and initiate the approved guest booking flow.
- **Administrator:** Can manage facilities, prices, availability and bookings.
- **Operator:** Only if enabled/approved; may update assigned facility data and should not automatically inherit full admin privileges.
- **Parking staff:** Verification workflow only if an additional role/screen is explicitly approved; not required to add a separate login in the current scope.

### 14.2 Recommended SPA authentication

For the first-party Vue SPA, consider Laravel Sanctum's cookie/session-based SPA authentication rather than inventing a custom token scheme. Configure the SPA/backend hostnames, stateful domains, credentials, CSRF cookie, CORS and session cookie correctly. Do not place long-lived admin access tokens in localStorage by default.

For local development with separate Vite and Laravel ports, follow Sanctum's exact current setup steps and verify CSRF behavior. If the deployment topology changes, revisit cookie/domain configuration.

### 14.3 Authorization rules

- Backend middleware/policies protect every administrative mutation and protected read endpoint.
- Frontend route guards improve UX but do not secure data.
- An operator, if supported, must only modify facilities they are assigned to.
- Validate current user, allowed role, resource ownership and operation at the backend.
- Use Laravel password hashing and standard session protections; never create a plain-text password table.

---

## 15. Security and Privacy Requirements

1. **Secrets:** `.env` files must not be committed. Keep `.env.example` without real keys/passwords. Never place Gemini or payment secrets in browser variables.
2. **Validation:** Validate all browser input server-side, even if Vue already validates it.
3. **SQL:** Use Eloquent/query builder parameterization; do not concatenate untrusted input into SQL.
4. **Authorization:** Restrict every admin/operator endpoint and verify role/resource scope.
5. **CSRF/CORS:** Configure for the actual SPA topology; do not solve local CORS issues by allowing all origins in production.
6. **Rate limiting:** Apply endpoint limits to sign-in, AI parsing, public search, booking creation and confirmation lookup as suitable.
7. **Payment:** Server-verify provider status and handle callbacks idempotently.
8. **Data minimization:** Collect only contact and booking information necessary for booking and verification.
9. **Location:** Ask before using GPS; provide manual search and avoid retaining precise GPS location without a concrete requirement.
10. **Logs:** Do not log passwords, keys, session cookies, full payment payloads, CVV or unnecessary personal content.
11. **Errors:** User responses must not contain stack traces, secrets, SQL queries or filesystem details.
12. **Dependency hygiene:** Use lockfiles, stable packages, official docs and package review; do not run untrusted shell scripts without inspection.
13. **HTTPS:** Use HTTPS in deployment; local HTTP is acceptable for local development only.
14. **AI grounding:** Externally generated text must be validated and matched with actual database IDs before it can become a recommendation.

---

## 16. Environment Configuration

Maintain separate `.env` files in `frontend` and `backend`. Commit only the corresponding `.env.example` files.

### 16.1 Frontend `.env.example`

```dotenv
VITE_API_BASE_URL=http://127.0.0.1:8000/api/v1
VITE_GOOGLE_MAPS_BROWSER_KEY=replace_with_restricted_browser_key
```

All `VITE_*` values are public at build time. The Maps key may be used in the browser only with appropriate API and referrer restrictions. Do not put `GEMINI_API_KEY`, SSLCOMMERZ secret credentials, database password, or other server secrets here.

### 16.2 Backend `.env.example`

```dotenv
APP_NAME=Parkly
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Dhaka

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parkly
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=localhost

GEMINI_API_KEY=
GEMINI_MODEL=

SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWORD=
SSLCOMMERZ_SANDBOX=true
SSLCOMMERZ_VALIDATION_URL=
```

This is an example only. Confirm your XAMPP host, port, username and password. Some XAMPP installations use an empty local root password; do not assume it. Keep real credentials private. Add only environment values actually needed by the implementation.

### 16.3 Setup validation commands

Before creating/installing dependencies, inspect the existing tools in the same VS Code terminal that will run the project:

```powershell
php -v
where.exe php
php -m
composer --version
node -v
npm -v
git --version
```

Check the database version in phpMyAdmin or run:

```sql
SELECT VERSION();
```

After the backend project exists, typical verification commands are:

```bash
composer install
php artisan key:generate
php artisan migrate:status
php artisan migrate
php artisan test
```

After frontend scaffold exists:

```bash
npm install
npm run dev
npm run build
```

Run only commands appropriate for the generated project. `php artisan key:generate` belongs in the backend project and must not be run against an unrelated directory. Review destructive migration/reset commands before use; never run `migrate:fresh` against a database with useful data.

---

## 17. API Error Handling and Observability

### 17.1 Frontend behavior

Every API-backed page should define:

- Idle state.
- Loading state.
- Success state.
- Empty result state.
- Validation failure state.
- Network/server error state.
- External service unavailable state, where applicable.

Show messages that help the user recover. Preserve safe user input after a recoverable error; do not duplicate bookings when retrying.

### 17.2 Backend behavior

- Use Laravel validation exceptions and consistent JSON error responses.
- Use `409 Conflict` for a valid-looking booking request that conflicts with current availability/state.
- Use `401`/`403` correctly for authentication/authorization failures.
- Keep application logs readable and include a request/correlation ID where practical.
- Record booking/payment status transitions with timestamps. If a full audit table is excessive, maintain enough safe records to debug state changes.
- For AI/map/payment upstream failures, record provider-safe diagnostics and return a stable app-level error instead of leaking upstream secrets/responses.

---

## 18. Testing Plan

### 18.1 Backend tests

Use Laravel's supported test runner (PHPUnit by default or Pest if the team selects it). Prioritize:

- Facility search by coordinates/manual location.
- Facility active/inactive behavior.
- Vehicle compatibility and pricing rules.
- Fee calculation and price snapshot storage.
- Availability validation and concurrent booking protection.
- Allowed/blocked booking status transitions.
- Guest booking validation.
- Admin authentication and authorization on all write endpoints.
- Facility/rate/availability updates.
- Gemini parser output schema validation, unknown vehicle category and invalid JSON.
- Recommendation results are all backed by actual database record IDs.
- No-match and stale/missing availability responses.
- Payment callback validation and duplicate callback/idempotency.
- Invoice content is consistent with saved booking/payment records.

Use factories/seeders for repeatable data. Do not make ordinary unit tests depend on live Google/Gemini/SSLCOMMERZ calls; mock their responses. Keep a small manual integration test plan for valid API keys/sandbox access.

### 18.2 Frontend tests

Use Vitest and Vue Test Utils if adopted. Test:

- Location form selection and GPS denial fallback.
- Search loading, no-result and error states.
- Parking cards show recorded data and handle unknown availability.
- Vehicle/duration form validation and quote refresh.
- Booking submit-disabled/loading behavior and duplicate-submit protection.
- Admin form validation and permission-related redirects.
- AI search invalid/unavailable response state.
- Responsive navigation and key keyboard interactions.

### 18.3 Manual API testing

Maintain a Postman collection in `docs/` or an exported project collection. Add example environment variables without secrets. Cover public routes, invalid payloads, protected routes and payment mock/sandbox scenarios.

### 18.4 End-to-end acceptance scenarios

1. GPS granted -> nearby registered facilities -> facility detail -> price quote -> booking -> provider/mock-approved payment path -> invoice/confirmation.
2. GPS denied -> manual Division/District/Thana/Area or typed location -> relevant registered results.
3. Natural-language request -> Gemini structured extraction -> server-side database matching -> actual registered recommendations.
4. No matching records -> no fabricated results; show a useful no-match message.
5. Admin changes rate -> new quotes reflect the rate, but existing confirmed invoice snapshot stays unchanged.
6. Admin updates availability -> next search returns the updated recorded value.
7. Simultaneous bookings compete for the last recorded space -> only permitted booking(s) succeed within the selected model.
8. Failed/cancelled payment -> booking is not marked paid and no paid invoice is issued.
9. Unauthorized user calls admin endpoint -> request rejected.
10. Gemini unavailable -> standard location search still works.
11. Maps key/quota issue -> list search/error fallback remains understandable.
12. Payment callback repeats -> no duplicate payment confirmation/invoice or double availability decrement.

---

## 19. Code Quality and Git Workflow

### 19.1 Git branches

- `main`: known working and reviewed code.
- Feature branches: `feature/location-search`, `feature/booking-flow`, `feature/admin-availability`, `feature/gemini-search`, etc.
- Fix branches: `fix/...`.

Use pull requests where practical. At least one other teammate should review high-impact booking, payment, authorization and database changes. Do not commit directly to `main` for routine feature work.

### 19.2 Commit conventions

Use concise descriptive commits, for example:

- `feat(search): add manual location query`
- `feat(booking): validate recorded availability`
- `fix(payment): make callback idempotent`
- `test(ai): cover invalid Gemini output`
- `docs(trd): document local database setup`

### 19.3 Definition of technically complete

A change is complete only when:

- It meets the matching PRD feature and this TRD.
- It includes required validation and authorization.
- Database changes are migration-based.
- It contains tests for important success and failure cases.
- It has loading, empty and error states on the UI when relevant.
- Frontend builds and backend tests pass for the changed area.
- No secret/credential or generated environment file is committed.
- Documentation is updated if routes, schemas, setup, or decisions change.

---

## 20. Build and Local Runtime

### 20.1 Local development topology

Recommended local processes:

- XAMPP database service running for MariaDB/MySQL-compatible persistence.
- Laravel backend served at `http://127.0.0.1:8000` during development.
- Vite frontend served at `http://localhost:5173` during development.

Apache from XAMPP is not necessarily required for `php artisan serve`, but the XAMPP database service must be running. The developer must verify which PHP binary the Laravel CLI uses. Do not assume XAMPP's Apache PHP and CLI PHP are identical.

### 20.2 First working vertical slice

Before full features, implement and verify this narrow vertical slice:

1. Create the Laravel backend and connect to the empty Parkly database.
2. Add migrations and seed a few clearly labelled demo facilities, vehicle types, prices and recorded availability.
3. Add `GET /api/v1/parking-facilities` with validation and a JSON response.
4. Create Vue frontend and configure Axios base URL.
5. Fetch and show facility list with name, price and recorded availability.
6. Add one backend test and one frontend smoke check.
7. Commit the working slice and document how to run it.

Only then expand into map display, admin CRUD, booking transactions, AI parsing and payment.

### 20.3 Build checks

Before merging, run the backend tests and the production frontend build. The exact CI workflow may be added later; a GitHub Actions workflow is recommended once the basic repository builds reliably on all three machines.

---

## 21. Recommended Implementation Sequence

1. Read and approve the PRD/TRD; resolve data and booking decisions needed for schema design.
2. Audit versions of XAMPP PHP/MariaDB, Composer, Node/npm and Git.
3. Create the repository structure, `.env.example`, lockfiles and README.
4. Scaffold Vue 3/Vite/Tailwind and Laravel, then establish the local frontend/backend API connection.
5. Create migrations, relationships, seed data and the first parking list endpoint.
6. Build manual location search and facility list; add GPS and map support.
7. Implement facility details, vehicle compatibility, pricing and recorded availability display.
8. Build protected admin sign-in and facility/rate/availability management.
9. Implement booking creation and safe availability validation with database transactions.
10. Implement confirmation/invoice flow.
11. Add Gemini structured extraction and deterministic database-grounded matching.
12. Integrate SSLCOMMERZ sandbox only after core booking is stable and credentials/access are confirmed.
13. Complete security, integration, responsive and end-to-end tests.
14. Update docs, seed a reproducible demo dataset, rehearse the demo and prepare submission material.

This order prioritizes a working core workflow and reduces risk from external services.

---

## 22. Decisions Required Before or During Implementation

The proposal/PRD do not settle the following technical/business details. The team should document decisions in `docs/decisions/` before relying on them:

1. **Runtime compatibility:** Which PHP version is actually installed through XAMPP, and which Laravel release can run safely?
2. **DB engine:** Will the final demo use XAMPP's MariaDB or a MySQL server? What version will the final deployment use?
3. **Availability model:** Count per facility and vehicle type, or individual bay records? Recommended MVP is count-based.
4. **Pricing:** Hourly or other unit; rounding; minimum duration; overtime; taxes/fees; and amount format.
5. **Booking start time:** Is the booking immediate-use only, or can the user select an arrival/start time?
6. **Guest details:** What information must be collected before booking, and how can the visitor retrieve a booking later without an account?
7. **Payment holding rule:** Is availability reserved during payment-pending state, and for how long?
8. **Cancellation/refund:** Are cancellations allowed? What happens to availability and any payment state?
9. **Availability freshness:** What age of a manual update should be labelled stale or not confirmed?
10. **Operator access:** Separate operator accounts with facility-level permissions, or administrator-only updates for the first version?
11. **Staff verification:** Restricted admin booking lookup or another approved process? QR scanning is not mandatory.
12. **Notification channel:** In-app only for the MVP, or email notification too?
13. **Location hierarchy source:** Which dataset provides Division/District/Thana/Area values?
14. **PDF invoice:** Is a downloadable PDF mandatory for final demonstration, or is an HTML invoice plus print option sufficient if time is limited?
15. **Payment fallback:** If SSLCOMMERZ sandbox access is unavailable, is a clearly labelled demo payment flow acceptable to the supervisor?
16. **AI model:** Which supported Gemini model is available for the team's API credentials/quota at implementation time?
17. **Team consistency:** The updated task distribution covers three developers, while older proposal statements mention a two-person team. Ensure the final proposal and other documents consistently describe three contributors.

---

## 23. Instructions for AI Coding Agents

Any AI agent (including VS Code Agent mode/Copilot) working on Parkly must follow these instructions:

1. Read `PRD.md` and `TRD.md` before implementing a feature.
2. Inspect the repository, `package.json`, `composer.json`, lockfiles, `.env.example`, migrations and current Git status before changing anything.
3. Reuse installed tools and current project conventions. Do not recreate the app or replace dependencies without a reason.
4. Do not delete, overwrite or reset the database, migrations, user files or existing implementation without explicit approval.
5. Do not install a conflicting Laravel/Tailwind version. Inspect PHP/runtime compatibility first and use official installation guidance for the selected versions.
6. Never commit real credentials. Do not expose Gemini or payment secrets in frontend code.
7. Do not invent facilities, pricing, available slots, payment results or external API responses. Use seed data clearly marked as demo data during development.
8. Keep AI as a parser/recommendation helper. All results must be matched to real database records; booking and payment authority remains in Laravel.
9. Implement backend validation and authorization even when the frontend validates the same input.
10. Use migrations for schema changes and tests for high-risk booking, availability, authorization and payment behavior.
11. Do not implement camera detection, CCTV/sensors, custom model training, automatic gates, reviews, QR scanning or extra features unless the team explicitly expands scope.
12. If a business rule is not defined, find the relevant open decision in this document and ask before making a behavior-changing choice.
13. Make small changes, run relevant tests/builds, summarize modified files, report commands that failed, and never claim verification that was not run.
14. Keep `TRD.md`, `PRD.md`, API examples and implementation in sync when an approved decision changes the blueprint.

---

## 24. Official Technical References

The links below provide technical guidance. Use the documentation matching the versions actually selected and locked by the project.

- **Laravel documentation:** <https://laravel.com/docs>
- **Laravel release notes / supported versions:** <https://laravel.com/docs/releases>
- **Laravel Sanctum (SPA authentication):** <https://laravel.com/docs/sanctum>
- **Laravel HTTP Client:** <https://laravel.com/docs/http-client>
- **Laravel validation:** <https://laravel.com/docs/validation>
- **Laravel database and migrations:** <https://laravel.com/docs/migrations>
- **Vue 3 guide:** <https://vuejs.org/guide/>
- **Vue Router:** <https://router.vuejs.org/>
- **Vite guide:** <https://vite.dev/guide/>
- **Tailwind CSS installation guides:** <https://tailwindcss.com/docs/installation>
- **Google Maps JavaScript API:** <https://developers.google.com/maps/documentation/javascript/overview>
- **Google Places API (New):** <https://developers.google.com/maps/documentation/places/web-service>
- **Google Gemini API:** <https://ai.google.dev/gemini-api/docs>
- **Gemini structured output:** <https://ai.google.dev/gemini-api/docs/structured-output>
- **SSLCOMMERZ sandbox documentation:** <https://sandbox-gw.sslcommerz.com/docs>
- **MySQL reference manual:** <https://dev.mysql.com/doc/refman/8.4/en/>
- **Composer:** <https://getcomposer.org/doc/>
- **Node.js downloads:** <https://nodejs.org/en/download>
- **Git documentation:** <https://git-scm.com/doc>

### Source and currency note

This TRD was prepared from the Parkly project proposal and PRD available at the time of writing, with current official technical documentation links provided for implementation checks. Version numbers, provider availability, API pricing/quotas, and exact authentication/payment integration details should be verified when the team starts the corresponding implementation. The team's checked-in dependency lockfiles and approved implementation decisions must define the exact versions used by the final prototype.
