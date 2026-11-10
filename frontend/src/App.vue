<script setup>
/**
 * Main Application Shell for Parkly Frontend
 * Integrates floating pill Navbar, Homepage, and dark editorial Footer.
 * Also offers an optional toggle to inspect the Design System Showcase.
 */
import { ref } from 'vue'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'
import HomePage from './components/HomePage.vue'
import DesignSystemShowcase from './components/DesignSystemShowcase.vue'

const showShowcase = ref(false)

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
      <HomePage v-if="!showShowcase" />
      <DesignSystemShowcase v-else />
    </div>

    <!-- Floating Dev Pill to switch between Homepage and Design System Showcase -->
    <div class="dev-switch-pill">
      <button
        type="button"
        class="switch-btn"
        @click="showShowcase = !showShowcase"
      >
        <span class="switch-dot"></span>
        {{ showShowcase ? 'View Redesigned Homepage' : 'View Design System Tokens' }}
      </button>
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

/* Dev switch pill in corner for convenience */
.dev-switch-pill {
  position: fixed;
  bottom: 16px;
  left: 16px;
  z-index: 100;
}

.switch-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: var(--radius-pill, 999px);
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text-muted);
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-fast);
}

.switch-btn:hover {
  background-color: var(--color-surface-subtle);
  color: var(--color-primary);
  border-color: var(--color-primary);
}

.switch-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}
</style>
