/**
 * useTheme composable for Parkly design system.
 * Supports 'light', 'dark', or 'system' modes, persists preference in localStorage,
 * and updates document.documentElement data-theme attribute.
 */
import { ref, onMounted } from 'vue'

const THEME_STORAGE_KEY = 'parkly-theme-preference'

// Global reactive state shared across all components
const currentTheme = ref('light')

function applyTheme(theme) {
  const root = document.documentElement
  let resolvedTheme = theme

  if (theme === 'system') {
    resolvedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  root.setAttribute('data-theme', resolvedTheme)
  currentTheme.value = resolvedTheme
}

export function useTheme() {
  function setTheme(theme) {
    try {
      localStorage.setItem(THEME_STORAGE_KEY, theme)
    } catch {
      // Ignore localStorage failure in restricted contexts
    }
    applyTheme(theme)
  }

  function toggleTheme() {
    const nextTheme = currentTheme.value === 'dark' ? 'light' : 'dark'
    setTheme(nextTheme)
  }

  function initTheme() {
    let saved = null
    try {
      saved = localStorage.getItem(THEME_STORAGE_KEY)
    } catch {
      // Ignore
    }

    if (saved && (saved === 'light' || saved === 'dark')) {
      applyTheme(saved)
    } else {
      // Detect system preference as default
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
      const initial = prefersDark ? 'dark' : 'light'
      applyTheme(initial)
    }

    // Listen for OS theme changes if user has not explicitly locked light/dark
    if (window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        let currentSaved = null
        try {
          currentSaved = localStorage.getItem(THEME_STORAGE_KEY)
        } catch {
          // Ignore
        }
        if (!currentSaved) {
          applyTheme(e.matches ? 'dark' : 'light')
        }
      })
    }
  }

  return {
    currentTheme,
    setTheme,
    toggleTheme,
    initTheme,
  }
}

