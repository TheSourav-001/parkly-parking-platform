<script setup>
/**
 * SectionHeader Component
 * Unified, accessible typography structure for all homepage sections.
 */
defineProps({
  eyebrow: {
    type: String,
    default: '',
  },
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  centered: {
    type: Boolean,
    default: true,
  },
})
</script>

<template>
  <div class="section-header" :class="{ 'is-centered': centered }">
    <span v-if="eyebrow" class="section-eyebrow">{{ eyebrow }}</span>
    <h2 class="section-title">{{ title }}</h2>
    <p v-if="subtitle" class="section-subtitle">{{ subtitle }}</p>
  </div>
</template>

<style scoped>
.section-header {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-width: 680px;
  margin-bottom: clamp(32px, 5vh, 48px);
}

.section-header.is-centered {
  align-items: center;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
}

.section-eyebrow {
  font-family: var(--font-sans);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-primary);
}

.section-title {
  font-family: var(--font-sans);
  font-size: clamp(1.75rem, 3.2vw, 2.375rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  color: var(--color-text);
  line-height: 1.2;
  margin: 0;
}

.section-subtitle {
  font-family: var(--font-sans);
  font-size: clamp(0.9375rem, 1.4vw, 1.0625rem);
  color: var(--color-text-muted);
  line-height: 1.5;
  margin: 0;
}
</style>

