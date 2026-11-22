<script setup>
/**
 * PrivacyView Component
 * Route: /privacy
 * Professional Privacy Policy for Parkly Parking Platform.
 * Matches Parkly design system tokens, typography, and dark/light theme.
 */
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const lastUpdated = 'November 20, 2026'

const sections = [
  { id: 'overview', title: '1. Privacy Commitment & Scope' },
  { id: 'data-collection', title: '2. Information We Collect' },
  { id: 'usage', title: '3. How We Use Your Information' },
  { id: 'sharing', title: '4. Information Sharing & Third Parties' },
  { id: 'telemetry', title: '5. Location & ANPR Telemetry' },
  { id: 'security', title: '6. Data Security & Storage' },
  { id: 'retention', title: '7. Data Retention & Deletion' },
  { id: 'rights', title: '8. Your Rights & Choices' },
  { id: 'cookies', title: '9. Cookies & Tracking Technologies' },
  { id: 'dpo', title: '10. Data Protection Officer & Contact' },
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
        <h1 class="header-title">Privacy Policy</h1>
        <p class="header-lead">
          We believe mobility privacy is fundamental. This policy outlines how Parkly collects, encrypts, and safeguards your personal data, vehicle telemetry, and payment information.
        </p>
        <div class="header-meta">
          <span class="meta-item">Effective: {{ lastUpdated }}</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <span class="meta-item">Version 2.3</span>
          <span class="meta-sep" aria-hidden="true">&bull;</span>
          <router-link to="/terms" class="meta-link">View Terms of Service</router-link>
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
            <router-link to="/terms" class="sidebar-btn">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
              </svg>
              <span>Terms of Service</span>
            </router-link>
            <a href="mailto:privacy@parkly.com" class="sidebar-btn">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              <span>Contact Privacy Officer</span>
            </a>
          </div>
        </div>
      </aside>

      <!-- Document Body -->
      <article class="legal-body">
        <!-- Section 1 -->
        <section id="overview" class="doc-section">
          <h2>1. Privacy Commitment & Scope</h2>
          <p>
            At Parkly ("we", "us", or "our"), safeguarding your personal details and vehicle telemetry is central to our mission of delivering seamless smart urban mobility. This Privacy Policy applies to personal data collected across the Parkly platform, mobile applications, parking partner gate systems, and customer support channels.
          </p>
          <div class="highlight-box">
            <strong>Key Guarantee:</strong> Parkly never sells your personal identity, license plate records, or GPS trip histories to data brokers, ad networks, or unauthorized third parties.
          </div>
        </section>

        <!-- Section 2 -->
        <section id="data-collection" class="doc-section">
          <h2>2. Information We Collect</h2>
          <p>
            We collect information strictly necessary to facilitate reservations, verify identity, and operate automated garage entry:
          </p>
          <ul>
            <li><strong>Account Profile:</strong> Full name, verified email address, phone number, hashed password credentials, and avatar preferences.</li>
            <li><strong>Vehicle Identifiers:</strong> License plate number, vehicle class (sedan, SUV, EV, motorcycle), and EV charging connector requirements.</li>
            <li><strong>Financial & Transaction Data:</strong> Payment card token (handled securely by PCI-DSS Level 1 compliant processors), digital wallet identifiers, billing history, and session receipts. We never store raw credit card numbers on Parkly servers.</li>
            <li><strong>Device & Technical Telemetry:</strong> IP address, browser type, operating system, and crash reports.</li>
          </ul>
        </section>

        <!-- Section 3 -->
        <section id="usage" class="doc-section">
          <h2>3. How We Use Your Information</h2>
          <p>
            Your information powers core platform functionality:
          </p>
          <ul>
            <li>Locating, booking, and confirming real-time parking stalls in partner facilities.</li>
            <li>Transmitting entry passes (dynamic QR codes and authorized license plate hashes) to automated gate barriers.</li>
            <li>Processing reservation charges, wallet top-ups, and automated refunds.</li>
            <li>Sending critical booking updates, session expiry warnings, and digital invoices via SMS or push notifications.</li>
            <li>Detecting payment fraud and preventing gate abuse.</li>
          </ul>
        </section>

        <!-- Section 4 -->
        <section id="sharing" class="doc-section">
          <h2>4. Information Sharing & Third Parties</h2>
          <p>
            To deliver parking services, we share limited data only with trusted partners under strict confidentiality agreements:
          </p>
          <div class="table-card">
            <table class="legal-table">
              <thead>
                <tr>
                  <th scope="col">Recipient</th>
                  <th scope="col">Shared Data</th>
                  <th scope="col">Purpose</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Parking Garage Operators</strong></td>
                  <td>Vehicle plate, reservation start/end window, bay number</td>
                  <td>Authorize physical garage entry and allocate reserved bay</td>
                </tr>
                <tr>
                  <td><strong>Payment Gateways (SSL/Stripe)</strong></td>
                  <td>Card billing tokens, amount, customer ID</td>
                  <td>Secure cashless transaction processing</td>
                </tr>
                <tr>
                  <td><strong>Cloud Infrastructure (AWS / GCP)</strong></td>
                  <td>Encrypted database backups and API logs</td>
                  <td>High-availability hosting and cyber defense</td>
                </tr>
                <tr>
                  <td><strong>Law Enforcement</strong></td>
                  <td>Specific session record with court order</td>
                  <td>Legal compliance and emergency life safety</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Section 5 -->
        <section id="telemetry" class="doc-section">
          <h2>5. Location & ANPR Telemetry</h2>
          <p>
            When utilizing location-based garage discovery, Parkly accesses precise GPS coordinates only with your explicit browser or OS permission. We use geolocation exclusively to calculate distance, driving duration, and nearby available capacity.
          </p>
          <p>
            Partner garages equipped with Automatic Number Plate Recognition (ANPR) cameras scan plates at ingress and egress points solely to trigger barrier arms and compute exact stay durations. Plate images captured by gate cameras are automatically deleted or scrubbed after transaction settlement.
          </p>
        </section>

        <!-- Section 6 -->
        <section id="security" class="doc-section">
          <h2>6. Data Security & Storage</h2>
          <p>
            Parkly implements industry-leading defense-in-depth security measures to protect your information:
          </p>
          <ul>
            <li><strong>Encryption in Transit:</strong> 256-bit TLS 1.3 encryption across all website and API communications.</li>
            <li><strong>Encryption at Rest:</strong> All user credentials and transaction records encrypted via AES-256 storage volumes.</li>
            <li><strong>Zero-Knowledge Passwords:</strong> Account passwords are salted and hashed utilizing modern Argon2id cryptographic algorithms.</li>
            <li><strong>Strict Role-Based Access:</strong> Production database access is governed by multi-factor authentication (MFA) and least-privilege principles.</li>
          </ul>
        </section>

        <!-- Section 7 -->
        <section id="retention" class="doc-section">
          <h2>7. Data Retention & Deletion</h2>
          <p>
            We retain your personal data only for as long as your account remains active or as required by law:
          </p>
          <ul>
            <li>Active account profiles and vehicle records remain stored until you request account closure.</li>
            <li>Completed parking session logs and financial invoices are retained for five (5) years to comply with statutory accounting and tax regulations.</li>
            <li>ANPR gate camera optical captures are automatically purged within thirty (30) days of session closure.</li>
          </ul>
        </section>

        <!-- Section 8 -->
        <section id="rights" class="doc-section">
          <h2>8. Your Rights & Choices</h2>
          <p>
            Regardless of your jurisdiction, Parkly grants comprehensive control over your personal data:
          </p>
          <ul>
            <li><strong>Access & Portability:</strong> View your complete booking history and export your profile data in machine-readable JSON format.</li>
            <li><strong>Rectification:</strong> Edit your name, email, phone number, and vehicle records instantly via your <router-link to="/profile" class="inline-link">Profile Settings</router-link>.</li>
            <li><strong>Right to Erasure ("Right to be Forgotten"):</strong> Request full deletion of your user account and profile data by emailing our privacy team.</li>
            <li><strong>Communication Controls:</strong> Opt out of promotional newsletters while retaining essential booking alert SMS messages.</li>
          </ul>
        </section>

        <!-- Section 9 -->
        <section id="cookies" class="doc-section">
          <h2>9. Cookies & Tracking Technologies</h2>
          <p>
            Parkly utilizes minimal cookies and browser local storage strictly necessary for authentication tokens, theme preferences (light/dark mode), and active reservation session state. We do not use intrusive third-party cross-site advertising tracking cookies.
          </p>
        </section>

        <!-- Section 10 -->
        <section id="dpo" class="doc-section">
          <h2>10. Data Protection Officer & Contact</h2>
          <p>
            If you have questions regarding this Privacy Policy, wish to exercise your data subject rights, or have a security inquiry, please contact our designated Data Protection Officer (DPO):
          </p>
          <div class="contact-card">
            <h3>Parkly Privacy & Data Protection Office</h3>
            <p>Our dedicated compliance team reviews and responds to all privacy inquiries within 48 business hours.</p>
            <div class="contact-details">
              <span><strong>Privacy Inquiries:</strong> privacy@parkly.com</span>
              <span><strong>Data Protection Officer:</strong> dpo@parkly.com</span>
              <span><strong>Physical Address:</strong> Parkly Technologies Inc., Level 8, Plot 42, Gulshan Avenue, Dhaka 1212, Bangladesh</span>
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

