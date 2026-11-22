<script setup>
/**
 * StatChip Component
 * Reusable animated or static badge chip for numerical metrics
 */
defineProps({
  value: {
    type: [String, Number],
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
  sublabel: {
    type: String,
    default: '',
  },
  accent: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <div class="stat-chip" :class="{ 'is-accent': accent }">
    <div class="stat-main">
      <span class="stat-value">{{ value }}</span>
      <span class="stat-label">{{ label }}</span>
    </div>
    <span v-if="sublabel" class="stat-sublabel">{{ sublabel }}</span>
  </div>
</template>

<style scoped>
.stat-chip {
  display: inline-flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 8px 16px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg, 12px);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.stat-chip.is-accent {
  border-color: var(--color-primary);
  background-color: rgba(15, 118, 110, 0.04);
}

.stat-main {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.stat-value {
  font-family: var(--font-sans);
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--color-primary);
  line-height: 1;
}

.stat-label {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--color-text);
}

.stat-sublabel {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
  margin-top: 2px;
}
</style>

