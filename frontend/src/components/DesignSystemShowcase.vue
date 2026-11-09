<script setup>
/**
 * DesignSystemShowcase Component
 * Displays and tests all Parkly design system elements:
 * - Brand typography & colors
 * - AppButton variants and sizes
 * - AppInput states (normal, with prefix/suffix, error, hint, disabled)
 * - Theme toggle demonstration
 */
import { ref } from 'vue'
import AppButton from './common/AppButton.vue'
import AppInput from './common/AppInput.vue'
import ThemeToggle from './common/ThemeToggle.vue'
import { useTheme } from '../composables/useTheme'

const { currentTheme } = useTheme()

const sampleSearch = ref('')
const sampleEmail = ref('')
const samplePassword = ref('')
const sampleError = ref('Invalid license plate number')
const isSubmitting = ref(false)

function triggerLoading() {
  isSubmitting.value = true
  setTimeout(() => {
    isSubmitting.value = false
  }, 1500)
}
</script>

<template>
  <main class="showcase-container">
    <!-- Header Banner -->
    <header class="showcase-header">
      <div class="badge">Design System Preview</div>
      <h1 class="showcase-title">Parkly Shared UI Foundation</h1>
      <p class="showcase-subtitle">
        Find. Book. Park. — Modern minimal token-driven components supporting Light & Dark themes.
      </p>
      <div class="active-theme-pill">
        Current Theme: <strong>{{ currentTheme }}</strong>
      </div>
    </header>

    <!-- Palette Section -->
    <section class="showcase-card">
      <h2 class="section-title">1. Agreed Brand Color Tokens</h2>
      <p class="section-desc">Tokens adapt automatically to light and dark themes via CSS variables.</p>
      <div class="palette-grid">
        <div class="color-chip" style="background-color: var(--color-primary); color: var(--color-primary-contrast)">
          <div class="chip-label">Primary</div>
          <div class="chip-value">var(--color-primary)</div>
        </div>
        <div class="color-chip" style="background-color: var(--color-bg); color: var(--color-text); border: 1px solid var(--color-border)">
          <div class="chip-label">Background</div>
          <div class="chip-value">var(--color-bg)</div>
        </div>
        <div class="color-chip" style="background-color: var(--color-surface); color: var(--color-text); border: 1px solid var(--color-border)">
          <div class="chip-label">Surface</div>
          <div class="chip-value">var(--color-surface)</div>
        </div>
        <div class="color-chip" style="background-color: var(--color-border); color: var(--color-text)">
          <div class="chip-label">Border</div>
          <div class="chip-value">var(--color-border)</div>
        </div>
      </div>
    </section>

    <!-- Buttons Section -->
    <section class="showcase-card">
      <h2 class="section-title">2. AppButton Variants & Sizes</h2>
      <p class="section-desc">Accessible, interactive buttons with hover, active, focus, and loading states.</p>

      <div class="demo-group">
        <h3 class="group-title">Variants</h3>
        <div class="button-row">
          <AppButton variant="primary">Primary</AppButton>
          <AppButton variant="secondary">Secondary</AppButton>
          <AppButton variant="outline">Outline</AppButton>
          <AppButton variant="ghost">Ghost</AppButton>
          <AppButton variant="danger">Danger</AppButton>
        </div>
      </div>

      <div class="demo-group">
        <h3 class="group-title">Sizes</h3>
        <div class="button-row">
          <AppButton size="sm">Small (32px)</AppButton>
          <AppButton size="md">Medium (40px)</AppButton>
          <AppButton size="lg">Large (48px)</AppButton>
        </div>
      </div>

      <div class="demo-group">
        <h3 class="group-title">States & Actions</h3>
        <div class="button-row">
          <AppButton :loading="isSubmitting" @click="triggerLoading">
            {{ isSubmitting ? 'Processing...' : 'Click for Loading State' }}
          </AppButton>
          <AppButton disabled>Disabled Button</AppButton>
          <AppButton variant="outline">
            <template #prefix>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </template>
            With Icon Prefix
          </AppButton>
        </div>
      </div>
    </section>

    <!-- Inputs Section -->
    <section class="showcase-card">
      <h2 class="section-title">3. AppInput States</h2>
      <p class="section-desc">Consistent inputs with floating helper text, accessible error state, and icon slots.</p>

      <div class="input-grid">
        <AppInput
          v-model="sampleSearch"
          label="Search Parking Spots"
          placeholder="e.g. Downtown Central Garage"
          hint="Search by neighborhood, street name or landmark"
        >
          <template #prefix>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </template>
        </AppInput>

        <AppInput
          v-model="sampleEmail"
          label="Email Address"
          type="email"
          placeholder="driver@parkly.com"
          required
        />

        <AppInput
          v-model="samplePassword"
          label="Vehicle License Plate"
          placeholder="e.g. DHA-11-2024"
          :error="sampleError"
        />

        <AppInput
          label="Disabled Field"
          modelValue="Permanent Resident Permit"
          disabled
          hint="This field cannot be edited"
        />
      </div>
    </section>

    <!-- Theme Toggle Section -->
    <section class="showcase-card">
      <h2 class="section-title">4. Theme Switcher</h2>
      <p class="section-desc">Try toggling between light and dark modes here or from the top navbar.</p>
      <div class="toggle-demo-row">
        <ThemeToggle />
        <span class="toggle-label">Click icon to switch theme (persists in localStorage)</span>
      </div>
    </section>
  </main>
</template>

<style scoped>
.showcase-container {
  max-width: var(--container-max-width);
  margin: 0 auto;
  padding: var(--space-8) var(--space-4) var(--space-12);
  display: flex;
  flex-direction: column;
  gap: var(--space-8);
  width: 100%;
}

.showcase-header {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-6) 0;
}

.badge {
  display: inline-flex;
  align-items: center;
  padding: var(--space-1) var(--space-3);
  background-color: var(--color-primary-light);
  color: var(--color-primary);
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-semibold);
  border-radius: var(--radius-full);
}

.showcase-title {
  font-size: var(--font-size-4xl);
  color: var(--color-text);
  margin: 0;
}

.showcase-subtitle {
  font-size: var(--font-size-lg);
  color: var(--color-text-muted);
  max-width: 650px;
  margin: 0;
}

.active-theme-pill {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  padding: var(--space-1) var(--space-3);
  border-radius: var(--radius-full);
  text-transform: capitalize;
}

.showcase-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  box-shadow: var(--shadow-sm);
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal);
}

.section-title {
  font-size: var(--font-size-xl);
  color: var(--color-text);
  margin: 0;
}

.section-desc {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
  margin: 0;
}

/* Palette */
.palette-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: var(--space-3);
  margin-top: var(--space-2);
}

.color-chip {
  padding: var(--space-4);
  border-radius: var(--radius-md);
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.chip-label {
  font-weight: var(--font-weight-semibold);
  font-size: var(--font-size-sm);
}

.chip-value {
  font-size: var(--font-size-xs);
  opacity: 0.85;
  font-family: var(--font-mono);
}

/* Buttons */
.demo-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.group-title {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-text-subtle);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0;
}

.button-row {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
  align-items: center;
}

/* Inputs */
.input-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--space-4);
}

/* Toggle */
.toggle-demo-row {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.toggle-label {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}
</style>

