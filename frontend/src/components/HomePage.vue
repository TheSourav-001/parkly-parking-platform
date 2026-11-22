<script setup>
/**
 * HomePage Component
 * Orchestrates the full redesigned homepage experience per Master Prompt:
 * 1. HeroSection (Visual design kept, honest copy updated)
 * 2. SearchWidget (Direct Search + Ask Parkly AI with 3D Map)
 * 3. TrustStrip (Registered facilities, reported slots, live API stats)
 * 4. JourneyStory (600vh signature scroll parallax story with 6 steps)
 * 5. AISearchShowcase (Prompt examples, extracted chips, DB cards, what AI does/doesn't do)
 * 6. VehiclesAndPricing (Car, Motorcycle, Others + Instant server fee estimator)
 * 7. BookingAndInvoice (Book, Sandbox SSLCOMMERZ checkout, Sample invoice + Status Stepper)
 * 8. ForOperators (id: operators, benefits + interactive dashboard mock)
 * 9. DhakaCoverage (id: locations, Dhaka areas grid with facility counts)
 * 10. PrivacyAndSafety (On-click GPS, un-stored payment info, reported availability)
 * 11. RoadmapSection (Planned features: mobile app, ANPR hardware, more cities)
 * 12. FaqSection (id: support, accessible questions & answers)
 * 13. FinalCTA (Find parking & List your garage)
 */
import { ref } from 'vue'
import HeroSection from './home/HeroSection.vue'
import SearchWidget from './home/SearchWidget.vue'
import JourneyStory from './home/JourneyStory.vue'
import BookingAndInvoice from './home/BookingAndInvoice.vue'
import VehicleDeck from './home/VehicleDeck.vue'
import ForOperators from './home/ForOperators.vue'
import DhakaCoverage from './home/DhakaCoverage.vue'
import PrivacyAndSafety from './home/PrivacyAndSafety.vue'
import RoadmapSection from './home/RoadmapSection.vue'
import FaqSection from './home/FaqSection.vue'
import FinalCTA from './home/FinalCTA.vue'

const activeFeedback = ref('')

function handleScrollTo(id) {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

function handleSearch(params) {
  const loc = params?.area || params?.thana || params?.district || params?.location || 'Dhaka'
  activeFeedback.value = `Searching parking in ${loc}...`
  setTimeout(() => {
    activeFeedback.value = ''
  }, 2500)
}

function handleAiSearch(query) {
  activeFeedback.value = `AI Recommendation: "${query}"...`
  setTimeout(() => {
    activeFeedback.value = ''
  }, 2500)
}
</script>

<template>
  <main class="parkly-homepage">
    <!-- Active Toast Feedback Notification -->
    <transition name="toast-slide">
      <div v-if="activeFeedback" class="feedback-toast" role="status">
        <span class="toast-dot"></span>
        <span class="toast-text">{{ activeFeedback }}</span>
      </div>
    </transition>

    <!-- 1. Hero Section (Visuals preserved, copy corrected) -->
    <HeroSection
      @explore="handleScrollTo('discovery')"
      @howItWorks="handleScrollTo('journey')"
    />

    <!-- 2. Search & Map Widget (Direct Search & Ask Parkly AI) -->
    <SearchWidget
      @search="handleSearch"
      @aiSearch="handleAiSearch"
    />

    <!-- 3. Signature Scroll Story (Replaces old 4 steps) -->
    <JourneyStory />

    <!-- 4. Booking & Invoice (Sandbox demo, invoice mock & lifecycle stepper) -->
    <BookingAndInvoice />

    <!-- 5. Vehicle Classes & Server-Calculated Pricing (Full-Window Scroll-Stacking Card Deck) -->
    <VehicleDeck />

    <!-- 8. For Operators (id: operators) -->
    <ForOperators />

    <!-- 9. Dhaka Coverage (id: locations) -->
    <DhakaCoverage />

    <!-- 10. Privacy & Safety -->
    <PrivacyAndSafety />

    <!-- 11. Product Roadmap (Planned Features) -->
    <RoadmapSection />

    <!-- 12. Frequently Asked Questions (id: support) -->
    <FaqSection />

    <!-- 13. Final Call To Action -->
    <FinalCTA />
  </main>
</template>

<style scoped>
.parkly-homepage {
  width: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  background-color: var(--color-bg);
}

/* Toast Feedback */
.feedback-toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 999;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background-color: var(--color-surface);
  border: 1.5px solid var(--color-primary);
  border-radius: var(--radius-pill, 999px);
  padding: 10px 20px;
  box-shadow: var(--shadow-elevation);
  backdrop-filter: blur(8px);
}

.toast-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.toast-text {
  font-size: 0.84375rem;
  font-weight: 500;
  color: var(--color-text);
}

.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all var(--transition-normal);
}

.toast-slide-enter-from,
.toast-slide-leave-to {
  opacity: 0;
  transform: translateY(16px);
}
</style>
