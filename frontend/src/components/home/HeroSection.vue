<script setup>
/**
 * HeroSection Component — Full-Width Animated Parking Canvas
 *
 * Layout: Full-width rectangular parking facility background spanning
 * left-to-right behind hero content. Text, Digital Pass, and Availability
 * Card overlay on top.
 *
 * Vehicle Journey:
 * 1. Car enters through facility entrance gate.
 * 2. Follows the curved parking lane in sync with SVG route drawing.
 * 3. Smoothly turns into reserved parking stall P-04.
 * 4. Straightens out and comes to a complete halt inside the bay.
 * 5. Stall illuminates with Teal success indicator & verified status.
 * 6. Pauses for ~2 seconds.
 * 7. Smoothly resets and restarts.
 */
import { onMounted, onBeforeUnmount } from 'vue'
import { animate, createTimeline, svg } from 'animejs'
import AppButton from '../common/AppButton.vue'

defineEmits(['explore', 'howItWorks'])

let activeTimeline = null
let activeAnimations = []

onMounted(() => {
  const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches

  if (prefersReduced) {
    const carEls = document.querySelectorAll('.car')
    const circuitEls = document.querySelectorAll('.circuit')
    const targetBays = document.querySelectorAll('.target-parking-bay')
    const slots = [644, 724, 804, 888]
    carEls.forEach((car, i) => {
      if (car) {
        car.style.transform = `translate(${slots[i]}px, 95px) rotate(-90deg)`
        car.style.opacity = '1'
      }
    })
    circuitEls.forEach(c => { if (c) c.style.opacity = '0.8' })
    targetBays.forEach(b => {
      const ring = b.querySelector('.bay-success-ring')
      const badge = b.querySelector('.parking-success-badge')
      if (ring) ring.style.opacity = '1'
      if (badge) badge.style.opacity = '1'
    })
    return
  }

  try {
    // 1. Canvas entrance animation
    const entranceCanvas = animate('.hero-parking-canvas', {
      opacity: [0, 1],
      ease: 'outCubic',
      duration: 800,
    })
    activeAnimations.push(entranceCanvas)

    // 2. Overlay content stagger
    const entranceOverlay = animate('.hero-overlay-content', {
      translateY: [20, 0],
      opacity: [0, 1],
      ease: 'outCubic',
      duration: 700,
      delay: 200,
    })
    activeAnimations.push(entranceOverlay)

    // 4. Realistic Parking Journey Timeline for 4 Sequential Cars (P-04, P-05, P-06, P-07)
    const cars = [
      { id: 1, carSel: '.car-1', circuitSel: '.circuit-1', ringSel: '.bay-ring-1', badgeSel: '.bay-badge-1', passText: 'Spot P-04 Reserved' },
      { id: 2, carSel: '.car-2', circuitSel: '.circuit-2', ringSel: '.bay-ring-2', badgeSel: '.bay-badge-2', passText: 'Spot P-05 Reserved' },
      { id: 3, carSel: '.car-3', circuitSel: '.circuit-3', ringSel: '.bay-ring-3', badgeSel: '.bay-badge-3', passText: 'Spot P-06 Reserved' },
      { id: 4, carSel: '.car-4', circuitSel: '.circuit-4', ringSel: '.bay-ring-4', badgeSel: '.bay-badge-4', passText: 'Spot P-07 Reserved' },
    ]

    const pathData = [
      { motion: svg.createMotionPath('.circuit-1'), drawable: svg.createDrawable('.circuit-1') },
      { motion: svg.createMotionPath('.circuit-2'), drawable: svg.createDrawable('.circuit-2') },
      { motion: svg.createMotionPath('.circuit-3'), drawable: svg.createDrawable('.circuit-3') },
      { motion: svg.createMotionPath('.circuit-4'), drawable: svg.createDrawable('.circuit-4') },
    ]

    activeTimeline = createTimeline({
      loop: true,
      loopDelay: 800,
    })

    // Initial reset of all 4 cars and slots
    cars.forEach(c => {
      activeTimeline
        .set(c.carSel, { opacity: 0 }, 0)
        .set(c.ringSel, { opacity: 0, scale: 0.95 }, 0)
        .set(c.badgeSel, { opacity: 0, scale: 0.8 }, 0)
    })

    // Sequential timing for each car:
    // Car 1 -> enters at 100ms, drives 3200ms -> parks at P-04 (3300ms)
    // Car 2 -> enters at 3500ms, drives 3400ms -> parks at P-05 (6900ms)
    // Car 3 -> enters at 7100ms, drives 3600ms -> parks at P-06 (10700ms)
    // Car 4 -> enters at 10900ms, drives 3800ms -> parks at P-07 (14700ms)
    // All hold parked until 17500ms -> fade out -> loop resets!
    const timings = [
      { start: 100, driveDur: 3200 },
      { start: 3500, driveDur: 3400 },
      { start: 7100, driveDur: 3600 },
      { start: 10900, driveDur: 3800 },
    ]

    cars.forEach((c, idx) => {
      const t = timings[idx]
      const p = pathData[idx]
      const parkTime = t.start + t.driveDur

      activeTimeline
        // Fade in entering car
        .add(c.carSel, {
          opacity: [0, 1],
          duration: 350,
          ease: 'outQuad',
        }, t.start)
        // Follow straight route
        .add(c.carSel, {
          ...p.motion,
          duration: t.driveDur,
          ease: 'inOutCubic',
        }, t.start + 50)
        // Draw route line in sync
        .add(p.drawable, {
          draw: ['0 0', '0 1'],
          duration: t.driveDur,
          ease: 'inOutCubic',
        }, t.start + 50)
        // Arrival: Success ring & Badge pop up
        .add(c.ringSel, {
          opacity: [0, 1],
          scale: [0.94, 1],
          duration: 350,
          ease: 'outBack(2)',
        }, parkTime)
        .add(c.badgeSel, {
          opacity: [0, 1],
          scale: [0.8, 1],
          duration: 350,
          ease: 'outBack(2)',
        }, parkTime + 80)
    })

    // All 4 parked cars remain on screen for ~2.5 seconds, then smoothly fade out
    activeTimeline.add('.car, .circuit, .bay-success-ring, .parking-success-badge', {
      opacity: 0,
      duration: 600,
      ease: 'outQuad',
    }, 17500)
  } catch (err) {
    console.error('Error initializing hero parking journey:', err)
  }
})

onBeforeUnmount(() => {
  if (activeTimeline) {
    try {
      if (typeof activeTimeline.pause === 'function') activeTimeline.pause()
      if (typeof activeTimeline.revert === 'function') activeTimeline.revert()
    } catch {
      // Ignore cleanup error
    }
    activeTimeline = null
  }

  activeAnimations.forEach((anim) => {
    try {
      if (anim && typeof anim.pause === 'function') anim.pause()
      if (anim && typeof anim.revert === 'function') anim.revert()
    } catch {
      // Ignore cleanup error
    }
  })
  activeAnimations = []
})
</script>

<template>
  <section class="hero-section">
    <!-- =====================================================================
         FULL-WIDTH PARKING CANVAS BACKGROUND
         Spans left-to-right as a rectangular background behind all content
         ===================================================================== -->
    <div class="hero-parking-canvas" aria-hidden="true">
      <svg
        viewBox="0 0 1200 480"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="parking-canvas-svg"
        preserveAspectRatio="xMidYMid slice"
      >
        <defs>
          <!-- Sky gradient — uses CSS custom properties for theme awareness -->
          <linearGradient id="sky-gradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="var(--hero-sky-top)" />
            <stop offset="60%" stop-color="var(--hero-sky-mid)" />
            <stop offset="100%" stop-color="var(--hero-sky-bottom)" />
          </linearGradient>

          <!-- Ground / asphalt gradient -->
          <linearGradient id="ground-gradient" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="var(--hero-ground-top)" />
            <stop offset="100%" stop-color="var(--hero-ground-bottom)" />
          </linearGradient>

          <!-- Building fill -->
          <linearGradient id="building-fill" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="var(--hero-building-top)" />
            <stop offset="100%" stop-color="var(--hero-building-bottom)" />
          </linearGradient>

          <!-- Headlight glow beam -->
          <linearGradient id="car-headlight-beam" x1="0%" y1="50%" x2="100%" y2="50%">
            <stop offset="0%" stop-color="#5EEAD4" stop-opacity="0.9" />
            <stop offset="45%" stop-color="#14B8A6" stop-opacity="0.35" />
            <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
          </linearGradient>

          <!-- Subtle route glow filter -->
          <filter id="route-glow" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="2" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
          </filter>

          <!-- Soft lamp glow filter (night mode) -->
          <filter id="lamp-glow" x="-100%" y="-100%" width="300%" height="300%">
            <feGaussianBlur stdDeviation="6" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
          </filter>
        </defs>

        <!-- ======== SKY ======== -->
        <rect width="1200" height="280" fill="url(#sky-gradient)" />

        <!-- Subtle clouds (day) / stars (night) — using theme opacity -->
        <g class="sky-clouds" opacity="var(--hero-cloud-opacity)">
          <ellipse cx="180" cy="80" rx="70" ry="20" fill="white" opacity="0.35" />
          <ellipse cx="220" cy="72" rx="50" ry="18" fill="white" opacity="0.3" />
          <ellipse cx="800" cy="60" rx="60" ry="16" fill="white" opacity="0.25" />
          <ellipse cx="840" cy="55" rx="40" ry="14" fill="white" opacity="0.2" />
          <ellipse cx="1050" cy="90" rx="55" ry="15" fill="white" opacity="0.2" />
        </g>

        <!-- Stars (visible in dark mode only) -->
        <g class="sky-stars" opacity="var(--hero-star-opacity)">
          <circle cx="100" cy="30" r="1.2" fill="white" opacity="0.7" />
          <circle cx="250" cy="50" r="0.8" fill="white" opacity="0.5" />
          <circle cx="400" cy="25" r="1" fill="white" opacity="0.6" />
          <circle cx="550" cy="45" r="0.7" fill="white" opacity="0.4" />
          <circle cx="700" cy="20" r="1.1" fill="white" opacity="0.6" />
          <circle cx="900" cy="35" r="0.9" fill="white" opacity="0.5" />
          <circle cx="1050" cy="28" r="1" fill="white" opacity="0.7" />
          <circle cx="1150" cy="55" r="0.8" fill="white" opacity="0.4" />
          <circle cx="320" cy="15" r="0.6" fill="white" opacity="0.3" />
          <circle cx="620" cy="12" r="0.8" fill="white" opacity="0.5" />
        </g>

        <!-- ======== BACKGROUND BUILDINGS (Far skyline) ======== -->
        <g class="skyline-buildings">
          <!-- Building 1 — Tall residential tower (far left) -->
          <rect x="60" y="110" width="65" height="170" rx="3" fill="var(--hero-building-far)" stroke="var(--hero-building-stroke)" stroke-width="0.8" />
          <g class="building-windows" opacity="var(--hero-window-light-opacity)">
            <rect x="70" y="120" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="84" y="120" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="100" y="120" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="70" y="138" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="84" y="138" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="100" y="138" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="70" y="156" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="84" y="156" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="70" y="174" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="100" y="174" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="84" y="192" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="70" y="210" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="100" y="210" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
          </g>

          <!-- Building 2 — Mid-height office (left-center) -->
          <rect x="145" y="160" width="55" height="120" rx="3" fill="var(--hero-building-mid)" stroke="var(--hero-building-stroke)" stroke-width="0.8" />
          <g class="building-windows" opacity="var(--hero-window-light-opacity)">
            <rect x="153" y="170" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="165" y="170" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="179" y="170" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="153" y="186" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="165" y="186" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="179" y="186" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="153" y="202" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="179" y="202" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="165" y="218" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="153" y="234" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="179" y="234" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
          </g>

          <!-- Building 3 — Commercial block (far right) -->
          <rect x="1020" y="130" width="70" height="150" rx="3" fill="var(--hero-building-far)" stroke="var(--hero-building-stroke)" stroke-width="0.8" />
          <g class="building-windows" opacity="var(--hero-window-light-opacity)">
            <rect x="1030" y="140" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1045" y="140" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1060" y="140" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1075" y="140" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1030" y="158" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1045" y="158" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1075" y="158" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1030" y="176" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1060" y="176" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1045" y="194" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1075" y="194" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1030" y="212" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1060" y="212" width="8" height="10" rx="1" fill="var(--hero-window-glow)" />
          </g>

          <!-- Building 4 — Apartment (right-center) -->
          <rect x="1110" y="170" width="50" height="110" rx="3" fill="var(--hero-building-mid)" stroke="var(--hero-building-stroke)" stroke-width="0.8" />
          <g class="building-windows" opacity="var(--hero-window-light-opacity)">
            <rect x="1118" y="180" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1132" y="180" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1146" y="180" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1118" y="196" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1146" y="196" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1132" y="212" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1118" y="228" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
            <rect x="1146" y="228" width="7" height="9" rx="1" fill="var(--hero-window-glow)" />
          </g>
        </g>

        <!-- ======== TREES (between buildings and parking) ======== -->
        <g class="landscape-trees">
          <!-- Tree cluster left -->
          <circle cx="50" cy="260" r="18" fill="var(--hero-tree-fill)" />
          <circle cx="38" cy="268" r="14" fill="var(--hero-tree-fill-dark)" />
          <rect x="46" y="274" width="8" height="14" rx="2" fill="var(--hero-trunk)" />

          <circle cx="135" cy="265" r="15" fill="var(--hero-tree-fill)" />
          <circle cx="128" cy="272" r="12" fill="var(--hero-tree-fill-dark)" />
          <rect x="131" y="276" width="7" height="12" rx="2" fill="var(--hero-trunk)" />

          <!-- Tree cluster right -->
          <circle cx="1000" cy="258" r="17" fill="var(--hero-tree-fill)" />
          <circle cx="1012" cy="266" r="13" fill="var(--hero-tree-fill-dark)" />
          <rect x="996" y="272" width="8" height="14" rx="2" fill="var(--hero-trunk)" />

          <circle cx="1100" cy="262" r="14" fill="var(--hero-tree-fill)" />
          <circle cx="1092" cy="268" r="11" fill="var(--hero-tree-fill-dark)" />
          <rect x="1096" y="274" width="7" height="12" rx="2" fill="var(--hero-trunk)" />

          <!-- Small bushes -->
          <ellipse cx="240" cy="278" rx="12" ry="8" fill="var(--hero-tree-fill-dark)" opacity="0.7" />
          <ellipse cx="960" cy="276" rx="14" ry="9" fill="var(--hero-tree-fill-dark)" opacity="0.7" />
        </g>

        <!-- ======== GROUND / ROAD SURFACE ======== -->
        <rect x="0" y="280" width="1200" height="200" fill="url(#ground-gradient)" />

        <!-- Road edge / curb line -->
        <line x1="0" y1="282" x2="1200" y2="282" stroke="var(--hero-curb)" stroke-width="2" />

        <!-- ======== STREETLIGHTS ======== -->
        <g class="streetlights">
          <!-- Left streetlight -->
          <rect x="218" y="210" width="4" height="72" rx="1" fill="var(--hero-pole)" />
          <rect x="210" y="208" width="20" height="6" rx="3" fill="var(--hero-pole)" />
          <circle cx="220" cy="208" r="4" fill="var(--hero-lamp-off)" class="streetlamp-bulb" />
          <circle cx="220" cy="208" r="12" fill="var(--hero-lamp-glow)" class="streetlamp-glow" opacity="var(--hero-lamp-glow-opacity)" filter="url(#lamp-glow)" />

          <!-- Right streetlight -->
          <rect x="978" y="210" width="4" height="72" rx="1" fill="var(--hero-pole)" />
          <rect x="970" y="208" width="20" height="6" rx="3" fill="var(--hero-pole)" />
          <circle cx="980" cy="208" r="4" fill="var(--hero-lamp-off)" class="streetlamp-bulb" />
          <circle cx="980" cy="208" r="12" fill="var(--hero-lamp-glow)" class="streetlamp-glow" opacity="var(--hero-lamp-glow-opacity)" filter="url(#lamp-glow)" />
        </g>

        <!-- ======== PARKING FACILITY STRUCTURE ======== -->
        <!-- Parking building / entrance structure -->
        <g class="parking-structure">
          <!-- Main parking roof canopy -->
          <rect x="310" y="250" width="780" height="16" rx="4" fill="var(--hero-canopy)" stroke="var(--hero-building-stroke)" stroke-width="1" />
          <!-- Canopy support pillars -->
          <rect x="330" y="266" width="6" height="18" rx="1" fill="var(--hero-pole)" />
          <rect x="530" y="266" width="6" height="18" rx="1" fill="var(--hero-pole)" />
          <rect x="730" y="266" width="6" height="18" rx="1" fill="var(--hero-pole)" />
          <rect x="920" y="266" width="6" height="18" rx="1" fill="var(--hero-pole)" />
          <rect x="1060" y="266" width="6" height="18" rx="1" fill="var(--hero-pole)" />
          <!-- PARKLY signage on canopy -->
          <text x="700" y="262" font-family="'Inter', sans-serif" font-size="9" font-weight="800" fill="var(--color-primary)" text-anchor="middle" letter-spacing="0.2em">PARKLY SMART FACILITY</text>

          <!-- Overhead parking lights (on canopy) -->
          <g class="parking-canopy-lights" opacity="var(--hero-lamp-glow-opacity)">
            <circle cx="430" cy="268" r="6" fill="var(--hero-lamp-glow)" filter="url(#lamp-glow)" />
            <circle cx="600" cy="268" r="6" fill="var(--hero-lamp-glow)" filter="url(#lamp-glow)" />
            <circle cx="800" cy="268" r="6" fill="var(--hero-lamp-glow)" filter="url(#lamp-glow)" />
          </g>
        </g>

        <!-- ======== ENTRANCE GATE (far left of parking) ======== -->
        <g class="facility-gate-complex" transform="translate(275, 285)">
          <!-- Entry concrete island -->
          <rect x="0" y="20" width="40" height="10" rx="3" fill="var(--hero-surface-dark)" stroke="var(--hero-building-stroke)" stroke-width="0.8" />
          <!-- Terminal kiosk -->
          <rect x="5" y="4" width="12" height="20" rx="3" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1.2" />
          <circle cx="11" cy="10" r="2" fill="var(--color-success, #34D399)" />
          <!-- Barrier arm (raised) -->
          <line x1="17" y1="10" x2="42" y2="-4" stroke="var(--color-success, #34D399)" stroke-width="2.5" stroke-linecap="round" />
          <!-- Gate entrance light -->
          <circle cx="11" cy="2" r="3" fill="var(--hero-lamp-off)" class="gate-lamp" />
          <circle cx="11" cy="2" r="8" fill="var(--hero-lamp-glow)" class="gate-lamp-glow" opacity="var(--hero-lamp-glow-opacity)" filter="url(#lamp-glow)" />
          <text x="18" y="28" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="var(--hero-text-subtle)" letter-spacing="0.06em">IN · GATE</text>
        </g>

        <!-- ======== ASPHALT DRIVEWAY ======== -->
        <path
          d="M 280,310 L 1090,310 L 1090,336 L 280,336 Z"
          fill="var(--hero-asphalt)"
          opacity="0.9"
        />

        <!-- Lane markings (center dashes) -->
        <g stroke="var(--hero-lane-marking)" stroke-width="1.5" stroke-dasharray="8 12" opacity="0.5">
          <line x1="320" y1="323" x2="1080" y2="323" />
        </g>

        <!-- Directional arrows -->
        <g stroke="var(--hero-arrow)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.6">
          <path d="M 380,323 L 400,323 M 393,318 L 400,323 L 393,328" />
          <path d="M 550,323 L 570,323 M 563,318 L 570,323 L 563,328" />
          <path d="M 720,323 L 740,323 M 733,318 L 740,323 L 733,328" />
          <path d="M 870,323 L 890,323 M 883,318 L 890,323 L 883,328" />
        </g>

        <!-- Aisle markings -->
        <text x="440" y="348" font-family="'Inter', sans-serif" font-size="6" font-weight="700" fill="var(--hero-arrow)" letter-spacing="0.08em" opacity="0.5">AISLE 01</text>
        <text x="650" y="348" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="var(--hero-arrow)" letter-spacing="0.08em" opacity="0.4">MAX 10 KM/H</text>

        <!-- ======== PARKING BAYS ======== -->
        <g class="parking-bays-row">
          <!-- Stall P-01 (Occupied) -->
          <g transform="translate(370, 355)">
            <rect width="68" height="90" rx="4" fill="var(--hero-bay-fill)" stroke="var(--hero-bay-stroke)" stroke-width="1.2" />
            <line x1="0" y1="90" x2="68" y2="90" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="16" font-family="'Inter', sans-serif" font-size="8" font-weight="700" fill="var(--hero-text-subtle)" text-anchor="middle">P-01</text>
            <!-- Parked car -->
            <rect x="12" y="24" width="44" height="56" rx="9" fill="var(--hero-car-parked)" stroke="var(--hero-car-parked-stroke)" stroke-width="1" />
            <rect x="17" y="38" width="34" height="18" rx="4" fill="var(--hero-car-glass)" />
            <circle cx="19" cy="74" r="2" fill="#EF4444" opacity="0.7" />
            <circle cx="49" cy="74" r="2" fill="#EF4444" opacity="0.7" />
          </g>

          <!-- Stall P-02 (Occupied) -->
          <g transform="translate(450, 355)">
            <rect width="68" height="90" rx="4" fill="var(--hero-bay-fill)" stroke="var(--hero-bay-stroke)" stroke-width="1.2" />
            <line x1="0" y1="90" x2="68" y2="90" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="16" font-family="'Inter', sans-serif" font-size="8" font-weight="700" fill="var(--hero-text-subtle)" text-anchor="middle">P-02</text>
            <rect x="10" y="26" width="48" height="54" rx="9" fill="var(--hero-car-parked-alt)" stroke="var(--hero-car-parked-stroke)" stroke-width="1" />
            <rect x="16" y="38" width="36" height="18" rx="4" fill="var(--hero-car-glass)" />
            <circle cx="19" cy="74" r="2" fill="#EF4444" opacity="0.7" />
            <circle cx="49" cy="74" r="2" fill="#EF4444" opacity="0.7" />
          </g>

          <!-- Stall P-03 (EV) -->
          <g transform="translate(530, 355)">
            <rect width="68" height="90" rx="4" fill="var(--hero-bay-fill)" stroke="var(--hero-bay-stroke)" stroke-width="1.2" />
            <line x1="0" y1="90" x2="68" y2="90" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="16" font-family="'Inter', sans-serif" font-size="7.5" font-weight="700" fill="var(--color-primary)" text-anchor="middle">⚡ EV-01</text>
            <rect x="11" y="24" width="46" height="56" rx="9" fill="var(--hero-car-parked)" stroke="var(--color-primary)" stroke-width="1" stroke-opacity="0.4" />
            <rect x="16" y="36" width="36" height="20" rx="4" fill="var(--hero-car-glass)" />
            <circle cx="19" cy="74" r="2" fill="#10B981" />
            <circle cx="49" cy="74" r="2" fill="#10B981" />
          </g>

          <!-- Stall P-04 (Available - Target Bay 1) -->
          <g transform="translate(610, 355)" class="target-parking-bay">
            <rect width="68" height="92" rx="5" fill="var(--hero-bay-target-fill)" stroke="var(--color-primary)" stroke-width="1.6" />
            <line x1="0" y1="92" x2="68" y2="92" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="18" font-family="'Inter', sans-serif" font-size="8.5" font-weight="800" fill="var(--color-primary)" text-anchor="middle">P-04</text>
            <text x="34" y="29" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="#5EEAD4" text-anchor="middle" letter-spacing="0.05em">AVAILABLE</text>
            <circle cx="34" cy="6" r="3.5" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1" />
            <circle cx="34" cy="6" r="1.8" fill="var(--color-success, #34D399)" />
          </g>

          <!-- Stall P-05 (Available - Target Bay 2) -->
          <g transform="translate(690, 355)" class="target-parking-bay">
            <rect width="68" height="92" rx="5" fill="var(--hero-bay-target-fill)" stroke="var(--color-primary)" stroke-width="1.6" />
            <line x1="0" y1="92" x2="68" y2="92" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="18" font-family="'Inter', sans-serif" font-size="8.5" font-weight="800" fill="var(--color-primary)" text-anchor="middle">P-05</text>
            <text x="34" y="29" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="#5EEAD4" text-anchor="middle" letter-spacing="0.05em">AVAILABLE</text>
            <circle cx="34" cy="6" r="3.5" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1" />
            <circle cx="34" cy="6" r="1.8" fill="var(--color-success, #34D399)" />
          </g>

          <!-- Stall P-06 (Available - Target Bay 3) -->
          <g transform="translate(770, 355)" class="target-parking-bay">
            <rect width="68" height="92" rx="5" fill="var(--hero-bay-target-fill)" stroke="var(--color-primary)" stroke-width="1.6" />
            <line x1="0" y1="92" x2="68" y2="92" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="18" font-family="'Inter', sans-serif" font-size="8.5" font-weight="800" fill="var(--color-primary)" text-anchor="middle">P-06</text>
            <text x="34" y="29" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="#5EEAD4" text-anchor="middle" letter-spacing="0.05em">AVAILABLE</text>
            <circle cx="34" cy="6" r="3.5" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1" />
            <circle cx="34" cy="6" r="1.8" fill="var(--color-success, #34D399)" />
          </g>

          <!-- STALL P-07 (Available - Target Bay 4) -->
          <g transform="translate(850, 355)" class="target-parking-bay">
            <rect width="76" height="92" rx="5" fill="var(--hero-bay-target-fill)" stroke="var(--color-primary)" stroke-width="1.8" />
            <line x1="0" y1="92" x2="76" y2="92" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="38" y="18" font-family="'Inter', sans-serif" font-size="8.5" font-weight="800" fill="var(--color-primary)" text-anchor="middle">P-07</text>
            <text x="38" y="29" font-family="'Inter', sans-serif" font-size="6" font-weight="700" fill="#5EEAD4" text-anchor="middle" letter-spacing="0.06em">AVAILABLE</text>
            <circle cx="38" cy="6" r="4" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1" />
            <circle cx="38" cy="6" r="2" fill="var(--color-success, #34D399)" />
          </g>

          <!-- Stall P-08 (Occupied) -->
          <g transform="translate(938, 355)">
            <rect width="68" height="90" rx="4" fill="var(--hero-bay-fill)" stroke="var(--hero-bay-stroke)" stroke-width="1.2" />
            <line x1="0" y1="90" x2="68" y2="90" stroke="var(--hero-asphalt)" stroke-width="2" />
            <text x="34" y="16" font-family="'Inter', sans-serif" font-size="8" font-weight="700" fill="var(--hero-text-subtle)" text-anchor="middle">P-08</text>
            <rect x="12" y="26" width="44" height="54" rx="8" fill="var(--hero-car-parked)" stroke="var(--hero-car-parked-stroke)" stroke-width="1" />
            <circle cx="19" cy="74" r="2" fill="#EF4444" opacity="0.7" />
            <circle cx="49" cy="74" r="2" fill="#EF4444" opacity="0.7" />
          </g>
        </g>

        <!-- ======== SUCCESS RINGS & BADGES FOR P-04, P-05, P-06, P-07 ======== -->
        <!-- Bay P-04 Success Elements -->
        <rect
          x="607"
          y="352"
          width="74"
          height="98"
          rx="6"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="2.2"
          class="bay-success-ring bay-ring-1"
          style="opacity: 0; transform-origin: 644px 401px;"
          filter="url(#route-glow)"
        />
        <g class="parking-success-badge bay-badge-1" transform="translate(644, 345)" style="opacity: 0; transform-origin: 0 0;">
          <rect x="-38" y="-11" width="76" height="20" rx="10" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1.3" />
          <circle cx="-26" cy="-1" r="4" fill="var(--color-success, #34D399)" />
          <text x="-18" y="2.5" font-family="'Inter', sans-serif" font-size="6.8" font-weight="700" fill="var(--color-success, #34D399)">PARKED ✓</text>
        </g>

        <!-- Bay P-05 Success Elements -->
        <rect
          x="687"
          y="352"
          width="74"
          height="98"
          rx="6"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="2.2"
          class="bay-success-ring bay-ring-2"
          style="opacity: 0; transform-origin: 724px 401px;"
          filter="url(#route-glow)"
        />
        <g class="parking-success-badge bay-badge-2" transform="translate(724, 345)" style="opacity: 0; transform-origin: 0 0;">
          <rect x="-38" y="-11" width="76" height="20" rx="10" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1.3" />
          <circle cx="-26" cy="-1" r="4" fill="var(--color-success, #34D399)" />
          <text x="-18" y="2.5" font-family="'Inter', sans-serif" font-size="6.8" font-weight="700" fill="var(--color-success, #34D399)">PARKED ✓</text>
        </g>

        <!-- Bay P-06 Success Elements -->
        <rect
          x="767"
          y="352"
          width="74"
          height="98"
          rx="6"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="2.2"
          class="bay-success-ring bay-ring-3"
          style="opacity: 0; transform-origin: 804px 401px;"
          filter="url(#route-glow)"
        />
        <g class="parking-success-badge bay-badge-3" transform="translate(804, 345)" style="opacity: 0; transform-origin: 0 0;">
          <rect x="-38" y="-11" width="76" height="20" rx="10" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1.3" />
          <circle cx="-26" cy="-1" r="4" fill="var(--color-success, #34D399)" />
          <text x="-18" y="2.5" font-family="'Inter', sans-serif" font-size="6.8" font-weight="700" fill="var(--color-success, #34D399)">PARKED ✓</text>
        </g>

        <!-- Bay P-07 Success Elements -->
        <rect
          x="847"
          y="352"
          width="82"
          height="98"
          rx="6"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="2.2"
          class="bay-success-ring bay-ring-4"
          style="opacity: 0; transform-origin: 888px 401px;"
          filter="url(#route-glow)"
        />
        <g class="parking-success-badge bay-badge-4" transform="translate(888, 345)" style="opacity: 0; transform-origin: 0 0;">
          <rect x="-42" y="-11" width="84" height="20" rx="10" fill="var(--hero-surface)" stroke="var(--color-primary)" stroke-width="1.3" />
          <circle cx="-30" cy="-1" r="4" fill="var(--color-success, #34D399)" />
          <text x="-22" y="2.5" font-family="'Inter', sans-serif" font-size="6.8" font-weight="700" fill="var(--color-success, #34D399)">RESERVED ✓</text>
        </g>

        <!-- ======== 4 STRAIGHT ROUTES FOR CARS 1 TO 4 ======== -->
        <!-- Circuit 1 -> straight to P-04 (x=644) -->
        <path class="circuit-bg circuit-bg-1" d="M 280,323 L 620,323 Q 644,323 644,345 L 644,400" fill="none" stroke="var(--hero-bay-stroke)" stroke-width="2.2" stroke-dasharray="5 8" opacity="0.35" />
        <path class="circuit circuit-1" d="M 280,323 L 620,323 Q 644,323 644,345 L 644,400" fill="none" stroke="var(--color-primary)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" filter="url(#route-glow)" />

        <!-- Circuit 2 -> straight to P-05 (x=724) -->
        <path class="circuit-bg circuit-bg-2" d="M 280,323 L 700,323 Q 724,323 724,345 L 724,400" fill="none" stroke="var(--hero-bay-stroke)" stroke-width="2.2" stroke-dasharray="5 8" opacity="0.35" />
        <path class="circuit circuit-2" d="M 280,323 L 700,323 Q 724,323 724,345 L 724,400" fill="none" stroke="var(--color-primary)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" filter="url(#route-glow)" />

        <!-- Circuit 3 -> straight to P-06 (x=804) -->
        <path class="circuit-bg circuit-bg-3" d="M 280,323 L 780,323 Q 804,323 804,345 L 804,400" fill="none" stroke="var(--hero-bay-stroke)" stroke-width="2.2" stroke-dasharray="5 8" opacity="0.35" />
        <path class="circuit circuit-3" d="M 280,323 L 780,323 Q 804,323 804,345 L 804,400" fill="none" stroke="var(--color-primary)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" filter="url(#route-glow)" />

        <!-- Circuit 4 -> straight to P-07 (x=888) -->
        <path class="circuit-bg circuit-bg-4" d="M 280,323 L 864,323 Q 888,323 888,345 L 888,400" fill="none" stroke="var(--hero-bay-stroke)" stroke-width="2.2" stroke-dasharray="5 8" opacity="0.35" />
        <path class="circuit circuit-4" d="M 280,323 L 864,323 Q 888,323 888,345 L 888,400" fill="none" stroke="var(--color-primary)" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" filter="url(#route-glow)" />

        <!-- ======== 4 ANIMATED VEHICLES ======== -->
        <!-- Car 1 (Parks in P-04) -->
        <g class="car car-1" style="transform-origin: 0 0;">
          <polygon points="20,-8 65,-20 65,20 20,8" fill="url(#car-headlight-beam)" opacity="0.55" />
          <rect x="-24" y="-13" width="48" height="26" rx="9" fill="#000" opacity="0.35" />
          <rect x="-23" y="-11.5" width="46" height="23" rx="8" fill="var(--hero-car-body)" stroke="var(--color-primary)" stroke-width="1.6" />
          <path d="M 2,-9 L 10,-7.5 L 10,7.5 L 2,9 Z" fill="var(--color-primary)" fill-opacity="0.35" stroke="#5EEAD4" stroke-width="0.9" />
          <path d="M -12,-8 L -7,-7.5 L -7,7.5 L -12,8 Z" fill="var(--color-primary)" fill-opacity="0.25" stroke="#5EEAD4" stroke-width="0.75" />
          <rect x="-7" y="-7.5" width="9" height="15" rx="3" fill="var(--hero-car-glass)" stroke="var(--color-primary)" stroke-width="0.6" stroke-opacity="0.5" />
          <line x1="12" y1="-4" x2="19" y2="-4" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.8" />
          <line x1="12" y1="4" x2="19" y2="4" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.8" />
          <rect x="5" y="-14" width="3" height="2" rx="0.75" fill="var(--color-primary)" />
          <rect x="5" y="12" width="3" height="2" rx="0.75" fill="var(--color-primary)" />
          <circle cx="21" cy="-7" r="2.2" fill="white" />
          <circle cx="21" cy="7" r="2.2" fill="white" />
          <rect x="-22" y="-9" width="2" height="4" rx="0.75" fill="#EF4444" />
          <rect x="-22" y="5" width="2" height="4" rx="0.75" fill="#EF4444" />
        </g>

        <!-- Car 2 (Parks in P-05 - Sapphire Blue) -->
        <g class="car car-2" style="transform-origin: 0 0;">
          <polygon points="20,-8 65,-20 65,20 20,8" fill="url(#car-headlight-beam)" opacity="0.55" />
          <rect x="-24" y="-13" width="48" height="26" rx="9" fill="#000" opacity="0.35" />
          <rect x="-23" y="-11.5" width="46" height="23" rx="8" fill="#1A2D4A" stroke="#38BDF8" stroke-width="1.6" />
          <path d="M 2,-9 L 10,-7.5 L 10,7.5 L 2,9 Z" fill="#38BDF8" fill-opacity="0.35" stroke="#7DD3FC" stroke-width="0.9" />
          <path d="M -12,-8 L -7,-7.5 L -7,7.5 L -12,8 Z" fill="#38BDF8" fill-opacity="0.25" stroke="#7DD3FC" stroke-width="0.75" />
          <rect x="-7" y="-7.5" width="9" height="15" rx="3" fill="var(--hero-car-glass)" stroke="#38BDF8" stroke-width="0.6" stroke-opacity="0.5" />
          <line x1="12" y1="-4" x2="19" y2="-4" stroke="#38BDF8" stroke-width="0.75" opacity="0.8" />
          <line x1="12" y1="4" x2="19" y2="4" stroke="#38BDF8" stroke-width="0.75" opacity="0.8" />
          <rect x="5" y="-14" width="3" height="2" rx="0.75" fill="#38BDF8" />
          <rect x="5" y="12" width="3" height="2" rx="0.75" fill="#38BDF8" />
          <circle cx="21" cy="-7" r="2.2" fill="white" />
          <circle cx="21" cy="7" r="2.2" fill="white" />
          <rect x="-22" y="-9" width="2" height="4" rx="0.75" fill="#EF4444" />
          <rect x="-22" y="5" width="2" height="4" rx="0.75" fill="#EF4444" />
        </g>

        <!-- Car 3 (Parks in P-06 - Charcoal Slate) -->
        <g class="car car-3" style="transform-origin: 0 0;">
          <polygon points="20,-8 65,-20 65,20 20,8" fill="url(#car-headlight-beam)" opacity="0.55" />
          <rect x="-24" y="-13" width="48" height="26" rx="9" fill="#000" opacity="0.35" />
          <rect x="-23" y="-11.5" width="46" height="23" rx="8" fill="#1E293B" stroke="#94A3B8" stroke-width="1.6" />
          <path d="M 2,-9 L 10,-7.5 L 10,7.5 L 2,9 Z" fill="#94A3B8" fill-opacity="0.35" stroke="#CBD5E1" stroke-width="0.9" />
          <path d="M -12,-8 L -7,-7.5 L -7,7.5 L -12,8 Z" fill="#94A3B8" fill-opacity="0.25" stroke="#CBD5E1" stroke-width="0.75" />
          <rect x="-7" y="-7.5" width="9" height="15" rx="3" fill="var(--hero-car-glass)" stroke="#94A3B8" stroke-width="0.6" stroke-opacity="0.5" />
          <line x1="12" y1="-4" x2="19" y2="-4" stroke="#94A3B8" stroke-width="0.75" opacity="0.8" />
          <line x1="12" y1="4" x2="19" y2="4" stroke="#94A3B8" stroke-width="0.75" opacity="0.8" />
          <rect x="5" y="-14" width="3" height="2" rx="0.75" fill="#94A3B8" />
          <rect x="5" y="12" width="3" height="2" rx="0.75" fill="#94A3B8" />
          <circle cx="21" cy="-7" r="2.2" fill="white" />
          <circle cx="21" cy="7" r="2.2" fill="white" />
          <rect x="-22" y="-9" width="2" height="4" rx="0.75" fill="#EF4444" />
          <rect x="-22" y="5" width="2" height="4" rx="0.75" fill="#EF4444" />
        </g>

        <!-- Car 4 (Parks in P-07 - Flagship Teal) -->
        <g class="car car-4" style="transform-origin: 0 0;">
          <polygon points="20,-8 65,-20 65,20 20,8" fill="url(#car-headlight-beam)" opacity="0.55" />
          <rect x="-24" y="-13" width="48" height="26" rx="9" fill="#000" opacity="0.35" />
          <rect x="-23" y="-11.5" width="46" height="23" rx="8" fill="var(--hero-car-body)" stroke="var(--color-primary)" stroke-width="1.6" />
          <path d="M 2,-9 L 10,-7.5 L 10,7.5 L 2,9 Z" fill="var(--color-primary)" fill-opacity="0.35" stroke="#5EEAD4" stroke-width="0.9" />
          <path d="M -12,-8 L -7,-7.5 L -7,7.5 L -12,8 Z" fill="var(--color-primary)" fill-opacity="0.25" stroke="#5EEAD4" stroke-width="0.75" />
          <rect x="-7" y="-7.5" width="9" height="15" rx="3" fill="var(--hero-car-glass)" stroke="var(--color-primary)" stroke-width="0.6" stroke-opacity="0.5" />
          <line x1="12" y1="-4" x2="19" y2="-4" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.8" />
          <line x1="12" y1="4" x2="19" y2="4" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.8" />
          <rect x="5" y="-14" width="3" height="2" rx="0.75" fill="var(--color-primary)" />
          <rect x="5" y="12" width="3" height="2" rx="0.75" fill="var(--color-primary)" />
          <circle cx="21" cy="-7" r="2.2" fill="white" />
          <circle cx="21" cy="7" r="2.2" fill="white" />
          <rect x="-22" y="-9" width="2" height="4" rx="0.75" fill="#EF4444" />
          <rect x="-22" y="5" width="2" height="4" rx="0.75" fill="#EF4444" />
        </g>

        <!-- ======== GPS COORDINATE WATERMARK ======== -->
        <text x="600" y="476" font-family="'Inter', sans-serif" font-size="6.5" font-weight="600" fill="var(--hero-text-subtle)" opacity="0.35" text-anchor="middle" letter-spacing="0.12em">GPS 23°47'N 90°25'E · PARKLY SMART PARKING</text>

      </svg>

      <!-- Semi-transparent overlay for text readability -->
      <div class="hero-canvas-overlay"></div>
    </div>

    <!-- =====================================================================
         HERO CONTENT OVERLAY — sits on top of the canvas
         ===================================================================== -->
    <div class="hero-overlay-content">
      <div class="hero-container">
        <!-- Left Column: Text -->
        <div class="hero-content">
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

          <!-- Trust Stats -->
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
      </div>
    </div>

    <!-- =====================================================================
         HERO CONTENT OVERLAY — sits on top of the canvas
         ===================================================================== -->
  </section>
</template>

<style scoped>
/* ============================================================
   THEME-AWARE CSS CUSTOM PROPERTIES FOR THE PARKING SCENE
   ============================================================ */
.hero-section {
  /* LIGHT MODE (default) — Daytime */
  --hero-sky-top: #87CEEB;
  --hero-sky-mid: #B0DAF1;
  --hero-sky-bottom: #D4E8F5;
  --hero-cloud-opacity: 1;
  --hero-star-opacity: 0;

  --hero-ground-top: #D1D5DB;
  --hero-ground-bottom: #C8CCD2;
  --hero-curb: #9CA3AF;
  --hero-asphalt: #B0B5BC;
  --hero-lane-marking: #E5E7EB;
  --hero-arrow: #6B7280;

  --hero-building-far: #CBD5E1;
  --hero-building-mid: #D6DDE6;
  --hero-building-stroke: #B0B9C5;
  --hero-window-glow: transparent;
  --hero-window-light-opacity: 0;

  --hero-tree-fill: #4ADE80;
  --hero-tree-fill-dark: #22C55E;
  --hero-trunk: #92400E;

  --hero-pole: #9CA3AF;
  --hero-lamp-off: #D1D5DB;
  --hero-lamp-glow: transparent;
  --hero-lamp-glow-opacity: 0;

  --hero-canopy: #E2E8F0;
  --hero-surface: #FFFFFF;
  --hero-surface-inner: #F1F5F9;
  --hero-surface-dark: #E2E8F0;
  --hero-text-subtle: #64748B;

  --hero-bay-fill: #F1F5F9;
  --hero-bay-stroke: #CBD5E1;
  --hero-bay-target-fill: #ECFDF5;

  --hero-car-body: #1E293B;
  --hero-car-parked: #94A3B8;
  --hero-car-parked-alt: #6B7280;
  --hero-car-parked-stroke: #9CA3AF;
  --hero-car-glass: #CBD5E1;

  position: relative;
  width: 100%;
  min-height: 520px;
  overflow: hidden;
  background-color: var(--color-bg);
}

/* DARK MODE — Nighttime */
:root[data-theme='dark'] .hero-section {
  --hero-sky-top: #070D18;
  --hero-sky-mid: #0B1220;
  --hero-sky-bottom: #101B2C;
  --hero-cloud-opacity: 0;
  --hero-star-opacity: 1;

  --hero-ground-top: #111C2E;
  --hero-ground-bottom: #0D1726;
  --hero-curb: #26364C;
  --hero-asphalt: #152236;
  --hero-lane-marking: #26364C;
  --hero-arrow: #475569;

  --hero-building-far: #19273C;
  --hero-building-mid: #1A293F;
  --hero-building-stroke: #26364C;
  --hero-window-glow: #FDE68A;
  --hero-window-light-opacity: 0.85;

  --hero-tree-fill: #166534;
  --hero-tree-fill-dark: #14532D;
  --hero-trunk: #78350F;

  --hero-pole: #475569;
  --hero-lamp-off: #FDE68A;
  --hero-lamp-glow: rgba(253, 230, 138, 0.5);
  --hero-lamp-glow-opacity: 0.7;

  --hero-canopy: #19273C;
  --hero-surface: #111C2E;
  --hero-surface-inner: #19273C;
  --hero-surface-dark: #1E293B;
  --hero-text-subtle: #9CAEC2;

  --hero-bay-fill: #111C2E;
  --hero-bay-stroke: #26364C;
  --hero-bay-target-fill: #16273E;

  --hero-car-body: #0B1220;
  --hero-car-parked: #1E293B;
  --hero-car-parked-alt: #1A2638;
  --hero-car-parked-stroke: #334155;
  --hero-car-glass: #0F172A;
}

/* Fallback for prefers-color-scheme dark when data-theme not set */
@media (prefers-color-scheme: dark) {
  :root:not([data-theme='light']) .hero-section {
    --hero-sky-top: #070D18;
    --hero-sky-mid: #0B1220;
    --hero-sky-bottom: #101B2C;
    --hero-cloud-opacity: 0;
    --hero-star-opacity: 1;

    --hero-ground-top: #111C2E;
    --hero-ground-bottom: #0D1726;
    --hero-curb: #26364C;
    --hero-asphalt: #152236;
    --hero-lane-marking: #26364C;
    --hero-arrow: #475569;

    --hero-building-far: #19273C;
    --hero-building-mid: #1A293F;
    --hero-building-stroke: #26364C;
    --hero-window-glow: #FDE68A;
    --hero-window-light-opacity: 0.85;

    --hero-tree-fill: #166534;
    --hero-tree-fill-dark: #14532D;
    --hero-trunk: #78350F;

    --hero-pole: #475569;
    --hero-lamp-off: #FDE68A;
    --hero-lamp-glow: rgba(253, 230, 138, 0.5);
    --hero-lamp-glow-opacity: 0.7;

    --hero-canopy: #19273C;
    --hero-surface: #111C2E;
    --hero-surface-inner: #19273C;
    --hero-surface-dark: #1E293B;
    --hero-text-subtle: #9CAEC2;

    --hero-bay-fill: #111C2E;
    --hero-bay-stroke: #26364C;
    --hero-bay-target-fill: #16273E;

    --hero-car-body: #0B1220;
    --hero-car-parked: #1E293B;
    --hero-car-parked-alt: #1A2638;
    --hero-car-parked-stroke: #334155;
    --hero-car-glass: #0F172A;
  }
}

/* ============================================================
   FULL-WIDTH PARKING CANVAS
   ============================================================ */
.hero-parking-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  overflow: hidden;
  transition: opacity 1.2s ease;
}

.parking-canvas-svg {
  width: 100%;
  height: 100%;
  display: block;
  user-select: none;
  /* Smooth theme transitions for all SVG children */
  transition: fill 1.2s ease, stroke 1.2s ease, opacity 1.2s ease;
}

/* Transition theme-aware SVG properties smoothly */
.parking-canvas-svg rect,
.parking-canvas-svg circle,
.parking-canvas-svg line,
.parking-canvas-svg path,
.parking-canvas-svg ellipse,
.parking-canvas-svg text {
  transition: fill 1.2s ease, stroke 1.2s ease, opacity 1.2s ease;
}

.sky-clouds ellipse,
.sky-stars circle {
  transition: opacity 1.2s ease;
}

/* Semi-transparent overlay for text readability */
.hero-canvas-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 55%;
  height: 100%;
  background: linear-gradient(
    90deg,
    var(--color-bg) 0%,
    var(--color-bg) 30%,
    rgba(248, 250, 252, 0.85) 55%,
    rgba(248, 250, 252, 0.4) 75%,
    transparent 100%
  );
  pointer-events: none;
  z-index: 2;
  transition: background 1.2s ease;
}

:root[data-theme='dark'] .hero-canvas-overlay {
  background: linear-gradient(
    90deg,
    var(--color-bg) 0%,
    var(--color-bg) 30%,
    rgba(11, 18, 32, 0.85) 55%,
    rgba(11, 18, 32, 0.4) 75%,
    transparent 100%
  );
}

@media (prefers-color-scheme: dark) {
  :root:not([data-theme='light']) .hero-canvas-overlay {
    background: linear-gradient(
      90deg,
      var(--color-bg) 0%,
      var(--color-bg) 30%,
      rgba(11, 18, 32, 0.85) 55%,
      rgba(11, 18, 32, 0.4) 75%,
      transparent 100%
    );
  }
}

/* ============================================================
   HERO CONTENT OVERLAY (sits on top of the parking canvas)
   ============================================================ */
.hero-overlay-content {
  position: relative;
  z-index: 3;
  padding: 56px 24px 100px;
}

.hero-container {
  max-width: 1200px;
  margin: 0 auto;
}

.hero-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 520px;
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
  font-size: clamp(2.4rem, 4.5vw, 3.85rem);
  font-weight: 500;
  line-height: 1.08;
  letter-spacing: -0.03em;
  color: var(--color-text);
  margin: 0;
}

.hero-description {
  font-size: clamp(1rem, 1.3vw, 1.18rem);
  font-weight: 450;
  line-height: 1.55;
  color: var(--color-text-muted);
  max-width: 480px;
  margin: 0;
}

.hero-ctas {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-top: 6px;
}

.cta-primary {
  padding: 0 32px !important;
  font-weight: 600;
  font-size: 0.95rem;
}

.cta-secondary {
  padding: 0 28px !important;
  font-weight: 500;
  font-size: 0.95rem;
}

/* Stats */
.hero-stats {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 10px;
  padding-top: 20px;
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

/* ============================================================
   BOTTOM FLOATING ELEMENTS — Pass + Card + Arrow
   ============================================================ */
.hero-bottom-elements {
  position: absolute;
  bottom: 20px;
  left: 24px;
  right: 24px;
  z-index: 4;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  max-width: 1200px;
  margin: 0 auto;
  pointer-events: none;
}

.hero-bottom-elements > * {
  pointer-events: auto;
}

/* Digital Pass (lower-left) */
.hero-pass-float {
  flex-shrink: 0;
}

.pass-circle {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--color-surface);
  box-shadow: var(--shadow-halo);
  transition: transform var(--transition-smooth);
}

.pass-circle:hover {
  transform: translateY(-3px);
}

.pass-svg {
  width: 100%;
  height: 100%;
  display: block;
}

/* Card group (card + arrow) */
.hero-card-group {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.floating-availability-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 8px 16px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: var(--shadow-card-elevated);
  backdrop-filter: blur(8px);
}

.card-pulse-indicator {
  position: relative;
  width: 9px;
  height: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--color-success);
}

.live-ping {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: var(--color-success);
  opacity: 0.5;
  animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
  75%, 100% {
    transform: scale(2.2);
    opacity: 0;
  }
}

.card-info {
  display: flex;
  flex-direction: column;
}

.card-title {
  font-size: 0.78125rem;
  font-weight: 600;
  color: var(--color-text);
  line-height: 1.2;
}

.card-detail {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
}

.card-badge {
  font-size: 0.625rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: var(--radius-pill, 999px);
  background-color: rgba(22, 163, 74, 0.12);
  color: var(--color-success);
  letter-spacing: 0.05em;
}

/* Circular Arrow CTA */
.satellite-cta {
  width: 50px;
  height: 50px;
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
  flex-shrink: 0;
  transition:
    transform var(--transition-fast),
    background-color var(--transition-fast),
    color var(--transition-fast);
}

.satellite-cta:hover {
  transform: scale(1.08) rotate(-6deg);
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  text-decoration: none;
}

/* Animation targets */
.car {
  transform-origin: 0 0;
  will-change: transform, opacity;
}

.circuit {
  will-change: stroke-dashoffset, stroke-dasharray, opacity;
}

.bay-success-ring {
  will-change: transform, opacity;
}

.parking-success-badge {
  will-change: transform, opacity;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1024px) {
  .hero-overlay-content {
    padding: 48px 24px 120px;
  }

  .hero-content {
    max-width: 440px;
  }

  .hero-canvas-overlay {
    width: 60%;
  }

  .hero-bottom-elements {
    bottom: 16px;
  }

  .pass-circle {
    width: 110px;
    height: 110px;
  }
}

@media (max-width: 768px) {
  .hero-section {
    min-height: 600px;
  }

  .hero-overlay-content {
    padding: 36px 16px 160px;
  }

  .hero-content {
    max-width: 100%;
    text-align: center;
    align-items: center;
  }

  .hero-ctas {
    justify-content: center;
  }

  .hero-stats {
    justify-content: center;
  }

  .hero-canvas-overlay {
    width: 100%;
    background: linear-gradient(
      180deg,
      var(--color-bg) 0%,
      rgba(248, 250, 252, 0.85) 35%,
      rgba(248, 250, 252, 0.4) 65%,
      transparent 100%
    );
  }

  :root[data-theme='dark'] .hero-canvas-overlay {
    background: linear-gradient(
      180deg,
      var(--color-bg) 0%,
      rgba(11, 18, 32, 0.85) 35%,
      rgba(11, 18, 32, 0.4) 65%,
      transparent 100%
    );
  }

  .hero-bottom-elements {
    bottom: 12px;
    left: 16px;
    right: 16px;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
  }

  .pass-circle {
    width: 95px;
    height: 95px;
  }

  .floating-availability-card {
    padding: 6px 12px;
    gap: 8px;
  }

  .satellite-cta {
    width: 44px;
    height: 44px;
  }
}

@media (max-width: 480px) {
  .hero-section {
    min-height: 560px;
  }

  .hero-overlay-content {
    padding: 28px 12px 170px;
  }

  .hero-bottom-elements {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .hero-card-group {
    gap: 8px;
  }

  .pass-circle {
    width: 80px;
    height: 80px;
  }
}
</style>
