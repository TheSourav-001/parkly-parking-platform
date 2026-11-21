<script setup>
/**
 * ThemeToggle Component
 * Allows user to toggle between Light and Dark mode.
 * Remembers the selection in localStorage and respects system settings.
 */
import { useTheme } from '../../composables/useTheme'

const { currentTheme, toggleTheme } = useTheme()
</script>

<template>
  <button
    type="button"
    class="theme-toggle"
    :title="`Switch to ${currentTheme === 'dark' ? 'light' : 'dark'} mode`"
    :aria-label="`Switch to ${currentTheme === 'dark' ? 'light' : 'dark'} mode`"
    @click="toggleTheme"
  >
    <!-- Sun icon (shown in dark mode, click switches to light) -->
    <svg
      v-if="currentTheme === 'dark'"
      xmlns="http://www.w3.org/2000/svg"
      class="theme-icon icon-sun"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <circle cx="12" cy="12" r="4" />
      <path d="M12 2v2" />
      <path d="M12 20v2" />
      <path d="m4.93 4.93 1.41 1.41" />
      <path d="m17.66 17.66 1.41 1.41" />
      <path d="M2 12h2" />
      <path d="M20 12h2" />
      <path d="m6.34 17.66-1.41 1.41" />
      <path d="m19.07 4.93-1.41 1.41" />
    </svg>

    <!-- Moon icon (shown in light mode, click switches to dark) -->
    <svg
      v-else
      xmlns="http://www.w3.org/2000/svg"
      class="theme-icon icon-moon"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
    </svg>
    <span class="sr-only">Toggle theme</span>
  </button>
</template>

<style scoped>
.theme-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background-color: var(--color-surface);
  color: var(--color-text);
  cursor: pointer;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast),
    color var(--transition-fast),
    transform var(--transition-fast);
}

.theme-toggle:hover {
  background-color: var(--color-surface-subtle);
  border-color: var(--color-border-hover);
  color: var(--color-primary);
}

.theme-toggle:active {
  transform: scale(0.96);
}

.theme-icon {
  width: 18px;
  height: 18px;
  transition: transform var(--transition-normal);
}

.theme-toggle:hover .theme-icon {
  transform: rotate(15deg);
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>

