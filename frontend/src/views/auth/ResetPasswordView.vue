<script setup>
/**
 * ResetPasswordView Component
 * Route: /reset-password?token=...&email=...
 * Heading: "Reset Password"
 * Subtitle: "Choose a strong password to protect your Parkly account."
 */
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthCard from '../../components/auth/AuthCard.vue'
import PasswordStrengthBar from '../../components/auth/PasswordStrengthBar.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'
import { useAuthStore } from '../../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const token = ref('')
const email = ref('')

const form = reactive({
  password: '',
  password_confirmation: '',
})

const errors = reactive({
  password: '',
  password_confirmation: '',
  general: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isSubmitting = ref(false)
const isSuccess = ref(false)

onMounted(() => {
  token.value = (route.query.token || '').toString()
  email.value = (route.query.email || '').toString()

  if (!token.value || !email.value) {
    errors.general = 'The password reset link is invalid or incomplete. Please request a new recovery link.'
  }
})

function toggleShowPassword() {
  showPassword.value = !showPassword.value
}

function toggleShowConfirmPassword() {
  showConfirmPassword.value = !showConfirmPassword.value
}

function validateForm() {
  let isValid = true
  errors.password = ''
  errors.password_confirmation = ''
  errors.general = ''

  if (!token.value || !email.value) {
    errors.general = 'Missing reset token or email address.'
    return false
  }

  if (!form.password) {
    errors.password = 'New password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }

  if (!form.password_confirmation) {
    errors.password_confirmation = 'Please confirm your new password'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

async function handleResetPassword() {
  if (isSubmitting.value) return
  if (!validateForm()) return

  isSubmitting.value = true
  errors.general = ''

  try {
    await authStore.resetPassword({
      token: token.value,
      email: email.value,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })

    isSuccess.value = true

    // Remove sensitive query parameters from the visible browser URL
    router.replace({ path: '/reset-password', query: {} })
  } catch (err) {
    if (err.response?.status === 422) {
      const responseErrors = err.response?.data?.errors
      if (responseErrors?.password) {
        errors.password = responseErrors.password[0]
      } else {
        errors.general = err.response?.data?.message || 'The password reset link is invalid or has expired.'
      }
    } else {
      errors.general = 'Unable to reset password. Please verify your connection or request a new link.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthCard
    title="Reset Password"
    subtitle="Choose a strong password to protect your Parkly account."
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

    <!-- Success Banner -->
    <div v-if="isSuccess" class="success-alert" role="status">
      <svg class="alert-icon success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
      <div>
        <p class="alert-title">Password Successfully Reset</p>
        <p class="alert-body">Your account credentials have been updated securely. You can now sign in with your new password.</p>
        <router-link to="/login">
          <AppButton variant="primary" size="md" block class="mt-4">
            Proceed to Sign In
          </AppButton>
        </router-link>
      </div>
    </div>

    <!-- Reset Form -->
    <form v-if="!isSuccess" class="auth-form" @submit.prevent="handleResetPassword" novalidate>
      <AppInput
        id="reset-password"
        v-model="form.password"
        :type="showPassword ? 'text' : 'password'"
        label="New Password"
        placeholder="At least 8 characters"
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
        id="reset-confirm-password"
        v-model="form.password_confirmation"
        :type="showConfirmPassword ? 'text' : 'password'"
        label="Confirm New Password"
        placeholder="Re-enter new password"
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

      <!-- Password Strength Indicator -->
      <PasswordStrengthBar :password="form.password" />

      <!-- Submit Button -->
      <AppButton
        type="submit"
        variant="primary"
        size="md"
        block
        :loading="isSubmitting"
        :disabled="Boolean(!token || !email)"
      >
        Save New Password
      </AppButton>
    </form>

    <!-- Card Footer -->
    <template #footer>
      Back to
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

.success-alert {
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  padding: var(--space-4);
  background-color: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.25);
  border-radius: var(--radius-md);
  margin-bottom: var(--space-4);
}

.alert-icon {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  color: #ef4444;
}

.success-icon {
  width: 20px;
  height: 20px;
  color: var(--color-success);
  margin-top: 2px;
}

.alert-title {
  font-weight: var(--font-weight-semibold);
  font-size: var(--font-size-sm);
  color: var(--color-text);
  margin: 0 0 var(--space-1) 0;
}

.alert-body {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  line-height: 1.5;
  margin: 0;
}

.mt-4 {
  margin-top: var(--space-4);
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
</style>
