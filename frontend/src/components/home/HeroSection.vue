<script setup>
/**
 * HeroSection Component — Realistic Animated Parking Journey
 * Asymmetric composition: Confident headline, tight tracking,
 * and an authentic smart parking facility SVG illustration.
 *
 * Vehicle Journey:
 * 1. Car enters through facility entrance gate.
 * 2. Follows the curved parking lane in sync with SVG route drawing.
 * 3. Smoothly turns 90° into reserved parking stall P-04.
 * 4. Straightens out and comes to a complete halt inside the bay.
 * 5. Stall illuminates with Teal success indicator & verified status.
 * 6. Pauses for ~1.8 seconds.
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
    // If user prefers reduced motion, show car parked in stall without animation loop
    const carEl = document.querySelector('.car')
    const circuitEl = document.querySelector('.circuit')
    const successBadge = document.querySelector('.parking-success-badge')
    const successRing = document.querySelector('.bay-success-ring')
    if (carEl) {
      carEl.style.transform = 'translate(295px, 80px) rotate(-90deg)'
      carEl.style.opacity = '1'
    }
    if (circuitEl) {
      circuitEl.style.opacity = '0.8'
    }
    if (successBadge) successBadge.style.opacity = '1'
    if (successRing) successRing.style.opacity = '1'
    return
  }

  try {
    // 1. Initial Staggered Entrance of containers
    const entrancePrimary = animate('.primary-circle', {
      scale: [0.96, 1],
      opacity: [0, 1],
      ease: 'outCubic',
      duration: 700,
    })
    activeAnimations.push(entrancePrimary)

    const entranceSecondary = animate('.secondary-circle', {
      translateY: [12, 0],
      opacity: [0, 1],
      ease: 'outCubic',
      duration: 700,
      delay: 180,
    })
    activeAnimations.push(entranceSecondary)

    const entranceCard = animate('.floating-availability-card', {
      translateY: [14, 0],
      opacity: [0, 1],
      ease: 'outCubic',
      duration: 700,
      delay: 320,
    })
    activeAnimations.push(entranceCard)

    // 2. Realistic Parking Journey Timeline using Anime.js v4
    const carMotionPath = svg.createMotionPath('.circuit')
    const drawableCircuit = svg.createDrawable('.circuit')

    if (carMotionPath && drawableCircuit && drawableCircuit.length) {
      activeTimeline = createTimeline({
        loop: true,
        loopDelay: 600,
      })

      activeTimeline
        // Step 1: Initial state at entrance
        .set('.car', { opacity: 0 }, 0)
        .set('.parking-success-badge', { opacity: 0, scale: 0.8 }, 0)
        .set('.bay-success-ring', { opacity: 0, scale: 0.95 }, 0)
        // Fade in car entering through the gate
        .add('.car', {
          opacity: [0, 1],
          duration: 350,
          ease: 'outQuad',
        }, 80)
        // Step 2 & 3: Car drives along curved parking lane + route line draws in sync
        .add('.car', {
          ...carMotionPath,
          duration: 4600,
          ease: 'inOutCubic',
        }, 150)
        .add(drawableCircuit, {
          draw: ['0 0', '0 1'],
          duration: 4600,
          ease: 'inOutCubic',
        }, 150)
        // Step 4 & 5: Car completes turn, straightens out & stops in Stall P-04 at ~4750ms
        // Step 6: Stall border turns Teal & Success Badge pops up
        .add('.bay-success-ring', {
          opacity: [0, 1],
          scale: [0.94, 1],
          duration: 400,
          ease: 'outBack(2)',
        }, 4700)
        .add('.parking-success-badge', {
          opacity: [0, 1],
          scale: [0.8, 1],
          duration: 400,
          ease: 'outBack(2)',
        }, 4780)
        // Subtle success bay pulse
        .add('.bay-success-ring', {
          scale: [1, 1.04, 1],
          duration: 700,
          ease: 'inOutQuad',
        }, 5150)
        // Step 7: Car stays cleanly parked for ~1.8 seconds (until 6900ms)
        // Step 8: Smooth fade out before loop resets
        .add('.car, .circuit, .parking-success-badge, .bay-success-ring', {
          opacity: 0,
          duration: 450,
          ease: 'outQuad',
        }, 6900)
    }

    // 3. Digital Parking Pass Gentle Float (Subtle & compact)
    const passFloatAnimation = animate('.secondary-circle', {
      translateY: [-4, 4],
      ease: 'inOutSine',
      duration: 3200,
      alternate: true,
      loop: true,
      delay: 800,
    })
    activeAnimations.push(passFloatAnimation)

    // 4. Floating Availability Card Gentle Float (Clean & subtle)
    const cardFloatAnimation = animate('.floating-availability-card', {
      translateY: [3, -3],
      ease: 'inOutSine',
      duration: 3800,
      alternate: true,
      loop: true,
      delay: 1000,
    })
    activeAnimations.push(cardFloatAnimation)

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
    <!-- Subtle Background Watermark Typography (Restrained & Minimal) -->
    <div class="hero-watermark" aria-hidden="true">
      PARKLY
    </div>

    <!-- Single Thin Decorative Orbital Line -->
    <svg class="orbital-svg" viewBox="0 0 1200 600" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path
        d="M 180,540 C 380,560 640,430 780,260 C 880,140 1040,90 1200,110"
        stroke="var(--color-primary)"
        stroke-width="1"
        stroke-dasharray="4 6"
        opacity="0.2"
      />
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

      <!-- Right Column: Realistic Smart Parking Facility & Vehicle Journey -->
      <div class="hero-visual">
        <div class="visual-constellation">
          <!-- Main Primary Circular Illustration (420px) -->
          <div class="circle-portrait primary-circle">
            <div class="portrait-inner">
              <svg viewBox="0 0 440 440" fill="none" xmlns="http://www.w3.org/2000/svg" class="portrait-img smart-parking-svg">
                <defs>
                  <!-- Balanced Navy Radial Gradient (Preserves Parkly Theme without pitch darkness) -->
                  <radialGradient id="parking-radial" cx="50%" cy="50%" r="50%" fx="45%" fy="40%">
                    <stop offset="0%" stop-color="#1A293F" />
                    <stop offset="55%" stop-color="#111C2E" />
                    <stop offset="100%" stop-color="#0B1220" />
                  </radialGradient>

                  <!-- Headlight Glow Beam Gradient -->
                  <linearGradient id="car-headlight-beam" x1="0%" y1="50%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#5EEAD4" stop-opacity="0.9" />
                    <stop offset="45%" stop-color="#14B8A6" stop-opacity="0.35" />
                    <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
                  </linearGradient>

                  <!-- Subtle Soft Glow Filter for Route & Target -->
                  <filter id="route-glow" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur stdDeviation="2" result="blur" />
                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                  </filter>
                </defs>

                <!-- Base Circular Canvas -->
                <rect width="440" height="440" fill="url(#parking-radial)" />

                <!-- Outer Architectural Rings -->
                <circle cx="220" cy="220" r="216" stroke="var(--color-border)" stroke-width="1.5" opacity="0.6" />
                <circle cx="220" cy="220" r="202" stroke="#14B8A6" stroke-width="0.8" stroke-dasharray="4 8" opacity="0.25" />

                <!-- Technical Blueprint Grid & GPS Coordinates -->
                <g stroke="#26364C" stroke-width="0.75" opacity="0.25">
                  <line x1="220" y1="14" x2="220" y2="426" stroke-dasharray="2 8" />
                  <line x1="14" y1="220" x2="426" y2="220" stroke-dasharray="2 8" />
                </g>
                <text x="220" y="24" font-family="'Inter', sans-serif" font-size="7" font-weight="600" fill="#9CAEC2" opacity="0.5" text-anchor="middle" letter-spacing="0.1em">GPS 23°47'N 90°25'E &bull; PARKLY SMART FACILITY</text>

                <!-- Asphalt Driving Lane Surface (Clear Realistic Roads) -->
                <path
                  d="M 45,310 L 45,255 C 45,255 105,255 145,225 C 185,195 210,180 265,180 L 395,180 L 395,245 L 265,245 C 215,245 180,255 130,310 Z"
                  fill="#152236"
                  opacity="0.85"
                />

                <!-- Lane Road Edge Markings -->
                <path
                  d="M 45,310 C 105,310 155,275 195,245 L 265,245"
                  stroke="#26364C"
                  stroke-width="1.5"
                  fill="none"
                  opacity="0.5"
                />

                <!-- Directional Traffic Arrows on Asphalt -->
                <g stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.7">
                  <!-- Entrance arrow -->
                  <path d="M 85,280 L 105,280 M 98,275 L 105,280 L 98,285" />
                  <!-- Mid aisle arrow -->
                  <path d="M 195,195 L 215,195 M 208,190 L 215,195 L 208,200" />
                  <!-- Turn arrow into Stall P-04 -->
                  <path d="M 295,168 L 295,150 M 290,157 L 295,150 L 300,157" />
                </g>

                <!-- Speed & Aisle Stencil Markings -->
                <text x="145" y="275" font-family="'Inter', sans-serif" font-size="6.5" font-weight="700" fill="#475569" letter-spacing="0.08em" opacity="0.6">AISLE 01</text>
                <text x="235" y="225" font-family="'Inter', sans-serif" font-size="6" font-weight="700" fill="#475569" letter-spacing="0.08em" opacity="0.5">MAX 10 KM/H</text>

                <!-- Architectural Parking Bays Row (Top) -->
                <g class="parking-bays-row">
                  <!-- Stall P-01 (Occupied by Sedan) -->
                  <g transform="translate(75, 45)">
                    <rect width="52" height="78" rx="4" fill="#111C2E" stroke="#26364C" stroke-width="1.2" />
                    <line x1="0" y1="78" x2="52" y2="78" stroke="#152236" stroke-width="2" />
                    <text x="26" y="14" font-family="'Inter', sans-serif" font-size="7" font-weight="700" fill="#64748B" text-anchor="middle">P-01</text>
                    <!-- Parked Car Silhouette -->
                    <rect x="10" y="22" width="32" height="48" rx="7" fill="#1E293B" stroke="#334155" stroke-width="1" />
                    <rect x="14" y="32" width="24" height="16" rx="3" fill="#0F172A" />
                    <circle cx="15" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                    <circle cx="37" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                  </g>

                  <!-- Stall P-02 (Occupied by Crossover) -->
                  <g transform="translate(139, 45)">
                    <rect width="52" height="78" rx="4" fill="#111C2E" stroke="#26364C" stroke-width="1.2" />
                    <line x1="0" y1="78" x2="52" y2="78" stroke="#152236" stroke-width="2" />
                    <text x="26" y="14" font-family="'Inter', sans-serif" font-size="7" font-weight="700" fill="#64748B" text-anchor="middle">P-02</text>
                    <rect x="9" y="24" width="34" height="46" rx="7" fill="#1A2638" stroke="#2E3D52" stroke-width="1" />
                    <rect x="14" y="34" width="24" height="16" rx="3" fill="#0F172A" />
                    <circle cx="15" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                    <circle cx="37" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                  </g>

                  <!-- Stall P-03 (Occupied by EV SUV) -->
                  <g transform="translate(203, 45)">
                    <rect width="52" height="78" rx="4" fill="#111C2E" stroke="#26364C" stroke-width="1.2" />
                    <line x1="0" y1="78" x2="52" y2="78" stroke="#152236" stroke-width="2" />
                    <text x="26" y="14" font-family="'Inter', sans-serif" font-size="6.5" font-weight="700" fill="#14B8A6" text-anchor="middle">⚡ EV-01</text>
                    <rect x="9" y="22" width="34" height="48" rx="7" fill="#19273C" stroke="#14B8A6" stroke-width="1" stroke-opacity="0.4" />
                    <rect x="14" y="32" width="24" height="18" rx="4" fill="#0B1220" />
                    <circle cx="15" cy="66" r="1.5" fill="#10B981" />
                    <circle cx="37" cy="66" r="1.5" fill="#10B981" />
                  </g>

                  <!-- STALL P-04: THE USER'S TARGET BAY (CLEAR, HIGHLIGHTED, ELEGANT) -->
                  <g transform="translate(267, 45)" class="target-parking-bay">
                    <!-- Base Bay Area -->
                    <rect width="56" height="80" rx="5" fill="#16273E" stroke="#14B8A6" stroke-width="1.6" />
                    <line x1="0" y1="80" x2="56" y2="80" stroke="#152236" stroke-width="2" />

                    <!-- Stencil text inside stall -->
                    <text x="28" y="16" font-family="'Inter', sans-serif" font-size="7" font-weight="800" fill="#14B8A6" text-anchor="middle">P-04</text>
                    <text x="28" y="27" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="#5EEAD4" text-anchor="middle" letter-spacing="0.06em">AVAILABLE</text>

                    <!-- Stall guide landing corners -->
                    <path d="M 8,36 L 8,42 M 8,36 L 14,36" stroke="#5EEAD4" stroke-width="1" opacity="0.6" />
                    <path d="M 48,36 L 48,42 M 48,36 L 42,36" stroke="#5EEAD4" stroke-width="1" opacity="0.6" />
                    <path d="M 8,72 L 8,66 M 8,72 L 14,72" stroke="#5EEAD4" stroke-width="1" opacity="0.6" />
                    <path d="M 48,72 L 48,66 M 48,72 L 42,72" stroke="#5EEAD4" stroke-width="1" opacity="0.6" />

                    <!-- Overhead Ultrasonic Sensor Node -->
                    <circle cx="28" cy="4" r="3.5" fill="#111C2E" stroke="#14B8A6" stroke-width="1" />
                    <circle cx="28" cy="4" r="1.5" fill="#34D399" />
                  </g>

                  <!-- Stall P-05 (Occupied) -->
                  <g transform="translate(335, 45)">
                    <rect width="52" height="78" rx="4" fill="#111C2E" stroke="#26364C" stroke-width="1.2" />
                    <line x1="0" y1="78" x2="52" y2="78" stroke="#152236" stroke-width="2" />
                    <text x="26" y="14" font-family="'Inter', sans-serif" font-size="7" font-weight="700" fill="#64748B" text-anchor="middle">P-05</text>
                    <rect x="10" y="24" width="32" height="46" rx="6" fill="#1E293B" stroke="#334155" stroke-width="1" />
                    <circle cx="15" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                    <circle cx="37" cy="66" r="1.5" fill="#EF4444" opacity="0.8" />
                  </g>
                </g>

                <!-- SUCCESS RING & BADGE OVER BAY P-04 (Animated on Arrival) -->
                <rect
                  x="265"
                  y="43"
                  width="60"
                  height="84"
                  rx="6"
                  fill="none"
                  stroke="#14B8A6"
                  stroke-width="2.5"
                  class="bay-success-ring"
                  style="opacity: 0; transform-origin: 295px 85px;"
                  filter="url(#route-glow)"
                />

                <g class="parking-success-badge" transform="translate(295, 34)" style="opacity: 0; transform-origin: 0 0;">
                  <rect x="-42" y="-10" width="84" height="20" rx="10" fill="#0B1220" stroke="#14B8A6" stroke-width="1.5" />
                  <circle cx="-30" cy="0" r="4.5" fill="#34D399" />
                  <path d="M -32,-0.5 L -30.5,1.5 L -27,-2" stroke="#0B1220" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                  <text x="-21" y="3.5" font-family="'Inter', sans-serif" font-size="7.5" font-weight="700" fill="#34D399" letter-spacing="0.04em">RESERVED ✓</text>
                </g>

                <!-- Facility Entrance Gate & Barrier Complex (Bottom Left) -->
                <g transform="translate(48, 260)" class="facility-gate-complex">
                  <!-- Entry Concrete Island -->
                  <rect x="0" y="32" width="46" height="12" rx="3" fill="#1E293B" stroke="#26364C" stroke-width="1" />
                  <!-- Terminal Kiosk -->
                  <rect x="6" y="14" width="12" height="22" rx="3" fill="#111C2E" stroke="#14B8A6" stroke-width="1.2" />
                  <circle cx="12" cy="20" r="2" fill="#34D399" />
                  <!-- Gate Barrier Arm (Raised Clear) -->
                  <line x1="18" y1="20" x2="42" y2="2" stroke="#34D399" stroke-width="2.8" stroke-linecap="round" />
                  <text x="22" y="41" font-family="'Inter', sans-serif" font-size="6" font-weight="700" fill="#9CAEC2" letter-spacing="0.06em">IN &bull; GATE 01</text>
                </g>

                <!-- Static Roadbed Planned Route Guide (Subtle Dashed Track) -->
                <path
                  class="circuit-bg"
                  d="M 60,280 C 110,280 140,260 160,230 C 180,200 205,195 240,195 L 265,195 C 285,195 295,175 295,140 L 295,80"
                  fill="none"
                  stroke="#26364C"
                  stroke-width="2.5"
                  stroke-dasharray="4 6"
                  opacity="0.45"
                />

                <!-- ACTIVE SVG ROUTE LINE (.circuit) - DRAWS IN SYNC WITH CAR -->
                <path
                  class="circuit"
                  d="M 60,280 C 110,280 140,260 160,230 C 180,200 205,195 240,195 L 265,195 C 285,195 295,175 295,140 L 295,80"
                  fill="none"
                  stroke="#14B8A6"
                  stroke-width="3.2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  filter="url(#route-glow)"
                />

                <!-- PROMINENT MODERN VEHICLE (.car) - REALISTIC MOTION ALONG ROUTE -->
                <g class="car" style="transform-origin: 0 0;">
                  <!-- Forward Headlight Cones (Shooting forward along +X) -->
                  <polygon points="20,-8 68,-22 68,22 20,8" fill="url(#car-headlight-beam)" opacity="0.65" />

                  <!-- Contact Shadow -->
                  <rect x="-24" y="-13" width="48" height="26" rx="9" fill="#000000" opacity="0.45" />

                  <!-- Main Aerodynamic Body Shell (Length 46px, Width 23px) -->
                  <rect x="-23" y="-11.5" width="46" height="23" rx="8" fill="#0B1220" stroke="#14B8A6" stroke-width="1.6" />

                  <!-- Front Windshield -->
                  <path d="M 2,-9 L 10,-7.5 L 10,7.5 L 2,9 Z" fill="#14B8A6" fill-opacity="0.35" stroke="#5EEAD4" stroke-width="0.9" />

                  <!-- Rear Window -->
                  <path d="M -12,-8 L -7,-7.5 L -7,7.5 L -12,8 Z" fill="#14B8A6" fill-opacity="0.25" stroke="#5EEAD4" stroke-width="0.75" />

                  <!-- Roof / Sunroof Glass Panel -->
                  <rect x="-7" y="-7.5" width="9" height="15" rx="3" fill="#162338" stroke="#14B8A6" stroke-width="0.6" stroke-opacity="0.5" />

                  <!-- Hood Crease Lines -->
                  <line x1="12" y1="-4" x2="19" y2="-4" stroke="#14B8A6" stroke-width="0.75" opacity="0.8" />
                  <line x1="12" y1="4" x2="19" y2="4" stroke="#14B8A6" stroke-width="0.75" opacity="0.8" />

                  <!-- Side Mirrors -->
                  <rect x="5" y="-14" width="3" height="2" rx="0.75" fill="#14B8A6" />
                  <rect x="5" y="12" width="3" height="2" rx="0.75" fill="#14B8A6" />

                  <!-- Dual Front Headlights (Crisp White/Cyan) -->
                  <circle cx="21" cy="-7" r="2.2" fill="#FFFFFF" />
                  <circle cx="21" cy="7" r="2.2" fill="#FFFFFF" />

                  <!-- Dual Rear Taillights (Vibrant Red) -->
                  <rect x="-22" y="-9" width="2" height="4" rx="0.75" fill="#EF4444" />
                  <rect x="-22" y="5" width="2" height="4" rx="0.75" fill="#EF4444" />
                </g>
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

          <!-- Secondary Compact Circular Element: Digital Parking Pass (135px) -->
          <div class="circle-portrait secondary-circle">
            <div class="portrait-inner">
              <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" class="portrait-img pass-svg">
                <!-- Base Navy Surface -->
                <rect width="140" height="140" fill="#0B1220" />
                <circle cx="70" cy="70" r="66" fill="#111C2E" stroke="#14B8A6" stroke-width="1.2" stroke-opacity="0.3" />

                <!-- Smartphone / Digital Pass Graphic -->
                <g transform="translate(36, 16)">
                  <rect width="68" height="108" rx="12" fill="#0B1220" stroke="#14B8A6" stroke-width="1.6" />
                  <line x1="26" y1="5" x2="42" y2="5" stroke="#26364C" stroke-width="2" stroke-linecap="round" />

                  <!-- Header -->
                  <rect x="7" y="12" width="54" height="16" rx="5" fill="rgba(20, 184, 166, 0.15)" stroke="#14B8A6" stroke-width="0.8" />
                  <circle cx="16" cy="20" r="2.5" fill="#14B8A6" />
                  <text x="23" y="23" font-family="'Inter', sans-serif" font-size="5.5" font-weight="700" fill="#F1F5F9">PARKLY PASS</text>

                  <!-- QR Code Matrix -->
                  <rect x="13" y="34" width="42" height="42" rx="5" fill="#111C2E" stroke="#26364C" stroke-width="1" />
                  <g fill="#14B8A6">
                    <!-- Corner Targets -->
                    <rect x="17" y="38" width="9" height="9" rx="1.5" />
                    <rect x="19" y="40" width="5" height="5" fill="#111C2E" />
                    <rect x="20.5" y="41.5" width="2" height="2" />

                    <rect x="42" y="38" width="9" height="9" rx="1.5" />
                    <rect x="44" y="40" width="5" height="5" fill="#111C2E" />
                    <rect x="45.5" y="41.5" width="2" height="2" />

                    <rect x="17" y="63" width="9" height="9" rx="1.5" />
                    <rect x="19" y="65" width="5" height="5" fill="#111C2E" />
                    <rect x="20.5" y="41.5" width="2" height="2" />
                    <rect x="20.5" y="66.5" width="2" height="2" />

                    <!-- Data Cells -->
                    <rect x="29" y="39" width="3" height="3" />
                    <rect x="35" y="39" width="4" height="3" />
                    <rect x="29" y="45" width="4" height="3" />
                    <rect x="37" y="45" width="3" height="4" />
                    <rect x="20" y="51" width="4" height="3" />
                    <rect x="27" y="51" width="3" height="3" />
                    <rect x="33" y="51" width="5" height="3" />
                    <rect x="41" y="53" width="3" height="4" />
                    <rect x="29" y="57" width="5" height="3" />
                    <rect x="37" y="59" width="4" height="3" />
                    <rect x="29" y="63" width="4" height="3" />
                    <rect x="44" y="63" width="4" height="3" />
                    <rect x="36" y="68" width="5" height="3" />
                  </g>

                  <!-- Pass Status Info -->
                  <text x="34" y="86" font-family="'Inter', sans-serif" font-size="6.5" font-weight="700" fill="#34D399" text-anchor="middle">GATE: AUTO-OPEN</text>
                  <text x="34" y="96" font-family="'Inter', sans-serif" font-size="5.5" font-weight="500" fill="#9CAEC2" text-anchor="middle">Spot P-04 Reserved</text>
                </g>
              </svg>
            </div>
          </div>

          <!-- Floating Parking Availability Information Card (Compact & Clean) -->
          <div class="floating-availability-card">
            <div class="card-pulse-indicator">
              <span class="live-dot"></span>
              <span class="live-ping"></span>
            </div>
            <div class="card-info">
              <span class="card-title">Gulshan Avenue Garage</span>
              <span class="card-detail">14 Slots Free &bull; ৳80/hr &bull; 0.4 km (Demo)</span>
            </div>
            <div class="card-badge">VERIFIED</div>
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
  padding: 36px 24px 44px;
  overflow: hidden;
  background-color: var(--color-bg);
}

/* Watermark typography: Ultra subtle and clean */
.hero-watermark {
  position: absolute;
  top: 8%;
  right: -2%;
  font-size: clamp(90px, 14vw, 170px);
  font-weight: 800;
  letter-spacing: -0.05em;
  color: var(--color-text);
  opacity: 0.015;
  line-height: 0.8;
  pointer-events: none;
  user-select: none;
  z-index: 1;
}

/* Thin Single Decorative Orbital Line */
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
  gap: 48px;
}

/* Left Content */
.hero-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
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
  max-width: 520px;
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

/* Visual Right Column (Balanced & Centered) */
.hero-visual {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.visual-constellation {
  position: relative;
  width: 420px;
  height: 420px;
  margin: 0 auto;
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
  transform: translateY(-3px);
}

.primary-circle {
  top: 12px;
  left: 25px;
  width: 370px;
  height: 370px;
  z-index: 2;
}

.portrait-inner {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  overflow: hidden;
  border: 3.5px solid var(--color-surface);
  box-shadow: inset 0 0 0 1px var(--color-border);
  background-color: #0B1220;
}

.portrait-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.smart-parking-svg {
  width: 100%;
  height: 100%;
  display: block;
  user-select: none;
}

/* Vehicle & Circuit Styles */
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

/* Satellite Micro-CTA docked onto perimeter */
.satellite-cta {
  position: absolute;
  bottom: 12px;
  right: 20px;
  width: 52px;
  height: 52px;
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
  transform: scale(1.08) rotate(-6deg);
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  text-decoration: none;
}

/* Secondary Circle (Compact Digital Pass) */
.secondary-circle {
  bottom: 10px;
  left: -10px;
  width: 135px;
  height: 135px;
  z-index: 3;
}

.secondary-circle .portrait-inner {
  border-width: 2.5px;
}

/* Floating Availability Info Card (Compact & Clean) */
.floating-availability-card {
  position: absolute;
  bottom: 20px;
  right: -12px;
  z-index: 4;
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

/* Responsive */
@media (max-width: 1024px) {
  .hero-container {
    grid-template-columns: 1fr;
    gap: 40px;
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
    width: 310px;
    height: 310px;
    left: 25px;
  }

  .secondary-circle {
    width: 115px;
    height: 115px;
    left: -5px;
    bottom: 5px;
  }

  .floating-availability-card {
    right: 0;
    bottom: 10px;
  }
}

@media (max-width: 640px) {
  .hero-section {
    padding: 24px 16px 40px;
  }

  .orbital-svg {
    display: none;
  }

  .visual-constellation {
    width: 290px;
    height: 290px;
  }

  .primary-circle {
    width: 250px;
    height: 250px;
    left: 20px;
    top: 5px;
  }

  .secondary-circle {
    width: 95px;
    height: 95px;
    left: -5px;
    bottom: -5px;
  }

  .satellite-cta {
    width: 44px;
    height: 44px;
    bottom: 6px;
    right: 12px;
  }

  .floating-availability-card {
    position: relative;
    right: auto;
    bottom: auto;
    margin-top: 20px;
    width: 100%;
    justify-content: center;
  }
}
</style>

