<script setup>
/**
 * FeaturedFacilitiesSection Component — Mastercard-Inspired Facility Showcase
 * Displays realistic verified parking facilities:
 * - Image / Facility architectural vector visual
 * - Facility name & verified badge
 * - Location & distance
 * - Starting price & slots available
 * - Vehicle compatibility tags (Sedan, SUV, Bike, EV)
 * - View Details CTA with circular arrow
 */
import { ref } from 'vue'
import AppButton from '../common/AppButton.vue'

defineEmits(['viewFacility', 'bookFacility'])

const facilities = ref([
  {
    id: 1,
    name: 'Gulshan Tower Automated Garage',
    location: 'Gulshan-1 Circle, Dhaka',
    distance: '0.4 km away',
    hourlyRate: 80,
    dailyRate: 600,
    currency: '৳',
    availableSlots: 14,
    totalSlots: 45,
    isCovered: true,
    hasEv: true,
    rating: 4.9,
    compatibility: ['Car', 'SUV', 'EV'],
    features: ['CCTV 24/7', 'Boom Barrier', 'EV Fast Charge'],
    badge: 'Popular',
  },
  {
    id: 2,
    name: 'Dhanmondi 27 Square Parking',
    location: 'Satmasjid Road, Dhanmondi',
    distance: '1.2 km away',
    hourlyRate: 60,
    dailyRate: 450,
    currency: '৳',
    availableSlots: 8,
    totalSlots: 30,
    isCovered: true,
    hasEv: false,
    rating: 4.8,
    compatibility: ['Car', 'Sedan', 'Bike'],
    features: ['Attendant on duty', 'Valet Optional', 'Security Guard'],
    badge: 'Near You',
  },
  {
    id: 3,
    name: 'Mirpur DOHS Smart Bay Hub',
    location: 'Road 12, Mirpur DOHS, Dhaka',
    distance: '2.5 km away',
    hourlyRate: 50,
    dailyRate: 350,
    currency: '৳',
    availableSlots: 22,
    totalSlots: 60,
    isCovered: true,
    hasEv: true,
    rating: 4.7,
    compatibility: ['Car', 'SUV', 'Bike', 'EV'],
    features: ['License Plate Recog.', 'Mobile Pay', '24/7 Access'],
    badge: 'Best Value',
  },
  {
    id: 4,
    name: 'Motijheel Commercial Plaza Parking',
    location: 'Dilkusha C/A, Motijheel',
    distance: '3.1 km away',
    hourlyRate: 90,
    dailyRate: 700,
    currency: '৳',
    availableSlots: 6,
    totalSlots: 80,
    isCovered: true,
    hasEv: false,
    rating: 4.6,
    compatibility: ['Car', 'SUV', 'Van'],
    features: ['Multi-level Covered', 'Elevator Access', 'Guarded'],
    badge: 'High Demand',
  },
])
</script>

<template>
  <section class="facilities-section" id="featured">
    <div class="facilities-container">
      <!-- Section Header -->
      <div class="section-header-row">
        <div class="section-header-left">
          <div class="eyebrow">
            <span class="eyebrow-dot" aria-hidden="true"></span>
            VERIFIED FACILITIES
          </div>
          <h2 class="section-title">Featured parking facilities.</h2>
          <p class="section-subtitle">
            Secure, verified garages with automated entry gates and real-time slot tracking.
          </p>
        </div>

        <div class="section-header-right">
          <a href="#discovery" class="explore-all-pill">
            View All Facilities (50+)
            <span class="pill-arrow" aria-hidden="true">&nearr;</span>
          </a>
        </div>
      </div>

      <!-- Facilities Grid -->
      <div class="facilities-grid">
        <div
          v-for="facility in facilities"
          :key="facility.id"
          class="facility-card"
        >
          <!-- Image Frame with 24px inner radius -->
          <div class="facility-visual">
            <!-- Architectural Graphic Illustration for Facility -->
            <svg viewBox="0 0 400 220" fill="none" xmlns="http://www.w3.org/2000/svg" class="facility-svg">
              <rect width="400" height="220" fill="#0B1220" />
              <!-- Facility Perspective Structure -->
              <path d="M 40,180 L 160,50 L 360,50 L 360,180 Z" fill="#111C2E" stroke="var(--color-border)" stroke-width="1.5" />
              <path d="M 160,50 L 160,180" stroke="var(--color-border)" stroke-width="1" stroke-dasharray="3 3" />
              <line x1="80" y1="180" x2="160" y2="90" stroke="var(--color-primary)" stroke-width="2" opacity="0.6" />
              <!-- Parking bays -->
              <g stroke="var(--color-primary)" stroke-width="1.5" opacity="0.4">
                <rect x="180" y="80" width="45" height="70" rx="4" />
                <rect x="240" y="80" width="45" height="70" rx="4" />
                <rect x="300" y="80" width="45" height="70" rx="4" />
              </g>
              <!-- Car in bay 1 -->
              <g transform="translate(186, 92)">
                <rect width="32" height="46" rx="8" fill="#14B8A6" fill-opacity="0.3" stroke="#14B8A6" stroke-width="1.5" />
                <circle cx="16" cy="12" r="3" fill="#FFFFFF" opacity="0.8" />
              </g>
              <!-- Sensor beam -->
              <circle cx="202" cy="70" r="4" fill="#34D399" />
              <line x1="202" y1="74" x2="202" y2="90" stroke="#34D399" stroke-width="1.5" stroke-dasharray="2 2" />
            </svg>

            <!-- Floating Top Badges -->
            <div class="visual-badges">
              <span class="facility-badge-popular">{{ facility.badge }}</span>
              <span v-if="facility.hasEv" class="facility-badge-ev">⚡ EV Ready</span>
            </div>

            <!-- Rating Chip -->
            <div class="facility-rating-chip">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="#EAB308" stroke="#EAB308">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
              </svg>
              <span>{{ facility.rating }}</span>
            </div>
          </div>

          <!-- Facility Body -->
          <div class="facility-body">
            <!-- Location and Distance -->
            <div class="facility-subrow">
              <span class="facility-location">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                {{ facility.location }}
              </span>
              <span class="facility-distance">{{ facility.distance }}</span>
            </div>

            <h3 class="facility-name">{{ facility.name }}</h3>

            <!-- Vehicle Compatibility Badges -->
            <div class="compatibility-row">
              <span
                v-for="compat in facility.compatibility"
                :key="compat"
                class="compat-chip"
              >
                {{ compat }}
              </span>
              <span class="amenity-chip" v-for="feat in facility.features.slice(0, 1)" :key="feat">
                &bull; {{ feat }}
              </span>
            </div>

            <!-- Pricing & Slot Availability Footer -->
            <div class="facility-footer-row">
              <div class="pricing-block">
                <span class="price-from">Starting from</span>
                <div class="price-tag">
                  <span class="price-amount">{{ facility.currency }}{{ facility.hourlyRate }}</span>
                  <span class="price-unit">/ hour</span>
                </div>
              </div>

              <div class="slots-block">
                <span class="slots-pill">
                  <span class="slot-live-dot"></span>
                  <strong>{{ facility.availableSlots }}</strong> spots free
                </span>
              </div>
            </div>

            <!-- Action Row -->
            <div class="facility-action-row">
              <AppButton
                variant="primary"
                size="md"
                pill
                block
                @click="$emit('bookFacility', facility)"
              >
                Book This Spot
                <template #suffix>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                  </svg>
                </template>
              </AppButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.facilities-section {
  width: 100%;
  padding: 88px 24px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
}

.facilities-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 56px;
}

/* Header */
.section-header-row {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.section-header-left {
  max-width: 620px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.eyebrow {
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
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.section-title {
  font-size: clamp(2rem, 3.5vw, 2.75rem);
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.025em;
  color: var(--color-text);
  margin: 0;
}

.section-subtitle {
  font-size: 1.05rem;
  color: var(--color-text-muted);
  line-height: 1.55;
  margin: 0;
}

.explore-all-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: var(--radius-pill, 999px);
  border: 1.5px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text);
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.explore-all-pill:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  background-color: var(--color-surface-subtle);
  text-decoration: none;
}

.pill-arrow {
  font-size: 0.95rem;
}

/* Facilities Grid (2x2 on Desktop) */
.facilities-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 28px;
}

/* Facility Card: Mastercard 36px corner */
.facility-card {
  background-color: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: 32px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-sm);
  transition:
    transform var(--transition-normal),
    box-shadow var(--transition-normal),
    border-color var(--transition-normal);
}

.facility-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-halo);
  border-color: var(--color-primary);
}

/* Visual Header Frame */
.facility-visual {
  position: relative;
  width: 100%;
  height: 200px;
  background-color: #0B1220;
  overflow: hidden;
}

.facility-svg {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-smooth);
}

.facility-card:hover .facility-svg {
  transform: scale(1.04);
}

.visual-badges {
  position: absolute;
  top: 14px;
  left: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  z-index: 2;
}

.facility-badge-popular {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  font-size: 0.6875rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: var(--radius-pill, 999px);
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.facility-badge-ev {
  background-color: rgba(20, 184, 166, 0.2);
  color: #5EEAD4;
  border: 1px solid rgba(94, 234, 212, 0.4);
  font-size: 0.6875rem;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: var(--radius-pill, 999px);
  backdrop-filter: blur(4px);
}

.facility-rating-chip {
  position: absolute;
  top: 14px;
  right: 16px;
  display: flex;
  align-items: center;
  gap: 4px;
  background-color: rgba(15, 23, 42, 0.75);
  color: #FFFFFF;
  border-radius: var(--radius-pill, 999px);
  padding: 4px 10px;
  font-size: 0.75rem;
  font-weight: 600;
  backdrop-filter: blur(4px);
}

/* Body */
.facility-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  flex: 1;
}

.facility-subrow {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.8125rem;
}

.facility-location {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  color: var(--color-text-muted);
}

.facility-distance {
  color: var(--color-primary);
  font-weight: 600;
}

.facility-name {
  font-size: 1.35rem;
  font-weight: 500;
  letter-spacing: -0.02em;
  color: var(--color-text);
  margin: 0;
  line-height: 1.25;
}

.compatibility-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.compat-chip {
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: var(--radius-pill, 999px);
}

.amenity-chip {
  font-size: 0.75rem;
  color: var(--color-text-subtle);
}

/* Footer Pricing & Slots */
.facility-footer-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 14px;
  border-top: 1px solid var(--color-border);
}

.pricing-block {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.price-from {
  font-size: 0.6875rem;
  color: var(--color-text-subtle);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.price-tag {
  display: flex;
  align-items: baseline;
  gap: 4px;
}

.price-amount {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-text);
  letter-spacing: -0.03em;
}

.price-unit {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
}

.slots-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: var(--radius-pill, 999px);
  background-color: rgba(22, 163, 74, 0.1);
  color: var(--color-success);
  font-size: 0.8125rem;
  font-weight: 500;
}

.slot-live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--color-success);
}

.facility-action-row {
  margin-top: 4px;
}

/* Breakpoints */
@media (max-width: 960px) {
  .facilities-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .facilities-section {
    padding: 56px 16px;
  }
}
</style>

