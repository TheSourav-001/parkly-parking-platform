/**
 * Parkly Design System Tokens
 * Reference constants for JavaScript/Vue usage where CSS variables are not directly applicable.
 */
export const tokens = {
  colors: {
    light: {
      primary: '#0F766E',
      primaryHover: '#0D655E',
      primaryActive: '#0A524D',
      background: '#F8FAFC',
      surface: '#FFFFFF',
      surfaceSubtle: '#F1F5F9',
      text: '#0F172A',
      mutedText: '#64748B',
      border: '#E2E8F0',
      borderFocus: '#0F766E',
      ring: 'rgba(15, 118, 110, 0.25)',
      error: '#DC2626',
      errorBg: '#FEF2F2',
      success: '#16A34A',
    },
    dark: {
      primary: '#14B8A6',
      primaryHover: '#2DD4BF',
      primaryActive: '#0D9488',
      background: '#0B1220',
      surface: '#111C2E',
      surfaceSubtle: '#19273C',
      text: '#F1F5F9',
      mutedText: '#9CAEC2',
      border: '#26364C',
      borderFocus: '#14B8A6',
      ring: 'rgba(20, 184, 166, 0.3)',
      error: '#F87171',
      errorBg: 'rgba(239, 68, 68, 0.15)',
      success: '#34D399',
    },
  },
  typography: {
    fontSans: "Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif",
    fontMono: "ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace",
  },
  breakpoints: {
    sm: '640px',
    md: '768px',
    lg: '1024px',
    xl: '1280px',
    '2xl': '1536px',
  },
  radii: {
    sm: '4px',
    md: '8px',
    lg: '12px',
    xl: '16px',
    full: '9999px',
  },
  shadows: {
    sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
    md: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)',
    lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)',
  },
}

