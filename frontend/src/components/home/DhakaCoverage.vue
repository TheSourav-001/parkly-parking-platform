<script setup>
/**
 * DhakaCoverage Component (Editorial Minimalist Redesign)
 *
 * Requirements:
 * 1. Zero emojis. Replaced with pure inline SVGs (1.5px stroke, currentColor).
 * 2. Clean table/grid of area names with facility counts and thin 1px separators.
 * 3. Stable anchor ID: #locations.
 * 4. Selecting an area sets useSearchFiltersStore and scrolls to #discovery.
 */
import { ref, onMounted } from 'vue'
import SectionHeader from '../common/SectionHeader.vue'
import { useSearchFiltersStore } from '../../stores/searchFilters'

const searchFilters = useSearchFiltersStore()

const coverageAreas = ref([
  { id: 1, name: 'Gulshan', name_bn: 'গুলশান', slug: 'gulshan', facility_count: 4, status: 'active' },
  { id: 2, name: 'Banani', name_bn: 'বনানী', slug: 'banani', facility_count: 2, status: 'active' },
  { id: 3, name: 'Dhanmondi', name_bn: 'ধানমন্ডি', slug: 'dhanmondi', facility_count: 3, status: 'active' },
  { id: 4, name: 'Mirpur', name_bn: 'মিরপুর', slug: 'mirpur', facility_count: 3, status: 'active' },
  { id: 5, name: 'Uttara', name_bn: 'উত্তরা', slug: 'uttara', facility_count: 2, status: 'active' },
  { id: 6, name: 'Motijheel', name_bn: 'মতিঝিল', slug: 'motijheel', facility_count: 2, status: 'active' },
  { id: 7, name: 'Tejgaon & Panthapath', name_bn: 'তেজগাঁও ও পান্থপথ', slug: 'tejgaon', facility_count: 2, status: 'active' },
  { id: 8, name: 'Badda & Kuril', name_bn: 'বাড্ডা ও কুড়িল', slug: 'badda', facility_count: 1, status: 'active' },
  { id: 9, name: 'Mohammadpur', name_bn: 'মোহাম্মদপুর', slug: 'mohammadpur', facility_count: 0, status: 'coming_soon' },
  { id: 10, name: 'Khilgaon', name_bn: 'খিলগাঁও', slug: 'khilgaon', facility_count: 0, status: 'coming_soon' },
])

async function fetchCoverage() {
  try {
    const res = await fetch('/api/locations/coverage')
    if (res.ok) {
      const data = await res.json()
      if (data.areas && data.areas.length > 0) {
        coverageAreas.value = data.areas
      }
    }
  } catch (e) {
    // Graceful offline fallback
  }
}

function selectArea(area) {
  if (area.facility_count === 0) return
  searchFilters.setThana(area.slug)
  const mapElement = document.getElementById('discovery')
  if (mapElement) {
    mapElement.scrollIntoView({ behavior: 'smooth' })
  }
}

onMounted(() => {
  fetchCoverage()
})
</script>

<template>
  <section id="locations" class="dhaka-coverage-section">
    <div class="coverage-container">
      <SectionHeader
        eyebrow="Coverage Hubs"
        title="Official Dhaka Parking Networks"
        subtitle="Parkly connects verified parking inventory across key metropolitan business, civic, and transit districts."
      />

      <!-- Clean List Grid with Hairline Separators -->
      <div class="coverage-table-card">
        <div class="table-header-row">
          <span class="col-zone">Metropolitan Zone</span>
          <span class="col-status">Status & Inventory</span>
          <span class="col-action">Action</span>
        </div>

        <div
          v-for="area in coverageAreas"
          :key="area.id"
          class="table-data-row"
          :class="{ 'is-inactive': area.facility_count === 0 }"
        >
          <div class="zone-info">
            <span class="zone-name">{{ area.name }}</span>
            <span v-if="area.name_bn" class="zone-bn">({{ area.name_bn }})</span>
          </div>

          <div class="zone-inventory">
            <span
              v-if="area.facility_count > 0"
              class="inventory-tag"
            >
              {{ area.facility_count }} Registered Hubs
            </span>
            <span v-else class="coming-soon-tag">Onboarding Soon</span>
          </div>

          <div class="zone-cta">
            <button
              class="table-view-btn"
              :disabled="area.facility_count === 0"
              @click="selectArea(area)"
            >
              {{ area.facility_count > 0 ? 'Select On Map' : 'Unavailable' }}
            </button>
          </div>
        </div>
      </div>

      <div class="dhaka-footer-note">
        <span class="note-pill">Dhaka Metro Focus</span>
        <p class="note-text">
          Parkly is actively expanding across Dhaka District. Chittagong and Sylhet metropolitan areas are scheduled for future onboarding phases.
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.dhaka-coverage-section {
  width: 100%;
  padding: 120px 24px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  scroll-margin-top: var(--nav-h, 72px);
}

.coverage-container {
  max-width: 1200px;
  margin: 0 auto;
}

.coverage-table-card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  overflow: hidden;
  margin-bottom: 32px;
}

.table-header-row {
  display: grid;
  grid-template-columns: 2fr 1.5fr 1fr;
  padding: 16px 24px;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-text-muted);
  letter-spacing: 0.05em;
}

.table-data-row {
  display: grid;
  grid-template-columns: 2fr 1.5fr 1fr;
  align-items: center;
  padding: 16px 24px;
  border-bottom: 1px solid var(--color-border);
  transition: background 150ms ease;
}

.table-data-row:last-child {
  border-bottom: none;
}

.table-data-row:hover:not(.is-inactive) {
  background: var(--color-surface);
}

.table-data-row.is-inactive {
  opacity: 0.55;
}

.zone-info {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.zone-name {
  font-size: 0.9375rem;
  font-weight: 700;
  color: var(--color-text);
}

.zone-bn {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
}

.inventory-tag {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-primary);
  font-variant-numeric: tabular-nums;
}

.coming-soon-tag {
  font-size: 0.75rem;
  color: var(--color-text-muted);
}

.table-view-btn {
  background: transparent;
  border: 1px solid var(--color-border);
  border-radius: 999px;
  padding: 6px 14px;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-primary);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.table-view-btn:not(:disabled):hover {
  background: var(--color-primary);
  color: #FFF;
  border-color: var(--color-primary);
}

.table-view-btn:disabled {
  cursor: not-allowed;
  color: var(--color-text-muted);
  border-color: transparent;
}

.dhaka-footer-note {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg, 12px);
  flex-wrap: wrap;
}

.note-pill {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-primary);
  border: 1px solid var(--color-primary);
  padding: 2px 8px;
  border-radius: 4px;
}

.note-text {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  margin: 0;
}
</style>
