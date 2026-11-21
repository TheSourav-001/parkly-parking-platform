<script setup>
/**
 * ParkingIllustration Component
 * Premium original vector illustration for Parkly Authentication.
 * Features:
 * - Multi-level parking garage structure with architectural lines
 * - Sleek modern EV/car parked in a designated bay with neon floor guide markings
 * - Isometric parking bays with 'P' emblems
 * - Location pin badge ("Parkly Spot AI")
 * - Subtle road / access pathway
 * - Live parking availability indicators (e.g. "Spot #24 Available", "Level 2 • 88% Empty")
 * - 100% theme-adaptive via CSS variables and currentColor.
 */
</script>

<template>
  <div class="illustration-wrapper" aria-hidden="true">
    <!-- Ambient glowing backdrop effect -->
    <div class="ambient-glow"></div>

    <svg
      viewBox="0 0 540 420"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      class="parking-svg"
    >
      <defs>
        <!-- Gradients -->
        <linearGradient id="garageWallGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--color-primary)" stop-opacity="0.18" />
          <stop offset="100%" stop-color="var(--color-primary)" stop-opacity="0.04" />
        </linearGradient>

        <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="var(--color-primary)" />
          <stop offset="100%" stop-color="#2DD4BF" />
        </linearGradient>

        <linearGradient id="carBodyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="var(--color-primary)" />
          <stop offset="100%" stop-color="#0F766E" />
        </linearGradient>

        <linearGradient id="glassGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#E2E8F0" stop-opacity="0.8" />
          <stop offset="100%" stop-color="#94A3B8" stop-opacity="0.4" />
        </linearGradient>

        <filter id="cardShadow" x="-10%" y="-10%" width="130%" height="130%">
          <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="rgba(15, 23, 42, 0.15)" />
        </filter>
      </defs>

      <!-- GROUND PLANE / ACCESS ROAD -->
      <path
        d="M20 290 L260 170 L520 290 L280 410 Z"
        fill="var(--color-surface)"
        stroke="var(--color-border)"
        stroke-width="1.5"
      />

      <!-- Pathway / Driving Lane Marking -->
      <path
        d="M100 280 L260 200 L440 285"
        stroke="var(--color-primary)"
        stroke-opacity="0.3"
        stroke-width="2"
        stroke-dasharray="8 6"
      />

      <!-- PARKING GARAGE STRUCTURE (Multi-level Modern Pavilion) -->
      <!-- Left Garage Column & Structure -->
      <path
        d="M140 160 L140 70 L280 135 L280 225 Z"
        fill="url(#garageWallGrad)"
        stroke="var(--color-border)"
        stroke-width="1.5"
      />
      <!-- Garage Roof Top -->
      <path
        d="M140 70 L260 20 L400 85 L280 135 Z"
        fill="var(--color-surface)"
        stroke="var(--color-border)"
        stroke-width="1.5"
      />
      <!-- Right Garage Face -->
      <path
        d="M280 135 L400 85 L400 175 L280 225 Z"
        fill="url(#garageWallGrad)"
        stroke="var(--color-border)"
        stroke-width="1.5"
      />

      <!-- Multi-level floors & architectural horizontal louvers -->
      <line x1="140" y1="100" x2="280" y2="165" stroke="var(--color-border)" stroke-width="1.5" />
      <line x1="140" y1="130" x2="280" y2="195" stroke="var(--color-border)" stroke-width="1.5" />
      <line x1="280" y1="165" x2="400" y2="115" stroke="var(--color-border)" stroke-width="1.5" />
      <line x1="280" y1="195" x2="400" y2="145" stroke="var(--color-border)" stroke-width="1.5" />

      <!-- Garage Modern Signboard 'PARKLY' -->
      <rect x="235" y="45" width="70" height="20" rx="4" fill="var(--color-surface)" stroke="var(--color-primary)" stroke-width="1.5" />
      <text x="270" y="59" fill="var(--color-primary)" font-family="system-ui, sans-serif" font-size="9" font-weight="700" letter-spacing="1.5" text-anchor="middle">PARKLY</text>

      <!-- PARKING BAYS (On Ground Deck) -->
      <!-- Bay 1 (Empty with green status line) -->
      <path d="M190 270 L230 250 L275 272 L235 292 Z" fill="var(--color-surface-subtle)" stroke="var(--color-border)" stroke-width="1" />
      <path d="M195 270 L230 252" stroke="var(--color-primary)" stroke-width="2" />
      <path d="M235 290 L270 273" stroke="var(--color-primary)" stroke-width="2" />
      <text x="233" y="275" fill="var(--color-text-muted)" font-family="system-ui, sans-serif" font-size="11" font-weight="600" opacity="0.6">P1</text>

      <!-- Bay 2: Designated for the Car -->
      <path d="M255 302 L295 282 L340 304 L300 324 Z" fill="var(--color-primary-light)" fill-opacity="0.4" stroke="var(--color-primary)" stroke-width="1.5" />
      <path d="M260 302 L295 284" stroke="var(--color-primary)" stroke-width="2.5" />
      <path d="M300 322 L335 305" stroke="var(--color-primary)" stroke-width="2.5" />

      <!-- Bay 3 (Empty with markings) -->
      <path d="M320 334 L360 314 L405 336 L365 356 Z" fill="var(--color-surface-subtle)" stroke="var(--color-border)" stroke-width="1" />
      <path d="M325 334 L360 316" stroke="var(--color-border)" stroke-width="2" />
      <path d="M365 354 L400 337" stroke="var(--color-border)" stroke-width="2" />
      <text x="363" y="339" fill="var(--color-text-muted)" font-family="system-ui, sans-serif" font-size="11" font-weight="600" opacity="0.6">P3</text>

      <!-- MODERN SLEEK CAR (Isometric in Bay 2) -->
      <g transform="translate(18, -12)">
        <!-- Car Shadow -->
        <ellipse cx="282" cy="308" rx="42" ry="14" fill="rgba(15, 23, 42, 0.25)" />

        <!-- Car Wheels -->
        <ellipse cx="258" cy="304" rx="7" ry="9" fill="#0F172A" />
        <ellipse cx="258" cy="304" rx="3" ry="4" fill="var(--color-primary)" />
        <ellipse cx="308" cy="285" rx="7" ry="9" fill="#0F172A" />
        <ellipse cx="308" cy="285" rx="3" ry="4" fill="var(--color-primary)" />

        <!-- Car Lower Body Chassis -->
        <path
          d="M245 292 L290 270 L325 285 L280 310 Z"
          fill="url(#carBodyGrad)"
          stroke="#0D655E"
          stroke-width="1"
        />

        <!-- Car Cabin / Roof Structure -->
        <path
          d="M260 280 L285 268 L308 277 L285 292 Z"
          fill="var(--color-surface-raised)"
          stroke="var(--color-primary)"
          stroke-width="1"
        />

        <!-- Windshield & Windows -->
        <path
          d="M263 280 L283 270 L283 278 L265 286 Z"
          fill="url(#glassGrad)"
        />
        <path
          d="M285 270 L305 278 L298 285 L285 278 Z"
          fill="url(#glassGrad)"
        />

        <!-- LED Headlights / Ambient Glow -->
        <circle cx="247" cy="293" r="2.5" fill="#5EEAD4" />
        <circle cx="252" cy="298" r="2.5" fill="#5EEAD4" />
      </g>

      <!-- FLOATING ACCENT 1: SMART RESERVATION BADGE (Top Right) -->
      <g class="float-badge-1" filter="url(#cardShadow)">
        <rect x="340" y="55" width="165" height="52" rx="10" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1.2" />
        <!-- Pulse Status Dot -->
        <circle cx="362" cy="81" r="5" fill="var(--color-success)" />
        <circle cx="362" cy="81" r="9" fill="var(--color-success)" fill-opacity="0.25" />
        <text x="378" y="76" fill="var(--color-text)" font-family="system-ui, sans-serif" font-size="12" font-weight="600">Spot #24 Reserved</text>
        <text x="378" y="93" fill="var(--color-text-muted)" font-family="system-ui, sans-serif" font-size="10">Level 2 • EV Ready</text>
      </g>

      <!-- FLOATING ACCENT 2: LOCATION PIN (Center Left) -->
      <g class="float-badge-2" filter="url(#cardShadow)">
        <g transform="translate(75, 125)">
          <path
            d="M24 0C10.745 0 0 10.745 0 24c0 18 24 38 24 38s24-20 24-38C48 10.745 37.255 0 24 0z"
            fill="url(#accentGrad)"
          />
          <circle cx="24" cy="22" r="10" fill="var(--color-surface)" />
          <text x="24" y="26" fill="var(--color-primary)" font-family="system-ui, sans-serif" font-size="12" font-weight="800" text-anchor="middle">P</text>
        </g>
      </g>

      <!-- FLOATING ACCENT 3: REAL-TIME AVAILABILITY PILL (Bottom Left) -->
      <g class="float-badge-3" filter="url(#cardShadow)">
        <rect x="35" y="325" width="160" height="42" rx="21" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="1.2" />
        <circle cx="56" cy="346" r="12" fill="var(--color-primary-light)" />
        <!-- Sparkle / AI icon -->
        <path d="M56 341 L57.5 344.5 L61 346 L57.5 347.5 L56 351 L54.5 347.5 L51 346 L54.5 344.5 Z" fill="var(--color-primary)" />
        <text x="76" y="344" fill="var(--color-text)" font-family="system-ui, sans-serif" font-size="11" font-weight="600">AI Spot Finder</text>
        <text x="76" y="357" fill="var(--color-primary)" font-family="system-ui, sans-serif" font-size="9" font-weight="600">Optimal Match Found</text>
      </g>
    </svg>
  </div>
</template>

<style scoped>
.illustration-wrapper {
  position: relative;
  width: 100%;
  max-width: 520px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.ambient-glow {
  position: absolute;
  width: 320px;
  height: 320px;
  border-radius: 50%;
  background: radial-gradient(circle, var(--color-primary-light) 0%, transparent 70%);
  opacity: 0.5;
  filter: blur(40px);
  z-index: 0;
  pointer-events: none;
}

.parking-svg {
  width: 100%;
  height: auto;
  position: relative;
  z-index: 1;
  overflow: visible;
}

/* Subtle floating animations for high-end SaaS feel */
.float-badge-1 {
  animation: floatSlow 5s ease-in-out infinite alternate;
}

.float-badge-2 {
  animation: floatSlow 4.5s ease-in-out infinite alternate-reverse;
}

.float-badge-3 {
  animation: floatSlow 6s ease-in-out infinite alternate;
}

@keyframes floatSlow {
  0% {
    transform: translateY(0px);
  }
  100% {
    transform: translateY(-6px);
  }
}

@media (prefers-reduced-motion: reduce) {
  .float-badge-1,
  .float-badge-2,
  .float-badge-3 {
    animation: none !important;
  }
}
</style>

