<script setup>
/**
 * ProfileParkingAnimation.vue
 *
 * Interactive Multi-Vehicle Autonomous Smart-Parking Scene (SVG + Anime.js v4):
 *  - Pointer-driven multi-layer 3D depth parallax via createAnimatable()
 *  - Dual-vehicle synchronized bay choreography:
 *      * Outgoing sedan vacates Bay P-03 and exits along the boulevard
 *      * Overhead smart-pylon flips Bay P-03 sensor LED from occupied -> available
 *      * Incoming EV clears ANPR LiDAR laser scan + lifting boom barrier (-76deg)
 *      * Staggered runway chevrons + glowing drawable path guide EV into Bay P-02
 *      * Dynamic braking physics, bay outline morph (morphTo),
 *        4 corner HUD lock brackets, wireless EV floor charging rings, & telemetry HUD
 *  - Interactive Bay Inspector pills (click P-01..P-04 or Pause / Replay)
 */
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { animate, createAnimatable, createTimeline, morphTo, stagger, svg } from 'animejs'
import { useTheme } from '../../composables/useTheme'

const { currentTheme } = useTheme()
const rootEl = ref(null)

const selectedBay = ref('P-02')
const isPaused = ref(false)
const currentViewBox = ref('0 0 1200 280')
const currentPreserveAspect = ref('xMidYMid slice')

function updateResponsiveViewBox() {
  if (typeof window === 'undefined') return
  const w = window.innerWidth
  if (w <= 640) {
    // Mobile: Frame entire active scene from LiDAR Gate (x=215) to Bay P-04 (x=1155) cleanly without clipping
    currentViewBox.value = '215 12 940 256'
    currentPreserveAspect.value = 'xMidYMid meet'
  } else if (w <= 1024) {
    // Tablet: Frame Gate + Radar + Full Boulevard + P-01..P-04
    currentViewBox.value = '140 8 1025 268'
    currentPreserveAspect.value = 'xMidYMid meet'
  } else {
    // Desktop: Full panoramic cover
    currentViewBox.value = '0 0 1200 280'
    currentPreserveAspect.value = 'xMidYMid slice'
  }
}

const bayStatusMap = {
  'P-01': { code: 'P-01', state: 'Occupied • Compact EV', color: '#94A3B8' },
  'P-02': { code: 'P-02', state: 'Reserved • Inductive Charge', color: '#14B8A6' },
  'P-03': { code: 'P-03', state: 'Live • Vacating Spot', color: '#38BDF8' },
  'P-04': { code: 'P-04', state: 'Occupied • Standard', color: '#94A3B8' },
}

const activeBayInfo = computed(() => bayStatusMap[selectedBay.value])

let mainTimeline = null
let ambientAnims = []
let skyParallax = null
let plazaParallax = null

function handlePointerMove(e) {
  if (!rootEl.value) return
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return
  const rect = rootEl.value.getBoundingClientRect()
  const nx = (e.clientX - rect.left) / rect.width - 0.5
  const ny = (e.clientY - rect.top) / rect.height - 0.5

  if (skyParallax) {
    skyParallax.x(-nx * 14)
    skyParallax.y(-ny * 8)
  }
  if (plazaParallax) {
    plazaParallax.x(nx * 6)
    plazaParallax.y(ny * 4)
  }
}

function handlePointerLeave() {
  if (skyParallax) {
    skyParallax.x(0)
    skyParallax.y(0)
  }
  if (plazaParallax) {
    plazaParallax.x(0)
    plazaParallax.y(0)
  }
}

function togglePlayback() {
  if (!mainTimeline) return
  if (isPaused.value) {
    mainTimeline.play()
    isPaused.value = false
  } else {
    mainTimeline.pause()
    isPaused.value = true
  }
}

function replaySequence() {
  if (!mainTimeline) return
  mainTimeline.restart()
  isPaused.value = false
}

onMounted(() => {
  updateResponsiveViewBox()
  window.addEventListener('resize', updateResponsiveViewBox, { passive: true })

  const scene = rootEl.value
  if (!scene) return

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  const skyLayer = scene.querySelector('.parallax-sky-layer')
  const plazaLayer = scene.querySelector('.parallax-plaza-layer')

  const route = scene.querySelector('.parking-route')
  const exitRoute = scene.querySelector('.exit-route')

  const car = scene.querySelector('.hero-ev-car')
  const exitCar = scene.querySelector('.outgoing-car')
  const headlights = scene.querySelector('.ev-headlights')
  const brakeLights = scene.querySelector('.ev-brakelights')
  const chargeWave = scene.querySelector('.ev-charge-bar')

  const barrierArm = scene.querySelector('.gate-barrier-arm')
  const gateLed = scene.querySelector('.gate-status-led')
  const scanBeam = scene.querySelector('.scanner-laser-beam')
  const anprFrame = scene.querySelector('.anpr-scan-box')

  const bayIdlePath = scene.querySelector('.bay-p02-idle')
  const bayLockedPath = scene.querySelector('.bay-p02-locked')
  const bayGlow = scene.querySelector('.bay-p02-glow')
  const p03Led = scene.querySelector('.bay-p03-led')
  const chargeCoils = scene.querySelectorAll('.inductive-coil-ring')
  const sonar1 = scene.querySelector('.bay-sonar-1')
  const sonar2 = scene.querySelector('.bay-sonar-2')
  const hudCard = scene.querySelector('.bay-hud-card')
  const lockBrackets = scene.querySelectorAll('.hud-bracket')
  const chevrons = scene.querySelectorAll('.aisle-chevron')
  const radarSweep = scene.querySelector('.radar-sweep-group')
  const trafficA = scene.querySelector('.express-car-a')
  const trafficB = scene.querySelector('.express-car-b')
  const windows = scene.querySelectorAll('.skyline-win')

  if (prefersReduced) {
    if (car) {
      car.style.transform = 'translate(908px, 122px) rotate(-90deg)'
      car.style.opacity = '1'
    }
    if (hudCard) hudCard.style.opacity = '1'
    if (bayGlow) bayGlow.style.opacity = '0.9'
    return
  }

  // ── Pointer Parallax Animatables ─────────────────────────────────────────
  if (skyLayer) {
    skyParallax = createAnimatable(skyLayer, { x: 420, y: 420, ease: 'outQuad' })
  }
  if (plazaLayer) {
    plazaParallax = createAnimatable(plazaLayer, { x: 420, y: 420, ease: 'outQuad' })
  }

  // ── Ambient Loops: Telemetry Radar + Skyline Windows + Outer Express Traffic ─
  if (radarSweep) {
    ambientAnims.push(
      animate(radarSweep, {
        rotate: 360,
        duration: 5000,
        ease: 'linear',
        loop: true,
      })
    )
  }
  if (windows.length) {
    ambientAnims.push(
      animate(windows, {
        opacity: [0.22, 0.95, 0.3],
        duration: 2600,
        delay: stagger(140, { from: 'random' }),
        ease: 'inOutSine',
        loop: true,
      })
    )
  }
  if (trafficA) {
    ambientAnims.push(
      animate(trafficA, {
        translateX: [-120, 1320],
        duration: 6800,
        ease: 'linear',
        loop: true,
      })
    )
  }
  if (trafficB) {
    ambientAnims.push(
      animate(trafficB, {
        translateX: [1320, -140],
        duration: 8400,
        delay: 1200,
        ease: 'linear',
        loop: true,
      })
    )
  }

  // ── Main Multi-Vehicle Autonomous Parking Choreography ──────────────────
  if (!route || !car) return

  const motionPath = svg.createMotionPath(route)
  const drawableRoute = svg.createDrawable(route)
  const exitMotion = exitRoute ? svg.createMotionPath(exitRoute) : null
  const originalBayD = bayIdlePath ? bayIdlePath.getAttribute('d') : ''

  mainTimeline = createTimeline({
    loop: true,
    loopDelay: 650,
  })

  mainTimeline
    .set(car, { opacity: 0 }, 0)
    .set(exitCar, { opacity: 0.88 }, 0)
    .set(p03Led, { fill: '#F59E0B' }, 0)
    .set(headlights, { opacity: 0.95 }, 0)
    .set(brakeLights, { opacity: 0.2 }, 0)
    .set(chargeWave, { scaleX: 0, opacity: 0 }, 0)
    .set(chargeCoils, { opacity: 0, scale: 0.6 }, 0)
    .set(barrierArm, { rotate: 0 }, 0)
    .set(gateLed, { fill: '#F59E0B' }, 0)
    .set(scanBeam, { opacity: 0, translateX: -16 }, 0)
    .set(anprFrame, { opacity: 0, scale: 0.85 }, 0)
    .set(bayGlow, { opacity: 0.15 }, 0)
    .set(sonar1, { opacity: 0, scale: 0.7 }, 0)
    .set(sonar2, { opacity: 0, scale: 0.7 }, 0)
    .set(hudCard, { opacity: 0, translateY: 8, scale: 0.9 }, 0)
    .set(lockBrackets, { opacity: 0, scale: 1.4 }, 0)
    .set(chevrons, { opacity: 0.15 }, 0)

  // Phase 1: Outgoing sedan reverses/exits Bay P-03, overhead sensor turns emerald
  if (exitMotion && exitCar) {
    mainTimeline
      .add(
        exitCar,
        {
          ...exitMotion,
          duration: 3100,
          ease: 'inOutCubic',
        },
        150
      )
      .add(
        p03Led,
        {
          fill: '#10B981',
          duration: 280,
        },
        950
      )
      .add(
        exitCar,
        {
          opacity: [0.88, 0],
          duration: 420,
          ease: 'outQuad',
        },
        2850
      )
  }

  // Phase 2: Hero EV enters, passes ANPR Laser Scan Arch & Lifting Boom Barrier
  mainTimeline
    .add(
      car,
      {
        opacity: [0, 1],
        duration: 380,
        ease: 'outQuad',
      },
      300
    )
    .add(
      drawableRoute,
      {
        draw: ['0 0', '0 1'],
        duration: 3500,
        ease: 'inOutSine',
      },
      360
    )
    .add(
      car,
      {
        ...motionPath,
        duration: 4450,
        ease: 'inOutCubic',
      },
      360
    )
    .add(
      anprFrame,
      {
        opacity: [0, 1, 0],
        scale: [0.85, 1, 1.06],
        duration: 750,
        ease: 'outQuad',
      },
      760
    )
    .add(
      scanBeam,
      {
        opacity: [0, 0.95, 0],
        translateX: [-16, 22],
        duration: 620,
        ease: 'inOutQuad',
      },
      790
    )
    .add(
      gateLed,
      {
        fill: '#10B981',
        duration: 220,
      },
      920
    )
    .add(
      barrierArm,
      {
        rotate: [0, -76],
        duration: 520,
        ease: 'outBack(1.5)',
      },
      920
    )
    // Sequential runway chevrons wave forward
    .add(
      chevrons,
      {
        opacity: [0.15, 1, 0.25],
        scale: [0.85, 1.18, 1],
        duration: 480,
        delay: stagger(105),
        ease: 'outQuad',
      },
      1750
    )
    // Boom gate closes cleanly
    .add(
      barrierArm,
      {
        rotate: [-76, 0],
        duration: 540,
        ease: 'inOutQuad',
      },
      2350
    )
    .add(
      gateLed,
      {
        fill: '#F59E0B',
        duration: 200,
      },
      2550
    )
    // Phase 3: Braking & precision curve into Bay P-02
    .add(
      brakeLights,
      {
        opacity: [0.2, 1, 0.3],
        duration: 950,
        ease: 'inOutSine',
      },
      3850
    )
    .add(
      headlights,
      {
        opacity: [0.95, 0.16],
        duration: 600,
        ease: 'outQuad',
      },
      4450
    )
    // Phase 4: Bay Morph + Corner Brackets Lock + Inductive Floor Charging Coils + Dual Sonar + HUD
    .add(
      bayIdlePath,
      {
        d: morphTo(bayLockedPath),
        stroke: '#14B8A6',
        strokeWidth: [1.8, 2.8],
        duration: 550,
        ease: 'outCubic',
      },
      4550
    )
    .add(
      bayGlow,
      {
        opacity: [0.15, 1],
        duration: 520,
        ease: 'outQuad',
      },
      4550
    )
    .add(
      lockBrackets,
      {
        opacity: [0, 1],
        scale: [1.4, 1],
        duration: 420,
        delay: stagger(55),
        ease: 'outBack(1.6)',
      },
      4620
    )
    .add(
      chargeCoils,
      {
        opacity: [0, 0.9, 0.2],
        scale: [0.6, 1.18],
        duration: 1100,
        delay: stagger(180),
        ease: 'outCubic',
      },
      4720
    )
    .add(
      chargeWave,
      {
        opacity: [0, 1],
        scaleX: [0, 1],
        duration: 1200,
        ease: 'outExpo',
      },
      4780
    )
    .add(
      sonar1,
      {
        opacity: [0.85, 0],
        scale: [0.7, 1.75],
        duration: 1150,
        ease: 'outCubic',
      },
      4650
    )
    .add(
      sonar2,
      {
        opacity: [0.65, 0],
        scale: [0.7, 2.05],
        duration: 1300,
        ease: 'outCubic',
      },
      4820
    )
    .add(
      hudCard,
      {
        opacity: [0, 1],
        translateY: [8, 0],
        scale: [0.9, 1],
        duration: 480,
        ease: 'outBack(1.6)',
      },
      4750
    )
    // Phase 5: Hold parked & charging (~2.3s), then fade smoothly for next cycle
    .add(
      [car, hudCard, lockBrackets, chargeWave],
      {
        opacity: 0,
        duration: 550,
        ease: 'inOutQuad',
      },
      7400
    )
    .add(
      drawableRoute,
      {
        draw: ['0 1', '1 1'],
        duration: 450,
        ease: 'inQuad',
      },
      7400
    )
    .add(
      bayGlow,
      {
        opacity: 0.15,
        duration: 450,
      },
      7400
    )
    .add(
      bayIdlePath,
      {
        d: originalBayD,
        strokeWidth: 1.8,
        duration: 400,
        ease: 'inOutQuad',
      },
      7550
    )
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateResponsiveViewBox)

  if (mainTimeline && typeof mainTimeline.pause === 'function') {
    mainTimeline.pause()
  }
  mainTimeline = null
  ambientAnims.forEach((a) => {
    if (a && typeof a.pause === 'function') a.pause()
  })
  ambientAnims = []
})
</script>

<template>
  <div
    ref="rootEl"
    class="profile-parking-animation"
    :class="{ 'is-dark': currentTheme === 'dark' }"
    @mousemove="handlePointerMove"
    @mouseleave="handlePointerLeave"
  >
    <svg
      :viewBox="currentViewBox"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      :preserveAspectRatio="currentPreserveAspect"
      class="cover-svg"
      aria-hidden="true"
    >
      <defs>
        <linearGradient id="ultra-sky" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--cov-bg-start)" />
          <stop offset="50%" stop-color="var(--cov-bg-mid)" />
          <stop offset="100%" stop-color="var(--cov-bg-end)" />
        </linearGradient>

        <linearGradient id="ultra-road" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="var(--cov-road-top)" />
          <stop offset="100%" stop-color="var(--cov-road-bot)" />
        </linearGradient>

        <linearGradient id="ultra-ev-body" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#0F172A" />
          <stop offset="45%" stop-color="#334155" />
          <stop offset="85%" stop-color="#1E293B" />
          <stop offset="100%" stop-color="#0F172A" />
        </linearGradient>

        <linearGradient id="ultra-beam" x1="0%" y1="50%" x2="100%" y2="50%">
          <stop offset="0%" stop-color="#5EEAD4" stop-opacity="0.88" />
          <stop offset="55%" stop-color="#14B8A6" stop-opacity="0.3" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </linearGradient>

        <linearGradient id="ultra-scan" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#14B8A6" stop-opacity="0" />
          <stop offset="50%" stop-color="#5EEAD4" stop-opacity="0.9" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </linearGradient>

        <linearGradient id="ultra-radar-wedge" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#14B8A6" stop-opacity="0.42" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </linearGradient>

        <radialGradient id="ultra-bay-glow" cx="50%" cy="50%" r="55%">
          <stop offset="0%" stop-color="#14B8A6" stop-opacity="0.48" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </radialGradient>

        <filter id="ultra-neon" x="-25%" y="-25%" width="150%" height="150%">
          <feGaussianBlur stdDeviation="2.8" result="blur" />
          <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>

        <filter id="ultra-car-shadow" x="-40%" y="-40%" width="180%" height="180%">
          <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#020617" flood-opacity="0.46" />
        </filter>
      </defs>

      <!-- ======== BACKGROUND LAYER 1: SKY + PARALLAX HORIZON ======== -->
      <rect width="1200" height="280" fill="url(#ultra-sky)" />

      <g class="parallax-sky-layer">
        <!-- Orbital Telemetry Rings -->
        <g opacity="0.32">
          <circle cx="908" cy="122" r="215" stroke="var(--color-primary)" stroke-width="1" stroke-dasharray="5 7" />
          <circle cx="908" cy="122" r="340" stroke="var(--cov-stroke)" stroke-width="1" stroke-dasharray="3 9" />
          <circle cx="230" cy="40" r="190" stroke="var(--color-primary)" stroke-width="1" stroke-dasharray="4 8" opacity="0.4" />
        </g>

        <!-- Architectural Grid -->
        <g opacity="0.14" stroke="var(--cov-grid)" stroke-width="1">
          <path d="M0 70H1200 M0 140H1200 M0 210H1200" />
          <path d="M120 0V280 M240 0V280 M360 0V280 M480 0V280 M600 0V280 M720 0V280 M840 0V280 M960 0V280 M1080 0V280" />
        </g>

        <!-- Urban Terminal Buildings + Twinkling Windows -->
        <rect x="34" y="18" width="96" height="68" rx="8" fill="var(--cov-building)" stroke="var(--cov-stroke)" stroke-width="1.2" />
        <rect x="46" y="30" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="64" y="30" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="82" y="30" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="100" y="30" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="46" y="48" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="82" y="48" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="100" y="48" width="12" height="10" rx="2" class="skyline-win" fill="var(--cov-window)" />

        <rect x="142" y="28" width="78" height="58" rx="8" fill="var(--cov-building)" stroke="var(--cov-stroke)" stroke-width="1.2" />
        <rect x="154" y="40" width="11" height="9" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="172" y="40" width="11" height="9" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="190" y="40" width="11" height="9" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="154" y="56" width="11" height="9" rx="2" class="skyline-win" fill="var(--cov-window)" />
        <rect x="190" y="56" width="11" height="9" rx="2" class="skyline-win" fill="var(--cov-window)" />

        <!-- Rotating AI Telemetry Radar Hub -->
        <g transform="translate(362, 54)">
          <circle cx="0" cy="0" r="34" fill="var(--cov-building)" stroke="var(--cov-stroke)" stroke-width="1" opacity="0.88" />
          <circle cx="0" cy="0" r="24" stroke="var(--color-primary)" stroke-width="1" stroke-dasharray="3 4" opacity="0.45" />
          <circle cx="0" cy="0" r="12" stroke="var(--color-primary)" stroke-width="1" opacity="0.4" />
          <g class="radar-sweep-group">
            <path d="M0 0 L30 -10 A32 32 0 0 1 28 16 Z" fill="url(#ultra-radar-wedge)" />
            <line x1="0" y1="0" x2="31" y2="2" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.9" />
          </g>
          <circle cx="0" cy="0" r="3.5" fill="var(--color-primary)" />
          <circle cx="14" cy="-12" r="2" fill="#5EEAD4" />
          <circle cx="-16" cy="9" r="2" fill="#5EEAD4" opacity="0.7" />
        </g>

        <!-- Landscaped Divider Park Strip -->
        <rect x="424" y="34" width="290" height="44" rx="22" fill="var(--cov-grass)" stroke="var(--cov-stroke)" stroke-width="1" />
        <circle cx="458" cy="56" r="14" fill="var(--cov-tree)" />
        <circle cx="476" cy="60" r="10" fill="var(--cov-tree-dark)" />
        <circle cx="566" cy="56" r="13" fill="var(--cov-tree)" />
        <circle cx="672" cy="56" r="15" fill="var(--cov-tree)" />
        <circle cx="654" cy="61" r="11" fill="var(--cov-tree-dark)" />

        <!-- Bottom-Left Safe Landscape Island (Clear Behind Avatar) -->
        <rect x="24" y="218" width="218" height="44" rx="22" fill="var(--cov-grass)" stroke="var(--cov-stroke)" stroke-width="1" opacity="0.82" />
        <circle cx="196" cy="238" r="13" fill="var(--cov-tree)" />
        <circle cx="218" cy="244" r="9" fill="var(--cov-tree-dark)" />
      </g>

      <!-- ======== MIDGROUND LAYER 2: PARALLAX SMART PLAZA & TRAFFIC ======== -->
      <g class="parallax-plaza-layer">
        <!-- Outer Express Horizon Lane -->
        <rect x="0" y="246" width="1200" height="34" fill="var(--cov-road-bot)" opacity="0.58" />
        <line x1="0" y1="263" x2="1200" y2="263" stroke="var(--cov-dash)" stroke-width="1.2" stroke-dasharray="10 14" opacity="0.35" />

        <g class="express-car-a" transform="translate(0, 255)" opacity="0.72">
          <rect x="-15" y="-6" width="30" height="12" rx="4" fill="#334155" />
          <rect x="-7" y="-4" width="14" height="8" rx="2.5" fill="#0F172A" />
          <rect x="13" y="-4" width="2.5" height="2.5" rx="1" fill="#99F6E4" />
          <rect x="13" y="1.5" width="2.5" height="2.5" rx="1" fill="#99F6E4" />
        </g>

        <g class="express-car-b" transform="translate(0, 271)" opacity="0.65">
          <rect x="-15" y="-6" width="30" height="12" rx="4" fill="#475569" />
          <rect x="-7" y="-4" width="14" height="8" rx="2.5" fill="#0F172A" />
          <rect x="-15.5" y="-4" width="2.5" height="2.5" rx="1" fill="#FDE047" />
          <rect x="-15.5" y="1.5" width="2.5" height="2.5" rx="1" fill="#FDE047" />
        </g>

        <!-- Curved Boulevard Roadway -->
        <path
          d="M 105 152 C 260 152, 345 206, 520 206 C 695 206, 785 196, 920 196 L 1185 196"
          stroke="url(#ultra-road)"
          stroke-width="62"
          stroke-linecap="round"
          fill="none"
        />
        <path
          d="M 105 121 C 260 121, 345 175, 520 175 C 695 175, 785 165, 920 165 L 1185 165"
          stroke="var(--cov-stroke)"
          stroke-width="1.2"
          opacity="0.72"
          fill="none"
        />
        <path
          d="M 105 183 C 260 183, 345 237, 520 237 C 695 237, 785 227, 920 227 L 1185 227"
          stroke="var(--cov-stroke)"
          stroke-width="1.2"
          opacity="0.72"
          fill="none"
        />
        <path
          d="M 135 152 C 265 152, 350 206, 520 206 C 695 206, 785 196, 1165 196"
          stroke="var(--cov-dash)"
          stroke-width="2"
          stroke-dasharray="14 14"
          opacity="0.52"
          fill="none"
        />

        <!-- Runway Directional Chevrons -->
        <g class="aisle-chevrons" stroke="var(--color-primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none">
          <g transform="translate(465, 204)"><polyline class="aisle-chevron" points="-4,-5 2,0 -4,5" /></g>
          <g transform="translate(555, 206)"><polyline class="aisle-chevron" points="-4,-5 2,0 -4,5" /></g>
          <g transform="translate(645, 203)"><polyline class="aisle-chevron" points="-4,-5 2,0 -4,5" /></g>
          <g transform="translate(735, 198)"><polyline class="aisle-chevron" points="-4,-5 2,0 -4,5" /></g>
          <g transform="translate(820, 196)"><polyline class="aisle-chevron" points="-4,-5 2,0 -4,5" /></g>
        </g>

        <!-- Smart Parking Plaza (Interactive Bays P-01..P-04) -->
        <rect x="760" y="62" width="386" height="110" rx="14" fill="var(--cov-road-bot)" opacity="0.65" />

        <!-- P-01 Occupied -->
        <g transform="translate(778, 74)" class="bay-interactive" @click="selectedBay = 'P-01'">
          <rect
            width="76"
            height="90"
            rx="10"
            fill="var(--cov-bay-fill)"
            :stroke="selectedBay === 'P-01' ? '#38BDF8' : 'var(--cov-stroke)'"
            :stroke-width="selectedBay === 'P-01' ? '2.2' : '1.4'"
          />
          <circle cx="65" cy="11" r="3" fill="#F59E0B" />
          <text x="38" y="81" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-01</text>
          <g transform="translate(38, 42) rotate(-90)" opacity="0.82">
            <rect x="-20" y="-11" width="40" height="22" rx="7" fill="#334155" stroke="#64748B" stroke-width="0.9" />
            <rect x="-10" y="-8" width="20" height="16" rx="4" fill="#0F172A" />
            <path d="M4 -7 C7 -5, 7 5, 4 7 L9 6 L9 -6 Z" fill="#94A3B8" opacity="0.55" />
          </g>
        </g>

        <!-- P-02 TARGET AUTONOMOUS + INDUCTIVE CHARGING BAY (Center: 908, 122) -->
        <g transform="translate(870, 74)" class="bay-interactive" @click="selectedBay = 'P-02'">
          <ellipse class="bay-p02-glow" cx="38" cy="46" rx="54" ry="56" fill="url(#ultra-bay-glow)" opacity="0.15" />

          <!-- Dual Sonar Pulse Rings -->
          <g transform="translate(38, 46)">
            <circle class="bay-sonar-1" cx="0" cy="0" r="34" stroke="var(--color-primary)" stroke-width="2" fill="none" opacity="0" />
            <circle class="bay-sonar-2" cx="0" cy="0" r="34" stroke="#5EEAD4" stroke-width="1.5" fill="none" opacity="0" />
            <!-- Inductive Wireless Charging Pad Rings -->
            <circle class="inductive-coil-ring" cx="0" cy="0" r="14" stroke="#2DD4BF" stroke-width="1.8" fill="none" opacity="0" />
            <circle class="inductive-coil-ring" cx="0" cy="0" r="22" stroke="#5EEAD4" stroke-width="1.4" stroke-dasharray="3 3" fill="none" opacity="0" />
          </g>

          <path
            class="bay-p02-idle"
            d="M 10 0 L 66 0 Q 76 0 76 10 L 76 80 Q 76 90 66 90 L 10 90 Q 0 90 0 80 L 0 10 Q 0 0 10 0 Z"
            fill="var(--cov-target-fill)"
            stroke="var(--color-primary)"
            stroke-width="1.8"
          />
          <path
            class="bay-p02-locked"
            d="M 14 -3 L 62 -3 Q 79 -3 79 14 L 79 76 Q 79 93 62 93 L 14 93 Q -3 93 -3 76 L -3 14 Q -3 -3 14 -3 Z"
            fill="none"
            opacity="0"
          />

          <g class="hud-brackets" stroke="#5EEAD4" stroke-width="2.4" stroke-linecap="round" fill="none">
            <path class="hud-bracket" d="M-2 10 V0 H8" />
            <path class="hud-bracket" d="M68 0 H78 V10" />
            <path class="hud-bracket" d="M-2 80 V90 H8" />
            <path class="hud-bracket" d="M68 90 H78 V80" />
          </g>

          <text x="38" y="51" text-anchor="middle" fill="var(--color-primary)" font-size="16" font-weight="800" font-family="Inter, sans-serif" opacity="0.32">P</text>
          <text x="38" y="81" text-anchor="middle" fill="var(--color-primary)" font-size="10" font-weight="800" font-family="Inter, sans-serif">P-02</text>
        </g>

        <!-- P-03 Vacating Bay -->
        <g transform="translate(962, 74)" class="bay-interactive" @click="selectedBay = 'P-03'">
          <rect
            width="76"
            height="90"
            rx="10"
            fill="var(--cov-bay-fill)"
            :stroke="selectedBay === 'P-03' ? '#38BDF8' : 'var(--cov-stroke)'"
            :stroke-width="selectedBay === 'P-03' ? '2.2' : '1.4'"
            stroke-dasharray="5 4"
          />
          <circle class="bay-p03-led" cx="65" cy="11" r="3" fill="#F59E0B" />
          <text x="38" y="81" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-03</text>
        </g>

        <!-- P-04 Occupied -->
        <g transform="translate(1054, 74)" class="bay-interactive" @click="selectedBay = 'P-04'">
          <rect
            width="76"
            height="90"
            rx="10"
            fill="var(--cov-bay-fill)"
            :stroke="selectedBay === 'P-04' ? '#38BDF8' : 'var(--cov-stroke)'"
            :stroke-width="selectedBay === 'P-04' ? '2.2' : '1.4'"
          />
          <circle cx="65" cy="11" r="3" fill="#F59E0B" />
          <text x="38" y="81" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-04</text>
          <g transform="translate(38, 42) rotate(-90)" opacity="0.72">
            <rect x="-20" y="-11" width="40" height="22" rx="7" fill="#1E293B" stroke="#475569" stroke-width="0.9" />
            <rect x="-10" y="-8" width="20" height="16" rx="4" fill="#0F172A" />
            <path d="M4 -7 C7 -5, 7 5, 4 7 L9 6 L9 -6 Z" fill="#94A3B8" opacity="0.45" />
          </g>
        </g>

        <!-- Gatehouse + ANPR Plate Scanner + Boom Barrier -->
        <g transform="translate(268, 152)">
          <rect x="-8" y="-38" width="16" height="76" rx="4" fill="var(--cov-building)" stroke="var(--cov-stroke)" stroke-width="1.2" opacity="0.92" />
          <rect class="scanner-laser-beam" x="-8" y="-28" width="16" height="56" rx="3" fill="url(#ultra-scan)" opacity="0" />
          <g class="anpr-scan-box" opacity="0">
            <rect x="-18" y="-22" width="36" height="44" rx="6" stroke="#5EEAD4" stroke-width="1.4" stroke-dasharray="4 3" fill="none" />
          </g>
          <rect x="-12" y="34" width="22" height="18" rx="5" fill="#0F172A" stroke="var(--color-primary)" stroke-width="1.2" />
          <circle class="gate-status-led" cx="-1" cy="43" r="3.5" fill="#F59E0B" />
          <g transform="translate(0, 36)">
            <g class="gate-barrier-arm">
              <rect x="-3" y="-66" width="6" height="66" rx="3" fill="#F8FAFC" stroke="#0F172A" stroke-width="1" />
              <rect x="-3" y="-56" width="6" height="10" fill="#EF4444" />
              <rect x="-3" y="-34" width="6" height="10" fill="#EF4444" />
              <rect x="-3" y="-12" width="6" height="8" fill="#14B8A6" />
            </g>
          </g>
        </g>

        <!-- Hidden Outgoing Route from Bay P-03 -> Exit Right -->
        <path
          class="exit-route"
          d="M 1000 116 L 1000 155 C 1000 182, 1020 196, 1060 196 L 1220 196"
          stroke="none"
          fill="none"
        />

        <!-- Outgoing Sedan from P-03 -->
        <g class="outgoing-car" filter="url(#ultra-car-shadow)" opacity="0.88">
          <rect x="-20" y="-10.5" width="40" height="21" rx="7" fill="#334155" stroke="#94A3B8" stroke-width="0.9" />
          <rect x="-10" y="-8" width="20" height="16" rx="4" fill="#0F172A" />
          <path d="M4 -7 C8 -5, 8 5, 4 7 L10 6 L10 -6 Z" fill="#94A3B8" opacity="0.65" />
          <rect x="17" y="-8" width="2.5" height="3.5" rx="1" fill="#FDE047" />
          <rect x="17" y="4.5" width="2.5" height="3.5" rx="1" fill="#FDE047" />
        </g>

        <!-- Main EV Navigation Route -->
        <path
          d="M 135 152 C 265 152, 350 206, 520 206 C 695 206, 785 196, 862 196 C 898 196, 908 174, 908 144 L 908 122"
          stroke="var(--color-primary)"
          stroke-width="2"
          stroke-dasharray="5 9"
          opacity="0.2"
          fill="none"
        />
        <path
          class="parking-route"
          d="M 135 152 C 265 152, 350 206, 520 206 C 695 206, 785 196, 862 196 C 898 196, 908 174, 908 144 L 908 122"
          stroke="var(--color-primary)"
          stroke-width="3.2"
          stroke-linecap="round"
          filter="url(#ultra-neon)"
          fill="none"
        />

        <!-- Primary Animated EV Sedan -->
        <g class="hero-ev-car" filter="url(#ultra-car-shadow)" opacity="0">
          <g class="ev-headlights">
            <path d="M23 -8 L66 -21 L66 21 L23 8 Z" fill="url(#ultra-beam)" />
          </g>
          <rect x="-24" y="-12.5" width="48" height="25" rx="8.5" fill="url(#ultra-ev-body)" stroke="#94A3B8" stroke-width="1.1" />
          <rect x="-12" y="-9.5" width="24" height="19" rx="5" fill="#0B1220" />
          <rect x="-8" y="-1.5" width="14" height="3" rx="1.5" fill="#1E293B" />
          <rect class="ev-charge-bar" x="-8" y="-1.5" width="14" height="3" rx="1.5" fill="#2DD4BF" opacity="0" />
          <path d="M5 -8.5 C10.5 -6, 10.5 6, 5 8.5 L13 7.5 L13 -7.5 Z" fill="#5EEAD4" opacity="0.88" />
          <path d="M-11 -7.5 L-7 -8.5 L-7 8.5 L-11 7.5 Z" fill="#38BDF8" opacity="0.4" />
          <rect x="4" y="-14.5" width="4.5" height="2.5" rx="1" fill="#64748B" />
          <rect x="4" y="12" width="4.5" height="2.5" rx="1" fill="#64748B" />
          <rect x="21" y="-9.5" width="3" height="4.5" rx="1" fill="#99F6E4" />
          <rect x="21" y="5" width="3" height="4.5" rx="1" fill="#99F6E4" />
          <g class="ev-brakelights" opacity="0.2">
            <rect x="-24.5" y="-10" width="3.5" height="5" rx="1.2" fill="#EF4444" filter="url(#ultra-neon)" />
            <rect x="-24.5" y="5" width="3.5" height="5" rx="1.2" fill="#EF4444" filter="url(#ultra-neon)" />
          </g>
        </g>

        <!-- Floating Locked Bay HUD Callout -->
        <g transform="translate(908, 44)">
          <g class="bay-hud-card" opacity="0">
            <rect x="-64" y="-13" width="128" height="24" rx="12" fill="#0B1220" stroke="#14B8A6" stroke-width="1.2" />
            <circle cx="-48" cy="-1" r="3.5" fill="#10B981" />
            <text x="6" y="2.5" text-anchor="middle" fill="#F8FAFC" font-size="8.8" font-weight="700" letter-spacing="0.06em" font-family="Inter, sans-serif">P-02 • INDUCTIVE SYNC</text>
          </g>
        </g>
      </g>
    </svg>

    <!-- Interactive Telemetry Controls Overlay (Bottom-Right Safe Area) -->
    <div class="cover-controls-bar" role="region" aria-label="Smart parking animation controls">
      <div class="bay-telemetry-pill">
        <span class="telemetry-dot" :style="{ backgroundColor: activeBayInfo.color }"></span>
        <span class="telemetry-code">{{ activeBayInfo.code }}</span>
        <span class="telemetry-sep">·</span>
        <span class="telemetry-text">{{ activeBayInfo.state }}</span>
      </div>
      <div class="control-btns">
        <button
          type="button"
          class="cover-ctrl-btn"
          :title="isPaused ? 'Resume animation' : 'Pause animation'"
          :aria-label="isPaused ? 'Resume animation' : 'Pause animation'"
          @click="togglePlayback"
        >
          <svg v-if="isPaused" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
            <polygon points="6 4 20 12 6 20 6 4" />
          </svg>
          <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
            <rect x="6" y="4" width="4" height="16" />
            <rect x="14" y="4" width="4" height="16" />
          </svg>
        </button>
        <button
          type="button"
          class="cover-ctrl-btn"
          title="Replay sequence"
          aria-label="Replay parking sequence"
          @click="replaySequence"
        >
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
            <polyline points="1 4 1 10 7 10" />
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-parking-animation {
  --cov-bg-start: #EEF4FA;
  --cov-bg-mid: #E2ECF5;
  --cov-bg-end: #D7E7F3;
  --cov-grid: #94A3B8;
  --cov-building: #FFFFFF;
  --cov-window: #CBD5E1;
  --cov-road-top: #CBD5E1;
  --cov-road-bot: #E2E8F0;
  --cov-stroke: #94A3B8;
  --cov-dash: #FFFFFF;
  --cov-bay-fill: rgba(255, 255, 255, 0.8);
  --cov-target-fill: rgba(15, 118, 110, 0.12);
  --cov-grass: #D1FAE5;
  --cov-tree: #34D399;
  --cov-tree-dark: #10B981;
  --cov-muted: #64748B;

  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--cov-bg-start) 0%, var(--cov-bg-mid) 55%, var(--cov-bg-end) 100%);
}

.profile-parking-animation.is-dark,
:global([data-theme='dark']) .profile-parking-animation {
  --cov-bg-start: #070E1A;
  --cov-bg-mid: #0C1829;
  --cov-bg-end: #102138;
  --cov-grid: #334155;
  --cov-building: #152338;
  --cov-window: #FDE047;
  --cov-road-top: #18273E;
  --cov-road-bot: #111F33;
  --cov-stroke: #334155;
  --cov-dash: #64748B;
  --cov-bay-fill: rgba(17, 28, 46, 0.84);
  --cov-target-fill: rgba(20, 184, 166, 0.16);
  --cov-grass: rgba(16, 185, 129, 0.14);
  --cov-tree: #059669;
  --cov-tree-dark: #047857;
  --cov-muted: #94A3B8;
}

@media (prefers-color-scheme: dark) {
  :global(:root:not([data-theme='light'])) .profile-parking-animation {
    --cov-bg-start: #070E1A;
    --cov-bg-mid: #0C1829;
    --cov-bg-end: #102138;
    --cov-grid: #334155;
    --cov-building: #152338;
    --cov-window: #FDE047;
    --cov-road-top: #18273E;
    --cov-road-bot: #111F33;
    --cov-stroke: #334155;
    --cov-dash: #64748B;
    --cov-bay-fill: rgba(17, 28, 46, 0.84);
    --cov-target-fill: rgba(20, 184, 166, 0.16);
    --cov-grass: rgba(16, 185, 129, 0.14);
    --cov-tree: #059669;
    --cov-tree-dark: #047857;
    --cov-muted: #94A3B8;
  }
}

.cover-svg {
  width: 100%;
  height: 100%;
  display: block;
}

.hero-ev-car,
.outgoing-car,
.gate-barrier-arm,
.radar-sweep-group,
.bay-sonar-1,
.bay-sonar-2,
.inductive-coil-ring {
  transform-origin: 0 0;
}

.hud-bracket,
.bay-hud-card,
.aisle-chevron,
.anpr-scan-box {
  transform-box: fill-box;
  transform-origin: center;
}

.ev-charge-bar {
  transform-box: fill-box;
  transform-origin: left center;
}

.bay-interactive {
  cursor: pointer;
}

/* Interactive Telemetry Controls Bar */
.cover-controls-bar {
  position: absolute;
  right: 18px;
  bottom: 14px;
  z-index: 4;
  display: flex;
  align-items: center;
  gap: 8px;
}

.bay-telemetry-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  color: var(--color-text);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
}

.profile-parking-animation.is-dark .bay-telemetry-pill {
  color: #f1f5f9;
  background: rgba(11, 18, 32, 0.86);
  border-color: rgba(148, 163, 184, 0.28);
}

@media (prefers-color-scheme: dark) {
  :global(:root:not([data-theme='light'])) .bay-telemetry-pill {
    color: #f1f5f9;
    background: rgba(11, 18, 32, 0.86);
    border-color: rgba(148, 163, 184, 0.28);
  }
}

.telemetry-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
}

.telemetry-code {
  font-weight: 800;
  letter-spacing: 0.04em;
}

.telemetry-sep {
  opacity: 0.45;
}

.control-btns {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.cover-ctrl-btn {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  color: var(--color-text);
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: border-color var(--transition-fast), color var(--transition-fast), transform var(--transition-fast);
}

.profile-parking-animation.is-dark .cover-ctrl-btn {
  background: rgba(11, 18, 32, 0.86);
  border-color: rgba(148, 163, 184, 0.28);
  color: #f1f5f9;
}

.cover-ctrl-btn:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  transform: scale(1.06);
}

@media (max-width: 640px) {
  .telemetry-sep,
  .telemetry-text {
    display: none;
  }
}
</style>
