<script setup>
/**
 * FaqSection Component
 * Reference: React "FAQ 5" ported directly to Vue 3 (Single-file Component)
 * - Section ID: #faq (also aliased for #support so legacy and new links work seamlessly)
 * - 2-column bordered frame: left column intro (mono eyebrow, serif heading, muted paragraph),
 *   right column hairline accordion with rotating chevron.
 * - Single-open collapsible with animated height (grid-template-rows 0fr -> 1fr).
 * - Full WCAG AA accessibility: heading <h3>, <button> trigger, aria-expanded, aria-controls,
 *   role="region", Arrow Up/Down & Home/End keyboard navigation.
 * - Deep linking: hash #faq-{id} opens item & updates hash via history.replaceState.
 * - FAQPage Schema (JSON-LD) injected into <head>.
 * - Zero emojis, no fake sensor/automation/rating claims.
 */
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

// Prototype sandbox flag (can also read from import.meta.env.VITE_PAYMENT_SANDBOX if defined)
const isPaymentSandbox = ref(true)

const faqs = [
  {
    id: 'availability',
    q: 'Is availability live?',
    a: 'Availability is the number of free slots last reported by the parking operator or an administrator. Each facility shows when it was last updated; if it is old or missing, Parkly says so instead of guessing.',
    group: 'discovery',
  },
  {
    id: 'account',
    q: 'Do I need an account to search for parking?',
    a: 'No. You can search and compare without signing in. Account details are asked only when you make a booking.',
    group: 'account',
  },
  {
    id: 'near-me',
    q: 'How do I find parking near me?',
    a: 'Allow location access, or choose District, Thana and Area manually. Parkly asks permission before using your location and the manual option always works.',
    group: 'discovery',
  },
  {
    id: 'ai-assistant',
    q: 'What does the AI assistant do?',
    a: 'You can describe what you need in plain language, such as an affordable spot in Mirpur for an SUV. The AI picks out the area, vehicle type, budget and duration, and Parkly matches them against registered facilities. It does not create prices or availability and does not confirm bookings.',
    group: 'ai',
  },
  {
    id: 'fee-calculation',
    q: 'How is the fee calculated?',
    a: 'The server calculates the fee from the facility\'s configured rate for your vehicle type and the duration you choose. You see the total before you pay.',
    group: 'pricing',
  },
  {
    id: 'vehicle-types',
    q: 'Which vehicle types are supported?',
    a: 'Car, SUV, Motorcycle and Others (only the types present in the database; each facility lists what it accepts).',
    group: 'pricing',
  },
  {
    id: 'payment',
    q: 'How do I pay?',
    a: computed(() => {
      if (isPaymentSandbox.value) {
        return 'Online payment through the supported gateway. During the prototype the gateway runs in sandbox mode, so no real money is taken.'
      }
      return 'Online payment through the supported gateway. You can choose mobile banking or card payments at checkout.'
    }),
    group: 'payment',
  },
  {
    id: 'entrance-verification',
    q: 'What do I show at the parking entrance?',
    a: 'Your digital invoice with the Booking ID. Parking staff check it against the booking record before you enter. Entry is not automated.',
    group: 'entry',
  },
  {
    id: 'cancellation',
    q: 'Can I cancel a booking?',
    a: 'Cancellation follows the Cancellation and Refund Policy outlined in our Terms of Service. Review booking details and operator rules before cancelling.',
    group: 'booking',
    link: '/terms#cancellation',
    linkText: 'Terms of Service',
  },
  {
    id: 'listing',
    q: 'I am a parking owner. How do I list my parking?',
    a: 'Use "List your parking" to send an enquiry. Administrators or operators then manage prices, slot availability and booking status in the admin panel.',
    group: 'operator',
  },
  {
    id: 'report-issue',
    q: 'What if the price or availability looks wrong?',
    a: 'Report it through Contact support so the operator can correct it.',
    group: 'support',
  },
]

// Single open item id (First item open by default)
const openItemId = ref('availability')

function toggleItem(id) {
  if (openItemId.value === id) {
    openItemId.value = null
  } else {
    openItemId.value = id
    // Update URL hash without causing a page jump
    const hash = `#faq-${id}`
    if (window.location.hash !== hash) {
      window.history.replaceState(null, '', hash)
    }
  }
}

// Keyboard Navigation between Accordion Triggers
function handleKeydown(event, currentIndex) {
  const triggers = document.querySelectorAll('.faq-trigger-btn')
  if (!triggers.length) return

  let targetIndex = null
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    targetIndex = (currentIndex + 1) % triggers.length
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    targetIndex = (currentIndex - 1 + triggers.length) % triggers.length
  } else if (event.key === 'Home') {
    event.preventDefault()
    targetIndex = 0
  } else if (event.key === 'End') {
    event.preventDefault()
    targetIndex = triggers.length - 1
  }

  if (targetIndex !== null && triggers[targetIndex]) {
    triggers[targetIndex].focus()
  }
}

// Deep Link Handling on Mount & Hash Changes
function checkHash() {
  const hash = window.location.hash
  if (!hash) return

  if (hash === '#faq' || hash === '#support') {
    // Just scroll to section
    return
  }

  const prefix = '#faq-'
  if (hash.startsWith(prefix)) {
    const targetId = hash.slice(prefix.length)
    const exists = faqs.some((f) => f.id === targetId)
    if (exists) {
      openItemId.value = targetId
    }
  }
}

let jsonLdScript = null

function injectSchema() {
  // Clean up any existing instance before injecting
  const existing = document.getElementById('parkly-faq-jsonld')
  if (existing) {
    existing.remove()
  }

  const schema = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map((f) => ({
      '@type': 'Question',
      name: f.q,
      acceptedAnswer: {
        '@type': 'Answer',
        text: typeof f.a === 'object' ? f.a.value : f.a,
      },
    })),
  }

  jsonLdScript = document.createElement('script')
  jsonLdScript.id = 'parkly-faq-jsonld'
  jsonLdScript.type = 'application/ld+json'
  jsonLdScript.text = JSON.stringify(schema)
  document.head.appendChild(jsonLdScript)
}

onMounted(() => {
  checkHash()
  window.addEventListener('hashchange', checkHash)
  injectSchema()
})

onBeforeUnmount(() => {
  try {
    window.removeEventListener('hashchange', checkHash)
    if (jsonLdScript) {
      jsonLdScript.remove()
      jsonLdScript = null
    } else {
      const existing = document.getElementById('parkly-faq-jsonld')
      if (existing) existing.remove()
    }
  } catch (err) {
    console.warn('[FaqSection] Cleanup caught:', err)
  }
})
</script>

<template>
  <section id="faq" class="parkly-faq-section">
    <!-- Anchor hook for legacy #support links -->
    <div id="support" class="faq-scroll-anchor" aria-hidden="true"></div>

    <div class="faq-container">
      <!-- Outer Bordered Frame: 1px hairline, two equal columns on md+ -->
      <div class="faq-frame-card">
        <!-- Left Column: Intro (Sticky / Top-aligned) -->
        <div class="faq-intro-col">
          <div class="intro-content-stack">
            <span class="faq-mono-eyebrow">FAQ</span>
            <h2 class="faq-serif-heading">Questions, answered.</h2>
            <p class="faq-intro-desc">
              The things drivers and operators ask most about Parkly. Still stuck?
              <router-link to="/contact" class="faq-inline-link">Contact support</router-link>
              and we will help.
            </p>
          </div>
        </div>

        <!-- Right Column: Hairline Accordion List -->
        <div class="faq-list-col">
          <div class="faq-accordion-stack">
            <div
              v-for="(item, idx) in faqs"
              :key="item.id"
              class="faq-item"
              :class="{ 'is-open': openItemId === item.id }"
            >
              <!-- Accessible Accordion Header & Trigger -->
              <h3 class="faq-item-heading">
                <button
                  type="button"
                  :id="`faq-trigger-${item.id}`"
                  class="faq-trigger-btn"
                  :aria-expanded="openItemId === item.id"
                  :aria-controls="`faq-panel-${item.id}`"
                  @click="toggleItem(item.id)"
                  @keydown="handleKeydown($event, idx)"
                >
                  <span class="faq-q-text">{{ item.q }}</span>

                  <!-- Rotating SVG Chevron Icon -->
                  <span class="chevron-wrap" aria-hidden="true">
                    <svg
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="chevron-svg"
                    >
                      <polyline points="6 9 12 15 18 9" />
                    </svg>
                  </span>
                </button>
              </h3>

              <!-- CSS Grid-Template-Rows Animated Panel -->
              <div
                :id="`faq-panel-${item.id}`"
                role="region"
                :aria-labelledby="`faq-trigger-${item.id}`"
                class="faq-answer-grid"
                :class="{ 'is-expanded': openItemId === item.id }"
              >
                <div class="faq-answer-overflow">
                  <div class="faq-answer-body">
                    <p class="faq-a-text">
                      {{ typeof item.a === 'object' ? item.a.value : item.a }}
                      <router-link
                        v-if="item.link"
                        :to="item.link"
                        class="faq-inline-link"
                      >
                        {{ item.linkText }}
                      </router-link>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.parkly-faq-section {
  position: relative;
  width: 100%;
  padding: 64px 20px;
  background-color: var(--color-bg, #F8FAFC);
  scroll-margin-top: var(--nav-h, 72px);
}

@media (min-width: 768px) {
  .parkly-faq-section {
    padding: 96px 24px;
  }
}

.faq-scroll-anchor {
  position: absolute;
  top: 0;
  left: 0;
  height: 1px;
  width: 1px;
  pointer-events: none;
  visibility: hidden;
}

.faq-container {
  max-width: 1024px; /* max-w-5xl */
  margin: 0 auto;
}

/* Single Bordered Frame: border-y always, md:border-x */
.faq-frame-card {
  width: 100%;
  border-top: 1px solid var(--color-border, #E2E8F0);
  border-bottom: 1px solid var(--color-border, #E2E8F0);
  display: flex;
  flex-direction: column;
}

@media (min-width: 768px) {
  .faq-frame-card {
    border-left: 1px solid var(--color-border, #E2E8F0);
    border-right: 1px solid var(--color-border, #E2E8F0);
    display: grid;
    grid-template-columns: 1fr 1fr;
  }
}

/* Left Column: Intro */
.faq-intro-col {
  padding: 48px 24px 28px;
  border-bottom: 1px solid var(--color-border, #E2E8F0);
}

@media (min-width: 768px) {
  .faq-intro-col {
    padding: 64px 40px;
    border-bottom: none;
    border-right: 1px solid var(--color-border, #E2E8F0);
  }
}

.intro-content-stack {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 380px;
}

/* Monospace Uppercase Eyebrow: 10px, tracking 0.16em */
.faq-mono-eyebrow {
  font-family: ui-monospace, 'JetBrains Mono', SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--color-primary, #0F766E);
}

/* Serif Heading: text-4xl md:text-5xl, medium weight, line-height 1.04 */
.faq-serif-heading {
  font-family: 'Instrument Serif', 'Fraunces', Georgia, serif;
  font-size: clamp(2.35rem, 4vw, 3.15rem);
  font-weight: 500;
  line-height: 1.04;
  letter-spacing: -0.025em;
  color: var(--color-text, #0F172A);
  margin: 0;
}

.faq-intro-desc {
  font-family: var(--font-sans);
  font-size: 0.875rem;
  line-height: 1.55;
  color: var(--color-text-muted, #64748B);
  margin: 0;
}

.faq-inline-link {
  color: var(--color-primary, #0F766E);
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: opacity var(--transition-fast);
}

.faq-inline-link:hover {
  opacity: 0.8;
}

/* Right Column: List */
.faq-list-col {
  padding: 16px 24px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

@media (min-width: 768px) {
  .faq-list-col {
    padding: 32px;
  }
}

.faq-accordion-stack {
  display: flex;
  flex-direction: column;
  width: 100%;
}

/* Each item has a bottom hairline */
.faq-item {
  border-bottom: 1px solid var(--color-border, #E2E8F0);
  transition: border-color var(--transition-fast);
}

.faq-item:last-child {
  border-bottom: none;
}

.faq-item-heading {
  margin: 0;
  font-size: inherit;
  font-weight: inherit;
}

/* Trigger Row: py-4, font medium, underline on hover */
.faq-trigger-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 0;
  min-height: 44px; /* Accessible tap target */
  background: transparent;
  border: none;
  cursor: pointer;
  text-align: left;
  outline: none;
  color: var(--color-text, #0F172A);
}

.faq-trigger-btn:focus-visible {
  border-radius: 4px;
  box-shadow: 0 0 0 2px var(--color-primary, #0F766E);
}

.faq-q-text {
  font-family: var(--font-sans);
  font-size: 0.9375rem;
  font-weight: 500;
  line-height: 1.45;
  color: var(--color-text, #0F172A);
  transition: color var(--transition-fast);
}

.faq-trigger-btn:hover .faq-q-text {
  text-decoration: underline;
  text-underline-offset: 3px;
}

.faq-item.is-open .faq-q-text {
  color: var(--color-primary, #0F766E);
}

/* Small Chevron Rotating 180deg */
.chevron-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 20px;
  height: 20px;
  color: var(--color-text-muted, #64748B);
  transition: transform 220ms ease, color 220ms ease;
}

.faq-item.is-open .chevron-wrap {
  transform: rotate(180deg);
  color: var(--color-primary, #0F766E);
}

.chevron-svg {
  display: block;
}

/* Animated Height with CSS Grid-Template-Rows Trick (0fr -> 1fr) */
.faq-answer-grid {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 220ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease;
  opacity: 0;
}

.faq-answer-grid.is-expanded {
  grid-template-rows: 1fr;
  opacity: 1;
}

.faq-answer-overflow {
  overflow: hidden;
}

.faq-answer-body {
  padding-bottom: 16px;
}

.faq-a-text {
  font-family: var(--font-sans);
  font-size: 0.875rem;
  line-height: 1.6;
  color: var(--color-text-muted, #64748B);
  margin: 0;
}

/* Respect Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  .chevron-wrap,
  .faq-answer-grid {
    transition: none !important;
  }
}
</style>
