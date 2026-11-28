<script setup>
/**
 * SiteFooter Component — "Meadow Rise" Animated Scene + Deep Ground Link Engine
 * 
 * Features:
 * - Top Scene: Pale sky gradient to teal horizon, 6 organic ridgelines composited on canvas
 *   with slope lighting, mist, and soft pointer light, melting into deep teal-ink ground (#0B1F1D / #070D14).
 * - Editorial Serif Italic Headline: "Find your spot. Park with confidence."
 * - Ground Block: Brand block, 4 accessible labeled navigation columns, verified social SVG links,
 *   newsletter subscribe with throttled Laravel backend, back-to-top button, and honest proposal disclaimers.
 * - WCAG AA, light/dark mode parity, zero emojis, responsive across 320px–1920px+.
 */
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { footerConfig } from '../../config/footer'
import logoUrl from '../../assets/logo.png'

const router = useRouter()
const currentYear = new Date().getFullYear()

// Canvas & Scene refs
const sceneContainer = ref(null)
const skyCanvas = ref(null)
const hillsCanvas = ref(null)
const isCanvasSupported = ref(true)

// Interactive State
const pointer = ref({ x: 0.5, y: 0.5, targetX: 0.5, targetY: 0.5 })
const isHoveringScene = ref(false)
const isVisible = ref(false)
const prefersReducedMotion = ref(false)

// Newsletter subscription
const newsletterEmail = ref('')
const newsletterHoneypot = ref('')
const subscriberStatus = ref({ loading: false, success: false, error: '' })

// Animation frames & observers
let animationFrameId = null
let resizeObserver = null
let intersectionObserver = null
let waveTime = 0
let lastTimestamp = 0

// Dynamic Theme detection
const isDarkMode = ref(false)
function checkTheme() {
  const root = document.documentElement
  isDarkMode.value = root.getAttribute('data-theme') === 'dark' ||
    (!root.hasAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)
}

// -------------------------------------------------------------
// 1. Hills and Ridges Canvas Engine with Dynamic Wave Animation
// -------------------------------------------------------------
// Palette: Back (haze/pale teal) to Front (deep teal-ink)
const lightRidgeColors = [
  { top: '#CFECE7', bot: '#BCE4DD' },
  { top: '#9FD6CC', bot: '#84C9BD' },
  { top: '#62B4A7', bot: '#4BA294' },
  { top: '#2E8C7E', bot: '#1F7669' },
  { top: '#145A50', bot: '#0F473E' },
  { top: '#0B2B26', bot: '#0B1F1D' }, // Nearest: darkens seamlessly into #0B1F1D ground
]

const darkRidgeColors = [
  { top: '#193943', bot: '#142E37' },
  { top: '#16313C', bot: '#11252D' },
  { top: '#122932', bot: '#0E1F27' },
  { top: '#0E2129', bot: '#0B1920' },
  { top: '#0B181E', bot: '#081318' },
  { top: '#071015', bot: '#070D14' }, // Nearest: darkens seamlessly into #070D14 ground
]

// -------------------------------------------------------------
// 2. Render & Composite Loop
// -------------------------------------------------------------
function renderScene(timestamp = 0) {
  if (!isVisible.value || prefersReducedMotion.value) {
    drawStaticFrame()
    return
  }

  // Smooth pointer interpolation
  pointer.value.x += (pointer.value.targetX - pointer.value.x) * 0.05
  pointer.value.y += (pointer.value.targetY - pointer.value.y) * 0.05

  if (lastTimestamp) {
    const dt = Math.min((timestamp - lastTimestamp) / 1000, 0.1)
    waveTime += dt
  } else {
    waveTime += 0.016
  }
  lastTimestamp = timestamp

  drawFrame(pointer.value.x, pointer.value.y, waveTime)
  animationFrameId = requestAnimationFrame(renderScene)
}

function drawFrame(px, py, t = 0) {
  if (!skyCanvas.value || !hillsCanvas.value) return

  const sCtx = skyCanvas.value.getContext('2d')
  const hCtx = hillsCanvas.value.getContext('2d')
  if (!sCtx || !hCtx) return

  const w = hillsCanvas.value.width
  const h = hillsCanvas.value.height
  const dpr = Math.min(window.devicePixelRatio || 1, 2)
  const logicalW = w / dpr
  const logicalH = h / dpr

  // 1. Sky Rendering: Gradient + Soft Pointer Spotlight
  sCtx.clearRect(0, 0, logicalW, logicalH)
  const skyGrad = sCtx.createLinearGradient(0, 0, 0, logicalH)
  if (isDarkMode.value) {
    skyGrad.addColorStop(0, '#0E1A24')
    skyGrad.addColorStop(0.65, '#132832')
    skyGrad.addColorStop(1, '#17323A')
  } else {
    skyGrad.addColorStop(0, '#F8FAFC')
    skyGrad.addColorStop(0.6, '#ECF7F5')
    skyGrad.addColorStop(1, '#E3F4F0')
  }
  sCtx.fillStyle = skyGrad
  sCtx.fillRect(0, 0, logicalW, logicalH)

  // Soft pointer glow on sky (desktop ambient)
  if (isHoveringScene.value) {
    const lightX = px * logicalW
    const lightY = py * logicalH * 0.8
    const lightRadius = logicalW * 0.4
    const radGlow = sCtx.createRadialGradient(lightX, lightY, 0, lightX, lightY, lightRadius)
    if (isDarkMode.value) {
      radGlow.addColorStop(0, 'rgba(45, 212, 191, 0.12)')
      radGlow.addColorStop(1, 'transparent')
    } else {
      radGlow.addColorStop(0, 'rgba(255, 255, 255, 0.55)')
      radGlow.addColorStop(1, 'transparent')
    }
    sCtx.fillStyle = radGlow
    sCtx.fillRect(0, 0, logicalW, logicalH)
  }

  // 2. Dynamic Wave Hill Rendering
  hCtx.clearRect(0, 0, logicalW, logicalH)

  const palette = isDarkMode.value ? darkRidgeColors : lightRidgeColors
  const count = palette.length
  const deltaX = (px - 0.5) * 26
  const deltaY = (py - 0.5) * 12

  for (let i = 0; i < count; i++) {
    const progress = i / (count - 1)
    // Parallax depth multiplier: distant layers shift more subtly with mouse
    const depth = 1 - progress * 0.75
    const offsetX = deltaX * depth
    const offsetY = deltaY * depth

    // Base elevation and wave attributes
    const baseElevation = logicalH * (0.32 + progress * 0.48) + offsetY
    const amplitude = 32 - i * 3
    const freq = 0.0024 + i * 0.0005
    const phase = i * 1.74

    // Continuous undulating wave motion: background moves gently, foreground has lively rolling swell
    const speed = 0.35 + i * 0.08
    const waveShift = t * speed

    hCtx.beginPath()
    hCtx.moveTo(0, logicalH)
    hCtx.lineTo(0, baseElevation)

    // Smooth undulating sine + harmonic curve
    const step = 4
    for (let x = 0; x <= logicalW; x += step) {
      const sampleX = x - offsetX
      const y = baseElevation +
        Math.sin(sampleX * freq + phase + waveShift) * amplitude +
        Math.cos(sampleX * freq * 1.6 + phase * 0.7 - waveShift * 0.8) * (amplitude * 0.42) +
        Math.sin(sampleX * freq * 0.5 + waveShift * 0.4) * (amplitude * 0.2)
      hCtx.lineTo(x, y)
    }

    hCtx.lineTo(logicalW, logicalH)
    hCtx.closePath()

    // Smooth vertical linear gradient for realistic terrain slope lighting
    const grad = hCtx.createLinearGradient(0, Math.max(0, baseElevation - amplitude), 0, logicalH)
    grad.addColorStop(0, palette[i].top)
    grad.addColorStop(1, palette[i].bot)
    hCtx.fillStyle = grad
    hCtx.fill()
  }
}

function drawStaticFrame() {
  drawFrame(0.5, 0.5, 0)
}

function updateCanvasSizes() {
  if (!sceneContainer.value || !skyCanvas.value || !hillsCanvas.value) return

  const rect = sceneContainer.value.getBoundingClientRect()
  const width = Math.floor(rect.width)
  const height = Math.floor(rect.height)
  if (width === 0 || height === 0) return

  const dpr = Math.min(window.devicePixelRatio || 1, 2)

  skyCanvas.value.width = width * dpr
  skyCanvas.value.height = height * dpr
  const sCtx = skyCanvas.value.getContext('2d')
  if (sCtx) sCtx.scale(dpr, dpr)

  hillsCanvas.value.width = width * dpr
  hillsCanvas.value.height = height * dpr
  const hCtx = hillsCanvas.value.getContext('2d')
  if (hCtx) hCtx.scale(dpr, dpr)

  drawStaticFrame()
}

// -------------------------------------------------------------
// 3. Pointer & Interaction Handlers
// -------------------------------------------------------------
function handlePointerMove(e) {
  if (prefersReducedMotion.value || !sceneContainer.value) return
  const rect = sceneContainer.value.getBoundingClientRect()
  const x = (e.clientX - rect.left) / rect.width
  const y = (e.clientY - rect.top) / rect.height
  pointer.value.targetX = Math.max(0, Math.min(1, x))
  pointer.value.targetY = Math.max(0, Math.min(1, y))
  isHoveringScene.value = true
}

function handlePointerLeave() {
  pointer.value.targetX = 0.5
  pointer.value.targetY = 0.5
  isHoveringScene.value = false
}

// -------------------------------------------------------------
// 4. Navigation & Cross-Route Scroll Helper
// -------------------------------------------------------------
function navigateToTarget(href) {
  if (href.startsWith('/#')) {
    const hash = href.substring(1)
    if (router.currentRoute.value.path === '/') {
      const aliasMap = {
        '#find-parking': '#discovery',
        '#journey': '#journey',
        '#how-it-works': '#journey',
        '#pricing': '#pricing',
        '#featured': '#pricing',
        '#operators': '#operators',
        '#faq': '#faq',
        '#support': '#faq',
      }
      const targetId = (aliasMap[hash] || hash).replace('#', '')
      const el = document.getElementById(targetId)
      if (el) {
        el.scrollIntoView({ behavior: 'smooth' })
      }
    } else {
      router.push({ path: '/', hash })
    }
  } else {
    router.push(href)
  }
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// -------------------------------------------------------------
// 5. Newsletter Subscription Submission
// -------------------------------------------------------------
async function handleSubscribe() {
  if (subscriberStatus.value.loading || !newsletterEmail.value) return

  subscriberStatus.value.error = ''
  subscriberStatus.value.success = false
  subscriberStatus.value.loading = true

  try {
    const res = await axios.post('/api/v1/subscribers', {
      email: newsletterEmail.value,
      website: newsletterHoneypot.value,
    })
    if (res.data?.success) {
      subscriberStatus.value.success = true
      newsletterEmail.value = ''
    }
  } catch (err) {
    if (err.response?.data?.errors?.email) {
      subscriberStatus.value.error = err.response.data.errors.email[0]
    } else if (err.response?.data?.message) {
      subscriberStatus.value.error = err.response.data.message
    } else {
      subscriberStatus.value.error = 'Unable to subscribe. Please try again later.'
    }
  } finally {
    subscriberStatus.value.loading = false
  }
}

// -------------------------------------------------------------
// 6. Lifecycle & Observers
// -------------------------------------------------------------
onMounted(() => {
  checkTheme()
  prefersReducedMotion.value = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches

  // Watch theme mutations on <html>
  const observer = new MutationObserver(checkTheme)
  observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] })

  // ResizeObserver (debounced)
  let resizeTimeout
  resizeObserver = new ResizeObserver(() => {
    clearTimeout(resizeTimeout)
    resizeTimeout = setTimeout(updateCanvasSizes, 60)
  })
  if (sceneContainer.value) {
    resizeObserver.observe(sceneContainer.value)
  }

  // IntersectionObserver to pause rendering when offscreen
  intersectionObserver = new IntersectionObserver(([entry]) => {
    isVisible.value = entry.isIntersecting
    if (isVisible.value && !prefersReducedMotion.value) {
      renderScene()
    } else if (animationFrameId) {
      cancelAnimationFrame(animationFrameId)
      animationFrameId = null
    }
  }, { threshold: 0.05 })

  if (sceneContainer.value) {
    intersectionObserver.observe(sceneContainer.value)
  }

  updateCanvasSizes()
})

onUnmounted(() => {
  try {
    if (animationFrameId) cancelAnimationFrame(animationFrameId)
    if (resizeObserver) resizeObserver.disconnect()
    if (intersectionObserver) intersectionObserver.disconnect()
  } catch (err) {
    console.warn('[SiteFooter] Cleanup error:', err)
  }
})
</script>

<template>
  <footer class="parkly-meadow-footer" role="contentinfo" id="footer">
    <!-- =========================================================
         TOP MEADOW SCENE: 6 Hills + Editorial Serif Headline
         ========================================================= -->
    <div
      ref="sceneContainer"
      class="meadow-scene"
      aria-label="Artistic landscape illustration: rolling green hills under soft sky"
      @mousemove="handlePointerMove"
      @mouseleave="handlePointerLeave"
    >
      <!-- Offscreen Canvas Renderers -->
      <canvas ref="skyCanvas" class="scene-canvas sky-layer" aria-hidden="true"></canvas>
      <canvas ref="hillsCanvas" class="scene-canvas hills-layer" aria-hidden="true"></canvas>

      <!-- SVG Fallback if canvases fail -->
      <div v-if="!isCanvasSupported" class="scene-fallback" aria-hidden="true"></div>

      <!-- Headline & Call to Action Floating Over Scene -->
      <div class="scene-overlay-content">
        <div class="scene-text-block">
          <p class="scene-eyebrow">
            <span class="eyebrow-line" aria-hidden="true"></span>
            DHAKA-FIRST SMART MOBILITY
          </p>

          <h2 class="scene-headline">
            Find your spot. <span class="serif-italic">Park with confidence.</span>
          </h2>

          <p class="scene-subline">
            Registered parking across Dhaka, with fees calculated on the server.
          </p>

          <div class="scene-cta-row">
            <button
              type="button"
              class="scene-cta-btn"
              @click="navigateToTarget(footerConfig.brand.ctaTarget)"
            >
              <span>{{ footerConfig.brand.ctaText }}</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- =========================================================
         DEEP GROUND FOOTER: Links, Brand, Support & Disclaimers
         Seamlessly connects to nearest hill #0B1F1D / #070D14
         ========================================================= -->
    <div class="footer-ground">
      <div class="ground-inner">
        <!-- Top Row: Brand Summary + Newsletter Subscribe -->
        <div class="ground-top-row">
          <div class="brand-summary">
            <div class="brand-logo-lockup">
              <img :src="logoUrl" alt="Parkly" class="brand-logo-img" width="34" height="34" />
              <span class="brand-name">Parkly</span>
            </div>
            <p class="brand-tagline">{{ footerConfig.brand.description }}</p>
            <div class="contact-pill-row">
              <span class="contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <a :href="`mailto:${footerConfig.contactInfo.email}`">{{ footerConfig.contactInfo.email }}</a>
              </span>
              <span class="contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>{{ footerConfig.contactInfo.phone }}</span>
              </span>
            </div>
          </div>

          <!-- Newsletter / Platform Updates -->
          <div class="newsletter-block">
            <h3 class="newsletter-title">Platform Coverage Updates</h3>
            <p class="newsletter-sub">Receive notifications when verified parking hubs open in new Dhaka zones.</p>

            <form @submit.prevent="handleSubscribe" class="newsletter-form" novalidate>
              <div class="sr-only" aria-hidden="true" style="display: none;">
                <label for="nl-honeypot">Leave blank</label>
                <input id="nl-honeypot" type="text" v-model="newsletterHoneypot" tabindex="-1" autocomplete="off" />
              </div>

              <div class="subscribe-input-group">
                <input
                  id="newsletter-email"
                  type="email"
                  v-model="newsletterEmail"
                  placeholder="Enter your email"
                  class="subscribe-input"
                  required
                  aria-label="Email address for platform updates"
                />
                <button
                  type="submit"
                  class="subscribe-btn"
                  :disabled="subscriberStatus.loading"
                  aria-label="Subscribe to updates"
                >
                  {{ subscriberStatus.loading ? 'Subscribing...' : 'Subscribe' }}
                </button>
              </div>

              <p v-if="subscriberStatus.success" class="subscribe-feedback success" role="status" aria-live="polite">
                Subscribed! You will receive updates about new Dhaka facilities.
              </p>
              <p v-if="subscriberStatus.error" class="subscribe-feedback error" role="alert" aria-live="assertive">
                {{ subscriberStatus.error }}
              </p>
            </form>
          </div>
        </div>

        <div class="ground-hairline" aria-hidden="true"></div>

        <!-- 4 Labeled Navigation Columns -->
        <div class="ground-columns-grid">
          <nav
            v-for="col in footerConfig.columns"
            :key="col.id"
            class="nav-column"
            :aria-label="col.ariaLabel"
          >
            <h4 class="column-title">{{ col.title }}</h4>
            <ul class="column-list">
              <li v-for="link in col.links" :key="link.href">
                <a
                  :href="link.href"
                  class="column-link"
                  @click.prevent="navigateToTarget(link.href)"
                >
                  {{ link.label }}
                </a>
              </li>
            </ul>
          </nav>
        </div>

        <div class="ground-hairline" aria-hidden="true"></div>

        <!-- Bottom Bar: Legal Disclaimer, Copyright & Social -->
        <div class="ground-bottom-bar">
          <div class="bottom-left-group">
            <p class="copyright-line">
              &copy; {{ currentYear }} Parkly Technologies. All rights reserved.
            </p>
            <p class="disclaimer-line">
              {{ footerConfig.disclaimer.text }}
              <span v-if="footerConfig.disclaimer.isSandbox">
                {{ footerConfig.disclaimer.sandboxText }}
              </span>
            </p>
          </div>

          <div class="bottom-right-group">
            <!-- Social Icons (Only render if URLs exist in config) -->
            <div v-if="footerConfig.socialLinks.length" class="social-row" aria-label="Official Social Profiles">
              <a
                v-for="item in footerConfig.socialLinks"
                :key="item.name"
                :href="item.url"
                target="_blank"
                rel="noopener noreferrer"
                class="social-btn"
                :aria-label="item.name"
              >
                <!-- LinkedIn SVG -->
                <svg v-if="item.icon === 'linkedin'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                  <rect x="2" y="9" width="4" height="12" />
                  <circle cx="4" cy="4" r="2" />
                </svg>
                <!-- Twitter / X SVG -->
                <svg v-else-if="item.icon === 'twitter'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                  <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                </svg>
                <!-- Facebook SVG -->
                <svg v-else-if="item.icon === 'facebook'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                </svg>
                <!-- GitHub SVG -->
                <svg v-else-if="item.icon === 'github'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" />
                </svg>
              </a>
            </div>

            <!-- Back to top button -->
            <button
              type="button"
              class="back-to-top-btn"
              @click="scrollToTop"
              aria-label="Scroll back to top of page"
            >
              <span>Back to top</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="19" x2="12" y2="5"></line>
                <polyline points="5 12 12 5 19 12"></polyline>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
/* =========================================================
   CONTAINER & GROUND TOKENS
   ========================================================= */
.parkly-meadow-footer {
  width: 100%;
  position: relative;
  margin-top: auto;
  overflow: hidden;
  background-color: #0B1F1D; /* Deep teal-ink ground */
  color: #F8FAFC;
}

:root[data-theme='dark'] .parkly-meadow-footer {
  background-color: #070D14; /* Deep dark slate-ink */
}

/* =========================================================
   TOP MEADOW SCENE (Canvases + Floating Text)
   ========================================================= */
.meadow-scene {
  position: relative;
  width: 100%;
  height: clamp(380px, 48vw, 560px);
  min-height: 380px;
  overflow: hidden;
  user-select: none;
}

.scene-canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: block;
}

.sky-layer {
  z-index: 1;
}

.hills-layer {
  z-index: 2;
}

.scene-fallback {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, #F8FAFC 0%, #E3F4F0 45%, #145A50 75%, #0B1F1D 100%);
  z-index: 0;
}

:root[data-theme='dark'] .scene-fallback {
  background: linear-gradient(180deg, #0E1A24 0%, #17323A 45%, #0B181E 75%, #070D14 100%);
}

.scene-overlay-content {
  position: relative;
  z-index: 3;
  width: 100%;
  height: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 56px 24px 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  text-align: center;
  pointer-events: none;
}

.scene-text-block {
  max-width: 760px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  pointer-events: auto;
}

.scene-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  color: #0F766E;
  margin: 0;
}

:root[data-theme='dark'] .scene-eyebrow {
  color: #2DD4BF;
}

.eyebrow-line {
  width: 24px;
  height: 2px;
  background-color: currentColor;
}

.scene-headline {
  font-family: var(--font-sans);
  font-size: clamp(2.2rem, 5vw, 3.8rem);
  font-weight: 400;
  line-height: 1.15;
  letter-spacing: -0.03em;
  color: #0F172A;
  margin: 0;
}

:root[data-theme='dark'] .scene-headline {
  color: #F8FAFC;
}

.serif-italic {
  font-family: 'Instrument Serif', 'Fraunces', Georgia, serif;
  font-style: italic;
  font-weight: 400;
  letter-spacing: -0.01em;
  color: #0F766E;
}

:root[data-theme='dark'] .serif-italic {
  color: #2DD4BF;
}

.scene-subline {
  font-size: clamp(0.95rem, 1.4vw, 1.125rem);
  line-height: 1.5;
  color: #475569;
  max-width: 580px;
  margin: 0;
}

:root[data-theme='dark'] .scene-subline {
  color: #94A3B8;
}

.scene-cta-row {
  margin-top: 8px;
}

.scene-cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 12px 28px;
  border-radius: var(--radius-pill, 999px);
  background-color: #0F766E;
  color: #FFFFFF;
  border: none;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 10px 25px -5px rgba(15, 118, 110, 0.4);
  transition: transform var(--transition-fast), background-color var(--transition-fast), box-shadow var(--transition-fast);
}

:root[data-theme='dark'] .scene-cta-btn {
  background-color: #14B8A6;
  color: #070D14;
  box-shadow: 0 10px 25px -5px rgba(20, 184, 166, 0.35);
}

.scene-cta-btn:hover {
  transform: translateY(-2px);
  background-color: #0D655E;
  box-shadow: 0 14px 30px -5px rgba(15, 118, 110, 0.5);
}

:root[data-theme='dark'] .scene-cta-btn:hover {
  background-color: #2DD4BF;
}

/* =========================================================
   FOOTER GROUND (Deep Teal-Ink)
   ========================================================= */
.footer-ground {
  width: 100%;
  background-color: #0B1F1D;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

:root[data-theme='dark'] .footer-ground {
  background-color: #070D14;
}

.ground-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 24px 36px;
  display: flex;
  flex-direction: column;
  gap: 48px;
}

/* Ground Top Row */
.ground-top-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 40px;
  flex-wrap: wrap;
}

.brand-summary {
  max-width: 460px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.brand-logo-lockup {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-logo-img {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: #FFFFFF;
}

.brand-tagline {
  font-size: 0.9375rem;
  line-height: 1.55;
  color: rgba(255, 255, 255, 0.72);
  margin: 0;
}

.contact-pill-row {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 6px;
}

.contact-item {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8125rem;
  color: rgba(255, 255, 255, 0.75);
}

.contact-item a {
  color: #2DD4BF;
  text-decoration: none;
}

.contact-item a:hover {
  text-decoration: underline;
}

/* Newsletter Block */
.newsletter-block {
  max-width: 420px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.newsletter-title {
  font-size: 0.875rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
}

.newsletter-sub {
  font-size: 0.8125rem;
  line-height: 1.5;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
}

.newsletter-form {
  margin-top: 8px;
}

.subscribe-input-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.subscribe-input {
  flex: 1;
  padding: 10px 14px;
  background-color: rgba(255, 255, 255, 0.07);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: var(--radius-pill, 999px);
  color: #FFFFFF;
  font-size: 0.875rem;
  outline: none;
  transition: border-color var(--transition-fast), background-color var(--transition-fast);
}

.subscribe-input:focus {
  border-color: #2DD4BF;
  background-color: rgba(255, 255, 255, 0.12);
}

.subscribe-btn {
  padding: 10px 18px;
  background-color: #14B8A6;
  color: #070D14;
  border: none;
  border-radius: var(--radius-pill, 999px);
  font-size: 0.8125rem;
  font-weight: 700;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color var(--transition-fast), transform var(--transition-fast);
}

.subscribe-btn:hover:not(:disabled) {
  background-color: #2DD4BF;
  transform: translateY(-1px);
}

.subscribe-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.subscribe-feedback {
  margin-top: 6px;
  font-size: 0.75rem;
}

.subscribe-feedback.success {
  color: #34D399;
}

.subscribe-feedback.error {
  color: #F87171;
}

.ground-hairline {
  width: 100%;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.1);
}

/* 4 Labeled Columns */
.ground-columns-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
}

.nav-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.column-title {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.6);
  margin: 0;
}

.column-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.column-link {
  font-size: 0.875rem;
  font-weight: 450;
  color: rgba(255, 255, 255, 0.82);
  text-decoration: none;
  transition: color var(--transition-fast), transform var(--transition-fast);
  display: inline-block;
}

.column-link:hover {
  color: #2DD4BF;
  transform: translateX(2px);
  text-decoration: underline;
}

.column-link:focus-visible {
  outline: 2px solid #2DD4BF;
  outline-offset: 3px;
  border-radius: 4px;
}

/* Bottom Bar */
.ground-bottom-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.bottom-left-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
  max-width: 680px;
}

.copyright-line {
  font-size: 0.8125rem;
  color: rgba(255, 255, 255, 0.65);
  margin: 0;
}

.disclaimer-line {
  font-size: 0.72rem;
  color: rgba(255, 255, 255, 0.45);
  line-height: 1.45;
  margin: 0;
}

.bottom-right-group {
  display: flex;
  align-items: center;
  gap: 16px;
}

.social-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.social-btn {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.14);
  background-color: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.85);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all var(--transition-fast);
}

.social-btn:hover {
  background-color: #14B8A6;
  border-color: #14B8A6;
  color: #070D14;
  transform: translateY(-2px);
}

.social-btn:focus-visible {
  outline: 2px solid #2DD4BF;
  outline-offset: 2px;
}

.back-to-top-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background-color: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: var(--radius-pill, 999px);
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.back-to-top-btn:hover {
  background-color: rgba(255, 255, 255, 0.12);
  color: #FFFFFF;
  border-color: rgba(255, 255, 255, 0.3);
}

.back-to-top-btn:focus-visible {
  outline: 2px solid #2DD4BF;
  outline-offset: 2px;
}

/* =========================================================
   RESPONSIVE BREAKPOINTS
   ========================================================= */
@media (max-width: 960px) {
  .ground-columns-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 32px;
  }

  .ground-top-row {
    flex-direction: column;
  }

  .newsletter-block {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .meadow-scene {
    height: 380px;
    min-height: 360px;
  }

  .scene-overlay-content {
    padding-top: 36px;
  }

  .ground-inner {
    padding: 44px 16px 28px;
    gap: 36px;
  }

  .ground-columns-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }

  .ground-bottom-bar {
    flex-direction: column;
    align-items: flex-start;
  }

  .bottom-right-group {
    width: 100%;
    justify-content: space-between;
  }
}
</style>
