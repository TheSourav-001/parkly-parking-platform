<script setup>
/**
 * Footer Component
 * Shared Footer for Parkly.
 * Modern minimal layout with branding, tagline, product links, and copyright.
 */
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="parkly-footer">
    <div class="footer-container">
      <div class="footer-top">
        <!-- Brand & Description -->
        <div class="footer-brand-section">
          <div class="brand-group">
            <div class="logo-mark" aria-hidden="true">
              <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
                <rect width="32" height="32" rx="8" fill="currentColor" />
                <path
                  d="M10 23V9h6.2a4.8 4.8 0 0 1 4.8 4.8v.4a4.8 4.8 0 0 1-4.8 4.8H14v4h-4zm4-7.8h2.2a1.8 1.8 0 0 0 1.8-1.8v-.4a1.8 1.8 0 0 0-1.8-1.8H14v4z"
                  fill="#FFFFFF"
                />
              </svg>
            </div>
            <div class="brand-text">
              <span class="brand-title">Parkly</span>
              <span class="brand-tagline">Find. Book. Park.</span>
            </div>
          </div>
          <p class="brand-description">
            Next-generation AI-powered parking discovery and real-time reservation platform. Effortless urban parking for everyone.
          </p>
        </div>

        <!-- Links Columns -->
        <div class="footer-nav-grid">
          <div class="footer-col">
            <h4 class="col-title">Platform</h4>
            <ul class="col-list">
              <li><a href="#discovery">Find Parking</a></li>
              <li><a href="#airports">Airport Parking</a></li>
              <li><a href="#ev-charging">EV Stations</a></li>
              <li><a href="#pricing">Pricing & Rates</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4 class="col-title">Partners</h4>
            <ul class="col-list">
              <li><a href="#operators">Parking Operators</a></li>
              <li><a href="#enterprise">Enterprise Solutions</a></li>
              <li><a href="#partner-portal">Operator Portal</a></li>
              <li><a href="#api">Developer API</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4 class="col-title">Company</h4>
            <ul class="col-list">
              <li><a href="#about">About Us</a></li>
              <li><a href="#careers">Careers</a></li>
              <li><a href="#press">Press</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <p class="copyright-text">
          &copy; {{ currentYear }} Parkly Technologies Inc. All rights reserved.
        </p>
        <div class="legal-links">
          <a href="#privacy">Privacy Policy</a>
          <span class="divider" aria-hidden="true">&bull;</span>
          <a href="#terms">Terms of Service</a>
          <span class="divider" aria-hidden="true">&bull;</span>
          <a href="#security">Security</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.parkly-footer {
  width: 100%;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
  margin-top: auto;
  transition:
    background-color var(--transition-normal),
    border-color var(--transition-normal);
}

.footer-container {
  max-width: var(--container-max-width);
  margin: 0 auto;
  padding: var(--space-10) var(--space-4) var(--space-6);
  display: flex;
  flex-direction: column;
  gap: var(--space-8);
}

.footer-top {
  display: flex;
  justify-content: space-between;
  gap: var(--space-8);
  flex-wrap: wrap;
}

.footer-brand-section {
  max-width: 340px;
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.brand-group {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.logo-mark {
  width: 32px;
  height: 32px;
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

.brand-title {
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-bold);
  color: var(--color-text);
  line-height: 1.1;
}

.brand-tagline {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
  font-weight: var(--font-weight-medium);
}

.brand-description {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
  line-height: var(--line-height-relaxed);
}

/* --- Navigation Grid --- */
.footer-nav-grid {
  display: flex;
  gap: var(--space-10);
  flex-wrap: wrap;
}

.footer-col {
  min-width: 140px;
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.col-title {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.col-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.col-list a {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.col-list a:hover {
  color: var(--color-primary);
  text-decoration: none;
}

/* --- Footer Bottom --- */
.footer-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--space-6);
  border-top: 1px solid var(--color-border);
  gap: var(--space-4);
  flex-wrap: wrap;
}

.copyright-text {
  font-size: var(--font-size-xs);
  color: var(--color-text-muted);
}

.legal-links {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  font-size: var(--font-size-xs);
}

.legal-links a {
  color: var(--color-text-muted);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.legal-links a:hover {
  color: var(--color-primary);
  text-decoration: none;
}

.divider {
  color: var(--color-border);
}

@media (max-width: 768px) {
  .footer-top {
    flex-direction: column;
  }
  .footer-nav-grid {
    gap: var(--space-6);
  }
  .footer-bottom {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>

