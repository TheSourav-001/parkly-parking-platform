<script setup>
/**
 * AppButton Component
 * Reusable button for Parkly Design System.
 *
 * @prop {string} variant - 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger'
 * @prop {string} size - 'sm' | 'md' | 'lg'
 * @prop {string} type - 'button' | 'submit' | 'reset'
 * @prop {boolean} disabled - Whether the button is disabled
 * @prop {boolean} loading - Display loading state
 * @prop {boolean} block - Full width button
 */
defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(v),
  },
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  type: {
    type: String,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  block: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['click'])
</script>

<template>
  <button
    :type="type"
    class="parkly-button"
    :class="[
      `variant-${variant}`,
      `size-${size}`,
      { 'is-loading': loading, 'is-block': block }
    ]"
    :disabled="disabled || loading"
    :aria-busy="loading"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="spinner" aria-hidden="true"></span>
    <span v-if="$slots.prefix" class="icon-prefix">
      <slot name="prefix" />
    </span>
    <span class="button-label">
      <slot />
    </span>
    <span v-if="$slots.suffix" class="icon-suffix">
      <slot name="suffix" />
    </span>
  </button>
</template>

<style scoped>
.parkly-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  border-radius: var(--radius-md);
  font-family: var(--font-sans);
  font-weight: var(--font-weight-medium);
  cursor: pointer;
  border: 1px solid transparent;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast),
    color var(--transition-fast),
    box-shadow var(--transition-fast),
    transform var(--transition-fast);
  white-space: nowrap;
  user-select: none;
  text-decoration: none;
}

.parkly-button:active:not(:disabled) {
  transform: translateY(1px);
}

.parkly-button.is-block {
  display: flex;
  width: 100%;
}

.parkly-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* --- Sizes --- */
.size-sm {
  font-size: var(--font-size-xs);
  padding: var(--space-1) var(--space-3);
  height: 32px;
}

.size-md {
  font-size: var(--font-size-sm);
  padding: var(--space-2) var(--space-4);
  height: 40px;
}

.size-lg {
  font-size: var(--font-size-base);
  padding: var(--space-3) var(--space-6);
  height: 48px;
}

/* --- Variants --- */
.variant-primary {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  box-shadow: var(--shadow-sm);
}
.variant-primary:hover:not(:disabled) {
  background-color: var(--color-primary-hover);
}
.variant-primary:active:not(:disabled) {
  background-color: var(--color-primary-active);
}

.variant-secondary {
  background-color: var(--color-surface-subtle);
  color: var(--color-text);
  border-color: var(--color-border);
}
.variant-secondary:hover:not(:disabled) {
  background-color: var(--color-border);
  color: var(--color-text);
}

.variant-outline {
  background-color: transparent;
  color: var(--color-primary);
  border-color: var(--color-primary);
}
.variant-outline:hover:not(:disabled) {
  background-color: var(--color-primary-light);
}

.variant-ghost {
  background-color: transparent;
  color: var(--color-text);
  border-color: transparent;
}
.variant-ghost:hover:not(:disabled) {
  background-color: var(--color-surface-subtle);
}

.variant-danger {
  background-color: var(--color-danger);
  color: #FFFFFF;
}
.variant-danger:hover:not(:disabled) {
  filter: brightness(0.9);
}

/* --- Spinner --- */
.spinner {
  width: 1em;
  height: 1em;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.icon-prefix,
.icon-suffix {
  display: inline-flex;
  align-items: center;
}
</style>

