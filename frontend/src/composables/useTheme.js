/**
 * useTheme composable for Parkly design system.
 * Supports 'light', 'dark', or 'system' modes, persists preference in localStorage,
 * and updates document.documentElement data-theme attribute.
 */
import { ref, onMounted } from 'vue'

const THEME_STORAGE_KEY = 'parkly-theme-preference'

// Global reactive state shared across all components
const currentTheme = ref('light')
const themePreference = ref('system')

function applyTheme(theme) {
  const root = document.documentElement
  let resolvedTheme = theme

  if (theme === 'system') {
    resolvedTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  root.setAttribute('data-theme', resolvedTheme)
  currentTheme.value = resolvedTheme
  themePreference.value = theme
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

    if (saved && (saved === 'light' || saved === 'dark' || saved === 'system')) {
      applyTheme(saved)
    } else {
      applyTheme('system')
    }

    // Listen for OS theme changes if user has system preference
    if (window.matchMedia) {
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        let currentSaved = null
        try {
          currentSaved = localStorage.getItem(THEME_STORAGE_KEY)
        } catch {
          // Ignore
        }
        if (!currentSaved || currentSaved === 'system') {
          applyTheme('system')
        }
      })
    }
  }

  return {
    currentTheme,
    themePreference,
    setTheme,
    toggleTheme,
    initTheme,
  }
}

