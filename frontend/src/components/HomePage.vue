<script setup>
/**
 * HomePage Component
 * Orchestrates the full redesigned homepage experience:
 * 1. HeroSection (Asymmetric, Circular Masks, Satellite CTA, Orbital Arcs)
 * 2. SearchWidget (Elevated Pill Bar + Ask Parkly AI)
 * 3. FeaturesSection (4 Architecture Blocks)
 * 4. HowItWorksSection (4 Step Connected Nodes)
 * 5. LocationsSection (4 Stadium Cards with Satellite Arrow CTAs)
 * 6. FeaturedFacilitiesSection (Verified Garages & Live Slots)
 * 7. CtaBannerSection (40px Stadium Banner)
 */
import { ref } from 'vue'
import HeroSection from './home/HeroSection.vue'
import SearchWidget from './home/SearchWidget.vue'
import FeaturesSection from './home/FeaturesSection.vue'
import HowItWorksSection from './home/HowItWorksSection.vue'
import LocationsSection from './home/LocationsSection.vue'
import FeaturedFacilitiesSection from './home/FeaturedFacilitiesSection.vue'
import CtaBannerSection from './home/CtaBannerSection.vue'

const activeFeedback = ref('')

function handleScrollTo(id) {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

function handleSearch(params) {
  activeFeedback.value = `Searching parking near "${params.location}" for ${params.vehicle} (${params.duration} hrs)...`
  handleScrollTo('featured')
  setTimeout(() => {
    activeFeedback.value = ''
  }, 4000)
}

function handleAiSearch(query) {
  activeFeedback.value = `AI Recommendation Engine processing: "${query}"...`
  handleScrollTo('featured')
  setTimeout(() => {
    activeFeedback.value = ''
  }, 4000)
}

function handleSelectLocation(loc) {
  activeFeedback.value = `Selected region: ${loc.name}. Loading ${loc.registeredFacilities} parking hubs...`
  handleScrollTo('featured')
  setTimeout(() => {
    activeFeedback.value = ''
  }, 4000)
}

function handleBookFacility(facility) {
  activeFeedback.value = `Booking pass requested for "${facility.name}". Reservation modal ready.`
  setTimeout(() => {
    activeFeedback.value = ''
  }, 5000)
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

    <!-- 1. Hero Section -->
    <HeroSection
      @explore="handleScrollTo('discovery')"
      @howItWorks="handleScrollTo('how-it-works')"
    />

    <!-- 2. Search Widget -->
    <SearchWidget
      @search="handleSearch"
      @aiSearch="handleAiSearch"
    />

    <!-- 3. Core Features Section -->
    <FeaturesSection />

    <!-- 4. How It Works Section -->
    <HowItWorksSection />

    <!-- 5. Popular Locations Section -->
    <LocationsSection
      @selectLocation="handleSelectLocation"
    />

    <!-- 6. Featured Facilities Showcase -->
    <FeaturedFacilitiesSection
      @bookFacility="handleBookFacility"
    />

    <!-- 7. Call To Action Stadium Banner -->
    <CtaBannerSection
      @action="handleScrollTo('discovery')"
    />
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

