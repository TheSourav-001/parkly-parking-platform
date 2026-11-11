import { defineStore } from 'pinia'
import api, { ensureCsrf, resetCsrf } from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    isInitialized: false,
    isLoading: false,
    error: null,
  }),

  getters: {
    userName: (state) => state.user?.name || '',
    userEmail: (state) => state.user?.email || '',
    userRole: (state) => state.user?.role || 'driver',
    userAvatar: (state) => state.user?.avatar || null,
  },

  actions: {
    /**
     * Initialize auth state on app startup by verifying backend session with /me.
     */
    async initializeAuth() {
      if (this.isInitialized) return

      this.isLoading = true
      try {
        await this.fetchUser()
      } catch (err) {
        // Normal unauthenticated state on 401
        this.user = null
        this.isAuthenticated = false
      } finally {
        this.isInitialized = true
        this.isLoading = false
      }
    },

    /**
     * Retrieve the currently authenticated user from /api/v1/auth/me.
     */
    async fetchUser() {
      try {
        const response = await api.get('/api/v1/auth/me')
        if (response.data?.data?.user) {
          this.user = response.data.data.user
          this.isAuthenticated = true
          this.error = null
          return this.user
        } else {
          this.user = null
          this.isAuthenticated = false
          return null
        }
      } catch (err) {
        this.user = null
        this.isAuthenticated = false
        throw err
      }
    },

    /**
     * Register a new user account.
     */
    async register(payload) {
      this.isLoading = true
      this.error = null

      try {
        await ensureCsrf()
        const response = await api.post('/api/v1/auth/register', {
          name: payload.name,
          email: payload.email,
          phone: payload.phone || null,
          password: payload.password,
          password_confirmation: payload.password_confirmation,
          role: payload.role || 'driver',
        })

        // If the newly registered user is active (Driver), verify and sync state via /me
        if (response.data?.data?.user?.is_active) {
          await this.fetchUser()
        }
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Registration failed.'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Login using email, password, and optional remember me.
     */
    async login(payload) {
      this.isLoading = true
      this.error = null

      try {
        await ensureCsrf()
        const response = await api.post('/api/v1/auth/login', {
          email: payload.email,
          password: payload.password,
          remember: Boolean(payload.remember),
        })

        // Verify and sync state via /me
        await this.fetchUser()
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Invalid credentials.'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Logout user and invalidate backend session.
     */
    async logout() {
      this.isLoading = true
      try {
        await api.post('/api/v1/auth/logout')
      } catch (err) {
        // Proceed with frontend state cleanup even if session was already expired
      } finally {
        this.user = null
        this.isAuthenticated = false
        this.error = null
        this.isLoading = false
        resetCsrf()
      }
    },

    /**
     * Request password reset link.
     */
    async forgotPassword(email) {
      this.isLoading = true
      this.error = null

      try {
        await ensureCsrf()
        const response = await api.post('/api/v1/auth/forgot-password', { email })
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to dispatch password recovery email.'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Reset password with token.
     */
    async resetPassword(payload) {
      this.isLoading = true
      this.error = null

      try {
        await ensureCsrf()
        const response = await api.post('/api/v1/auth/reset-password', {
          token: payload.token,
          email: payload.email,
          password: payload.password,
          password_confirmation: payload.password_confirmation,
        })
        return response.data
      } catch (err) {
        this.error = err.response?.data?.message || 'The password reset link is invalid or has expired.'
        throw err
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Handle post-Google OAuth callback verification via /me.
     */
    async handleGoogleCallback() {
      this.isLoading = true
      try {
        await this.fetchUser()
        return true
      } catch (err) {
        this.user = null
        this.isAuthenticated = false
        return false
      } finally {
        this.isLoading = false
      }
    },
  },
})

