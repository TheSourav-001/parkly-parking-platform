<script setup>
/**
 * Main Application Shell for Parkly Frontend
 * Renders the unified Navbar across all routes
 * and Footer on standard routes.
 */
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Navbar from './components/layout/Navbar.vue'
import SiteFooter from './components/layout/SiteFooter.vue'
import AppSplashScreen from './components/common/AppSplashScreen.vue'

const route = useRoute()
const router = useRouter()

const isAuthRoute = computed(() => Boolean(route.meta && route.meta.isAuth))
const isProfileRoute = computed(() => route.name === 'profile')

function scrollOrNavigate(targetId) {
  const el = document.getElementById(targetId)

  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  } else {
    router.push({ path: '/', hash: `#${targetId}` })
  }
}

function handleOpenSearch() {
  scrollOrNavigate('discovery')
}

function handleNavigate(targetId) {
  scrollOrNavigate(targetId)
}
</script>

<template>
  <div class="parkly-app" :class="{ 'is-auth-view': isAuthRoute }">
    <!-- App Entrance / Refresh Loading Animation -->
    <AppSplashScreen />

    <Navbar
      @openSearch="handleOpenSearch"
      @navigate="handleNavigate"
    />

    <main class="content-container">
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <SiteFooter v-if="!isAuthRoute && !isProfileRoute" />
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

.parkly-app.is-auth-view {
  min-height: 100vh;
  min-height: 100dvh;
}

.content-container {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}

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
