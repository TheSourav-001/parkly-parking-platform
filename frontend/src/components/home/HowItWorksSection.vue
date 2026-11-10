<script setup>
/**
 * HowItWorksSection Component — Mastercard-Inspired 4-Step Workflow
 * Steps:
 * 1. Find Parking
 * 2. Compare Options
 * 3. Book & Pay
 * 4. Get Your Digital Pass
 *
 * Characteristics:
 * - Circular icon containers
 * - Minimal connecting arrows / orbital progress trail
 * - Generous editorial whitespace
 */
const steps = [
  {
    number: '1',
    title: 'Find Parking',
    subtitle: 'Search & Locate',
    description: 'Enter your destination, select your vehicle type, or tap GPS to see available parking spaces in real time.',
    icon: 'search',
  },
  {
    number: '2',
    title: 'Compare Options',
    subtitle: 'Inspect & Choose',
    description: 'Compare transparent hourly rates, distance, covered bays, security ratings, and live remaining slot capacity.',
    icon: 'sliders',
  },
  {
    number: '3',
    title: 'Book & Pay',
    subtitle: 'Instant Reservation',
    description: 'Lock in your parking slot instantly with bKash, Nagad, card, or wallet without paying surge markups.',
    icon: 'credit-card',
  },
  {
    number: '4',
    title: 'Get Your Digital Pass',
    subtitle: 'Effortless Entry',
    description: 'Receive your dynamic QR parking pass and Google Maps route. Drive up to the gate, scan, and park seamlessly.',
    icon: 'qr-code',
  },
]
</script>

<template>
  <section class="how-it-works-section" id="how-it-works">
    <div class="how-container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="eyebrow">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          HOW PARKLY WORKS
        </div>
        <h2 class="section-title">Parking in four effortless steps.</h2>
        <p class="section-subtitle">
          Say goodbye to uncertain parking searches and stressful street meters.
        </p>
      </div>

      <!-- 4 Connected Steps -->
      <div class="steps-flow">
        <div
          v-for="(st, idx) in steps"
          :key="st.number"
          class="step-item"
        >
          <!-- Top Row with Step Circle and Connector -->
          <div class="step-node-row">
            <div class="step-circle-container">
              <div class="step-circle">
                <span class="step-digit">{{ st.number }}</span>
              </div>
              <div class="step-circle-halo" aria-hidden="true"></div>
            </div>

            <!-- Connecting Arrow / Line (except last item) -->
            <div v-if="idx < steps.length - 1" class="step-connector" aria-hidden="true">
              <div class="connector-line"></div>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="connector-arrow">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </div>
          </div>

          <!-- Step Content -->
          <div class="step-content">
            <span class="step-subhead">{{ st.subtitle }}</span>
            <h3 class="step-title">{{ st.title }}</h3>
            <p class="step-description">{{ st.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.how-it-works-section {
  width: 100%;
  padding: 88px 24px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
  position: relative;
  overflow: hidden;
}

.how-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 64px;
}

/* Header */
.section-header {
  max-width: 600px;
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

/* Flow Grid */
.steps-flow {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
  position: relative;
}

.step-item {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Node & Connector */
.step-node-row {
  display: flex;
  align-items: center;
  position: relative;
}

.step-circle-container {
  position: relative;
  width: 68px;
  height: 68px;
  flex-shrink: 0;
}

.step-circle {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: var(--color-surface);
  border: 2px solid var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-sm);
  position: relative;
  z-index: 2;
  transition:
    transform var(--transition-fast),
    background-color var(--transition-fast);
}

.step-item:hover .step-circle {
  transform: scale(1.08);
  background-color: var(--color-primary);
}

.step-item:hover .step-digit {
  color: var(--color-primary-contrast);
}

.step-digit {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--color-primary);
  letter-spacing: -0.03em;
  transition: color var(--transition-fast);
}

.step-circle-halo {
  position: absolute;
  inset: -6px;
  border-radius: 50%;
  background-color: var(--color-primary-light);
  z-index: 1;
  opacity: 0.6;
}

/* Connector Line */
.step-connector {
  flex: 1;
  display: flex;
  align-items: center;
  padding: 0 12px;
  color: var(--color-border);
}

.connector-line {
  flex: 1;
  height: 1.5px;
  background-color: var(--color-border);
}

.connector-arrow {
  color: var(--color-border);
  margin-left: -4px;
}

/* Content */
.step-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.step-subhead {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.step-title {
  font-size: 1.25rem;
  font-weight: 500;
  letter-spacing: -0.02em;
  color: var(--color-text);
  margin: 0;
}

.step-description {
  font-size: 0.9375rem;
  font-weight: 450;
  line-height: 1.5;
  color: var(--color-text-muted);
  margin: 0;
}

/* Breakpoints */
@media (max-width: 1024px) {
  .steps-flow {
    grid-template-columns: repeat(2, 1fr);
    gap: 40px;
  }

  .step-connector {
    display: none;
  }
}

@media (max-width: 640px) {
  .how-it-works-section {
    padding: 56px 16px;
  }

  .steps-flow {
    grid-template-columns: 1fr;
    gap: 36px;
  }
}
</style>

