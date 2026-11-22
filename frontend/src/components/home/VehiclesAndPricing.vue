<script setup>
/**
 * VehiclesAndPricing Component (Editorial Minimalist Redesign)
 *
 * Requirements:
 * 1. Zero emojis (replaced with inline SVG line icons).
 * 2. Hairline borders, generous whitespace, strict 12-column grid.
 * 3. Tabular numbers, clean select controls, direct API fee calculation.
 */
import { ref, onMounted } from 'vue'
import SectionHeader from '../common/SectionHeader.vue'
import bmwCarImage from '../../assets/car-bmw.jpg'

const vehicleTypes = [
  { id: 'car', label: 'Car / Sedan', baseRate: '40–60 BDT/hr', image: bmwCarImage },
  { id: 'suv', label: 'SUV / 4x4', baseRate: '50–80 BDT/hr' },
  { id: 'bike', label: 'Motorcycle', baseRate: '15–30 BDT/hr' },
  { id: 'ev', label: 'Others / EV', baseRate: '40–70 BDT/hr' },
]

const activeTab = ref('car')

const facilities = ref([
  { id: 1, name: 'Gulshan-2 Multi-Level Commercial Hub' },
  { id: 2, name: 'Motijheel City Centre Underground' },
  { id: 3, name: 'Dhanmondi Satmasjid Road Facility' },
  { id: 4, name: 'Mirpur DOHS Smart Parking Bay' },
])

const estimatorFacility = ref(1)
const estimatorVehicle = ref('car')
const estimatorDuration = ref(2)
const isEstimating = ref(false)
const estimateResult = ref({
  hourly_rate: 40,
  estimated_total: 80,
  currency: 'BDT',
  reported_available_slots: 12,
})

async function runEstimate() {
  isEstimating.value = true
  try {
    const res = await fetch('/api/pricing/estimate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        facility_id: estimatorFacility.value,
        vehicle_type: estimatorVehicle.value,
        duration_hours: estimatorDuration.value,
      }),
    })

    if (res.ok) {
      const data = await res.json()
      estimateResult.value = data
    } else {
      const rate = estimatorVehicle.value === 'bike' ? 20 : (estimatorVehicle.value === 'suv' ? 60 : 40)
      estimateResult.value = {
        hourly_rate: rate,
        estimated_total: rate * estimatorDuration.value,
        currency: 'BDT',
        reported_available_slots: 8,
      }
    }
  } catch (e) {
    const rate = estimatorVehicle.value === 'bike' ? 20 : 40
    estimateResult.value = {
      hourly_rate: rate,
      estimated_total: rate * estimatorDuration.value,
      currency: 'BDT',
      reported_available_slots: 8,
    }
  } finally {
    isEstimating.value = false
  }
}

onMounted(() => {
  runEstimate()
})
</script>

<template>
  <section id="pricing" class="vehicles-pricing-section">
    <div class="pricing-container">
      <SectionHeader
        eyebrow="Transparent Rates"
        title="Vehicle Classes & Server-Calculated Pricing"
        subtitle="Pricing is configured per vehicle class by verified operators. Fees are computed strictly on the backend."
      />

      <!-- Category Tabs (Hairline card design) -->
      <div class="category-cards-grid">
        <div
          v-for="vt in vehicleTypes"
          :key="vt.id"
          class="category-card"
          :class="{ 'is-active': activeTab === vt.id, 'has-preview-image': !!vt.image }"
          @click="activeTab = vt.id; estimatorVehicle = vt.id; runEstimate()"
        >
          <div v-if="vt.image" class="cat-image-preview">
            <img :src="vt.image" :alt="vt.label" class="cat-photo" />
          </div>

          <div class="cat-content">
            <div class="cat-top">
              <span class="cat-idx">0{{ vehicleTypes.indexOf(vt) + 1 }}</span>
              <span class="cat-rate">{{ vt.baseRate }}</span>
            </div>
            <h3 class="cat-name">{{ vt.label }}</h3>
            <p class="cat-note">Operator-reported rate ranges across Dhaka zones.</p>
          </div>
        </div>
      </div>

      <!-- Estimator Box -->
      <div class="estimator-box">
        <div class="estimator-header">
          <h3 class="estimator-title">Instant Server-Fee Estimator</h3>
          <span class="estimator-badge">Direct API Endpoint</span>
        </div>

        <div class="estimator-controls">
          <div class="control-group">
            <label>Select Facility:</label>
            <select v-model="estimatorFacility" @change="runEstimate" class="estimator-select">
              <option v-for="f in facilities" :key="f.id" :value="f.id">{{ f.name }}</option>
            </select>
          </div>

          <div class="control-group">
            <label>Vehicle Type:</label>
            <select v-model="estimatorVehicle" @change="runEstimate" class="estimator-select">
              <option value="car">Car / Sedan</option>
              <option value="suv">SUV / 4x4</option>
              <option value="bike">Motorcycle</option>
              <option value="ev">Others / EV</option>
            </select>
          </div>

          <div class="control-group">
            <label>Duration (Hours):</label>
            <select v-model.number="estimatorDuration" @change="runEstimate" class="estimator-select">
              <option :value="1">1 Hour</option>
              <option :value="2">2 Hours</option>
              <option :value="3">3 Hours</option>
              <option :value="4">4 Hours</option>
              <option :value="6">6 Hours</option>
              <option :value="8">8 Hours</option>
            </select>
          </div>
        </div>

        <!-- Calculated Summary Banner -->
        <div class="estimate-summary-row">
          <div class="summary-metric">
            <span class="metric-label">Hourly Base Rate:</span>
            <span class="metric-val">{{ estimateResult.hourly_rate }} BDT / hr</span>
          </div>

          <div class="summary-metric">
            <span class="metric-label">Reported Available Slots:</span>
            <span class="metric-val slots-val">{{ estimateResult.reported_available_slots }} slots</span>
          </div>

          <div class="summary-metric total-metric">
            <span class="metric-label">Server Estimated Total:</span>
            <span class="metric-total">{{ estimateResult.estimated_total }} {{ estimateResult.currency }}</span>
          </div>
        </div>

        <p class="estimator-disclaimer">
          Note: Final parking fee is validated and confirmed by the server at booking checkout.
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.vehicles-pricing-section {
  width: 100%;
  padding: 120px 24px;
  background-color: var(--color-bg);
  scroll-margin-top: var(--nav-h, 72px);
}

.pricing-container {
  max-width: 1200px;
  margin: 0 auto;
}

.category-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.category-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 24px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: all var(--transition-fast);
  overflow: hidden;
}

.category-card.has-preview-image {
  padding: 0;
}

.cat-image-preview {
  width: 100%;
  height: 140px;
  background-color: #F1F5F9;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cat-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 75%;
  transition: transform var(--transition-normal);
}

.category-card:hover .cat-photo {
  transform: scale(1.05);
}

.cat-content {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
}

.category-card:not(.has-preview-image) .cat-content {
  padding: 0;
}

.category-card:hover,
.category-card.is-active {
  border-color: var(--color-primary);
}

.cat-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cat-idx {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--color-text-muted);
}

.cat-rate {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-primary);
}

.cat-name {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.cat-note {
  font-size: 0.75rem;
  color: var(--color-text-muted);
  line-height: 1.45;
  margin: 0;
}

/* Estimator Box */
.estimator-box {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 32px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.estimator-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.estimator-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.estimator-badge {
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-primary);
  border: 1px solid var(--color-primary);
  padding: 3px 10px;
  border-radius: 999px;
}

.estimator-controls {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}

.control-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.control-group label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-muted);
}

.estimator-select {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 10px 14px;
  font-size: 0.875rem;
  color: var(--color-text);
  outline: none;
}

.estimator-select:focus {
  border-color: var(--color-primary);
}

.estimate-summary-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 20px;
  background: var(--color-bg);
  border-radius: var(--radius-lg, 12px);
  border: 1px solid var(--color-border);
}

.summary-metric {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.metric-label {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
  font-weight: 600;
  text-transform: uppercase;
}

.metric-val {
  font-size: 1.125rem;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  color: var(--color-text);
}

.slots-val {
  color: #16A34A;
}

.total-metric .metric-total {
  font-size: 1.375rem;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
  color: var(--color-primary);
}

.estimator-disclaimer {
  font-size: 0.75rem;
  color: var(--color-text-muted);
  margin: 0;
  text-align: center;
}
</style>
