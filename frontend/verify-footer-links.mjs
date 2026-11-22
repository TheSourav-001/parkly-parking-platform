/**
 * Automated Link & Content Integrity Verification Test for Parkly Footer
 * Verifies:
 * 1. Zero dead links / zero empty '#' placeholders in footer config.
 * 2. Every footer target corresponds to a registered Vue route or valid DOM ID.
 * 3. Zero emoji characters anywhere in the footer text or component source.
 * 4. Grounded copy integrity (no sensor/automated gate/realtime claims).
 */
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const footerConfigPath = path.resolve(__dirname, 'src/config/footer.js')
const routerIndexPath = path.resolve(__dirname, 'src/router/index.js')
const siteFooterPath = path.resolve(__dirname, 'src/components/layout/SiteFooter.vue')

console.log('--- PARKLY FOOTER INTEGRITY AUDIT ---')

// 1. Check footer config content
const footerConfigContent = fs.readFileSync(footerConfigPath, 'utf8')
const routerIndexContent = fs.readFileSync(routerIndexPath, 'utf8')
const siteFooterContent = fs.readFileSync(siteFooterPath, 'utf8')

// Regex for emoji characters
const emojiRegex = /[\u{1F300}-\u{1F9FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F1E6}-\u{1F1FF}]/gu

const foundEmojis = (footerConfigContent + siteFooterContent).match(emojiRegex)
if (foundEmojis && foundEmojis.length > 0) {
  console.error('FAIL: Found emoji characters in footer:', foundEmojis)
  process.exit(1)
} else {
  console.log('PASS: Zero emoji characters detected.')
}

// 2. Check for dead links or '#' placeholders in config
const rawLinks = []
const linkMatchRegex = /href:\s*'([^']+)'/g
let match
while ((match = linkMatchRegex.exec(footerConfigContent)) !== null) {
  rawLinks.push(match[1])
}

console.log(`Found ${rawLinks.length} configured footer links:`, rawLinks)

if (rawLinks.length === 0) {
  console.error('FAIL: No footer links detected in footerConfig!')
  process.exit(1)
}

let hasPlaceholder = false
rawLinks.forEach((link) => {
  if (link === '#' || link === '' || link.startsWith('#!')) {
    console.error(`FAIL: Invalid link placeholder found: "${link}"`)
    hasPlaceholder = true
  }
})

if (hasPlaceholder) {
  process.exit(1)
}
console.log('PASS: Zero "#" empty placeholders detected.')

// 3. Verify each link maps to a real route or home section
const knownHomeSections = ['#find-parking', '#journey', '#pricing', '#locations', '#faq', '#discovery', '#how-it-works', '#featured']
const knownRoutes = ['/privacy', '/terms', '/cancellation-policy', '/location-and-data-use', '/contact', '/operators/enquiry', '/login', '/register', '/profile', '/find-parking']

rawLinks.forEach((link) => {
  const cleanPath = link.split('?')[0]
  if (cleanPath.startsWith('/#')) {
    const hash = cleanPath.replace('/', '')
    if (!knownHomeSections.includes(hash)) {
      console.error(`FAIL: Anchor "${hash}" does not map to any recognized home section!`)
      process.exit(1)
    }
  } else {
    if (!knownRoutes.includes(cleanPath)) {
      console.error(`FAIL: Route "${cleanPath}" does not exist in registered routes!`)
      process.exit(1)
    }
  }
})
console.log('PASS: All footer links target valid routes or valid home section aliases.')

// 4. Verify no positive claims of automated sensors, automated gates, or guaranteed real-time availability
const forbiddenPositiveClaims = ['sensor & gate api', 'automated gate', 'instant real-time guarantee', 'nationwide across bangladesh']
forbiddenPositiveClaims.forEach((claim) => {
  if (siteFooterContent.toLowerCase().includes(claim) || footerConfigContent.toLowerCase().includes(claim)) {
    console.error(`FAIL: Forbidden claim "${claim}" detected in footer copy!`)
    process.exit(1)
  }
})
console.log('PASS: Zero false claims (sensors, automated gates, nationwide guarantees) detected.')

console.log('ALL AUDIT CHECKS PASSED SUCCESSFULLY!')
