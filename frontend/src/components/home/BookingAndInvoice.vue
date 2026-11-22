<script setup>
/**
 * BookingAndInvoice Component (Editorial Minimalist Redesign)
 *
 * Requirements:
 * 1. Zero emojis. Replaced with pure inline SVGs (1.5px stroke, currentColor).
 * 2. Two-column editorial layout: Left = verification & gateway specs, Right = sample invoice mock.
 * 3. Lifecycle Status Stepper (Pending > Confirmed > In Progress > Completed > Cancelled).
 */
import SectionHeader from '../common/SectionHeader.vue'

const steps = [
  { id: 'pending', label: 'Pending', desc: 'Request submitted' },
  { id: 'confirmed', label: 'Confirmed', desc: 'Slot locked & paid' },
  { id: 'in_progress', label: 'In Progress', desc: 'Parked in facility' },
  { id: 'completed', label: 'Completed', desc: 'Trip finalized' },
  { id: 'cancelled', label: 'Cancelled', desc: 'Voided before entry' },
]

const currentStepId = 'confirmed'
</script>

<template>
  <section id="booking-invoice" class="booking-invoice-section">
    <div class="booking-container">
      <SectionHeader
        eyebrow="Verification & Checkout"
        title="Guaranteed Booking, Sandbox Demo & Staff Invoice"
        subtitle="Every reservation receives a verifiable digital PDF invoice with Booking ID to present to parking personnel."
      />

      <!-- Editorial 2-Column Row -->
      <div class="editorial-booking-grid">
        <!-- LEFT: 3 Process Rows -->
        <div class="process-rows-column">
          <!-- Item 1 -->
          <div class="process-row">
            <span class="process-num">01</span>
            <div class="process-body">
              <h3 class="process-title">Availability Validated</h3>
              <p class="process-desc">
                When you select your duration and vehicle type, the backend server verifies slot inventory before reserving.
              </p>
              <span class="process-tag">No Overbooking</span>
            </div>
          </div>

          <!-- Item 2 -->
          <div class="process-row">
            <span class="process-num">02</span>
            <div class="process-body">
              <h3 class="process-title">SSLCOMMERZ Sandbox Checkout</h3>
              <p class="process-desc">
                Integrated with SSLCOMMERZ demo environment. Test transactions safely without charging real money.
              </p>
              <div class="gateway-tags">
                <span class="gw-tag">bKash</span>
                <span class="gw-tag">Nagad</span>
                <span class="gw-tag">Rocket</span>
                <span class="gw-tag">Cards</span>
              </div>
            </div>
          </div>

          <!-- Item 3 -->
          <div class="process-row">
            <span class="process-num">03</span>
            <div class="process-body">
              <h3 class="process-title">Digital Invoice & Verification</h3>
              <p class="process-desc">
                Instant PDF invoice generated. Show the booking pass or QR to staff on arrival for entry clearance.
              </p>
              <span class="process-tag">Digital Verification Pass</span>
            </div>
          </div>
        </div>

        <!-- RIGHT: Clean Neutral Invoice Mockup -->
        <div class="invoice-mock-column">
          <div class="sample-invoice-mock" aria-label="Sample invoice preview">
            <div class="inv-head">
              <div class="inv-brand-row">
                <span class="inv-brand">PARKLY PASS</span>
                <span class="inv-badge">Sample Invoice</span>
              </div>
              <span class="inv-id">PKLY-2026-8941</span>
            </div>

            <div class="inv-body">
              <div class="inv-row">
                <span class="inv-label">Facility</span>
                <span class="inv-val">Gulshan-2 Circular Road Hub #04</span>
              </div>
              <div class="inv-row">
                <span class="inv-label">Vehicle Class</span>
                <span class="inv-val">Car / Sedan</span>
              </div>
              <div class="inv-row">
                <span class="inv-label">Reserved Duration</span>
                <span class="inv-val">2 Hours</span>
              </div>
              <div class="inv-row">
                <span class="inv-label">Reporting Status</span>
                <span class="inv-val status-confirmed">Confirmed & Locked</span>
              </div>

              <div class="inv-divider"></div>

              <div class="inv-row inv-total-row">
                <span>Calculated Fee</span>
                <strong>80.00 BDT</strong>
              </div>

              <div class="inv-footer-note">
                Show this digital invoice or Booking ID to the attendant at entry.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Lifecycle Stepper -->
      <div class="stepper-box">
        <h4 class="stepper-heading">Lifecycle Booking Statuses</h4>
        <div class="status-stepper-bar">
          <div
            v-for="(st, idx) in steps"
            :key="st.id"
            class="stepper-node"
            :class="{ 'is-active': st.id === currentStepId, 'is-complete': idx < 1 }"
          >
            <div class="node-circle">
              <span v-if="idx < 1">OK</span>
              <span v-else>{{ idx + 1 }}</span>
            </div>
            <div class="node-texts">
              <span class="node-label">{{ st.label }}</span>
              <span class="node-desc">{{ st.desc }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.booking-invoice-section {
  width: 100%;
  padding: 120px 24px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  scroll-margin-top: var(--nav-h, 72px);
}

.booking-container {
  max-width: 1200px;
  margin: 0 auto;
}

.editorial-booking-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
  margin-bottom: 64px;
}

@media (min-width: 992px) {
  .editorial-booking-grid {
    grid-template-columns: 1.1fr 0.9fr;
    align-items: center;
  }
}

.process-rows-column {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.process-row {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding-bottom: 24px;
  border-bottom: 1px solid var(--color-border);
}

.process-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.process-num {
  font-family: var(--font-sans);
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--color-primary);
}

.process-body {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.process-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.process-desc {
  font-size: 0.84375rem;
  color: var(--color-text-muted);
  line-height: 1.5;
  margin: 0;
}

.process-tag {
  align-self: flex-start;
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-primary);
  border: 1px solid var(--color-primary);
  padding: 2px 8px;
  border-radius: 4px;
  margin-top: 4px;
}

.gateway-tags {
  display: flex;
  gap: 8px;
  margin-top: 4px;
}

.gw-tag {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text);
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  padding: 2px 8px;
  border-radius: 4px;
}

/* Sample Invoice Mockup (Neutral Line-Based) */
.sample-invoice-mock {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.inv-head {
  display: flex;
  flex-direction: column;
  gap: 6px;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 16px;
}

.inv-brand-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.inv-brand {
  font-size: 0.8125rem;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: 0.05em;
}

.inv-badge {
  font-size: 0.625rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  padding: 2px 6px;
  border-radius: 4px;
}

.inv-id {
  font-family: monospace;
  font-size: 0.875rem;
  font-weight: 700;
  color: var(--color-text);
}

.inv-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.inv-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.8125rem;
}

.inv-label {
  color: var(--color-text-muted);
}

.inv-val {
  font-weight: 600;
  color: var(--color-text);
}

.status-confirmed {
  color: #16A34A;
}

.inv-divider {
  height: 1px;
  background: var(--color-border);
  margin: 6px 0;
}

.inv-total-row {
  font-size: 0.9375rem;
  color: var(--color-text);
}

.inv-total-row strong {
  font-size: 1.125rem;
  color: var(--color-primary);
}

.inv-footer-note {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
  line-height: 1.4;
  margin-top: 8px;
  text-align: center;
}

/* Stepper */
.stepper-box {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 28px;
}

.stepper-heading {
  font-size: 0.875rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0 0 20px;
  text-align: center;
}

.status-stepper-bar {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 16px;
}

.stepper-node {
  display: flex;
  align-items: center;
  gap: 12px;
}

.node-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1.5px solid var(--color-border);
  background: var(--color-surface);
  display: grid;
  place-items: center;
  font-size: 0.6875rem;
  font-weight: 700;
  color: var(--color-text-muted);
  flex-shrink: 0;
}

.stepper-node.is-active .node-circle {
  border-color: var(--color-primary);
  background: var(--color-primary);
  color: #FFF;
}

.node-texts {
  display: flex;
  flex-direction: column;
}

.node-label {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--color-text);
}

.node-desc {
  font-size: 0.6875rem;
  color: var(--color-text-muted);
}
</style>
