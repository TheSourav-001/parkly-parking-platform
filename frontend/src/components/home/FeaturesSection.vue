<script setup>
/**
 * FeaturesSection Component — Mastercard-Inspired 4 Core Features
 * 1. Location-Based Search
 * 2. AI-Powered Recommendations
 * 3. Transparent Pricing
 * 4. Easy Online Booking
 *
 * Characteristics:
 * - Eyebrow label with tiny accent dot
 * - Asymmetric editorial layout with circular icon containers
 * - Generous whitespace & soft halos
 */
const features = [
  {
    id: 'location',
    step: '01',
    title: 'Location-Based Search',
    tag: 'GEO-DISCOVERY',
    description: 'Find verified parking facilities nearest to your current location or target destination with real-time GPS accuracy.',
    iconSvg: 'map-pin',
  },
  {
    id: 'ai',
    step: '02',
    title: 'AI-Powered Recommendations',
    tag: 'INTELLIGENCE',
    description: 'Gemini-assisted intelligent matching analyzes past parking availability, traffic flows, and your vehicle requirements.',
    iconSvg: 'sparkles',
  },
  {
    id: 'pricing',
    step: '03',
    title: 'Transparent Pricing',
    tag: 'FAIR RATES',
    description: 'No surprise surge fees. Upfront hourly and daily rates with verified vehicle-tier pricing before you navigate.',
    iconSvg: 'receipt',
  },
  {
    id: 'booking',
    step: '04',
    title: 'Easy Online Booking',
    tag: 'SEAMLESS PASS',
    description: 'Reserve your spot in two taps. Instant digital pass with QR code verification for automated gate check-in.',
    iconSvg: 'ticket',
  },
]
</script>

<template>
  <section class="features-section" id="features">
    <div class="features-container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="eyebrow">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          PLATFORM ARCHITECTURE
        </div>
        <h2 class="section-title">Designed for frictionless urban mobility.</h2>
        <p class="section-subtitle">
          Parkly replaces circling the block with instant discovery, transparent rates, and guaranteed spots.
        </p>
      </div>

      <!-- 4 Editorial Feature Cards -->
      <div class="features-grid">
        <div
          v-for="feat in features"
          :key="feat.id"
          class="feature-card"
        >
          <!-- Top Row: Circular Icon + Step Tag -->
          <div class="card-top">
            <div class="circle-icon-mask" aria-hidden="true">
              <!-- Location Icon -->
              <svg v-if="feat.iconSvg === 'map-pin'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>

              <!-- AI Sparkles -->
              <svg v-else-if="feat.iconSvg === 'sparkles'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
              </svg>

              <!-- Pricing Receipt -->
              <svg v-else-if="feat.iconSvg === 'receipt'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/>
                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                <path d="M12 6v12"/>
              </svg>

              <!-- Booking Pass -->
              <svg v-else-if="feat.iconSvg === 'ticket'" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                <path d="M13 5v2"/>
                <path d="M13 17v2"/>
                <path d="M13 11v2"/>
              </svg>
            </div>

            <span class="card-step-badge">{{ feat.step }}</span>
          </div>

          <!-- Eyebrow Tag -->
          <div class="card-eyebrow">
            <span class="eyebrow-mini-dot"></span>
            {{ feat.tag }}
          </div>

          <!-- Headline & Paragraph -->
          <h3 class="card-title">{{ feat.title }}</h3>
          <p class="card-description">{{ feat.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.features-section {
  width: 100%;
  padding: 80px 24px;
  background-color: var(--color-bg);
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 56px;
}

/* Header */
.section-header {
  max-width: 640px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.section-title {
  font-size: clamp(2rem, 3.5vw, 2.75rem);
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.025em;
  color: var(--color-text);
  margin: 0;
}

.section-subtitle {
  font-size: 1.05rem;
  color: var(--color-text-muted);
  line-height: 1.55;
  margin: 0;
}

/* Grid */
.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

/* Feature Card (Mastercard-inspired 32px rounded stadium container) */
.feature-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 28px;
  padding: 32px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: var(--shadow-sm);
  transition:
    transform var(--transition-normal),
    box-shadow var(--transition-normal),
    border-color var(--transition-normal);
}

.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-halo);
  border-color: var(--color-primary);
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Circular Icon Mask */
.circle-icon-mask {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background-color: var(--color-surface-subtle);
  color: var(--color-primary);
  border: 1.5px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: center;
  transition:
    background-color var(--transition-fast),
    transform var(--transition-fast);
}

.feature-card:hover .circle-icon-mask {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  transform: scale(1.05);
}

.card-step-badge {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-text-subtle);
  font-family: var(--font-mono);
}

.card-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.eyebrow-mini-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.card-title {
  font-size: 1.25rem;
  font-weight: 500;
  line-height: 1.25;
  letter-spacing: -0.02em;
  color: var(--color-text);
  margin: 0;
}

.card-description {
  font-size: 0.9375rem;
  font-weight: 450;
  line-height: 1.5;
  color: var(--color-text-muted);
  margin: 0;
}

/* Breakpoints */
@media (max-width: 1080px) {
  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .features-section {
    padding: 56px 16px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>

