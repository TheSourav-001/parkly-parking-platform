<script setup>
/**
 * AppSplashScreen Component
 * Modern, clean loading screen for Parkly.
 *
 * Requirements:
 * 1. Car drives full window width from outside left to outside right.
 * 2. Brand logo and name reveal with the car as it drives across.
 * 3. Uses the green car provided by the user.
 * 4. Percentage progress below road line.
 */
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import logoUrl from '../../assets/logo.png'
import carUrl from '../../assets/car-green.png'

const SESSION_KEY = 'parkly_splash_seen'
const MIN_DISPLAY_TIME_MS = 1600
const DURATION_MS = 2200
const CAR_WIDTH = 140

const props = defineProps({
  ready: {
    type: Boolean,
    default: null,
  },
  alwaysShow: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['done'])

function checkAlreadySeen() {
  if (props.alwaysShow) return false
  try {
    return sessionStorage.getItem(SESSION_KEY) === 'true'
  } catch {
    return false
  }
}

function markAsSeen() {
  if (props.alwaysShow) return
  try {
    sessionStorage.setItem(SESSION_KEY, 'true')
  } catch {
    // Gracefully handle storage errors
  }
}

const isVisible = ref(!checkAlreadySeen())
const isExiting = ref(false)
const progress = ref(0) // 0 to 1
const displayPercentage = computed(() => Math.round(progress.value * 100))

// Measurements & Positions
const brandRowRef = ref(null)
const viewportWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1200)
const brandRect = ref({ left: 0, right: 0, width: 0 })

const carX = ref(-CAR_WIDTH)
const coverTranslateX = ref(0)

let rAFId = null
let exitTimeoutId = null
let holdTimeoutId = null
let startTimestamp = null
let initialOverflowStyle = ''
let isHoldingAtEighty = false
let approachStartTime = null

function lockScroll() {
  if (typeof document === 'undefined') return
  initialOverflowStyle = document.documentElement.style.overflow
  document.documentElement.style.overflow = 'hidden'
}

function unlockScroll() {
  if (typeof document === 'undefined') return
  document.documentElement.style.overflow = initialOverflowStyle || ''
}

function easeInOutCubic(t) {
  return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2
}

function easeOutQuad(t) {
  return t * (2 - t)
}

function measureElements() {
  if (typeof window === 'undefined') return
  viewportWidth.value = window.innerWidth
  if (brandRowRef.value) {
    const rect = brandRowRef.value.getBoundingClientRect()
    brandRect.value = {
      left: rect.left,
      right: rect.right,
      width: rect.width,
    }
  }
  updatePositions(progress.value)
}

function updatePositions(p) {
  // Car traverses full window width from outside left to outside right
  const totalTravel = viewportWidth.value + 2 * CAR_WIDTH
  const currentCarX = -CAR_WIDTH + p * totalTravel
  carX.value = currentCarX

  // Synchronize cover with the rear edge of the car
  // When the rear of the car reaches brand left, slide the cover to the right
  if (brandRect.value.width > 0) {
    const carRearX = currentCarX // rear of car
    const revealOffset = Math.max(0, Math.min(brandRect.value.width, carRearX - brandRect.value.left))
    coverTranslateX.value = revealOffset
  } else {
    coverTranslateX.value = 0
  }
}

function completeAndExit() {
  progress.value = 1
  updatePositions(1)
  markAsSeen()

  holdTimeoutId = setTimeout(() => {
    isExiting.value = true
    exitTimeoutId = setTimeout(() => {
      isVisible.value = false
      unlockScroll()
      emit('done')
    }, 450)
  }, 350)
}

function animationLoop(timestamp) {
  if (!startTimestamp) startTimestamp = timestamp
  const elapsed = timestamp - startTimestamp

  if (props.ready !== null) {
    if (!props.ready) {
      const approachDuration = 900
      const t = Math.min(1, elapsed / approachDuration)
      progress.value = easeOutQuad(t) * 0.8
      updatePositions(progress.value)
      isHoldingAtEighty = t >= 1
      rAFId = requestAnimationFrame(animationLoop)
      return
    } else {
      if (!approachStartTime) approachStartTime = timestamp
      const finishElapsed = timestamp - approachStartTime
      const remainingTime = Math.max(400, MIN_DISPLAY_TIME_MS - elapsed)
      const t = Math.min(1, finishElapsed / remainingTime)
      const startP = isHoldingAtEighty ? 0.8 : progress.value
      progress.value = startP + (1 - startP) * easeInOutCubic(t)
      updatePositions(progress.value)

      if (t < 1) {
        rAFId = requestAnimationFrame(animationLoop)
      } else {
        completeAndExit()
      }
      return
    }
  }

  const linearRatio = Math.min(1, elapsed / DURATION_MS)
  const p = easeInOutCubic(linearRatio)
  progress.value = p
  updatePositions(p)

  if (linearRatio < 1) {
    rAFId = requestAnimationFrame(animationLoop)
  } else {
    completeAndExit()
  }
}

watch(
  () => props.ready,
  (newVal) => {
    if (newVal === true && !startTimestamp) {
      // Loop picks it up
    }
  }
)

onMounted(() => {
  if (!isVisible.value) {
    emit('done')
    return
  }

  lockScroll()
  measureElements()
  window.addEventListener('resize', measureElements)

  // Reduced motion
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (prefersReduced) {
    progress.value = 1
    if (brandRowRef.value) {
      coverTranslateX.value = brandRect.value.width || 1000
    }
    markAsSeen()
    exitTimeoutId = setTimeout(() => {
      isExiting.value = true
      setTimeout(() => {
        isVisible.value = false
        unlockScroll()
        emit('done')
      }, 300)
    }, 400)
    return
  }

  rAFId = requestAnimationFrame(animationLoop)
})

onBeforeUnmount(() => {
  if (rAFId) cancelAnimationFrame(rAFId)
  if (exitTimeoutId) clearTimeout(exitTimeoutId)
  if (holdTimeoutId) clearTimeout(holdTimeoutId)
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', measureElements)
  }
  unlockScroll()
})
</script>

<template>
  <div
    v-if="isVisible"
    class="parkly-splash-curtain"
    :class="{ 'is-exiting': isExiting }"
    role="status"
    aria-live="polite"
    aria-label="Loading Parkly"
  >
    <span class="sr-only">Loading Parkly: {{ displayPercentage }}%</span>

    <div class="splash-stage">
      <!-- 1. BRAND ROW (REVEALS AS CAR PASSES OVER) -->
      <div class="brand-reveal-wrapper">
        <div ref="brandRowRef" class="brand-row">
          <img
            :src="logoUrl"
            alt="Parkly Logo"
            class="brand-logo"
          />
          <span class="brand-name">Parkly</span>
        </div>

        <!-- Cover element matching background color that slides right with the car -->
        <div
          class="brand-reveal-cover"
          :style="{ transform: `translateX(${coverTranslateX}px)` }"
          aria-hidden="true"
        ></div>
      </div>

      <!-- 2. FULL WINDOW WIDTH ROAD & MOVING GREEN CAR -->
      <div class="road-track" aria-hidden="true">
        <!-- Full-width Road Line with Green Progress Fill -->
        <div class="road-surface">
          <div class="road-base-line"></div>
          <div
            class="road-active-line"
            :style="{ width: `${progress * 100}%` }"
          ></div>
        </div>

        <!-- MOVING GREEN CAR (FULL WINDOW BAM THEKE DAN) -->
        <div
          class="car-container"
          :style="{ transform: `translateX(${carX}px)` }"
        >
          <img
            :src="carUrl"
            alt="Parkly Car"
            class="green-car-image"
          />
        </div>
      </div>

      <!-- 3. PERCENTAGE & STATUS -->
      <div class="progress-container" aria-hidden="true">
        <span class="loading-tag">AI Parking Discovery</span>
        <div class="percentage-row">
          <span class="number">{{ displayPercentage }}</span>
          <span class="unit">%</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Accessibility */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

/* Curtain overlay */
.parkly-splash-curtain {
  position: fixed;
  inset: 0;
  z-index: 999999;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  min-height: 100dvh;
  width: 100vw;
  background-color: var(--color-bg, #F8FAFC);
  color: var(--color-text, #0F172A);
  overflow: hidden;
  pointer-events: all;
  transition: opacity 450ms cubic-bezier(0.16, 1, 0.3, 1), transform 450ms cubic-bezier(0.16, 1, 0.3, 1);
  will-change: opacity, transform;
}

.parkly-splash-curtain.is-exiting {
  opacity: 0;
  transform: scale(1.01);
  pointer-events: none;
}

/* Central Stage */
.splash-stage {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}

/* ================= 1. REVEALABLE BRAND ROW ================= */
.brand-reveal-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: clamp(32px, 5vh, 48px);
  overflow: hidden; /* Clips the cover inside the brand boundaries */
}

.brand-row {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: clamp(16px, 3vw, 24px);
  white-space: nowrap;
  user-select: none;
}

.brand-logo {
  width: clamp(76px, 10vw, 108px);
  height: clamp(76px, 10vw, 108px);
  object-fit: contain;
  display: block;
  filter: drop-shadow(0 4px 14px rgba(15, 118, 110, 0.15));
}

.brand-name {
  font-family: var(--font-sans, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
  font-size: clamp(48px, 7vw, 68px);
  font-weight: 800;
  letter-spacing: -0.03em;
  color: var(--color-text, #0F172A);
  line-height: 1;
}

/* Cover matching page background color */
.brand-reveal-cover {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--color-bg, #F8FAFC);
  pointer-events: none;
  z-index: 5;
  will-change: transform;
}

/* ================= 2. FULL WINDOW WIDTH ROADWAY ================= */
.road-track {
  position: relative;
  width: 100vw;
  height: 60px;
  display: flex;
  align-items: flex-end;
  margin-bottom: 24px;
}

/* 3px Road line spanning full window */
.road-surface {
  position: absolute;
  bottom: 0px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: var(--color-border, #E2E8F0);
  overflow: hidden;
  z-index: 1;
}

.road-base-line {
  position: absolute;
  inset: 0;
  background-color: var(--color-border, #E2E8F0);
}

.road-active-line {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  background-color: var(--color-primary, #0F766E);
  transition: width 50ms linear;
}

/* Moving Car (Spans full window left to right) */
.car-container {
  position: absolute;
  bottom: 2px;
  left: 0;
  width: 140px;
  height: auto;
  z-index: 2;
  will-change: transform;
}

.green-car-image {
  width: 100%;
  height: auto;
  display: block;
  object-fit: contain;
  filter: drop-shadow(0 4px 6px rgba(15, 118, 110, 0.2));
}

/* ================= 3. PROGRESS BLOCK ================= */
.progress-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  user-select: none;
}

.loading-tag {
  font-family: var(--font-sans, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-text-muted, #64748B);
}

.percentage-row {
  display: flex;
  align-items: baseline;
  font-family: var(--font-sans, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
  font-size: clamp(28px, 4vw, 36px);
  font-weight: 800;
  font-variant-numeric: tabular-nums;
  color: var(--color-primary, #0F766E);
  line-height: 1;
}

.percentage-row .unit {
  font-size: 0.65em;
  margin-left: 2px;
  font-weight: 700;
  opacity: 0.85;
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  .brand-reveal-cover {
    display: none !important;
  }

  .car-container {
    display: none !important;
  }

  .parkly-splash-curtain {
    transition: opacity 250ms ease !important;
  }
}
</style>
