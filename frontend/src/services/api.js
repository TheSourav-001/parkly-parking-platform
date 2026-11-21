import axios from 'axios'

/**
 * Centralized Axios API instance for Parkly.
 * Configured for Laravel Sanctum SPA authentication.
 */
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

// CSRF initialization
let csrfPromise = null

export async function ensureCsrf() {
  if (!csrfPromise) {
    csrfPromise = api.get('/sanctum/csrf-cookie').catch((err) => {
      csrfPromise = null
      throw err
    })
  }

  return csrfPromise
}

// Reset CSRF tracker
export function resetCsrf() {
  csrfPromise = null
}

/**
 * Helper for Location and Vehicle Type APIs.
 */
async function request(endpoint) {
  const response = await api.get(`/api${endpoint}`)
  const result = response.data

  if (!response.data || !response.data.success) {
    throw new Error(result?.message || 'Something went wrong.')
  }

  return result.data
}

// Location APIs
export const getDivisions = () => {
  return request('/locations?type=division')
}

export const getDistricts = (divisionId) => {
  return request(`/locations?type=district&parent_id=${encodeURIComponent(divisionId)}`)
}

export const getUpazilas = (districtId) => {
  return request(`/locations?type=thana&parent_id=${encodeURIComponent(districtId)}`)
}

// Vehicle Type API
export const getVehicleTypes = () => {
  return request('/vehicle-types')
}

// Default Axios export for Authentication and other API requests
export default api
