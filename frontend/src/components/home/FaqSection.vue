<script setup>
/**
 * FaqSection Component
 * ID: support (targeted by Navbar "Support").
 * Fully keyboard-accessible accordion addressing transparent realities of Parkly:
 * - Real-time availability vs reported counts (no cameras/sensors/CCTV myths)
 * - Account requirement (browse/search freely; account needed only at booking)
 * - How entry verification works (digital PDF invoice shown to on-duty staff, no automated gates)
 * - How AI works (Gemini extracts search intent; inventory strictly comes from DB records)
 * - Dhaka-centered scope (District -> Thana -> Area cascading filter)
 * - Pricing & calculation (calculated by backend based on vehicle type and duration)
 * - Payment processing (SSLCOMMERZ sandbox demo flow)
 * - For garage operators & administrators (manual updates, booking alerts, status tracking)
 */
import { ref } from 'vue'
import SectionHeader from '../common/SectionHeader.vue'

const openIndex = ref(0)

const faqs = [
  {
    q: 'How does parking slot availability work in Parkly?',
    a: 'Availability in Parkly represents reported slot counts maintained directly by facility administrators or garage attendants. We do not use automated cameras, CCTV analytics, or IoT road sensors. When a driver books a slot, the system reserves that record, and available counts reflect the latest operator updates.',
  },
  {
    q: 'Do I need an account to search for parking?',
    a: 'No. Anyone can search for parking across Dhaka by GPS or cascading dropdowns (District → Thana → Area), check hourly fees, filter by vehicle type, or use Ask Parkly AI without creating an account. Account details are collected only when you confirm and pay for a booking.',
  },
  {
    q: 'How do I enter the parking facility after booking?',
    a: 'Once your payment is confirmed, Parkly generates a digital booking confirmation and downloadable PDF invoice with a unique Booking ID. When you arrive at the garage gate, show this invoice or Booking ID on your phone to the parking staff or security guard. They verify it against their operator booking record before granting entry. There are no automatic boom barriers or sensor gates.',
  },
  {
    q: 'What is the role of Google Gemini AI in Parkly?',
    a: 'Our AI feature ("Ask Parkly AI") is powered by Google Gemini strictly to parse natural-language prompts into structured search filters (such as location, vehicle type, duration, and budget). The AI does not generate inventory, predict availability, or confirm bookings. Every recommendation comes strictly from verified database records.',
  },
  {
    q: 'How are parking fees calculated?',
    a: 'Fees are calculated entirely on the backend based on your chosen vehicle category (Car, Motorcycle, or Others), the facility\'s configured hourly tariff, and your selected duration. The frontend displays the server-calculated total prior to checkout so you never face hidden fees or surge pricing.',
  },
  {
    q: 'Why is Parkly currently focused on Dhaka?',
    a: 'Parkly is designed around Dhaka\'s urban traffic hubs (such as Gulshan, Banani, Dhanmondi, Motijheel, and Uttara) where congestion and parking scarcity are most acute. The search hierarchy allows fast, accurate filtering by District, Thana, and Area without unnecessary nationwide fields.',
  },
  {
    q: 'Which vehicle types are supported?',
    a: 'Parkly supports standard Cars/Sedans, Motorcycles, and Others (including SUVs and microbuses) where designated by individual facility operators. Each facility profile indicates which vehicle types it accepts and their respective slot allocations.',
  },
  {
    q: 'How does payment work on the platform?',
    a: 'The platform integrates with the SSLCOMMERZ payment gateway sandbox for the prototype evaluation. You can test checkout with demo mobile banking accounts (bKash, Nagad) or sandbox test cards. Real funds are not deducted during this evaluation phase.',
  },
  {
    q: 'How do garage owners and administrators manage bookings?',
    a: 'Authorized administrators and operators access a dedicated management panel where they can register facility details, configure vehicle pricing, update reported available slots, receive new booking notifications, and track booking statuses through Pending → Confirmed → In Progress → Completed.',
  },
]

function toggleFaq(idx) {
  openIndex.value = openIndex.value === idx ? -1 : idx
}
</script>

<template>
  <section id="support" class="faq-section">
    <div class="faq-container">
      <SectionHeader
        eyebrow="Help & Transparency"
        title="Frequently Asked Questions"
        subtitle="Transparent answers regarding how Parkly operates, how availability is recorded, and how entry verification works."
      />

      <div class="accordion-wrapper">
        <div
          v-for="(faq, idx) in faqs"
          :key="idx"
          class="accordion-item"
          :class="{ 'is-open': openIndex === idx }"
        >
          <button
            class="accordion-trigger"
            :aria-expanded="openIndex === idx"
            @click="toggleFaq(idx)"
          >
            <span class="faq-question">{{ faq.q }}</span>
            <span class="faq-icon" aria-hidden="true">
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="faq-icon-svg"
                :class="{ 'is-rotated': openIndex === idx }"
              >
                <line x1="12" y1="5" x2="12" y2="19" class="vertical-line" />
                <line x1="5" y1="12" x2="19" y2="12" />
              </svg>
            </span>
          </button>

          <div v-show="openIndex === idx" class="accordion-content">
            <p class="faq-answer">{{ faq.a }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.faq-section {
  width: 100%;
  padding: 120px 24px;
  background-color: var(--color-bg);
  border-top: 1px solid var(--color-border);
}

.faq-container {
  max-width: 860px;
  margin: 0 auto;
}

.accordion-wrapper {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 48px;
}

.accordion-item {
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg, 12px);
  overflow: hidden;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.accordion-item:hover {
  border-color: var(--color-primary);
}

.accordion-item.is-open {
  border-color: var(--color-primary);
  box-shadow: 0 4px 16px -2px rgba(15, 118, 110, 0.08);
}

.accordion-trigger {
  width: 100%;
  padding: 22px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: transparent;
  border: none;
  cursor: pointer;
  text-align: left;
  transition: background-color var(--transition-fast);
}

.accordion-trigger:hover {
  background-color: rgba(15, 118, 110, 0.02);
}

.faq-question {
  font-family: var(--font-sans);
  font-size: 1.05rem;
  font-weight: 600;
  color: var(--color-text);
  line-height: 1.4;
}

.faq-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: var(--color-bg);
  border: 1px solid var(--color-border);
  color: var(--color-primary);
  flex-shrink: 0;
  transition: transform var(--transition-fast), background-color var(--transition-fast);
}

.accordion-item.is-open .faq-icon {
  background-color: var(--color-primary);
  color: var(--color-primary-contrast, #FFF);
  border-color: var(--color-primary);
}

.faq-icon-svg .vertical-line {
  transition: transform 0.2s ease, opacity 0.2s ease;
  transform-origin: center;
}

.faq-icon-svg.is-rotated .vertical-line {
  transform: scaleY(0);
  opacity: 0;
}

.accordion-content {
  padding: 0 24px 22px;
  border-top: 1px solid rgba(0, 0, 0, 0.04);
}

.faq-answer {
  font-family: var(--font-sans);
  font-size: 0.9375rem;
  color: var(--color-text-muted);
  line-height: 1.65;
  margin: 14px 0 0;
}

@media (max-width: 640px) {
  .faq-section {
    padding: 72px 16px;
  }

  .accordion-trigger {
    padding: 18px 18px;
  }

  .faq-question {
    font-size: 0.95rem;
  }

  .accordion-content {
    padding: 0 18px 18px;
  }

  .faq-answer {
    font-size: 0.875rem;
  }
}
</style>
