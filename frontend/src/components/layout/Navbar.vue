<script setup>
/**
 * Navbar Component — Mastercard-Inspired Floating Pill Navigation
 * Floating pill-shaped bar docked below the viewport top with rounded shoulders.
 * Uses Parkly's existing Navy + Teal tokens & light/dark theme switching.
 */
import { ref, onMounted, onUnmounted } from 'vue'
import ThemeToggle from '../common/ThemeToggle.vue'
import AppButton from '../common/AppButton.vue'

defineProps({
  activeSection: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['openSearch', 'navigate'])

const isMobileMenuOpen = ref(false)
const isScrolled = ref(false)

function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

function closeMobileMenu() {
  isMobileMenuOpen.value = false
}

function handleScroll() {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <header class="parkly-navbar-wrapper" :class="{ 'is-scrolled': isScrolled }">
    <div class="navbar-pill">
      <!-- Brand Logo -->
      <a href="#" class="brand-group" aria-label="Parkly Home" @click="closeMobileMenu">
        <div class="brand-symbol" aria-hidden="true">
          <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="symbol-svg">
            <rect width="32" height="32" rx="10" fill="currentColor" class="symbol-bg" />
            <path
              d="M10 23V9h6.2a4.8 4.8 0 0 1 4.8 4.8v.4a4.8 4.8 0 0 1-4.8 4.8H14v4h-4zm4-7.8h2.2a1.8 1.8 0 0 0 1.8-1.8v-.4a1.8 1.8 0 0 0-1.8-1.8H14v4z"
              fill="#FFFFFF"
              class="symbol-letter"
            />
          </svg>
          <span class="symbol-orbit-dot" aria-hidden="true"></span>
        </div>
        <div class="brand-text">
          <span class="brand-name">Parkly</span>
        </div>
      </a>

      <!-- Desktop Links: Airy pill spacing -->
      <nav class="nav-links" aria-label="Main Navigation">
        <a href="#discovery" class="nav-link">Find Parking</a>
        <a href="#how-it-works" class="nav-link">How It Works</a>
        <a href="#locations" class="nav-link">Locations</a>
        <a href="#featured" class="nav-link">Featured</a>
        <a href="#operators" class="nav-link">For Operators</a>
        <a href="#support" class="nav-link">Support</a>
      </nav>

      <!-- Right Actions: Search Circle + Theme Toggle + Sign In + Get Started -->
      <div class="nav-actions">
        <!-- Circular Search Trigger -->
        <button
          type="button"
          class="circle-action-btn search-btn"
          aria-label="Search parking locations"
          title="Search"
          @click="$emit('openSearch')"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="17"
            height="17"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </button>

        <!-- Theme Toggle -->
        <ThemeToggle />

        <!-- Auth CTAs -->
        <a href="#signin" class="nav-signin-link">Sign In</a>

        <AppButton
          variant="primary"
          size="sm"
          pill
          class="btn-get-started"
          @click="$emit('navigate', 'discovery')"
        >
          Get Started
        </AppButton>

        <!-- Mobile Hamburger Button -->
        <button
          type="button"
          class="circle-action-btn mobile-menu-btn"
          :aria-expanded="isMobileMenuOpen"
          aria-label="Toggle navigation menu"
          @click="toggleMobileMenu"
        >
          <svg
            v-if="!isMobileMenuOpen"
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="4" x2="20" y1="12" y2="12" />
            <line x1="4" x2="20" y1="6" y2="6" />
            <line x1="4" x2="20" y1="18" y2="18" />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="18" x2="6" y1="6" y2="18" />
            <line x1="6" x2="18" y1="6" y2="18" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <transition name="drawer-fade">
      <div v-if="isMobileMenuOpen" class="mobile-drawer-overlay" @click.self="closeMobileMenu">
        <div class="mobile-drawer-pill" role="navigation" aria-label="Mobile Navigation">
          <div class="mobile-drawer-header">
            <span class="mobile-eyebrow">
              <span class="eyebrow-dot"></span>
              NAVIGATION
            </span>
            <button class="mobile-drawer-close" aria-label="Close menu" @click="closeMobileMenu">&times;</button>
          </div>
          <div class="mobile-drawer-links">
            <a href="#discovery" class="mobile-link" @click="closeMobileMenu">Find Parking</a>
            <a href="#how-it-works" class="mobile-link" @click="closeMobileMenu">How It Works</a>
            <a href="#locations" class="mobile-link" @click="closeMobileMenu">Popular Locations</a>
            <a href="#featured" class="mobile-link" @click="closeMobileMenu">Featured Facilities</a>
            <a href="#operators" class="mobile-link" @click="closeMobileMenu">For Operators</a>
            <a href="#support" class="mobile-link" @click="closeMobileMenu">Support</a>
          </div>
          <div class="mobile-drawer-footer">
            <AppButton variant="secondary" size="md" pill block @click="closeMobileMenu">Sign In</AppButton>
            <AppButton variant="primary" size="md" pill block @click="closeMobileMenu">Get Started</AppButton>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<style scoped>
.parkly-navbar-wrapper {
  position: sticky;
  top: 0;
  z-index: 100;
  width: 100%;
  padding: 16px 24px;
  pointer-events: none; /* Allows click-through outside the pill */
  display: flex;
  justify-content: center;
  transition: padding var(--transition-normal);
}

.parkly-navbar-wrapper.is-scrolled {
  padding-top: 10px;
}

.navbar-pill {
  pointer-events: auto;
  width: 100%;
  max-width: 1200px;
  height: 60px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 0 16px 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: var(--shadow-nav-pill);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal),
    box-shadow var(--transition-normal),
    transform var(--transition-fast);
}

/* Brand */
.brand-group {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: inherit;
  flex-shrink: 0;
}

.brand-symbol {
  position: relative;
  width: 32px;
  height: 32px;
  color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.symbol-svg {
  width: 100%;
  height: 100%;
  border-radius: 9px;
  display: block;
}

.symbol-orbit-dot {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: var(--color-primary);
  box-shadow: 0 0 0 2px var(--color-surface);
}

.brand-text {
  display: flex;
  align-items: center;
}

.brand-name {
  font-size: 1.15rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  color: var(--color-text);
  line-height: 1;
}

/* Nav links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 32px;
}

.nav-link {
  font-size: 0.875rem;
  font-weight: 500;
  letter-spacing: -0.015em;
  color: var(--color-text-muted);
  text-decoration: none;
  position: relative;
  padding: 6px 2px;
  transition: color var(--transition-fast);
}

.nav-link:hover {
  color: var(--color-text);
  text-decoration: none;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%) scaleX(0);
  width: 16px;
  height: 2px;
  border-radius: 2px;
  background-color: var(--color-primary);
  transition: transform var(--transition-fast);
}

.nav-link:hover::after {
  transform: translateX(-50%) scaleX(1);
}

/* Actions */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.circle-action-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast),
    color var(--transition-fast),
    transform var(--transition-fast);
}

.circle-action-btn:hover {
  border-color: var(--color-border-hover);
  background-color: var(--color-surface-subtle);
  color: var(--color-primary);
}

.circle-action-btn:active {
  transform: scale(0.95);
}

.nav-signin-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-text);
  text-decoration: none;
  padding: 6px 14px;
  border-radius: var(--radius-pill, 999px);
  transition: color var(--transition-fast), background-color var(--transition-fast);
}

.nav-signin-link:hover {
  color: var(--color-primary);
  background-color: var(--color-surface-subtle);
  text-decoration: none;
}

.btn-get-started {
  font-weight: 600;
  padding: 0 18px !important;
  height: 36px !important;
}

.mobile-menu-btn {
  display: none;
}

/* Mobile Drawer Overlay */
.mobile-drawer-overlay {
  pointer-events: auto;
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.45);
  backdrop-filter: blur(6px);
  z-index: 200;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px 16px;
}

.mobile-drawer-pill {
  width: 100%;
  max-width: 480px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 28px;
  padding: 24px;
  box-shadow: var(--shadow-halo);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.mobile-drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 12px;
}

.mobile-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--color-text-muted);
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-primary);
}

.mobile-drawer-close {
  background: none;
  border: none;
  font-size: 28px;
  line-height: 1;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 0 6px;
}

.mobile-drawer-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.mobile-link {
  font-size: 1.05rem;
  font-weight: 500;
  letter-spacing: -0.01em;
  color: var(--color-text);
  padding: 10px 14px;
  border-radius: 12px;
  text-decoration: none;
  transition: background-color var(--transition-fast), color var(--transition-fast);
}

.mobile-link:hover {
  background-color: var(--color-surface-subtle);
  color: var(--color-primary);
  text-decoration: none;
}

.mobile-drawer-footer {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--color-border);
}

/* Drawer Transitions */
.drawer-fade-enter-active,
.drawer-fade-leave-active {
  transition: opacity var(--transition-normal);
}

.drawer-fade-enter-from,
.drawer-fade-leave-to {
  opacity: 0;
}

/* Breakpoints */
@media (max-width: 1080px) {
  .nav-links {
    gap: 18px;
  }
}

@media (max-width: 900px) {
  .nav-links {
    display: none;
  }

  .nav-signin-link {
    display: none;
  }

  .mobile-menu-btn {
    display: inline-flex;
  }
}

@media (max-width: 640px) {
  .parkly-navbar-wrapper {
    padding: 12px 14px;
  }

  .navbar-pill {
    height: 54px;
    padding: 0 10px 0 16px;
  }

  .btn-get-started {
    display: none;
  }
}
</style>
