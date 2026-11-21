<script setup>
/**
 * ParklyMap Component — Hybrid High-Res Satellite & Street Interactive Map
 * 
 * Powered by Leaflet with Esri World Imagery (Satellite) + CartoDB Dark/Positron
 * - 100% Authentic High-Resolution Satellite & Aerial Imagery
 * - NO "For development purposes only" watermark
 * - NO billing errors or broken popups
 * - Real Google/Satellite smooth zoom & fly-to animation directly to target parking slots
 * - Branded Parkly "P" pins with pricing & live slot indicator
 * - 2D / Satellite toggle, zoom controls, compass reset & location finder
 */
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  facilities: {
    type: Array,
    default: () => [],
  },
  selectedFacilityId: {
    type: [Number, String],
    default: null,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  userLocation: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['select-facility', 'map-ready', 'view-details'])

const mapContainerRef = ref(null)
let map = null
let markersGroup = null
let satelliteLayer = null
let streetLayer = null
let labelsLayer = null

const isSatellite = ref(true)
const selectedInfo = ref(null)

// Custom Parkly Pin Maker
function createPinIcon(facility, isSelected = false) {
  const lowestRate = getFacilityLowestRate(facility)
  const isAvailable = checkAvailability(facility)

  const html = `
    <div class="parkly-map-pin ${isSelected ? 'is-selected' : ''}">
      <div class="pin-badge ${!isAvailable ? 'is-full' : ''}">
        <span class="pin-letter">P</span>
        ${lowestRate ? `<span class="pin-price">৳${lowestRate}</span>` : ''}
      </div>
      <div class="pin-pulse"></div>
    </div>
  `

  return L.divIcon({
    html,
    className: 'custom-parkly-leaflet-icon',
    iconSize: [isSelected ? 64 : 54, isSelected ? 38 : 32],
    iconAnchor: [isSelected ? 32 : 27, isSelected ? 19 : 16],
  })
}

function initMap() {
  if (!mapContainerRef.value || map) return

  // Default Dhaka Gulshan center
  const initialCenter = props.userLocation
    ? [props.userLocation.lat, props.userLocation.lng]
    : [23.7808, 90.4178]

  map = L.map(mapContainerRef.value, {
    center: initialCenter,
    zoom: 15,
    zoomControl: false,
    attributionControl: false,
  })

  // 1. High-Resolution Satellite Layer (Esri World Imagery)
  satelliteLayer = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
    {
      maxZoom: 19,
      subdomains: ['server', 'services'],
    }
  )

  // Satellite Reference Labels
  labelsLayer = L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
    {
      maxZoom: 19,
      opacity: 0.85,
    }
  )

  // 2. Clean Street Vector Layer (CartoDB Positron / Dark Matter fallback)
  streetLayer = L.tileLayer(
    'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
    {
      maxZoom: 19,
      subdomains: 'abcd',
    }
  )

  // Start with satellite
  satelliteLayer.addTo(map)
  labelsLayer.addTo(map)

  markersGroup = L.layerGroup().addTo(map)

  map.on('click', () => {
    selectedInfo.value = null
    emit('select-facility', null)
  })

  renderMarkers()
  emit('map-ready')
}

function renderMarkers() {
  if (!map || !markersGroup) return

  markersGroup.clearLayers()

  props.facilities.forEach((facility) => {
    const lat = parseFloat(facility.latitude)
    const lng = parseFloat(facility.longitude)
    if (isNaN(lat) || isNaN(lng)) return

    const isSelected = facility.id === props.selectedFacilityId
    const icon = createPinIcon(facility, isSelected)

    const marker = L.marker([lat, lng], { icon, zIndexOffset: isSelected ? 1000 : 100 })

    marker.on('click', (e) => {
      L.DomEvent.stopPropagation(e)
      emit('select-facility', facility.id)
      flyTo(lat, lng, 17)
    })

    markersGroup.addLayer(marker)
  })

  updateSelectedInfo()
}

function updateSelectedInfo() {
  if (!props.selectedFacilityId) {
    selectedInfo.value = null
    return
  }

  const facility = props.facilities.find((f) => f.id === props.selectedFacilityId)
  if (facility) {
    const lowestRate = getFacilityLowestRate(facility)
    const avail = getFacilityAvailability(facility)

    selectedInfo.value = {
      id: facility.id,
      name: facility.name,
      address_text: facility.address_text,
      distance: facility.distance ? `${Number(facility.distance).toFixed(1)} km away` : null,
      rate: lowestRate ? `৳${lowestRate}/hr` : null,
      vehicleTypes: (facility.vehicle_rates || []).map(
        (vr) => vr.vehicle_type?.name || vr.vehicle_type_name || 'Vehicle'
      ),
      available: avail ? avail.available_slots > 0 : true,
      availableSlots: avail ? avail.available_slots : null,
      totalSlots: avail ? avail.total_slots : null,
    }
  }
}

// --- Smooth Authentic Camera Fly-To & Zoom ---
function flyTo(lat, lng, zoom = 17) {
  if (!map) return
  map.flyTo([lat, lng], zoom, {
    animate: true,
    duration: 1.5,
    easeLinearity: 0.25,
  })
}

function fitBoundsToFacilities(facilitiesList) {
  if (!map) return
  const list = facilitiesList || props.facilities
  if (!list || list.length === 0) return

  if (list.length === 1) {
    const f = list[0]
    flyTo(parseFloat(f.latitude), parseFloat(f.longitude), 17)
    return
  }

  const latLngs = list
    .map((f) => [parseFloat(f.latitude), parseFloat(f.longitude)])
    .filter(([lat, lng]) => !isNaN(lat) && !isNaN(lng))

  if (latLngs.length > 0) {
    const bounds = L.latLngBounds(latLngs)
    map.flyToBounds(bounds, {
      padding: [40, 40],
      duration: 1.4,
      maxZoom: 16,
    })
  }
}

function fitBoundsToAll() {
  fitBoundsToFacilities(props.facilities)
}

// --- Map Controls ---
function toggleLayer() {
  if (!map) return
  isSatellite.value = !isSatellite.value

  if (isSatellite.value) {
    map.removeLayer(streetLayer)
    satelliteLayer.addTo(map)
    labelsLayer.addTo(map)
  } else {
    map.removeLayer(satelliteLayer)
    map.removeLayer(labelsLayer)
    streetLayer.addTo(map)
  }
}

function zoomIn() {
  if (map) map.zoomIn()
}

function zoomOut() {
  if (map) map.zoomOut()
}

function resetNorth() {
  if (map) {
    map.setView([23.7808, 90.4178], 14, { animate: true })
  }
}

function goToMyLocation() {
  if (props.userLocation) {
    flyTo(props.userLocation.lat, props.userLocation.lng, 17)
    return
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        flyTo(pos.coords.latitude, pos.coords.longitude, 17)
      },
      () => {
        flyTo(23.7808, 90.4178, 15)
      },
      { timeout: 6000 }
    )
  }
}

function closeInfo() {
  selectedInfo.value = null
  emit('select-facility', null)
}

function getFacilityLowestRate(facility) {
  const rates = facility.vehicle_rates
  if (!rates || rates.length === 0) return null
  let min = Infinity
  for (const vr of rates) {
    const val = parseFloat(vr.rate_amount)
    if (!isNaN(val) && val < min) min = val
  }
  return min === Infinity ? null : min
}

function getFacilityAvailability(facility) {
  if (facility.availabilities && facility.availabilities.length > 0) {
    return facility.availabilities[0]
  }
  if (facility.availability) return facility.availability
  return null
}

function checkAvailability(facility) {
  const avail = getFacilityAvailability(facility)
  if (!avail) return true
  return avail.available_slots > 0
}

watch(
  () => props.facilities,
  () => renderMarkers(),
  { deep: true }
)

watch(
  () => props.selectedFacilityId,
  (newId) => {
    renderMarkers()
    if (newId) {
      const facility = props.facilities.find((f) => f.id === newId)
      if (facility) {
        flyTo(parseFloat(facility.latitude), parseFloat(facility.longitude), 17)
      }
    }
  }
)

defineExpose({
  fitBoundsToFacilities,
  fitBoundsToAll,
  flyTo,
})

onMounted(() => {
  nextTick(() => {
    initMap()
  })
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<template>
  <div class="parkly-map-container" role="application" aria-label="Dhaka interactive parking map">
    <!-- Leaflet Canvas -->
    <div ref="mapContainerRef" class="map-canvas"></div>

    <!-- Map Controls (Floating Top Right) -->
    <div class="map-controls">
      <!-- Satellite / Street Toggle -->
      <button
        type="button"
        class="map-ctrl-btn mode-btn"
        :class="{ 'is-active': isSatellite }"
        :aria-label="isSatellite ? 'Satellite active' : 'Street active'"
        title="Toggle Satellite / Street View"
        @click="toggleLayer"
      >
        {{ isSatellite ? 'Sat' : 'Map' }}
      </button>

      <!-- Zoom In / Out -->
      <button type="button" class="map-ctrl-btn" aria-label="Zoom In" title="Zoom In" @click="zoomIn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
      </button>
      <button type="button" class="map-ctrl-btn" aria-label="Zoom Out" title="Zoom Out" @click="zoomOut">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
      </button>

      <!-- Reset View to Dhaka Central -->
      <button
        type="button"
        class="map-ctrl-btn"
        aria-label="Reset North"
        title="Reset View"
        @click="resetNorth"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 19 21 12 17 5 21 12 2" fill="var(--color-primary)" opacity="0.9"></polygon>
        </svg>
      </button>

      <!-- Locate Me -->
      <button
        type="button"
        class="map-ctrl-btn"
        aria-label="Center on my location"
        title="My Location"
        @click="goToMyLocation"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"></circle>
          <path d="M12 2v3m0 14v3M2 12h3m14 0h3"></path>
        </svg>
      </button>
    </div>

    <!-- Live Slot Syncing Shimmer -->
    <div v-if="isLoading" class="map-loading-overlay" aria-live="polite">
      <div class="loading-badge">
        <span class="loading-dot"></span>
        Syncing live slots...
      </div>
    </div>

    <!-- Floating Info Card on Pin Click -->
    <transition name="info-slide">
      <div v-if="selectedInfo" class="map-info-card" role="dialog" :aria-label="selectedInfo.name">
        <button type="button" class="info-close-btn" aria-label="Close details" @click="closeInfo">
          &times;
        </button>

        <div class="info-header">
          <h4 class="info-title">{{ selectedInfo.name }}</h4>
          <span
            class="info-status-pill"
            :class="selectedInfo.available ? 'is-available' : 'is-full'"
          >
            <span class="status-dot"></span>
            {{ selectedInfo.available ? `${selectedInfo.availableSlots || 'Slots'} Available` : 'Full' }}
          </span>
        </div>

        <p class="info-address">{{ selectedInfo.address_text }}</p>

        <div class="info-details-row">
          <span v-if="selectedInfo.rate" class="info-rate">{{ selectedInfo.rate }}</span>
          <span v-if="selectedInfo.distance" class="info-distance">{{ selectedInfo.distance }}</span>
        </div>

        <div v-if="selectedInfo.vehicleTypes.length > 0" class="info-vehicles">
          <span v-for="vt in selectedInfo.vehicleTypes" :key="vt" class="vehicle-tag">
            {{ vt }}
          </span>
        </div>

        <button
          type="button"
          class="info-action-btn"
          @click="$emit('view-details', selectedInfo.id)"
        >
          View Details & Book
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.parkly-map-container {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 480px;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
  background-color: #0b1220;
}

.map-canvas {
  width: 100%;
  height: 100%;
  z-index: 1;
}

/* Controls */
.map-controls {
  position: absolute;
  top: 12px;
  right: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  z-index: 1000;
}

.map-ctrl-btn {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-fast);
  backdrop-filter: blur(8px);
}

.map-ctrl-btn:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  background-color: var(--color-surface-subtle);
}

.mode-btn {
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.mode-btn.is-active {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  border-color: var(--color-primary);
}

/* Loading Overlay */
.map-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(11, 18, 32, 0.4);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1001;
  pointer-events: none;
}

.loading-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: var(--radius-pill, 999px);
  background-color: var(--color-surface);
  color: var(--color-text);
  font-size: 0.75rem;
  font-weight: 600;
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-elevation);
}

.loading-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
  animation: pulse-dot 1.2s infinite ease-in-out;
}

@keyframes pulse-dot {
  0%, 100% { opacity: 0.4; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1.2); }
}

/* Floating Info Card */
.map-info-card {
  position: absolute;
  bottom: 14px;
  left: 14px;
  right: 14px;
  max-width: 310px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 12px 14px;
  box-shadow: var(--shadow-halo);
  z-index: 1002;
  display: flex;
  flex-direction: column;
  gap: 6px;
  backdrop-filter: blur(12px);
}

.info-close-btn {
  position: absolute;
  top: 8px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 1.2rem;
  line-height: 1;
  color: var(--color-text-muted);
  cursor: pointer;
}

.info-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
  padding-right: 14px;
}

.info-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0;
  line-height: 1.25;
}

.info-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 6px;
  border-radius: var(--radius-pill, 999px);
  font-size: 0.625rem;
  font-weight: 700;
  white-space: nowrap;
}

.info-status-pill.is-available {
  background-color: rgba(22, 163, 74, 0.12);
  color: var(--color-success);
}

.info-status-pill.is-full {
  background-color: var(--color-danger-bg);
  color: var(--color-danger);
}

.status-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: currentColor;
}

.info-address {
  font-size: 0.72rem;
  color: var(--color-text-muted);
  margin: 0;
  line-height: 1.35;
}

.info-details-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.75rem;
}

.info-rate {
  font-weight: 700;
  color: var(--color-primary);
}

.info-distance {
  color: var(--color-text-subtle);
}

.info-vehicles {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.vehicle-tag {
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
  font-size: 0.625rem;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: var(--radius-pill, 999px);
}

.info-action-btn {
  width: 100%;
  height: 32px;
  margin-top: 2px;
  border-radius: var(--radius-pill, 999px);
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  border: none;
  font-size: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  cursor: pointer;
  transition: opacity var(--transition-fast);
}

.info-action-btn:hover {
  opacity: 0.92;
}

.info-slide-enter-active,
.info-slide-leave-active {
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.info-slide-enter-from,
.info-slide-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.96);
}
</style>

<!-- Leaflet Custom Icon Styles -->
<style>
.custom-parkly-leaflet-icon {
  background: transparent !important;
  border: none !important;
}

.parkly-map-pin {
  cursor: pointer;
  outline: none;
  position: relative;
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
  display: inline-block;
}

.parkly-map-pin:hover {
  transform: scale(1.15) translateY(-3px);
  z-index: 1000 !important;
}

.parkly-map-pin.is-selected {
  transform: scale(1.22) translateY(-4px);
  z-index: 1001 !important;
}

.pin-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background-color: #0F766E;
  color: #FFFFFF;
  padding: 3px 7px 3px 5px;
  border-radius: 999px;
  border: 1.5px solid #FFFFFF;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.45);
  font-family: var(--font-sans, system-ui);
  white-space: nowrap;
}

:root[data-theme='dark'] .pin-badge {
  background-color: #14B8A6;
  color: #0B1220;
  border-color: #111C2E;
}

.parkly-map-pin.is-selected .pin-badge {
  background-color: #14B8A6;
  color: #0B1220;
  box-shadow: 0 6px 16px rgba(20, 184, 166, 0.55);
  border-color: #FFFFFF;
}

.pin-badge.is-full {
  background-color: #64748B;
  opacity: 0.85;
}

.pin-letter {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 16px;
  height: 16px;
  background-color: rgba(255, 255, 255, 0.25);
  border-radius: 50%;
  font-size: 10px;
  font-weight: 800;
}

:root[data-theme='dark'] .pin-letter {
  background-color: rgba(11, 18, 32, 0.2);
}

.pin-price {
  font-size: 10px;
  font-weight: 700;
}

.pin-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  transform: translate(-50%, -50%);
  border-radius: 999px;
  background-color: #14B8A6;
  opacity: 0;
  pointer-events: none;
}

.parkly-map-pin.is-selected .pin-pulse {
  animation: marker-pulse 1.8s infinite cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

@keyframes marker-pulse {
  0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.6; }
  100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
}
</style>
