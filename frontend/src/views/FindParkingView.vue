<script setup>
/**
 * FindParkingView — Main "Find Parking" page
 * Two-column layout: Google Map (left) + Search panel & results list (right)
 * Mobile: toggleable map/list via FAB
 */
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import SearchWidget from '../components/home/SearchWidget.vue'
import ParklyMap from '../components/parking/ParklyMap.vue'
import { useParkingStore } from '../stores/parking'

const parkingStore = useParkingStore()

const mapRef = ref(null)
const userLocation = ref(null)
const resultsListRef = ref(null)

// --- Lifecycle ---
onMounted(() => {
  parkingStore.fetchAllFacilities()

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLocation.value = {
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
        }
      },
      () => {
        // GPS denied or unavailable — leave null
      },
      { timeout: 8000, enableHighAccuracy: false }
    )
  }
})

// --- Search Handlers ---
function handleDirectSearch(params) {
  parkingStore.searchFacilities(params).then(() => {
    fitMapToResults()
  })
}

function handleAISearch(query) {
  parkingStore.searchByAI(query).then(() => {
    fitMapToResults()
  })
}

function handleSelectFacility(id) {
  parkingStore.selectFacility(id)
}

function handleClearSearch() {
  parkingStore.clearSearch()
  if (mapRef.value && mapRef.value.fitBoundsToAll) {
    mapRef.value.fitBoundsToAll()
  }
}

function fitMapToResults() {
  nextTick(() => {
    if (mapRef.value && mapRef.value.fitBoundsToFacilities) {
      mapRef.value.fitBoundsToFacilities(parkingStore.displayFacilities)
    }
  })
}

// --- Map Events ---
function onMapReady() {
  // Map is initialized — no-op for now
}

function onMapError(error) {
  console.error('Map error:', error)
}

// --- Helpers ---
function getAvailability(facility) {
  if (facility.availabilities && facility.availabilities.length > 0) {
    return facility.availabilities[0]
  }
  if (facility.availability) {
    return facility.availability
  }
  return null
}

function getLowestRate(facility) {
  const rates = facility.vehicle_rates
  if (!rates || rates.length === 0) return null

  let min = Infinity
  for (const vr of rates) {
    const amount = parseFloat(vr.rate_amount)
    if (!isNaN(amount) && amount < min) {
      min = amount
    }
  }
  return min === Infinity ? null : min
}

function getVehicleName(vehicleRate) {
  if (vehicleRate.vehicle_type && vehicleRate.vehicle_type.name) {
    return vehicleRate.vehicle_type.name
  }
  if (vehicleRate.vehicle_type_name) {
    return vehicleRate.vehicle_type_name
  }
  return 'Vehicle'
}

// --- Watch selectedFacilityId to scroll result card into view ---
watch(
  () => parkingStore.selectedFacilityId,
  (newId) => {
    if (!newId) return
    nextTick(() => {
      const card = document.querySelector(`.result-card[data-facility-id="${newId}"]`)
      if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' })
      }
    })
  }
)
</script>

<template>
  <div class="find-parking-page">
    <div class="fp-container">
      <!-- LEFT: Map Column (desktop) or below search (mobile) -->
      <div class="fp-map-col" :class="{ 'mobile-visible': parkingStore.viewMode === 'map' }">
        <ParklyMap
          ref="mapRef"
          :facilities="parkingStore.displayFacilities"
          :selected-facility-id="parkingStore.selectedFacilityId"
          :is-loading="parkingStore.isLoading || parkingStore.searchLoading"
          :user-location="userLocation"
          aria-label="Parking facilities map"
          @select-facility="handleSelectFacility"
          @map-ready="onMapReady"
          @map-error="onMapError"
        />
      </div>

      <!-- RIGHT: Search & Results Column -->
      <div class="fp-search-col" :class="{ 'mobile-hidden': parkingStore.viewMode === 'map' }">
        <!-- Reuse SearchWidget re-styled for half-width -->
        <div class="fp-search-panel">
          <SearchWidget
            @search="handleDirectSearch"
            @aiSearch="handleAISearch"
          />
        </div>

        <!-- Results List -->
        <div class="fp-results" v-if="parkingStore.displayFacilities.length > 0">
          <div class="results-header">
            <h3 class="results-title">
              {{ parkingStore.hasSearched ? 'Search Results' : 'All Facilities' }}
              <span class="results-count">({{ parkingStore.displayFacilities.length }})</span>
            </h3>
            <button
              v-if="parkingStore.hasSearched"
              class="clear-search-btn"
              @click="handleClearSearch"
            >
              Clear Search
            </button>
          </div>

          <div class="results-list" role="list" ref="resultsListRef">
            <div
              v-for="facility in parkingStore.displayFacilities"
              :key="facility.id"
              class="result-card"
              :class="{ 'is-selected': facility.id === parkingStore.selectedFacilityId }"
              :data-facility-id="facility.id"
              role="listitem"
              tabindex="0"
              :aria-label="`${facility.name}, ${facility.address_text}`"
              @click="handleSelectFacility(facility.id)"
              @keyup.enter="handleSelectFacility(facility.id)"
            >
              <div class="result-top">
                <h4 class="result-name">{{ facility.name }}</h4>
                <span
                  v-if="getAvailability(facility)"
                  class="result-avail-dot"
                  :class="getAvailability(facility).available_slots > 0 ? 'available' : 'full'"
                  :aria-label="getAvailability(facility).available_slots > 0 ? 'Available' : 'Full'"
                ></span>
              </div>
              <p class="result-address">{{ facility.address_text }}</p>
              <div class="result-meta">
                <span v-if="getLowestRate(facility)" class="result-price">
                  ৳{{ getLowestRate(facility) }}/hr
                </span>
                <span v-if="getAvailability(facility)" class="result-slots">
                  {{ getAvailability(facility).available_slots }}/{{ getAvailability(facility).total_slots }} slots
                </span>
                <span v-if="facility.distance" class="result-distance">{{ facility.distance }}</span>
              </div>
              <div class="result-vehicles" v-if="facility.vehicle_rates && facility.vehicle_rates.length">
                <span
                  v-for="vr in facility.vehicle_rates"
                  :key="vr.id"
                  class="result-vehicle-chip"
                >
                  {{ getVehicleName(vr) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty results message -->
        <div v-else-if="parkingStore.hasSearched && !parkingStore.isLoading" class="fp-empty">
          <p>No parking facilities match your search.</p>
          <button class="clear-search-btn" @click="handleClearSearch">Clear filters</button>
        </div>

        <!-- AI Response area -->
        <div v-if="parkingStore.aiResponse" class="ai-response-card">
          <div class="ai-response-header">
            <span class="ai-sparkle">✨</span>
            <span>Parkly AI Response</span>
          </div>
          <p class="ai-response-text">{{ parkingStore.aiResponse }}</p>
        </div>
      </div>
    </div>

    <!-- Mobile Map/List Toggle FAB -->
    <button
      class="mobile-view-toggle"
      @click="parkingStore.toggleViewMode()"
      :aria-label="parkingStore.viewMode === 'list' ? 'Show map' : 'Show list'"
    >
      <!-- Map icon -->
      <svg v-if="parkingStore.viewMode === 'list'" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" />
        <line x1="8" y1="2" x2="8" y2="18" />
        <line x1="16" y1="6" x2="16" y2="22" />
      </svg>
      <!-- List icon -->
      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="8" y1="6" x2="21" y2="6" />
        <line x1="8" y1="12" x2="21" y2="12" />
        <line x1="8" y1="18" x2="21" y2="18" />
        <line x1="3" y1="6" x2="3.01" y2="6" />
        <line x1="3" y1="12" x2="3.01" y2="12" />
        <line x1="3" y1="18" x2="3.01" y2="18" />
      </svg>
      {{ parkingStore.viewMode === 'list' ? 'Map' : 'List' }}
    </button>
  </div>
</template>

<style scoped>
/* ===== Page Layout ===== */
.find-parking-page {
  width: 100%;
  min-height: calc(100dvh - 92px);
  padding: 24px;
  background: var(--color-bg);
}

.fp-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  height: calc(100dvh - 92px - 48px);
}

/* ===== Map Column ===== */
.fp-map-col {
  position: sticky;
  top: 92px;
  height: calc(100dvh - 92px - 48px);
  min-height: 560px;
  border-radius: 24px;
  overflow: hidden;
  border: 1px solid var(--color-border);
}

/* ===== Search & Results Column ===== */
.fp-search-col {
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-height: calc(100dvh - 92px - 48px);
  scrollbar-width: thin;
  scrollbar-color: var(--color-border) transparent;
}

.fp-search-col::-webkit-scrollbar {
  width: 6px;
}

.fp-search-col::-webkit-scrollbar-track {
  background: transparent;
}

.fp-search-col::-webkit-scrollbar-thumb {
  background-color: var(--color-border);
  border-radius: 999px;
}

/* ===== Search Panel — override SearchWidget for half-width stacked layout ===== */
.fp-search-panel :deep(.search-widget-section) {
  padding: 0;
}

.fp-search-panel :deep(.search-card-pill) {
  flex-direction: column;
  align-items: stretch;
  border-radius: 24px;
  padding: 20px;
  gap: 16px;
}

.fp-search-panel :deep(.field-divider) {
  width: 100%;
  height: 1px;
}

.fp-search-panel :deep(.search-submit-btn) {
  width: 100% !important;
}

/* Vehicle + Duration side-by-side within stacked layout */
.fp-search-panel :deep(.field-vehicle),
.fp-search-panel :deep(.field-duration) {
  flex: 1;
  min-width: 0;
}

.fp-search-panel :deep(.search-ai-card) {
  border-radius: 24px;
}

/* ===== Results Header ===== */
.results-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 0 4px;
}

.results-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.results-count {
  font-weight: 500;
  color: var(--color-text-muted);
  font-size: 0.875rem;
}

.clear-search-btn {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text-muted);
  font-size: 0.8125rem;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition-fast);
  white-space: nowrap;
}

.clear-search-btn:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  background-color: var(--color-primary-light);
}

/* ===== Results List ===== */
.results-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* ===== Result Card ===== */
.result-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 16px;
  cursor: pointer;
  transition: all var(--transition-fast);
  outline: none;
}

.result-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-sm);
}

.result-card:focus-visible {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-ring, rgba(15, 118, 110, 0.2));
}

.result-card.is-selected {
  border-color: var(--color-primary);
  background: var(--color-primary-light);
  box-shadow: var(--shadow-sm);
}

/* ===== Card Content ===== */
.result-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 4px;
}

.result-name {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
}

.result-avail-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

.result-avail-dot.available {
  background-color: #22c55e;
  box-shadow: 0 0 6px rgba(34, 197, 94, 0.5);
}

.result-avail-dot.full {
  background-color: #ef4444;
  box-shadow: 0 0 6px rgba(239, 68, 68, 0.4);
}

.result-address {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  margin: 0 0 8px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.result-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.8125rem;
  margin-bottom: 8px;
  flex-wrap: wrap;
}

.result-price {
  color: var(--color-primary);
  font-weight: 600;
}

.result-slots {
  color: var(--color-text-muted);
}

.result-distance {
  color: var(--color-text-muted);
}

.result-vehicles {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}

.result-vehicle-chip {
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 999px;
}

/* ===== Empty State ===== */
.fp-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 48px 24px;
  text-align: center;
  color: var(--color-text-muted);
  font-size: 0.9375rem;
}

.fp-empty p {
  margin: 0;
}

/* ===== AI Response Card ===== */
.ai-response-card {
  background: var(--color-surface);
  border: 1px solid var(--color-primary);
  border-radius: 16px;
  padding: 16px 20px;
  box-shadow: 0 0 12px rgba(15, 118, 110, 0.08);
  transition: all var(--transition-normal);
}

.ai-response-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 0.9375rem;
  color: var(--color-primary);
  margin-bottom: 8px;
}

.ai-sparkle {
  font-size: 1.125rem;
}

.ai-response-text {
  font-size: 0.875rem;
  color: var(--color-text);
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
}

/* ===== Mobile Map/List Toggle FAB ===== */
.mobile-view-toggle {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  display: none;
  align-items: center;
  gap: 8px;
  z-index: 50;
  background-color: var(--color-primary);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 10px 24px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  transition: all var(--transition-fast);
}

.mobile-view-toggle:hover {
  box-shadow: 0 6px 28px rgba(0, 0, 0, 0.3);
  transform: translateX(-50%) translateY(-1px);
}

.mobile-view-toggle:active {
  transform: translateX(-50%) scale(0.97);
}

.mobile-view-toggle svg {
  flex-shrink: 0;
}

/* ===== Dark Mode — AI card glow ===== */
:root[data-theme='dark'] .ai-response-card,
.dark .ai-response-card {
  box-shadow: 0 0 16px rgba(20, 184, 166, 0.15);
  border-color: rgba(20, 184, 166, 0.5);
}

/* ===== RESPONSIVE: Tablet (768–1023px) ===== */
@media (max-width: 1023px) {
  .fp-container {
    grid-template-columns: 1fr;
    height: auto;
  }

  .fp-map-col {
    position: static;
    height: 420px;
    min-height: 420px;
  }

  .fp-search-col {
    max-height: none;
    overflow: visible;
    order: -1;
  }
}

/* ===== RESPONSIVE: Mobile (<768px) ===== */
@media (max-width: 767px) {
  .find-parking-page {
    padding: 16px;
  }

  .fp-container {
    grid-template-columns: 1fr;
    height: auto;
    gap: 0;
  }

  .fp-map-col {
    position: static;
    height: 55vh;
    min-height: 300px;
    display: none;
  }

  .fp-map-col.mobile-visible {
    display: block;
  }

  .fp-search-col {
    max-height: none;
    overflow: visible;
    order: -1;
  }

  .fp-search-col.mobile-hidden {
    display: none;
  }

  .mobile-view-toggle {
    display: inline-flex;
  }

  .result-name {
    font-size: 0.9375rem;
  }

  .result-meta {
    gap: 8px;
  }
}

/* ===== Extra-small (<375px) ===== */
@media (max-width: 374px) {
  .find-parking-page {
    padding: 12px;
  }

  .result-card {
    padding: 12px;
    border-radius: 12px;
  }

  .result-vehicles {
    gap: 4px;
  }

  .result-vehicle-chip {
    font-size: 0.6875rem;
    padding: 2px 8px;
  }

  .mobile-view-toggle {
    padding: 8px 20px;
    font-size: 0.8125rem;
  }
}
</style>
