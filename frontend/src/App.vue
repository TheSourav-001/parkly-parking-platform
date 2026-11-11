<script setup>
/**
 * Main Application Shell for Parkly Frontend
 * Integrates floating pill Navbar, Homepage, and dark editorial Footer.
 */
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'
import HomePage from './components/HomePage.vue'

function handleOpenSearch() {
  const el = document.getElementById('discovery')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

function handleNavigate(targetId) {
  const el = document.getElementById(targetId)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <div class="parkly-app">
    <!-- Floating Pill Navigation -->
    <Navbar
      @openSearch="handleOpenSearch"
      @navigate="handleNavigate"
    />

    <!-- Main Content Area -->
    <div class="content-container">
      <HomePage />
    </div>

    <!-- Editorial Dark Footer -->
    <Footer />
  </div>
</template>

<style scoped>
.parkly-app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-height: 100dvh;
  width: 100%;
  background-color: var(--color-bg);
  color: var(--color-text);
  position: relative;
}

.content-container {
  flex: 1 0 auto;
  display: flex;
  flex-direction: column;
}
</style>
