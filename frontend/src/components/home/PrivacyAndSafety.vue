<script setup>
/**
 * PrivacyAndSafety Component
 * Transparent communication about:
 * - Location permission (asked ONLY on click, manual selection always available)
 * - Payment credentials not stored by Parkly (handled by SSLCOMMERZ gateway)
 * - AI grounded strictly in DB records
 * - Reported availability (clearly marked as admin/operator updated, no sensor myths)
 */
import SectionHeader from '../common/SectionHeader.vue'

const safetyPillars = [
  {
    type: 'gps',
    title: 'On-Click Location Only',
    desc: 'We never prompt for browser GPS location on page load. Location is only requested when you click "Near Me". Manual cascading search is always available.',
  },
  {
    type: 'security',
    title: 'Zero Payment Card Storage',
    desc: 'Parkly never handles or stores your credit/debit card numbers or MFS PINs. All sandbox/live transactions are processed through SSLCOMMERZ secure tokenization.',
  },
  {
    type: 'ai',
    title: 'Grounded & Bound AI',
    desc: 'Our Gemini AI model is strictly bounded to natural language interpretation. It cannot invent inventory, alter database records, or make financial decisions.',
  },
  {
    type: 'transparency',
    title: 'Reported Availability Transparency',
    desc: 'We never claim automated sensor or camera detection. All open bay statuses represent the latest manual counts reported by facility admins or garage attendants.',
  },
]
</script>

<template>
  <section id="privacy" class="privacy-safety-section">
    <div class="safety-container">
      <SectionHeader
        eyebrow="Privacy & Integrity"
        title="Transparent Policies, Zero Misleading Promises"
        subtitle="Parkly is built on user privacy, clear data boundaries, and complete honesty regarding physical garage operations."
      />

      <div class="pillars-grid">
        <div v-for="(p, idx) in safetyPillars" :key="idx" class="pillar-card">
          <div class="pillar-icon-wrap" aria-hidden="true">
            <!-- GPS Line Icon -->
            <svg v-if="p.type === 'gps'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <!-- Lock Line Icon -->
            <svg v-else-if="p.type === 'security'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <!-- AI Line Icon -->
            <svg v-else-if="p.type === 'ai'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2v4" />
              <path d="m4.93 10.93 2.83-2.83" />
              <path d="M2 18h4" />
              <path d="M20 18h2" />
              <path d="m19.07 10.93-2.83-2.83" />
              <rect width="14" height="12" x="5" y="10" rx="3" />
              <circle cx="9" cy="16" r="1" fill="currentColor" />
              <circle cx="15" cy="16" r="1" fill="currentColor" />
            </svg>
            <!-- Transparency Line Icon -->
            <svg v-else-if="p.type === 'transparency'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
              <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
              <path d="M9 12h6" />
              <path d="M9 16h6" />
            </svg>
          </div>
          <h3 class="pillar-title">{{ p.title }}</h3>
          <p class="pillar-desc">{{ p.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.privacy-safety-section {
  padding: 120px 24px;
  background-color: var(--color-bg);
  border-top: 1px solid var(--color-border);
}

.safety-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 48px;
}

.pillars-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.pillar-card {
  padding: 32px 24px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg, 12px);
  display: flex;
  flex-direction: column;
  gap: 14px;
  transition: transform var(--transition-fast), border-color var(--transition-fast);
}

.pillar-card:hover {
  transform: translateY(-2px);
  border-color: var(--color-primary);
}

.pillar-icon-wrap {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-primary);
  background-color: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 8px;
}

.pillar-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--color-text);
  line-height: 1.35;
  margin: 0;
}

.pillar-desc {
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--color-text-muted);
  margin: 0;
}

@media (max-width: 1024px) {
  .pillars-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .privacy-safety-section {
    padding: 72px 16px;
  }

  .pillars-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}
</style>
