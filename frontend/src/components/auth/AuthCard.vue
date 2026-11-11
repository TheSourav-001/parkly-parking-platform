<script setup>
/**
 * AuthCard Component (Refined for Masterclass Full-Viewport Split-Screen Layout)
 *
 * Left Panel (approx 44% on desktop):
 * - Clean brand header: "Your Parking Journey Starts Here."
 * - Minimal, authentic supporting text: "Discover, compare, and reserve registered parking facilities with a simpler parking experience."
 * - Dedicated ParkingAuthIllustration component with entrance animation.
 * - Genuine benefits (Easy Parking Discovery, Clear Parking Rates, Simple Online Booking).
 *
 * Right Panel (approx 56% on desktop):
 * - Vertically and horizontally centered form container.
 * - Compact form layout tailored to fit desktop viewports (1366x768, 1440x900, 1920x1080) without vertical page scroll.
 * - Natural scroll on shorter viewports or longer forms (e.g. registration).
 */
import ParkingAuthIllustration from './ParkingAuthIllustration.vue'

defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  compact: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <div class="auth-viewport-wrapper" :class="{ 'auth-viewport-wrapper--compact': compact }">
    <div class="auth-split-card" :class="{ 'auth-split-card--compact': compact }">
      <!-- ================= LEFT PANEL: BRAND & ANIMATED SCENE ================= -->
      <aside class="auth-left-panel">
        <div class="left-panel-content">
          <!-- Textual Brand Context -->
          <div class="brand-text-block">
            <h2 class="brand-heading">
              Your Parking Journey <br />
              <span class="brand-highlight">Starts Here.</span>
            </h2>
            <p class="brand-subtext">
              Discover, compare, and reserve registered parking facilities with a simpler parking experience.
            </p>
          </div>

          <!-- Main Cohesive Parking Illustration -->
          <div class="illustration-stage">
            <ParkingAuthIllustration />
          </div>

          <!-- Genuine, Non-fabricated Benefits -->
          <ul class="benefit-list" aria-label="Platform advantages">
            <li class="benefit-item">
              <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span>Easy Parking Discovery</span>
            </li>
            <li class="benefit-item">
              <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span>Clear Parking Rates</span>
            </li>
            <li class="benefit-item">
              <svg class="check-icon" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
              <span>Simple Online Booking</span>
            </li>
          </ul>
        </div>
      </aside>

      <!-- ================= RIGHT PANEL: AUTHENTICATION FORM ================= -->
      <section class="auth-right-panel">
        <div class="auth-form-card">
          <!-- Form Header -->
          <header class="auth-form-header">
            <h1 class="form-title">{{ title }}</h1>
            <p v-if="subtitle" class="form-subtitle">{{ subtitle }}</p>
          </header>

          <!-- Form Body Slot -->
          <div class="auth-form-body">
            <slot />
          </div>

          <!-- Form Footer Slot -->
          <footer v-if="$slots.footer" class="auth-form-footer">
            <slot name="footer" />
          </footer>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
/* Main Full-Viewport Container: guarantees clean fit below the floating pill Navbar */
.auth-viewport-wrapper {
  height: calc(100svh - 92px);
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-2) var(--space-4) var(--space-4);
  box-sizing: border-box;
  background-color: var(--color-bg);
  overflow-y: auto;
}

.auth-split-card {
  max-width: 1080px;
  width: 100%;
  max-height: calc(100svh - 116px);
  min-height: 500px;
  display: flex;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal),
    box-shadow var(--transition-normal);
}

:global([data-theme='dark']) .auth-split-card {
  border-color: #26364C;
  box-shadow: none;
}

/* ================= LEFT PANEL ================= */
.auth-left-panel {
  flex: 0 0 42%;
  max-width: 42%;
  background: linear-gradient(150deg, var(--color-surface-subtle) 0%, var(--color-surface) 100%);
  border-right: 1px solid var(--color-border);
  padding: var(--space-5) var(--space-6);
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
}

:global([data-theme='dark']) .auth-left-panel {
  border-right-color: #26364C;
}

.left-panel-content {
  display: flex;
  flex-direction: column;
  height: 100%;
  justify-content: space-between;
  gap: var(--space-3);
}

.brand-heading {
  font-size: clamp(1.2rem, 2vw, 1.5rem);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  line-height: 1.2;
  margin: 0;
  letter-spacing: -0.02em;
}

.brand-highlight {
  color: var(--color-primary);
}

.brand-subtext {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  line-height: var(--line-height-normal);
  margin-top: 4px;
  max-width: 360px;
}

.illustration-stage {
  flex: 1 1 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  max-height: 270px;
}

.benefit-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  font-weight: var(--font-weight-medium);
}

.check-icon {
  width: 13px;
  height: 13px;
  color: var(--color-primary);
  flex-shrink: 0;
}

/* ================= RIGHT PANEL ================= */
.auth-right-panel {
  flex: 1 1 58%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-4) var(--space-6);
  background-color: var(--color-surface);
  overflow-y: auto;
  box-sizing: border-box;
}

.auth-form-card {
  width: 100%;
  max-width: 440px;
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  margin: auto 0;
}

.auth-form-header {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-bottom: 2px;
}

.form-title {
  font-size: clamp(1.3rem, 1.8vw, 1.65rem);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  margin: 0;
  letter-spacing: -0.02em;
}

.form-subtitle {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  margin: 0;
  line-height: var(--line-height-normal);
}

.auth-form-body {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.auth-form-footer {
  border-top: 1px solid var(--color-border);
  padding-top: var(--space-2);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  text-align: center;
}

:global([data-theme='dark']) .auth-form-footer {
  border-top-color: #26364C;
}

/* ================= RESPONSIVE LAYOUT ================= */
@media (max-width: 960px) {
  .auth-viewport-wrapper {
    height: auto;
    min-height: calc(100svh - 92px);
    padding: var(--space-3) var(--space-2) var(--space-5);
  }

  .auth-split-card {
    flex-direction: column;
    max-height: none;
    min-height: auto;
    border-radius: var(--radius-lg);
  }

  .auth-left-panel {
    flex: none;
    max-width: 100%;
    width: 100%;
    border-right: none;
    border-bottom: 1px solid var(--color-border);
    padding: var(--space-5);
  }

  .brand-text-block {
    text-align: center;
  }

  .brand-subtext {
    margin: var(--space-1) auto 0;
  }

  .illustration-stage {
    max-height: 180px;
  }

  .benefit-list {
    flex-direction: row;
    justify-content: center;
    flex-wrap: wrap;
    gap: var(--space-3);
  }

  .auth-right-panel {
    flex: none;
    width: 100%;
    padding: var(--space-6) var(--space-4);
  }
}

@media (max-width: 640px) {
  .auth-viewport-wrapper {
    padding: var(--space-2);
  }

  .auth-left-panel {
    padding: var(--space-4);
  }

  .benefit-list {
    display: none; /* Keep clean on small mobile devices */
  }

  .illustration-stage {
    max-height: 140px;
  }

  .auth-right-panel {
    padding: var(--space-5) var(--space-3);
  }
}
</style>
