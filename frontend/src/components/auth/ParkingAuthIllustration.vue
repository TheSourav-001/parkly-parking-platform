<script setup>
/**
 * ParkingAuthIllustration.vue
 *
 * Premium Satellite-Style Aerial Parking Illustration for Parkly.
 * Powered by Anime.js v4:
 *  - svg.createDrawable() for progressive navigation route drawing
 *  - svg.createMotionPath() for the vehicle moving along the SVG route
 *  - morphTo() for destination parking bay shape transformation
 *  - createTimeline() for cohesive 5-stage orchestration
 *
 * Supports Light & Dark themes via CSS tokens and prefers-reduced-motion.
 */
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useTheme } from '../../composables/useTheme';
import {
  animate,
  createTimeline,
  morphTo,
  svg
} from 'animejs';

const { currentTheme } = useTheme();

const rootEl = ref(null);
let activeTimeline = null;
let rippleAnim = null;

onMounted(() => {
  // Check user preference for reduced motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const scope = rootEl.value;
  if (!scope) return;

  const mapEl = scope.querySelector('.map-scene');
  const circuitEl = scope.querySelector('.circuit');
  const carEl = scope.querySelector('.car');
  const pinEl = scope.querySelector('.animated-pin');
  const bayAEl = scope.querySelector('.circuit-a');
  const bayBEl = scope.querySelector('.circuit-b');
  const arrowEl = scope.querySelector('.route-arrow');
  const rippleEl = scope.querySelector('.pin-ripple');

  if (prefersReducedMotion) {
    // Immediately settle in parked state
    if (mapEl) mapEl.style.opacity = '1';
    if (circuitEl) {
      circuitEl.style.opacity = '0.85';
      circuitEl.setAttribute('stroke-dashoffset', '0');
    }
    if (bayAEl && bayBEl) {
      bayAEl.setAttribute('d', bayBEl.getAttribute('d'));
      bayAEl.style.stroke = 'var(--color-primary)';
      bayAEl.style.strokeWidth = '2.5px';
    }
    if (carEl) {
      // Parked bay position: x=252, y=268, rotation=90deg
      carEl.style.transform = 'translate(252px, 268px) rotate(90deg)';
      carEl.style.opacity = '1';
    }
    if (arrowEl) arrowEl.style.opacity = '0.8';
    if (pinEl) {
      pinEl.style.opacity = '1';
      pinEl.style.transform = 'translateY(0)';
    }
    return;
  }

  // Pre-animation initial styles
  if (mapEl) mapEl.style.opacity = '0';
  if (pinEl) {
    pinEl.style.opacity = '0';
    pinEl.style.transform = 'translateY(-22px)';
  }
  if (arrowEl) arrowEl.style.opacity = '0';
  if (carEl) carEl.style.opacity = '0';

  // Initialize drawable proxy on .circuit
  const drawableCircuit = svg.createDrawable(circuitEl, 0, 0);

  // Build master timeline
  activeTimeline = createTimeline({
    defaults: {
      ease: 'outQuad',
    },
    onComplete: () => {
      // Start gentle, continuous ripple pulse on location pin after arrival
      if (rippleEl) {
        rippleAnim = animate(rippleEl, {
          r: [4, 18],
          opacity: [0.8, 0],
          duration: 1800,
          ease: 'outQuad',
          loop: true,
        });
      }
    }
  });

  // Stage A: Map Scene Fade-in (0.0 - 0.7s)
  activeTimeline.add(mapEl, {
    opacity: [0, 1],
    duration: 700,
    ease: 'outCubic',
  }, 0);

  // Stage B: Navigation Route Drawing via createDrawable (0.6 - 2.0s)
  activeTimeline.add(drawableCircuit, {
    draw: '0 1',
    duration: 1400,
    ease: 'inOutSine',
  }, 600);

  // Arrow appears as route finishes
  if (arrowEl) {
    activeTimeline.add(arrowEl, {
      opacity: [0, 0.85],
      scale: [0.4, 1],
      duration: 300,
      ease: 'outBack',
    }, 1800);
  }

  // Stage C: Vehicle follows .circuit via createMotionPath (1.4 - 4.8s)
  // Car fades in at route start
  activeTimeline.add(carEl, {
    opacity: [0, 1],
    duration: 250,
  }, 1400);

  activeTimeline.add(carEl, {
    ...svg.createMotionPath(circuitEl),
    duration: 3400,
    ease: 'inOutCubic',
  }, 1400);

  // Stage D: Parking Bay Outline morphs into highlighted state (4.1 - 4.7s)
  if (bayAEl && bayBEl) {
    activeTimeline.add(bayAEl, {
      d: morphTo(bayBEl),
      stroke: ['var(--color-border)', 'var(--color-primary)'],
      strokeWidth: [1.2, 2.5],
      duration: 650,
      ease: 'outCubic',
    }, 4100);
  }

  // Stage E: Teal Location Pin drops in and bounces (4.4 - 5.1s)
  if (pinEl) {
    activeTimeline.add(pinEl, {
      opacity: [0, 1],
      translateY: [-22, 0],
      duration: 700,
      ease: 'outBack(1.8)',
    }, 4400);
  }
});

onBeforeUnmount(() => {
  if (activeTimeline) {
    activeTimeline.pause();
    activeTimeline = null;
  }
  if (rippleAnim) {
    rippleAnim.pause();
    rippleAnim = null;
  }
});
</script>

<template>
  <div ref="rootEl" class="auth-illustration-container" :class="{ 'is-dark': currentTheme === 'dark' }" aria-hidden="true">
    <svg
      viewBox="0 0 520 400"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      class="scene-svg"
    >
      <defs>
        <!-- Map Canvas Background Gradient -->
        <linearGradient id="mapBgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--color-surface)" />
          <stop offset="100%" stop-color="var(--color-surface-subtle)" />
        </linearGradient>

        <!-- Road Base Surface Gradient for realistic depth & texture -->
        <linearGradient id="roadBaseGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="var(--color-road)" />
          <stop offset="50%" stop-color="var(--color-road-surface)" />
          <stop offset="100%" stop-color="var(--color-road)" />
        </linearGradient>

        <linearGradient id="roadVertGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="var(--color-road)" />
          <stop offset="50%" stop-color="var(--color-road-surface)" />
          <stop offset="100%" stop-color="var(--color-road)" />
        </linearGradient>

        <!-- Building Surface Gradient -->
        <linearGradient id="bldgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--color-surface)" />
          <stop offset="100%" stop-color="var(--color-surface-subtle)" />
        </linearGradient>

        <!-- Premium Car Body Gradient (Deep Navy Metallic) -->
        <linearGradient id="carBodyMetal" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#0A1120" />
          <stop offset="25%" stop-color="#1E293B" />
          <stop offset="50%" stop-color="#334155" />
          <stop offset="75%" stop-color="#1E293B" />
          <stop offset="100%" stop-color="#0A1120" />
        </linearGradient>

        <!-- Car Cabin / Roof Gradient -->
        <linearGradient id="carRoofGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#1E293B" />
          <stop offset="50%" stop-color="#0F172A" />
          <stop offset="100%" stop-color="#020617" />
        </linearGradient>

        <!-- Windshield Glass Reflections -->
        <linearGradient id="windshieldGlass" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#BAE6FD" stop-opacity="0.9" />
          <stop offset="45%" stop-color="#38BDF8" stop-opacity="0.65" />
          <stop offset="100%" stop-color="#0284C7" stop-opacity="0.4" />
        </linearGradient>

        <!-- Location Pin Teal Gradient -->
        <linearGradient id="pinTealGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#2DD4BF" />
          <stop offset="100%" stop-color="var(--color-primary)" />
        </linearGradient>

        <!-- Soft Vehicle Shadow Filter -->
        <filter id="softVehicleShadow" x="-30%" y="-30%" width="160%" height="160%">
          <feDropShadow dx="0" dy="2" stdDeviation="3.5" flood-color="#020617" flood-opacity="0.38" />
        </filter>

        <!-- Soft Yellow Neon Glow Filter (active on road lane dividers in Dark Mode) -->
        <filter id="laneDividerGlow" x="-50%" y="-50%" width="200%" height="200%">
          <feDropShadow dx="0" dy="0" stdDeviation="2.5" flood-color="#FEF08A" flood-opacity="0.95" />
          <feDropShadow dx="0" dy="0" stdDeviation="5.5" flood-color="#FACC15" flood-opacity="0.75" />
        </filter>

        <!-- Window Neon Glow Filter (active on illuminated windows in Dark Mode) -->
        <filter id="windowGlow" x="-60%" y="-60%" width="220%" height="220%">
          <feDropShadow dx="0" dy="0" stdDeviation="2.5" flood-color="#FEF08A" flood-opacity="1" />
          <feDropShadow dx="0" dy="0" stdDeviation="6" flood-color="#EAB308" flood-opacity="0.85" />
        </filter>

        <!-- Map Rounded Clip Boundary -->
        <clipPath id="mapBoundsClip">
          <rect x="12" y="12" width="496" height="376" rx="16" />
        </clipPath>
      </defs>

      <!-- ================================================================
           MAP SCENE (Fades in first)
           ================================================================ -->
      <g class="map-scene" clip-path="url(#mapBoundsClip)">
        <!-- Base Aerial Canvas -->
        <rect x="12" y="12" width="496" height="376" rx="16" fill="url(#mapBgGrad)" />

        <!-- ==== ROAD NETWORK ==== -->
        <!-- Main Top Thoroughfare -->
        <rect x="12" y="32" width="496" height="52" fill="url(#roadBaseGrad)" stroke="var(--color-road-edge)" stroke-width="0.8" />
        <!-- Top Road Lane Divider (Theme-aware with soft neon in Dark Mode) -->
        <line x1="12" y1="58" x2="508" y2="58"
              class="road-lane-divider"
              stroke="var(--color-road-marking)" stroke-width="2.2"
              stroke-dasharray="16 10" />

        <!-- Vertical Connecting Road (Right side) -->
        <rect x="390" y="32" width="54" height="356" fill="url(#roadVertGrad)" stroke="var(--color-road-edge)" stroke-width="0.8" />
        <!-- Vertical Road Lane Divider -->
        <line x1="417" y1="32" x2="417" y2="388"
              class="road-lane-divider"
              stroke="var(--color-road-marking)" stroke-width="2.2"
              stroke-dasharray="16 10" />

        <!-- Bottom Arrival Road (Where car enters) -->
        <rect x="12" y="326" width="384" height="54" fill="url(#roadBaseGrad)" stroke="var(--color-road-edge)" stroke-width="0.8" />
        <!-- Bottom Road Lane Divider -->
        <line x1="12" y1="353" x2="390" y2="353"
              class="road-lane-divider"
              stroke="var(--color-road-marking)" stroke-width="2.2"
              stroke-dasharray="16 10" />

        <!-- Parking Lot Drive Lane (Vertical access) -->
        <rect x="92" y="84" width="40" height="244" fill="var(--color-road)" opacity="0.92" stroke="var(--color-road-edge)" stroke-width="0.5" />

        <!-- Entrance Crosswalk Markings (Bottom Road) -->
        <g class="crosswalk-markings">
          <rect x="135" y="327" width="7" height="52" fill="var(--color-road-marking)" opacity="0.6" />
          <rect x="147" y="327" width="7" height="52" fill="var(--color-road-marking)" opacity="0.6" />
          <rect x="159" y="327" width="7" height="52" fill="var(--color-road-marking)" opacity="0.6" />
          <rect x="171" y="327" width="7" height="52" fill="var(--color-road-marking)" opacity="0.6" />
        </g>

        <!-- ==== URBAN BUILDING FOOTPRINTS ==== -->
        <!-- Building North-West -->
        <rect x="24" y="98" width="56" height="92" rx="4"
              fill="url(#bldgGrad)" stroke="var(--color-border)" stroke-width="1.2" />
        <!-- Windows Grid (Theme-aware: Unlit in Light mode, Warm glowing in Dark mode) -->
        <g class="building-windows">
          <rect class="window-lit-1" x="32" y="106" width="9" height="7" rx="1" />
          <rect class="window-lit-2" x="47" y="106" width="9" height="7" rx="1" />
          <rect class="window-lit-3" x="62" y="106" width="9" height="7" rx="1" />
          <rect class="window-lit-2" x="32" y="120" width="9" height="7" rx="1" />
          <rect class="window-lit-1" x="47" y="120" width="9" height="7" rx="1" />
          <rect class="window-lit-2" x="62" y="120" width="9" height="7" rx="1" />
          <rect class="window-lit-3" x="32" y="134" width="9" height="7" rx="1" />
          <rect class="window-lit-2" x="47" y="134" width="9" height="7" rx="1" />
          <rect class="window-lit-1" x="62" y="134" width="9" height="7" rx="1" />
        </g>
        <rect x="36" y="156" width="32" height="22" rx="2"
              fill="var(--color-surface-subtle)" stroke="var(--color-border)" stroke-width="1" />

        <!-- Building North-East -->
        <rect x="454" y="98" width="46" height="114" rx="4"
              fill="url(#bldgGrad)" stroke="var(--color-border)" stroke-width="1.2" />
        <g class="building-windows">
          <rect class="window-lit-1" x="462" y="108" width="8" height="7" rx="1" />
          <rect class="window-lit-2" x="476" y="108" width="8" height="7" rx="1" />
          <rect class="window-lit-3" x="462" y="122" width="8" height="7" rx="1" />
          <rect class="window-lit-1" x="476" y="122" width="8" height="7" rx="1" />
          <rect class="window-lit-2" x="462" y="136" width="8" height="7" rx="1" />
          <rect class="window-lit-3" x="476" y="136" width="8" height="7" rx="1" />
        </g>

        <!-- Building South-East -->
        <rect x="454" y="244" width="46" height="76" rx="4"
              fill="url(#bldgGrad)" stroke="var(--color-border)" stroke-width="1.2" />
        <g class="building-windows">
          <rect class="window-lit-2" x="462" y="254" width="8" height="7" rx="1" />
          <rect class="window-lit-1" x="476" y="254" width="8" height="7" rx="1" />
          <rect class="window-lit-3" x="462" y="268" width="8" height="7" rx="1" />
          <rect class="window-lit-2" x="476" y="268" width="8" height="7" rx="1" />
        </g>

        <!-- ==== GREENERY & TREES ==== -->
        <!-- West Tree Cluster -->
        <circle cx="50" cy="216" r="14" fill="var(--color-greenery)" opacity="0.55" />
        <circle cx="38" cy="232" r="10" fill="var(--color-greenery-dark)" opacity="0.5" />
        <circle cx="62" cy="230" r="9" fill="var(--color-greenery)" opacity="0.45" />

        <!-- South-West Corner Trees -->
        <circle cx="36" cy="306" r="13" fill="var(--color-greenery)" opacity="0.5" />
        <circle cx="54" cy="312" r="10" fill="var(--color-greenery-dark)" opacity="0.45" />

        <!-- South-East Tree (Right side e niche: single 2 ta circle) -->
        <circle cx="472" cy="354" r="16" fill="var(--color-greenery)" opacity="0.6" />
        <circle cx="484" cy="360" r="11" fill="var(--color-greenery-dark)" opacity="0.5" />

        <!-- ==== PARKING FACILITY AREA ==== -->
        <!-- Concrete Lot Deck with enhanced edge contrast -->
        <rect x="132" y="84" width="248" height="242" rx="4"
              fill="var(--color-lot-surface)" stroke="var(--color-lot-border)" stroke-width="1.5" />

        <!-- Facility Label: PARKLY ZONE A (Enhanced contrast & readability) -->
        <text x="256" y="113"
              class="zone-header-label"
              text-anchor="middle">PARKLY ZONE A</text>

        <!-- Row A (Top bays: y=124 to y=194) -->
        <!-- Bay A1 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="138" y="124" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="155" y="163" text-anchor="middle">P</text>

        <!-- Bay A2 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="174" y="124" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="191" y="163" text-anchor="middle">P</text>

        <!-- Bay A3 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="210" y="124" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="227" y="163" text-anchor="middle">P</text>

        <!-- Bay A4 (Occupied) -->
        <rect class="parking-bay-rect bay-occupied" x="246" y="124" width="34" height="70" rx="3" />
        <rect class="parked-vehicle-silhouette" x="253" y="136" width="20" height="46" rx="5" />

        <!-- Bay A5 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="282" y="124" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="299" y="163" text-anchor="middle">P</text>

        <!-- Bay A6 (Occupied) -->
        <rect class="parking-bay-rect bay-occupied" x="318" y="124" width="34" height="70" rx="3" />
        <rect class="parked-vehicle-silhouette" x="325" y="136" width="20" height="46" rx="5" />

        <!-- Middle Driving Aisle Centerline Divider -->
        <line x1="134" y1="208" x2="378" y2="208"
              class="aisle-divider"
              stroke-width="1.2" stroke-dasharray="6 4" />

        <!-- Row B (Bottom bays: y=222 to y=292) -->
        <!-- Bay B1 (Occupied) -->
        <rect class="parking-bay-rect bay-occupied" x="138" y="222" width="34" height="70" rx="3" />
        <rect class="parked-vehicle-silhouette" x="145" y="234" width="20" height="46" rx="5" />

        <!-- Bay B2 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="174" y="222" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="191" y="261" text-anchor="middle">P</text>

        <!-- Bay B3 (Occupied) -->
        <rect class="parking-bay-rect bay-occupied" x="210" y="222" width="34" height="70" rx="3" />
        <rect class="parked-vehicle-silhouette" x="217" y="234" width="20" height="46" rx="5" />

        <!-- Bay B4: DESTINATION / TARGET PARKING BAY -->
        <!-- Static background tint -->
        <rect class="target-bay-backdrop" x="246" y="222" width="34" height="70" rx="3"
              fill="var(--color-primary)" fill-opacity="0.14" />
        <!-- P Monogram in destination bay -->
        <text x="263" y="261"
              fill="var(--color-primary)"
              font-family="system-ui, -apple-system, sans-serif"
              font-size="12"
              font-weight="800"
              text-anchor="middle"
              opacity="0.8">P</text>

        <!-- Anime.js morphTo target bay outline: .circuit-a morphs into .circuit-b -->
        <!-- Standard bay rectangle outline -->
        <path
          class="circuit-a"
          d="M 246 222 L 280 222 L 280 292 L 246 292 Z"
          fill="none"
          stroke="var(--color-bay-line)"
          stroke-width="1.4"
        />
        <!-- Highlighted expanded destination outline (hidden reference for morphTo) -->
        <path
          class="circuit-b"
          d="M 244 220 L 282 220 L 282 294 L 244 294 Z"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="2.5"
          style="display: none;"
        />

        <!-- Bay B5 (Empty) -->
        <rect class="parking-bay-rect bay-empty" x="282" y="222" width="34" height="70" rx="3" />
        <text class="bay-p-mark" x="299" y="261" text-anchor="middle">P</text>

        <!-- Bay B6 (Occupied) -->
        <rect class="parking-bay-rect bay-occupied" x="318" y="222" width="34" height="70" rx="3" />
        <rect class="parked-vehicle-silhouette" x="325" y="234" width="20" height="46" rx="5" />

        <!-- ==== NAVIGATION ROUTE (Anime.js svg.createDrawable & svg.createMotionPath) ==== -->
        <!--
          Dynamic motion path with S-curve and realistic steering trajectory:
          Starts on the road entrance, curves gracefully up through the access lane,
          sweeps into the central aisle, and makes a smooth, flowing turn into Bay B4.
        -->
        <path
          class="circuit"
          d="M 112 353 C 112 315 110 270 114 240 C 117 215 130 208 152 208 L 242 208 C 256 208 263 214 263 226 L 263 256"
          fill="none"
          stroke="var(--color-primary)"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-dasharray="6 5"
          opacity="0.85"
        />

        <!-- Route Guidance Arrow pointing into destination bay -->
        <polygon
          class="route-arrow"
          points="263,266 257,255 269,255"
          fill="var(--color-primary)"
        />

        <!-- ==== LOCATION PIN (Anchored at Target Bay) ==== -->
        <g class="animated-pin">
          <!-- Expanding ground ripples -->
          <circle class="pin-ripple" cx="263" cy="222" r="6"
                  fill="none" stroke="var(--color-primary)" stroke-width="1.8" />

          <!-- Teardrop Marker Pin (Target point anchored at x=263, y=222) -->
          <g transform="translate(251, 186)">
            <!-- Teardrop SVG -->
            <path
              d="M12 0 C5.373 0 0 5.373 0 12 C0 20.5 12 34 12 34 C12 34 24 20.5 24 12 C24 5.373 18.627 0 12 0 Z"
              fill="url(#pinTealGrad)"
              filter="url(#softVehicleShadow)"
            />
            <!-- Inner White Core -->
            <circle cx="12" cy="11.5" r="5.5" fill="#FFFFFF" />
            <!-- Parkly P Monogram inside Pin -->
            <text x="12" y="15"
                  fill="var(--color-primary)"
                  font-family="system-ui, -apple-system, sans-serif"
                  font-size="8.5"
                  font-weight="900"
                  text-anchor="middle">P</text>
          </g>
        </g>

        <!-- ==== RECOGNIZABLE MODERN PASSENGER VEHICLE (Anime.js .car) ==== -->
        <!--
          Drawn centered at origin (0, 0), facing EAST (0 degrees)
          so Anime.js createMotionPath with rotate: true aligns perfectly
          along the driving tangent into the parking bay.
          Car Dimensions: 46px length (along X), 24px width (along Y).
        -->
        <g class="car" filter="url(#softVehicleShadow)">
          <!-- Ground Ambient Vehicle Shadow -->
          <ellipse cx="0" cy="0" rx="25" ry="14" fill="#000000" opacity="0.32" />

          <!-- 4 Wheels with rubber tread & teal rim hubs -->
          <!-- Front-Left Wheel -->
          <rect x="11" y="-14" width="9" height="4.5" rx="1.5" fill="#020617" />
          <line x1="12" y1="-11.8" x2="19" y2="-11.8" stroke="var(--color-primary)" stroke-width="1" />

          <!-- Front-Right Wheel -->
          <rect x="11" y="9.5" width="9" height="4.5" rx="1.5" fill="#020617" />
          <line x1="12" y1="11.8" x2="19" y2="11.8" stroke="var(--color-primary)" stroke-width="1" />

          <!-- Rear-Left Wheel -->
          <rect x="-20" y="-14" width="9" height="4.5" rx="1.5" fill="#020617" />
          <line x1="-19" y1="-11.8" x2="-12" y2="-11.8" stroke="var(--color-primary)" stroke-width="1" />

          <!-- Rear-Right Wheel -->
          <rect x="-20" y="9.5" width="9" height="4.5" rx="1.5" fill="#020617" />
          <line x1="-19" y1="11.8" x2="-12" y2="11.8" stroke="var(--color-primary)" stroke-width="1" />

          <!-- Aerodynamic Passenger Car Body Chassis -->
          <path
            d="M -22 -11 C -23 -11 -24 -9 -24 -6 L -24 6 C -24 9 -23 11 -22 11 L 18 11 C 21 11 23 9 23 6 L 24 2 C 24.5 1 24.5 -1 24 -2 L 23 -6 C 23 -9 21 -11 18 -11 Z"
            fill="url(#carBodyMetal)"
            stroke="#334155"
            stroke-width="0.8"
          />

          <!-- Front Bumper Accent (Facing Positive X) -->
          <path d="M 23 -6 Q 25 0 23 6" stroke="#475569" stroke-width="1.2" fill="none" />

          <!-- Rear Bumper Accent (Facing Negative X) -->
          <path d="M -23 -7 Q -25 0 -23 7" stroke="#334155" stroke-width="1.2" fill="none" />

          <!-- LED Headlights (Warm white, forward illumination) -->
          <ellipse cx="23" cy="-7" rx="1.5" ry="2.2" fill="#FEF08A" />
          <ellipse cx="23" cy="7" rx="1.5" ry="2.2" fill="#FEF08A" />

          <!-- LED Taillights (Vivid red safety markers) -->
          <rect x="-24" y="-8.5" width="1.5" height="3" rx="0.75" fill="#EF4444" />
          <rect x="-24" y="5.5" width="1.5" height="3" rx="0.75" fill="#EF4444" />

          <!-- Side Mirrors -->
          <ellipse cx="8" cy="-12.5" rx="2.2" ry="1.4" fill="#1E293B" stroke="#475569" stroke-width="0.5" />
          <ellipse cx="8" cy="12.5" rx="2.2" ry="1.4" fill="#1E293B" stroke="#475569" stroke-width="0.5" />

          <!-- Hood Contour Character Lines -->
          <line x1="8" y1="-6" x2="20" y2="-4" stroke="#475569" stroke-width="0.6" opacity="0.6" />
          <line x1="8" y1="6" x2="20" y2="4" stroke="#475569" stroke-width="0.6" opacity="0.6" />

          <!-- Cabin Greenhouse / Roof Profile -->
          <path
            d="M -15 -8.5 C -16 -8.5 -16 -7 -16 -5 L -16 5 C -16 7 -16 8.5 -15 8.5 L 6 8.5 C 9 8.5 12 7 13 4 L 13 -4 C 12 -7 9 -8.5 6 -8.5 Z"
            fill="url(#carRoofGrad)"
          />

          <!-- Front Windshield Glass with Reflection Highlight -->
          <path
            d="M 6 -7.5 L 12 -3.5 L 12 3.5 L 6 7.5 Z"
            fill="url(#windshieldGlass)"
          />

          <!-- Rear Windshield Glass -->
          <path
            d="M -11 -7.5 L -14 -6 L -14 6 L -11 7.5 Z"
            fill="url(#windshieldGlass)"
            opacity="0.8"
          />

          <!-- Left Side Windows -->
          <path d="M -9 -8 L 4 -8 L 4 -6.8 L -9 -6.8 Z" fill="url(#windshieldGlass)" opacity="0.85" />

          <!-- Right Side Windows -->
          <path d="M -9 8 L 4 8 L 4 6.8 L -9 6.8 Z" fill="url(#windshieldGlass)" opacity="0.85" />

          <!-- Center Roofline & Parkly Teal Accent Feature -->
          <rect x="-8" y="-4.5" width="11" height="9" rx="2.5" fill="#0F172A" stroke="var(--color-primary)" stroke-width="0.75" />
          <circle cx="-2.5" cy="0" r="1.6" fill="var(--color-primary)" opacity="0.8" />
        </g>

        <!-- Outer Boundary Map Border -->
        <!-- removed black border -->
      </g>
    </svg>
  </div>
</template>

<style scoped>
/* ============================================================
   DESIGN SYSTEM CSS TOKENS & SATELLITE PALETTE
   ============================================================ */
.auth-illustration-container {
  /* --- Light Theme Palette --- */
  --color-road: #CBD5E1;
  --color-road-surface: #D8E2EC;
  --color-road-edge: #94A3B8;
  --color-road-marking: #FFFFFF;
  --color-lane-glow: transparent;

  /* Parking Lot Tokens */
  --color-lot-surface: #F8FAFC;
  --color-lot-border: #94A3B8;
  --color-bay-empty: #FFFFFF;
  --color-bay-occupied: #E2E8F0;
  --color-bay-line: #94A3B8;
  --color-bay-text: #475569;
  --color-occupied-car: #64748B;
  --color-zone-label: #334155;

  /* Windows Tokens: Black in Light Theme */
  --color-window-fill: #0F172A;
  --color-window-glow: transparent;
  --color-window-op-1: 0.85;
  --color-window-op-2: 0.7;
  --color-window-op-3: 0.55;

  /* Greenery */
  --color-greenery: #86EFAC;
  --color-greenery-dark: #22C55E;

  width: 100%;
  max-width: 440px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  position: relative;
}

/* --- Dark Theme Palette Overrides --- */
.auth-illustration-container.is-dark,
:global([data-theme="dark"]) .auth-illustration-container {
  /* Road & Soft Neon Divider */
  --color-road: #0F172A;
  --color-road-surface: #172033;
  --color-road-edge: #334155;
  --color-road-marking: #FEF08A;
  --color-lane-glow: #FDE047;

  /* Parking Lot Tokens */
  --color-lot-surface: #0B1322;
  --color-lot-border: #334155;
  --color-bay-empty: rgba(26, 38, 57, 0.95);
  --color-bay-occupied: rgba(15, 23, 42, 0.95);
  --color-bay-line: #475569;
  --color-bay-text: #94A3B8;
  --color-occupied-car: #334155;
  --color-zone-label: #E2E8F0;

  /* Windows Tokens: Intense Neon Yellow Illumination in Dark Theme */
  --color-window-fill: #FEF08A;
  --color-window-glow: #FDE047;
  --color-window-op-1: 1;
  --color-window-op-2: 0.95;
  --color-window-op-3: 0.88;

  /* Greenery */
  --color-greenery: #166534;
  --color-greenery-dark: #14532D;
}

.scene-svg {
  width: 100%;
  height: auto;
  display: block;
  overflow: visible;
}

/* ============================================================
   1. PARKING BAY STYLING & CONTRAST
   ============================================================ */
.parking-bay-rect {
  stroke-width: 1.4px;
  stroke: var(--color-bay-line);
  transition: fill 0.25s ease, stroke 0.25s ease;
}

.bay-empty {
  fill: var(--color-bay-empty);
  filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.04));
}

.bay-occupied {
  fill: var(--color-bay-occupied);
}

.parked-vehicle-silhouette {
  fill: var(--color-occupied-car);
  opacity: 0.75;
}

.bay-p-mark {
  fill: var(--color-bay-text);
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 11.5px;
  font-weight: 700;
  opacity: 0.85;
  letter-spacing: 0.5px;
}

.aisle-divider {
  stroke: var(--color-bay-line);
  opacity: 0.6;
}

/* ============================================================
   2. ROAD LANE DIVIDERS & SOFT NEON
   ============================================================ */
.road-lane-divider {
  transition: stroke 0.25s ease;
}

.auth-illustration-container.is-dark .road-lane-divider,
:global([data-theme="dark"]) .road-lane-divider {
  filter: url(#laneDividerGlow);
}

/* ============================================================
   3. BUILDING WINDOW LIGHTING
   ============================================================ */
.building-windows rect {
  fill: var(--color-window-fill);
  transition: fill 0.25s ease, opacity 0.25s ease;
}

.window-lit-1 {
  opacity: var(--color-window-op-1);
}

.window-lit-2 {
  opacity: var(--color-window-op-2);
}

.window-lit-3 {
  opacity: var(--color-window-op-3);
}

/* Neon glow filter only in Dark Theme */
.auth-illustration-container.is-dark .building-windows rect,
:global([data-theme="dark"]) .building-windows rect {
  filter: url(#windowGlow);
}

/* ============================================================
   4. ZONE A HEADER LABEL VISIBILITY
   ============================================================ */
.zone-header-label {
  fill: var(--color-zone-label);
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 10.5px;
  font-weight: 700;
  letter-spacing: 2.8px;
  opacity: 0.95;
  transition: fill 0.25s ease;
}

/* Base states managed by Anime.js timeline */
.map-scene {
  transform-origin: center center;
}

.animated-pin {
  transform-origin: 263px 222px;
}

/* Accessible Reduced-Motion fallback */
@media (prefers-reduced-motion: reduce) {
  .map-scene,
  .circuit,
  .car,
  .animated-pin,
  .route-arrow,
  .pin-ripple {
    transition: none !important;
    animation: none !important;
  }
}
</style>
