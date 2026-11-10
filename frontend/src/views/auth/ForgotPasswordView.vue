<script setup>
/**
 * ForgotPasswordView Component
 * Route: /forgot-password
 * Heading: "Reset Your Password"
 * Subtitle: "Enter your verified email to receive secure recovery instructions."
 *
 * Integrated into the Premium Split-Screen Authentication Layout.
 * Preserves email validation, generic security success response, and clean UI transitions.
 */
import { ref } from 'vue'
import AuthCard from '../../components/auth/AuthCard.vue'
import AppButton from '../../components/common/AppButton.vue'
import AppInput from '../../components/common/AppInput.vue'

const email = ref('')
const error = ref('')
const successMessage = ref('')
const isSubmitting = ref(false)

function validateForm() {
  error.value = ''
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

function handleForgotPassword() {
  if (!validateForm()) return

  isSubmitting.value = true
  successMessage.value = ''

  setTimeout(() => {
    isSubmitting.value = false
    // Generic security response to prevent user enumeration
    successMessage.value =
      'If an account exists with this email address, password reset instructions have been dispatched. Please check your inbox and spam folder.'
  }, 500)
}
</script>

<template>
  <AuthCard
    title="Reset Your Password"
    subtitle="Enter your email to receive secure recovery instructions."
  >
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
      Remembered your password?
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

.resend-action {
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
