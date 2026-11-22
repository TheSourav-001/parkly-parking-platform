<script setup>
/**
 * FinalCTA Component
 * Clean closing call-to-action banner for driver search and garage owner onboarding.
 */
function scrollToDiscovery() {
  const el = document.getElementById('discovery')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}
</script>

<template>
  <section class="final-cta-section">
    <div class="cta-container">
      <div class="cta-stadium-card">
        <span class="cta-eyebrow">Start Parking Smarter</span>
        <h2 class="cta-title">Ready to Discover Verified Parking Across Dhaka?</h2>
        <p class="cta-sub">
          Explore open slots across Gulshan, Banani, Dhanmondi, and Mirpur, or onboard your commercial garage today.
        </p>

        <div class="cta-buttons">
          <button class="btn-primary" @click="scrollToDiscovery">
            Find Parking Now
          </button>
          <router-link to="/register" class="btn-secondary">
            List Your Garage
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.final-cta-section {
  width: 100%;
  padding: 80px 24px 100px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
}

.cta-container {
  max-width: 1000px;
  margin: 0 auto;
}

.cta-stadium-card {
  background: var(--color-primary);
  color: var(--color-primary-contrast, #FFF);
  border-radius: var(--radius-3xl, 32px);
  padding: clamp(48px, 6vw, 64px) clamp(24px, 4vw, 48px);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  box-shadow: 0 20px 40px -8px rgba(15, 118, 110, 0.35);
}

.cta-eyebrow {
  font-size: 0.8125rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #99F6E4;
}

.cta-title {
  font-family: var(--font-sans);
  font-size: clamp(1.75rem, 3.5vw, 2.5rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  color: #FFFFFF;
  line-height: 1.2;
  margin: 0;
  max-width: 720px;
}

.cta-sub {
  font-size: 1rem;
  color: #CCFBF1;
  max-width: 580px;
  line-height: 1.5;
  margin: 0;
}

.cta-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-top: 16px;
  justify-content: center;
}

.btn-primary {
  background: #FFFFFF;
  color: var(--color-primary);
  font-size: 0.9375rem;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.btn-secondary {
  background: transparent;
  color: #FFFFFF;
  border: 1.5px solid rgba(255, 255, 255, 0.5);
  font-size: 0.9375rem;
  font-weight: 600;
  padding: 14px 28px;
  border-radius: 999px;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.btn-secondary:hover {
  border-color: #FFFFFF;
  background: rgba(255, 255, 255, 0.1);
}
</style>

