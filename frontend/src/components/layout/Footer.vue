<script setup>
/**
 * Footer Component — Mastercard-Inspired Editorial Dark Footer
 * Dark surface (#0B1220 / Navy), conversational headline, 4-column links,
 * social links, copyright, and country/language pill selector.
 */
import { ref } from 'vue'

const currentYear = new Date().getFullYear()
const selectedRegion = ref('Bangladesh (EN)')
</script>

<template>
  <footer class="parkly-footer" id="support">
    <div class="footer-inner">
      <!-- Conversational Headline Section -->
      <div class="footer-hero">
        <div class="footer-eyebrow">
          <span class="eyebrow-dot"></span>
          ALWAYS ACCESSIBLE
        </div>
        <h2 class="footer-headline">We're always here when you need a spot.</h2>
        <p class="footer-subheadline">
          Connecting smart drivers with trusted parking facilities across Bangladesh. Real-time availability, instant reservations, and AI assistance.
        </p>
      </div>

      <!-- 4-Column Grid -->
      <div class="footer-grid">
        <!-- Col 1: Platform -->
        <div class="footer-column">
          <h3 class="column-header">PLATFORM</h3>
          <ul class="column-links">
            <li><a href="#discovery">Find Parking</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#locations">Popular Locations</a></li>
            <li><a href="#featured">Featured Facilities</a></li>
            <li><a href="#ai-search">Ask Parkly AI</a></li>
          </ul>
        </div>

        <!-- Col 2: For Operators -->
        <div class="footer-column" id="operators">
          <h3 class="column-header">FOR OPERATORS</h3>
          <ul class="column-links">
            <li>
              <a href="#partner">
                Parking Operators
                <span class="ext-arrow" aria-hidden="true">&nearr;</span>
              </a>
            </li>
            <li>
              <a href="#partner-with-us">
                Partner With Us
                <span class="ext-arrow" aria-hidden="true">&nearr;</span>
              </a>
            </li>
            <li><a href="#portal">Operator Portal</a></li>
            <li><a href="#hardware">Sensor & Gate API</a></li>
            <li><a href="#enterprise">Commercial Garages</a></li>
          </ul>
        </div>

        <!-- Col 3: Company -->
        <div class="footer-column">
          <h3 class="column-header">COMPANY</h3>
          <ul class="column-links">
            <li><a href="#about">About Parkly</a></li>
            <li><a href="#careers">Careers</a></li>
            <li><a href="#press">Press & Media</a></li>
            <li><a href="#contact">Contact Support</a></li>
            <li><a href="#security">Security & Trust</a></li>
          </ul>
        </div>

        <!-- Col 4: Legal & Help -->
        <div class="footer-column">
          <h3 class="column-header">LEGAL & COMPLIANCE</h3>
          <ul class="column-links">
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="#cookies">Cookie Preferences</a></li>
            <li><a href="#refunds">Cancellation Policy</a></li>
            <li><a href="#help">Help Center & FAQ</a></li>
          </ul>
        </div>
      </div>

      <!-- Divider -->
      <div class="footer-divider" aria-hidden="true"></div>

      <!-- Bottom Bar: Brand, Region Selector, Copyright, Socials -->
      <div class="footer-bottom">
        <div class="bottom-brand-group">
          <div class="mini-logo" aria-hidden="true">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" width="22" height="22">
              <rect width="32" height="32" rx="8" fill="var(--color-primary)" />
              <path
                d="M10 23V9h6.2a4.8 4.8 0 0 1 4.8 4.8v.4a4.8 4.8 0 0 1-4.8 4.8H14v4h-4zm4-7.8h2.2a1.8 1.8 0 0 0 1.8-1.8v-.4a1.8 1.8 0 0 0-1.8-1.8H14v4z"
                fill="#FFFFFF"
              />
            </svg>
          </div>
          <span class="copyright-text">
            &copy; {{ currentYear }} Parkly Technologies Inc. All rights reserved.
          </span>
        </div>

        <!-- Region / Language Selector Pill -->
        <div class="region-selector-pill">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="2" x2="22" y1="12" y2="12" />
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
          </svg>
          <span>{{ selectedRegion }}</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="12"
            height="12"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
          >
            <polyline points="6 9 12 15 18 9" />
          </svg>
        </div>

        <!-- Social Links (Circular Minimal Buttons) -->
        <div class="social-links" aria-label="Social media">
          <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="social-circle" aria-label="LinkedIn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
              <rect x="2" y="9" width="4" height="12" />
              <circle cx="4" cy="4" r="2" />
            </svg>
          </a>
          <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="social-circle" aria-label="X / Twitter">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
              <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
            </svg>
          </a>
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-circle" aria-label="Facebook">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
            </svg>
          </a>
          <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="social-circle" aria-label="GitHub">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.parkly-footer {
  width: 100%;
  background-color: #0B1220; /* Deep Navy surface, consistent with Parkly dark token */
  color: #F1F5F9;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  margin-top: auto;
  position: relative;
  overflow: hidden;
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 80px 24px 40px;
  display: flex;
  flex-direction: column;
  gap: 56px;
}

/* Conversational Top */
.footer-hero {
  max-width: 680px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.footer-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: #14B8A6; /* Parkly Teal accent */
  text-transform: uppercase;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #14B8A6;
}

.footer-headline {
  font-size: clamp(2rem, 3.5vw, 2.75rem);
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.025em;
  color: #FFFFFF;
}

.footer-subheadline {
  font-size: 1rem;
  line-height: 1.6;
  color: #94A3B8;
  margin: 0;
}

/* 4-Column Grid */
.footer-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
}

.footer-column {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.column-header {
  font-size: 0.8125rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  color: #94A3B8;
  margin: 0;
  text-transform: uppercase;
}

.column-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.column-links a {
  font-size: 0.9375rem;
  font-weight: 450;
  color: #E2E8F0;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: color var(--transition-fast), transform var(--transition-fast);
}

.column-links a:hover {
  color: #14B8A6;
  transform: translateX(2px);
  text-decoration: none;
}

.ext-arrow {
  font-size: 0.85rem;
  opacity: 0.7;
}

/* Divider */
.footer-divider {
  width: 100%;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.08);
}

/* Bottom Bar */
.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.bottom-brand-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mini-logo {
  display: flex;
  align-items: center;
}

.copyright-text {
  font-size: 0.8125rem;
  color: #94A3B8;
}

/* Region Selector Pill */
.region-selector-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px;
  border-radius: var(--radius-pill, 999px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  background-color: rgba(255, 255, 255, 0.03);
  color: #E2E8F0;
  font-size: 0.8125rem;
  font-weight: 500;
  cursor: pointer;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast);
}

.region-selector-pill:hover {
  background-color: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.3);
}

/* Social Circles */
.social-links {
  display: flex;
  align-items: center;
  gap: 10px;
}

.social-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.12);
  background-color: rgba(255, 255, 255, 0.04);
  color: #E2E8F0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition:
    background-color var(--transition-fast),
    border-color var(--transition-fast),
    color var(--transition-fast),
    transform var(--transition-fast);
}

.social-circle:hover {
  background-color: #14B8A6;
  border-color: #14B8A6;
  color: #0B1220;
  transform: translateY(-2px);
  text-decoration: none;
}

/* Breakpoints */
@media (max-width: 960px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 36px;
  }
}

@media (max-width: 640px) {
  .footer-inner {
    padding: 56px 16px 32px;
    gap: 40px;
  }

  .footer-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }

  .footer-bottom {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
}
</style>
