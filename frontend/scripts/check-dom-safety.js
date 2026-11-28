#!/usr/bin/env node
/**
 * DOM Safety Linter for Parkly
 *
 * Ensures no component uses raw `removeChild` or direct unsafe DOM manipulation
 * on nodes that Vue or the browser lifecycle manages. Such calls are notorious
 * for throwing NotFoundError and aborting Vue unmount/patch during route changes.
 */
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const srcDir = path.resolve(__dirname, '../src')

let hasErrors = false
const FORBIDDEN_PATTERNS = [
  {
    regex: /\.removeChild\s*\(/,
    message: 'Raw ".removeChild()" is forbidden. Use element?.remove() or safe cleanup guards.',
  },
  {
    regex: /parentNode\.removeChild/,
    message: '"parentNode.removeChild" is forbidden. It can mistakenly target document.head or detached nodes.',
  },
]

function walkDir(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true })
  for (const entry of entries) {
    const fullPath = path.join(dir, entry.name)
    if (entry.isDirectory()) {
      walkDir(fullPath)
    } else if (/\.(vue|js|ts)$/.test(entry.name)) {
      checkFile(fullPath)
    }
  }
}

function checkFile(filePath) {
  const content = fs.readFileSync(filePath, 'utf-8')
  const lines = content.split('\n')

  lines.forEach((line, index) => {
    for (const pattern of FORBIDDEN_PATTERNS) {
      if (pattern.regex.test(line)) {
        // Allow comments or test files if specifically bypassed
        if (line.trim().startsWith('//') || line.trim().startsWith('*')) continue
        console.error(
          `\x1b[31m[DOM Safety Violation]\x1b[0m ${path.relative(process.cwd(), filePath)}:${index + 1}`
        )
        console.error(`  Line: ${line.trim()}`)
        console.error(`  Reason: ${pattern.message}\n`)
        hasErrors = true
      }
    }
  })
}

console.log('Running Parkly DOM Safety Audit on frontend/src...')
walkDir(srcDir)

if (hasErrors) {
  console.error('\x1b[31mDOM Safety Audit FAILED: Forbidden DOM methods detected.\x1b[0m')
  process.exit(1)
} else {
  console.log('\x1b[32m✔ DOM Safety Audit PASSED: Zero dangerous removeChild calls found.\x1b[0m')
  process.exit(0)
}

