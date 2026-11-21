<script setup>
/**
 * Navbar Component — Mastercard-Inspired Floating Pill Navigation
 * Floating pill-shaped bar docked below the viewport top with rounded shoulders.
 * Uses Parkly's existing Navy + Teal tokens & light/dark theme switching.
 * Integrates Vue Router auth links and Pinia auth state.
 */
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ThemeToggle from '../common/ThemeToggle.vue'
import AppButton from '../common/AppButton.vue'
import { useAuthStore } from '../../stores/auth'

defineProps({
  activeSection: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['openSearch', 'navigate'])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const isMobileMenuOpen = ref(false)
const isScrolled = ref(false)

const userInitials = computed(() => {
  const name = authStore.userName
  if (!name) return 'U'
  return name
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

function closeMobileMenu() {
  isMobileMenuOpen.value = false
}

function handleSectionNav(sectionId) {
  closeMobileMenu()
  if (route.path === '/') {
    const el = document.getElementById(sectionId)
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' })
    }
    emit('navigate', sectionId)
  } else {
    router.push({ path: '/', hash: `#${sectionId}` })
  }
}

function handleScroll() {
  isScrolled.value = window.scrollY > 20
}

function handleKeydown(e) {
  if (e.key === 'Escape') closeMobileMenu()
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <header class="parkly-navbar-wrapper" :class="{ 'is-scrolled': isScrolled }">
    <div class="navbar-pill">
      <!-- Brand Logo -->
      <router-link to="/" class="brand-group" aria-label="Parkly Home" @click="closeMobileMenu">
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
      </router-link>

      <!-- Desktop Links: Airy pill spacing -->
      <nav class="nav-links" aria-label="Main Navigation">
        <a href="/#discovery" class="nav-link" @click.prevent="handleSectionNav('discovery')">Find Parking</a>
        <a href="/#how-it-works" class="nav-link" @click.prevent="handleSectionNav('how-it-works')">How It Works</a>
        <a href="/#locations" class="nav-link" @click.prevent="handleSectionNav('locations')">Locations</a>
        <a href="/#featured" class="nav-link" @click.prevent="handleSectionNav('featured')">Featured</a>
        <a href="/#operators" class="nav-link" @click.prevent="handleSectionNav('operators')">For Operators</a>
        <a href="/#support" class="nav-link" @click.prevent="handleSectionNav('support')">Support</a>
      </nav>

      <!-- Right Actions: Search Circle + Theme Toggle + Sign In / User Profile Dropdown -->
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

        <!-- Unauthenticated Guest CTAs -->
        <template v-if="!authStore.isAuthenticated">
          <router-link to="/login" class="nav-signin-link">Sign In</router-link>

          <router-link to="/register">
            <AppButton
              variant="primary"
              size="sm"
              pill
              class="btn-get-started"
            >
              Get Started
            </AppButton>
          </router-link>
        </template>

        <!-- Authenticated User Profile Icon -->
        <template v-else>
          <router-link
            to="/profile"
            class="circle-action-btn profile-icon-btn"
            :title="`${authStore.userName} — My Profile`"
            :aria-label="`Open ${authStore.userName} profile`"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
          </router-link>
        </template>

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
            <a href="/#discovery" class="mobile-link" @click.prevent="handleSectionNav('discovery')">Find Parking</a>
            <a href="/#how-it-works" class="mobile-link" @click.prevent="handleSectionNav('how-it-works')">How It Works</a>
            <a href="/#locations" class="mobile-link" @click.prevent="handleSectionNav('locations')">Popular Locations</a>
            <a href="/#featured" class="mobile-link" @click.prevent="handleSectionNav('featured')">Featured Facilities</a>
            <a href="/#operators" class="mobile-link" @click.prevent="handleSectionNav('operators')">For Operators</a>
            <a href="/#support" class="mobile-link" @click.prevent="handleSectionNav('support')">Support</a>
          </div>
          <div class="mobile-drawer-footer">
            <template v-if="!authStore.isAuthenticated">
              <router-link to="/login" @click="closeMobileMenu">
                <AppButton variant="secondary" size="md" pill block>Sign In</AppButton>
              </router-link>
              <router-link to="/register" @click="closeMobileMenu">
                <AppButton variant="primary" size="md" pill block>Get Started</AppButton>
              </router-link>
            </template>
            <template v-else>
              <div class="mobile-user-info">
                <div class="mobile-user-row">
                  <div class="user-avatar-pill small">
                    <img
                      v-if="authStore.userAvatar"
                      :src="authStore.userAvatar"
                      :alt="authStore.userName"
                      class="avatar-img"
                    />
                    <span v-else class="avatar-fallback">{{ userInitials }}</span>
                  </div>
                  <div>
                    <div class="user-name">{{ authStore.userName }}</div>
                    <div class="user-email">{{ authStore.userEmail }}</div>
                  </div>
                </div>
              </div>
              <router-link to="/profile" @click="closeMobileMenu">
                <AppButton variant="secondary" size="md" pill block>My Profile</AppButton>
              </router-link>
            </template>
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
  max-width: 1240px;
  height: 60px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-pill, 999px);
  padding: 0 10px 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  box-shadow:
    0 10px 30px -10px rgba(15, 23, 42, 0.08),
    0 2px 6px -1px rgba(15, 23, 42, 0.04);
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal),
    box-shadow var(--transition-normal),
    transform var(--transition-normal);
}

:global([data-theme="dark"]) .navbar-pill {
  box-shadow:
    0 14px 34px -10px rgba(0, 0, 0, 0.45),
    0 2px 8px -1px rgba(0, 0, 0, 0.3);
}

/* Brand Group */
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
}

.symbol-orbit-dot {
  position: absolute;
  top: -1px;
  right: -1px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: var(--color-accent);
  box-shadow: 0 0 8px var(--color-accent);
}

.brand-name {
  font-family: var(--font-family-display, inherit);
  font-size: 1.15rem;
  font-weight: 700;
  letter-spacing: -0.025em;
  color: var(--color-text);
}

/* Nav Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 22px;
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

.profile-icon-btn {
  text-decoration: none;
}

.profile-icon-btn.router-link-active {
  border-color: var(--color-primary);
  background-color: var(--color-primary-light);
  color: var(--color-primary);
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

/* ============================================================
   AUTHENTICATED PROFILE AVATAR LINK
   ============================================================ */
.profile-avatar-wrapper {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.user-profile-trigger {
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: 1px solid transparent;
  padding: 2px;
  border-radius: var(--radius-pill, 999px);
  cursor: pointer;
  color: var(--color-text);
  text-decoration: none;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast),
    transform var(--transition-fast);
  outline: none;
}

.user-profile-trigger:hover {
  background-color: var(--color-surface-subtle);
  border-color: var(--color-border);
  transform: scale(1.04);
}

.user-profile-trigger:focus-visible {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
}

.user-avatar-pill {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), #1e293b);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: var(--font-weight-bold);
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(15, 118, 110, 0.2);
}

.user-avatar-pill.small {
  width: 36px;
  height: 36px;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-name {
  font-size: 13px;
  font-weight: var(--font-weight-medium);
  color: var(--color-text);
  max-width: 180px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  line-height: 1.2;
}

/* Mobile Drawer Info */
.mobile-user-info {
  margin-bottom: var(--space-3);
  padding: var(--space-3);
  background-color: var(--color-bg);
  border-radius: var(--radius-md);
  text-align: left;
}

.mobile-user-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mobile-user-info .user-name {
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
}

.mobile-user-info .user-email {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  word-break: break-all;
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
  -webkit-backdrop-filter: blur(6px);
  z-index: 200;
  padding: 16px;
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

.mobile-drawer-pill {
  width: 100%;
  max-width: 480px;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 28px;
  padding: 20px 24px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.mobile-drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--color-border);
}

.mobile-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  color: var(--color-primary);
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: var(--color-accent);
}

.mobile-drawer-close {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  line-height: 1;
  color: var(--color-text-muted);
  cursor: pointer;
  padding: 0 4px;
}

.mobile-drawer-links {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.mobile-link {
  font-size: 1rem;
  font-weight: 500;
  color: var(--color-text);
  text-decoration: none;
  padding: 10px 12px;
  border-radius: 12px;
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
  padding-top: 12px;
  border-top: 1px solid var(--color-border);
}

/* Drawer Transitions */
.drawer-fade-enter-active,
.drawer-fade-leave-active {
  transition: opacity 0.2s ease;
}

.drawer-fade-enter-from,
.drawer-fade-leave-to {
  opacity: 0;
}

.drawer-fade-enter-active .mobile-drawer-pill,
.drawer-fade-leave-active .mobile-drawer-pill {
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.drawer-fade-enter-from .mobile-drawer-pill {
  transform: translateY(-16px) scale(0.97);
}

.drawer-fade-leave-to .mobile-drawer-pill {
  transform: translateY(-8px) scale(0.98);
}

/* Responsive: Hide desktop links on tablet/mobile */
@media (max-width: 992px) {
  .nav-links {
    display: none;
  }
}

@media (max-width: 640px) {
  .parkly-navbar-wrapper {
    padding: 10px 14px;
  }

  .navbar-pill {
    padding: 0 8px 0 16px;
    height: 54px;
  }

  .nav-signin-link,
  .btn-get-started {
    display: none;
  }

  .mobile-menu-btn {
    display: inline-flex;
  }
}
</style>
