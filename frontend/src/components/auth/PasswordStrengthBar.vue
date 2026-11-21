<script setup>
/**
 * PasswordStrengthBar Component
 * Evaluates password strength and visually displays a responsive strength meter.
 */
import { computed } from 'vue'

const props = defineProps({
  password: {
    type: String,
    default: '',
  },
})

const score = computed(() => {
  const pwd = props.password
  if (!pwd) return 0
  let s = 0
  if (pwd.length >= 8) s += 1
  if (pwd.length >= 12) s += 1
  if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) s += 1
  if (/[0-9]/.test(pwd)) s += 1
  if (/[^A-Za-z0-9]/.test(pwd)) s += 1
  return s // 0 to 5
})

const strengthLabel = computed(() => {
  if (!props.password) return ''
  if (score.value <= 1) return 'Weak'
  if (score.value <= 3) return 'Moderate'
  return 'Strong'
})

const strengthClass = computed(() => {
  if (!props.password) return ''
  if (score.value <= 1) return 'strength-weak'
  if (score.value <= 3) return 'strength-moderate'
  return 'strength-strong'
})

const percentage = computed(() => {
  if (!props.password) return 0
  return Math.min(100, (score.value / 5) * 100)
})
</script>

<template>
  <div v-if="password" class="password-strength-container" aria-live="polite">
    <div class="meter-bar-track">
      <div
        class="meter-bar-fill"
        :class="strengthClass"
        :style="{ width: `${percentage}%` }"
      />
    </div>
    <div class="strength-info">
      <span class="label-text">Strength:</span>
      <span class="status-text" :class="strengthClass">{{ strengthLabel }}</span>
    </div>
  </div>
</template>

<style scoped>
.password-strength-container {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  margin-top: calc(-1 * var(--space-1));
}

.meter-bar-track {
  height: 4px;
  width: 100%;
  background-color: var(--color-surface-subtle);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.meter-bar-fill {
  height: 100%;
  transition: width var(--transition-normal), background-color var(--transition-normal);
}

.strength-info {
  display: flex;
  justify-content: flex-end;
  gap: var(--space-1);
  font-size: var(--font-size-xs);
}

.label-text {
  color: var(--color-text-muted);
}

.status-text {
  font-weight: var(--font-weight-medium);
}

/* Color variations */
.strength-weak {
  background-color: var(--color-danger);
  color: var(--color-danger);
}

.strength-moderate {
  background-color: #F59E0B;
  color: #D97706;
}

.strength-strong {
  background-color: var(--color-success);
  color: var(--color-success);
}
</style>

