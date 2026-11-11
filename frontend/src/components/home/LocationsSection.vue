<script setup>
/**
 * LocationsSection Component — Mastercard-Inspired Popular Parking Hubs
 * Locations:
 * - Dhaka Central (Motijheel, Shahbagh, Farmgate)
 * - Mirpur (Mirpur-10, Mirpur-1, DOHS)
 * - Dhanmondi (Satmasjid Road, Dhanmondi 27)
 * - Gulshan (Gulshan 1 & 2, Banani Avenue)
 *
 * Characteristics:
 * - Photographic cards with rounded stadium-like shapes (40px radius)
 * - Attached circular arrow CTA docked onto boundary
 * - Registered facility counts
 */
defineEmits(['selectLocation'])

const locations = [
  {
    id: 'gulshan',
    name: 'Gulshan & Banani',
    district: 'North Dhaka',
    registeredFacilities: 24,
    availableSlotsEstimate: '180+ active bays',
    popularSpots: 'Gulshan Avenue, Road 11, Kemal Ataturk',
    accentColor: '#0F766E',
    svgGraphic: 'corporate',
  },
  {
    id: 'dhanmondi',
    name: 'Dhanmondi',
    district: 'Central Dhaka',
    registeredFacilities: 16,
    availableSlotsEstimate: '110+ active bays',
    popularSpots: 'Satmasjid Rd, Road 27, Shimanto Square',
    accentColor: '#0D9488',
    svgGraphic: 'residential',
  },
  {
    id: 'mirpur',
    name: 'Mirpur',
    district: 'West Dhaka',
    registeredFacilities: 19,
    availableSlotsEstimate: '140+ active bays',
    popularSpots: 'Mirpur-10 Circle, Sony Square, DOHS',
    accentColor: '#14B8A6',
    svgGraphic: 'commercial',
  },
  {
    id: 'dhaka-central',
    name: 'Dhaka Central',
    district: 'South / Hub',
    registeredFacilities: 28,
    availableSlotsEstimate: '220+ active bays',
    popularSpots: 'Motijheel C/A, Farmgate, Shahbagh Hub',
    accentColor: '#0F766E',
    svgGraphic: 'transit',
  },
]
</script>

<template>
  <section class="locations-section" id="locations">
    <div class="locations-container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="eyebrow">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          COVERAGE & HUBS
        </div>
        <h2 class="section-title">Popular parking locations.</h2>
        <p class="section-subtitle">
          Explore registered parking hubs across prime districts with verified slots and automated entry.
        </p>
      </div>

      <!-- 4 Location Stadium Cards -->
      <div class="locations-grid">
        <div
          v-for="loc in locations"
          :key="loc.id"
          class="location-stadium-card"
          @click="$emit('selectLocation', loc)"
        >
          <!-- Background Visual Treatment with Top Mask -->
          <div class="card-visual-header">
            <!-- Stylized Urban Map Pattern Vector -->
            <svg viewBox="0 0 300 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="map-bg-vector">
              <rect width="300" height="160" fill="var(--color-surface-subtle)" />
              <!-- Street Grid -->
              <g stroke="var(--color-border)" stroke-width="2" opacity="0.6">
                <line x1="0" y1="40" x2="300" y2="40" />
                <line x1="0" y1="100" x2="300" y2="100" />
                <line x1="60" y1="0" x2="60" y2="160" />
                <line x1="180" y1="0" x2="180" y2="160" />
                <line x1="240" y1="0" x2="240" y2="160" />
                <!-- Diagonal Avenue -->
                <line x1="20" y1="160" x2="280" y2="0" stroke="var(--color-primary)" stroke-width="2.5" stroke-dasharray="4 4" opacity="0.4" />
              </g>
              <!-- Location Hub Node -->
              <circle cx="150" cy="75" r="32" fill="var(--color-primary-light)" />
              <circle cx="150" cy="75" r="14" fill="var(--color-primary)" />
              <circle cx="150" cy="75" r="5" fill="#FFFFFF" />
            </svg>

            <!-- Attached Circular Arrow CTA (docked onto corner like satellite) -->
            <button
              type="button"
              class="card-satellite-btn"
              aria-label="View facilities in {{ loc.name }}"
              title="Explore {{ loc.name }}"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </div>

          <!-- Card Content Body -->
          <div class="card-body">
            <div class="loc-district-badge">
              <span class="loc-pin-dot"></span>
              {{ loc.district }}
            </div>

            <h3 class="loc-name">{{ loc.name }}</h3>

            <div class="loc-meta">
              <div class="meta-row">
                <span class="meta-label">Registered Facilities:</span>
                <span class="meta-value">{{ loc.registeredFacilities }} facilities</span>
              </div>
              <div class="meta-row">
                <span class="meta-label">Available Capacity:</span>
                <span class="meta-value highlight">{{ loc.availableSlotsEstimate }}</span>
              </div>
            </div>

            <div class="loc-spots-preview">
              <span class="preview-label">Key Hubs:</span>
              <span class="preview-text">{{ loc.popularSpots }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.locations-section {
  width: 100%;
  padding: 88px 24px;
  background-color: var(--color-bg);
}

.locations-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 56px;
}

/* Header */
.section-header {
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

/* 4-Card Stadium Grid */
.locations-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

/* Stadium Card with 36px radius */
.location-stadium-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 36px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: var(--shadow-sm);
  cursor: pointer;
  transition:
    transform var(--transition-normal),
    box-shadow var(--transition-normal),
    border-color var(--transition-normal);
}

.location-stadium-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-halo);
  border-color: var(--color-primary);
}

/* Visual Header */
.card-visual-header {
  position: relative;
  width: 100%;
  height: 140px;
  overflow: hidden;
  background-color: var(--color-surface-subtle);
  border-bottom: 1px solid var(--color-border);
}

.map-bg-vector {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-smooth);
}

.location-stadium-card:hover .map-bg-vector {
  transform: scale(1.05);
}

/* Satellite Micro-CTA on card boundary */
.card-satellite-btn {
  position: absolute;
  bottom: 12px;
  right: 14px;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background-color: var(--color-surface);
  border: 1.5px solid var(--color-border);
  color: var(--color-text);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition:
    transform var(--transition-fast),
    background-color var(--transition-fast),
    color var(--transition-fast),
    border-color var(--transition-fast);
}

.location-stadium-card:hover .card-satellite-btn {
  background-color: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-primary-contrast);
  transform: rotate(-10deg) scale(1.06);
}

/* Body */
.card-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.loc-district-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: var(--color-primary);
  text-transform: uppercase;
}

.loc-pin-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.loc-name {
  font-size: 1.35rem;
  font-weight: 500;
  letter-spacing: -0.02em;
  color: var(--color-text);
  margin: 0;
}

.loc-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 10px 0;
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
}

.meta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.8125rem;
}

.meta-label {
  color: var(--color-text-muted);
}

.meta-value {
  font-weight: 600;
  color: var(--color-text);
}

.meta-value.highlight {
  color: var(--color-primary);
}

.loc-spots-preview {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.preview-label {
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-text-subtle);
  text-transform: uppercase;
}

.preview-text {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  line-height: 1.4;
}

/* Breakpoints */
@media (max-width: 1080px) {
  .locations-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 640px) {
  .locations-section {
    padding: 56px 16px;
  }

  .locations-grid {
    grid-template-columns: 1fr;
  }
}
</style>

