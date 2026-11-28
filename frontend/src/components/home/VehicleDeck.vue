<script setup>
/**
 * VehicleDeck Component
 * True edge-to-edge, full-window scroll-driven card deck with 3D stacking physics for Parkly.
 * Section ID: #pricing
 *
 * Requirements:
 * 1. True Edge-to-Edge:
 *    - Full window width (100% of wrapper, no outer page margins, inset-inline: 0).
 *    - No horizontal scrollbars (guarded with overflow-x: clip).
 *    - Stage height: calc(100svh - var(--nav-h)).
 * 2. 3D Tuning:
 *    - Min scale on covered cards 0.97 (tuned above(i) * 0.02 instead of 0.05) to avoid visible side gaps.
 * 3. Theme match:
 *    - Light studio theme, clean teal-tint overlays, text contrast.
 */
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import SectionHeader from '../common/SectionHeader.vue'
import VehicleCard from './VehicleCard.vue'
import { useVehicleRanges } from '../../composables/useVehicleRanges'

gsap.registerPlugin(ScrollTrigger)

const { vehicleRanges, isLoading, error, fetchRanges } = useVehicleRanges()

const deckSectionRef = ref(null)
const stageRef = ref(null)
const scrollProgress = ref(0)
const isReducedMotion = ref(false)

let ctx = null

function clamp01(val) {
  return Math.min(1, Math.max(0, val))
}

function easeOut(t) {
  return 1 - Math.pow(1 - t, 3)
}

const ENTER = 0.72

function calculateCardTransforms(index, count, v) {
  if (count <= 1) {
    return {
      style: {
        transform: 'translateY(0) rotateX(0deg) scale(1)',
        filter: 'blur(0px)',
      },
      dimOpacity: 0,
    }
  }

  const span = Math.max(1, count - 1)

  const arrived = (j) => {
    if (j <= 0) return 1
    const raw = (v * span - (j - 1)) / ENTER
    return easeOut(clamp01(raw))
  }

  let sumAbove = 0
  for (let j = index + 1; j < count; j++) {
    sumAbove += arrived(j)
  }
  const above = Math.min(3, sumAbove)

  const cardArrived = arrived(index)

  // Edge-to-edge tuning:
  // Scale down on beneath cards is gentle (min ~0.97) so there are NO visible side gaps!
  const translateY = (1 - cardArrived) * 105 - above * 3
  const rotateX = -(1 - cardArrived) * 26 + above * 3
  const scale = 0.98 + 0.02 * cardArrived - above * 0.012

  // Blur cap: subtle for performance (max 8px)
  const blurPx = Math.min(8, Math.pow(1 - cardArrived, 2) * 8 + above * 0.8)
  const dimOpacity = Math.min(0.5, above * 0.12)

  return {
    style: {
      transform: `translate3d(0, ${translateY}svh, 0) rotateX(${rotateX}deg) scale(${scale})`,
      filter: blurPx > 0.3 ? `blur(${blurPx.toFixed(1)}px)` : 'none',
      zIndex: index + 1,
    },
    dimOpacity,
  }
}

const activeCardIndex = computed(() => {
  const count = vehicleRanges.value.length
  if (count <= 1) return 0
  return Math.min(count - 1, Math.floor(scrollProgress.value * count))
})

function scrollToCard(idx) {
  if (!deckSectionRef.value) return
  const count = vehicleRanges.value.length
  if (count <= 1) return

  const rect = deckSectionRef.value.getBoundingClientRect()
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  const containerTop = rect.top + scrollTop
  const containerHeight = deckSectionRef.value.offsetHeight
  const viewportHeight = window.innerHeight
  const scrollableDistance = Math.max(0, containerHeight - viewportHeight)

  const targetProgress = idx / Math.max(1, count - 1)
  const targetY = containerTop + (targetProgress * scrollableDistance)

  window.scrollTo({
    top: targetY,
    behavior: 'smooth',
  })
}

function handleLoad() {
  ScrollTrigger.refresh()
}

function initScrollTrigger() {
  if (isReducedMotion.value || !deckSectionRef.value || vehicleRanges.value.length === 0) return

  if (ctx) {
    try {
      ctx.revert()
    } catch {
      // Ignore
    }
  }

  ctx = gsap.context(() => {
    ScrollTrigger.create({
      trigger: deckSectionRef.value,
      start: 'top top',
      end: 'bottom bottom',
      scrub: 0.35,
      invalidateOnRefresh: true,
      onUpdate: (self) => {
        scrollProgress.value = self.progress
      },
    })
  }, deckSectionRef.value)
}

onMounted(async () => {
  await fetchRanges()

  isReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (isReducedMotion.value) return

  await nextTick()
  initScrollTrigger()

  window.addEventListener('load', handleLoad)
})

onBeforeUnmount(() => {
  try {
    window.removeEventListener('load', handleLoad)
    if (ctx) {
      ctx.revert()
      ctx = null
    }
  } catch (err) {
    console.warn('[VehicleDeck] Cleanup error:', err)
  }
})
</script>

<template>
  <section id="pricing" class="vehicle-deck-section">
    <!-- Static Header Outside Pinned Stage -->
    <div class="deck-static-header">
      <SectionHeader
        eyebrow="Vehicle Classes"
        title="Pay only for what you park"
        subtitle="Fees are calculated on the server from each facility's configured rates."
      />

      <!-- Interactive 4-Vehicle Classes Switcher Pills -->
      <div v-if="vehicleRanges.length > 0" class="vehicle-class-pills" role="tablist" aria-label="Vehicle Classes">
        <button
          v-for="(card, idx) in vehicleRanges"
          :key="card.id || idx"
          type="button"
          role="tab"
          :aria-selected="activeCardIndex === idx"
          class="class-pill-btn"
          :class="{ 'is-active': activeCardIndex === idx }"
          @click="scrollToCard(idx)"
        >
          <span class="pill-idx">0{{ idx + 1 }}</span>
          <span class="pill-name">{{ card.displayName }}</span>
          <span v-if="card.rate_display" class="pill-rate">{{ card.rate_display }}</span>
        </button>
      </div>
    </div>

    <!-- Rates Loading or Empty State -->
    <div v-if="isLoading" class="deck-state-card" role="status">
      <div class="state-spinner" aria-hidden="true"></div>
      <p class="state-text">Loading operator rates...</p>
    </div>

    <div v-else-if="vehicleRanges.length === 0" class="deck-state-card" role="status">
      <span class="state-eyebrow">OPERATOR RATES</span>
      <h3 class="state-title">Rates not published yet</h3>
      <p class="state-desc">
        Hourly rates are configured directly by registered parking operators. Check back shortly or explore available parking locations.
      </p>
      <button class="state-retry-btn" @click="fetchRanges">
        Check for updates
      </button>
    </div>

    <!-- Reduced Motion Vertical Fallback -->
    <div v-else-if="isReducedMotion" class="reduced-motion-deck">
      <div
        v-for="(card, idx) in vehicleRanges"
        :key="card.id || idx"
        class="reduced-motion-card-wrap"
      >
        <VehicleCard
          :card="card"
          :index="idx"
          :total="vehicleRanges.length"
          :is-eager="idx < 2"
        />
      </div>
    </div>

    <!-- Full-Window Scroll Deck with GSAP Pinned Stage -->
    <div
      v-else
      ref="deckSectionRef"
      class="deck-scroll-container"
      :style="{ height: `${Math.max(3, vehicleRanges.length) * 100 + 20}svh` }"
    >
      <div ref="stageRef" class="deck-sticky-stage">
        <!-- True Full-Width Stage Grid -->
        <div class="deck-cards-stage" role="region" aria-label="Vehicle classes card deck">
          <VehicleCard
            v-for="(card, idx) in vehicleRanges"
            :key="card.id || idx"
            :card="card"
            :index="idx"
            :total="vehicleRanges.length"
            :style-object="calculateCardTransforms(idx, vehicleRanges.length, scrollProgress).style"
            :dim-opacity="calculateCardTransforms(idx, vehicleRanges.length, scrollProgress).dimOpacity"
            :is-eager="idx < 2"
          />
        </div>

        <!-- Center Bottom Progress Indicator (Clickable Segments) -->
        <div class="deck-progress-bar" role="tablist" aria-label="Vehicle card pagination">
          <button
            v-for="(card, idx) in vehicleRanges"
            :key="idx"
            type="button"
            class="progress-segment"
            :class="{ 'is-active': activeCardIndex === idx }"
            :aria-current="activeCardIndex === idx ? 'step' : undefined"
            :aria-label="`Switch to ${card.displayName}`"
            @click="scrollToCard(idx)"
          >
            <div class="segment-fill"></div>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.vehicle-deck-section {
  position: relative;
  width: 100%;
  overflow-x: clip;
  background-color: var(--color-bg, #F8FAFC);
  scroll-margin-top: var(--nav-h, 72px);
  border-top: 1px solid var(--color-border, #E2E8F0);
}

.deck-static-header {
  padding: clamp(48px, 6vw, 64px) clamp(24px, 6vw, 96px) 24px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Interactive 4-Vehicle Classes Switcher Pills */
.vehicle-class-pills {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 24px;
}

.class-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: var(--radius-pill, 999px);
  background: var(--color-surface, #FFFFFF);
  border: 1.5px solid var(--color-border, #E2E8F0);
  cursor: pointer;
  transition: all var(--transition-fast, 0.15s ease);
  font-family: var(--font-sans);
  color: var(--color-text, #0F172A);
}

:global([data-theme='dark']) .class-pill-btn {
  background: rgba(30, 41, 59, 0.6);
  border-color: rgba(255, 255, 255, 0.12);
}

.class-pill-btn:hover {
  border-color: var(--color-primary, #0F766E);
  transform: translateY(-1px);
}

.class-pill-btn.is-active {
  background: var(--color-primary, #0F766E);
  border-color: var(--color-primary, #0F766E);
  color: #FFFFFF;
  box-shadow: 0 4px 14px rgba(15, 118, 110, 0.25);
}

.pill-idx {
  font-family: ui-monospace, "JetBrains Mono", monospace;
  font-size: 0.75rem;
  font-weight: 700;
  opacity: 0.8;
}

.pill-name {
  font-size: 0.875rem;
  font-weight: 600;
}

.pill-rate {
  font-size: 0.75rem;
  padding: 2px 8px;
  border-radius: 999px;
  background: rgba(15, 118, 110, 0.1);
  color: var(--color-primary, #0F766E);
  font-weight: 600;
}

.class-pill-btn.is-active .pill-rate {
  background: rgba(255, 255, 255, 0.2);
  color: #FFFFFF;
}

/* Outer Scroll Height = cards * 100svh + ~20vh dwell */
.deck-scroll-container {
  position: relative;
  width: 100%;
}

/* True Edge-to-Edge Sticky Stage */
.deck-sticky-stage {
  position: sticky;
  top: var(--nav-h, 72px);
  height: calc(100svh - var(--nav-h, 72px));
  min-height: 520px;
  width: 100%;
  overflow: hidden;
  perspective: 1800px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin: 0;
}

/* 100% Edge-to-Edge Stage Cell */
.deck-cards-stage {
  position: relative;
  width: 100%;
  height: 100%;
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  margin: 0;
  padding: 0;
}

.deck-cards-stage > * {
  grid-area: 1 / 1;
}

/* Center Bottom Progress Bar */
.deck-progress-bar {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 25;
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid var(--color-border, #E2E8F0);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}

:global([data-theme='dark']) .deck-progress-bar {
  background: rgba(11, 18, 32, 0.85);
  border-color: rgba(255, 255, 255, 0.12);
}

.progress-segment {
  width: 28px;
  height: 6px;
  border-radius: 999px;
  background: var(--color-border, #E2E8F0);
  border: none;
  padding: 0;
  cursor: pointer;
  overflow: hidden;
  transition: all var(--transition-fast);
}

:global([data-theme='dark']) .progress-segment {
  background: rgba(255, 255, 255, 0.2);
}

.progress-segment.is-active {
  width: 44px;
  background: var(--color-primary, #0F766E);
}

/* Reduced Motion Mode */
.reduced-motion-deck {
  width: 100%;
  padding: 24px 0 80px;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.reduced-motion-card-wrap {
  position: relative;
  width: 100%;
  height: 85svh;
  overflow: hidden;
}

@media (max-width: 768px) {
  .deck-static-header {
    padding: 48px 20px 20px;
  }

  .deck-progress-bar {
    bottom: 16px;
  }
}

/* Empty & Loading States */
.deck-state-card {
  max-width: 640px;
  margin: 40px auto 80px;
  padding: 48px 32px;
  text-align: center;
  background: var(--color-surface, #FFFFFF);
  border: 1px solid var(--color-border, #E2E8F0);
  border-radius: var(--radius-2xl, 24px);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
}

:global([data-theme='dark']) .deck-state-card {
  background: rgba(30, 41, 59, 0.5);
  border-color: rgba(255, 255, 255, 0.1);
}

.state-eyebrow {
  font-family: ui-monospace, "JetBrains Mono", monospace;
  font-size: 0.6875rem;
  letter-spacing: 0.14em;
  font-weight: 600;
  color: var(--color-primary, #0F766E);
  text-transform: uppercase;
}

.state-title {
  font-family: var(--font-serif, "Instrument Serif", Georgia, serif);
  font-size: 2rem;
  font-weight: 500;
  color: var(--color-text, #0F172A);
  margin: 0;
}

.state-desc {
  font-size: 0.9375rem;
  line-height: 1.5;
  color: var(--color-muted, #64748B);
  max-width: 460px;
  margin: 0;
}

.state-retry-btn {
  margin-top: 8px;
  padding: 10px 24px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #FFFFFF;
  background-color: var(--color-primary, #0F766E);
  border: none;
  border-radius: var(--radius-pill, 999px);
  cursor: pointer;
  transition: opacity var(--transition-fast, 0.15s ease);
}

.state-retry-btn:hover {
  opacity: 0.9;
}

.state-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid var(--color-border, #E2E8F0);
  border-top-color: var(--color-primary, #0F766E);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.state-text {
  font-size: 0.9375rem;
  color: var(--color-muted, #64748B);
  margin: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
