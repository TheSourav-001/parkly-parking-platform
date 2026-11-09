# Parkly Shared Design System

> **Product**: Parkly — AI-Powered Parking Discovery and Booking Platform  
> **Tagline**: *Find. Book. Park.*  
> **Brand**: Modern Minimal (Teal + Navy)  
> **Theme**: Light & Dark mode support  

---

## 1. Agreed Color Tokens

| Token | Light Theme | Dark Theme | CSS Variable |
|---|---|---|---|
| **Primary** | `#0F766E` | `#14B8A6` | `--color-primary` |
| **Primary Hover** | `#0D655E` | `#2DD4BF` | `--color-primary-hover` |
| **Background** | `#F8FAFC` | `#0B1220` | `--color-bg` |
| **Surface** | `#FFFFFF` | `#111C2E` | `--color-surface` |
| **Surface Subtle** | `#F1F5F9` | `#19273C` | `--color-surface-subtle` |
| **Text** | `#0F172A` | `#F1F5F9` | `--color-text` |
| **Muted Text** | `#64748B` | `#9CAEC2` | `--color-text-muted` |
| **Border** | `#E2E8F0` | `#26364C` | `--color-border` |
| **Focus Ring** | `rgba(15, 118, 110, 0.25)` | `rgba(20, 184, 166, 0.35)` | `--color-ring` |

---

## 2. Shared Components & Usage Examples

### `AppButton.vue`
Supports variants (`primary`, `secondary`, `outline`, `ghost`, `danger`) and sizes (`sm`, `md`, `lg`).

```vue
<script setup>
import AppButton from '@/components/common/AppButton.vue'
</script>

<template>
  <!-- Primary button -->
  <AppButton variant="primary" size="md" @click="handleAction">
    Find Parking
  </AppButton>

  <!-- Button with prefix icon and loading state -->
  <AppButton variant="outline" :loading="isLoading">
    <template #prefix>
      <SearchIcon />
    </template>
    Search Spots
  </AppButton>
</template>
```

---

### `AppInput.vue`
Accessible form input with built-in label, hint, error states, and icon slots.

```vue
<script setup>
import { ref } from 'vue'
import AppInput from '@/components/common/AppInput.vue'

const email = ref('')
const error = ref('')
</script>

<template>
  <AppInput
    v-model="email"
    label="Email Address"
    type="email"
    placeholder="driver@parkly.com"
    :error="error"
    hint="We'll send your booking confirmation here."
    required
  />
</template>
```

---

### `ThemeToggle.vue` & `useTheme()`
Manages light/dark mode and persists state in `localStorage`:

```vue
<script setup>
import ThemeToggle from '@/components/common/ThemeToggle.vue'
import { useTheme } from '@/composables/useTheme'

const { currentTheme, toggleTheme, setTheme } = useTheme()
</script>

<template>
  <ThemeToggle />
</template>
```

---

### `Navbar.vue` & `Footer.vue`
Layout headers and footers matching the Modern Minimal design language:

```vue
<script setup>
import Navbar from '@/components/layout/Navbar.vue'
import Footer from '@/components/layout/Footer.vue'
</script>

<template>
  <Navbar />
  <!-- Page Content -->
  <Footer />
</template>
```

