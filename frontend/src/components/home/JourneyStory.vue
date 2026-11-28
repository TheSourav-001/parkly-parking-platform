<script setup>
/**
 * JourneyStory Component (Scroll Animation Redesign)
 *
 * Requirements & Fixes:
 * 1. Timeline & Dwell:
 *    - Driver car reaches its final stopped position fully inside the parking slot at p = 0.92.
 *    - Dwell held from p = 0.92 to 1.0 (Ready to park state, headlights dim, checkmark drawn).
 *    - Extra scroll space added (~20vh dwell); never releases while car is moving.
 * 2. Traffic in both directions:
 *    - 3 to 5 cars of different colors + bus moving in BOTH directions on separate lanes at different speeds (looping, offscreen-recycled).
 *    - 2 to 3 parked cars at the curb.
 *    - Cars slightly vary in size by lane (depth). Wheels spin, lane dashes move.
 *    - Traffic loops continue while the section is visible; scroll drives the camera/sky and driver car.
 * 3. Info card placement (Zero overlap with road / slot):
 *    - Desktop: docked in upper sky zone on the side opposite to the car's destination (left/top), max-height ~38% of stage.
 *    - Mobile: docked at top of stage, compact, road & scene strictly below it.
 * 4. Header:
 *    - Positioned outside/above sticky stage with proper navbar offset (top: var(--nav-h)).
 *    - Celestial sun/moon does not collide with header text.
 * 5. Reduced-motion fallback & GSAP ScrollTrigger cleanup with ctx.revert().
 * 6. Step 6 copy: show invoice to parking staff (no automatic gate).
 */
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import SectionHeader from '../common/SectionHeader.vue'

gsap.registerPlugin(ScrollTrigger)

const storyWrapperRef = ref(null)
const currentStep = ref(0)
const scrollProgress = ref(0)
const isReducedMotion = ref(false)

const steps = [
  {
    number: '01',
    title: 'Select Your Area',
    subtitle: 'Allow GPS or select District → Thana → Area manually. No account needed to search.',
    tag: 'Location Discovery',
    badge: 'Dhaka-Centered',
  },
  {
    number: '02',
    title: 'Discover Parking Nearby',
    subtitle: 'Browse registered facilities with transparent hourly rates and reported slots.',
    tag: 'Verified Hubs',
    badge: 'Registered Facilities Only',
  },
  {
    number: '03',
    title: 'Choose What Works Best',
    subtitle: 'Filter by Car, SUV, or Motorcycle and review latest admin-updated slots.',
    tag: 'Reported Availability',
    badge: 'Verified Records',
  },
  {
    number: '04',
    title: 'Let AI Find Your Match',
    subtitle: 'Describe destination, vehicle and budget in plain language. Recommendations come purely from verified DB records.',
    tag: 'Grounded AI',
    badge: 'Zero Hallucinations',
  },
  {
    number: '05',
    title: 'Book Your Parking Space',
    subtitle: 'Select duration, let the server calculate the fee, and pay via SSLCOMMERZ demo gateway.',
    tag: 'Server-Calculated Fee',
    badge: 'Instant Invoice',
  },
  {
    number: '06',
    title: "You're Ready to Park",
    subtitle: 'Show your digital PDF invoice or Booking ID to parking staff for verification before entry.',
    tag: 'Staff Verification',
    badge: 'Guaranteed Entry Protocol',
  },
]

// Car motion: Reaches final stopped position fully inside parking bay at p = 0.92,
// then holds p = 0.92 to 1.0 as dwell before sticky section releases.
const carPanX = computed(() => {
  const p = Math.min(1, Math.max(0, scrollProgress.value / 0.92))
  // Starts offscreen left (-140) and parks squarely inside slot at x = 1270
  return -140 + p * 1410
})

const isParkedDwell = computed(() => scrollProgress.value >= 0.92)

const skyStyle = computed(() => {
  const p = scrollProgress.value
  if (p < 0.45) {
    return 'linear-gradient(180deg, #E0F2FE 0%, #F0F9FF 60%, #BAE6FD 100%)'
  } else if (p < 0.75) {
    return 'linear-gradient(180deg, #FDE68A 0%, #FDBA74 45%, #F472B6 100%)'
  } else {
    return 'linear-gradient(180deg, #090D16 0%, #0F172A 60%, #1E293B 100%)'
  }
})

const lampOpacity = computed(() => {
  const p = scrollProgress.value
  if (p < 0.65) return 0
  return Math.min(1, (p - 0.65) / 0.15)
})

let ctx = null

onMounted(() => {
  isReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (isReducedMotion.value) return

  ctx = gsap.context(() => {
    ScrollTrigger.create({
      trigger: storyWrapperRef.value,
      start: 'top top',
      end: 'bottom bottom',
      scrub: 0.4,
      onUpdate: (self) => {
        scrollProgress.value = self.progress
        const stepIdx = Math.min(5, Math.floor(self.progress * 6))
        currentStep.value = stepIdx
      },
    })
  }, storyWrapperRef.value)
})

onBeforeUnmount(() => {
  try {
    if (ctx) {
      ctx.revert()
      ctx = null
    }
  } catch (err) {
    console.warn('[JourneyStory] Cleanup error:', err)
  }
})

function scrollToStep(idx) {
  if (!storyWrapperRef.value || isReducedMotion.value) return
  const rect = storyWrapperRef.value.getBoundingClientRect()
  const scrollY = window.scrollY + rect.top + (idx / 6) * rect.height
  window.scrollTo({ top: scrollY, behavior: 'smooth' })
}
</script>

<template>
  <section id="journey" class="journey-story-section">
    <!-- Static Section Header (Outside sticky stage to prevent navbar collision) -->
    <div class="story-static-header">
      <SectionHeader
        eyebrow="The Parkly Journey"
        title="From search to parking, in six simple steps"
        subtitle="Scroll down to explore how Parkly guides you from location discovery to staff-verified parking."
      />
    </div>

    <!-- Reduced Motion Static Stepper Fallback -->
    <div v-if="isReducedMotion" class="reduced-motion-container">
      <div class="static-steps-grid">
        <div v-for="(step, idx) in steps" :key="idx" class="static-step-card">
          <span class="static-step-number">{{ step.number }}</span>
          <span class="static-step-tag">{{ step.tag }}</span>
          <h3 class="static-step-title">{{ step.title }}</h3>
          <p class="static-step-desc">{{ step.subtitle }}</p>
          <span class="static-step-badge">{{ step.badge }}</span>
        </div>
      </div>
    </div>

    <!-- 600vh Scroll-Driven Parallax Experience with dwell -->
    <div v-else ref="storyWrapperRef" class="scroll-story-outer">
      <div class="sticky-stage">
        <!-- Parallax Canvas Scene -->
        <div class="scene-canvas-wrapper" :style="{ background: skyStyle }">
          <svg
            viewBox="0 0 1600 900"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            class="scene-svg"
            preserveAspectRatio="xMidYMax slice"
          >
            <!-- Celestial: Sun/Moon positioned in right sky to avoid header & card -->
            <g :transform="`translate(${1180 - scrollProgress * 150}, ${320 + scrollProgress * 90})`">
              <!-- Sun (Day/Sunset) -->
              <g v-if="scrollProgress < 0.75" class="celestial-sun-body">
                <circle cx="0" cy="0" r="46" fill="#FBBF24" opacity="0.3" />
                <circle cx="0" cy="0" r="34" fill="#F59E0B" />
                <circle cx="-6" cy="-6" r="24" fill="#FDE68A" opacity="0.7" />
              </g>
              <!-- Moon (Night) -->
              <g v-else class="celestial-moon-body">
                <circle cx="0" cy="0" r="40" fill="#38BDF8" opacity="0.25" />
                <circle cx="0" cy="0" r="28" fill="#F1F5F9" />
                <circle cx="-5" cy="-4" r="4" fill="#CBD5E1" opacity="0.7" />
                <circle cx="6" cy="5" r="5" fill="#CBD5E1" opacity="0.5" />
              </g>
            </g>

            <!-- Distant Mountains / Horizon Landscape -->
            <path
              d="M 0,580 Q 200,530 420,560 T 840,540 T 1260,565 T 1600,535 L 1600,680 L 0,680 Z"
              fill="#94A3B8"
              opacity="0.25"
            />
            <path
              d="M 0,610 Q 300,570 600,600 T 1200,580 T 1600,605 L 1600,680 L 0,680 Z"
              fill="#64748B"
              opacity="0.3"
            />

            <!-- Distant City Skyline Architecture -->
            <g opacity="0.45">
              <rect x="80" y="470" width="130" height="210" rx="3" fill="var(--color-border)" />
              <rect x="240" y="430" width="150" height="250" rx="3" fill="var(--color-border)" />
              <rect x="420" y="490" width="120" height="190" rx="3" fill="var(--color-border)" />
              <rect x="580" y="410" width="160" height="270" rx="3" fill="var(--color-border)" />
              <rect x="780" y="470" width="130" height="210" rx="3" fill="var(--color-border)" />
              <rect x="940" y="440" width="150" height="240" rx="3" fill="var(--color-border)" />
              <rect x="1120" y="480" width="140" height="200" rx="3" fill="var(--color-border)" />
              <rect x="1300" y="420" width="160" height="260" rx="3" fill="var(--color-border)" />
            </g>

            <!-- Lit Windows at Sunset & Night -->
            <g :opacity="lampOpacity">
              <rect x="260" y="450" width="14" height="16" fill="#FDE68A" />
              <rect x="290" y="490" width="14" height="16" fill="#FDE68A" />
              <rect x="610" y="440" width="16" height="18" fill="#FDE68A" />
              <rect x="660" y="480" width="16" height="18" fill="#FDE68A" />
              <rect x="970" y="460" width="14" height="16" fill="#FDE68A" />
              <rect x="1010" y="500" width="14" height="16" fill="#FDE68A" />
              <rect x="1330" y="450" width="16" height="18" fill="#FDE68A" />
              <rect x="1380" y="490" width="16" height="18" fill="#FDE68A" />
            </g>

            <!-- Ground & Road Infrastructure (Road occupies lower half, strictly away from top sky card) -->
            <rect x="0" y="670" width="1600" height="230" fill="var(--color-bg)" />

            <!-- Curb Sidewalk -->
            <rect x="0" y="684" width="1600" height="18" fill="#64748B" opacity="0.3" />

            <!-- Dual-Lane Road Surface -->
            <rect x="0" y="702" width="1600" height="148" fill="#1E293B" />

            <!-- Animated Lane Divider Dashes (Road movement) -->
            <g class="animated-lane-dashes">
              <line
                x1="-200"
                y1="774"
                x2="1800"
                y2="774"
                stroke="#F8FAFC"
                stroke-width="2.8"
                stroke-dasharray="28 24"
              />
            </g>

            <!-- 2 to 3 Parked Cars at the Curb -->
            <g transform="translate(140, 676)">
              <rect x="0" y="0" width="68" height="20" rx="4" fill="#334155" />
              <rect x="15" y="-6" width="34" height="10" rx="3" fill="#1E293B" opacity="0.7" />
              <circle cx="14" cy="20" r="5" fill="#0F172A" />
              <circle cx="54" cy="20" r="5" fill="#0F172A" />
            </g>
            <g transform="translate(250, 676)">
              <rect x="0" y="0" width="64" height="20" rx="4" fill="#475569" />
              <rect x="14" y="-6" width="32" height="10" rx="3" fill="#1E293B" opacity="0.7" />
              <circle cx="14" cy="20" r="5" fill="#0F172A" />
              <circle cx="50" cy="20" r="5" fill="#0F172A" />
            </g>
            <g transform="translate(360, 676)">
              <rect x="0" y="0" width="62" height="20" rx="4" fill="#64748B" />
              <rect x="14" y="-6" width="30" height="10" rx="3" fill="#1E293B" opacity="0.7" />
              <circle cx="14" cy="20" r="5" fill="#0F172A" />
              <circle cx="48" cy="20" r="5" fill="#0F172A" />
            </g>

            <!-- =======================================================
                 DYNAMIC TRAFFIC IN BOTH DIRECTIONS (Looping, offscreen-recycled)
                 Opposing Traffic (Upper Lane, moving LEFT, smaller depth scale 0.92)
                 Same-Direction Traffic (Lower Lane, moving RIGHT, scale 1.0)
                 ======================================================= -->

            <!-- Opposing Lane: Car 1 (Silver Sedan moving Left) -->
            <g class="traffic-left-car-1" transform="scale(0.92)">
              <rect x="0" y="784" width="64" height="20" rx="4" fill="#94A3B8" />
              <rect x="14" y="776" width="32" height="10" rx="3" fill="#64748B" opacity="0.6" />
              <circle cx="14" cy="804" r="5" fill="#0F172A" />
              <circle cx="50" cy="804" r="5" fill="#0F172A" />
              <!-- Spinning wheel spokes -->
              <line x1="14" y1="799" x2="14" y2="809" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
              <line x1="50" y1="799" x2="50" y2="809" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
            </g>

            <!-- Opposing Lane: Bus (Public Transit Bus moving Left) -->
            <g class="traffic-left-bus" transform="scale(0.94)">
              <rect x="0" y="760" width="130" height="34" rx="5" fill="#0284C7" />
              <!-- Bus Windows -->
              <rect x="12" y="766" width="20" height="10" rx="2" fill="#E0F2FE" opacity="0.8" />
              <rect x="38" y="766" width="20" height="10" rx="2" fill="#E0F2FE" opacity="0.8" />
              <rect x="64" y="766" width="20" height="10" rx="2" fill="#E0F2FE" opacity="0.8" />
              <rect x="90" y="766" width="20" height="10" rx="2" fill="#E0F2FE" opacity="0.8" />
              <!-- Bus Wheels -->
              <circle cx="24" cy="794" r="7" fill="#0F172A" />
              <circle cx="106" cy="794" r="7" fill="#0F172A" />
              <line x1="24" y1="787" x2="24" y2="801" stroke="#94A3B8" stroke-width="1.5" class="wheel-spin" />
              <line x1="106" y1="787" x2="106" y2="801" stroke="#94A3B8" stroke-width="1.5" class="wheel-spin" />
            </g>

            <!-- Opposing Lane: Car 2 (Crimson Compact moving Left) -->
            <g class="traffic-left-car-2" transform="scale(0.92)">
              <rect x="0" y="784" width="58" height="20" rx="4" fill="#DC2626" />
              <rect x="12" y="776" width="28" height="10" rx="3" fill="#7F1D1D" opacity="0.7" />
              <circle cx="14" cy="804" r="5" fill="#0F172A" />
              <circle cx="44" cy="804" r="5" fill="#0F172A" />
              <line x1="14" y1="799" x2="14" y2="809" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
              <line x1="44" y1="799" x2="44" y2="809" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
            </g>

            <!-- Same-Direction Lane: Car 3 (Amber Hatchback moving Right) -->
            <g class="traffic-right-car-1">
              <rect x="0" y="792" width="62" height="22" rx="4" fill="#F59E0B" />
              <rect x="14" y="784" width="30" height="10" rx="3" fill="#78350F" opacity="0.6" />
              <circle cx="14" cy="814" r="5.5" fill="#0F172A" />
              <circle cx="48" cy="814" r="5.5" fill="#0F172A" />
              <line x1="14" y1="808.5" x2="14" y2="819.5" stroke="#FEF3C7" stroke-width="1.2" class="wheel-spin" />
              <line x1="48" y1="808.5" x2="48" y2="819.5" stroke="#FEF3C7" stroke-width="1.2" class="wheel-spin" />
            </g>

            <!-- Same-Direction Lane: Car 4 (Navy SUV moving Right) -->
            <g class="traffic-right-car-2">
              <rect x="0" y="788" width="76" height="26" rx="5" fill="#3B82F6" />
              <rect x="16" y="779" width="38" height="11" rx="3" fill="#1E3A8A" opacity="0.7" />
              <circle cx="18" cy="814" r="6" fill="#0F172A" />
              <circle cx="58" cy="814" r="6" fill="#0F172A" />
              <line x1="18" y1="808" x2="18" y2="820" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
              <line x1="58" y1="808" x2="58" y2="820" stroke="#E2E8F0" stroke-width="1.2" class="wheel-spin" />
            </g>

            <!-- Street Lamp Post at Destination -->
            <g transform="translate(1120, 540)">
              <line x1="0" y1="0" x2="0" y2="160" stroke="#64748B" stroke-width="4" />
              <path d="M 0,0 Q 20,-12 40,0" stroke="#64748B" stroke-width="3" fill="none" />
              <circle cx="40" cy="5" r="7" fill="#FDE68A" />
              <polygon
                points="40,10 -15,220 120,220"
                fill="#FEF08A"
                :opacity="lampOpacity * 0.25"
              />
            </g>

            <!-- Destination Parking Bay & Staff Verification Booth -->
            <g transform="translate(1260, 580)">
              <!-- 'P' Pole Sign -->
              <rect x="18" y="20" width="5" height="100" fill="#64748B" />
              <circle cx="20" cy="20" r="22" fill="var(--color-primary)" />
              <text x="20" y="28" font-family="'Inter', sans-serif" font-size="20" font-weight="800" fill="#FFFFFF" text-anchor="middle">P</text>

              <!-- Marked Parking Bay Lines (Slot clearly bounds driver car) -->
              <rect x="60" y="115" width="140" height="5" fill="#F8FAFC" opacity="0.8" />
              <rect x="195" y="115" width="5" height="65" fill="#F8FAFC" opacity="0.8" />

              <!-- Verification Desk / Parking Staff Indicator -->
              <rect x="-35" y="75" width="46" height="46" rx="4" fill="var(--color-surface)" stroke="var(--color-primary)" stroke-width="1.8" />
              <text x="-12" y="96" font-family="'Inter', sans-serif" font-size="7.5" font-weight="700" fill="var(--color-text)" text-anchor="middle">STAFF</text>
              <text x="-12" y="108" font-family="'Inter', sans-serif" font-size="6.5" font-weight="600" fill="var(--color-primary)" text-anchor="middle">CHECK</text>

              <!-- Success Checkmark (Appears when fully parked at p >= 0.92) -->
              <g v-if="isParkedDwell" transform="translate(-12, 52)">
                <circle cx="0" cy="0" r="13" fill="var(--color-primary)" />
                <path d="M -5 0 L -1 4 L 6 -3" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
              </g>
            </g>

            <!-- Driver's Hero Car (Controlled by Scroll Progress: parks at p=0.92, dwells 0.92-1.0) -->
            <g :transform="`translate(${carPanX}, 730)`">
              <ellipse cx="60" cy="55" rx="52" ry="4" fill="rgba(0, 0, 0, 0.4)" />

              <!-- Headlight Beam (Dims when parked) -->
              <polygon
                points="115,35 240,15 240,55 115,48"
                fill="#5EEAD4"
                :opacity="isParkedDwell ? 0.05 : lampOpacity * 0.45"
              />

              <!-- Hero Car Body (Facing Right) -->
              <path
                d="M8 40 C8 36 12 33 17 33 L29 33 L46 20 C49 18 54 16 61 16 L89 16 C95 16 100 18 105 22 L118 33 L123 35 C126 36 128 38 128 42 L128 45 C128 47 126 49 123 49 L112 49 C112 44 108 40 102 40 C96 40 92 44 92 49 L47 49 C47 44 43 40 37 40 C31 40 27 44 27 49 L13 49 C10 49 8 47 8 43 Z"
                fill="var(--color-primary)"
              />

              <!-- Windows -->
              <path d="M48 30 L63 30 L63 20 L53 20 Z" fill="#E2E8F0" opacity="0.85" />
              <path d="M67 30 L95 30 L87 20 L67 20 Z" fill="#E2E8F0" opacity="0.85" />

              <!-- Wheels (Spinning during driving) -->
              <g transform="translate(37, 49)">
                <circle r="8" fill="#0F172A" />
                <circle r="4" fill="#64748B" />
                <circle r="1.5" fill="var(--color-primary)" />
                <line v-if="!isParkedDwell" x1="0" y1="-6" x2="0" y2="6" stroke="#94A3B8" stroke-width="1.2" class="wheel-spin" />
              </g>
              <g transform="translate(102, 49)">
                <circle r="8" fill="#0F172A" />
                <circle r="4" fill="#64748B" />
                <circle r="1.5" fill="var(--color-primary)" />
                <line v-if="!isParkedDwell" x1="0" y1="-6" x2="0" y2="6" stroke="#94A3B8" stroke-width="1.2" class="wheel-spin" />
              </g>
            </g>
          </svg>
        </div>

        <!-- Upper Sky Info Card (Docked in upper sky zone, strictly avoiding the road & slot) -->
        <div class="sky-docked-panel">
          <div class="panel-card">
            <div class="panel-header">
              <span class="step-chip">{{ steps[currentStep].number }} of 06</span>
              <span class="step-badge">{{ steps[currentStep].badge }}</span>
            </div>

            <span class="step-tag">{{ steps[currentStep].tag }}</span>
            <h3 class="step-title">{{ steps[currentStep].title }}</h3>
            <p class="step-subtitle">{{ steps[currentStep].subtitle }}</p>

            <!-- Step Rail Dots -->
            <div class="step-dots-rail" role="tablist">
              <button
                v-for="(st, idx) in steps"
                :key="idx"
                class="rail-dot-btn"
                :class="{ 'is-active': currentStep === idx }"
                :aria-label="`Jump to step ${idx + 1}: ${st.title}`"
                @click="scrollToStep(idx)"
              ></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.journey-story-section {
  position: relative;
  width: 100%;
  background-color: var(--color-bg);
  scroll-margin-top: var(--nav-h, 72px);
}

.story-static-header {
  padding: 64px 24px 24px;
  max-width: 1200px;
  margin: 0 auto;
}

/* 600vh scroll container + ~20vh extra dwell buffer */
.scroll-story-outer {
  height: 560vh;
  position: relative;
}

.sticky-stage {
  position: sticky;
  top: var(--nav-h, 72px);
  width: 100%;
  height: calc(100svh - var(--nav-h, 72px));
  min-height: 540px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.scene-canvas-wrapper {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  transition: background 500ms ease;
  z-index: 1;
}

.scene-svg {
  width: 100%;
  height: 100%;
  display: block;
}

/* Animated Wheels & Road Dashes */
.wheel-spin {
  transform-origin: center;
  animation: spin 0.4s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animated-lane-dashes {
  animation: laneDashMove 1.2s linear infinite;
}

@keyframes laneDashMove {
  0% { transform: translateX(0); }
  100% { transform: translateX(52px); }
}

/* Multi-Vehicle Traffic Animations (Looping, Offscreen-Recycled) */
.traffic-left-car-1 {
  animation: trafficMoveLeft 9.5s linear infinite;
}

.traffic-left-bus {
  animation: trafficMoveLeft 14s linear infinite;
  animation-delay: -4s;
}

.traffic-left-car-2 {
  animation: trafficMoveLeft 8s linear infinite;
  animation-delay: -7s;
}

.traffic-right-car-1 {
  animation: trafficMoveRight 10s linear infinite;
  animation-delay: -2s;
}

.traffic-right-car-2 {
  animation: trafficMoveRight 12.5s linear infinite;
  animation-delay: -6.5s;
}

@keyframes trafficMoveLeft {
  0% { transform: translateX(1800px); }
  100% { transform: translateX(-300px); }
}

@keyframes trafficMoveRight {
  0% { transform: translateX(-300px); }
  100% { transform: translateX(1800px); }
}

/* Info Panel Docked in Upper Sky Zone (Never covers road or slot) */
.sky-docked-panel {
  position: absolute;
  top: 24px;
  left: clamp(16px, 4vw, 48px);
  z-index: 15;
  width: min(440px, 92vw);
  max-height: 38%;
}

.panel-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 20px 24px;
  box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  gap: 8px;
  backdrop-filter: blur(8px);
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.step-chip {
  font-family: var(--font-sans);
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-primary);
  background-color: rgba(15, 118, 110, 0.08);
  padding: 3px 8px;
  border-radius: 999px;
}

.step-badge {
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-text-muted);
}

.step-tag {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-primary);
}

.step-title {
  font-family: var(--font-sans);
  font-size: 1.1875rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--color-text);
  line-height: 1.25;
  margin: 0;
}

.step-subtitle {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  line-height: 1.45;
  margin: 0;
}

.step-dots-rail {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 6px;
}

.rail-dot-btn {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--color-border);
  border: none;
  padding: 0;
  cursor: pointer;
}

.rail-dot-btn.is-active {
  width: 20px;
  border-radius: 999px;
  background-color: var(--color-primary);
}

/* Mobile & Tablet Optimizations */
@media (max-width: 768px) {
  .sky-docked-panel {
    top: 12px;
    left: 12px;
    right: 12px;
    width: auto;
  }

  .panel-card {
    padding: 14px 16px;
    gap: 6px;
  }

  .step-title {
    font-size: 1rem;
  }

  .step-subtitle {
    font-size: 0.75rem;
  }
}

/* Reduced Motion Mode */
.reduced-motion-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}

.static-steps-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.static-step-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.static-step-number {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--color-primary);
}

.static-step-tag {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-primary);
}

.static-step-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.static-step-desc {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  line-height: 1.45;
  margin: 0;
}

.static-step-badge {
  display: inline-block;
  align-self: flex-start;
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-primary);
  background: rgba(15, 118, 110, 0.08);
  padding: 2px 8px;
  border-radius: 4px;
  margin-top: 6px;
}
</style>
