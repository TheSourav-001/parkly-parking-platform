<script setup>
/**
 * LoginView Component
 * Route: /login
 * Heading: "Welcome Back"
 * Subtitle: "Sign in to continue your parking journey."
 */
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AuthCard from '../../components/auth/AuthCard.vue'
import GoogleAuthButton from '../../components/auth/GoogleAuthButton.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = reactive({
  email: '',
  password: '',
  general: '',
})

const showPassword = ref(false)
const isSubmitting = ref(false)
const notificationMessage = ref('')

onMounted(() => {
  // Check for Google OAuth callback failure
  if (route.query.google === 'failed') {
    errors.general = 'Google sign-in was canceled or could not be verified. Please try again.'
  }
})

function toggleShowPassword() {
  showPassword.value = !showPassword.value
}

function validateForm() {
  let isValid = true
  errors.email = ''
  errors.password = ''
  errors.general = ''

  if (!form.email.trim()) {
    errors.email = 'Email address is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }

  return isValid
}

async function handleLogin() {
  if (isSubmitting.value) return
  if (!validateForm()) return

  isSubmitting.value = true
  errors.general = ''
  notificationMessage.value = ''

  try {
    await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember,
    })

    router.push('/')
  } catch (err) {
    if (err.response?.status === 422 || err.response?.status === 401) {
      const responseErrors = err.response?.data?.errors
      if (responseErrors) {
        if (responseErrors.email) errors.email = responseErrors.email[0]
        if (responseErrors.password) errors.password = responseErrors.password[0]
      } else {
        errors.general = err.response?.data?.message || 'Invalid email or password.'
      }
    } else if (err.response?.status === 403) {
      errors.general = err.response?.data?.message || 'Your account is inactive. Please contact support.'
    } else if (err.response?.status === 429) {
      errors.general = err.response?.data?.message || 'Too many attempts. Please try again later.'
    } else {
      errors.general = 'Unable to connect to the authentication server. Please check your connection.'
    }
  } finally {
    isSubmitting.value = false
  }
}

function handleGoogleSignIn() {
  const apiBase = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'
  window.location.href = `${apiBase}/auth/google/redirect`
}
</script>

<template>
  <AuthCard
    title="Welcome Back"
    subtitle="Sign in to continue your parking journey."
  >
    <!-- General Error Banner -->
    <div v-if="errors.general" class="error-alert" role="alert">
      <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ errors.general }}</span>
    </div>

    <!-- Notification Banner -->
    <div v-if="notificationMessage" class="info-alert" role="status">
      <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <span>{{ notificationMessage }}</span>
    </div>

    <!-- Login Form -->
    <form class="auth-form" @submit.prevent="handleLogin" novalidate>
      <AppInput
        id="login-email"
        v-model="form.email"
        type="email"
        label="Email Address"
        placeholder="name@example.com"
        :error="errors.email"
        required
      >
        <template #prefix>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </template>
      </AppInput>

      <AppInput
        id="login-password"
        v-model="form.password"
        :type="showPassword ? 'text' : 'password'"
        label="Password"
        placeholder="Enter your password"
        :error="errors.password"
        required
      >
        <template #prefix>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
        </template>
        <template #suffix>
          <button
            type="button"
            class="toggle-pwd-btn"
            :aria-label="showPassword ? 'Hide password' : 'Show password'"
            @click="toggleShowPassword"
          >
            <svg v-if="showPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
              <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
            <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>
        </template>
      </AppInput>

      <!-- Remember Me & Forgot Password Row -->
      <div class="form-row-between">
        <label class="checkbox-label">
          <input
            v-model="form.remember"
            type="checkbox"
            class="checkbox-input"
          />
          <span>Remember Me</span>
        </label>
        <router-link to="/forgot-password" class="auth-link">
          Forgot Password?
        </router-link>
      </div>

      <!-- Submit Button -->
      <AppButton
        type="submit"
        variant="primary"
        size="md"
        block
        :loading="isSubmitting"
      >
        Sign In
      </AppButton>
    </form>

    <!-- Divider: OR CONTINUE WITH -->
    <div class="auth-divider">
      <span class="divider-text">OR CONTINUE WITH</span>
    </div>

    <!-- Google Sign In -->
    <GoogleAuthButton text="Continue with Google" @click="handleGoogleSignIn" />

    <!-- Card Footer -->
    <template #footer>
      Don't have an account?
      <router-link to="/register" class="auth-link strong">
        Create Account
      </router-link>
    </template>
  </AuthCard>
</template>

<style scoped>
.auth-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.error-alert {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3);
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.25);
  color: #ef4444;
  border-radius: var(--radius-md);
  font-size: var(--font-size-xs);
  line-height: 1.4;
  margin-bottom: var(--space-2);
}

.toggle-pwd-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2px;
  border-radius: var(--radius-sm);
  transition: color var(--transition-fast);
}

.toggle-pwd-btn:hover {
  color: var(--color-primary);
}

.form-row-between {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: -2px;
  margin-bottom: 2px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  cursor: pointer;
  user-select: none;
}

.checkbox-input {
  accent-color: var(--color-primary);
  width: 14px;
  height: 14px;
  border-radius: var(--radius-sm);
  cursor: pointer;
}

.auth-divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: var(--space-3) 0;
}

.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--color-border);
}

.divider-text {
  padding: 0 var(--space-2);
  font-size: 10px;
  font-weight: var(--font-weight-bold);
  letter-spacing: 0.08em;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.auth-link {
  font-size: var(--font-size-xs);
  color: var(--color-primary);
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  transition: opacity var(--transition-fast);
}

.auth-link:hover {
  opacity: 0.85;
  text-decoration: underline;
}

.auth-link.strong {
  font-weight: var(--font-weight-semibold);
  margin-left: 2px;
}

.info-alert {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  color: var(--color-text-secondary);
  border-radius: var(--radius-md);
  font-size: var(--font-size-xs);
  margin-bottom: var(--space-2);
}

.alert-icon {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  color: var(--color-primary);
}
</style>
