<script setup>
/**
 * Main Application Shell for Parkly Frontend
 * Dynamically renders the compact AuthNavbar on authentication routes (/login, /register, etc.)
 * while hiding the heavy marketing links and footer to ensure a pristine full-viewport experience.
 */
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/layout/Navbar.vue'
import AuthNavbar from './components/layout/AuthNavbar.vue'
import Footer from './components/layout/Footer.vue'

const route = useRoute()
const isAuthRoute = computed(() => Boolean(route.meta && route.meta.isAuth))
</script>

<template>
  <div class="parkly-app" :class="{ 'is-auth-view': isAuthRoute }">
    <!-- Render AuthNavbar on auth pages, standard Navbar elsewhere -->
    <AuthNavbar v-if="isAuthRoute" />
    <Navbar v-else />

    <main class="content-container">
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Marketing Footer is hidden on authentication routes -->
    <Footer v-if="!isAuthRoute" />
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

.parkly-app.is-auth-view {
  height: 100vh;
  height: 100dvh;
  overflow: hidden;
}

@media (max-width: 960px) {
  .parkly-app.is-auth-view {
    height: auto;
    min-height: 100dvh;
    overflow-y: auto;
  }
}

.content-container {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}

/* Smooth, modern page transition */
.page-fade-enter-active,
.page-fade-leave-active {
  transition: opacity var(--transition-fast), transform var(--transition-fast);
}

.page-fade-enter-from {
  opacity: 0;
  transform: translateY(4px);
}

.page-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
