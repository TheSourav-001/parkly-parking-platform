import { test, expect } from '@playwright/test'

test.describe('Parkly Client-Side Navigation Matrix', () => {
  test.beforeEach(async ({ page }) => {
    // Collect all console errors during page lifecycle
    page.on('console', (msg) => {
      if (msg.type() === 'error') {
        const text = msg.text()
        // Fail immediately if unmount or removeChild error is logged
        expect(text).not.toContain('NotFoundError')
        expect(text).not.toContain('Failed to execute \'removeChild\'')
        expect(text).not.toContain('Cannot read properties of null')
      }
    })

    page.on('pageerror', (err) => {
      expect(err.message).not.toContain('NotFoundError')
      expect(err.message).not.toContain('removeChild')
    })
  })

  test('Navbar "Sign In" navigates to /login and swaps page view without refresh', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    // Find and click the desktop Sign In link in Navbar
    const signInLink = page.locator('.nav-signin-link')
    await expect(signInLink).toBeVisible()
    await signInLink.click()

    // Assert URL updated
    await expect(page).toHaveURL(/\/login/)

    // Assert the login view is visibly rendered (NOT the homepage hero)
    const loginCard = page.locator('.auth-card, .login-page, form')
    await expect(loginCard.first()).toBeVisible()

    // Verify hero from homepage is gone
    const heroTitle = page.locator('.hero-h1')
    await expect(heroTitle).toHaveCount(0)
  })

  test('Navbar "Get Started" navigates to /register and swaps page view without refresh', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    const getStartedBtn = page.locator('.btn-get-started')
    await expect(getStartedBtn).toBeVisible()
    await getStartedBtn.click()

    await expect(page).toHaveURL(/\/register/)

    // Register card/form must be visible
    const registerCard = page.locator('.auth-card, .register-view, form')
    await expect(registerCard.first()).toBeVisible()
  })

  test('Full navigation cycle: Home -> Login -> Register -> Home via Logo', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    // 1. Home -> Login
    await page.locator('.nav-signin-link').click()
    await expect(page).toHaveURL(/\/login/)
    await expect(page.locator('input[type="email"]')).toBeVisible()

    // 2. Login -> Register
    const createAccountLink = page.locator('a[href="/register"]').first()
    await createAccountLink.click()
    await expect(page).toHaveURL(/\/register/)

    // 3. Register -> Home via Logo
    const brandLogo = page.locator('.brand-group')
    await brandLogo.click()
    await expect(page).toHaveURL('/')
    await expect(page.locator('.hero-parking-canvas')).toBeAttached()
  })

  test('Browser Back and Forward buttons swap views cleanly', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    // Navigate to Login
    await page.locator('.nav-signin-link').click()
    await expect(page).toHaveURL(/\/login/)

    // Back to Home
    await page.goBack()
    await expect(page).toHaveURL('/')
    await expect(page.locator('.navbar-pill .brand-name')).toBeVisible()

    // Forward to Login
    await page.goForward()
    await expect(page).toHaveURL(/\/login/)
  })

  test('FAQ "Contact support" link navigates directly to /contact', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    // Scroll to FAQ
    const faqSection = page.locator('#faq')
    await faqSection.scrollIntoViewIfNeeded()

    const contactSupportLink = page.locator('a[href="/contact"]').first()
    await expect(contactSupportLink).toBeVisible()
    await contactSupportLink.click()

    await expect(page).toHaveURL(/\/contact/)
    await expect(page.locator('h1, h2').first()).toBeVisible()
  })

  test('Legal footer links navigate without page freeze', async ({ page }) => {
    await page.goto('/')
    await page.waitForLoadState('domcontentloaded')

    const termsLink = page.locator('a[href="/terms"]').first()
    await termsLink.scrollIntoViewIfNeeded()
    await termsLink.click()

    await expect(page).toHaveURL(/\/terms/)
    await expect(page.locator('.legal-page').first()).toBeVisible()

    const privacyLink = page.locator('a[href="/privacy"]').first()
    if (await privacyLink.count() > 0) {
      await privacyLink.scrollIntoViewIfNeeded()
      await privacyLink.click()
      await expect(page).toHaveURL(/\/privacy/)
    }
  })
})
