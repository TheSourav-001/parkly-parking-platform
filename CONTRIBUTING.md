# Contributing to Parkly

Welcome to Parkly! To ensure platform stability, accessible navigation, and high code quality across our Vue 3 frontend and Laravel backend, please adhere to the following development standards.

---

## 1. Vue 3 Lifecycle & Navigation Safety Guidelines

### 1.1 Never Use Raw `removeChild` or Unsafe DOM Manipulation
Direct DOM manipulation inside Vue-rendered components can conflict with Vue's virtual DOM reconciliation and patch pipeline:
- **Forbidden**: `node.parentNode.removeChild(node.parentNode)` or `parentNode.removeChild(...)`.
- **Reason**: Mistakenly targeting `document.head` or an already detached node throws a `NotFoundError`, which aborts Vue's unmount phase. When unmount is aborted, Vue Router updates the browser URL (via `history.pushState`), but the DOM view fails to swap, freezing the user on the previous page.
- **Allowed**: Use `element?.remove()` guarded with `if (element) { ... }`, or use unique element IDs (e.g., `document.getElementById('parkly-faq-jsonld')?.remove()`). Run `npm run lint:dom` to audit your code.

### 1.2 Unmount Hook Hygiene (`onBeforeUnmount` / `onUnmounted`)
Any component creating side effects MUST clean them up, wrapped defensively in `try / catch`:
- **Window & Document Event Listeners**: Always save a named function reference when attaching with `addEventListener`, and remove it in `onBeforeUnmount` with `removeEventListener`.
- **Animation Timelines (GSAP, Anime.js)**:
  - GSAP: Wrap context creation with `ctx = gsap.context(...)` and clean up with `if (ctx) ctx.revert()`.
  - Anime.js: Pause active animations with `timeline?.pause()`.
  - Always guard with `try { ... } catch (err) { console.warn(...) }`.
- **Leaflet & Third-Party Maps**: Call `map.remove()` inside `try / catch` in `onUnmounted` and set `map = null`.
- **Observers & Timers**: Disconnect `ResizeObserver`, `IntersectionObserver`, and clear `setTimeout`, `setInterval`, and `cancelAnimationFrame`.

### 1.3 Route Transitions & Root Nodes
- Every view component rendered inside `<router-view>` must have a single root element. Fragment roots or comment nodes at the root of a transitioned view trigger DOM patch errors.
- `<RouteErrorBoundary>` is wrapped around `<router-view>` in `App.vue` to intercept and safely isolate any unexpected rendering crashes.

---

## 2. Parkly Product & Data Honesty Principles

- **No False Claims**:
  - Availability is **operator-reported**, not real-time guaranteed.
  - Do NOT invent or hardcode mock pricing ranges or facility counts in fallback composables.
  - When backend rate APIs (such as `/api/pricing/ranges`) return empty results or are offline, show the honest fallback: *"Rates not published yet"* with a **Retry** action.
- **Minimalist Premium Design**:
  - No emojis in UI text.
  - Light/dark parity with WCAG AA compliance.
  - Use theme design tokens (`--color-bg`, `--color-primary`, `--color-border`, `--color-surface`, `--color-muted`).

---

## 3. Verification & Testing Checklist

Before submitting changes, run all automated checks:

```bash
# 1. Run DOM Safety Audit
npm run lint:dom

# 2. Run Component Lifecycle & Unmount Unit Tests (Vitest)
npm run test

# 3. Run Navigation Matrix E2E Tests (Playwright)
npm run test:e2e

# 4. Verify Frontend Production Build
npm run build

# 5. Run Backend Feature Tests (Laravel)
cd backend && php artisan test
```

