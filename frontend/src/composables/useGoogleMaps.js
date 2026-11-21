// frontend/src/composables/useGoogleMaps.js
import { ref } from 'vue'

const isLoaded = ref(false)
const loadError = ref(null)

let loadPromise = null

/**
 * Lazily loads the Google Maps JavaScript API exactly once (singleton).
 * Subsequent calls return the same promise / resolved state.
 *
 * @returns {Promise<void>}
 */
function loadGoogleMaps() {
  if (loadPromise) {
    return loadPromise
  }

  loadPromise = new Promise((resolve) => {
    if (isLoaded.value) {
      resolve()
      return
    }

    const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || ''

    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&libraries=maps,marker,geometry`
    script.async = true
    script.defer = true

    script.addEventListener('load', () => {
      isLoaded.value = true
      loadError.value = null
      resolve()
    })

    script.addEventListener('error', () => {
      loadError.value = new Error('Failed to load Google Maps JavaScript API')
      resolve()
    })

    document.head.appendChild(script)
  })

  return loadPromise
}

/**
 * Returns sensible default map options for the Parkly platform.
 * Consumer can spread & override as needed:
 *   `new google.maps.Map(el, { ...getMapOptions(), zoom: 18 })`
 *
 * @param {Object} [overrides] - Optional overrides merged on top of defaults.
 * @returns {Object} Google Maps MapOptions
 */
function getMapOptions(overrides = {}) {
  return {
    mapTypeId: 'satellite',
    tilt: 45,
    heading: 0,
    zoom: 15,
    center: { lat: 23.8103, lng: 90.4125 },
    mapId: import.meta.env.VITE_GOOGLE_MAPS_MAP_ID || '',
    disableDefaultUI: true,
    zoomControl: true,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: false,
    gestureHandling: 'greedy',
    ...overrides,
  }
}

/**
 * Composable that lazily loads the Google Maps JavaScript API (singleton)
 * and exposes reactive loading state plus map utility helpers.
 */
export function useGoogleMaps() {
  return {
    isLoaded,
    loadError,
    loadGoogleMaps,
    getMapOptions,
  }
}
