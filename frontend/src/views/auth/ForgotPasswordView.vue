<script setup>
/**
 * ForgotPasswordView Component
 * Route: /forgot-password
 * Heading: "Reset Your Password"
 * Subtitle: "Enter your verified email to receive secure recovery instructions."
 */
import { ref } from 'vue'
import AuthCard from '../../components/auth/AuthCard.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const email = ref('')
const error = ref('')
const generalError = ref('')
const successMessage = ref('')
const isSubmitting = ref(false)

function validateForm() {
  error.value = ''
  generalError.value = ''

  if (!email.value.trim()) {
    error.value = 'Email address is required'
    return false
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    error.value = 'Please enter a valid email address'
    return false
  }
  return true
}

async function handleForgotPassword() {
  if (isSubmitting.value) return
  if (!validateForm()) return

  isSubmitting.value = true
  successMessage.value = ''
  generalError.value = ''

  try {
    const data = await authStore.forgotPassword(email.value.trim())
    // Generic security response from backend to prevent user enumeration
    successMessage.value = data?.message ||
      'If an account exists with this email address, password reset instructions have been sent.'
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = err.response?.data?.errors?.email?.[0] || 'Please provide a valid email address.'
    } else if (err.response?.status === 429) {
      generalError.value = err.response?.data?.message || 'Too many attempts. Please try again in a few minutes.'
    } else {
      generalError.value = 'Unable to send recovery email. Please check your internet connection.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <AuthCard
    title="Reset Your Password"
    subtitle="Enter your email to receive secure recovery instructions."
  >
    <!-- General Error Banner -->
    <div v-if="generalError" class="error-alert" role="alert">
      <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ generalError }}</span>
    </div>

    <!-- Success Banner -->
    <div v-if="successMessage" class="success-alert" role="status">
      <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
      </svg>
      <div>
        <p class="alert-title">Recovery Link Dispatched</p>
        <p class="alert-body">{{ successMessage }}</p>
      </div>
    </div>

    <!-- Forgot Password Form -->
    <form v-if="!successMessage" class="auth-form" @submit.prevent="handleForgotPassword" novalidate>
      <AppInput
        id="forgot-email"
        v-model="email"
        type="email"
        label="Email Address"
        placeholder="name@example.com"
        :error="error"
        hint="We will send a single-use password recovery link."
        required
      >
        <template #prefix>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </template>
      </AppInput>

      <AppButton
        type="submit"
        variant="primary"
        size="md"
        block
        :loading="isSubmitting"
      >
        Send Recovery Link
      </AppButton>
    </form>

    <div v-else class="resend-action">
      <AppButton variant="secondary" size="md" block @click="successMessage = ''">
        Send to another email
      </AppButton>
    </div>

    <!-- Card Footer -->
    <template #footer>
      Remember your credentials?
      <router-link to="/login" class="auth-link strong">
        Back to Sign In
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
  margin-bottom: var(--space-3);
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
  width: 20px;
  height: 20px;
  flex-shrink: 0;
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

.resend-action {
  margin-top: var(--space-2);
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
