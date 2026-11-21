<script setup>
/**
 * SearchWidget Component — Mastercard-Inspired Elevated Pill Search Bar
 * Includes:
 * - Location input with clear/focus
 * - GPS / Current Location button
 * - Vehicle type selector (Car, SUV, Bike, EV)
 * - Parking duration selector (1h, 2h, 3h, 4h, Full Day)
 * - Find Parking button
 * - Secondary "Ask Parkly AI" natural language mode with prompt suggestions
 */
import { ref } from 'vue'
import AppButton from '../common/AppButton.vue'

const emit = defineEmits(['search', 'aiSearch'])

const location = ref('')
const selectedVehicle = ref('car')
const selectedDuration = ref('2')
const isAiMode = ref(false)
const aiQuery = ref('')
const isDetectingGps = ref(false)

const vehicleOptions = [
  { id: 'car', label: 'Car / Sedan', icon: '' },
  { id: 'suv', label: 'SUV / 4x4', icon: '' },
  { id: 'bike', label: 'Motorbike', icon: '' },
  { id: 'ev', label: 'EV (Charger)', icon: '' },
]

const durationOptions = [
  { id: '1', label: '1 Hour' },
  { id: '2', label: '2 Hours' },
  { id: '3', label: '3 Hours' },
  { id: '4', label: '4 Hours' },
  { id: 'day', label: 'Full Day' },
]

const sampleAiPrompts = [
  'Find affordable parking in Mirpur for my SUV for 3 hours',
  'Covered parking near Gulshan-2 with EV charging',
  'Cheapest 2-hour spot around Dhanmondi 27',
  'Secure motorbike parking near Motijheel Commercial Area',
]

function handleGpsClick() {
  isDetectingGps.value = true
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        isDetectingGps.value = false
        location.value = 'Current Location (Gulshan-1, Dhaka)'
      },
      () => {
        isDetectingGps.value = false
        location.value = 'Gulshan, Dhaka'
      },
      { timeout: 5000 }
    )
  } else {
    isDetectingGps.value = false
    location.value = 'Gulshan, Dhaka'
  }
}

function handleSearch() {
  emit('search', {
    location: location.value || 'Dhaka Central',
    vehicle: selectedVehicle.value,
    duration: selectedDuration.value,
  })
}

function handleAiSubmit() {
  if (!aiQuery.value.trim()) return
  emit('aiSearch', aiQuery.value)
}

function pickAiPrompt(prompt) {
  aiQuery.value = prompt
  handleAiSubmit()
}
</script>

<template>
  <section class="search-widget-section" id="discovery">
    <div class="search-widget-container">
      <!-- Search Mode Toggle Pill -->
      <div class="search-mode-switcher">
        <button
          type="button"
          class="mode-btn"
          :class="{ 'is-active': !isAiMode }"
          @click="isAiMode = false"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
          Direct Search
        </button>

        <button
          type="button"
          class="mode-btn ai-btn"
          :class="{ 'is-active': isAiMode }"
          @click="isAiMode = true"
        >
          <span class="ai-sparkle-dot"></span>
          Ask Parkly AI
        </button>
      </div>

      <!-- Mode 1: Standard Elevated Pill Search Widget -->
      <div v-if="!isAiMode" class="search-card-pill">
        <!-- Input 1: Destination Location -->
        <div class="search-field field-location">
          <label for="search-location" class="field-label">DESTINATION / AREA</label>
          <div class="field-input-row">
            <svg class="field-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <input
              id="search-location"
              v-model="location"
              type="text"
              placeholder="e.g. Gulshan, Mirpur, Dhanmondi..."
              class="pill-input"
              @keyup.enter="handleSearch"
            />
            <button
              type="button"
              class="gps-pill-btn"
              :class="{ 'is-loading': isDetectingGps }"
              title="Use Current Location (GPS)"
              aria-label="Use Current Location (GPS)"
              @click="handleGpsClick"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3" />
                <path d="M12 2v3m0 14v3M2 12h3m14 0h3" />
              </svg>
              <span class="gps-text">GPS</span>
            </button>
          </div>
        </div>

        <div class="field-divider" aria-hidden="true"></div>

        <!-- Input 2: Vehicle Type Selector -->
        <div class="search-field field-vehicle">
          <label for="search-vehicle" class="field-label">VEHICLE TYPE</label>
          <div class="field-input-row">
            <select id="search-vehicle" v-model="selectedVehicle" class="pill-select">
              <option v-for="veh in vehicleOptions" :key="veh.id" :value="veh.id">
                {{ veh.icon }} {{ veh.label }}
              </option>
            </select>
          </div>
        </div>

        <div class="field-divider" aria-hidden="true"></div>

        <!-- Input 3: Duration Selector -->
        <div class="search-field field-duration">
          <label for="search-duration" class="field-label">PARKING DURATION</label>
          <div class="field-input-row">
            <select id="search-duration" v-model="selectedDuration" class="pill-select">
              <option v-for="dur in durationOptions" :key="dur.id" :value="dur.id">
                {{ dur.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Submit CTA (Mastercard Pill radius) -->
        <div class="search-submit-col">
          <AppButton
            variant="primary"
            size="lg"
            pill
            class="search-submit-btn"
            @click="handleSearch"
          >
            Find Parking
            <template #suffix>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </template>
          </AppButton>
        </div>
      </div>

      <!-- Mode 2: AI Natural Language Search -->
      <div v-else class="search-ai-card">
        <div class="ai-input-wrapper">
          <span class="ai-sparkle-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
            </svg>
          </span>
          <input
            v-model="aiQuery"
            type="text"
            placeholder="Ask anything: 'Find affordable parking in Mirpur for my SUV for 3 hours...'"
            class="ai-input-control"
            @keyup.enter="handleAiSubmit"
          />
          <AppButton
            variant="primary"
            size="md"
            pill
            class="ai-submit-btn"
            @click="handleAiSubmit"
          >
            Search
          </AppButton>
        </div>

        <!-- Sample AI Prompts Chips -->
        <div class="ai-suggestions-row">
          <span class="suggestions-label">Try asking:</span>
          <div class="suggestions-chips">
            <button
              v-for="(prompt, index) in sampleAiPrompts"
              :key="index"
              type="button"
              class="suggestion-chip"
              @click="pickAiPrompt(prompt)"
            >
              "{{ prompt }}"
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.search-widget-section {
  position: relative;
  z-index: 10;
  width: 100%;
  padding: 0 24px 64px;
}

.search-widget-container {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* Mode Switcher */
.search-mode-switcher {
  display: inline-flex;
  align-items: center;
  align-self: flex-start;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 4px;
  box-shadow: var(--shadow-sm);
  gap: 4px;
}

.mode-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 16px;
  border-radius: var(--radius-pill, 999px);
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  font-size: 0.8125rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.mode-btn.is-active {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  box-shadow: var(--shadow-sm);
}

.ai-sparkle-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
  display: inline-block;
}

.mode-btn.is-active .ai-sparkle-dot {
  background-color: #FFFFFF;
}

/* Primary Search Card Pill */
.search-card-pill {
  background-color: var(--color-surface);
  border: 1.5px solid var(--color-border);
  border-radius: 36px;
  padding: 12px 14px 12px 28px;
  display: flex;
  align-items: center;
  box-shadow: var(--shadow-halo);
  gap: 20px;
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal),
    box-shadow var(--transition-normal);
}

.search-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.field-location {
  flex: 1.6;
}

.field-label {
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: var(--color-text-muted);
  text-transform: uppercase;
}

.field-input-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.field-icon {
  color: var(--color-primary);
  flex-shrink: 0;
}

.pill-input {
  width: 100%;
  border: none;
  background: transparent;
  color: var(--color-text);
  font-size: 0.9375rem;
  font-weight: 500;
  outline: none;
}

.pill-input::placeholder {
  color: var(--color-text-subtle);
  font-weight: 400;
}

.gps-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: var(--radius-pill, 999px);
  border: 1px solid var(--color-border);
  background-color: var(--color-surface-subtle);
  color: var(--color-primary);
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  flex-shrink: 0;
  transition: all var(--transition-fast);
}

.gps-pill-btn:hover {
  border-color: var(--color-primary);
  background-color: var(--color-primary-light);
}

.field-divider {
  width: 1px;
  height: 40px;
  background-color: var(--color-border);
}

.pill-select {
  width: 100%;
  border: none;
  background: transparent;
  color: var(--color-text);
  font-size: 0.9375rem;
  font-weight: 500;
  outline: none;
  cursor: pointer;
}

.search-submit-col {
  flex-shrink: 0;
}

.search-submit-btn {
  padding: 0 28px !important;
  height: 52px !important;
  font-weight: 600;
  font-size: 0.9375rem;
}

/* AI Mode */
.search-ai-card {
  background-color: var(--color-surface);
  border: 1.5px solid var(--color-border);
  border-radius: 28px;
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: var(--shadow-halo);
}

.ai-input-wrapper {
  display: flex;
  align-items: center;
  gap: 14px;
  background-color: var(--color-surface-subtle);
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 6px 8px 6px 18px;
  transition: border-color var(--transition-fast);
}

.ai-input-wrapper:focus-within {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-ring);
}

.ai-sparkle-icon {
  color: var(--color-primary);
  display: flex;
  align-items: center;
}

.ai-input-control {
  flex: 1;
  border: none;
  background: transparent;
  color: var(--color-text);
  font-size: 0.9375rem;
  outline: none;
}

.ai-input-control::placeholder {
  color: var(--color-text-subtle);
}

.ai-submit-btn {
  padding: 0 24px !important;
  height: 42px !important;
}

.ai-suggestions-row {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.suggestions-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  flex-shrink: 0;
}

.suggestions-chips {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.suggestion-chip {
  background-color: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 4px 12px;
  font-size: 0.78125rem;
  color: var(--color-text-muted);
  cursor: pointer;
  text-align: left;
  transition: all var(--transition-fast);
}

.suggestion-chip:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  background-color: var(--color-surface);
}

/* Breakpoints */
@media (max-width: 900px) {
  .search-card-pill {
    flex-direction: column;
    align-items: stretch;
    border-radius: 28px;
    padding: 20px;
    gap: 16px;
  }

  .field-divider {
    width: 100%;
    height: 1px;
  }

  .search-submit-btn {
    width: 100% !important;
  }
}
</style>

