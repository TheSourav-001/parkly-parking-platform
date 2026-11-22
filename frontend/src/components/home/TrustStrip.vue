<script setup>
/**
 * TrustStrip Component
 * Slim credibility band placed right under the Map section.
 * Shows verified guarantees + live database metrics.
 */
import { ref, onMounted } from 'vue'
import StatChip from '../common/StatChip.vue'

const stats = ref({
  verified_facilities: 15,
  areas_covered: 12,
  last_data_update_minutes_ago: 8,
})

const isLoading = ref(true)

async function fetchStats() {
  try {
    const res = await fetch('/api/stats')
    if (res.ok) {
      const data = await res.json()
      stats.value = data
    }
  } catch (e) {
    // Graceful fallback to default values
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchStats()
})
</script>

<template>
  <section class="trust-strip-section" aria-label="Platform Trust & Standards">
    <div class="trust-container">
      <!-- 4 Core Guarantees -->
      <div class="guarantees-grid">
        <div class="guarantee-item">
          <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="guarantee-text">Registered facilities only</span>
        </div>

        <div class="guarantee-item">
          <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="guarantee-text">Admin & operator-updated slots</span>
        </div>

        <div class="guarantee-item">
          <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="guarantee-text">Server-calculated fees</span>
        </div>

        <div class="guarantee-item">
          <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <span class="guarantee-text">Digital invoice for entry staff</span>
        </div>
      </div>

      <!-- Live Metric Chips from API -->
      <div class="chips-row">
        <StatChip
          :value="stats.verified_facilities"
          label="Registered Hubs"
          sublabel="Dhaka records"
          accent
        />
        <StatChip
          :value="stats.areas_covered"
          label="Areas Covered"
          sublabel="Key commercial zones"
        />
        <StatChip
          :value="`~${stats.last_data_update_minutes_ago}m`"
          label="Last Slot Sync"
          sublabel="Reported status"
        />
      </div>
    </div>
  </section>
</template>

<style scoped>
.trust-strip-section {
  width: 100%;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
  padding: 24px 16px;
}

.trust-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  align-items: center;
}

@media (min-width: 992px) {
  .trust-container {
    flex-direction: row;
    justify-content: space-between;
  }
}

.guarantees-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 16px 24px;
  align-items: center;
  justify-content: center;
}

.guarantee-item {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.check-icon {
  width: 18px;
  height: 18px;
  color: var(--color-primary);
  flex-shrink: 0;
}

.guarantee-text {
  font-size: 0.84375rem;
  font-weight: 600;
  color: var(--color-text);
  white-space: nowrap;
}

.chips-row {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
  justify-content: center;
}
</style>

