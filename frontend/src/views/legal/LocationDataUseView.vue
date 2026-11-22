<script setup>
/**
 * LocationDataUseView Component
 * Route: /location-and-data-use
 * Grounded disclosure regarding GPS usage, manual alternative,
 * and zero storage of raw payment credentials.
 * Draft, subject to legal review.
 */
import { ref, onMounted, onUnmounted } from 'vue'

const lastUpdated = 'November 23, 2026'

const sections = [
  { id: 'gps-permission', title: '1. GPS Location Permission' },
  { id: 'manual-alternative', title: '2. Manual Location Alternative' },
  { id: 'data-minimization', title: '3. Data Minimization & Storage' },
  { id: 'payment-credentials', title: '4. Payment Credentials Security' },
  { id: 'ai-boundaries', title: '5. AI & External Map Boundaries' },
  { id: 'rights', title: '6. User Rights & Data Deletion' },
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
        <h1 class="header-title">Location & Data Use Disclosure</h1>
        <p class="header-lead">
          Parkly believes in complete transparency. Here is exactly how device geolocation is requested, why manual location is always offered, and how payment data is handled.
        </p>
        <div class="header-meta">
          <span class="meta-item">Effective: {{ lastUpdated }}</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/privacy" class="meta-link">Privacy Policy</router-link>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/terms" class="meta-link">Terms of Service</router-link>
        </div>
      </div>
    </header>

    <main class="legal-container main-layout">
      <aside class="sidebar-col">
        <nav class="sticky-nav" aria-label="Disclosure sections">
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
            <h4 class="card-title">Privacy Questions?</h4>
            <p class="card-text">Our compliance team is available to answer any questions about your data rights.</p>
            <router-link to="/contact" class="sidebar-btn">Contact Privacy Team</router-link>
          </div>
        </nav>
      </aside>

      <article class="content-col">
        <section id="gps-permission" class="content-section">
          <h2>1. GPS Location Permission</h2>
          <p>
            When searching for nearby parking spots in Dhaka, Parkly offers an optional <strong>"Current Location (GPS)"</strong> button.
          </p>
          <ul>
            <li><strong>User-Initiated Only:</strong> Parkly never requests browser location silently on page load. The browser's permission prompt appears only when you explicitly click the GPS search action.</li>
            <li><strong>Purpose:</strong> Geolocation coordinates (latitude and longitude) are used solely to sort registered parking hubs by straight-line proximity to your current position.</li>
            <li><strong>No Continuous Tracking:</strong> Parkly does not run background tracking, track journeys across the city, or store historical location breadcrumbs.</li>
          </ul>
        </section>

        <section id="manual-alternative" class="content-section">
          <h2>2. Manual Location Alternative</h2>
          <p>
            You are never required to share your device location to use Parkly.
          </p>
          <ul>
            <li>Every search screen provides a <strong>Manual Zone Selector</strong> featuring official Dhaka commercial and civic parking hubs (Gulshan-1, Gulshan-2, Banani 11, Motijheel CBD, Dhanmondi 27, Panthapath, Uttara, Mirpur DOHS, and more).</li>
            <li>Denying browser GPS permission does not restrict your ability to browse facilities, compare pricing, review reported availability, or reserve parking spots.</li>
          </ul>
        </section>

        <section id="data-minimization" class="content-section">
          <h2>3. Data Minimization & Storage</h2>
          <p>
            Parkly adheres to strict data minimization principles:
          </p>
          <ul>
            <li><strong>Browsing Without Accounts:</strong> You can explore registered facilities, map markers, pricing matrices, and reported slot counts completely anonymously without creating an account or providing personal details.</li>
            <li><strong>Booking Data:</strong> When reserving a spot, we collect only the minimal details necessary for administrative booking confirmation and parking staff invoice verification: driver name, contact phone, vehicle type, and requested duration.</li>
            <li><strong>No Secondary Data Monetization:</strong> We do not sell user data, telemetry, or contact records to advertisers or third-party data brokers.</li>
          </ul>
        </section>

        <section id="payment-credentials" class="content-section">
          <h2>4. Payment Credentials Security</h2>
          <p>
            <strong>Parkly never stores, logs, or processes raw payment credentials.</strong>
          </p>
          <ul>
            <li>We do not store credit/debit card numbers, CVV/CVC codes, card expiration dates, or bank login credentials on our servers.</li>
            <li>All checkout transactions in production are redirected to certified PCI-DSS compliant payment gateways (e.g., SSLCOMMERZ). Parkly receives only an authorized transaction reference token and verification status.</li>
            <li>During the current prototype release, payments operate strictly in <strong>sandbox mode</strong>. No financial accounts are connected or charged.</li>
          </ul>
        </section>

        <section id="ai-boundaries" class="content-section">
          <h2>5. AI & External Map Boundaries</h2>
          <p>
            Parkly integrates with Google Gemini for natural-language query parsing and map tile providers for spatial visualization:
          </p>
          <ul>
            <li><strong>Gemini Natural Language Queries:</strong> When using the natural-language search bar ("Ask Parkly AI"), only your typed search query is processed to extract structured intent (e.g. "Gulshan-2", "SUV", "2 hours"). Your precise GPS coordinates, identity, and personal records are never passed to the LLM.</li>
            <li><strong>Grounded Records Only:</strong> The AI never invents parking facilities or slots. All displayed facilities and rates come directly from verified records in Parkly's database.</li>
          </ul>
        </section>

        <section id="rights" class="content-section">
          <h2>6. User Rights & Data Deletion</h2>
          <p>
            In accordance with digital data protection principles, users may:
          </p>
          <ul>
            <li>Review all historical bookings associated with their account in their user profile.</li>
            <li>Request deletion of their contact messages, operator enquiries, or subscriber status by contacting our support desk at <a href="mailto:support@parkly.com.bd" class="inline-link">support@parkly.com.bd</a>.</li>
          </ul>
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

.content-section ul {
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
