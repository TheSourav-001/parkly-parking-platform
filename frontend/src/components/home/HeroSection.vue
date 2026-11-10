<script setup>
/**
 * HeroSection Component — Mastercard-Inspired Editorial Hero
 * Asymmetric composition: Confident headline, tight tracking,
 * circular image masks with satellite CTA, thin decorative orbital lines,
 * and subtle watermark typography.
 */
import AppButton from '../common/AppButton.vue'

defineEmits(['explore', 'howItWorks'])
</script>

<template>
  <section class="hero-section">
    <!-- Subtle Background Watermark Typography -->
    <div class="hero-watermark" aria-hidden="true">
      PARKLY
    </div>

    <!-- Thin Decorative Orbital Arcs (Mastercard Signature translated to Parkly Teal) -->
    <svg class="orbital-svg" viewBox="0 0 1200 600" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path
        d="M 150,520 C 350,560 620,440 760,260 C 860,130 1020,80 1180,120"
        stroke="var(--color-primary)"
        stroke-width="1.25"
        stroke-dasharray="4 6"
        class="orbit-line-1"
      />
      <path
        d="M 400,580 C 600,600 850,500 1000,320 C 1100,200 1180,180 1240,190"
        stroke="var(--color-primary)"
        stroke-width="1"
        opacity="0.35"
        class="orbit-line-2"
      />
      <!-- Orbital Dots -->
      <circle cx="760" cy="260" r="3.5" fill="var(--color-primary)" class="orbit-dot-1" />
      <circle cx="1000" cy="320" r="3" fill="var(--color-primary)" class="orbit-dot-2" />
    </svg>

    <div class="hero-container">
      <!-- Left Column: Confident Editorial Typography -->
      <div class="hero-content">
        <!-- Eyebrow with accent dot -->
        <div class="hero-eyebrow">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          SMART URBAN MOBILITY
        </div>

        <h1 class="hero-headline">
          A Smarter Way to Find and Book Parking.
        </h1>

        <p class="hero-description">
          Discover nearby parking spaces, compare prices, check availability, and book your parking spot effortlessly with Parkly.
        </p>

        <div class="hero-ctas">
          <AppButton
            variant="primary"
            size="lg"
            pill
            class="cta-primary"
            @click="$emit('explore')"
          >
            Find Parking
            <template #suffix>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </template>
          </AppButton>

          <AppButton
            variant="secondary"
            size="lg"
            pill
            class="cta-secondary"
            @click="$emit('howItWorks')"
          >
            How It Works
          </AppButton>
        </div>

        <!-- Trust Stats Badges -->
        <div class="hero-stats">
          <div class="stat-pill">
            <span class="stat-number">50+</span>
            <span class="stat-label">Verified Garages</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-pill">
            <span class="stat-number">99.8%</span>
            <span class="stat-label">Spot Guarantee</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-pill">
            <span class="stat-number">AI</span>
            <span class="stat-label">Optimal Routing</span>
          </div>
        </div>
      </div>

      <!-- Right Column: Asymmetric Circular Image Constellation + Satellites -->
      <div class="hero-visual">
        <div class="visual-constellation">
          <!-- Main Primary Circular Portrait (400px) -->
          <div class="circle-portrait primary-circle">
            <div class="portrait-inner">
              <!-- Premium Modern Architectural Parking Facility SVG Illustration -->
              <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="portrait-img">
                <rect width="400" height="400" fill="#0F172A" />
                <circle cx="200" cy="200" r="190" fill="url(#hero-gradient)" />
                <defs>
                  <linearGradient id="hero-gradient" x1="0" y1="0" x2="400" y2="400" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#111C2E" />
                    <stop offset="0.6" stop-color="#0F766E" stop-opacity="0.85" />
                    <stop offset="1" stop-color="#042F2E" />
                  </linearGradient>
                </defs>
                <!-- Architectural Grid & Parking Lanes -->
                <g opacity="0.3" stroke="#FFFFFF" stroke-width="1.5">
                  <line x1="50" y1="200" x2="350" y2="200" />
                  <line x1="100" y1="120" x2="300" y2="120" />
                  <line x1="80" y1="280" x2="320" y2="280" />
                  <line x1="140" y1="100" x2="140" y2="300" stroke-dasharray="4 4" />
                  <line x1="260" y1="100" x2="260" y2="300" stroke-dasharray="4 4" />
                </g>
                <!-- Central Smart Parking Bay Isometric -->
                <path d="M 200,90 L 310,150 L 310,230 L 200,290 L 90,230 L 90,150 Z" fill="rgba(15, 118, 110, 0.4)" stroke="#14B8A6" stroke-width="2" />
                <path d="M 200,90 L 200,290" stroke="#14B8A6" stroke-width="1" stroke-dasharray="3 3" opacity="0.6" />
                <path d="M 90,150 L 200,210 L 310,150" stroke="#14B8A6" stroke-width="2" />
                <!-- Modern Stylized Car Silhouette (Teal Neon Outline) -->
                <g transform="translate(145, 165)">
                  <rect x="15" y="10" width="80" height="42" rx="14" fill="#0F172A" stroke="#5EEAD4" stroke-width="2.5" />
                  <rect x="28" y="16" width="54" height="18" rx="6" fill="#14B8A6" fill-opacity="0.3" stroke="#99F6E4" stroke-width="1" />
                  <!-- Headlights & Tail lights -->
                  <circle cx="25" cy="22" r="3" fill="#F8FAFC" />
                  <circle cx="85" cy="22" r="3" fill="#F8FAFC" />
                  <!-- Parking Spot Badge "P-04" -->
                  <rect x="36" y="38" width="38" height="12" rx="4" fill="#14B8A6" />
                  <text x="55" y="47" font-family="sans-serif" font-size="8" font-weight="bold" fill="#042F2E" text-anchor="middle">RESERVED</text>
                </g>
                <!-- Sensor Wave pulses -->
                <circle cx="200" cy="200" r="140" stroke="#14B8A6" stroke-width="1" opacity="0.25" stroke-dasharray="6 8" />
              </svg>
            </div>

            <!-- Attached Satellite Micro-CTA (Mastercard Signature docked bottom-right) -->
            <a href="#discovery" class="satellite-cta" aria-label="Explore spot availability" title="Find Parking">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>

          <!-- Secondary Overlapping Circular Image (160px) -->
          <div class="circle-portrait secondary-circle">
            <div class="portrait-inner">
              <svg viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="portrait-img">
                <rect width="160" height="160" fill="#111C2E" />
                <circle cx="80" cy="80" r="70" fill="var(--color-surface)" />
                <!-- Smartphone Digital Pass Graphic -->
                <g transform="translate(45, 30)">
                  <rect width="70" height="100" rx="12" fill="#0B1220" stroke="#14B8A6" stroke-width="2" />
                  <rect x="10" y="10" width="50" height="40" rx="6" fill="#14B8A6" fill-opacity="0.2" />
                  <!-- QR Code Mini Grid -->
                  <g fill="#14B8A6">
                    <rect x="18" y="16" width="8" height="8" rx="1" />
                    <rect x="44" y="16" width="8" height="8" rx="1" />
                    <rect x="18" y="34" width="8" height="8" rx="1" />
                    <rect x="32" y="24" width="6" height="6" />
                    <rect x="42" y="34" width="10" height="6" />
                  </g>
                  <text x="35" y="68" font-size="7" font-weight="bold" fill="#F1F5F9" text-anchor="middle">DIGITAL PASS</text>
                  <text x="35" y="80" font-size="6" fill="#9CAEC2" text-anchor="middle">Gate Opens Auto</text>
                </g>
              </svg>
            </div>
          </div>

          <!-- Floating Parking Availability Information Card (Stadium / Pill style) -->
          <div class="floating-availability-card">
            <div class="card-pulse-indicator">
              <span class="live-dot"></span>
              <span class="live-ping"></span>
            </div>
            <div class="card-info">
              <span class="card-title">Gulshan Avenue Garage</span>
              <span class="card-detail">14 Slots Free &bull; $1.50/hr &bull; 0.4 km</span>
            </div>
            <div class="card-badge">LIVE</div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero-section {
  position: relative;
  width: 100%;
  padding: 48px 24px 80px;
  overflow: hidden;
  background-color: var(--color-bg);
}

/* Watermark typography: Cream-on-cream / Dark-on-dark ghost text */
.hero-watermark {
  position: absolute;
  top: 10%;
  right: -5%;
  font-size: clamp(120px, 18vw, 240px);
  font-weight: 800;
  letter-spacing: -0.05em;
  color: var(--color-text);
  opacity: 0.03;
  line-height: 0.8;
  pointer-events: none;
  user-select: none;
  z-index: 1;
}

/* Thin Decorative Orbital Lines */
.orbital-svg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 2;
  overflow: visible;
}

.hero-container {
  position: relative;
  z-index: 3;
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  align-items: center;
  gap: 56px;
}

/* Left Content */
.hero-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.hero-eyebrow {
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
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.hero-headline {
  font-size: clamp(2.5rem, 5vw, 4.25rem);
  font-weight: 500;
  line-height: 1.05;
  letter-spacing: -0.03em;
  color: var(--color-text);
  margin: 0;
}

.hero-description {
  font-size: clamp(1.05rem, 1.4vw, 1.25rem);
  font-weight: 450;
  line-height: 1.5;
  color: var(--color-text-muted);
  max-width: 520px;
  margin: 0;
}

.hero-ctas {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-top: 8px;
}

.cta-primary {
  padding: 0 32px !important;
  font-weight: 600;
  font-size: 1rem;
}

.cta-secondary {
  padding: 0 28px !important;
  font-weight: 500;
  font-size: 1rem;
}

/* Stats */
.hero-stats {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 16px;
  padding-top: 24px;
  border-top: 1px solid var(--color-border);
}

.stat-pill {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-number {
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: var(--color-text);
}

.stat-label {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--color-text-muted);
}

.stat-divider {
  width: 1px;
  height: 28px;
  background-color: var(--color-border);
}

/* Visual Right Column */
.hero-visual {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
}

.visual-constellation {
  position: relative;
  width: 440px;
  height: 440px;
}

/* Primary Portrait Circle */
.circle-portrait {
  position: absolute;
  border-radius: 50%;
  overflow: visible;
  box-shadow: var(--shadow-halo);
  transition: transform var(--transition-smooth);
}

.circle-portrait:hover {
  transform: translateY(-4px);
}

.primary-circle {
  top: 10px;
  left: 20px;
  width: 360px;
  height: 360px;
  z-index: 2;
}

.portrait-inner {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid var(--color-surface);
  box-shadow: inset 0 0 0 1px var(--color-border);
}

.portrait-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Satellite Micro-CTA docked onto perimeter */
.satellite-cta {
  position: absolute;
  bottom: 16px;
  right: 16px;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background-color: var(--color-surface);
  color: var(--color-primary);
  border: 2px solid var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-elevation);
  cursor: pointer;
  text-decoration: none;
  z-index: 5;
  transition:
    transform var(--transition-fast),
    background-color var(--transition-fast),
    color var(--transition-fast);
}

.satellite-cta:hover {
  transform: scale(1.1) rotate(-8deg);
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  text-decoration: none;
}

/* Secondary Circle */
.secondary-circle {
  bottom: 0px;
  left: -20px;
  width: 160px;
  height: 160px;
  z-index: 3;
}

.secondary-circle .portrait-inner {
  border-width: 3px;
}

/* Floating Availability Info Card */
.floating-availability-card {
  position: absolute;
  bottom: 30px;
  right: -20px;
  z-index: 4;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 10px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: var(--shadow-card-elevated);
  backdrop-filter: blur(8px);
}

.card-pulse-indicator {
  position: relative;
  width: 10px;
  height: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.live-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--color-success);
}

.live-ping {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: var(--color-success);
  opacity: 0.6;
  animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
  75%, 100% {
    transform: scale(2.4);
    opacity: 0;
  }
}

.card-info {
  display: flex;
  flex-direction: column;
}

.card-title {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--color-text);
  line-height: 1.2;
}

.card-detail {
  font-size: 0.71875rem;
  color: var(--color-text-muted);
}

.card-badge {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: var(--radius-pill, 999px);
  background-color: rgba(22, 163, 74, 0.12);
  color: var(--color-success);
  letter-spacing: 0.05em;
}

/* Responsive */
@media (max-width: 1024px) {
  .hero-container {
    grid-template-columns: 1fr;
    gap: 48px;
    text-align: center;
  }

  .hero-content {
    align-items: center;
  }

  .hero-description {
    max-width: 600px;
  }

  .hero-ctas {
    justify-content: center;
  }

  .hero-stats {
    justify-content: center;
  }

  .visual-constellation {
    width: 360px;
    height: 360px;
  }

  .primary-circle {
    width: 300px;
    height: 300px;
  }

  .secondary-circle {
    width: 130px;
    height: 130px;
  }

  .floating-availability-card {
    right: 0;
    bottom: 10px;
  }
}

@media (max-width: 640px) {
  .hero-section {
    padding: 24px 16px 56px;
  }

  .orbital-svg {
    display: none; /* Keep mobile clean as specified in design doc */
  }

  .visual-constellation {
    width: 290px;
    height: 290px;
  }

  .primary-circle {
    width: 240px;
    height: 240px;
    left: 25px;
  }

  .secondary-circle {
    width: 100px;
    height: 100px;
    left: -10px;
    bottom: -10px;
  }

  .satellite-cta {
    width: 46px;
    height: 46px;
    bottom: 8px;
    right: 8px;
  }

  .floating-availability-card {
    position: relative;
    right: auto;
    bottom: auto;
    margin-top: 24px;
    width: 100%;
    justify-content: center;
  }
}
</style>

