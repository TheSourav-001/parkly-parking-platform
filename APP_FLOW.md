# Parkly --- Application Flow & Feature Interaction Specification

**Document:** `APP_FLOW.md`\
**Product:** Parkly: An AI-Powered Parking Discovery and Booking
Platform\
**Version:** 1.0 \| **Status:** Implementation Blueprint\
**Audience:** Product, UI/UX, frontend, backend, database, and QA teams\
**Stack:** Vue 3, JavaScript, Vite, Tailwind CSS, Laravel, PHP,
MySQL/MariaDB (XAMPP-compatible)\
**Integrations:** Google Maps Platform, Gemini API, SSLCOMMERZ sandbox
(subject to credentials), PDF invoice generation

------------------------------------------------------------------------

## 1. Purpose

This document defines how Parkly screens, features, roles, backend
services, database records, and external integrations connect. It is an
implementation and QA blueprint, not just a screen list.

**Source-of-truth rule:** Laravel and the database are authoritative for
prices, availability records, booking state, permissions, and payment
state. Google Maps provides mapping context; it is not Parkly's parking
inventory. Gemini can interpret requests and help rank real database
results; it must never invent facilities, prices, availability,
bookings, or payment outcomes.

## 2. Product Summary

Parkly helps drivers discover registered parking facilities, compare
recorded availability and prices, choose vehicle type and duration, book
a facility, complete the configured payment flow, and receive a digital
invoice. Administrators manage facilities, pricing, recorded
availability, and bookings.

## 3. Roles and Permissions

  -----------------------------------------------------------------------
  Role                    Capabilities            Restrictions
  ----------------------- ----------------------- -----------------------
  Guest                   View landing page,      Cannot access private
                          search registered       booking data
                          facilities, view public 
                          details                 

  Registered driver       Search, use AI-assisted Cannot edit facilities,
                          search, create          prices, availability,
                          bookings, pay, view own other users' bookings,
                          bookings/invoices,      or payment records
                          manage own profile      

  Administrator           Manage facilities,      Must pass server-side
                          vehicle compatibility,  authentication and
                          prices, recorded        authorization
                          availability, bookings, 
                          and notifications       

  Parking staff           Manually verify booking A separate staff portal
                          reference/invoice       is not assumed in the
                          before entry            current scope
  -----------------------------------------------------------------------

The current baseline has a driver-facing application and an admin
dashboard. Do not add a separate operator portal unless it is approved
as a scope change.

## 4. Application Areas and Navigation

### Public

-   Landing page
-   Parking search
-   Results (map + list)
-   Facility details
-   Login/registration when required for booking
-   About/how-it-works information

### Driver

-   Profile
-   Search and recommendation results
-   Booking checkout
-   Payment outcome
-   Booking confirmation
-   My Bookings
-   Booking details and invoice

### Admin

-   Admin login
-   Dashboard
-   Facility management
-   Pricing management
-   Manual availability management
-   Booking management
-   Notifications
-   Booking/payment detail review

Protect driver and admin routes, but remember that frontend route guards
are not security by themselves. Laravel must authorize every protected
request.

## 5. End-to-End Driver Journey

1.  Driver opens Parkly and starts a parking search.
2.  Driver chooses GPS location or manual location selection: **Division
    → District → Thana → Area**. GPS permission is optional.
3.  Driver enters filters or a natural-language request such as "Find
    affordable SUV parking in Mirpur for three hours."
4.  Standard search submits structured filters. AI search sends the
    request to Gemini for intent extraction, then validates the
    extracted criteria.
5.  Laravel queries registered Parkly facilities and matches location,
    supported vehicle type, price, and recorded availability.
6.  Results appear in a list and on Google Maps. Both views use the same
    returned Parkly records.
7.  Driver opens a facility, selects vehicle type and duration, and
    requests a quote.
8.  Laravel calculates the fee using stored pricing rules and returns
    the authoritative amount.
9.  Driver proceeds to booking. Laravel revalidates price, vehicle
    compatibility, and availability and creates a pending booking
    safely.
10. Backend initiates SSLCOMMERZ sandbox payment when configured.
11. Payment provider returns a result; Laravel verifies it server-side.
    A browser redirect alone is never proof of payment.
12. Verified success transitions the booking to confirmed and stores the
    payment reference.
13. Laravel generates a booking confirmation/invoice from saved booking
    and payment records.
14. Driver views/downloads the invoice and finds the booking under My
    Bookings.
15. Driver presents booking details/invoice to parking staff for manual
    entry verification.
16. Driver can review the booking status and history.
    Cancellation/refund behavior must follow an explicitly approved
    policy.

## 6. Feature Specifications

### 6.1 Landing Page

**Flow:** Load public shell → show Parkly value proposition and search
entry point → user selects GPS, manual, standard, or AI search. Public
discovery may be allowed; authentication is required at the point
defined by the booking policy.

**Connects to:** Search, location selection, authentication, results.

### 6.2 GPS Search

**Flow:** User clicks "Use my location" → browser requests permission →
frontend submits coordinates → Laravel finds registered facilities using
stored facility coordinates and distance/relevance ranking → results
appear in list and map.

**Rules:** Request location only after user action. If permission is
denied, unavailable, or times out, offer manual selection. Use
coordinates for the search purpose only. Do not treat Google Maps place
results as registered bookable Parkly facilities.

**Connects to:** Search API, facility records, distance ranking, map
display.

### 6.3 Manual Location Selection

**Flow:** Select Division → load Districts → select District → load
Thanas → select Thana → select Area when available → submit → Laravel
filters registered facilities.

Changing a parent selection clears dependent selections. Location
options must come from a maintained dataset or application tables; the
team must select and seed the dataset.

**Connects to:** Location data, facility search, results, AI criteria
normalization.

### 6.4 Standard Search and Filters

**Flow:** User submits location/vehicle/duration and supported filters →
Laravel validates inputs → database query filters active registered
facilities and supported vehicle types → ranking/sorting is applied
consistently → frontend displays results.

Standard search must keep working when Gemini is unavailable. Only
filter on information actually stored in the database.

### 6.5 AI Natural-Language Search

**Example:** "I need cheap parking for an SUV near Mirpur for three
hours."

**Flow:** User submits text → Laravel validates it → backend sends
minimum necessary request to Gemini → Gemini returns structured intent
(location, vehicle, duration, budget if stated) → Laravel
validates/normalizes it → Search Service queries Parkly database →
ranking uses only real returned records → same result list/map is shown.

**Boundaries:** - Gemini does not directly query or mutate the
database. - Gemini cannot create bookings, confirm payments, or invent
inventory/prices. - Unknown or ambiguous fields are not treated as
facts. - On Gemini failure or invalid output, fall back to standard
search or ask for clarification. - Recommendations must be grounded in
registered facilities and their recorded availability.

**Connects to:** Search, database, result ranking, map/list.

### 6.6 Results List and Google Maps

Laravel returns one normalized set of facility records. The UI renders
cards and markers from those same records. Selecting a card highlights
its marker; selecting a marker identifies the corresponding card;
selecting either opens facility details.

Facilities without valid coordinates may remain in the list but cannot
have a correctly placed marker. Empty results should explain that no
registered facility matched and offer a way to broaden criteria. Map
markers do not mean Google has supplied live parking inventory.

**Connects to:** Search Service, facility details, Maps integration.

### 6.7 Facility Details

Show facility name/address, location, supported vehicle types, stored
pricing rules, recorded availability, and last availability update where
available. Laravel supplies the data. Unknown or stale availability must
be labelled or blocked from booking according to the policy chosen by
the team; never present unknown data as confirmed.

**Connects to:** Facility records, pricing, availability,
vehicle/duration selection.

### 6.8 Vehicle, Duration, and Fee Quote

**Flow:** User selects supported vehicle type and duration/start time →
frontend submits selection → Laravel validates compatibility → Pricing
Service loads the applicable rule → backend calculates and returns
quote/breakdown → UI displays the server-calculated amount.

Price calculation must run on the backend. Never trust a
client-submitted total. Define partial-hour, minimum-charge, rounding,
and quote-expiry rules before production. Revalidate the quote during
booking.

**Connects to:** Facility details, pricing records, availability,
booking checkout.

### 6.9 Booking Creation and Availability Protection

**Flow:** User continues → Laravel authenticates user → validates
facility, vehicle, duration, price, and availability → safe database
transaction/locking operation → creates unique booking reference with
pending status → payment flow begins.

Recheck availability server-side. Protect against concurrent requests
that could overbook the final space. Use idempotency or equivalent
duplicate-submit protection. Define whether pending bookings hold
capacity and, if so, their expiry. Never confirm a booking from a
frontend-only success message.

**Recommended initial statuses:** `pending_payment`, `confirmed`,
`payment_failed`, `cancelled`, `completed`; add `expired` only if an
expiry policy is implemented. Enforce transitions centrally.

### 6.10 Payment

**Flow:** Backend creates payment attempt → initiates SSLCOMMERZ sandbox
session if credentials are configured → user completes provider checkout
→ provider redirects/calls back → Laravel verifies the transaction
server-side → stores verified result idempotently → updates
booking/payment state → UI fetches authoritative Parkly status.

Never expose secret credentials in Vue. Verify amount, currency,
transaction/reference, and expected booking. Handle duplicate callbacks
safely. Do not log secrets. If credentials are unavailable, use a
clearly labelled development-only simulation; never represent simulated
payment as real.

**Connects to:** Booking Service, payment records, confirmation,
invoice, admin booking view.

### 6.11 Confirmation and Invoice

Only after verified payment success should Laravel transition the
booking to confirmed and make a paid confirmation invoice available.
Generate the document from database records.

Invoice fields: unique booking reference, facility/address, vehicle
type, duration/timing, price breakdown/total, payment status, safe
transaction reference, confirmation timestamp, and entry instructions.
The invoice uses the booking's stored price snapshot, not a facility's
later edited price.

Invoice generation failure must be safely retryable and must not reverse
verified payment or create a duplicate booking.

**Connects to:** Payment verification, booking state, invoice/PDF
service, My Bookings, manual entry verification.

### 6.12 My Bookings

Laravel returns only bookings owned by the authenticated driver. UI
shows reference, facility, date/time, amount, and status. Opening a
booking reveals details and invoice. Cancellation appears only if
allowed by the implemented policy. Changing an ID in a URL must never
expose another user's booking.

**Connects to:** Authentication, Booking Service, Invoice Service.

### 6.13 Admin Dashboard

Admin signs in → Laravel authenticates and checks admin role → dashboard
loads actual database summaries → admin opens facility, price,
availability, or booking management. Metrics must not be hardcoded.
Administrative mutations should record actor and timestamp.

### 6.14 Facility Management

Admin creates/edits facility details, address, location hierarchy,
coordinates, supported vehicle types, and active status → Laravel
validates → database stores changes → search/details use updated active
records. Deactivated facilities should not appear as bookable results;
historical bookings should retain the relevant booking snapshot.

**Connects to:** Search, map/list, facility details, pricing,
availability, bookings.

### 6.15 Pricing Management

Admin edits a facility's pricing rule → Laravel validates and stores it
→ new facility views/quotes use the applicable rule → booking
revalidates price and saves a price snapshot → invoice uses that
snapshot. Editing a future price must not silently alter existing
confirmed totals.

### 6.16 Manual Availability Management

Authorized admin updates capacity/occupied/available data → Laravel
validates non-negative and consistent counts → database saves record,
updater, and timestamp → search/details/booking validation use the
latest valid record.

Availability is manually maintained in this project. Do not claim
sensor, CCTV, camera, or Google Maps inventory detection. Define exactly
when confirmed bookings reduce availability and how
cancellations/completions release capacity. Prevent admin edits from
silently conflicting with already committed bookings.

### 6.17 Admin Booking Management and Notifications

Booking creation or relevant status change → notification record is
created for admin → admin opens booking detail → reviews facility,
booking, and verified payment status → takes only allowed status
actions. Notification is not proof of payment and must not bypass
verification. In-app notifications are the baseline unless email/SMS is
explicitly approved.

## 7. Feature Connection Map

``` mermaid
flowchart TD
    A[Landing Page] --> B{Search Method}
    B --> C[GPS Location]
    B --> D[Manual Location Hierarchy]
    B --> E[Standard Filters]
    B --> F[AI Natural-Language Request]
    C --> G[Validated Search Criteria]
    D --> G
    E --> G
    F --> H[Gemini Intent Extraction]
    H --> I[Laravel Validates Intent]
    I --> G
    G --> J[Search Service]
    J --> K[(Parkly Facility / Price / Availability Data)]
    K --> L[Matching and Ranking]
    L --> M[Results List + Google Map]
    M --> N[Facility Details]
    N --> O[Vehicle + Duration]
    O --> P[Server-Side Quote]
    P --> Q[Availability and Price Revalidation]
    Q --> R[Pending Booking]
    R --> S[Payment Gateway]
    S --> T[Server-Side Payment Verification]
    T -->|Verified success| U[Confirmed Booking]
    T -->|Failure/cancel| V[Failure or Retry State]
    U --> W[Invoice / Confirmation]
    W --> X[My Bookings]
    W --> Y[Manual Entry Verification]
    R --> Z[Admin Notification]
    AA[Admin Facility Management] --> K
    AB[Admin Pricing Management] --> K
    AC[Admin Manual Availability Update] --> K
    AD[Admin Booking Management] --> R
```

### The central dependency chain

**Location → Search criteria → Database query → Ranking → Map/list →
Facility details → Vehicle/duration → Quote → Revalidation → Pending
booking → Payment → Server verification → Confirmation → Invoice → My
Bookings/manual entry verification.**

### Admin dependency chain

**Admin authentication → Facility management / pricing / availability →
Updated database records → Search, quote, and booking validation use
those records.**

### AI dependency chain

**Natural-language request → Gemini intent extraction → Laravel
validation → database-backed search → ranked real facilities → same
result UI as standard search.**

## 8. Data Source of Truth

  ------------------------------------------------------------------------------
  Data                           Authoritative source    Dependent features
  ------------------------------ ----------------------- -----------------------
  Registered facilities          Parkly database         Search, map, list,
                                                         details, admin

  Coordinates                    Facility record,        Distance ranking and
                                 verified during setup   map markers

  Division/District/Thana/Area   Maintained location     Manual selectors and
                                 dataset                 filtering

  Prices                         Pricing records         Details, quote,
                                                         booking, invoice

  Recorded availability          Database records        Search, details,
                                 updated by authorized   booking validation
                                 people                  

  User identity/role             Backend auth and user   Route/API authorization
                                 records                 

  Booking reference/status       Booking records         Driver, admin, invoice,
                                                         entry verification

  Payment outcome                Server-verified         Confirmation, invoice,
                                 provider result +       admin
                                 payment record          

  AI intent                      Validated Gemini output Search criteria only
  ------------------------------------------------------------------------------

## 9. Backend Service Boundaries

Keep business rules in reusable Laravel services rather than embedding
them all in controllers.

-   **Auth/Authorization:** identity, roles, ownership checks.
-   **Location Service:** location hierarchy and validation.
-   **Facility Service:** facility records, active status, coordinates,
    vehicle compatibility.
-   **Search Service:** filters, distance/relevance, ranking.
-   **AI Search Service:** Gemini request, structured-output validation,
    fallback.
-   **Pricing Service:** pricing rules, quote calculation, price
    snapshots.
-   **Availability Service:** manual records, consistency,
    concurrency-safe reservation checks.
-   **Booking Service:** creation, reference generation, ownership,
    status transitions.
-   **Payment Service:** session initiation, callback/return handling,
    server verification, idempotency.
-   **Invoice Service:** invoice data and PDF generation.
-   **Notification Service:** admin notification records and read state.
-   **Audit/Logging:** safe records of important admin, booking, and
    payment events.

## 10. Suggested Logical Data Model

Finalize schema, constraints, indexes, and field names during database
design.

-   `users` --- identity, credentials, role/status.
-   `locations` or normalized location tables --- Division, District,
    Thana, Area hierarchy.
-   `facilities` --- name, address, location reference, coordinates,
    active status.
-   `vehicle_types` --- supported vehicle categories if normalized.
-   `facility_vehicle_types` --- many-to-many facility/vehicle
    compatibility.
-   `pricing_rules` --- facility, vehicle category/rate structure,
    status/effective dates if needed.
-   `availability_records` --- facility capacity/occupied/available
    values, timestamp, updated-by user.
-   `bookings` --- unique reference, user, facility, vehicle,
    time/duration, status, price snapshot.
-   `payments` --- booking, provider, transaction reference,
    amount/currency, verification status, safe metadata.
-   `invoices` --- booking, invoice number, file/generation metadata.
-   `admin_notifications` --- recipient, type, related booking/facility,
    read state, timestamps.
-   `audit_logs` --- actor, action, affected record, safe metadata,
    timestamp.

**Relationships:** one user has many bookings; one facility has many
bookings, pricing rules, and availability records; facilities and
vehicle types may be many-to-many; bookings may have multiple payment
attempts, but only a correctly verified payment may confirm a booking;
invoices reference bookings; notifications can reference
bookings/facilities.

## 11. Booking and Payment State Rules

  -------------------------------------------------------------------------------
  Event             Booking state       Payment state       UI outcome
  ----------------- ------------------- ------------------- ---------------------
  Booking created   Pending payment     Pending/initiated   Continue to payment

  Verified payment  Failed or retryable Failed              Explain failure;
  failure           according to policy                     offer retry if
                                                            allowed

  User cancels      Policy-defined      Provider-verified   Booking not shown as
  provider checkout pending/cancelled   state               paid/confirmed
                    state                                   

  Server verifies   Confirmed           Successful          Confirmation and
  payment success                                           invoice available

  Duplicate         No duplicate        No duplicate        Same final state
  callback          transition          processing          

  Availability      No booking or       No payment          Ask user to select
  fails before      rejected attempt                        another facility
  booking                                                   

  Pending booking   Expired only if     Appropriate         Booking not confirmed
  expires           expiry policy       verified state      
                    exists                                  

  Eligible          Cancelled only      Refund state only   Show actual
  cancellation      under policy        if supported        cancellation/refund
  after payment                                             state
  -------------------------------------------------------------------------------

Do not silently invent refund, cancellation fee, no-show, or
automatic-expiry policies. Approve them before implementation.

## 12. UI States and Error Handling

Every important screen must support loading, success, empty, and error
states.

-   **Location:** permission denied, unavailable, timeout, manual
    fallback.
-   **Search:** no results, invalid filters, network error, Gemini
    unavailable.
-   **Facility:** inactive, missing coordinates, stale/unknown
    availability.
-   **Quote/booking:** price changed, availability changed, unsupported
    vehicle, duplicate submit.
-   **Payment:** pending, verified success, verified failure, cancelled
    checkout, delayed verification.
-   **Invoice:** generation failure with safe retry; never fabricate a
    paid invoice.
-   **Admin:** unauthorized access, validation failure, conflicting
    availability update.
-   **Network:** retry only safe operations; protect booking/payment
    mutations with idempotency.

Messages must be actionable and must not expose stack traces, SQL
details, API keys, secrets, or internal errors.

## 13. Security and Privacy

-   Validate all inputs in Laravel, even if Vue validates them too.
-   Protect admin endpoints and check ownership of every private
    booking/invoice request.
-   Keep Gemini and payment secrets on the server.
-   Apply suitable CSRF/session protections or the chosen secure
    authentication approach.
-   Rate-limit authentication, AI search, booking, and payment
    initiation.
-   Request GPS only after user action and avoid unnecessary location
    history.
-   Verify payment server-side; make callback handling idempotent.
-   Use transactions/locking for booking and availability changes.
-   Audit administrative facility, price, and availability changes.
-   Do not expose private user data in public search results.
-   Use sandbox credentials during development; production payments
    require valid merchant configuration and full testing.

## 14. External Integrations

### Google Maps Platform

Displays facility locations and map context. Parkly facility records
remain the source for registered facilities and bookable inventory.
Restrict API keys appropriately.

### Gemini API

Extracts intent from natural-language requests and may explain matches
based on real results. Backend validates output and performs database
queries. Standard search remains available as fallback.

### SSLCOMMERZ

Use sandbox mode when credentials are available. Backend initiates and
verifies transactions. Without credentials, mark integration as blocked
or use a clearly labelled development-only simulation.

### PDF invoice generation

Generate documents from confirmed booking/payment records. PDF failure
must not alter verified payment state and should be safely retryable.

## 15. Minimum QA Acceptance Scenarios

1.  GPS search returns registered facilities ranked consistently.
2.  Denied GPS permission falls back to manual selection.
3.  Changing a parent location resets dependent selectors.
4.  Standard search works while Gemini is unavailable.
5.  AI search uses only validated criteria and real database records.
6.  Invented AI facilities/prices are never displayed as verified
    inventory.
7.  Map markers and list items represent the same results.
8.  Inactive facilities cannot be booked.
9.  Unsupported vehicle types are rejected server-side.
10. Quotes match stored pricing rules and duration logic.
11. Price and availability changes are revalidated before booking.
12. Concurrent attempts cannot overbook the final space under the chosen
    capacity model.
13. Failed/cancelled payment never produces a confirmed paid booking.
14. A success redirect without server verification does not confirm
    payment.
15. Duplicate callbacks do not duplicate booking confirmation or
    capacity updates.
16. Invoice and booking show the same price snapshot.
17. A user cannot access another user's booking/invoice by changing an
    ID.
18. Non-admin users cannot access admin endpoints.
19. Manual availability changes are validated, timestamped, and
    reflected in future searches.
20. New bookings trigger the configured admin notification.
21. Invoice generation can be retried without duplicate booking/payment.
22. Any payment simulation is visibly marked development-only.

## 16. Recommended Implementation Sequence

Build connected vertical slices, not isolated screens.

1.  **Foundation:** repository, Vue/Laravel setup, environment
    variables, database connection, common error/response patterns.
2.  **Authentication and roles:** driver/admin permissions and route
    guards.
3.  **Location and facility records:** migrations, seed data, facility
    CRUD, coordinates, location hierarchy.
4.  **Standard search:** manual location, filters, backend query, result
    list.
5.  **Map and details:** map markers, synchronized list/map, facility
    details.
6.  **Pricing and availability:** admin controls, manual updates,
    server-side quote.
7.  **Booking core:** unique reference, status transitions,
    transactions, concurrency/idempotency.
8.  **Payment:** sandbox configuration, verification, failure and
    duplicate-callback handling.
9.  **Confirmation/invoice:** My Bookings, confirmed details, PDF
    generation.
10. **AI search:** Gemini intent extraction, validation, database-backed
    recommendations, fallback.
11. **Admin operations:** dashboard, booking review, notifications,
    audit trail.
12. **Hardening:** responsive UI, security, edge cases, regression
    tests, deployment.

Align this sequence with the approved 12-week team plan. Booking and
payment integrity must not be postponed to the final week.

## 17. Explicit Scope Boundaries

Not in the current baseline unless formally approved: -
CCTV/camera-based slot detection - YOLO or computer-vision parking
detection - Hardware sensors or IoT - Custom ML model training -
Automatic gate control - Mandatory QR scanner - Native Android/iOS
application - Separate operator portal - Ratings/reviews - Real
production payments without valid merchant configuration

Current entry verification is manual using booking details/invoice.
Availability updates are manual. Google Maps is a mapping integration,
not Parkly's parking inventory.

## 18. Decisions Required Before Production

Document decisions on: 1. Whether login is required before booking and
the authentication method. 2. Pricing model (hourly, fixed block,
vehicle-based, etc.). 3. Immediate-only or future-start bookings. 4.
Whether pending bookings hold capacity and for how long. 5. When
confirmed bookings reduce availability and how cancellations/completions
restore it. 6. Cancellation, refund, and no-show policies. 7.
Availability freshness threshold. 8. Supported SSLCOMMERZ
verification/callback method. 9. Source dataset for
Division/District/Thana/Area. 10. Notification channels (in-app
baseline; email/SMS only if approved). 11. Final booking statuses and
who may change them.

Until these are decided, implement conservative explicit behavior and do
not invent business policies.

## Definition of Done

A feature is complete when its UI states and workflow, Laravel
validation/authorization, database changes, integrations,
dependent-feature contracts, error handling, security protections, and
acceptance tests are implemented. Update documentation and environment
variables. No feature may violate the source-of-truth rules for
availability, pricing, booking, or payment.
