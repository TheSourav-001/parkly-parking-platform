<script setup>
/**
 * VehicleCard Component (Light Theme-Matched Edge-to-Edge Card)
 *
 * Requirements:
 * 1. Studio Backdrop in site palette: #F8FAFC to soft teal-tint (#ECFDF5 / #CCFBF1).
 *    Subtle floor ground shadow, faint large "P" or subtle topography at 4-6% opacity.
 * 2. Vehicle Cutout / Light Studio Shot:
 *    White/light-colored vehicle placed upper-middle / center-right.
 *    No dark garage, no brand badges, soft grounded shadow beneath.
 * 3. Light Fade:
 *    linear-gradient(to top, rgba(248,250,252,0.96) 0%, rgba(248,250,252,0.85) 30%, rgba(248,250,252,0) 62%)
 *    Text in ink #0F172A, rate in brand teal #0F766E.
 * 4. True Edge-to-Edge:
 *    Fills 100% of stage, border-radius: 0 (or top-corners only during deal-in).
 */
import { computed } from 'vue'
import { useSearchFiltersStore } from '../../stores/searchFilters'

const props = defineProps({
  card: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    required: true,
  },
  styleObject: {
    type: Object,
    default: () => ({}),
  },
  dimOpacity: {
    type: Number,
    default: 0,
  },
  isEager: {
    type: Boolean,
    default: false,
  },
})

const searchFilters = useSearchFiltersStore()

const ariaRateLabel = computed(() => {
  return `${props.card.displayName}: ${props.card.rate_display || 'Rates not published yet'}`
})

function handleFindParking() {
  if (props.card.filterKey) {
    searchFilters.setVehicleType(props.card.filterKey)
  }

  const target = document.getElementById('discovery') || document.getElementById('find-parking')
  if (target) {
    const navOffset = 90
    const topPos = target.getBoundingClientRect().top + window.pageYOffset - navOffset
    window.scrollTo({ top: topPos, behavior: 'smooth' })
  }
}
</script>

<template>
  <article
    class="vehicle-deck-card"
    :style="styleObject"
    :aria-label="ariaRateLabel"
  >
    <!-- Full-Bleed Card Background Layer -->
    <div class="card-media-background" aria-hidden="true">
      <img
        :src="card.photo1920"
        :srcset="`${card.photo960} 960w, ${card.photo1920} 1920w`"
        sizes="100vw"
        :alt="`${card.displayName} background`"
        class="card-full-photo"
        :loading="isEager ? 'eager' : 'lazy'"
        decoding="async"
      />

      <!-- Soft Light Fade Overlay covering bottom area for text legibility -->
      <div class="card-light-fade-overlay"></div>

      <!-- Clean Teal-Tint Dimming Layer for covered cards -->
      <div
        class="card-teal-dim-overlay"
        :style="{ opacity: dimOpacity }"
      ></div>
    </div>

    <!-- Top Left Index Chip (Theme-Matched Clean Style) -->
    <div class="card-clean-chip" aria-hidden="true">
      <span class="chip-num">{{ card.indexStr }}</span>
      <span class="chip-sep">/</span>
      <span class="chip-tot">0{{ total }}</span>
    </div>

    <!-- Bottom Editorial Content Safe Area (Edge-to-Edge aligned) -->
    <div class="card-safe-content">
      <div class="content-left-block">
        <div class="rate-status-row">
          <span class="rate-headline">{{ card.rate_display || 'Rates not published yet' }}</span>
          <span v-if="card.facility_count > 0" class="facilities-chip">
            {{ card.facility_count }} facilities support this
          </span>
        </div>

        <h3 class="vehicle-name-title">{{ card.displayName }}</h3>
        <p class="vehicle-sub-desc">{{ card.description }}</p>
        <span class="operator-note">Operator-reported rates across registered Dhaka facilities.</span>
      </div>

      <div class="content-right-block">
        <button
          type="button"
          class="deck-action-btn"
          :aria-label="`Find ${card.displayName} parking`"
          @click="handleFindParking"
        >
          <span>Find {{ card.displayName.split('/')[0].trim() }} parking</span>
          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="arrow-icon"
            aria-hidden="true"
          >
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12 5 19 12 12 19" />
          </svg>
        </button>
      </div>
    </div>
  </article>
</template>

<style scoped>
.vehicle-deck-card {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border-radius: 0;
  border-top-left-radius: 28px;
  border-top-right-radius: 28px;
  overflow: hidden;
  border-top: 1px solid var(--color-border, #E2E8F0);
  box-shadow: 0 40px 80px -40px rgba(15, 23, 42, 0.22);
  transform-origin: 50% 25%;
  will-change: transform, filter;
  background-color: var(--color-bg, #F8FAFC);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Full-Bleed Media Background: Image Spans 100% Width & Height of Card */
.card-media-background {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 1;
}

.card-full-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 42%;
  display: block;
  transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.vehicle-deck-card:hover .card-full-photo {
  transform: scale(1.03);
}

/* Light Theme Fade Overlay: crisp readable background behind text in all themes */
.card-light-fade-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(255, 255, 255, 0.98) 0%,
    rgba(255, 255, 255, 0.92) 28%,
    rgba(255, 255, 255, 0.55) 52%,
    rgba(255, 255, 255, 0.1) 72%,
    transparent 100%
  );
  pointer-events: none;
  z-index: 2;
}

/* Dim overlay when cards are covered */
.card-teal-dim-overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(15, 118, 110, 0.1);
  pointer-events: none;
  z-index: 3;
  transition: opacity 0.25s ease;
}

/* Index Chip (Clean Flat Theme Style) */
.card-clean-chip {
  position: absolute;
  top: clamp(20px, 4vh, 36px);
  left: clamp(24px, 6vw, 96px);
  z-index: 10;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 999px;
  padding: 6px 14px;
  font-family: var(--font-sans);
  font-size: 0.8125rem;
  font-weight: 600;
  color: #0F172A;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
}

.chip-num {
  color: #0F766E;
}

.chip-sep,
.chip-tot {
  color: #64748B;
}

/* Bottom Safe Content Area (Aligned to 100vw Safe Margins) */
.card-safe-content {
  position: relative;
  z-index: 10;
  margin-top: auto;
  padding: 0 clamp(24px, 6vw, 96px) clamp(32px, 5vh, 48px);
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 32px;
  width: 100%;
}

.content-left-block {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 720px;
}

.rate-status-row {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.rate-headline {
  font-family: var(--font-sans);
  font-size: clamp(1rem, 1.4vw, 1.25rem);
  font-weight: 700;
  color: #0F766E !important;
  letter-spacing: -0.01em;
  font-variant-numeric: tabular-nums;
}

.facilities-chip {
  font-size: 0.75rem;
  font-weight: 600;
  color: #475569 !important;
  background: #FFFFFF !important;
  padding: 3px 12px;
  border-radius: 999px;
  border: 1px solid #E2E8F0 !important;
}

.vehicle-name-title {
  font-family: var(--font-sans);
  font-size: clamp(2rem, 3.8vw, 3.4rem);
  font-weight: 600;
  line-height: 1.08;
  letter-spacing: -0.03em;
  color: #0F172A !important;
  margin: 0;
}

.vehicle-sub-desc {
  font-family: var(--font-sans);
  font-size: clamp(0.9375rem, 1.15vw, 1.08rem);
  color: #475569 !important;
  line-height: 1.5;
  margin: 0;
}

.operator-note {
  font-size: 0.75rem;
  color: #64748B !important;
  opacity: 0.9;
}

/* Right Content Button */
.content-right-block {
  flex-shrink: 0;
}

.deck-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background-color: var(--color-primary, #0F766E);
  color: #FFFFFF;
  border: 1px solid var(--color-primary, #0F766E);
  border-radius: 999px;
  padding: 14px 26px;
  font-family: var(--font-sans);
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(15, 118, 110, 0.25);
  transition: all var(--transition-fast);
}

.deck-action-btn:hover {
  background-color: var(--color-primary-hover, #0D655E);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(15, 118, 110, 0.35);
}

.arrow-icon {
  transition: transform var(--transition-fast);
}

.deck-action-btn:hover .arrow-icon {
  transform: translateX(4px);
}

/* Mobile & Tablet Optimizations */
@media (max-width: 1024px) {
  .vehicle-display-stage {
    top: 8%;
    right: 50%;
    transform: translateX(50%);
    width: 85vw;
    height: 48%;
  }

  .card-safe-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
  }

  .deck-action-btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .vehicle-deck-card {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
  }

  .vehicle-display-stage {
    top: 6%;
    height: 44%;
    width: 92vw;
  }

  .card-safe-content {
    padding: 0 20px 24px;
    gap: 16px;
  }

  .vehicle-name-title {
    font-size: 1.85rem;
  }
}
</style>
