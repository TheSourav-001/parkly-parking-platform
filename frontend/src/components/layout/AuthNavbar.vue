<script setup>
/**
 * AuthNavbar Component
 * Ultra-compact top navigation header dedicated for Authentication pages.
 * Height: 56px - 60px.
 * Features:
 * - Compact Parkly Logo & Tagline.
 * - Subtle "Back to Home" navigation link.
 * - Persistent ThemeToggle.
 * - Completely avoids regular marketing links to guarantee a single-viewport fit.
 */
import ThemeToggle from '../common/ThemeToggle.vue'
</script>

<template>
  <header class="auth-navbar">
    <div class="auth-nav-container">
      <!-- Brand Logo -->
      <router-link to="/" class="brand-link" aria-label="Parkly Home">
        <div class="logo-mark" aria-hidden="true">
          <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
            <rect width="32" height="32" rx="8" fill="currentColor" />
            <path
              d="M10 23V9h6.2a4.8 4.8 0 0 1 4.8 4.8v.4a4.8 4.8 0 0 1-4.8 4.8H14v4h-4zm4-7.8h2.2a1.8 1.8 0 0 0 1.8-1.8v-.4a1.8 1.8 0 0 0-1.8-1.8H14v4z"
              fill="#FFFFFF"
            />
          </svg>
        </div>
        <div class="brand-titles">
          <span class="brand-name">Parkly</span>
          <span class="brand-tagline">Find. Book. Park.</span>
        </div>
      </router-link>

      <!-- Right Controls: Back to Home + Theme Toggle -->
      <div class="nav-controls">
        <router-link to="/" class="back-home-link">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12" />
            <polyline points="12 19 5 12 12 5" />
          </svg>
          <span>Back to Home</span>
        </router-link>
        <ThemeToggle />
      </div>
    </div>
  </header>
</template>

<style scoped>
.auth-navbar {
  height: 58px;
  width: 100%;
  flex-shrink: 0;
  background-color: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
  display: flex;
  align-items: center;
  z-index: 40;
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal);
}

.auth-nav-container {
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 0 var(--space-4);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand-link {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  text-decoration: none;
  color: inherit;
}

.logo-mark {
  width: 28px;
  height: 28px;
  color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-icon {
  width: 100%;
  height: 100%;
}

.brand-titles {
  display: flex;
  flex-direction: column;
}

.brand-name {
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.brand-tagline {
  font-size: 0.65rem;
  color: var(--color-text-muted);
  font-weight: var(--font-weight-medium);
}

.nav-controls {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.back-home-link {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  text-decoration: none;
  font-weight: var(--font-weight-medium);
  padding: var(--space-1) var(--space-2);
  border-radius: var(--radius-sm);
  transition: color var(--transition-fast), background-color var(--transition-fast);
}

.back-home-link:hover {
  color: var(--color-primary);
  background-color: var(--color-surface-subtle);
}

@media (max-width: 640px) {
  .back-home-link span {
    display: none;
  }
}
</style>

