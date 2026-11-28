<script setup>
/**
 * RouteErrorBoundary Component
 * Catches uncaught runtime errors during route component rendering or unmounting.
 * Prevents the application from freezing or staying blank during navigation glitches.
 */
import { ref, onErrorCaptured } from 'vue'

const hasError = ref(false)
const errorMessage = ref('')

onErrorCaptured((err, instance, info) => {
  console.error('[Parkly RouteErrorBoundary caught error]:', err, info)
  hasError.value = true
  errorMessage.value = err?.message || 'An unexpected navigation error occurred'
  return false // Stop error propagation to root
})

function handleRetry() {
  hasError.value = false
  window.location.reload()
}

function handleGoHome() {
  hasError.value = false
  window.location.href = '/'
}
</script>

<template>
  <div v-if="hasError" class="route-error-boundary" role="alert" aria-live="assertive">
    <div class="boundary-card">
      <span class="boundary-eyebrow">NAVIGATION NOTICE</span>
      <h2 class="boundary-title">Something went wrong loading this section.</h2>
      <p class="boundary-desc">
        A temporary display or unmount error was captured. You can reload the view or return safely to the homepage.
      </p>
      <p v-if="errorMessage" class="boundary-debug">
        <code>{{ errorMessage }}</code>
      </p>
      <div class="boundary-actions">
        <button type="button" class="btn-boundary-primary" @click="handleRetry">
          Reload Page
        </button>
        <button type="button" class="btn-boundary-secondary" @click="handleGoHome">
          Return to Home
        </button>
      </div>
    </div>
  </div>
  <slot v-else />
</template>

<style scoped>
.route-error-boundary {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 50vh;
  padding: 64px 24px;
  background-color: var(--color-bg, #F8FAFC);
}

.boundary-card {
  max-width: 540px;
  width: 100%;
  padding: 40px 32px;
  background-color: var(--color-surface, #FFFFFF);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: var(--radius-2xl, 24px);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
}

:global([data-theme='dark']) .boundary-card {
  background-color: rgba(30, 41, 59, 0.6);
  border-color: rgba(255, 255, 255, 0.1);
}

.boundary-eyebrow {
  font-family: ui-monospace, "JetBrains Mono", monospace;
  font-size: 0.6875rem;
  letter-spacing: 0.14em;
  font-weight: 600;
  color: var(--color-primary, #0F766E);
  text-transform: uppercase;
}

.boundary-title {
  font-family: var(--font-serif, "Instrument Serif", Georgia, serif);
  font-size: 2rem;
  font-weight: 500;
  line-height: 1.15;
  color: var(--color-text, #0F172A);
  margin: 0;
}

.boundary-desc {
  font-size: 0.9375rem;
  color: var(--color-muted, #64748B);
  line-height: 1.5;
  margin: 0;
}

.boundary-debug {
  font-size: 0.75rem;
  padding: 6px 12px;
  background-color: var(--color-bg, #F1F5F9);
  border-radius: 6px;
  color: #DC2626;
  max-width: 100%;
  overflow-x: auto;
}

:global([data-theme='dark']) .boundary-debug {
  background-color: rgba(0, 0, 0, 0.3);
  color: #F87171;
}

.boundary-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.btn-boundary-primary {
  padding: 10px 24px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #FFFFFF;
  background-color: var(--color-primary, #0F766E);
  border: none;
  border-radius: var(--radius-pill, 999px);
  cursor: pointer;
  transition: opacity var(--transition-fast, 0.15s ease);
}

.btn-boundary-primary:hover {
  opacity: 0.9;
}

.btn-boundary-secondary {
  padding: 10px 24px;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text, #0F172A);
  background-color: transparent;
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: var(--radius-pill, 999px);
  cursor: pointer;
  transition: all var(--transition-fast, 0.15s ease);
}

.btn-boundary-secondary:hover {
  background-color: var(--color-hover, rgba(0, 0, 0, 0.04));
}
</style>

