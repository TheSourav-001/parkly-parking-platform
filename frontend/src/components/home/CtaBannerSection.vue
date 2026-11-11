<script setup>
/**
 * CtaBannerSection Component — Mastercard-Inspired Rounded Stadium Banner
 * Heading: "Ready to Find Your Parking Spot?"
 * Description: "Skip the hassle. Discover, compare, and book parking with Parkly."
 * Button: "Find Parking Now"
 *
 * Characteristics:
 * - 40px oversized rounded stadium corners
 * - Parkly Navy + Teal palette
 * - Thin orbital arcs
 * - Pill-shaped primary action
 */
import AppButton from '../common/AppButton.vue'

defineEmits(['action'])
</script>

<template>
  <section class="cta-banner-section">
    <div class="cta-banner-container">
      <!-- Stadium Container with 40px radius -->
      <div class="stadium-card">
        <!-- Thin Orbital Background Arc -->
        <svg class="cta-orbit-svg" viewBox="0 0 1000 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <circle cx="800" cy="200" r="280" stroke="var(--color-primary)" stroke-width="1.25" stroke-dasharray="4 6" opacity="0.4" />
          <circle cx="200" cy="350" r="180" stroke="var(--color-primary)" stroke-width="1" stroke-dasharray="6 8" opacity="0.25" />
          <circle cx="800" cy="480" r="4" fill="var(--color-primary)" />
        </svg>

        <div class="cta-content">
          <div class="cta-eyebrow">
            <span class="eyebrow-dot" aria-hidden="true"></span>
            GET STARTED TODAY
          </div>

          <h2 class="cta-heading">
            Ready to Find Your Parking Spot?
          </h2>

          <p class="cta-description">
            Skip the hassle. Discover, compare, and book parking with Parkly.
          </p>

          <div class="cta-actions">
            <AppButton
              variant="primary"
              size="lg"
              pill
              class="cta-pill-button"
              @click="$emit('action')"
            >
              Find Parking Now
              <template #suffix>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </template>
            </AppButton>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cta-banner-section {
  width: 100%;
  padding: 40px 24px 88px;
  background-color: var(--color-bg);
}

.cta-banner-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Stadium Card with 40px radius (iconic Mastercard gesture) */
.stadium-card {
  position: relative;
  background-color: var(--color-surface);
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-4xl, 40px);
  padding: 80px 48px;
  overflow: hidden;
  box-shadow: var(--shadow-halo);
  text-align: center;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Orbital Background SVG */
.cta-orbit-svg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 1;
}

.cta-content {
  position: relative;
  z-index: 2;
  max-width: 640px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.cta-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.eyebrow-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.cta-heading {
  font-size: clamp(2.25rem, 4vw, 3.25rem);
  font-weight: 500;
  line-height: 1.1;
  letter-spacing: -0.025em;
  color: var(--color-text);
  margin: 0;
}

.cta-description {
  font-size: 1.15rem;
  font-weight: 450;
  color: var(--color-text-muted);
  line-height: 1.5;
  margin: 0;
}

.cta-actions {
  margin-top: 12px;
}

.cta-pill-button {
  padding: 0 36px !important;
  height: 52px !important;
  font-size: 1rem;
  font-weight: 600;
}

/* Breakpoints */
@media (max-width: 768px) {
  .stadium-card {
    border-radius: 28px;
    padding: 56px 24px;
  }

  .cta-banner-section {
    padding: 32px 16px 64px;
  }
}
</style>

