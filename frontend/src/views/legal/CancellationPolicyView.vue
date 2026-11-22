<script setup>
/**
 * CancellationPolicyView Component
 * Route: /cancellation-policy
 * Clear, proposal-grounded cancellation and refund guidelines for Parkly.
 * Draft, subject to legal review.
 */
import { ref, onMounted, onUnmounted } from 'vue'

const lastUpdated = 'November 23, 2026'

const sections = [
  { id: 'overview', title: '1. Overview & Prototype Scope' },
  { id: 'cancellation-rules', title: '2. Cancellation Eligibility' },
  { id: 'refund-schedule', title: '3. Refund Schedule & Processing' },
  { id: 'operator-discrepancy', title: '4. Discrepancies & Denied Entry' },
  { id: 'procedure', title: '5. How to Request Cancellation' },
  { id: 'support', title: '6. Support & Contact' },
]

const activeSection = ref(sections[0].id)

function handleScroll() {
  const scrollPos = window.scrollY + 140
  for (let i = sections.length - 1; i >= 0; i--) {
    const el = document.getElementById(sections[i].id)
    if (el && el.offsetTop <= scrollPos) {
      activeSection.value = sections[i].id
      break
    }
  }
}

function scrollToSection(id) {
  const el = document.getElementById(id)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    activeSection.value = id
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <div class="legal-page">
    <header class="legal-header">
      <div class="legal-container header-inner">
        <div class="draft-badge" role="note">
          <span class="badge-dot" aria-hidden="true"></span>
          <span>Draft, subject to legal review</span>
        </div>
        <h1 class="header-title">Cancellation & Refund Policy</h1>
        <p class="header-lead">
          Parkly connects drivers with registered parking facilities across Dhaka. This policy details cancellation windows, refund processing, and discrepancy resolution.
        </p>
        <div class="header-meta">
          <span class="meta-item">Effective: {{ lastUpdated }}</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/terms" class="meta-link">Terms of Service</router-link>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/privacy" class="meta-link">Privacy Policy</router-link>
        </div>
      </div>
    </header>

    <main class="legal-container main-layout">
      <aside class="sidebar-col">
        <nav class="sticky-nav" aria-label="Policy sections">
          <p class="nav-heading">SECTIONS</p>
          <ul class="nav-list">
            <li v-for="sec in sections" :key="sec.id">
              <button
                class="nav-item-btn"
                :class="{ active: activeSection === sec.id }"
                @click="scrollToSection(sec.id)"
              >
                {{ sec.title }}
              </button>
            </li>
          </ul>

          <div class="sidebar-card">
            <h4 class="card-title">Need help with a booking?</h4>
            <p class="card-text">Contact our Dhaka-based support team with your digital invoice reference number.</p>
            <router-link to="/contact?topic=booking_help" class="sidebar-btn">Contact Support</router-link>
          </div>
        </nav>
      </aside>

      <article class="content-col">
        <section id="overview" class="content-section">
          <h2>1. Overview & Prototype Scope</h2>
          <p>
            Parkly operates as a parking discovery, fee calculation, and reservation management platform. Parking facility availability is reported by authorized operators and administrators, and staff verify booking confirmations at entry.
          </p>
          <p>
            During the prototype release, all payments are conducted in sandbox mode. No live funds are deducted from bank accounts or mobile financial services (bKash/Nagad) during prototype testing.
          </p>
        </section>

        <section id="cancellation-rules" class="content-section">
          <h2>2. Cancellation Eligibility</h2>
          <p>
            Users who reserve a parking spot through Parkly may cancel their reservation under the following conditions:
          </p>
          <ul>
            <li><strong>Advance Cancellation:</strong> Cancellations made at least 60 minutes prior to the booked arrival window qualify for full restoration of recorded availability and a full refund note.</li>
            <li><strong>Short-Notice Cancellation:</strong> Cancellations requested less than 60 minutes before the arrival window may be subject to a nominal administrative reservation hold fee.</li>
            <li><strong>Post-Entry Window:</strong> Once the reserved arrival window has elapsed and the facility has held the spot, cancellations are no longer eligible for automated refund.</li>
          </ul>
        </section>

        <section id="refund-schedule" class="content-section">
          <h2>3. Refund Schedule & Processing</h2>
          <p>
            When a cancellation is confirmed:
          </p>
          <ul>
            <li>The recorded facility capacity in the database is automatically restored so other drivers can discover the available slot.</li>
            <li>In the production release, refunds will be credited back to the original funding source (SSLCOMMERZ / card / MFS) within 5–7 business days, per Bangladesh Bank digital commerce guidelines.</li>
            <li>In the active sandbox/prototype mode, refund acknowledgments are processed immediately in the invoice status record.</li>
          </ul>
        </section>

        <section id="operator-discrepancy" class="content-section">
          <h2>4. Discrepancies & Denied Entry</h2>
          <p>
            If you arrive at a booked facility with a valid digital invoice and entry is denied due to unexpected facility closure or full capacity:
          </p>
          <ol>
            <li>Present your digital invoice reference to our support desk immediately via the <router-link to="/contact?topic=report_discrepancy" class="inline-link">Report a Problem</router-link> form.</li>
            <li>Parkly will flag the facility's reported availability for immediate administrative review.</li>
            <li>You will receive a 100% full refund and an alternate nearby parking recommendation.</li>
          </ol>
        </section>

        <section id="procedure" class="content-section">
          <h2>5. How to Request Cancellation</h2>
          <p>
            To cancel a confirmed parking reservation:
          </p>
          <ul>
            <li>Navigate to your user profile / booking history screen if logged in, select the active booking, and click "Cancel Reservation".</li>
            <li>If booking as a guest, submit a cancellation request via the <router-link to="/contact" class="inline-link">Support Contact Form</router-link> providing your invoice number and phone number.</li>
          </ul>
        </section>

        <section id="support" class="content-section">
          <h2>6. Support & Contact</h2>
          <p>
            For any queries regarding cancellations, disputed charges, or facility reports, please reach out through our official support channels:
          </p>
          <div class="contact-box">
            <p><strong>Support Email:</strong> support@parkly.com.bd</p>
            <p><strong>Support Phone:</strong> +880 1700-000000 (Sun – Thu, 9 AM – 6 PM BST)</p>
            <p><strong>Online Form:</strong> <router-link to="/contact" class="inline-link">Submit a Support Ticket</router-link></p>
          </div>
        </section>
      </article>
    </main>
  </div>
</template>

<style scoped>
.legal-page {
  width: 100%;
  min-height: 100vh;
  background-color: var(--color-bg);
  color: var(--color-text);
  padding-bottom: 80px;
}

.legal-header {
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  padding: 60px 24px 44px;
}

.legal-container {
  max-width: 1140px;
  margin: 0 auto;
}

.header-inner {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.draft-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  align-self: flex-start;
  padding: 4px 12px;
  border-radius: var(--radius-pill, 999px);
  background-color: rgba(234, 88, 12, 0.12);
  color: #ea580c;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.badge-dot {
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

.header-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.8125rem;
  color: var(--color-text-subtle);
  flex-wrap: wrap;
}

.meta-link {
  color: var(--color-primary);
  text-decoration: none;
}

.meta-link:hover {
  text-decoration: underline;
}

.main-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 48px;
  margin-top: 48px;
  padding: 0 24px;
}

.sidebar-col {
  position: relative;
}

.sticky-nav {
  position: sticky;
  top: 120px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.nav-heading {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--color-text-subtle);
  margin: 0;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item-btn {
  text-align: left;
  background: transparent;
  border: none;
  padding: 8px 12px;
  border-radius: var(--radius-sm, 6px);
  color: var(--color-text-muted);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition-fast);
  width: 100%;
}

.nav-item-btn:hover {
  background-color: var(--color-surface);
  color: var(--color-primary);
}

.nav-item-btn.active {
  background-color: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: 600;
}

.sidebar-card {
  margin-top: 16px;
  padding: 16px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg, 12px);
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.card-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0;
}

.card-text {
  font-size: 0.78125rem;
  color: var(--color-text-muted);
  line-height: 1.5;
  margin: 0;
}

.sidebar-btn {
  display: inline-block;
  margin-top: 6px;
  padding: 8px 12px;
  text-align: center;
  background-color: var(--color-primary);
  color: var(--color-primary-contrast);
  border-radius: var(--radius-pill, 999px);
  font-size: 0.8125rem;
  font-weight: 600;
  text-decoration: none;
  transition: background-color var(--transition-fast);
}

.sidebar-btn:hover {
  background-color: var(--color-primary-hover);
  text-decoration: none;
}

.content-col {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.content-section {
  scroll-margin-top: 130px;
}

.content-section h2 {
  font-size: 1.35rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0 0 14px;
}

.content-section p {
  font-size: 0.95rem;
  line-height: 1.65;
  color: var(--color-text-muted);
  margin: 0 0 12px;
}

.content-section ul,
.content-section ol {
  padding-left: 20px;
  margin: 0 0 16px;
  color: var(--color-text-muted);
  font-size: 0.95rem;
  line-height: 1.65;
}

.content-section li {
  margin-bottom: 8px;
}

.inline-link {
  color: var(--color-primary);
  text-decoration: underline;
}

.contact-box {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md, 8px);
  padding: 16px 20px;
  margin-top: 12px;
}

.contact-box p {
  margin: 4px 0;
  font-size: 0.875rem;
}

@media (max-width: 860px) {
  .main-layout {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .sidebar-col {
    display: none;
  }
}
</style>
