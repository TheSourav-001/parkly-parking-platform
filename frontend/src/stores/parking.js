import { defineStore } from 'pinia'
import api from '../services/api'

export const useParkingStore = defineStore('parking', {
  state: () => ({
    facilities: [],
    searchResults: [],
    selectedFacilityId: null,
    isLoading: false,
    searchLoading: false,
    error: null,
    searchQuery: {
      location: '',
      vehicle: '',
      duration: '',
      lat: null,
      lng: null,
    },
    aiQuery: '',
    aiResponse: null,
    aiLoading: false,
    viewMode: 'list',
  }),

  getters: {
    activeFacilities: (state) =>
      state.facilities.filter((f) => f.is_active),

    selectedFacility: (state) =>
      state.facilities.find((f) => f.id === state.selectedFacilityId) ?? null,

    displayFacilities: (state) =>
      state.searchResults.length > 0 ? state.searchResults : state.facilities,

    hasSearched: (state) => state.searchResults.length > 0,
  },

  actions: {
    async fetchAllFacilities() {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get('/api/v1/facilities')
        this.facilities = response.data?.data || response.data || []
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.isLoading = false
      }
    },

    async searchFacilities(params) {
      this.searchLoading = true
      this.error = null
      this.searchQuery = { ...this.searchQuery, ...params }

      const queryParams = {}
      if (params.location) queryParams.q = params.location
      if (params.vehicle) queryParams.vehicle_type = params.vehicle
      if (params.duration) queryParams.duration = params.duration
      if (params.lat != null) queryParams.lat = params.lat
      if (params.lng != null) queryParams.lng = params.lng

      try {
        const response = await api.get('/api/v1/facilities', {
          params: queryParams,
        })
        this.searchResults = response.data?.data || response.data || []
      } catch (err) {
        this.error = err.response?.data?.message || err.message
      } finally {
        this.searchLoading = false
      }
    },

    async searchByAI(query) {
      this.aiLoading = true
      this.aiQuery = query
      this.aiResponse = null
      this.error = null

      try {
        const { data } = await api.post('/api/v1/ai/search', { query })
        this.aiResponse = data

        if (data.facility_ids && data.facility_ids.length > 0) {
          const ids = new Set(data.facility_ids)
          this.searchResults = this.facilities.filter((f) => ids.has(f.id))
        }
      } catch (err) {
        this.aiResponse = {
          error: err.response?.data?.message || err.message,
        }
      } finally {
        this.aiLoading = false
      }
    },

    selectFacility(id) {
      this.selectedFacilityId = id
    },

    clearSearch() {
      this.searchResults = []
      this.searchQuery = {
        location: '',
        vehicle: '',
        duration: '',
        lat: null,
        lng: null,
      }
      this.aiQuery = ''
      this.aiResponse = null
    },

    toggleViewMode() {
      this.viewMode = this.viewMode === 'list' ? 'map' : 'list'
    },
  },
})
