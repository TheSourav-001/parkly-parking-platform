# Parkly - Product Requirements Document (PRD)

**Product:** Parkly: An AI-Powered Parking Discovery and Booking Platform  
**Document type:** Product Requirements Document  
**Version:** 1.0  
**Status:** Draft for team review  
**Project course:** SE 331 - Software Engineering Design Capstone Project  
**Institution:** Department of Software Engineering, Daffodil International University  
**Planned delivery:** 12 weeks (3 months)  
**Primary platform:** Responsive web application  

---

## 1. Product Overview

Parkly is a web-based platform that helps drivers discover nearby registered parking facilities, review their recorded availability and prices, choose a vehicle type and parking duration, book a suitable space, make an online payment in the prototype flow, and receive a digital booking confirmation or invoice. The invoice and booking details can be presented to parking staff for verification before entry.

Parkly will provide two ways to specify a search location: current GPS location, with the user's permission, or manual location selection. Manual selection should support Division, District, Thana, and Area where applicable. Parking facilities will be shown in a map and result list, with nearby suitable options prioritized and up to five results targeted for the initial display.

The AI component will use the Google Gemini API to interpret parking requests written in natural language. It will extract requirements such as location, vehicle type, budget, and duration. The application will then match those requirements against actual parking records stored in Parkly's database. AI is not the source of parking availability or price information.

Authorized administrators or participating parking operators maintain facility details, prices, and reported availability. The prototype will not use cameras, CCTV analytics, sensors, automatic gate hardware, or a custom-trained machine-learning model.

## 2. Product Goals

1. Help drivers discover registered parking facilities near a chosen location.
2. Make facility details, supported vehicle types, recorded availability, and prices easy to compare.
3. Support an end-to-end prototype booking process from facility selection through payment confirmation and digital invoice generation.
4. Give administrators or participating operators a way to maintain parking records and review bookings.
5. Use AI to make search more flexible while grounding recommendations in actual database records.
6. Deliver a demonstrable, maintainable prototype within the team's 12-week project timeline.

## 3. Success Criteria

The prototype will be considered successful when:

- A visitor can search for parking by GPS or manual location without being required to create an account just to search.
- The application displays registered parking facilities on a map and in a list.
- A visitor can review facility information, select a compatible vehicle type and duration, and see the calculated fee before confirming.
- Booking validation checks the latest recorded availability before confirmation.
- The prototype supports a payment workflow, preferably through an SSLCOMMERZ sandbox if access and time allow.
- After payment confirmation, the system records the booking and generates a digital receipt or invoice.
- An administrator can sign in and manage registered facilities, prices, reported availability, and booking records.
- A new booking generates an administrator notification.
- Gemini can interpret a natural-language parking request, and the application returns recommendations based on real records rather than invented prices or availability.
- The application communicates when availability is missing or possibly outdated rather than promising guaranteed real-time data.
- The main user and administrator workflows work on common desktop and mobile browsers.

## 4. Target Users and Stakeholders

### 4.1 Visitor / Driver

A person looking for a nearby parking facility. They may use GPS or manually choose a location, compare results, specify a vehicle and duration, book a space, pay through the prototype flow, and show the invoice and booking details to parking staff.

### 4.2 System Administrator

An authorized user who manages registered parking facility details, pricing, reported availability, and booking records; receives new-booking notifications; and reviews or updates relevant booking status.

### 4.3 Participating Parking Operator

A participating facility representative who provides or updates facility details, prices, reported available-space information, and booking status. The proposal does not specify whether operators will have a separate login and dashboard; that decision is listed under Open Questions.

### 4.4 Parking Staff / Guard

A person at the parking facility who checks the digital invoice and booking details against the booking record before allowing entry. A separate staff login or QR-scanning workflow is not required by the current proposal.

### 4.5 External Service Providers

- Google Maps Platform / Places API: map display, place lookup, and location-related functionality.
- Google Gemini API: natural-language request understanding and requirement extraction.
- SSLCOMMERZ sandbox: potential payment workflow for the prototype, subject to account and sandbox access.

## 5. Scope

### 5.1 In Scope

- Responsive landing page and user-facing parking search.
- GPS-based location search, with consent.
- Manual location selection including Division, District, Thana, and Area where applicable.
- Map and result-list display for registered parking facilities.
- Facility details, supported vehicle types, recorded availability, and prices.
- Vehicle type and duration selection.
- Fee calculation using configured price rules.
- Availability validation and booking creation.
- Online payment integration for the prototype, using a sandbox where required and feasible.
- Digital booking confirmation and invoice.
- Manual invoice and booking-detail verification by parking staff.
- Administrator panel for facility, price, availability, and booking management.
- Booking notifications for administrators.
- Gemini-based natural-language requirement extraction and database-grounded recommendations.
- Testing, documentation, Git/GitHub collaboration, and final demonstration.

### 5.2 Out of Scope

- Camera-based vehicle detection, CCTV analytics, or sensor-based occupancy detection.
- Automated parking gates or entry-barrier control.
- A custom machine-learning model trained from scratch.
- AI-based prediction of occupancy or automatic creation of availability data.
- Nationwide registration or integration with every parking facility in Bangladesh.
- A guarantee of live availability when facility records have not been updated.
- A dedicated native mobile application in the initial release.
- Automatic payment settlement, commission distribution, or owner payouts unless later approved as a separate requirement; these are not described in the current proposal.
- A mandatory separate parking-staff account, QR scanner, ratings, reviews, or unrelated marketplace features.

## 6. Assumptions and Constraints

1. The first release will use a limited set of registered or demo parking facilities.
2. Parking availability comes from records entered by an authorized administrator or participating operator; it does not come automatically from Google Maps or Gemini.
3. Google Maps and Gemini usage depends on valid API credentials, account setup, quotas, and service terms.
4. Payment integration is planned for a prototype. Sandbox availability may affect implementation; live payment processing is not assumed.
5. Searches can be performed without creating an account. Required contact or booking details will be collected when the user books. The exact fields are not specified in the proposal and must be finalized before implementation.
6. The invoice is shown to parking staff, who verify the booking against the system's record. The exact staff-facing verification interface is not defined yet.
7. The proposal refers to available spaces / slots but does not define whether individual physical spaces will be tracked by unique slot IDs or only as counts. The first implementation should use a clear, simple recorded-availability model and finalize this decision before schema implementation.
8. The team has 12 weeks to build the prototype, so the first priority is a reliable core discovery and booking workflow.

## 7. Main User Journeys

### 7.1 Driver Search and Booking Journey

1. The visitor opens Parkly.
2. The visitor allows GPS access or manually selects a location.
3. The system searches registered parking records and shows nearby facilities on a map and in a result list.
4. The visitor may use ordinary location input or describe the need in natural language.
5. For natural-language requests, Gemini extracts the search requirements; the application matches them against database records.
6. The visitor compares relevant facility details, including supported vehicle type, recorded availability, and price.
7. The visitor opens a facility, selects a vehicle type and parking duration, and reviews the calculated fee.
8. The application validates recorded availability before proceeding.
9. The visitor supplies the required booking details and confirms the booking/payment flow.
10. The payment provider or sandbox returns a payment result.
11. After payment is confirmed, Parkly saves/updates the booking state and creates a digital confirmation/invoice.
12. The visitor presents the invoice and booking details to parking staff for verification before entry.

### 7.2 Administrator / Operator Journey

1. The administrator signs in to the protected panel.
2. The administrator creates or updates a registered parking facility.
3. The administrator sets facility location, supported vehicle types, and applicable prices.
4. The administrator or participating operator updates reported availability when spaces become occupied or free.
5. When a new booking arrives, the system records it and notifies the administrator.
6. The administrator reviews booking details and updates relevant booking or parking status.
7. Updated records become the source for subsequent search results and AI recommendations.

## 8. Detailed Feature Requirements

Priority definitions: **P0** = core requirement for a working prototype; **P1** = important supporting requirement; **P2** = implement if time and access allow.

### F-01: Landing Page and Public Entry

**Priority:** P0  
**Purpose:** Introduce Parkly and provide a clear entry point to parking search.

**Requirements:**
- Present the Parkly identity and explain that the platform supports parking discovery and booking.
- Provide a clear way to start a parking search.
- Provide access to the location search and map/list results.
- Use a responsive layout on desktop and mobile browsers.
- Public parking search must not require account creation solely to browse.

**Acceptance criteria:**
- A first-time visitor can identify how to start a search without administrator assistance.
- The main content and search entry point remain usable on a mobile-sized screen.
- A visitor can enter the search flow without being forced to register first.

**Error / empty states:** If parking records cannot be loaded, show a clear message and a retry action instead of a blank page.

### F-02: GPS Location Search

**Priority:** P0  
**Purpose:** Let the visitor discover facilities near the device's current location.

**Requirements:**
- Request browser/device location permission only when the visitor chooses the GPS option.
- Use returned coordinates to search registered parking records with location data.
- Use map/location services for map functionality and place-related information where appropriate.
- Do not treat a Google Maps result as proof that slots are available.
- Provide manual location selection as an alternative if permission is denied or location cannot be retrieved.

**Acceptance criteria:**
- When permission is granted and location is available, the system can use it as the search center.
- When permission is denied, unavailable, or times out, the user can continue with manual location selection.
- Results only claim recorded availability when the relevant Parkly record contains it.

**Error / empty states:** Explain permission denial or unavailable location in plain language. Do not keep prompting repeatedly after the user declines.

### F-03: Manual Location Selection

**Priority:** P0  
**Purpose:** Support users who do not want to share GPS or need to search another area.

**Requirements:**
- Provide manual selection for Division, District, Thana, and Area where applicable.
- Permit the user to search for a location using ordinary location input as supported by the selected map/place service.
- Use the selected location as the search center or geographic constraint.
- Keep the selected location visible so the user understands which area is being searched.

**Acceptance criteria:**
- A user can complete a search without granting GPS permission.
- The selected location is passed to the backend search logic.
- If no registered facilities match the selected area, show an explicit no-results state.

**Open implementation detail:** The exact administrative-area dataset and dependent dropdown behavior must be selected before implementation.

### F-04: Parking Search Results, Map, and List

**Priority:** P0  
**Purpose:** Present nearby registered facilities in a way that supports comparison.

**Requirements:**
- Retrieve registered parking facilities from Parkly's database.
- Display matching facilities in a list and display their locations on a map when coordinates are available.
- Prioritize nearby suitable options.
- Aim to show up to five suitable results initially, as stated in the proposal.
- Show distance where it can be calculated and supported by available coordinates.
- Result information should include facility name, location, supported vehicle types, configured fee information, and recorded availability where available.
- Keep map display separate from availability truth: map/place data does not create availability records.

**Acceptance criteria:**
- Search results correspond to records returned by the backend.
- Selecting a list item allows the user to identify the same facility on the map when map markers are available.
- Results with missing availability do not display a fabricated count or falsely claim an open slot.
- If no records match, show an explicit empty state.

**Possible result ordering:** Nearby suitable facilities first. Additional sorting/filter behavior should not be treated as mandatory unless confirmed by the team.

### F-05: Parking Facility Details

**Priority:** P0  
**Purpose:** Help the visitor understand whether a facility is suitable before booking.

**Requirements:**
- Show registered facility name and location.
- Show supported vehicle types.
- Show the price rules or rates configured for the facility.
- Show reported available-space information and, where practical, when it was last updated.
- Show map/location information where available.
- Make missing or potentially outdated availability clear.

**Acceptance criteria:**
- The details displayed match the facility record and current configured price rules.
- The interface does not present stale/missing availability as guaranteed live data.
- The user can proceed to vehicle and duration selection only for a facility with the information required to create a valid booking.

### F-06: Vehicle Type and Parking Duration Selection

**Priority:** P0  
**Purpose:** Collect the requirements needed to calculate the booking fee.

**Requirements:**
- Allow the visitor to select a vehicle type supported by the selected facility.
- Allow selection or entry of a parking duration using the duration options decided by the project team.
- Prevent selection of a vehicle type not supported by that facility.
- Pass facility, vehicle type, duration, and applicable pricing reference to the backend for calculation and validation.

**Acceptance criteria:**
- Unsupported vehicle types cannot continue to booking.
- Changing the vehicle type or duration recalculates the displayed estimate.
- The fee shown before confirmation is returned or verified by backend pricing logic, not trusted solely from frontend calculations.

**Open implementation detail:** The exact vehicle categories and allowed duration increments must be confirmed and documented.

### F-07: Price Calculation

**Priority:** P0  
**Purpose:** Calculate the fee using prices configured for the selected facility and vehicle type.

**Requirements:**
- Use administrator-maintained prices and configured pricing rules.
- Calculate the amount using the selected duration and applicable vehicle rate.
- Show the amount to the visitor before booking confirmation.
- Revalidate the applicable price at booking time so that the booking is not based solely on a stale frontend value.
- Store the agreed amount with the booking/payment reference for later verification and invoice generation.

**Acceptance criteria:**
- The same facility, vehicle type, duration, and price rule produce a consistent amount.
- Invalid or missing price configuration prevents a valid booking and produces an understandable message.
- The invoice amount matches the amount recorded for the confirmed booking/payment.

**Open implementation detail:** The proposal mentions prices but does not define rounding rules, minimum charges, grace periods, overtime fees, or tax/fee treatment. These must not be invented silently.

### F-08: Reported Availability Management

**Priority:** P0  
**Purpose:** Keep a record of parking-space availability maintained by authorized people.

**Requirements:**
- Allow an authorized administrator or participating operator to update reported available spaces when spaces become occupied or free.
- Store availability in Parkly's database and use the most recent saved record for search and booking validation.
- Validate availability before confirming a booking.
- Update the recorded availability/booking state after a confirmed booking according to the chosen data model.
- Identify missing or outdated availability rather than implying real-time detection.
- Do not use cameras, sensors, CCTV, or automated detection in the current scope.

**Acceptance criteria:**
- An authorized user can update reported availability and see the saved value after refresh.
- User-facing results reflect the most recent saved availability record.
- A booking cannot exceed the recorded capacity according to the selected availability model.
- Unavailable or stale records are communicated transparently.

**Important design decision:** Decide whether Parkly tracks a count of available spaces per facility/vehicle category or individually identified physical bays. The proposal does not settle this. For a three-month prototype, a count-based model may be simpler, but the team must choose and document it before database implementation.

### F-09: Booking Creation and Validation

**Priority:** P0  
**Purpose:** Create a booking for a selected facility, vehicle type, duration, and calculated fee.

**Requirements:**
- Collect necessary user/booking information at booking time; the required fields must be agreed by the team.
- Validate facility existence, vehicle compatibility, selected duration, price configuration, and recorded availability on the backend.
- Prevent confirmation if required information is missing or if recorded capacity is insufficient.
- Assign a unique booking reference/ID.
- Store the booking details, recorded amount, and status in the database.
- Ensure the booking flow cannot be confirmed merely by manipulating frontend values.

**Acceptance criteria:**
- A valid booking request creates one identifiable booking record.
- An invalid request returns a useful validation message and does not create a confirmed booking.
- Repeated browser submissions or retries do not accidentally create duplicate confirmed bookings for the same single user action; suitable request/state validation is applied.
- Booking information can be retrieved for invoice generation and later verification.

**Suggested initial status model:** Pending payment, Confirmed, Cancelled, Completed. This is an implementation proposal; finalize statuses and transitions before building the status management screens.

### F-10: Online Payment Workflow

**Priority:** P1, subject to access and time  
**Purpose:** Demonstrate payment-related booking completion in the prototype.

**Requirements:**
- Integrate SSLCOMMERZ sandbox if available and feasible within the project timeline.
- Create a payment reference associated with the booking.
- Treat server-verified provider/sandbox confirmation as the payment result; do not mark payment as successful based solely on a frontend redirect.
- Store the payment status/reference needed for booking history and invoice generation.
- Do not store raw payment credentials or sensitive card information in Parkly.
- If sandbox integration is blocked, document the limitation and implement a clearly labelled mock/demo payment result only if the supervisor approves it.

**Acceptance criteria:**
- A successful sandbox response updates the associated booking/payment record and enables invoice generation.
- A failed or cancelled payment does not appear as a successful payment.
- The system handles retries or repeated callbacks without duplicating payment records or invoices.
- No real customer payment is collected during a sandbox-only demonstration.

### F-11: Digital Booking Confirmation and Invoice

**Priority:** P0 for confirmation/receipt; PDF export depends on time  
**Purpose:** Give the visitor evidence of the confirmed booking and payment result.

**Requirements:**
- Generate the digital confirmation/invoice only after the required payment confirmation for the selected flow.
- Include, at minimum, the booking ID, facility details, vehicle type, duration, payment amount, and booking date/time where available.
- Make the confirmation visible to the user and suitable for presentation to parking staff.
- A downloadable PDF may be generated using a Laravel PDF package as listed in the proposal.
- Ensure invoice details match the stored booking and payment records.

**Acceptance criteria:**
- Confirmed payment leads to a digital confirmation/invoice.
- An unsuccessful payment does not show a paid invoice.
- The booking ID and amount shown on the invoice match the database records.
- The user can return to the confirmation after the payment result if the application flow supports this.

### F-12: Invoice Verification at the Parking Facility

**Priority:** P0 as a business process; staff interface details to be confirmed  
**Purpose:** Let parking staff check the visitor's invoice and booking details before entry.

**Requirements:**
- The visitor presents the invoice and booking details to parking staff.
- Staff check the presented booking reference/details against Parkly's booking record before allowing entry.
- The project must provide a practical way to perform this check during the demo, such as an authorized booking lookup view. The exact screen/role must be confirmed by the team.
- QR scanning and a separate parking-staff login are not mandatory in the current proposal.
- The current project does not automate entry gates.

**Acceptance criteria:**
- A valid confirmed booking can be found and checked by its booking reference or agreed details.
- A booking that cannot be found or has an invalid status is not represented as verified.
- The workflow records or communicates the verification outcome where the implemented design supports it.

### F-13: Administrator Authentication and Access Control

**Priority:** P0  
**Purpose:** Protect administrative changes to parking information and bookings.

**Requirements:**
- Require sign-in before access to administrative functions.
- Restrict protected routes and backend endpoints to authorized administrative users.
- Validate permissions on the backend, not only by hiding frontend menu items.
- Do not expose passwords, secret values, or payment credentials in responses or logs.

**Acceptance criteria:**
- An unauthenticated user cannot perform protected administrative actions.
- Unauthorized requests to protected endpoints are rejected.
- A valid administrator can access the dashboard and permitted management features.

### F-14: Parking Facility Administration

**Priority:** P0  
**Purpose:** Maintain the registered facilities used by search and recommendations.

**Requirements:**
- Allow an authorized administrator to create and update registered parking facility records.
- Maintain facility name, location/address/coordinates where available, supported vehicle types, pricing information, and relevant availability fields.
- Validate required fields and prevent incomplete facility records from appearing as fully bookable.
- Use the database as the source for Parkly's registered facility list.

**Acceptance criteria:**
- A valid new facility can be saved and retrieved by search.
- An update is reflected in subsequent search/details results.
- Invalid or missing required data is rejected with a useful error.

### F-15: Pricing Administration

**Priority:** P0  
**Purpose:** Let authorized administrators maintain rates used for fee calculations.

**Requirements:**
- Create or update configured prices for supported vehicle types and applicable duration/rate rules.
- Validate that values are valid monetary amounts and that the required configuration exists before the facility can be booked under that rule.
- Ensure updated prices are reflected in future fee calculations.
- For already confirmed bookings, keep the agreed amount recorded with the booking rather than silently recalculating historical invoices from new prices.

**Acceptance criteria:**
- Valid price updates are saved and used by new quote/booking calculations.
- Invalid prices cannot be saved.
- An existing confirmed booking's recorded payment amount remains consistent with its invoice.

### F-16: Administrator Availability Updates

**Priority:** P0  
**Purpose:** Allow availability data to be maintained manually.

**Requirements:**
- Let an authorized administrator or participating operator enter updated reported availability.
- Save the update and make it available to search and booking validation.
- Provide enough information to distinguish a missing value from a known available-space count.
- Where practical, record the last update timestamp and the user who made the update.

**Acceptance criteria:**
- A saved update is reflected in subsequent search queries.
- Invalid negative counts or values beyond the chosen model's capacity are rejected.
- When no reliable update exists, the user-facing interface does not claim a definite availability value.

### F-17: Booking Administration and Notifications

**Priority:** P0  
**Purpose:** Help administrators respond to new bookings and review their status.

**Requirements:**
- Record every booking request that passes the applicable validation stage.
- Notify the administrator when a new booking is received. The initial implementation may use an in-app notification or another simple agreed method; the exact channel is not specified in the proposal.
- Provide a list/detail view to review booking ID, facility, vehicle type, duration, amount, payment state, and booking status where applicable.
- Allow authorized users to update the relevant booking or parking status according to approved transitions.
- Avoid changing the payment status manually in a way that falsely represents provider confirmation.

**Acceptance criteria:**
- A new booking creates a record and triggers the configured notification mechanism.
- The administrator can locate and review the record.
- Invalid status transitions are rejected according to the finalized status rules.

### F-18: Natural-Language Request Understanding with Gemini

**Priority:** P0 for the proposed AI requirement  
**Purpose:** Let a user describe parking needs in ordinary language instead of filling every search field manually.

**Example input:** "I need affordable parking near Mirpur for an SUV for three hours."

**Requirements:**
- Send the user's natural-language request to the Gemini API through a protected backend integration.
- Extract structured requirements, including location, vehicle type, budget/price preference, and duration when present.
- Validate and normalize extracted values before they are used by the application.
- Allow missing or ambiguous fields to remain unknown; do not invent requirements.
- Keep API keys on the backend and out of frontend code/repository commits.
- If the AI service is unavailable, explain that the AI-assisted search is temporarily unavailable and keep ordinary search usable.

**Acceptance criteria:**
- A supported request is converted into structured requirements suitable for database matching.
- Ambiguous or incomplete requests do not cause the system to fabricate a location, vehicle, budget, or duration.
- A failed API request produces a controlled error and does not break ordinary location search.
- AI output is treated as untrusted data and validated before being used.

### F-19: Database-Grounded AI Recommendations

**Priority:** P0 for the proposed AI requirement  
**Purpose:** Recommend suitable registered facilities based on the request and actual Parkly data.

**Requirements:**
- Retrieve relevant registered parking records and match them against the requirements extracted by Gemini.
- Consider location, supported vehicle type, reported availability, and price when these values exist in the records.
- Use application matching logic to compare records; the language model must not create the candidate facility list or invent availability/prices.
- Return relevant options with enough information for the user to compare them.
- If no records meet all requirements, clearly say no suitable recorded match was found and optionally explain which criteria could not be satisfied.
- If availability is missing or old, communicate that limitation rather than labeling the facility definitely available.
- AI does not calculate payment, confirm bookings, or alter database availability by itself.

**Acceptance criteria:**
- Every recommendation corresponds to an actual facility returned from Parkly's database.
- Displayed price and availability trace back to stored records.
- Unsupported or absent records are not invented in the AI answer.
- A no-match case is handled transparently.

### F-20: Responsive Interface and Basic Accessibility

**Priority:** P1  
**Purpose:** Keep core flows usable on common screens and by a range of users.

**Requirements:**
- Support common desktop and mobile browser sizes.
- Use readable text, clear labels, visible focus states, and usable form validation messages.
- Ensure core search, result, booking, admin, and invoice screens are not dependent on hover-only interactions.
- Use consistent loading, error, success, and empty states.

**Acceptance criteria:**
- Core flows can be completed at desktop and mobile widths without essential controls being hidden or unusable.
- Inputs have understandable labels and validation feedback.
- Network and API failures are communicated clearly.

## 9. Business Rules

1. Only registered parking facilities in Parkly's database can be returned as bookable Parkly facilities.
2. Map/place information helps locate facilities but does not establish slot availability.
3. Reported availability is maintained by authorized administrators/operators.
4. The backend is authoritative for price calculation and booking validation.
5. The system must validate availability at booking time; a user cannot confirm a booking that exceeds recorded capacity.
6. Booking confirmation and paid invoice generation depend on the payment flow's confirmed result.
7. The agreed amount must be stored with the booking/payment record so later price changes do not silently alter an already confirmed invoice.
8. Gemini extracts requirements and assists matching; it does not create availability, price, payment status, or booking confirmation.
9. Recommendations must be based on actual database records and must communicate missing or potentially outdated availability.
10. GPS is optional. Manual location selection is always available as an alternative.
11. Administrative functions require authorized access.
12. Camera detection, sensors, automatic gates, custom model training, and nationwide integration are excluded from the first version.

## 10. Functional Requirements Summary

| ID | Requirement | Priority | Main verification |
|---|---|---:|---|
| FR-01 | Public landing page and search entry | P0 | Visitor can begin a search without account creation |
| FR-02 | GPS-based location search | P0 | Granted GPS coordinates drive the search; denial falls back to manual location |
| FR-03 | Manual location selection | P0 | Search works using selected location without GPS |
| FR-04 | Map and result-list display | P0 | Results match registered records and show on map/list where data allows |
| FR-05 | Facility details | P0 | Facility, vehicle, price, and availability data match stored records |
| FR-06 | Vehicle and duration selection | P0 | Unsupported vehicle types are blocked and fee is updated |
| FR-07 | Price calculation | P0 | Backend returns/validates the applicable amount |
| FR-08 | Availability updates and validation | P0 | Admin/operator updates are stored and used for booking validation |
| FR-09 | Booking creation and validation | P0 | Valid booking creates a unique record; invalid booking is rejected |
| FR-10 | Online payment workflow | P1 | Sandbox result updates payment/booking state correctly |
| FR-11 | Digital confirmation and invoice | P0/P1 | Confirmed payment produces matching invoice details |
| FR-12 | Invoice verification at facility | P0 | Staff can check booking details against the record |
| FR-13 | Administrator authentication | P0 | Protected actions reject unauthorized access |
| FR-14 | Facility administration | P0 | Admin can create/update facilities and values flow to search |
| FR-15 | Pricing administration | P0 | Valid price changes affect new calculations |
| FR-16 | Manual availability administration | P0 | Valid updates persist and are reflected in searches |
| FR-17 | Booking management and notifications | P0 | New bookings are recorded, notified, and reviewable |
| FR-18 | Gemini request understanding | P0 | Natural language becomes validated structured requirements |
| FR-19 | Database-grounded recommendations | P0 | Every recommendation traces to real facility records |
| FR-20 | Responsive interface and basic accessibility | P1 | Core flows remain usable on desktop and mobile |

## 11. Non-Functional Requirements

### NFR-01: Usability

The user and administrator interfaces should be understandable, consistent, and clear about the current step and expected action. Error messages should explain what the user can do next.

### NFR-02: Performance

Under normal network conditions and with the project's initial limited dataset, search results and booking details should appear within a reasonable time. The team should measure actual response times during testing rather than claim an untested performance guarantee.

### NFR-03: Security

- Restrict administrative functions to authorized users.
- Validate inputs on the backend.
- Keep database passwords and API keys in environment configuration, not source control.
- Protect payment-related data and do not store unnecessary payment credentials.
- Use established framework security mechanisms for authentication, authorization, validation, and database queries.

### NFR-04: Data Accuracy

Fees must use configured pricing rules. Availability must reflect the latest recorded update, with missing or potentially outdated data communicated to users.

### NFR-05: Reliability

Validate availability before booking confirmation and update the booking record appropriately after confirmation. Handle API/payment errors without falsely marking a transaction successful.

### NFR-06: Compatibility

The web application should work on common, current desktop and mobile browsers.

### NFR-07: AI Reliability

Recommendations must use records retrieved from the database. Missing matches, missing availability, stale records, or Gemini service errors must be handled transparently.

### NFR-08: Maintainability

Use a structured codebase with Vue 3, Laravel, MySQL-compatible storage, Laravel Eloquent ORM, documented API contracts, environment-based configuration, and Git-based collaboration.

## 12. Data Requirements (Initial Model)

The following entities are proposed based on the features in the project proposal. Exact field names and relationships should be finalized in the ER diagram.

### 12.1 User / Administrator

Stores identity and authentication information needed for protected admin access and booking contact details where applicable. Public search must not require account creation. Decide whether customer accounts are excluded from the first version or introduced later.

### 12.2 Parking Facility

Suggested information: facility ID, name, address/location hierarchy, latitude/longitude when available, registration/active status, supported vehicle types, and timestamps.

### 12.3 Vehicle Type

Defines the vehicle categories supported by the prototype. A facility should only offer the categories it supports.

### 12.4 Pricing Rule

Connects a facility and vehicle type to the applicable fee/rate and any agreed duration rule. The proposal does not define minimum fees, overtime rules, taxes, or price rounding; decide these before implementation.

### 12.5 Availability Record

Stores the reported available-space value and related facility reference, with update time and updater where practical. Decide whether this is a count-based model or individual physical slots before implementation.

### 12.6 Booking

Suggested information: unique booking ID, facility, vehicle type, duration, recorded price/total amount, booking contact details, booking status, payment state/reference, and timestamps.

### 12.7 Payment Reference

Stores provider/sandbox transaction reference, related booking, amount, and payment status. Do not store raw card credentials.

### 12.8 Invoice / Confirmation

Stores or derives invoice details from the confirmed booking and payment records. Ensure invoice values match the stored booking snapshot.

### 12.9 Notification

Stores or represents new-booking alerts for administrators. The exact delivery mechanism is a design decision.

### Data Integrity Rules

- Facility, pricing, and vehicle compatibility must be validated before booking.
- Monetary values should use a suitable decimal representation rather than floating-point arithmetic for authoritative totals.
- A booking must reference a real facility and supported vehicle type.
- A payment reference must point to the booking it belongs to.
- Invoice totals must match the confirmed booking/payment record.
- Availability updates should have a clear timestamp; if no valid value exists, it must remain unknown rather than being treated as zero or available without an explicit rule.

## 13. Suggested API Surface

These are implementation suggestions inferred from the requirements, not endpoint names mandated by the proposal.

### Public Search and Facility Information

- `GET /api/parking-facilities` - search registered facilities by location and supported criteria.
- `GET /api/parking-facilities/{id}` - retrieve a facility's details.
- `POST /api/ai/parse-parking-request` - convert natural language into validated structured search requirements through the backend.
- `POST /api/ai/recommendations` - return matching registered facilities using requirements and database data.
- `POST /api/booking/quote` - calculate or validate the estimated fee for facility, vehicle type, and duration.

### Booking and Payment

- `POST /api/bookings` - validate and create a booking or payment-pending record.
- `GET /api/bookings/{bookingId}` - retrieve a booking confirmation or details according to access rules.
- `POST /api/payments/initiate` - create a payment attempt/reference.
- `POST /api/payments/callback` - process a provider/server-confirmed payment result, subject to provider requirements.
- `GET /api/bookings/{bookingId}/invoice` - retrieve or generate invoice details.

### Administrator

- `POST /api/admin/login` - administrator sign-in, following the selected authentication approach.
- `GET /api/admin/parking-facilities` - list facilities for management.
- `POST /api/admin/parking-facilities` - create a facility.
- `PUT /api/admin/parking-facilities/{id}` - update a facility.
- `PUT /api/admin/parking-facilities/{id}/pricing` - update pricing.
- `PUT /api/admin/parking-facilities/{id}/availability` - update reported availability.
- `GET /api/admin/bookings` - list and filter bookings.
- `PATCH /api/admin/bookings/{id}/status` - update an allowed booking status.
- `GET /api/admin/notifications` - retrieve new-booking notifications.

All private endpoints must enforce backend authorization and validate incoming data. Never expose Gemini or payment secrets to the browser.

## 14. AI Behaviour and Failure Handling

### 14.1 Input and Extraction

The AI should extract only the requirements expressed or reasonably unambiguous in a request. Example structured output may contain `location`, `vehicle_type`, `budget`, and `duration`; fields not supplied by the user should remain empty/unknown.

### 14.2 Matching

The backend/application queries the database and applies matching logic. Gemini can interpret the user's language, but facility candidates, price, and availability values must come from actual stored records.

### 14.3 No Suitable Match

When no recorded facility satisfies the requirements, show a no-match message. Do not generate fictional facilities or change prices to create a result.

### 14.4 Missing or Stale Availability

If availability is absent or potentially outdated, indicate that it could not be reliably confirmed. Do not state that a facility definitely has space.

### 14.5 AI Provider Failure

If Gemini is unavailable, times out, or returns invalid data:

- Validate and reject unusable AI output.
- Show a clear message that AI-assisted search is temporarily unavailable.
- Keep normal location search available.
- Log a safe diagnostic without logging secrets or unnecessary personal details.

### 14.6 AI Boundaries

AI will not detect vehicle occupancy, create availability data, determine authoritative prices, process payments, confirm bookings, or update booking/availability records autonomously.

## 15. Security, Privacy, and Ethical Considerations

- Request GPS permission before using current location and provide manual location selection as an alternative.
- Use location only for the requested search flow and avoid unnecessary retention of precise location history.
- Restrict admin endpoints to authorized users.
- Validate all input on the backend and use safe database query patterns.
- Store credentials in local environment files excluded by Git; commit only safe example environment files.
- Never hard-code Gemini, map, or payment secrets in frontend code or public repositories.
- Do not store sensitive payment credentials unnecessarily.
- Explain that availability is based on reported system records and may not be live if the operator/admin has not updated it.
- Clearly distinguish sandbox/test payment from live payment.
- Use AI recommendations grounded in stored parking information and communicate uncertainty honestly.

## 16. Testing Strategy

### 16.1 Unit-Level Testing

- Price calculation for supported vehicle types and durations.
- Validation of facility, vehicle compatibility, and booking fields.
- Availability count/slot validation according to the chosen model.
- Natural-language extraction validation and matching rules.
- Booking state transition rules.

### 16.2 API / Integration Testing

- Frontend-to-Laravel API connectivity.
- Laravel-to-MySQL read/write behaviour.
- Search query returns only registered facility records.
- Gemini output is validated and matched to database records.
- Payment sandbox result updates the intended booking only.
- Invoice fields agree with booking/payment records.
- Unauthorized users cannot call protected admin endpoints.

### 16.3 End-to-End Scenarios

1. GPS allowed -> nearby facilities displayed -> facility selected -> quote calculated -> booking submitted -> payment succeeds -> invoice displayed.
2. GPS denied -> manual location chosen -> results displayed -> user continues through booking.
3. Natural-language request specifies location, vehicle, budget, and duration -> AI extracts requirements -> database returns suitable results.
4. Natural-language request has no suitable database match -> clear no-match message, no fictional result.
5. Facility availability is missing or outdated -> system communicates uncertainty and does not falsely promise space.
6. Administrator changes a facility price -> subsequent quotes use the updated rate; existing confirmed invoice totals remain unchanged.
7. Administrator updates availability -> subsequent search reflects the recorded update.
8. A booking request exceeds recorded capacity -> backend rejects it.
9. Payment fails or is cancelled -> booking is not represented as paid and no paid invoice is issued.
10. Unauthorized user attempts admin action -> request is rejected.
11. Parking staff checks a valid booking invoice -> record can be located and status verified.
12. External AI/map/payment service is unavailable -> user sees a controlled message and unrelated core functions remain usable where possible.

### 16.4 Responsive / Usability Testing

- Test core screens on common desktop and mobile browser sizes.
- Confirm controls, labels, field errors, loading indicators, and empty states remain usable.
- Confirm the booking/invoice information remains readable on smaller screens.

## 17. Twelve-Week Delivery Plan

| Week | Focus | Expected deliverable |
|---|---|---|
| 1 | Requirement review, scope confirmation, user journeys | Approved feature checklist and initial task board |
| 2 | UI wireframes, architecture, database/ER design | Wireframes and reviewed schema draft |
| 3 | Environment setup for Vue, Laravel, database, Git | Frontend and backend skeletons run locally |
| 4 | Landing page and responsive base layout | Working public entry page |
| 5 | GPS/manual location search and map integration | Initial search flow |
| 6 | Facility details, vehicle compatibility, prices, availability display | Parking information module |
| 7 | Admin authentication, facility/pricing/availability management | Functional admin management flow |
| 8 | Booking, price revalidation, availability validation, booking status | Working booking process |
| 9 | Gemini request parsing and validation | Structured natural-language requirements |
| 10 | Database-grounded recommendations and invoice; sandbox payment if feasible | Recommendation and confirmation/invoice flow |
| 11 | API, end-to-end, security, failure, and responsive testing | Test results and bug fixes |
| 12 | Final fixes, documentation, presentation, demonstration | Final working prototype and report |

Payment integration is subject to sandbox access and remaining time. Core parking search, reliable recorded availability, booking, and database-grounded AI recommendations take priority.

## 18. Open Questions to Resolve Before Implementation

The proposal does not specify the following details. The team should make and document these decisions rather than letting the codebase decide them accidentally.

0. **Team-size consistency:** The cover page and updated task distribution list three team members, but some earlier proposal text refers to a two-person team/two developers. Update those statements in the final proposal so the team size is consistent.
1. **Availability model:** Is availability stored as a count per facility, per vehicle category, or as individually identified parking spaces?
2. **Vehicle categories:** Which types will the prototype support, and which facilities support each type?
3. **Duration and pricing rules:** Which time increments, rate rules, rounding, minimum charge, or overtime rules will apply?
4. **Location dataset:** What data source will populate Division, District, Thana, and Area values? How will user-entered locations be normalized?
5. **Booking contact fields:** Which user details must be collected at booking time if account creation is not required for browsing?
6. **Operator permissions:** Will participating parking operators use the same admin panel with restricted permissions, or will only the system administrator update data?
7. **Staff verification:** Will the prototype offer a protected booking lookup screen, or will staff verification be demonstrated through an administrator view? QR scanning is not currently required.
8. **Booking status rules:** Which statuses and transitions are allowed for payment pending, confirmed, cancelled, and completed bookings?
9. **Booking expiry/cancellation:** Can users cancel bookings? If yes, what happens to recorded availability and payment state? These rules are not defined in the proposal.
10. **Notification channel:** Will new-booking notifications be in-app only or use an additional channel?
11. **Payment fallback:** If SSLCOMMERZ sandbox access is unavailable, will the supervisor approve a labelled mock payment flow for demonstration?
12. **Availability freshness:** What age of an update should be labelled potentially outdated? The proposal requires honest communication but does not define a time threshold.
13. **Search result limit:** Should the initial screen display exactly five results when available, or up to five with a load-more/expanded list approach?
14. **Guest booking access:** Will a guest be able to retrieve a booking later using a booking ID plus verification detail, or only through the confirmation page? The current proposal does not define this.

## 19. Project Definition of Done

A feature is done when:

- Its frontend interaction is implemented and usable.
- Required backend validation and business logic are implemented.
- Required database structure and migrations are included.
- Loading, validation, empty, and error states are covered.
- The feature has been tested locally with valid and invalid scenarios.
- It does not expose secrets or bypass backend authorization.
- It is integrated with the related Parkly flows.
- The team has reviewed the change and updated relevant documentation.

The overall prototype is done when the priority P0 flows can be demonstrated end-to-end with seeded registered parking records, administrator-managed availability, database-grounded AI recommendations, booking validation, and digital confirmation. Sandbox payment should be integrated if access and schedule allow, with any limitation documented honestly.

## 20. References and Technical Documentation

These links are carried forward from the project proposal. Check current setup instructions, access requirements, pricing, quotas, and service terms before integration.

- Google Maps Platform, Places API: <https://developers.google.com/maps/documentation/places/web-service>
- Google Gemini API: <https://ai.google.dev/gemini-api/docs>
- SSLCOMMERZ Sandbox Documentation: <https://sandbox-gw.sslcommerz.com/docs>
- Laravel Documentation: <https://laravel.com/docs>
- Vue.js Documentation: <https://vuejs.org/guide/>
- Tailwind CSS Documentation: <https://tailwindcss.com/docs>
- MySQL Documentation: <https://dev.mysql.com/doc/>

---

**Document note:** This PRD expands the uploaded Parkly project proposal into implementable feature requirements. Items explicitly marked as open questions or implementation suggestions are not settled requirements from the proposal and should be approved by the project team/supervisor before development.
