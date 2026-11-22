<script setup>
/**
 * TermsView Component
 * Route: /terms
 * Professional Terms of Service for Parkly Parking Platform.
 * Matches Parkly design system tokens, typography, and dark/light theme.
 */
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const lastUpdated = 'November 20, 2026'

const sections = [
  { id: 'acceptance', title: '1. Acceptance of Terms' },
  { id: 'services', title: '2. Parkly Services & Scope' },
  { id: 'accounts', title: '3. User Accounts & Verification' },
  { id: 'booking-payment', title: '4. Reservations, Fees & Payments' },
  { id: 'cancellation', title: '5. Cancellation & Refund Policy' },
  { id: 'conduct', title: '6. Garage Conduct & Driver Responsibilities' },
  { id: 'operators', title: '7. Operator Terms & Bay Commitments' },
  { id: 'liability', title: '8. Limitation of Liability & Disclaimers' },
  { id: 'termination', title: '9. Account Suspension & Termination' },
  { id: 'contact', title: '10. Governing Law & Contact' },
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
    <!-- Header Hero Banner -->
    <header class="legal-header">
      <div class="legal-container header-inner">
        <div class="header-badge" role="note">
          <span class="badge-dot" aria-hidden="true"></span>
          <span>Draft, subject to legal review</span>
        </div>
        <h1 class="header-title">Terms of Service</h1>
        <p class="header-lead">
          Please read these terms carefully before accessing or using Parkly's smart parking reservation, navigation, and automated payment services.
        </p>
        <div class="header-meta">
          <span class="meta-item">Effective: {{ lastUpdated }}</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <span class="meta-item">Version 2.4</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/privacy" class="meta-link">View Privacy Policy</router-link>
        </div>
      </div>
    </header>

    <!-- Main Content Layout (Sidebar Sticky Navigation + Text Body) -->
    <div class="legal-container content-grid">
      <!-- Sticky Navigation Rail -->
      <aside class="legal-sidebar" aria-label="Table of contents">
        <div class="sidebar-card">
          <h2 class="sidebar-heading">On this page</h2>
          <nav class="sidebar-nav">
            <button
              v-for="sec in sections"
              :key="sec.id"
              type="button"
              class="nav-item"
              :class="{ 'is-active': activeSection === sec.id }"
              @click="scrollToSection(sec.id)"
            >
              {{ sec.title }}
            </button>
          </nav>
          <div class="sidebar-actions">
            <router-link to="/privacy" class="sidebar-btn">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
              <span>Privacy Policy</span>
            </router-link>
            <a href="mailto:legal@parkly.com" class="sidebar-btn">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <span>Contact Legal Team</span>
            </a>
          </div>
        </div>
      </aside>

      <!-- Document Body -->
      <article class="legal-body">
        <!-- Section 1 -->
        <section id="acceptance" class="doc-section">
          <h2>1. Acceptance of Terms</h2>
          <p>
            By registering for an account, browsing parking spots, or completing a reservation through the Parkly website, mobile web, or APIs (collectively, the "Platform"), you agree to be legally bound by these Terms of Service ("Terms") and our incorporated <router-link to="/privacy" class="inline-link">Privacy Policy</router-link>.
          </p>
          <p>
            If you represent a business entity, parking operator, or corporate fleet, you affirm that you have the full authority to bind that entity to these Terms. If you do not agree with any part of these conditions, you must discontinue using our services immediately.
          </p>
        </section>

        <!-- Section 2 -->
        <section id="services" class="doc-section">
          <h2>2. Parkly Services & Scope</h2>
          <p>
            Parkly operates an intelligent parking marketplace and mobility orchestration platform. We connect verified drivers ("Drivers") seeking vehicle storage with independent commercial, retail, residential, and municipal parking facility owners and operators ("Operators").
          </p>
          <div class="highlight-box">
            <strong>Platform Facilitator Notice:</strong> Parkly is a technology platform and broker. Unless expressly stated otherwise on a specific facility page, Parkly does not own, lease, control, or operate physical parking garages, nor do we employ parking attendants or valet personnel.
          </div>
          <p>
            Platform features include real-time spot occupancy forecasting, automated gate access via License Plate Recognition (ANPR) and dynamic QR codes, EV charger allocation, and cashless digital payment processing.
          </p>
        </section>

        <!-- Section 3 -->
        <section id="accounts" class="doc-section">
          <h2>3. User Accounts & Verification</h2>
          <p>
            To book spaces, store vehicle profiles, and access fast-gate privileges, users must create a Parkly account. You agree to:
          </p>
          <ul>
            <li>Provide accurate, current, and complete legal name, email address, phone number, and vehicle registration credentials.</li>
            <li>Maintain the confidentiality of your account password and security credentials.</li>
            <li>Accept sole responsibility for all activities, booking charges, and gate authorizations made under your account.</li>
            <li>Notify Parkly immediately at <a href="mailto:security@parkly.com" class="inline-link">security@parkly.com</a> upon suspecting unauthorized account access.</li>
          </ul>
        </section>

        <!-- Section 4 -->
        <section id="booking-payment" class="doc-section">
          <h2>4. Reservations, Fees & Payments</h2>
          <p>
            When booking through Parkly, rates displayed include parking tariffs set by operators, applicable municipal taxes, and transparent Parkly service facilitation fees.
          </p>
          <ul>
            <li><strong>Guaranteed Bay Allocation:</strong> Confirmed reservations reserve an available space matching your chosen vehicle tier during the designated entry and exit window.</li>
            <li><strong>Automated Billing:</strong> Payment is authorized at time of reservation or automatically billed upon garage exit for real-time sensor-monitored sessions.</li>
            <li><strong>Overstay Tariffs:</strong> Vehicles occupying bays beyond their reservation grace period (typically 15 minutes) will be billed at the facility's standard incremental hourly overstay rate.</li>
          </ul>
        </section>

        <!-- Section 5 -->
        <section id="cancellation" class="doc-section">
          <h2>5. Cancellation & Refund Policy</h2>
          <p>
            We recognize travel plans change. Our transparent cancellation tiers ensure fairness for both drivers and facility hosts:
          </p>
          <div class="table-card">
            <table class="legal-table">
              <thead>
                <tr>
                  <th scope="col">Notice Window</th>
                  <th scope="col">Refund Amount</th>
                  <th scope="col">Processing Time</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>More than 2 hours before start time</td>
                  <td><span class="tag-badge is-success">100% Full Refund</span></td>
                  <td>Instant to Parkly Wallet / 2-3 days card</td>
                </tr>
                <tr>
                  <td>Between 30 min and 2 hours before start</td>
                  <td><span class="tag-badge is-neutral">80% Partial Refund</span></td>
                  <td>1-3 business days</td>
                </tr>
                <tr>
                  <td>Less than 30 minutes before / No-show</td>
                  <td><span class="tag-badge is-danger">No Refund</span></td>
                  <td>Not applicable</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p>
            In the rare event a garage is inaccessible due to physical gate malfunction or operator error, notify our live support team within 45 minutes for a 100% refund plus alternative bay re-routing credit.
          </p>
        </section>

        <!-- Section 6 -->
        <section id="conduct" class="doc-section">
          <h2>6. Garage Conduct & Driver Responsibilities</h2>
          <p>
            Drivers utilizing partner garages agree to adhere to all posted signage, maximum height clearances, speed limits (maximum 10 km/h in all facilities), and staff directions.
          </p>
          <ul>
            <li>Vehicles must be parked strictly within marked stall boundaries. Blocking aisles or fire exits is grounds for immediate towing at vehicle owner expense.</li>
            <li>No hazardous materials, flammable gases, or unregistered commercial salvage may be stored in parked vehicles.</li>
            <li>Drivers are responsible for locking doors, securing personal property, and arming vehicle alarms.</li>
          </ul>
        </section>

        <!-- Section 7 -->
        <section id="operators" class="doc-section">
          <h2>7. Operator Terms & Bay Commitments</h2>
          <p>
            Operators listing inventory on the Parkly platform represent that their facilities comply with all local zoning, safety, fire prevention, and commercial insurance mandates.
          </p>
          <p>
            Operators must maintain active telemetry links with Parkly's sensor and gate APIs, honour valid driver pass confirmations, and maintain unobstructed ingress/egress routes. Failure to honor verified reservations subjects the operator to platform penalties and algorithmic de-ranking.
          </p>
        </section>

        <!-- Section 8 -->
        <section id="liability" class="doc-section">
          <h2>8. Limitation of Liability & Disclaimers</h2>
          <p>
            Parkly services are provided on an "as is" and "as available" basis without warranties of any kind, whether express or implied.
          </p>
          <p>
            To the maximum extent permitted by applicable law, Parkly Technologies Inc., its officers, employees, and agents shall not be liable for any direct, indirect, incidental, punitive, or consequential damages resulting from:
          </p>
          <ul>
            <li>Physical damage, collision, theft, burglary, fire, or vandalism occurring inside third-party parking garages.</li>
            <li>Temporary platform downtime, GPS routing inaccuracies, or third-party payment gateway latency.</li>
            <li>Disputes arising directly between Drivers and independent Garage Operators.</li>
          </ul>
        </section>

        <!-- Section 9 -->
        <section id="termination" class="doc-section">
          <h2>9. Account Suspension & Termination</h2>
          <p>
            Parkly reserves the right to suspend or permanently terminate user accounts or gate access credentials without prior notice in instances of:
          </p>
          <ul>
            <li>Fraudulent payment chargebacks, credit card abuse, or counterfeit voucher redemption.</li>
            <li>Reckless driving, verbal or physical harassment of garage personnel, or property destruction.</li>
            <li>Automated scraping of Parkly pricing data or attempted exploitation of gate hardware APIs.</li>
          </ul>
        </section>

        <!-- Section 10 -->
        <section id="contact" class="doc-section">
          <h2>10. Governing Law & Contact</h2>
          <p>
            These Terms shall be governed by and construed in accordance with the laws of the People's Republic of Bangladesh, without regard to conflict of law principles. Any dispute arising out of or relating to these Terms shall be settled through amicable negotiation or submitted to arbitration in Dhaka, Bangladesh.
          </p>
          <div class="contact-card">
            <h3>Questions regarding our terms?</h3>
            <p>Our legal and compliance team is available to assist users and enterprise garage partners.</p>
            <div class="contact-details">
              <span><strong>Email:</strong> legal@parkly.com</span>
              <span><strong>Compliance Office:</strong> Parkly Technologies Inc., Gulshan-2, Dhaka 1212, Bangladesh</span>
              <span><strong>Support Hotline:</strong> +880 (0) 9612-PARKLY</span>
            </div>
          </div>
        </section>
      </article>
    </div>
  </div>
</template>

<style scoped>
.legal-page {
  width: 100%;
  background: var(--color-bg);
  color: var(--color-text);
  padding-bottom: 80px;
}

.legal-container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Header Banner */
.legal-header {
  padding: 56px 0 44px;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}

.header-inner {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.header-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  align-self: flex-start;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-primary);
  background: var(--color-primary-light);
  border: 1px solid var(--color-border);
}

.badge-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--color-primary);
}

.header-title {
  font-size: 38px;
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--color-text);
}

.header-lead {
  font-size: 17px;
  line-height: 1.6;
  color: var(--color-text-muted);
  max-width: 780px;
}

.header-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  font-size: 13px;
  color: var(--color-text-muted);
  padding-top: 4px;
}

.meta-sep {
  opacity: 0.4;
}

.meta-link {
  color: var(--color-primary);
  text-decoration: underline;
  text-underline-offset: 3px;
  font-weight: 500;
}

.meta-link:hover {
  color: var(--color-primary-hover);
}

/* Grid Layout */
.content-grid {
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr);
  gap: 48px;
  padding-top: 48px;
  align-items: start;
}

/* Sidebar Sticky Navigation */
.legal-sidebar {
  position: sticky;
  top: 96px;
}

.sidebar-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 16px;
  padding: 20px;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sidebar-heading {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--color-text-muted);
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  width: 100%;
  text-align: left;
  padding: 8px 12px;
  border-radius: 8px;
  border: none;
  background: transparent;
  color: var(--color-text-muted);
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.nav-item:hover {
  background: var(--color-surface-subtle);
  color: var(--color-text);
}

.nav-item.is-active {
  background: var(--color-primary-light);
  color: var(--color-primary);
  font-weight: 600;
}

.sidebar-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-top: 12px;
  border-top: 1px solid var(--color-border);
}

.sidebar-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid var(--color-border);
  background: var(--color-surface-subtle);
  color: var(--color-text);
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.sidebar-btn:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

/* Document Body Styling */
.legal-body {
  display: flex;
  flex-direction: column;
  gap: 44px;
  min-width: 0;
}

.doc-section {
  scroll-margin-top: 110px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.doc-section h2 {
  font-size: 22px;
  font-weight: 700;
  color: var(--color-text);
  letter-spacing: -0.01em;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--color-border);
}

.doc-section p {
  font-size: 15px;
  line-height: 1.7;
  color: var(--color-text-muted);
}

.doc-section ul {
  list-style: disc;
  padding-left: 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.doc-section li {
  font-size: 15px;
  line-height: 1.65;
  color: var(--color-text-muted);
}

.inline-link {
  color: var(--color-primary);
  text-decoration: underline;
  text-underline-offset: 3px;
}

.inline-link:hover {
  color: var(--color-primary-hover);
}

/* Callout Highlight Box */
.highlight-box {
  padding: 16px 20px;
  border-radius: 12px;
  background: var(--color-surface-subtle);
  border-left: 4px solid var(--color-primary);
  font-size: 14px;
  line-height: 1.6;
  color: var(--color-text);
}

/* Responsive Table */
.table-card {
  width: 100%;
  overflow-x: auto;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  background: var(--color-surface);
}

.legal-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  text-align: left;
}

.legal-table th {
  padding: 14px 18px;
  background: var(--color-surface-subtle);
  font-weight: 600;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-border);
}

.legal-table td {
  padding: 14px 18px;
  border-bottom: 1px solid var(--color-border);
  color: var(--color-text-muted);
}

.legal-table tr:last-child td {
  border-bottom: none;
}

.tag-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.tag-badge.is-success {
  background: var(--color-success-bg, rgba(16, 185, 129, 0.12));
  color: var(--color-success);
}

.tag-badge.is-neutral {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.tag-badge.is-danger {
  background: var(--color-danger-bg, rgba(239, 68, 68, 0.12));
  color: var(--color-danger);
}

/* Contact Footer Card */
.contact-card {
  margin-top: 12px;
  padding: 24px;
  border-radius: 14px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contact-card h3 {
  font-size: 17px;
  font-weight: 700;
  color: var(--color-text);
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 14px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* Responsive Breakpoints */
@media (max-width: 900px) {
  .content-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .legal-sidebar {
    position: static;
  }

  .header-title {
    font-size: 30px;
  }
}

@media (max-width: 640px) {
  .legal-header {
    padding: 36px 0 28px;
  }

  .header-title {
    font-size: 26px;
  }

  .content-grid {
    padding-top: 28px;
  }

  .doc-section h2 {
    font-size: 19px;
  }
}
</style>

