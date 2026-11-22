/**
 * Single source of truth for Dhaka-centered search filters
 * Supports Direct Search and Ask Parkly AI.
 * Cascades: District -> Thana -> Area (Division removed as product is Dhaka-only).
 * Syncs with URL search parameters and localStorage.
 */
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useSearchFiltersStore = defineStore('searchFilters', () => {
  // Cascading location state: District -> Thana -> Area
  const selectedDistrict = ref('dhaka-district')
  const selectedThana = ref('')
  const selectedArea = ref('')

  // Vehicle, Duration, Pricing
  const selectedVehicle = ref('car')
  const durationHours = ref(2)
  const maxPrice = ref(200)
  const onlyAvailable = ref(false)
  const sortBy = ref('nearest') // 'nearest' | 'cheapest' | 'slots'

  // Coordinates / GPS
  const userLat = ref(null)
  const userLng = ref(null)
  const gpsRadiusKm = ref(3)
  const isGpsActive = ref(false)
  const isDetectingGps = ref(false)
  const gpsStatus = ref('idle') // 'idle' | 'requesting' | 'success' | 'denied' | 'unavailable' | 'timeout'
  const gpsMessage = ref('')

  // Raw text search / Combobox query
  const searchQuery = ref('')

  // AI intent state
  const lastAiUnderstood = ref(null)

  // Initialize from URL / LocalStorage
  function initFromUrlAndStorage() {
    if (typeof window === 'undefined') return

    const params = new URLSearchParams(window.location.search)
    if (params.has('district')) selectedDistrict.value = params.get('district')
    if (params.has('thana')) selectedThana.value = params.get('thana')
    if (params.has('area')) selectedArea.value = params.get('area')
    if (params.has('vehicle')) selectedVehicle.value = params.get('vehicle')
    if (params.has('duration')) durationHours.value = Number(params.get('duration')) || 2
    if (params.has('max_price')) maxPrice.value = Number(params.get('max_price')) || 200
    if (params.has('available')) onlyAvailable.value = params.get('available') === 'true'
    if (params.has('sort')) sortBy.value = params.get('sort')

    if (!params.has('thana')) {
      try {
        const saved = localStorage.getItem('parkly_search_filters')
        if (saved) {
          const parsed = JSON.parse(saved)
          if (parsed.thana) selectedThana.value = parsed.thana
          if (parsed.area) selectedArea.value = parsed.area
          if (parsed.vehicle) selectedVehicle.value = parsed.vehicle
        }
      } catch (e) {
        // Ignore storage errors gracefully
      }
    }
  }

  // Sync to URL & Storage
  function syncState() {
    if (typeof window === 'undefined') return
    const params = new URLSearchParams()
    if (selectedDistrict.value && selectedDistrict.value !== 'dhaka-district') {
      params.set('district', selectedDistrict.value)
    }
    if (selectedThana.value) params.set('thana', selectedThana.value)
    if (selectedArea.value) params.set('area', selectedArea.value)
    if (selectedVehicle.value && selectedVehicle.value !== 'car') {
      params.set('vehicle', selectedVehicle.value)
    }
    if (durationHours.value && durationHours.value !== 2) {
      params.set('duration', durationHours.value.toString())
    }
    if (onlyAvailable.value) params.set('available', 'true')
    if (sortBy.value !== 'nearest') params.set('sort', sortBy.value)

    const queryString = params.toString()
    const newUrl = `${window.location.pathname}${queryString ? '?' + queryString : ''}`
    window.history.replaceState({}, '', newUrl)

    try {
      localStorage.setItem('parkly_search_filters', JSON.stringify({
        district: selectedDistrict.value,
        thana: selectedThana.value,
        area: selectedArea.value,
        vehicle: selectedVehicle.value,
      }))
    } catch (e) {
      // Ignore storage errors
    }
  }

  // Cascading resets
  function setDistrict(districtSlug) {
    selectedDistrict.value = districtSlug
    selectedThana.value = ''
    selectedArea.value = ''
    syncState()
  }

  function setThana(thanaSlug) {
    selectedThana.value = thanaSlug
    selectedArea.value = ''
    syncState()
  }

  function setArea(areaSlug) {
    selectedArea.value = areaSlug
    syncState()
  }

  function clearAll() {
    selectedDistrict.value = 'dhaka-district'
    selectedThana.value = ''
    selectedArea.value = ''
    selectedVehicle.value = 'car'
    durationHours.value = 2
    maxPrice.value = 200
    onlyAvailable.value = false
    sortBy.value = 'nearest'
    userLat.value = null
    userLng.value = null
    isGpsActive.value = false
    isDetectingGps.value = false
    gpsStatus.value = 'idle'
    gpsMessage.value = ''
    searchQuery.value = ''
    lastAiUnderstood.value = null
    syncState()
  }

  // GPS on-click only with exhaustive state handling
  function requestGps(onSuccessCallback) {
    if (typeof window === 'undefined') return

    if (!navigator.geolocation) {
      gpsStatus.value = 'unavailable'
      gpsMessage.value = 'Geolocation is not supported by your browser.'
      return
    }

    isDetectingGps.value = true
    gpsStatus.value = 'requesting'
    gpsMessage.value = 'Requesting current coordinates...'

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        isDetectingGps.value = false
        const lat = pos.coords.latitude
        const lng = pos.coords.longitude

        // Validate Dhaka Metropolitan Bounding Box (~23.65 to 23.95 lat, ~90.25 to 90.55 lng)
        const isInsideDhaka = lat >= 23.65 && lat <= 23.95 && lng >= 90.25 && lng <= 90.55

        userLat.value = lat
        userLng.value = lng
        isGpsActive.value = true
        gpsStatus.value = 'success'

        if (!isInsideDhaka) {
          gpsMessage.value = 'Coordinates detected outside Dhaka metro. Please choose District, Thana and Area manually.'
        } else {
          gpsMessage.value = 'Location located. Showing registered parking within Dhaka.'
        }

        syncState()
        if (typeof onSuccessCallback === 'function') {
          onSuccessCallback({ lat, lng, isInsideDhaka })
        }
      },
      (err) => {
        isDetectingGps.value = false
        isGpsActive.value = false
        console.error('Parkly Geolocation error:', err)

        if (err.code === 1) { // PERMISSION_DENIED
          gpsStatus.value = 'denied'
          gpsMessage.value = 'Location permission denied. Choose District, Thana and Area manually.'
        } else if (err.code === 2) { // POSITION_UNAVAILABLE
          gpsStatus.value = 'unavailable'
          gpsMessage.value = 'Position unavailable. Choose District, Thana and Area manually.'
        } else if (err.code === 3) { // TIMEOUT
          gpsStatus.value = 'timeout'
          gpsMessage.value = 'Location request timed out. Please try again or select manually.'
        } else {
          gpsStatus.value = 'unavailable'
          gpsMessage.value = 'Unable to acquire location. Choose District, Thana and Area manually.'
        }
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000,
      }
    )
  }

  function applyAiIntent(intent) {
    lastAiUnderstood.value = intent
    if (intent.vehicle_type) selectedVehicle.value = intent.vehicle_type
    if (intent.duration) durationHours.value = intent.duration
    if (intent.budget) maxPrice.value = intent.budget
    if (intent.location) {
      selectedThana.value = intent.location.toLowerCase()
    }
    syncState()
  }

  const activeFiltersCount = computed(() => {
    let count = 0
    if (selectedThana.value) count++
    if (selectedArea.value) count++
    if (selectedVehicle.value !== 'car') count++
    if (onlyAvailable.value) count++
    if (isGpsActive.value) count++
    return count
  })

  return {
    selectedDistrict,
    selectedThana,
    selectedArea,
    selectedVehicle,
    durationHours,
    maxPrice,
    onlyAvailable,
    sortBy,
    userLat,
    userLng,
    gpsRadiusKm,
    isGpsActive,
    isDetectingGps,
    gpsStatus,
    gpsMessage,
    searchQuery,
    lastAiUnderstood,
    activeFiltersCount,
    initFromUrlAndStorage,
    setDistrict,
    setThana,
    setArea,
    clearAll,
    requestGps,
    applyAiIntent,
    syncState,
  }
})
