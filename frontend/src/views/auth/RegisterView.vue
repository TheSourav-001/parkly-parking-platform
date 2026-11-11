<script setup>
/**
 * RegisterView Component
 * Route: /register
 * Heading: "Create Your Parkly Account"
 * Subtitle: "Join Parkly and make parking easier."
 */
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import AuthCard from '../../components/auth/AuthCard.vue'
import GoogleAuthButton from '../../components/auth/GoogleAuthButton.vue'
import PasswordStrengthBar from '../../components/auth/PasswordStrengthBar.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  agreeToTerms: false,
})

const errors = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  agreeToTerms: '',
  general: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isSubmitting = ref(false)
const notificationMessage = ref('')

function toggleShowPassword() {
  showPassword.value = !showPassword.value
}

function toggleShowConfirmPassword() {
  showConfirmPassword.value = !showConfirmPassword.value
}

function validateForm() {
  let isValid = true
  errors.name = ''
  errors.email = ''
  errors.phone = ''
  errors.password = ''
  errors.password_confirmation = ''
  errors.agreeToTerms = ''
  errors.general = ''

  if (!form.name.trim()) {
    errors.name = 'Full name is required'
    isValid = false
  } else if (form.name.trim().length < 2) {
    errors.name = 'Name must be at least 2 characters'
    isValid = false
  }

  if (!form.email.trim()) {
    errors.email = 'Email address is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  if (form.phone && !/^[+0-9\s\-()]{7,20}$/.test(form.phone)) {
    errors.phone = 'Please enter a valid phone number format'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }

  if (!form.password_confirmation) {
    errors.password_confirmation = 'Please confirm your password'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }

  if (!form.agreeToTerms) {
    errors.agreeToTerms = 'You must accept the Terms and Privacy Policy'
    isValid = false
  }

  return isValid
}

async function handleRegister() {
  if (isSubmitting.value) return
  if (!validateForm()) return

  isSubmitting.value = true
  errors.general = ''
  notificationMessage.value = ''

  try {
    await authStore.register({
      name: form.name.trim(),
      email: form.email.trim(),
      phone: form.phone.trim() || null,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })

    router.push('/')
  } catch (err) {
    if (err.response?.status === 422) {
      const responseErrors = err.response?.data?.errors
      if (responseErrors) {
        if (responseErrors.name) errors.name = responseErrors.name[0]
        if (responseErrors.email) errors.email = responseErrors.email[0]
        if (responseErrors.phone) errors.phone = responseErrors.phone[0]
        if (responseErrors.password) errors.password = responseErrors.password[0]
        if (responseErrors.password_confirmation) errors.password_confirmation = responseErrors.password_confirmation[0]
      } else {
        errors.general = err.response?.data?.message || 'Validation failed. Please verify your details.'
      }
    } else {
      errors.general = 'Unable to complete registration. Please check your network connection.'
    }
  } finally {
    isSubmitting.value = false
  }
}

function handleGoogleSignUp() {
  const apiBase = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'
  window.location.href = `${apiBase}/auth/google/redirect`
}
</script>

<template>
  <AuthCard
    title="Create Your Parkly Account"
    subtitle="Join Parkly and make parking easier."
  >
    <!-- General Error Alert -->
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

    <!-- Registration Form -->
    <form class="auth-form" @submit.prevent="handleRegister" novalidate>
      <div class="form-row-grid">
        <AppInput
          id="reg-name"
          v-model="form.name"
          type="text"
          label="Full Name"
          placeholder="Jane Doe"
          :error="errors.name"
          required
        >
          <template #prefix>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
          </template>
        </AppInput>

        <AppInput
          id="reg-phone"
          v-model="form.phone"
          type="tel"
          label="Phone (Optional)"
          placeholder="+1 (555) 000-0000"
          :error="errors.phone"
        >
          <template #prefix>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
          </template>
        </AppInput>
      </div>

      <AppInput
        id="reg-email"
        v-model="form.email"
        type="email"
        label="Email Address"
        placeholder="jane@example.com"
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

      <!-- Row 3: Password and Confirm Password side-by-side in 2-column grid on desktop -->
      <div class="form-row-grid">
        <AppInput
          id="reg-password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          label="Password"
          placeholder="At least 8 chars"
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

        <AppInput
          id="reg-confirm-password"
          v-model="form.password_confirmation"
          :type="showConfirmPassword ? 'text' : 'password'"
          label="Confirm Password"
          placeholder="Re-enter password"
          :error="errors.password_confirmation"
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
              :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
              @click="toggleShowConfirmPassword"
            >
              <svg v-if="showConfirmPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
      </div>

      <!-- Password Strength Indicator -->
      <PasswordStrengthBar :password="form.password" />

      <!-- Terms & Privacy Agreement -->
      <div class="terms-group">
        <label class="checkbox-label">
          <input
            v-model="form.agreeToTerms"
            type="checkbox"
            class="checkbox-input"
          />
          <span class="terms-text">
            I agree to the
            <a href="#terms" target="_blank" class="auth-link">Terms</a>
            and
            <a href="#privacy" target="_blank" class="auth-link">Privacy Policy</a>.
          </span>
        </label>
        <p v-if="errors.agreeToTerms" class="error-text" role="alert">
          {{ errors.agreeToTerms }}
        </p>
      </div>

      <!-- Submit Button -->
      <AppButton
        type="submit"
        variant="primary"
        size="md"
        block
        :loading="isSubmitting"
      >
        Create Account
      </AppButton>
    </form>

    <!-- Divider: OR CONTINUE WITH -->
    <div class="auth-divider">
      <span class="divider-text">OR CONTINUE WITH</span>
    </div>

    <!-- Google Sign Up -->
    <GoogleAuthButton text="Sign up with Google" @click="handleGoogleSignUp" />

    <!-- Card Footer -->
    <template #footer>
      Already have an account?
      <router-link to="/login" class="auth-link strong">
        Sign In
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

.form-row-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-3);
}

@media (max-width: 640px) {
  .form-row-grid {
    grid-template-columns: 1fr;
    gap: var(--space-3);
  }
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

.terms-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-top: -2px;
  margin-bottom: 2px;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: var(--space-2);
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  cursor: pointer;
  user-select: none;
  line-height: 1.35;
}

.checkbox-input {
  accent-color: var(--color-primary);
  width: 14px;
  height: 14px;
  margin-top: 1px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  flex-shrink: 0;
}

.terms-text {
  color: var(--color-text-secondary);
}

.error-text {
  font-size: 11px;
  color: var(--color-error);
  margin: 0;
  padding-left: 22px;
  line-height: 1.2;
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
