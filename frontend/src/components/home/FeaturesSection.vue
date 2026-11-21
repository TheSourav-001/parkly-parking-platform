<script setup>
/**
 * FeaturesSection Component — Premium Bento Grid with Animated Illustrations
 * 
 * Design highlights:
 * - Bento grid layout (2 large + 2 standard cards)
 * - Animated gradient borders with glow on hover
 * - Glassmorphism card surfaces
 * - Unique SVG micro-animations per card
 * - Connected step flow with gradient connector
 * - Background decorative grid + gradient blobs
 * - Full dark/light mode support
 * - Fully responsive
 */
import { ref, onMounted, onBeforeUnmount } from 'vue'

const features = [
  {
    id: 'location',
    step: '01',
    title: 'Location-Based Search',
    tag: 'GEO-DISCOVERY',
    description: 'Find verified parking facilities nearest to your current location or target destination with real-time GPS accuracy.',
    iconType: 'map-pin',
    highlight: 'Real-time GPS',
  },
  {
    id: 'ai',
    step: '02',
    title: 'AI-Powered Recommendations',
    tag: 'INTELLIGENCE',
    description: 'Gemini-assisted intelligent matching analyzes past parking availability, traffic flows, and your vehicle requirements.',
    iconType: 'sparkles',
    highlight: 'Gemini AI',
  },
  {
    id: 'pricing',
    step: '03',
    title: 'Transparent Pricing',
    tag: 'FAIR RATES',
    description: 'No surprise surge fees. Upfront hourly and daily rates with verified vehicle-tier pricing before you navigate.',
    iconType: 'receipt',
    highlight: 'No hidden fees',
  },
  {
    id: 'booking',
    step: '04',
    title: 'Easy Online Booking',
    tag: 'SEAMLESS PASS',
    description: 'Reserve your spot in two taps. Instant digital pass with QR code verification for automated gate check-in.',
    iconType: 'ticket',
    highlight: 'QR Pass',
  },
]

// Intersection Observer for scroll-triggered animations
const sectionRef = ref(null)
const isVisible = ref(false)
let observer = null

onMounted(() => {
  observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        isVisible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.15 }
  )
  if (sectionRef.value) observer.observe(sectionRef.value)
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
})
</script>

<template>
  <section
    ref="sectionRef"
    class="features-section"
    id="features"
    :class="{ 'is-visible': isVisible }"
  >
    <!-- Background Decorations -->
    <div class="bg-decorations" aria-hidden="true">
      <div class="gradient-blob blob-1"></div>
      <div class="gradient-blob blob-2"></div>
      <div class="grid-pattern"></div>
    </div>

    <div class="features-container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="eyebrow">
          <span class="eyebrow-line" aria-hidden="true"></span>
          <span class="eyebrow-text">PLATFORM ARCHITECTURE</span>
          <span class="eyebrow-line" aria-hidden="true"></span>
        </div>
        <h2 class="section-title">
          Designed for frictionless
          <span class="title-gradient">urban mobility.</span>
        </h2>
        <p class="section-subtitle">
          Parkly replaces circling the block with instant discovery, transparent rates, and guaranteed spots.
        </p>
      </div>

      <!-- Step Flow Connector (Desktop) -->
      <div class="step-flow" aria-hidden="true">
        <div class="flow-track">
          <div class="flow-progress"></div>
        </div>
        <div
          v-for="(feat, index) in features"
          :key="'step-' + feat.id"
          class="flow-node"
          :style="{ left: (index * 33.33) + '%' }"
        >
          <div class="flow-dot">
            <span class="flow-number">{{ feat.step }}</span>
          </div>
          <span class="flow-label">{{ feat.tag }}</span>
        </div>
      </div>

      <!-- Bento Grid -->
      <div class="bento-grid">
        <!-- Card 1: Location (Large) -->
        <div class="bento-card bento-large card-reveal" style="--delay: 0">
          <div class="card-glow" aria-hidden="true"></div>
          <div class="card-inner">
            <div class="card-badge">
              <span class="badge-dot"></span>
              {{ features[0].tag }}
            </div>

            <div class="card-content">
              <div class="card-text">
                <h3 class="card-title">{{ features[0].title }}</h3>
                <p class="card-description">{{ features[0].description }}</p>
                <div class="card-highlight">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  {{ features[0].highlight }}
                </div>
              </div>

              <!-- Map Pin Illustration -->
              <div class="card-illustration">
                <svg class="illust-map" viewBox="0 0 160 160" fill="none">
                  <!-- Ripple rings -->
                  <circle class="ripple r1" cx="80" cy="85" r="55" stroke="var(--color-primary)" stroke-width="1" opacity="0.15"/>
                  <circle class="ripple r2" cx="80" cy="85" r="40" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.25"/>
                  <circle class="ripple r3" cx="80" cy="85" r="25" stroke="var(--color-primary)" stroke-width="2" opacity="0.35"/>
                  <!-- Pin -->
                  <g class="pin-group">
                    <path d="M80 30 C62 30 48 44 48 62 C48 85 80 115 80 115 C80 115 112 85 112 62 C112 44 98 30 80 30Z" fill="var(--color-primary)" opacity="0.9"/>
                    <circle cx="80" cy="60" r="12" fill="var(--color-primary-contrast)" opacity="0.9"/>
                    <circle cx="80" cy="60" r="5" fill="var(--color-primary)"/>
                  </g>
                  <!-- Small dots representing nearby locations -->
                  <circle class="loc-dot d1" cx="45" cy="110" r="3" fill="var(--color-primary)" opacity="0.4"/>
                  <circle class="loc-dot d2" cx="120" cy="100" r="2.5" fill="var(--color-primary)" opacity="0.3"/>
                  <circle class="loc-dot d3" cx="30" cy="75" r="2" fill="var(--color-primary)" opacity="0.25"/>
                  <circle class="loc-dot d4" cx="130" cy="70" r="2.5" fill="var(--color-primary)" opacity="0.35"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2: AI (Large) -->
        <div class="bento-card bento-large card-reveal" style="--delay: 1">
          <div class="card-glow" aria-hidden="true"></div>
          <div class="card-inner">
            <div class="card-badge">
              <span class="badge-dot"></span>
              {{ features[1].tag }}
            </div>

            <div class="card-content">
              <div class="card-text">
                <h3 class="card-title">{{ features[1].title }}</h3>
                <p class="card-description">{{ features[1].description }}</p>
                <div class="card-highlight">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                  {{ features[1].highlight }}
                </div>
              </div>

              <!-- AI Brain/Sparkles Illustration -->
              <div class="card-illustration">
                <svg class="illust-ai" viewBox="0 0 160 160" fill="none">
                  <!-- Central neural node -->
                  <circle class="neural-core" cx="80" cy="80" r="20" fill="var(--color-primary)" opacity="0.15" stroke="var(--color-primary)" stroke-width="1.5"/>
                  <circle cx="80" cy="80" r="8" fill="var(--color-primary)" opacity="0.8"/>
                  <!-- Neural connections -->
                  <line class="neural-line nl1" x1="80" y1="60" x2="55" y2="35" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.4" stroke-dasharray="3 3"/>
                  <line class="neural-line nl2" x1="80" y1="60" x2="105" y2="35" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.4" stroke-dasharray="3 3"/>
                  <line class="neural-line nl3" x1="100" y1="80" x2="130" y2="65" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.3" stroke-dasharray="3 3"/>
                  <line class="neural-line nl4" x1="60" y1="80" x2="30" y2="65" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.3" stroke-dasharray="3 3"/>
                  <line class="neural-line nl5" x1="80" y1="100" x2="55" y2="130" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.3" stroke-dasharray="3 3"/>
                  <line class="neural-line nl6" x1="80" y1="100" x2="110" y2="125" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.3" stroke-dasharray="3 3"/>
                  <!-- Outer nodes -->
                  <circle class="node n1" cx="55" cy="35" r="6" fill="var(--color-primary)" opacity="0.5"/>
                  <circle class="node n2" cx="105" cy="35" r="5" fill="var(--color-primary)" opacity="0.4"/>
                  <circle class="node n3" cx="130" cy="65" r="4" fill="var(--color-primary)" opacity="0.35"/>
                  <circle class="node n4" cx="30" cy="65" r="5" fill="var(--color-primary)" opacity="0.4"/>
                  <circle class="node n5" cx="55" cy="130" r="4" fill="var(--color-primary)" opacity="0.3"/>
                  <circle class="node n6" cx="110" cy="125" r="5" fill="var(--color-primary)" opacity="0.4"/>
                  <!-- Sparkle accents -->
                  <g class="sparkle sp1" transform="translate(120,40)">
                    <path d="M0-6 L1.5 -1.5 L6 0 L1.5 1.5 L0 6 L-1.5 1.5 L-6 0 L-1.5-1.5Z" fill="var(--color-primary)" opacity="0.6"/>
                  </g>
                  <g class="sparkle sp2" transform="translate(35,110)">
                    <path d="M0-5 L1.2 -1.2 L5 0 L1.2 1.2 L0 5 L-1.2 1.2 L-5 0 L-1.2-1.2Z" fill="var(--color-primary)" opacity="0.5"/>
                  </g>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Pricing (Standard) -->
        <div class="bento-card bento-standard card-reveal" style="--delay: 2">
          <div class="card-glow" aria-hidden="true"></div>
          <div class="card-inner">
            <div class="card-badge">
              <span class="badge-dot"></span>
              {{ features[2].tag }}
            </div>

            <h3 class="card-title">{{ features[2].title }}</h3>
            <p class="card-description">{{ features[2].description }}</p>

            <!-- Pricing Bars Illustration -->
            <div class="card-illustration illust-compact">
              <svg class="illust-pricing" viewBox="0 0 140 80" fill="none">
                <rect class="bar b1" x="10" y="50" width="18" height="25" rx="4" fill="var(--color-primary)" opacity="0.25"/>
                <rect class="bar b2" x="35" y="35" width="18" height="40" rx="4" fill="var(--color-primary)" opacity="0.4"/>
                <rect class="bar b3" x="60" y="20" width="18" height="55" rx="4" fill="var(--color-primary)" opacity="0.6"/>
                <rect class="bar b4" x="85" y="10" width="18" height="65" rx="4" fill="var(--color-primary)" opacity="0.8"/>
                <!-- Dollar sign -->
                <text x="120" y="50" font-size="24" font-weight="700" fill="var(--color-primary)" opacity="0.3" font-family="var(--font-sans)">$</text>
                <!-- Checkmark -->
                <g transform="translate(112, 12)">
                  <circle r="10" fill="var(--color-primary)" opacity="0.15"/>
                  <path d="M-4 0 L-1 3 L5 -3" stroke="var(--color-primary)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
                </g>
              </svg>
            </div>

            <div class="card-highlight">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              {{ features[2].highlight }}
            </div>
          </div>
        </div>

        <!-- Card 4: Booking (Standard) -->
        <div class="bento-card bento-standard card-reveal" style="--delay: 3">
          <div class="card-glow" aria-hidden="true"></div>
          <div class="card-inner">
            <div class="card-badge">
              <span class="badge-dot"></span>
              {{ features[3].tag }}
            </div>

            <h3 class="card-title">{{ features[3].title }}</h3>
            <p class="card-description">{{ features[3].description }}</p>

            <!-- QR / Ticket Illustration -->
            <div class="card-illustration illust-compact">
              <svg class="illust-booking" viewBox="0 0 140 80" fill="none">
                <!-- Ticket shape -->
                <rect class="ticket-bg" x="10" y="10" width="85" height="60" rx="10" fill="var(--color-primary)" opacity="0.1" stroke="var(--color-primary)" stroke-width="1" stroke-dasharray="4 2"/>
                <!-- QR code simplified -->
                <g transform="translate(25, 22)" class="qr-group">
                  <rect width="8" height="8" fill="var(--color-primary)" opacity="0.6"/>
                  <rect x="10" width="8" height="8" fill="var(--color-primary)" opacity="0.4"/>
                  <rect x="20" width="8" height="8" fill="var(--color-primary)" opacity="0.7"/>
                  <rect y="10" width="8" height="8" fill="var(--color-primary)" opacity="0.3"/>
                  <rect x="10" y="10" width="8" height="8" fill="var(--color-primary)" opacity="0.6"/>
                  <rect x="20" y="10" width="8" height="8" fill="var(--color-primary)" opacity="0.5"/>
                  <rect y="20" width="8" height="8" fill="var(--color-primary)" opacity="0.5"/>
                  <rect x="10" y="20" width="8" height="8" fill="var(--color-primary)" opacity="0.4"/>
                  <rect x="20" y="20" width="8" height="8" fill="var(--color-primary)" opacity="0.6"/>
                </g>
                <!-- Scan line -->
                <line class="scan-line" x1="22" y1="22" x2="22" y2="58" stroke="var(--color-primary)" stroke-width="2" opacity="0.5"/>
                <!-- Checkmark badge -->
                <g transform="translate(115, 40)">
                  <circle r="14" fill="var(--color-primary)" opacity="0.15"/>
                  <circle r="10" fill="var(--color-primary)" opacity="0.25"/>
                  <path d="M-4 0 L-1 3 L5 -3" stroke="var(--color-primary-contrast)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
              </svg>
            </div>

            <div class="card-highlight">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              {{ features[3].highlight }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* ======================================================================
   FEATURES SECTION — Premium Bento Grid
   ====================================================================== */
.features-section {
  position: relative;
  width: 100%;
  padding: 100px 24px;
  background-color: var(--color-bg);
  overflow: hidden;
}

/* Background Decorations */
.bg-decorations {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 0;
}

.gradient-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.4;
}

.blob-1 {
  width: 500px;
  height: 500px;
  top: -100px;
  right: -150px;
  background: radial-gradient(circle, var(--color-primary) 0%, transparent 70%);
  opacity: 0.07;
}

.blob-2 {
  width: 400px;
  height: 400px;
  bottom: -80px;
  left: -100px;
  background: radial-gradient(circle, var(--color-primary) 0%, transparent 70%);
  opacity: 0.05;
}

.grid-pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, var(--color-border) 1px, transparent 1px);
  background-size: 40px 40px;
  opacity: 0.3;
  mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 20%, transparent 80%);
  -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 20%, transparent 80%);
}

/* Container */
.features-container {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 48px;
}

/* ── Section Header ── */
.section-header {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  max-width: 720px;
  margin: 0 auto;
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.eyebrow-line {
  display: block;
  width: 32px;
  height: 1.5px;
  background: linear-gradient(90deg, transparent, var(--color-primary));
}

.eyebrow-line:last-child {
  background: linear-gradient(90deg, var(--color-primary), transparent);
}

.section-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  line-height: 1.12;
  letter-spacing: -0.03em;
  color: var(--color-text);
  margin: 0;
}

.title-gradient {
  background: linear-gradient(135deg, var(--color-primary) 0%, #2DD4BF 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--color-text-muted);
  line-height: 1.6;
  margin: 0;
  max-width: 560px;
}

/* ── Step Flow (Desktop Connector) ── */
.step-flow {
  position: relative;
  height: 72px;
  margin: 0 60px;
}

.flow-track {
  position: absolute;
  top: 16px;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--color-border);
  border-radius: 2px;
}

.flow-progress {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--color-primary), #2DD4BF);
  border-radius: 2px;
  transition: width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.is-visible .flow-progress {
  width: 100%;
}

.flow-node {
  position: absolute;
  top: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  transform: translateX(-50%);
}

.flow-node:first-child {
  left: 0% !important;
  transform: translateX(0);
}

.flow-node:last-child {
  left: 100% !important;
  transform: translateX(-100%);
}

.flow-dot {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--color-surface);
  border: 2px solid var(--color-border);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.4s, background-color 0.4s, box-shadow 0.4s;
  position: relative;
  z-index: 1;
}

.is-visible .flow-dot {
  border-color: var(--color-primary);
  background: var(--color-primary);
  box-shadow: 0 0 16px rgba(20, 184, 166, 0.3);
}

.flow-number {
  font-size: 0.6875rem;
  font-weight: 700;
  font-family: var(--font-mono);
  color: var(--color-text-subtle);
  transition: color 0.4s;
}

.is-visible .flow-number {
  color: var(--color-primary-contrast);
}

.flow-label {
  font-size: 0.625rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--color-text-subtle);
  white-space: nowrap;
  transition: color 0.4s;
}

.is-visible .flow-label {
  color: var(--color-primary);
}

/* ── Bento Grid ── */
.bento-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

/* ── Card Base ── */
.bento-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.bento-card:hover {
  transform: translateY(-6px);
}

/* Animated gradient border */
.card-glow {
  position: absolute;
  inset: 0;
  border-radius: 24px;
  padding: 1.5px;
  background: linear-gradient(
    135deg,
    var(--color-border) 0%,
    var(--color-border) 100%
  );
  -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  transition: background 0.5s;
}

.bento-card:hover .card-glow {
  background: linear-gradient(
    135deg,
    var(--color-primary) 0%,
    #2DD4BF 40%,
    var(--color-primary) 80%,
    #2DD4BF 100%
  );
  background-size: 300% 300%;
  animation: shimmer-border 3s linear infinite;
}

@keyframes shimmer-border {
  0% { background-position: 0% 0%; }
  100% { background-position: 300% 300%; }
}

.card-inner {
  position: relative;
  z-index: 1;
  height: 100%;
  padding: 32px 28px;
  border-radius: 23px;
  background: var(--color-surface);
  display: flex;
  flex-direction: column;
  gap: 16px;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

:root[data-theme='dark'] .card-inner {
  background: rgba(17, 28, 46, 0.85);
}

/* Hover inner glow */
.bento-card:hover .card-inner {
  box-shadow: inset 0 1px 0 rgba(20, 184, 166, 0.1),
              0 20px 50px -15px rgba(0, 0, 0, 0.15);
}

:root[data-theme='dark'] .bento-card:hover .card-inner {
  box-shadow: inset 0 1px 0 rgba(20, 184, 166, 0.15),
              0 20px 50px -15px rgba(0, 0, 0, 0.5),
              0 0 60px -20px rgba(20, 184, 166, 0.12);
}

/* ── Card Badge ── */
.card-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--color-primary);
  text-transform: uppercase;
  background: var(--color-primary-light);
  padding: 5px 12px;
  border-radius: var(--radius-pill);
  width: fit-content;
}

.badge-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: var(--color-primary);
  animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.7); }
}

/* ── Large Card Layout ── */
.bento-large .card-content {
  display: flex;
  align-items: center;
  gap: 24px;
  flex: 1;
}

.bento-large .card-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 0;
}

/* ── Card Typography ── */
.card-title {
  font-size: 1.4rem;
  font-weight: 600;
  line-height: 1.2;
  letter-spacing: -0.02em;
  color: var(--color-text);
  margin: 0;
}

.bento-standard .card-title {
  font-size: 1.2rem;
}

.card-description {
  font-size: 0.9375rem;
  font-weight: 400;
  line-height: 1.55;
  color: var(--color-text-muted);
  margin: 0;
}

/* ── Card Highlight (CTA hint) ── */
.card-highlight {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--color-primary);
  margin-top: 4px;
  transition: gap 0.3s;
}

.bento-card:hover .card-highlight {
  gap: 10px;
}

/* ── Card Illustrations ── */
.card-illustration {
  flex-shrink: 0;
  width: 140px;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.illust-compact {
  width: 100%;
  height: 80px;
  margin-top: auto;
}

.illust-compact svg {
  width: 100%;
  height: 100%;
}

.card-illustration svg {
  width: 100%;
  height: 100%;
}

/* ── SVG Animations ── */

/* Map Pin Ripples */
.ripple {
  transform-origin: center;
  animation: ripple-expand 3s ease-out infinite;
}
.r1 { animation-delay: 0s; }
.r2 { animation-delay: 1s; }
.r3 { animation-delay: 2s; }

@keyframes ripple-expand {
  0% { transform: scale(0.8); opacity: 0.4; }
  100% { transform: scale(1.3); opacity: 0; }
}

.pin-group {
  animation: pin-float 3s ease-in-out infinite;
}

@keyframes pin-float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}

.loc-dot {
  animation: dot-blink 2s ease-in-out infinite;
}
.d1 { animation-delay: 0.2s; }
.d2 { animation-delay: 0.7s; }
.d3 { animation-delay: 1.1s; }
.d4 { animation-delay: 1.5s; }

@keyframes dot-blink {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.7; }
}

/* AI Neural Pulses */
.neural-core {
  animation: core-pulse 2.5s ease-in-out infinite;
}

@keyframes core-pulse {
  0%, 100% { r: 20; opacity: 0.15; }
  50% { r: 24; opacity: 0.25; }
}

.neural-line {
  animation: line-dash 4s linear infinite;
}

@keyframes line-dash {
  0% { stroke-dashoffset: 0; }
  100% { stroke-dashoffset: 24; }
}

.node {
  animation: node-pulse 2.5s ease-in-out infinite;
}
.n1 { animation-delay: 0s; }
.n2 { animation-delay: 0.4s; }
.n3 { animation-delay: 0.8s; }
.n4 { animation-delay: 1.2s; }
.n5 { animation-delay: 1.6s; }
.n6 { animation-delay: 2s; }

@keyframes node-pulse {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.3); }
}

.sparkle {
  animation: sparkle-twinkle 2s ease-in-out infinite;
}
.sp1 { animation-delay: 0.3s; }
.sp2 { animation-delay: 1.2s; }

@keyframes sparkle-twinkle {
  0%, 100% { opacity: 0.3; transform: scale(0.8) rotate(0deg); }
  50% { opacity: 0.8; transform: scale(1.2) rotate(15deg); }
}

/* Pricing Bars */
.bar {
  animation: bar-grow 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
  transform-origin: bottom;
}
.b1 { animation-delay: 0.2s; }
.b2 { animation-delay: 0.4s; }
.b3 { animation-delay: 0.6s; }
.b4 { animation-delay: 0.8s; }

.is-visible .bar {
  animation-name: bar-grow;
}

@keyframes bar-grow {
  0% { transform: scaleY(0); }
  100% { transform: scaleY(1); }
}

/* QR Scan Line */
.scan-line {
  animation: scan-sweep 2.5s ease-in-out infinite;
}

@keyframes scan-sweep {
  0%, 100% { transform: translateX(0); opacity: 0.3; }
  50% { transform: translateX(35px); opacity: 0.7; }
}

.qr-group rect {
  animation: qr-fade 3s ease-in-out infinite;
}

.qr-group rect:nth-child(odd) {
  animation-delay: 0.3s;
}

@keyframes qr-fade {
  0%, 100% { opacity: 0.4; }
  50% { opacity: 0.8; }
}

/* ── Scroll-triggered Card Reveal ── */
.card-reveal {
  opacity: 0;
  transform: translateY(40px);
  transition:
    opacity 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94),
    transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  transition-delay: calc(var(--delay, 0) * 0.15s);
}

.is-visible .card-reveal {
  opacity: 1;
  transform: translateY(0);
}

/* ── Responsive ── */
@media (max-width: 1080px) {
  .step-flow {
    display: none;
  }

  .bento-grid {
    gap: 16px;
  }

  .bento-large .card-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .card-illustration {
    width: 120px;
    height: 120px;
  }
}

@media (max-width: 768px) {
  .features-section {
    padding: 72px 16px;
  }

  .bento-grid {
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .bento-large .card-content {
    flex-direction: row;
    align-items: center;
  }

  .card-illustration {
    width: 100px;
    height: 100px;
  }

  .card-inner {
    padding: 24px 20px;
  }

  .card-title {
    font-size: 1.2rem;
  }

  .bento-standard .card-title {
    font-size: 1.1rem;
  }

  .section-title {
    font-size: clamp(1.6rem, 5vw, 2.2rem);
  }
}

@media (max-width: 480px) {
  .bento-large .card-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .card-illustration {
    width: 100%;
    height: 100px;
  }

  .eyebrow-line {
    width: 20px;
  }
}

/* ── Reduced Motion ── */
@media (prefers-reduced-motion: reduce) {
  .ripple,
  .pin-group,
  .loc-dot,
  .neural-core,
  .neural-line,
  .node,
  .sparkle,
  .bar,
  .scan-line,
  .qr-group rect,
  .badge-dot {
    animation: none !important;
  }

  .card-reveal {
    opacity: 1;
    transform: none;
    transition: none;
  }

  .flow-progress {
    transition: none;
    width: 100%;
  }
}
</style>
