<script setup>
/**
 * Navbar Component
 * Shared Top Navigation bar for Parkly.
 * Modern minimal layout with logo, tagline, navigation items, theme toggle, and CTAs.
 */
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ThemeToggle from '../common/ThemeToggle.vue'
import AppButton from '../common/AppButton.vue'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const isMobileMenuOpen = ref(false)
const isLoggingOut = ref(false)

function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

async function handleLogout() {
  isLoggingOut.value = true
  try {
    await authStore.logout()
    router.push('/login')
  } finally {
    isLoggingOut.value = false
    isMobileMenuOpen.value = false
  }
}
</script>

<template>
  <header class="parkly-navbar">
    <div class="navbar-container">
      <!-- Brand Logo & Tagline -->
      <div class="brand-group">
        <a href="/" class="brand-link" aria-label="Parkly Home">
          <div class="logo-mark" aria-hidden="true">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
              <rect width="32" height="32" rx="8" fill="currentColor" class="logo-bg" />
              <path
                d="M10 23V9h6.2a4.8 4.8 0 0 1 4.8 4.8v.4a4.8 4.8 0 0 1-4.8 4.8H14v4h-4zm4-7.8h2.2a1.8 1.8 0 0 0 1.8-1.8v-.4a1.8 1.8 0 0 0-1.8-1.8H14v4z"
                fill="#FFFFFF"
                class="logo-letter"
              />
            </svg>
          </div>
          <div class="brand-text">
            <span class="brand-name">Parkly</span>
            <span class="brand-tagline">Find. Book. Park.</span>
          </div>
        </a>
      </div>

      <!-- Desktop Navigation Links -->
      <nav class="nav-links" aria-label="Main Navigation">
        <a href="#discovery" class="nav-link">Find Parking</a>
        <a href="#how-it-works" class="nav-link">How It Works</a>
        <a href="#locations" class="nav-link">Locations</a>
        <a href="#support" class="nav-link">Support</a>
      </nav>

      <!-- Right Controls: Theme Toggle & Actions -->
      <div class="nav-actions">
        <ThemeToggle />

        <!-- Unauthenticated Guest CTAs -->
        <template v-if="!authStore.isAuthenticated">
          <router-link to="/login">
            <AppButton variant="ghost" size="sm" class="btn-signin">Sign In</AppButton>
          </router-link>
          <router-link to="/register">
            <AppButton variant="primary" size="sm">Get Started</AppButton>
          </router-link>
        </template>

        <!-- Authenticated User Profile & Logout -->
        <template v-else>
          <div class="user-badge" :title="authStore.userEmail">
            <span class="user-name">{{ authStore.userName }}</span>
            <span class="user-role">{{ authStore.userRole }}</span>
          </div>
          <AppButton
            variant="ghost"
            size="sm"
            class="btn-logout"
            :loading="isLoggingOut"
            @click="handleLogout"
          >
            Logout
          </AppButton>
        </template>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          class="mobile-menu-btn"
          :aria-expanded="isMobileMenuOpen"
          aria-label="Toggle menu"
          @click="toggleMobileMenu"
        >
          <svg
            v-if="!isMobileMenuOpen"
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
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
            width="22"
            height="22"
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

    <!-- Mobile Navigation Drawer -->
    <div v-if="isMobileMenuOpen" class="mobile-drawer" role="navigation" aria-label="Mobile Navigation">
      <a href="#discovery" class="mobile-nav-link" @click="isMobileMenuOpen = false">Find Parking</a>
      <a href="#how-it-works" class="mobile-nav-link" @click="isMobileMenuOpen = false">How It Works</a>
      <a href="#locations" class="mobile-nav-link" @click="isMobileMenuOpen = false">Locations</a>
      <a href="#support" class="mobile-nav-link" @click="isMobileMenuOpen = false">Support</a>
      <div class="mobile-drawer-actions">
        <template v-if="!authStore.isAuthenticated">
          <router-link to="/login" @click="isMobileMenuOpen = false">
            <AppButton variant="secondary" size="md" block>Sign In</AppButton>
          </router-link>
          <router-link to="/register" @click="isMobileMenuOpen = false">
            <AppButton variant="primary" size="md" block>Get Started</AppButton>
          </router-link>
        </template>
        <template v-else>
          <div class="mobile-user-info">
            <div class="user-name">{{ authStore.userName }}</div>
            <div class="user-email">{{ authStore.userEmail }}</div>
          </div>
          <AppButton
            variant="secondary"
            size="md"
            block
            :loading="isLoggingOut"
            @click="handleLogout"
          >
            Logout
          </AppButton>
        </template>
      </div>
    </div>
  </header>
</template>

<style scoped>
.parkly-navbar {
  position: sticky;
  top: 0;
  z-index: 50;
  width: 100%;
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  backdrop-filter: blur(8px);
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal);
}

.navbar-container {
  max-width: var(--container-max-width);
  margin: 0 auto;
  padding: 0 var(--space-4);
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
}

/* --- Brand --- */
.brand-group {
  display: flex;
  align-items: center;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  text-decoration: none;
  color: inherit;
}

.brand-link:hover {
  text-decoration: none;
}

.logo-mark {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-primary);
}

.logo-icon {
  width: 100%;
  height: 100%;
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-name {
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.brand-tagline {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  font-weight: var(--font-weight-medium);
}

/* --- Desktop Nav Links --- */
.nav-links {
  display: flex;
  align-items: center;
  gap: var(--space-6);
}

.nav-link {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-text-muted);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.nav-link:hover {
  color: var(--color-primary);
  text-decoration: none;
}

/* --- Actions --- */
.nav-actions {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.user-badge {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin-right: var(--space-2);
}

.user-name {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-text);
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-role {
  font-size: 10px;
  text-transform: uppercase;
  color: var(--color-primary);
  font-weight: var(--font-weight-semibold);
  letter-spacing: 0.05em;
}

.mobile-user-info {
  margin-bottom: var(--space-3);
  padding: var(--space-3);
  background-color: var(--color-bg);
  border-radius: var(--radius-md);
  text-align: left;
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
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text);
  cursor: pointer;
}

/* --- Mobile Drawer --- */
.mobile-drawer {
  display: none;
  flex-direction: column;
  padding: var(--space-4);
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  gap: var(--space-3);
}

.mobile-nav-link {
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-medium);
  color: var(--color-text);
  padding: var(--space-2) 0;
  text-decoration: none;
  border-bottom: 1px solid var(--color-surface-subtle);
}

.mobile-drawer-actions {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
  margin-top: var(--space-2);
}

/* Responsive Breakpoint: Mobile (< 768px) */
@media (max-width: 768px) {
  .nav-links {
    display: none;
  }

  .btn-signin {
    display: none;
  }

  .mobile-menu-btn {
    display: inline-flex;
  }

  .mobile-drawer {
    display: flex;
  }
}
</style>

