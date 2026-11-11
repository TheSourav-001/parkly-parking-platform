<script setup>
/**
 * ProfileParkingAnimation.vue
 *
 * Architectural smart-parking cover banner with curved lane navigation,
 * occupied context bays, highlighted reserved bay P-02, and Anime.js v4 loop.
 * Supports Light & Dark themes and prefers-reduced-motion.
 */
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { createTimeline, svg } from 'animejs'

const rootEl = ref(null)
let activeTimeline = null

onMounted(() => {
  const scene = rootEl.value
  if (!scene) return

  const route = scene.querySelector('.parking-route')
  const car = scene.querySelector('.parking-car')
  const slotBorder = scene.querySelector('.target-slot-border')
  const slotPulse = scene.querySelector('.target-slot-pulse')
  const slotBadge = scene.querySelector('.target-slot-badge')
  const headlight = scene.querySelector('.car-headlights')
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

  if (!route || !car || !slotBorder) return

  if (prefersReducedMotion) {
    car.style.transform = 'translate(895px, 108px) rotate(-90deg)'
    car.style.opacity = '1'
    slotBorder.style.opacity = '1'
    if (slotBadge) slotBadge.style.opacity = '1'
    return
  }

  const motionPath = svg.createMotionPath(route)
  const drawableRoute = svg.createDrawable(route)

  activeTimeline = createTimeline({
    loop: true,
    loopDelay: 700,
  })

  activeTimeline
    .set(car, { opacity: 0 }, 0)
    .set(headlight, { opacity: 0.85 }, 0)
    .set(slotBorder, { opacity: 0.45 }, 0)
    .set(slotPulse, { opacity: 0, scale: 0.85 }, 0)
    .set(slotBadge, { opacity: 0, translateY: 6 }, 0)
    // 1. Progressive glowing route line draws ahead
    .add(
      drawableRoute,
      {
        draw: ['0 0', '0 1'],
        duration: 2800,
        ease: 'inOutSine',
      },
      150
    )
    // 2. Car enters from boulevard & drives smoothly into P-02
    .add(
      car,
      {
        opacity: [0, 1],
        duration: 420,
        ease: 'outQuad',
      },
      250
    )
    .add(
      car,
      {
        ...motionPath,
        duration: 3800,
        ease: 'inOutCubic',
      },
      250
    )
    // 3. Headlights dim softly as vehicle parks, slot illuminates
    .add(
      headlight,
      {
        opacity: [0.85, 0.2],
        duration: 500,
        ease: 'outQuad',
      },
      3850
    )
    .add(
      slotBorder,
      {
        opacity: [0.45, 1],
        duration: 450,
        ease: 'outQuad',
      },
      3850
    )
    .add(
      slotPulse,
      {
        opacity: [0.75, 0],
        scale: [0.88, 1.35],
        duration: 1100,
        ease: 'outCubic',
      },
      3950
    )
    .add(
      slotBadge,
      {
        opacity: [0, 1],
        translateY: [6, 0],
        duration: 450,
        ease: 'outBack(1.6)',
      },
      3980
    )
    // 4. Hold parked cleanly, then fade out gracefully before next loop
    .add(
      [car, slotBadge],
      {
        opacity: 0,
        duration: 550,
        ease: 'inOutQuad',
      },
      6100
    )
    .add(
      drawableRoute,
      {
        draw: ['0 1', '1 1'],
        duration: 450,
        ease: 'inQuad',
      },
      6100
    )
    .add(
      slotBorder,
      {
        opacity: [1, 0.45],
        duration: 450,
        ease: 'inOutQuad',
      },
      6100
    )
})

onBeforeUnmount(() => {
  if (activeTimeline && typeof activeTimeline.pause === 'function') {
    activeTimeline.pause()
  }
  activeTimeline = null
})
</script>

<template>
  <div ref="rootEl" class="profile-parking-animation" aria-hidden="true">
    <svg
      viewBox="0 0 1200 280"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      preserveAspectRatio="xMidYMid slice"
      class="cover-svg"
    >
      <defs>
        <!-- Theme-aware Backdrop & Surface Gradients -->
        <linearGradient id="prof-plaza-bg" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--cov-bg-start)" />
          <stop offset="55%" stop-color="var(--cov-bg-mid)" />
          <stop offset="100%" stop-color="var(--cov-bg-end)" />
        </linearGradient>

        <linearGradient id="prof-lane-surface" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="var(--cov-road-top)" />
          <stop offset="100%" stop-color="var(--cov-road-bot)" />
        </linearGradient>

        <linearGradient id="prof-car-body" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#0F172A" />
          <stop offset="45%" stop-color="#334155" />
          <stop offset="100%" stop-color="#0F172A" />
        </linearGradient>

        <linearGradient id="prof-headlight" x1="0%" y1="50%" x2="100%" y2="50%">
          <stop offset="0%" stop-color="#5EEAD4" stop-opacity="0.7" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </linearGradient>

        <radialGradient id="prof-bay-glow" cx="50%" cy="50%" r="55%">
          <stop offset="0%" stop-color="#14B8A6" stop-opacity="0.28" />
          <stop offset="100%" stop-color="#14B8A6" stop-opacity="0" />
        </radialGradient>

        <filter id="prof-route-glow" x="-10%" y="-10%" width="120%" height="120%">
          <feGaussianBlur stdDeviation="2.5" result="blur" />
          <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>

        <filter id="prof-car-shadow" x="-40%" y="-40%" width="180%" height="180%">
          <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#020617" flood-opacity="0.38" />
        </filter>
      </defs>

      <!-- ======== BASE ARCHITECTURAL PLAZA ======== -->
      <rect width="1200" height="280" fill="url(#prof-plaza-bg)" />

      <!-- Subtle Architectural Blueprint Grid -->
      <g opacity="0.16" stroke="var(--cov-grid)" stroke-width="1">
        <path d="M0 40H1200 M0 80H1200 M0 120H1200 M0 160H1200 M0 200H1200 M0 240H1200" />
        <path d="M120 0V280 M240 0V280 M360 0V280 M480 0V280 M600 0V280 M720 0V280 M840 0V280 M960 0V280 M1080 0V280" />
      </g>

      <!-- ======== TOP TERMINAL / GARAGE CANOPY & LANDSCAPE ======== -->
      <g class="plaza-architecture">
        <!-- Smart Terminal Header Strip (left/center top) -->
        <rect x="44" y="24" width="248" height="62" rx="10" fill="var(--cov-building)" stroke="var(--cov-stroke)" stroke-width="1.2" />
        <rect x="60" y="38" width="44" height="10" rx="3" fill="var(--color-primary)" opacity="0.7" />
        <rect x="114" y="38" width="90" height="10" rx="3" fill="var(--cov-stroke)" opacity="0.6" />
        <rect x="60" y="56" width="160" height="8" rx="3" fill="var(--cov-stroke)" opacity="0.4" />
        <rect x="234" y="38" width="42" height="34" rx="6" fill="var(--cov-bay-fill)" stroke="var(--color-primary)" stroke-width="1" opacity="0.75" />
        <text x="255" y="60" text-anchor="middle" fill="var(--color-primary)" font-size="13" font-weight="800" font-family="Inter, sans-serif">P</text>

        <!-- Landscaped Median Island (Bottom-Left Safe Zone behind Profile Avatar) -->
        <rect x="28" y="218" width="230" height="46" rx="23" fill="var(--cov-grass)" stroke="var(--cov-stroke)" stroke-width="1" opacity="0.7" />
        <circle cx="218" cy="238" r="14" fill="var(--cov-tree)" opacity="0.85" />
        <circle cx="242" cy="244" r="10" fill="var(--cov-tree-dark)" opacity="0.85" />

        <!-- Landscaped Divider Planter & Trees (Center-Top) -->
        <rect x="330" y="34" width="380" height="46" rx="23" fill="var(--cov-grass)" stroke="var(--cov-stroke)" stroke-width="1" opacity="0.65" />
        <circle cx="364" cy="57" r="14" fill="var(--cov-tree)" />
        <circle cx="382" cy="60" r="11" fill="var(--cov-tree-dark)" />
        <circle cx="520" cy="57" r="13" fill="var(--cov-tree)" />
        <circle cx="676" cy="57" r="15" fill="var(--cov-tree)" />
        <circle cx="656" cy="62" r="11" fill="var(--cov-tree-dark)" />
      </g>

      <!-- ======== CURVED SMART DRIVEWAY & PLAZA LANE ======== -->
      <!-- Main Curved Asphalt Roadway -->
      <path
        d="M 160 156 C 310 156, 360 205, 520 205 C 680 205, 760 190, 895 190 L 1160 190"
        stroke="url(#prof-lane-surface)"
        stroke-width="64"
        stroke-linecap="round"
        fill="none"
      />
      <!-- Crisp Road Curb Borders -->
      <path
        d="M 160 124 C 310 124, 360 173, 520 173 C 680 173, 760 158, 895 158 L 1160 158"
        stroke="var(--cov-stroke)"
        stroke-width="1.2"
        fill="none"
        opacity="0.7"
      />
      <path
        d="M 160 188 C 310 188, 360 237, 520 237 C 680 237, 760 222, 895 222 L 1160 222"
        stroke="var(--cov-stroke)"
        stroke-width="1.2"
        fill="none"
        opacity="0.7"
      />

      <!-- Center Dashed Guide Markings -->
      <path
        d="M 180 156 C 315 156, 365 205, 520 205 C 680 205, 760 190, 1140 190"
        stroke="var(--cov-dash)"
        stroke-width="2"
        stroke-dasharray="14 14"
        fill="none"
        opacity="0.5"
      />

      <!-- ======== PARKING BAYS ROW (TOP-RIGHT PLAZA: P-01 to P-04) ======== -->
      <g class="parking-bays-row">
        <!-- Plaza Apron Connector -->
        <rect x="752" y="54" width="376" height="106" rx="12" fill="var(--cov-road-bot)" opacity="0.55" />

        <!-- Bay P-01 (Occupied by Parked Compact Vehicle) -->
        <g transform="translate(768, 64)">
          <rect width="74" height="88" rx="10" fill="var(--cov-bay-fill)" stroke="var(--cov-stroke)" stroke-width="1.4" />
          <text x="37" y="80" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-01</text>
          <!-- Static Parked Car -->
          <g transform="translate(37, 40) rotate(-90)" opacity="0.72">
            <rect x="-20" y="-11" width="40" height="22" rx="7" fill="#334155" stroke="#64748B" stroke-width="0.9" />
            <rect x="-10" y="-8" width="20" height="16" rx="4" fill="#0F172A" />
            <path d="M4 -7 C7 -5, 7 5, 4 7 L9 6 L9 -6 Z" fill="#94A3B8" opacity="0.5" />
          </g>
        </g>

        <!-- Bay P-02 (TARGET HIGHLIGHTED SLOT — center at 895, 108) -->
        <g transform="translate(858, 64)">
          <ellipse cx="37" cy="44" rx="52" ry="56" fill="url(#prof-bay-glow)" />
          <rect
            class="target-slot-pulse"
            x="-3"
            y="-3"
            width="80"
            height="94"
            rx="12"
            stroke="var(--color-primary)"
            stroke-width="2"
            fill="none"
            opacity="0"
          />
          <rect
            class="target-slot-border"
            width="74"
            height="88"
            rx="10"
            fill="var(--cov-target-fill)"
            stroke="var(--color-primary)"
            stroke-width="2.2"
          />
          <text x="37" y="50" text-anchor="middle" fill="var(--color-primary)" font-size="16" font-weight="800" font-family="Inter, sans-serif" opacity="0.32">P</text>
          <text x="37" y="80" text-anchor="middle" fill="var(--color-primary)" font-size="10" font-weight="800" font-family="Inter, sans-serif">P-02</text>
        </g>

        <!-- Bay P-03 (Available Empty Slot) -->
        <g transform="translate(948, 64)">
          <rect width="74" height="88" rx="10" fill="var(--cov-bay-fill)" stroke="var(--cov-stroke)" stroke-width="1.4" stroke-dasharray="5 4" />
          <text x="37" y="80" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-03</text>
        </g>

        <!-- Bay P-04 (Occupied) -->
        <g transform="translate(1038, 64)">
          <rect width="74" height="88" rx="10" fill="var(--cov-bay-fill)" stroke="var(--cov-stroke)" stroke-width="1.4" />
          <text x="37" y="80" text-anchor="middle" fill="var(--cov-muted)" font-size="10" font-weight="700" font-family="Inter, sans-serif">P-04</text>
          <g transform="translate(37, 40) rotate(-90)" opacity="0.68">
            <rect x="-20" y="-11" width="40" height="22" rx="7" fill="#1E293B" stroke="#475569" stroke-width="0.9" />
            <rect x="-10" y="-8" width="20" height="16" rx="4" fill="#0F172A" />
            <path d="M4 -7 C7 -5, 7 5, 4 7 L9 6 L9 -6 Z" fill="#94A3B8" opacity="0.45" />
          </g>
        </g>

        <!-- Right Landscape Accent -->
        <circle cx="1156" cy="96" r="16" fill="var(--cov-tree)" opacity="0.8" />
        <circle cx="1164" cy="116" r="12" fill="var(--cov-tree-dark)" opacity="0.8" />
      </g>

      <!-- ======== NAVIGATION ROUTE & ANIMATED CAR ======== -->
      <!-- Subtle Guide Track -->
      <path
        d="M 190 156 C 315 156, 365 205, 520 205 C 680 205, 760 190, 845 190 C 885 190, 895 168, 895 138 L 895 108"
        stroke="var(--color-primary)"
        stroke-width="2"
        stroke-dasharray="5 9"
        opacity="0.22"
        fill="none"
      />

      <!-- Animated Glowing Teal Route -->
      <path
        class="parking-route"
        d="M 190 156 C 315 156, 365 205, 520 205 C 680 205, 760 190, 845 190 C 885 190, 895 168, 895 138 L 895 108"
        stroke="var(--color-primary)"
        stroke-width="3.2"
        stroke-linecap="round"
        filter="url(#prof-route-glow)"
        fill="none"
      />

      <!-- Entry Waypoint Pulse Ring -->
      <circle cx="190" cy="156" r="5" fill="var(--color-primary)" />
      <circle cx="190" cy="156" r="10" stroke="var(--color-primary)" stroke-width="1.5" opacity="0.35" />

      <!-- Animated Top-Down Vehicle (Centered at 0,0, front facing +X) -->
      <g class="parking-car" filter="url(#prof-car-shadow)" opacity="0">
        <!-- Headlight Cone -->
        <g class="car-headlights">
          <path d="M22 -7 L54 -16 L54 16 L22 7 Z" fill="url(#prof-headlight)" />
        </g>
        <!-- Body -->
        <rect x="-23" y="-12" width="46" height="24" rx="8" fill="url(#prof-car-body)" stroke="#94A3B8" stroke-width="1.1" />
        <!-- Cabin / Glass Roof -->
        <rect x="-12" y="-9" width="24" height="18" rx="5" fill="#0F172A" />
        <!-- Front Windshield -->
        <path d="M5 -8 C10 -6, 10 6, 5 8 L12 7 L12 -7 Z" fill="#5EEAD4" opacity="0.82" />
        <!-- Rear Window -->
        <path d="M-11 -7 L-7 -8 L-7 8 L-11 7 Z" fill="#38BDF8" opacity="0.38" />
        <!-- Side Mirrors -->
        <rect x="4" y="-14" width="4" height="2.5" rx="1" fill="#64748B" />
        <rect x="4" y="11.5" width="4" height="2.5" rx="1" fill="#64748B" />
        <!-- LED Headlights & Taillights -->
        <rect x="20" y="-9" width="3" height="4" rx="1" fill="#99F6E4" />
        <rect x="20" y="5" width="3" height="4" rx="1" fill="#99F6E4" />
        <rect x="-23" y="-9" width="2.5" height="4" rx="1" fill="#F87171" opacity="0.9" />
        <rect x="-23" y="5" width="2.5" height="4" rx="1" fill="#F87171" opacity="0.9" />
      </g>

      <!-- Parked Status Pill Above Bay P-02 -->
      <g class="target-slot-badge" transform="translate(895, 42)" opacity="0">
        <rect x="-42" y="-11" width="84" height="22" rx="11" fill="#0F766E" stroke="#5EEAD4" stroke-width="1" />
        <circle cx="-28" cy="0" r="3" fill="#99F6E4" />
        <text x="5" y="3.5" text-anchor="middle" fill="#FFFFFF" font-size="9" font-weight="700" letter-spacing="0.06em" font-family="Inter, sans-serif">PARKED P-02</text>
      </g>
    </svg>
  </div>
</template>

<style scoped>
.profile-parking-animation {
  --cov-bg-start: #F1F5F9;
  --cov-bg-mid: #E2E8F0;
  --cov-bg-end: #DCE7F3;
  --cov-grid: #94A3B8;
  --cov-building: #FFFFFF;
  --cov-road-top: #CBD5E1;
  --cov-road-bot: #E2E8F0;
  --cov-stroke: #94A3B8;
  --cov-dash: #FFFFFF;
  --cov-bay-fill: rgba(255, 255, 255, 0.72);
  --cov-target-fill: rgba(15, 118, 110, 0.14);
  --cov-grass: #D1FAE5;
  --cov-tree: #34D399;
  --cov-tree-dark: #10B981;
  --cov-muted: #64748B;

  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

:global([data-theme='dark']) .profile-parking-animation {
  --cov-bg-start: #091220;
  --cov-bg-mid: #0F1C2E;
  --cov-bg-end: #13233A;
  --cov-grid: #334155;
  --cov-building: #152338;
  --cov-road-top: #18273E;
  --cov-road-bot: #122033;
  --cov-stroke: #334155;
  --cov-dash: #64748B;
  --cov-bay-fill: rgba(17, 28, 46, 0.78);
  --cov-target-fill: rgba(20, 184, 166, 0.16);
  --cov-grass: rgba(16, 185, 129, 0.12);
  --cov-tree: #059669;
  --cov-tree-dark: #047857;
  --cov-muted: #94A3B8;
}

.cover-svg {
  width: 100%;
  height: 100%;
  display: block;
}

.parking-car {
  transform-origin: 0 0;
}

.target-slot-pulse {
  transform-box: fill-box;
  transform-origin: center;
}
</style>
