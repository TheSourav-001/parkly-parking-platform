<script setup>
/**
 * ResetPasswordView Component
 * Route: /reset-password
 * Heading: "Reset Password"
 * Subtitle: "Choose a strong password to protect your Parkly account."
 *
 * Integrated into the Premium Split-Screen Authentication Layout.
 * Preserves new password validation, password confirmation match,
 * strength indicator, and visibility toggles.
 */
import { ref, reactive } from 'vue'
import AuthCard from '../../components/auth/AuthCard.vue'
import PasswordStrengthBar from '../../components/auth/PasswordStrengthBar.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'

const form = reactive({
  password: '',
  password_confirmation: '',
})

const errors = reactive({
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isSubmitting = ref(false)
const isSuccess = ref(false)

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

function handleResetPassword() {
  if (!validateForm()) return

  isSubmitting.value = true

  setTimeout(() => {
    isSubmitting.value = false
    isSuccess.value = true
  }, 500)
}
</script>

<template>
  <AuthCard
    title="Reset Password"
    subtitle="Choose a strong password to protect your Parkly account."
  >
    <!-- Success Banner -->
    <div v-if="isSuccess" class="success-alert" role="status">
      <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
      <div>
        <p class="alert-title">Password Successfully Reset</p>
        <p class="alert-body">Your account credentials have been updated securely.</p>
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
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <svg v-if="showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
              <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
            <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>
        </template>
      </AppInput>

      <!-- Password Strength Indicator -->
      <PasswordStrengthBar :password="form.password" />

      <!-- Confirm Password Field -->
      <AppInput
        id="reset-confirm-password"
        v-model="form.password_confirmation"
        :type="showConfirmPassword ? 'text' : 'password'"
        label="Confirm New Password"
        placeholder="Re-enter your new password"
        :error="errors.password_confirmation"
        required
      >
        <template #prefix>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <svg v-if="showConfirmPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
              <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
            <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
          </button>
        </template>
      </AppInput>

      <!-- Submit Button -->
      <AppButton
        type="submit"
        variant="primary"
        size="md"
        block
        :loading="isSubmitting"
      >
        Reset Password
      </AppButton>
    </form>

    <!-- Success Redirection Option -->
    <div v-else class="success-action">
      <router-link to="/login">
        <AppButton variant="primary" size="md" block>
          Proceed to Sign In
        </AppButton>
      </router-link>
    </div>

    <!-- Card Footer -->
    <template #footer>
      Remember your old password?
      <router-link to="/login" class="auth-link strong">
        Back to Login
      </router-link>
    </template>
  </AuthCard>
</template>

<style scoped>
.auth-form {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.toggle-pwd-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--color-text-muted);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: color var(--transition-fast);
}

.toggle-pwd-btn:hover {
  color: var(--color-text);
}

.success-alert {
  display: flex;
  gap: var(--space-3);
  padding: var(--space-4);
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-success);
  border-radius: var(--radius-md);
  color: var(--color-text);
  line-height: var(--line-height-normal);
}

.alert-icon {
  width: 20px;
  height: 20px;
  color: var(--color-success);
  flex-shrink: 0;
  margin-top: 2px;
}

.alert-title {
  font-weight: var(--font-weight-semibold);
  font-size: var(--font-size-sm);
  color: var(--color-text);
  margin-bottom: var(--space-1);
}

.alert-body {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}

.success-action {
  margin-top: var(--space-2);
}

.auth-link {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  transition: color var(--transition-fast);
}

.auth-link:hover {
  text-decoration: underline;
}

.auth-link.strong {
  font-weight: var(--font-weight-semibold);
  margin-left: var(--space-1);
}
</style>
