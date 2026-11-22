<script setup>
/**
 * ContactView Component
 * Route: /contact
 * Public support and problem report form for Parkly.
 * Communicates with POST /api/v1/support/contact
 */
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import AppButton from '../components/common/AppButton.vue'
import AppInput from '../components/common/AppInput.vue'

const route = useRoute()

const form = ref({
  name: '',
  email: '',
  phone: '',
  topic: 'general',
  facility_reference: '',
  message: '',
  website: '', // Honeypot
})

const isSubmitting = ref(false)
const submitSuccess = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

onMounted(() => {
  if (route.query.topic) {
    form.value.topic = String(route.query.topic)
  }
  if (route.query.facility) {
    form.value.facility_reference = String(route.query.facility)
  }
})

async function handleSubmit() {
  errorMessage.value = ''
  submitSuccess.value = false
  isSubmitting.value = true

  try {
    const res = await axios.post('/api/v1/support/contact', form.value)
    if (res.data?.success) {
      submitSuccess.value = true
      successMessage.value = res.data.message || 'Thank you! Your message has been received.'
      form.value = {
        name: '',
        email: '',
        phone: '',
        topic: 'general',
        facility_reference: '',
        message: '',
        website: '',
      }
    }
  } catch (err) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = 'Failed to submit message. Please check your internet connection or email directly.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="contact-page">
    <header class="contact-header">
      <div class="contact-container header-inner">
        <div class="eyebrow-pill">
          <span class="eyebrow-dot" aria-hidden="true"></span>
          <span>Support & Assistance</span>
        </div>
        <h1 class="header-title">Contact Parkly Support</h1>
        <p class="header-lead">
          Have a question about a booking, need help finding a facility in Dhaka, or noticed a discrepancy in reported availability or pricing? Send us a message below.
        </p>
      </div>
    </header>

    <main class="contact-container main-grid">
      <!-- Contact Info Cards -->
      <aside class="info-sidebar">
        <div class="info-card">
          <h2 class="card-title">Official Desk</h2>
          <div class="info-item">
            <span class="info-label">Email Support</span>
            <a href="mailto:support@parkly.com.bd" class="info-val link">support@parkly.com.bd</a>
          </div>
          <div class="info-item">
            <span class="info-label">Helpline</span>
            <span class="info-val">+880 1700-000000</span>
          </div>
          <div class="info-item">
            <span class="info-label">Operating Hours</span>
            <span class="info-val">Sun – Thu, 9:00 AM – 6:00 PM BST</span>
          </div>
          <div class="info-item">
            <span class="info-label">Coverage Region</span>
            <span class="info-val">Dhaka Metropolitan Area (DNCC & DSCC)</span>
          </div>
        </div>

        <div class="info-card highlight-card">
          <h3 class="card-title">Facility Price or Slot Problem?</h3>
          <p class="card-text">
            If a parking operator charged higher than the server-calculated rate or had fewer slots than reported, select "Report a Facility Problem" in the form. Our administrative desk will audit the logs.
          </p>
        </div>
      </aside>

      <!-- Contact Form -->
      <section class="form-wrapper" aria-labelledby="form-heading">
        <h2 id="form-heading" class="form-title">Send a Message</h2>

        <!-- Alert messages with aria-live -->
        <div v-if="submitSuccess" class="alert-box success-alert" role="status" aria-live="polite">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <div>
            <strong>Message Sent Successfully!</strong>
            <p>{{ successMessage }}</p>
          </div>
        </div>

        <div v-if="errorMessage" class="alert-box error-alert" role="alert" aria-live="assertive">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div>
            <strong>Submission Error</strong>
            <p>{{ errorMessage }}</p>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="contact-form" novalidate>
          <!-- Honeypot -->
          <div class="sr-only" aria-hidden="true" style="display: none;">
            <label for="c-website">Leave this field blank</label>
            <input id="c-website" type="text" v-model="form.website" tabindex="-1" autocomplete="off" />
          </div>

          <div class="form-row">
            <AppInput
              id="c-name"
              label="Your Full Name"
              placeholder="e.g. Tanvir Hossain"
              v-model="form.name"
              required
            />

            <AppInput
              id="c-email"
              type="email"
              label="Email Address"
              placeholder="you@example.com"
              v-model="form.email"
              required
            />
          </div>

          <div class="form-row">
            <AppInput
              id="c-phone"
              type="tel"
              label="Phone Number (Optional)"
              placeholder="+880 17XX-XXXXXX"
              v-model="form.phone"
            />

            <div class="form-group">
              <label for="c-topic" class="input-label">Inquiry Topic <span class="required">*</span></label>
              <select id="c-topic" v-model="form.topic" class="select-field" required>
                <option value="general">General Inquiry</option>
                <option value="report_discrepancy">Report Facility Availability Discrepancy</option>
                <option value="pricing_issue">Report Incorrect Pricing / Overcharge</option>
                <option value="booking_help">Booking Assistance & Digital Invoice</option>
                <option value="other">Other Inquiry</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="c-facility" class="input-label">
              Facility Name or Booking Invoice # (If applicable)
            </label>
            <input
              id="c-facility"
              type="text"
              v-model="form.facility_reference"
              placeholder="e.g. Gulshan-2 Smart Zone or INV-2026-XXXX"
              class="text-input"
            />
          </div>

          <div class="form-group">
            <label for="c-message" class="input-label">
              Detailed Message <span class="required">*</span>
            </label>
            <textarea
              id="c-message"
              v-model="form.message"
              rows="5"
              placeholder="Please describe what happened or how we can assist you..."
              class="textarea-field"
              required
            ></textarea>
          </div>

          <div class="form-actions">
            <AppButton
              variant="primary"
              size="lg"
              pill
              type="submit"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Sending Message...' : 'Submit Message' }}
            </AppButton>
          </div>
        </form>
      </section>
    </main>
  </div>
</template>

<style scoped>
.contact-page {
  width: 100%;
  min-height: 100vh;
  background-color: var(--color-bg);
  color: var(--color-text);
  padding-bottom: 80px;
}

.contact-header {
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  padding: 60px 24px 44px;
}

.contact-container {
  max-width: 1140px;
  margin: 0 auto;
}

.header-inner {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.eyebrow-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  align-self: flex-start;
  padding: 4px 12px;
  border-radius: var(--radius-pill, 999px);
  background-color: var(--color-primary-light);
  color: var(--color-primary);
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: currentColor;
}

.header-title {
  font-size: clamp(2rem, 3.5vw, 2.75rem);
  font-weight: 700;
  letter-spacing: -0.025em;
  color: var(--color-text);
  margin: 0;
}

.header-lead {
  font-size: 1.0625rem;
  line-height: 1.6;
  color: var(--color-text-muted);
  max-width: 760px;
  margin: 0;
}

.main-grid {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 40px;
  margin-top: 48px;
  padding: 0 24px;
}

.info-sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-card {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.highlight-card {
  background-color: rgba(15, 118, 110, 0.05);
  border-color: rgba(15, 118, 110, 0.2);
}

.card-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0;
}

.card-text {
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--color-text-muted);
  margin: 0;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.info-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--color-text-subtle);
}

.info-val {
  font-size: 0.9375rem;
  color: var(--color-text);
}

.info-val.link {
  color: var(--color-primary);
  text-decoration: none;
}

.info-val.link:hover {
  text-decoration: underline;
}

.form-wrapper {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 32px;
}

.form-title {
  font-size: 1.35rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0 0 24px;
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.input-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-text);
}

.required {
  color: var(--color-danger);
}

.select-field,
.text-input,
.textarea-field {
  width: 100%;
  padding: 10px 14px;
  background-color: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  color: var(--color-text);
  font-size: 0.9375rem;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.select-field:focus,
.text-input:focus,
.textarea-field:focus {
  outline: none;
  border-color: var(--color-border-focus);
  box-shadow: 0 0 0 3px var(--color-ring);
}

.textarea-field {
  resize: vertical;
  min-height: 120px;
}

.form-actions {
  margin-top: 8px;
}

.alert-box {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 18px;
  border-radius: var(--radius-md, 8px);
  margin-bottom: 24px;
  font-size: 0.9rem;
}

.alert-box p {
  margin: 4px 0 0;
  font-size: 0.85rem;
}

.success-alert {
  background-color: rgba(22, 163, 74, 0.1);
  border: 1px solid rgba(22, 163, 74, 0.3);
  color: var(--color-success);
}

.error-alert {
  background-color: var(--color-danger-bg);
  border: 1px solid var(--color-danger-border);
  color: var(--color-danger);
}

@media (max-width: 860px) {
  .main-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
