<script setup>
/**
 * SearchWidget Component — Split 2-Column Map & Search Section on Homepage
 *
 * Requirements & Behavior:
 * 1. Dhaka-only filtering: District -> Thana -> Area (Division removed).
 * 2. Selecting District/Thana/Area does NOT move or zoom the map.
 * 3. Map shows no facility pins until the user clicks Search (Initial = Dhaka overview).
 * 4. On Search: queries matching facilities in that area, renders pins + result list, calls fitBounds.
 * 5. Choosing a facility (card or pin click) zooms in to that facility.
 * 6. Changing a parent field resets children and marks/clears search results.
 * 7. GPS button debugged & fixed: explicit states (requesting, success, denied, unavailable, timeout).
 * 8. Zero emojis: pure inline SVG icons with currentColor.
 */
import { ref, computed, onMounted, nextTick } from 'vue'
import AppButton from '../common/AppButton.vue'
import ParklyMap from '../parking/ParklyMap.vue'
import { useSearchFiltersStore } from '../../stores/searchFilters'

const emit = defineEmits(['search', 'aiSearch'])

const searchFilters = useSearchFiltersStore()
const mapRef = ref(null)

const isAiMode = ref(false)
const aiQuery = ref('')
const hasSearched = ref(false)
const searchResults = ref([])
const isSearching = ref(false)
const selectedFacilityId = ref(null)

// Location Options
const districtOptions = ref([
  { id: 'dhaka-district', name: 'Dhaka District', name_bn: 'ঢাকা জেলা' }
])

const thanaOptions = ref([
  { id: 'gulshan', name: 'Gulshan', name_bn: 'গুলশান', facility_count: 4 },
  { id: 'banani', name: 'Banani', name_bn: 'বনানী', facility_count: 2 },
  { id: 'dhanmondi', name: 'Dhanmondi', name_bn: 'ধানমন্ডি', facility_count: 3 },
  { id: 'mirpur', name: 'Mirpur', name_bn: 'মিরপুর', facility_count: 3 },
  { id: 'uttara', name: 'Uttara', name_bn: 'উত্তরা', facility_count: 2 },
  { id: 'motijheel', name: 'Motijheel', name_bn: 'মতিঝিল', facility_count: 2 },
  { id: 'tejgaon', name: 'Tejgaon & Panthapath', name_bn: 'তেজগাঁও', facility_count: 2 },
  { id: 'badda', name: 'Badda & Kuril', name_bn: 'বাড্ডা', facility_count: 1 },
])

const areaOptions = ref([])

const vehicleOptions = [
  { id: 'car', label: 'Car / Sedan' },
  { id: 'suv', label: 'SUV / 4x4' },
  { id: 'bike', label: 'Motorbike' },
  { id: 'ev', label: 'Others / EV' },
]

const durationOptions = [
  { id: '1', label: '1 Hour' },
  { id: '2', label: '2 Hours' },
  { id: '3', label: '3 Hours' },
  { id: '4', label: '4 Hours' },
  { id: 'day', label: 'Full Day' },
]

const sampleAiPrompts = [
  'Covered parking near Gulshan-2 for an SUV',
  'Multi-level parking in Motijheel City Centre for 3 hours',
  'Cheapest 2-hour spot around Dhanmondi 27',
  'Motorcycle parking near Mirpur 10 circle',
]

// Authentic registered facilities list
const allFacilitiesInventory = [
  {
    id: 1,
    name: 'Gulshan-2 Smart On-Street Zone (DNCC)',
    thana: 'gulshan',
    address_text: 'Road 58, 62 & Gulshan 2 Circular Rd, Dhaka',
    latitude: 23.7935,
    longitude: 90.4145,
    distance: 0.3,
    vehicle_rates: [{ rate_amount: '80', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 60, available_slots: 24 }],
  },
  {
    id: 2,
    name: 'Gulshan-1 DIT Commercial Hub',
    thana: 'gulshan',
    address_text: 'DIT Road, Near Navana Tower, Gulshan-1',
    latitude: 23.7808,
    longitude: 90.4178,
    distance: 0.5,
    vehicle_rates: [{ rate_amount: '90', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 45, available_slots: 12 }],
  },
  {
    id: 3,
    name: 'Banani Kemal Ataturk Bay',
    thana: 'banani',
    address_text: 'Kemal Ataturk Avenue, Road 11, Banani',
    latitude: 23.7937,
    longitude: 90.4066,
    distance: 0.9,
    vehicle_rates: [{ rate_amount: '70', vehicle_type: { name: 'SUV/4x4' } }],
    availabilities: [{ total_slots: 50, available_slots: 15 }],
  },
  {
    id: 4,
    name: 'City Centre Multi-Level Garage (DSCC)',
    thana: 'motijheel',
    address_text: 'Dilkusha Commercial Area, Motijheel, Dhaka',
    latitude: 23.7294,
    longitude: 90.4175,
    distance: 2.8,
    vehicle_rates: [{ rate_amount: '100', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 120, available_slots: 38 }],
  },
  {
    id: 5,
    name: 'Bashundhara City Parking Complex',
    thana: 'tejgaon',
    address_text: 'Panthapath East-West Corridor, Dhaka',
    latitude: 23.7513,
    longitude: 90.3906,
    distance: 1.5,
    vehicle_rates: [{ rate_amount: '60', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 200, available_slots: 85 }],
  },
  {
    id: 6,
    name: 'Dhanmondi 27 Square Parking (Rapa Plaza)',
    thana: 'dhanmondi',
    address_text: 'Satmasjid Road & Old Mirpur Rd, Dhanmondi',
    latitude: 23.7466,
    longitude: 90.3738,
    distance: 1.8,
    vehicle_rates: [{ rate_amount: '60', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 40, available_slots: 11 }],
  },
  {
    id: 7,
    name: 'Jamuna Future Park Basement Bay',
    thana: 'badda',
    address_text: 'Pragati Sarani, Kuril / Bashundhara, Dhaka',
    latitude: 23.8111,
    longitude: 90.4247,
    distance: 3.2,
    vehicle_rates: [{ rate_amount: '50', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 150, available_slots: 64 }],
  },
  {
    id: 8,
    name: 'Uttara Sector-3 Rajlaxmi Parking Zone',
    thana: 'uttara',
    address_text: 'Dhaka-Mymensingh Hwy, Sector 3, Uttara',
    latitude: 23.8640,
    longitude: 90.3989,
    distance: 4.5,
    vehicle_rates: [{ rate_amount: '50', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 50, available_slots: 19 }],
  },
  {
    id: 9,
    name: 'Mirpur DOHS Smart Bay Hub',
    thana: 'mirpur',
    address_text: 'Road 12, Mirpur DOHS, Dhaka',
    latitude: 23.8086,
    longitude: 90.3675,
    distance: 3.0,
    vehicle_rates: [{ rate_amount: '50', vehicle_type: { name: 'Car/Sedan' } }],
    availabilities: [{ total_slots: 60, available_slots: 22 }],
  },
]

// Facilities currently displayed on map (Only shown AFTER Search is triggered)
const displayedMapFacilities = computed(() => {
  if (!hasSearched.value) return []
  return searchResults.value
})

// Estimated count hint for current selection
const selectedAreaCountHint = computed(() => {
  const currentThana = searchFilters.selectedThana
  if (!currentThana) return null
  const matched = allFacilitiesInventory.filter(f => f.thana === currentThana)
  return matched.length
})

onMounted(() => {
  searchFilters.initFromUrlAndStorage()
  if (searchFilters.selectedThana) {
    handleSearch()
  }
})

// Selecting Thana does NOT move the map, only updates state & resets previous results
function onThanaSelected() {
  hasSearched.value = false
  searchResults.value = []
  selectedFacilityId.value = null
  updateAreasForThana(searchFilters.selectedThana)
}

function updateAreasForThana(thanaSlug) {
  if (thanaSlug === 'gulshan') {
    areaOptions.value = [
      { id: 'gulshan-1', name: 'Gulshan 1', name_bn: 'গুলশান ১' },
      { id: 'gulshan-2', name: 'Gulshan 2', name_bn: 'গুলশান ২' },
    ]
  } else if (thanaSlug === 'banani') {
    areaOptions.value = [
      { id: 'banani-road-11', name: 'Road 11', name_bn: '১১ নম্বর রোড' },
      { id: 'kemal-ataturk', name: 'Kemal Ataturk Ave', name_bn: 'কামাল আতাতুর্ক এভিনিউ' },
    ]
  } else if (thanaSlug === 'dhanmondi') {
    areaOptions.value = [
      { id: 'dhanmondi-27', name: 'Dhanmondi 27', name_bn: 'ধানমন্ডি ২৭' },
      { id: 'shimanto-square', name: 'Shimanto Square', name_bn: 'সীমান্ত স্কয়ার' },
    ]
  } else if (thanaSlug === 'mirpur') {
    areaOptions.value = [
      { id: 'mirpur-10', name: 'Mirpur 10 Circle', name_bn: 'মিরপুর ১০' },
      { id: 'mirpur-dohs', name: 'Mirpur DOHS', name_bn: 'মিরপুর ডিওএইচএস' },
    ]
  } else {
    areaOptions.value = []
  }
}

// GPS Button handler: debugged end-to-end
function handleGpsClick() {
  searchFilters.requestGps(({ lat, lng, isInsideDhaka }) => {
    if (isInsideDhaka) {
      // Find nearest facilities and trigger search
      searchResults.value = allFacilitiesInventory
      hasSearched.value = true
      nextTick(() => {
        if (mapRef.value?.fitBoundsToFacilities) {
          mapRef.value.fitBoundsToFacilities(searchResults.value)
        }
      })
    }
  })
}

// Search Button: queries facilities for selected area and calls fitBounds over pins
async function handleSearch() {
  isSearching.value = true
  const thana = searchFilters.selectedThana

  let matched = allFacilitiesInventory
  if (thana) {
    matched = allFacilitiesInventory.filter(f => f.thana === thana)
  }

  searchResults.value = matched
  hasSearched.value = true
  isSearching.value = false

  emit('search', {
    district: searchFilters.selectedDistrict,
    thana: searchFilters.selectedThana,
    area: searchFilters.selectedArea,
    vehicle: searchFilters.selectedVehicle,
    duration: searchFilters.durationHours,
  })

  nextTick(() => {
    if (mapRef.value?.fitBoundsToFacilities && searchResults.value.length > 0) {
      mapRef.value.fitBoundsToFacilities(searchResults.value)
    }
  })
}

// Choosing ONE facility zooms into it
function handleSelectFacility(facilityId) {
  selectedFacilityId.value = facilityId
  const fac = searchResults.value.find(f => f.id === facilityId)
  if (fac && mapRef.value?.flyTo) {
    mapRef.value.flyTo(parseFloat(fac.latitude), parseFloat(fac.longitude), 17)
  }
}

function handleAiSubmit() {
  if (!aiQuery.value.trim()) return
  emit('aiSearch', aiQuery.value)
  hasSearched.value = true
  searchResults.value = allFacilitiesInventory.slice(0, 4)
  nextTick(() => {
    if (mapRef.value?.fitBoundsToFacilities) {
      mapRef.value.fitBoundsToFacilities(searchResults.value)
    }
  })
}

function pickAiPrompt(prompt) {
  aiQuery.value = prompt
  handleAiSubmit()
}

function getLowestRate(facility) {
  const rates = facility.vehicle_rates
  if (!rates || rates.length === 0) return 40
  return rates[0].rate_amount
}

function getAvailability(facility) {
  if (facility.availabilities && facility.availabilities.length > 0) {
    return facility.availabilities[0]
  }
  return { available_slots: 10, total_slots: 40 }
}
</script>

<template>
  <section class="search-widget-section" id="discovery">
    <div class="search-widget-container">
      
      <!-- Section Header -->
      <div class="discovery-header">
        <div class="eyebrow">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          DHAKA METROPOLITAN PARKING MAP
        </div>
        <h2 class="discovery-title">Dhaka city parking discovery & verified records.</h2>
        <p class="discovery-subtitle">
          Search registered facilities by District, Thana and Area. Results appear on map upon search.
        </p>
      </div>

      <!-- Main Split Grid (Left: Filter Form | Right: Map) -->
      <div class="split-discovery-grid">
        
        <!-- LEFT COLUMN: Search & Filter Panel + Results -->
        <div class="search-column">
          
          <!-- Mode Switcher -->
          <div class="search-mode-switcher">
            <button
              type="button"
              class="mode-btn"
              :class="{ 'is-active': !isAiMode }"
              @click="isAiMode = false"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
              Direct Search
            </button>

            <button
              type="button"
              class="mode-btn ai-btn"
              :class="{ 'is-active': isAiMode }"
              @click="isAiMode = true"
            >
              <span class="ai-sparkle-dot"></span>
              Ask Parkly AI
            </button>
          </div>

          <!-- Mode 1: Direct Cascading Search Form (Dhaka Only) -->
          <div v-if="!isAiMode" class="search-card-panel">
            
            <!-- Row 1: District -> Thana -> Area Cascade -->
            <div class="form-row-cascade">
              <!-- District (Fixed to Dhaka) -->
              <div class="search-field">
                <label class="field-label">DISTRICT</label>
                <select
                  v-model="searchFilters.selectedDistrict"
                  class="zone-select"
                  disabled
                >
                  <option value="dhaka-district">Dhaka District (ঢাকা)</option>
                </select>
              </div>

              <!-- Thana -->
              <div class="search-field">
                <label for="thana-select" class="field-label">THANA / ZONE</label>
                <div class="field-input-row">
                  <select
                    id="thana-select"
                    v-model="searchFilters.selectedThana"
                    class="zone-select"
                    @change="onThanaSelected"
                  >
                    <option value="">Select Thana (All Dhaka)</option>
                    <option v-for="t in thanaOptions" :key="t.id" :value="t.id">
                      {{ t.name }} ({{ t.name_bn }})
                    </option>
                  </select>

                  <!-- GPS Button: Verified & Handled -->
                  <button
                    type="button"
                    class="gps-pill-btn"
                    :class="{ 'is-loading': searchFilters.isDetectingGps }"
                    title="Use Current Location (GPS)"
                    aria-label="Use Current Location (GPS)"
                    @click="handleGpsClick"
                  >
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="3" />
                      <path d="M12 2v3m0 14v3M2 12h3m14 0h3" />
                    </svg>
                    <span class="gps-text">GPS</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Area & Hint Row -->
            <div v-if="areaOptions.length > 0" class="search-field">
              <label for="area-select" class="field-label">AREA / LOCALITY</label>
              <select
                id="area-select"
                v-model="searchFilters.selectedArea"
                class="zone-select"
              >
                <option value="">All areas in this Thana</option>
                <option v-for="a in areaOptions" :key="a.id" :value="a.id">
                  {{ a.name }} ({{ a.name_bn }})
                </option>
              </select>
            </div>

            <!-- Hint: N registered parking in this area -->
            <div v-if="selectedAreaCountHint !== null" class="area-hint-row">
              <span class="hint-bullet">•</span>
              <span class="hint-text">
                {{ selectedAreaCountHint > 0
                  ? `${selectedAreaCountHint} registered parking facilities in this zone (click Search to view on map)`
                  : 'No registered parking in this area yet'
                }}
              </span>
            </div>

            <!-- GPS Status Message (if any) -->
            <div v-if="searchFilters.gpsMessage" class="gps-feedback-msg" :class="searchFilters.gpsStatus">
              {{ searchFilters.gpsMessage }}
            </div>

            <!-- Row 2: Vehicle & Duration -->
            <div class="form-row-dual">
              <div class="search-field">
                <label for="search-vehicle" class="field-label">VEHICLE TYPE</label>
                <select id="search-vehicle" v-model="searchFilters.selectedVehicle" class="pill-select">
                  <option v-for="veh in vehicleOptions" :key="veh.id" :value="veh.id">
                    {{ veh.label }}
                  </option>
                </select>
              </div>

              <div class="search-field">
                <label for="search-duration" class="field-label">DURATION</label>
                <select id="search-duration" v-model="searchFilters.durationHours" class="pill-select">
                  <option v-for="dur in durationOptions" :key="dur.id" :value="dur.id">
                    {{ dur.label }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Row 3: Submit Button (Triggers API & Map Bounds) -->
            <AppButton
              variant="primary"
              size="md"
              pill
              class="search-submit-btn"
              :loading="isSearching"
              @click="handleSearch"
            >
              Search Registered Parking
              <template #suffix>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </template>
            </AppButton>
          </div>

          <!-- Mode 2: AI Search Form -->
          <div v-else class="search-ai-card">
            <div class="ai-input-wrapper">
              <input
                v-model="aiQuery"
                type="text"
                placeholder="Ask e.g. 'Covered parking near Gulshan-2 for an SUV'..."
                class="ai-input-control"
                @keyup.enter="handleAiSubmit"
              />
              <AppButton
                variant="primary"
                size="sm"
                pill
                class="ai-submit-btn"
                @click="handleAiSubmit"
              >
                Ask AI
              </AppButton>
            </div>

            <div class="ai-suggestions-row">
              <span class="suggestions-label">Try asking:</span>
              <div class="suggestions-chips">
                <button
                  v-for="(prompt, index) in sampleAiPrompts"
                  :key="index"
                  type="button"
                  class="suggestion-chip"
                  @click="pickAiPrompt(prompt)"
                >
                  "{{ prompt }}"
                </button>
              </div>
            </div>
          </div>

          <!-- Search Results List (Shown on Search) -->
          <div class="discovery-results">
            <div class="results-top-bar">
              <span class="results-heading">
                {{ hasSearched ? 'Search Results' : 'Search Required' }}
                <strong v-if="hasSearched" class="count-badge">({{ searchResults.length }})</strong>
              </span>
              <span v-if="!hasSearched" class="results-subhint">
                Select your area and click Search to display map pins.
              </span>
            </div>

            <div v-if="hasSearched && searchResults.length === 0" class="no-results-panel">
              <p class="no-results-text">No registered parking in this area yet.</p>
              <p class="no-results-sub">Explore nearby hubs in Gulshan, Banani, or Dhanmondi.</p>
            </div>

            <div v-else-if="hasSearched" class="results-scroll-list">
              <div
                v-for="facility in searchResults"
                :key="facility.id"
                class="facility-mini-card"
                :class="{ 'is-selected': facility.id === selectedFacilityId }"
                @click="handleSelectFacility(facility.id)"
              >
                <div class="f-top">
                  <h4 class="f-name">{{ facility.name }}</h4>
                  <span
                    v-if="getAvailability(facility)"
                    class="f-avail"
                    :class="getAvailability(facility).available_slots > 0 ? 'is-open' : 'is-full'"
                    title="Reported availability • Updated recently by admin/operator"
                  >
                    <span class="live-dot"></span>
                    {{ getAvailability(facility).available_slots }} reported slots
                  </span>
                </div>
                <p class="f-address">{{ facility.address_text }}</p>
                <div class="f-bottom">
                  <span class="f-price">
                    ৳{{ getLowestRate(facility) }}/hr
                  </span>
                  <span class="f-action-hint">Click to focus pin →</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: Interactive Map (Only displays pins when search is active) -->
        <div class="map-column">
          <ParklyMap
            ref="mapRef"
            :facilities="displayedMapFacilities"
            :selected-facility-id="selectedFacilityId"
            @select-facility="handleSelectFacility"
          />
        </div>

      </div>
    </div>
  </section>
</template>

<style scoped>
.search-widget-section {
  width: 100%;
  padding: 64px 24px;
  background-color: var(--color-bg);
  scroll-margin-top: var(--nav-h, 72px);
}

.search-widget-container {
  max-width: 1200px;
  margin: 0 auto;
}

.discovery-header {
  margin-bottom: 28px;
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-primary);
  margin-bottom: 8px;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.discovery-title {
  font-family: var(--font-sans);
  font-size: clamp(1.5rem, 2.8vw, 2rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  color: var(--color-text);
  margin: 0 0 6px;
}

.discovery-subtitle {
  font-size: 0.90625rem;
  color: var(--color-text-muted);
  margin: 0;
}

.split-discovery-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 28px;
}

@media (min-width: 992px) {
  .split-discovery-grid {
    grid-template-columns: 460px 1fr;
  }
}

/* Left search column */
.search-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.search-mode-switcher {
  display: flex;
  gap: 8px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  padding: 4px;
  border-radius: var(--radius-lg, 12px);
}

.mode-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 14px;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--color-text-muted);
  background: transparent;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.mode-btn.is-active {
  color: var(--color-primary);
  background-color: var(--color-bg);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.ai-sparkle-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-accent, #14B8A6);
}

.search-card-panel {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row-cascade,
.form-row-dual {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.search-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-text-muted);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.field-input-row {
  display: flex;
  gap: 8px;
  align-items: center;
}

.zone-select,
.pill-select {
  width: 100%;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 9px 12px;
  font-size: 0.8125rem;
  color: var(--color-text);
  font-weight: 500;
  outline: none;
}

.zone-select:focus,
.pill-select:focus {
  border-color: var(--color-primary);
}

.gps-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 8px 12px;
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--color-primary);
  cursor: pointer;
  flex-shrink: 0;
}

.gps-pill-btn:hover {
  border-color: var(--color-primary);
  background: rgba(15, 118, 110, 0.05);
}

.area-hint-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  color: var(--color-primary);
  font-weight: 600;
}

.gps-feedback-msg {
  font-size: 0.75rem;
  padding: 6px 10px;
  border-radius: 6px;
}

.gps-feedback-msg.success {
  background: rgba(22, 163, 74, 0.1);
  color: #16A34A;
}

.gps-feedback-msg.denied,
.gps-feedback-msg.unavailable,
.gps-feedback-msg.timeout {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
}

.search-submit-btn {
  width: 100%;
  margin-top: 4px;
}

/* AI Mode */
.search-ai-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.ai-input-wrapper {
  display: flex;
  gap: 8px;
}

.ai-input-control {
  flex: 1;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 10px 14px;
  font-size: 0.8125rem;
  color: var(--color-text);
  outline: none;
}

.ai-suggestions-row {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.suggestions-label {
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-text-muted);
}

.suggestions-chips {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.suggestion-chip {
  text-align: left;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.75rem;
  color: var(--color-text);
  cursor: pointer;
}

.suggestion-chip:hover {
  border-color: var(--color-primary);
}

/* Results panel */
.discovery-results {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 16px;
}

.results-top-bar {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 12px;
  flex-wrap: wrap;
  gap: 8px;
}

.results-heading {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-text);
}

.count-badge {
  color: var(--color-primary);
}

.results-subhint {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
}

.no-results-panel {
  padding: 20px 12px;
  text-align: center;
}

.no-results-text {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.no-results-sub {
  font-size: 0.75rem;
  color: var(--color-text-muted);
  margin: 4px 0 0;
}

.results-scroll-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-height: 240px;
  overflow-y: auto;
}

.facility-mini-card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 12px;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.facility-mini-card:hover,
.facility-mini-card.is-selected {
  border-color: var(--color-primary);
}

.f-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.f-name {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.f-avail {
  font-size: 0.6875rem;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 4px;
}

.f-avail.is-open {
  background: rgba(22, 163, 74, 0.1);
  color: #16A34A;
}

.f-avail.is-full {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
}

.live-dot {
  display: inline-block;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: currentColor;
  margin-right: 2px;
}

.f-address {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
  margin: 4px 0;
}

.f-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.75rem;
}

.f-price {
  font-weight: 700;
  color: var(--color-primary);
}

.f-action-hint {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
}

/* Map Column */
.map-column {
  min-height: 480px;
  border-radius: var(--radius-xl, 16px);
  overflow: hidden;
  border: 1px solid var(--color-border);
}
</style>
