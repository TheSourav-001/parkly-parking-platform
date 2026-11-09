<script setup>
/**
 * Main Application Shell for Parkly Frontend
 * Integrates shared Navbar, active content area, and Footer.
 */
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'
import DesignSystemShowcase from './components/DesignSystemShowcase.vue'
</script>

<template>
  <div class="parkly-app">
    <Navbar />
    <div class="content-container">
      <DesignSystemShowcase />
    </div>
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
}

.content-container {
  flex: 1 0 auto;
  display: flex;
  flex-direction: column;
}
</style>
