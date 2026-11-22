<script setup>
/**
 * ForOperators Component (Editorial Minimalist Redesign)
 *
 * Requirements:
 * 1. Zero emojis. Replaced with pure inline SVGs (1.5px stroke, currentColor).
 * 2. Two-column editorial layout: Left = operator benefits, Right = neutral line-based dashboard mockup.
 * 3. Stable anchor ID: #operators (Navbar & Footer target).
 */
import { ref } from 'vue'
import SectionHeader from '../common/SectionHeader.vue'

const availableSlots = ref(14)
const totalSlots = ref(25)
const occupiedSlots = ref(11)

function adjustSlots(delta) {
  const newVal = availableSlots.value + delta
  if (newVal >= 0 && newVal <= totalSlots.value) {
    availableSlots.value = newVal
    occupiedSlots.value = totalSlots.value - newVal
  }
}
</script>

<template>
  <section id="operators" class="for-operators-section">
    <div class="operators-container">
      <div class="operators-grid">
        <!-- LEFT: Operator Value Proposition & CTAs -->
        <div class="operators-content">
          <SectionHeader
            eyebrow="Commercial Garages & Facility Owners"
            title="List Your Space, Update Slots & Manage Reservations"
            subtitle="Parkly gives parking owners and garage attendants full control over slot capacity, rates, and incoming bookings."
            :centered="false"
          />

          <div class="operator-features-list">
            <div class="op-feature-item">
              <span class="op-num">01</span>
              <div>
                <strong>Self-Service Slot Availability</strong>
                <p>Update your open bays in 2 clicks from any phone or computer with no expensive hardware required.</p>
              </div>
            </div>

            <div class="op-feature-item">
              <span class="op-num">02</span>
              <div>
                <strong>Transparent Vehicle Rates</strong>
                <p>Configure distinct hourly tariffs for Cars, SUVs, and Motorbikes with instant server calculation.</p>
              </div>
            </div>

            <div class="op-feature-item">
              <span class="op-num">03</span>
              <div>
                <strong>Booking Notifications & Verification</strong>
                <p>Verify customer PDF invoices by Booking ID at your entry gate before admitting vehicles.</p>
              </div>
            </div>

            <div class="op-feature-item">
              <span class="op-num">04</span>
              <div>
                <strong>Lifecycle Status Tracking</strong>
                <p>Mark drivers as Confirmed, In Progress, or Completed directly in your control console.</p>
              </div>
            </div>
          </div>

          <div class="operator-actions">
            <router-link to="/register" class="op-btn-primary">
              List Your Parking Facility
            </router-link>
            <router-link to="/login" class="op-btn-secondary">
              Admin & Operator Login
            </router-link>
          </div>
        </div>

        <!-- RIGHT: Stylized Operator Dashboard Mockup (Neutral Line-Based) -->
        <div class="operator-mock-wrapper">
          <div class="dashboard-mock-card">
            <div class="mock-header">
              <div class="mock-brand">
                <span class="mock-dot"></span>
                <strong>Parkly Operator Console</strong>
              </div>
              <span class="mock-badge">Preview</span>
            </div>

            <!-- Hub Details -->
            <div class="mock-hub-banner">
              <span class="hub-label">Active Garage</span>
              <h4 class="hub-title">Gulshan-2 Circular Rd Hub #04</h4>
              <span class="hub-status">Live & Accepting Passes</span>
            </div>

            <!-- Capacity Tiles -->
            <div class="capacity-tiles-grid">
              <div class="cap-tile">
                <span class="tile-label">Total Bays</span>
                <span class="tile-number">{{ totalSlots }}</span>
              </div>

              <div class="cap-tile tile-avail">
                <span class="tile-label">Reported Available</span>
                <span class="tile-number">{{ availableSlots }}</span>
              </div>

              <div class="cap-tile">
                <span class="tile-label">Occupied</span>
                <span class="tile-number">{{ occupiedSlots }}</span>
              </div>
            </div>

            <!-- Quick Slot Control -->
            <div class="slot-quick-control">
              <div class="control-text">
                <span class="control-title">Quick Availability Adjustment</span>
                <span class="control-sub">Report real-time bay status</span>
              </div>
              <div class="stepper-buttons">
                <button
                  class="step-btn"
                  :disabled="availableSlots <= 0"
                  aria-label="Decrease available slots"
                  @click="adjustSlots(-1)"
                >
                  -
                </button>
                <span class="slot-count-pill">{{ availableSlots }}</span>
                <button
                  class="step-btn"
                  :disabled="availableSlots >= totalSlots"
                  aria-label="Increase available slots"
                  @click="adjustSlots(1)"
                >
                  +
                </button>
              </div>
            </div>

            <!-- Simulated Incoming Booking Toast -->
            <div class="mock-toast-notification">
              <div class="toast-indicator-dot"></div>
              <div class="toast-body">
                <strong>New Booking #PKLY-8942</strong>
                <span>Car / 2 Hours • Fee 80 BDT • Status: Confirmed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.for-operators-section {
  width: 100%;
  padding: 120px 24px;
  background-color: var(--color-bg);
  border-top: 1px solid var(--color-border);
  scroll-margin-top: var(--nav-h, 72px);
}

.operators-container {
  max-width: 1200px;
  margin: 0 auto;
}

.operators-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
  align-items: center;
}

@media (min-width: 992px) {
  .operators-grid {
    grid-template-columns: 1.1fr 0.9fr;
  }
}

.operator-features-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin: 32px 0;
}

.op-feature-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--color-border);
}

.op-feature-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.op-num {
  font-family: var(--font-sans);
  font-size: 1.125rem;
  font-weight: 800;
  color: var(--color-primary);
  flex-shrink: 0;
}

.op-feature-item strong {
  display: block;
  font-size: 0.9375rem;
  color: var(--color-text);
  margin-bottom: 2px;
}

.op-feature-item p {
  font-size: 0.84375rem;
  color: var(--color-text-muted);
  line-height: 1.45;
  margin: 0;
}

.operator-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 14px;
  align-items: center;
}

.op-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary);
  color: var(--color-primary-contrast, #FFF);
  font-size: 0.875rem;
  font-weight: 600;
  padding: 12px 24px;
  border-radius: 999px;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.op-btn-primary:hover {
  background: var(--color-primary-hover);
}

.op-btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  color: var(--color-text);
  font-size: 0.875rem;
  font-weight: 600;
  padding: 12px 24px;
  border-radius: 999px;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.op-btn-secondary:hover {
  border-color: var(--color-primary);
}

/* Dashboard Mockup */
.operator-mock-wrapper {
  width: 100%;
}

.dashboard-mock-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.mock-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 14px;
}

.mock-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8125rem;
  color: var(--color-text);
}

.mock-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-primary);
}

.mock-badge {
  font-size: 0.625rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  padding: 2px 6px;
  border-radius: 4px;
}

.mock-hub-banner {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 14px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hub-label {
  font-size: 0.625rem;
  text-transform: uppercase;
  font-weight: 700;
  color: var(--color-primary);
}

.hub-title {
  font-size: 0.9375rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.hub-status {
  font-size: 0.6875rem;
  color: #16A34A;
  font-weight: 600;
}

.capacity-tiles-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.cap-tile {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 12px 8px;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cap-tile.tile-avail {
  border-color: var(--color-primary);
}

.tile-label {
  font-size: 0.625rem;
  color: var(--color-text-muted);
  font-weight: 600;
}

.tile-number {
  font-size: 1.25rem;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
  color: var(--color-text);
}

.tile-avail .tile-number {
  color: var(--color-primary);
}

.slot-quick-control {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  gap: 12px;
}

.control-text {
  display: flex;
  flex-direction: column;
}

.control-title {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--color-text);
}

.control-sub {
  font-size: 0.625rem;
  color: var(--color-text-muted);
}

.stepper-buttons {
  display: flex;
  align-items: center;
  gap: 8px;
}

.step-btn {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  color: var(--color-text);
  font-weight: 700;
  font-size: 0.9375rem;
  cursor: pointer;
}

.slot-count-pill {
  font-size: 0.875rem;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
  color: var(--color-primary);
  min-width: 20px;
  text-align: center;
}

.mock-toast-notification {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
}

.toast-indicator-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #16A34A;
  flex-shrink: 0;
}

.toast-body {
  display: flex;
  flex-direction: column;
  font-size: 0.6875rem;
}

.toast-body strong {
  color: var(--color-text);
}

.toast-body span {
  color: var(--color-text-muted);
}
</style>
