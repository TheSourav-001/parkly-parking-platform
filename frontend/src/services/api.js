import axios from 'axios'

/**
 * Centralized Axios API instance for Parkly
 * Configured specifically for Laravel Sanctum SPA cookie-based session authentication.
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

// Track CSRF initialization state to avoid unnecessary redundant calls
let csrfPromise = null

/**
 * Ensures a valid Sanctum CSRF cookie is present before state-changing requests.
 */
export async function ensureCsrf() {
  if (!csrfPromise) {
    csrfPromise = api.get('/sanctum/csrf-cookie').catch((err) => {
      csrfPromise = null
      throw err
    })
  }
  return csrfPromise
}

/**
 * Reset CSRF promise tracker (e.g. after logout or session invalidation)
 */
export function resetCsrf() {
  csrfPromise = null
}

export default api

