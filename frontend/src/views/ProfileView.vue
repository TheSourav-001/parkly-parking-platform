<script setup>
/**
 * ProfileView Component
 * Route: /profile
 * Flagship 12-Column Bento Cockpit Dashboard powered by authentic backend user data.
 */
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { animate, stagger } from 'animejs'
import { useAuthStore } from '../stores/auth'
import { useTheme } from '../composables/useTheme'
import AppButton from '../components/common/AppButton.vue'
import AppInput from '../components/common/AppInput.vue'
import PasswordStrengthBar from '../components/auth/PasswordStrengthBar.vue'
import ProfileParkingAnimation from '../components/profile/ProfileParkingAnimation.vue'

const router = useRouter()
const authStore = useAuthStore()
const { currentTheme, setTheme } = useTheme()

const activeTab = ref('overview')
const isLoading = ref(false)
const isLoggingOut = ref(false)
const avatarPreview = ref('')
const avatarInput = ref(null)
const showPassDetails = ref(false)

// Settings: Editable Profile Form State
const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
})
const profileErrors = reactive({
  name: '',
  email: '',
  phone: '',
  general: '',
})
const isSavingProfile = ref(false)
const profileSuccessMsg = ref('')

// Settings: Update Password Form State
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const passwordErrors = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
  general: '',
})
const isSavingPassword = ref(false)
const passwordSuccessMsg = ref('')
const showCurrentPwd = ref(false)
const showNewPwd = ref(false)

const passTiltX = ref(0)
const passTiltY = ref(0)
const passGlowX = ref(50)
const passGlowY = ref(50)
const isPassHover = ref(false)

const tabs = [
  { id: 'overview', label: 'Overview' },
  { id: 'activity', label: 'Activity' },
  { id: 'analytics', label: 'Analytics' },
  { id: 'settings', label: 'Settings' },
]

const user = computed(() => authStore.user)
const displayAvatar = computed(() => avatarPreview.value || user.value?.avatar || '')

const initials = computed(() => {
  if (!user.value?.name) return 'P'
  return user.value.name
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

const roleLabel = computed(() =>
  user.value?.role === 'operator' ? 'Parking Operator' : 'Driver'
)

const statusLabel = computed(() =>
  user.value?.is_active ? 'Active account' : 'Pending verification'
)

const formattedJoinedDate = computed(() => {
  if (!user.value?.created_at) return 'Recently'
  return new Date(user.value.created_at).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
})

const shortJoinedDate = computed(() => {
  if (!user.value?.created_at) return 'New Member'
  return new Date(user.value.created_at).toLocaleDateString(undefined, {
    month: 'short',
    year: 'numeric',
  })
})

const setupChecks = computed(() => {
  if (!user.value) return []
  return [
    {
      id: 'active',
      label: 'Account Status',
      value: user.value.is_active ? 'Active' : 'Pending',
      ok: Boolean(user.value.is_active),
    },
    {
      id: 'email',
      label: 'Email Verification',
      value: user.value.email_verified_at ? 'Verified' : 'Unverified',
      ok: Boolean(user.value.email_verified_at),
    },
    {
      id: 'phone',
      label: 'Phone Number',
      value: user.value.phone || 'Not linked',
      ok: Boolean(user.value.phone),
    },
    {
      id: 'role',
      label: 'Account Tier',
      value: roleLabel.value,
      ok: Boolean(user.value.role),
    },
  ]
})

const completedSetupCount = computed(() => setupChecks.value.filter((item) => item.ok).length)
const setupRingCircumference = 2 * Math.PI * 26
const setupRingOffset = computed(() => {
  const ratio = setupChecks.value.length ? completedSetupCount.value / setupChecks.value.length : 0
  return setupRingCircumference * (1 - ratio)
})

function handlePassPointerMove(e) {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return
  const rect = e.currentTarget.getBoundingClientRect()
  const px = (e.clientX - rect.left) / rect.width
  const py = (e.clientY - rect.top) / rect.height
  isPassHover.value = true
  passTiltX.value = (0.5 - py) * 7
  passTiltY.value = (px - 0.5) * 9
  passGlowX.value = px * 100
  passGlowY.value = py * 100
}

function handlePassPointerLeave() {
  isPassHover.value = false
  passTiltX.value = 0
  passTiltY.value = 0
}

onMounted(async () => {
  if (!authStore.user) {
    isLoading.value = true
    try {
      await authStore.fetchUser()
    } catch {
      router.push('/login')
      return
    } finally {
      isLoading.value = false
    }
  }

  await nextTick()
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (!prefersReduced) {
    animate('.bento-animate', {
      opacity: [0, 1],
      translateY: [18, 0],
      duration: 540,
      delay: stagger(65),
      ease: 'outCubic',
    })
  }
})

onBeforeUnmount(() => {
  if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value)
})

function chooseAvatar() {
  avatarInput.value?.click()
}

function handleAvatarChange(event) {
  const [file] = event.target.files || []
  if (!file || !file.type.startsWith('image/')) return
  if (avatarPreview.value) URL.revokeObjectURL(avatarPreview.value)
  avatarPreview.value = URL.createObjectURL(file)
}

function syncProfileFormFromUser() {
  if (!user.value) return
  profileForm.name = user.value.name || ''
  profileForm.email = user.value.email || ''
  profileForm.phone = user.value.phone || ''
  profileErrors.name = ''
  profileErrors.email = ''
  profileErrors.phone = ''
  profileErrors.general = ''
}

watch(
  user,
  () => {
    syncProfileFormFromUser()
  },
  { immediate: true }
)

const livePreviewInitials = computed(() => {
  const name = profileForm.name.trim() || user.value?.name || 'P'
  return name
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
})

function clearAvatarPreview() {
  if (avatarPreview.value) {
    URL.revokeObjectURL(avatarPreview.value)
    avatarPreview.value = ''
  }
  if (avatarInput.value) {
    avatarInput.value.value = ''
  }
}

const isProfileDirty = computed(() => {
  if (!user.value) return false
  return (
    profileForm.name.trim() !== (user.value.name || '') ||
    profileForm.email.trim() !== (user.value.email || '') ||
    (profileForm.phone.trim() || '') !== (user.value.phone || '')
  )
})

function validateProfileForm() {
  let valid = true
  profileErrors.name = ''
  profileErrors.email = ''
  profileErrors.phone = ''
  profileErrors.general = ''
  profileSuccessMsg.value = ''

  const name = profileForm.name.trim()
  const email = profileForm.email.trim()
  const phone = profileForm.phone.trim()

  if (!name) {
    profileErrors.name = 'Full name is required'
    valid = false
  } else if (name.length < 2) {
    profileErrors.name = 'Name must be at least 2 characters'
    valid = false
  }

  if (!email) {
    profileErrors.email = 'Email address is required'
    valid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    profileErrors.email = 'Please enter a valid email address'
    valid = false
  }

  if (phone && !/^[+0-9\s\-()]{7,20}$/.test(phone)) {
    profileErrors.phone = 'Please enter a valid phone number format'
    valid = false
  }

  return valid
}

async function handleSaveProfile() {
  if (isSavingProfile.value) return
  if (!validateProfileForm()) return

  isSavingProfile.value = true
  profileSuccessMsg.value = ''
  profileErrors.general = ''

  try {
    const data = await authStore.updateProfile({
      name: profileForm.name.trim(),
      email: profileForm.email.trim(),
      phone: profileForm.phone.trim() || null,
    })
    profileSuccessMsg.value = data?.message || 'Profile updated successfully.'
  } catch (err) {
    const errs = err.response?.data?.errors
    if (errs) {
      if (errs.name) profileErrors.name = errs.name[0]
      if (errs.email) profileErrors.email = errs.email[0]
      if (errs.phone) profileErrors.phone = errs.phone[0]
    } else {
      profileErrors.general = err.response?.data?.message || 'Unable to update profile. Please try again.'
    }
  } finally {
    isSavingProfile.value = false
  }
}

function validatePasswordForm() {
  let valid = true
  passwordErrors.current_password = ''
  passwordErrors.password = ''
  passwordErrors.password_confirmation = ''
  passwordErrors.general = ''
  passwordSuccessMsg.value = ''

  if (!passwordForm.current_password) {
    passwordErrors.current_password = 'Current password is required'
    valid = false
  }
  if (!passwordForm.password) {
    passwordErrors.password = 'New password is required'
    valid = false
  } else if (passwordForm.password.length < 8) {
    passwordErrors.password = 'New password must be at least 8 characters'
    valid = false
  }
  if (!passwordForm.password_confirmation) {
    passwordErrors.password_confirmation = 'Please confirm your new password'
    valid = false
  } else if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordErrors.password_confirmation = 'Passwords do not match'
    valid = false
  }

  return valid
}

async function handleUpdatePassword() {
  if (isSavingPassword.value) return
  if (!validatePasswordForm()) return

  isSavingPassword.value = true
  passwordSuccessMsg.value = ''
  passwordErrors.general = ''

  try {
    const data = await authStore.updatePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation,
    })
    passwordSuccessMsg.value = data?.message || 'Password updated successfully.'
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (err) {
    const errs = err.response?.data?.errors
    if (errs) {
      if (errs.current_password) passwordErrors.current_password = errs.current_password[0]
      if (errs.password) passwordErrors.password = errs.password[0]
    } else {
      passwordErrors.general = err.response?.data?.message || 'Unable to update password. Please try again.'
    }
  } finally {
    isSavingPassword.value = false
  }
}

async function handleLogout() {
  isLoggingOut.value = true
  try {
    await authStore.logout()
    router.push('/login')
  } finally {
    isLoggingOut.value = false
  }
}
</script>

<template>
  <div class="profile-page" :class="{ 'is-dark': currentTheme === 'dark' }">
    <div v-if="isLoading" class="profile-loading">
      <span class="loading-spinner"></span>
      <p>Loading your Parkly dashboard...</p>
    </div>

    <div v-else-if="user" class="profile-container">
      <!-- ================= HERO PROFILE HEADER ================= -->
      <header class="profile-hero-card">
        <div class="profile-cover" aria-label="Smart parking cover illustration">
          <ProfileParkingAnimation />
          <div class="cover-top-badge">
            <span class="live-dot"></span>
            <span>PARKLY MOBILITY COCKPIT</span>
          </div>
        </div>

        <div class="identity-panel">
          <div class="identity-main">
            <div class="identity-left">
              <!-- Avatar with Orbital Ring + Camera Upload -->
              <div class="avatar-wrap">
                <div class="avatar-orbit-ring" aria-hidden="true"></div>
                <button
                  type="button"
                  class="avatar-large"
                  :aria-label="`Change profile photo for ${user.name}`"
                  @click="chooseAvatar"
                >
                  <img v-if="displayAvatar" :src="displayAvatar" :alt="user.name" class="avatar-image" />
                  <span v-else class="avatar-fallback">{{ initials }}</span>
                  <span class="avatar-camera" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" />
                      <circle cx="12" cy="13" r="3.5" />
                    </svg>
                  </span>
                </button>
                <input ref="avatarInput" class="avatar-input" type="file" accept="image/*" @change="handleAvatarChange" />
              </div>

              <!-- User Info -->
              <div class="identity-copy">
                <div class="eyebrow-row">
                  <span class="eyebrow-dot"></span>
                  <span class="eyebrow">{{ user.role === 'operator' ? 'PARKING OPERATOR' : 'DRIVER MEMBER' }}</span>
                </div>
                <h1>{{ user.name }}</h1>
                <p class="identity-email">{{ user.email }}</p>

                <div class="identity-badges">
                  <span class="role-pill">{{ roleLabel }}</span>
                  <span class="status-pill" :class="{ active: user.is_active }">
                    <i></i>{{ statusLabel }}
                  </span>
                  <span class="verified-pill" :class="{ isVerified: Boolean(user.email_verified_at) }">
                    {{ user.email_verified_at ? 'Email Verified' : 'Email Unverified' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="identity-actions">
              <router-link to="/#discovery">
                <AppButton variant="secondary" size="sm" pill>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                  </svg>
                  <span>Explore Map</span>
                </AppButton>
              </router-link>
              <AppButton
                variant="ghost"
                size="sm"
                pill
                class="logout-pill-btn"
                :loading="isLoggingOut"
                @click="handleLogout"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <polyline points="16 17 21 12 16 7" />
                  <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                <span>Logout</span>
              </AppButton>
            </div>
          </div>

          <!-- Integrated Pill Tab Strip -->
          <div class="tabs-bar">
            <nav class="pill-tabs" role="tablist" aria-label="Profile sections">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                :id="`${tab.id}-tab`"
                type="button"
                class="pill-tab"
                :class="{ active: activeTab === tab.id }"
                :aria-selected="activeTab === tab.id"
                :aria-controls="`${tab.id}-panel`"
                role="tab"
                @click="activeTab = tab.id"
              >
                <svg v-if="tab.id === 'overview'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="7" height="7" rx="1.5" />
                  <rect x="14" y="3" width="7" height="7" rx="1.5" />
                  <rect x="14" y="14" width="7" height="7" rx="1.5" />
                  <rect x="3" y="14" width="7" height="7" rx="1.5" />
                </svg>
                <svg v-else-if="tab.id === 'activity'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="9" />
                  <polyline points="12 7 12 12 15 14" />
                </svg>
                <svg v-else-if="tab.id === 'analytics'" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19V5M4 19h16M8 15v-3M12 15V9M16 15v-5" />
                </svg>
                <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="3" />
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                <span>{{ tab.label }}</span>
              </button>
            </nav>
            <span class="member-since-note">Member since {{ formattedJoinedDate }}</span>
          </div>
        </div>
      </header>

      <!-- ================= TAB CONTENT ================= -->
      <main class="profile-main">
        <!-- OVERVIEW TAB: 12-COLUMN ASYMMETRIC BENTO COCKPIT -->
        <section
          v-if="activeTab === 'overview'"
          id="overview-panel"
          class="cockpit-grid"
          role="tabpanel"
          aria-labelledby="overview-tab"
        >
          <!-- LEFT RAIL (Cols 1-7): Metallic 3D Digital Pass + 3 Interactive Quick Dock Tiles -->
          <div class="cockpit-left-rail">
            <!-- Metallic 3D Digital Parkly Member Pass -->
            <article
              class="member-pass-card bento-animate"
              :style="isPassHover ? {
                transform: `perspective(1000px) rotateX(${passTiltX}deg) rotateY(${passTiltY}deg)`,
                '--foil-x': `${passGlowX}%`,
                '--foil-y': `${passGlowY}%`,
                '--foil-opacity': 1,
              } : undefined"
              @mousemove="handlePassPointerMove"
              @mouseleave="handlePassPointerLeave"
            >
              <div class="pass-foil-sheen" aria-hidden="true"></div>
              <svg class="pass-orbit-svg" viewBox="0 0 640 300" fill="none" aria-hidden="true">
                <circle cx="530" cy="145" r="185" stroke="#14B8A6" stroke-width="1.2" stroke-dasharray="5 6" opacity="0.34" />
                <circle cx="530" cy="145" r="265" stroke="#94A3B8" stroke-width="1" stroke-dasharray="3 8" opacity="0.18" />
                <circle cx="95" cy="275" r="145" stroke="#14B8A6" stroke-width="1" stroke-dasharray="4 7" opacity="0.22" />
              </svg>

              <div class="pass-top">
                <div class="pass-brand">
                  <span class="pass-chip">P</span>
                  <div>
                    <span class="pass-eyebrow">PARKLY DIGITAL GATE PASS</span>
                    <span class="pass-tier">{{ roleLabel }} Tier</span>
                  </div>
                </div>
                <div class="pass-top-actions">
                  <button
                    type="button"
                    class="pass-toggle-btn"
                    @click.stop="showPassDetails = !showPassDetails"
                  >
                    {{ showPassDetails ? 'Front Card' : 'Gate Preview' }}
                  </button>
                  <span class="pass-status-dot" :class="{ active: user.is_active }">
                    <i></i>{{ user.is_active ? 'READY' : 'PENDING' }}
                  </span>
                </div>
              </div>

              <!-- Front Face -->
              <template v-if="!showPassDetails">
                <div class="pass-holder-block">
                  <span class="pass-label">REGISTERED PASS HOLDER</span>
                  <h2 class="pass-holder-name">{{ user.name }}</h2>
                  <p class="pass-holder-email">{{ user.email }}</p>
                </div>

                <div class="pass-bottom">
                  <div class="pass-meta-group">
                    <div>
                      <span class="pass-label">JOINED</span>
                      <strong>{{ shortJoinedDate }}</strong>
                    </div>
                    <div>
                      <span class="pass-label">ACCESS MODE</span>
                      <strong>{{ user.role === 'operator' ? 'Facility Portal' : 'Automated Gate' }}</strong>
                    </div>
                    <div>
                      <span class="pass-label">PHONE</span>
                      <strong>{{ user.phone || 'Unlinked' }}</strong>
                    </div>
                  </div>
                  <router-link to="/#discovery" class="pass-cta">
                    <span>Reserve First Spot</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <line x1="5" y1="12" x2="19" y2="12" />
                      <polyline points="12 5 19 12 12 19" />
                    </svg>
                  </router-link>
                </div>
              </template>

              <!-- Gate Preview Back Face -->
              <template v-else>
                <div class="pass-gate-preview">
                  <div class="gate-qr-frame" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none">
                      <rect x="4" y="4" width="20" height="20" rx="4" stroke="#5EEAD4" stroke-width="2.5" />
                      <rect x="10" y="10" width="8" height="8" rx="1.5" fill="#5EEAD4" />
                      <rect x="40" y="4" width="20" height="20" rx="4" stroke="#5EEAD4" stroke-width="2.5" />
                      <rect x="46" y="10" width="8" height="8" rx="1.5" fill="#5EEAD4" />
                      <rect x="4" y="40" width="20" height="20" rx="4" stroke="#5EEAD4" stroke-width="2.5" />
                      <rect x="10" y="46" width="8" height="8" rx="1.5" fill="#5EEAD4" />
                      <rect x="30" y="30" width="8" height="8" rx="2" fill="#14B8A6" />
                      <rect x="44" y="42" width="14" height="6" rx="2" fill="#94A3B8" opacity="0.6" />
                      <rect x="34" y="50" width="10" height="8" rx="2" fill="#5EEAD4" opacity="0.7" />
                    </svg>
                  </div>
                  <div class="gate-preview-copy">
                    <span class="pass-label">AUTOMATED GATE CHECK-IN</span>
                    <h3>Ready when you book</h3>
                    <p>Active booking QR passes render automatically here for contactless barrier entry.</p>
                  </div>
                </div>
              </template>
            </article>

            <!-- 3 Interactive Quick Dock Tiles -->
            <div class="quick-dock-grid">
              <router-link to="/#discovery" class="quick-dock-tile bento-animate">
                <span class="dock-icon teal">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                    <circle cx="12" cy="10" r="3" />
                  </svg>
                </span>
                <div class="dock-text">
                  <strong>Find Parking</strong>
                  <small>Search nearby lots</small>
                </div>
                <span class="dock-arrow">↗</span>
              </router-link>

              <router-link to="/#featured" class="quick-dock-tile bento-animate">
                <span class="dock-icon indigo">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="3" width="18" height="18" rx="4" />
                    <path d="M9 17V7h4a3 3 0 0 1 0 6H9" />
                  </svg>
                </span>
                <div class="dock-text">
                  <strong>Verified Garages</strong>
                  <small>Clear hourly rates</small>
                </div>
                <span class="dock-arrow">↗</span>
              </router-link>

              <button type="button" class="quick-dock-tile bento-animate" @click="chooseAvatar">
                <span class="dock-icon slate">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 7h3l2-2h6l2 2h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" />
                    <circle cx="12" cy="13" r="3" />
                  </svg>
                </span>
                <div class="dock-text">
                  <strong>Update Photo</strong>
                  <small>Preview avatar</small>
                </div>
                <span class="dock-arrow">+</span>
              </button>
            </div>
          </div>

          <!-- RIGHT RAIL (Cols 8-12): Live Radar Console + Account Setup Ring -->
          <div class="cockpit-right-rail">
            <!-- Live Radar Session Card -->
            <article class="session-radar-card bento-animate">
              <div class="radar-header-row">
                <div class="radar-visual" aria-hidden="true">
                  <svg viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="44" stroke="var(--color-border)" stroke-width="1.2" stroke-dasharray="4 5" />
                    <circle cx="50" cy="50" r="30" stroke="var(--color-primary)" stroke-width="1.2" opacity="0.35" />
                    <circle cx="50" cy="50" r="16" fill="var(--color-primary-light)" />
                    <g class="mini-radar-arm">
                      <line x1="50" y1="50" x2="50" y2="8" stroke="var(--color-primary)" stroke-width="1.8" stroke-linecap="round" />
                    </g>
                    <circle cx="50" cy="50" r="4" fill="var(--color-primary)" />
                  </svg>
                </div>
                <span class="standby-chip">STANDBY</span>
              </div>

              <div class="session-copy">
                <div class="eyebrow-row">
                  <span class="eyebrow-dot"></span>
                  <span class="eyebrow">ACTIVE SESSION RADAR</span>
                </div>
                <h3>No active parking session</h3>
                <p>Once you reserve a bay, live gate access status and bay directions synchronize here in real time.</p>
                <router-link to="/#featured" class="card-action-link">
                  Browse verified garages <span>→</span>
                </router-link>
              </div>
            </article>

            <!-- Account Setup Ring Card (100% Real Backend Fields) -->
            <article class="bento-card readiness-card bento-animate">
              <div class="readiness-header">
                <div>
                  <p class="card-kicker">ACCOUNT SETUP</p>
                  <h3>Identity &amp; Security</h3>
                </div>
                <div class="progress-ring-wrap" :title="`${completedSetupCount} of ${setupChecks.length} completed`">
                  <svg viewBox="0 0 64 64" class="progress-ring-svg">
                    <circle cx="32" cy="32" r="26" class="ring-track" />
                    <circle
                      cx="32"
                      cy="32"
                      r="26"
                      class="ring-fill"
                      :stroke-dasharray="setupRingCircumference"
                      :stroke-dashoffset="setupRingOffset"
                    />
                  </svg>
                  <span class="ring-text">{{ completedSetupCount }}/{{ setupChecks.length }}</span>
                </div>
              </div>

              <ul class="readiness-list">
                <li v-for="item in setupChecks" :key="item.id">
                  <span class="check-dot" :class="{ ok: item.ok }"></span>
                  <span class="check-label">{{ item.label }}</span>
                  <strong>{{ item.value }}</strong>
                </li>
              </ul>
            </article>
          </div>

          <!-- BOTTOM TELEMETRY SPLIT (6 + 6) -->
          <article class="bento-card bottom-left-card bento-animate">
            <div class="bento-card-head">
              <div>
                <p class="card-kicker">BOOKING TELEMETRY</p>
                <h3>Reservations &amp; Passes</h3>
              </div>
              <span class="empty-tag">No reservations yet</span>
            </div>

            <div class="telemetry-empty-stage">
              <strong class="empty-metric">—</strong>
              <svg class="baseline-svg" viewBox="0 0 360 54" fill="none" aria-hidden="true">
                <line x1="0" y1="42" x2="360" y2="42" stroke="var(--color-border)" stroke-width="1.5" stroke-dasharray="4 5" />
                <line x1="0" y1="20" x2="360" y2="20" stroke="var(--color-border)" stroke-width="1" opacity="0.45" />
                <circle cx="40" cy="42" r="3" fill="var(--color-border-hover)" />
                <circle cx="145" cy="42" r="3" fill="var(--color-border-hover)" />
                <circle cx="245" cy="42" r="3" fill="var(--color-border-hover)" />
                <circle cx="330" cy="42" r="4" fill="var(--color-primary)" opacity="0.7" />
              </svg>
            </div>
            <p class="card-muted-note">Your completed stays, parking passes, and booking totals appear automatically after your first trip.</p>
          </article>

          <article class="bento-card bottom-right-card bento-animate">
            <div class="bento-card-head">
              <div>
                <p class="card-kicker">PERSONAL INSIGHTS</p>
                <h3>Mobility Analytics</h3>
              </div>
              <span class="empty-tag">Awaiting activity</span>
            </div>

            <div class="telemetry-empty-stage">
              <strong class="empty-metric">—</strong>
              <div class="empty-bars-visual" aria-hidden="true">
                <span style="height: 24%"></span>
                <span style="height: 38%"></span>
                <span style="height: 30%"></span>
                <span style="height: 52%"></span>
                <span style="height: 28%"></span>
                <span style="height: 40%"></span>
                <span style="height: 22%"></span>
              </div>
            </div>
            <p class="card-muted-note">Parkly never invents usage data — personal insights generate strictly from verified parking history.</p>
          </article>
        </section>

        <!-- ACTIVITY TAB -->
        <section
          v-else-if="activeTab === 'activity'"
          id="activity-panel"
          class="activity-split"
          role="tabpanel"
          aria-labelledby="activity-tab"
        >
          <div class="editorial-empty-card">
            <svg class="empty-orbit-bg" viewBox="0 0 800 320" fill="none" aria-hidden="true">
              <circle cx="680" cy="160" r="190" stroke="var(--color-primary)" stroke-width="1.2" stroke-dasharray="4 7" opacity="0.22" />
              <circle cx="120" cy="270" r="140" stroke="var(--color-border-hover)" stroke-width="1" stroke-dasharray="5 7" opacity="0.45" />
            </svg>
            <div class="empty-icon-ring">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9" />
                <polyline points="12 7 12 12 15 14" />
              </svg>
            </div>
            <div class="eyebrow-row center">
              <span class="eyebrow-dot"></span>
              <span class="eyebrow">PARKING TIMELINE</span>
            </div>
            <h2>No parking activity recorded yet.</h2>
            <p>Your upcoming bookings, automated gate entries, and receipts will stream here in chronological order.</p>
            <router-link to="/#discovery">
              <AppButton variant="primary" size="md" pill>Explore Parking Locations</AppButton>
            </router-link>
          </div>

          <aside class="bento-card">
            <p class="card-kicker">STREAM CAPABILITIES</p>
            <h3>What appears here</h3>
            <ul class="capability-list">
              <li>
                <span class="cap-dot"></span>
                <div>
                  <strong>Gate Check-In Logs</strong>
                  <small>Entry &amp; exit timestamps for registered parking facilities.</small>
                </div>
              </li>
              <li>
                <span class="cap-dot"></span>
                <div>
                  <strong>Digital Parking Passes</strong>
                  <small>Active reservations with bay numbers and check-in codes.</small>
                </div>
              </li>
              <li>
                <span class="cap-dot"></span>
                <div>
                  <strong>Session Receipts</strong>
                  <small>Transparent hourly breakdowns once trips conclude.</small>
                </div>
              </li>
            </ul>
          </aside>
        </section>

        <!-- ANALYTICS TAB -->
        <section
          v-else-if="activeTab === 'analytics'"
          id="analytics-panel"
          class="analytics-cockpit"
          role="tabpanel"
          aria-labelledby="analytics-tab"
        >
          <div class="kpi-placeholder-row">
            <div class="bento-card">
              <p class="card-kicker">TOTAL TRIPS</p>
              <strong class="empty-metric">—</strong>
              <span class="card-muted-note">No trips recorded</span>
            </div>
            <div class="bento-card">
              <p class="card-kicker">HOURS PARKED</p>
              <strong class="empty-metric">—</strong>
              <span class="card-muted-note">No duration data</span>
            </div>
            <div class="bento-card">
              <p class="card-kicker">FAVORITE ZONE</p>
              <strong class="empty-metric">—</strong>
              <span class="card-muted-note">Unassigned</span>
            </div>
          </div>

          <div class="editorial-empty-card">
            <svg class="empty-orbit-bg" viewBox="0 0 800 320" fill="none" aria-hidden="true">
              <circle cx="140" cy="110" r="170" stroke="var(--color-primary)" stroke-width="1.2" stroke-dasharray="4 7" opacity="0.22" />
              <circle cx="670" cy="260" r="150" stroke="var(--color-border-hover)" stroke-width="1" stroke-dasharray="5 7" opacity="0.45" />
            </svg>
            <div class="empty-icon-ring">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3v18h18" />
                <path d="m19 9-5 5-4-4-3 3" />
              </svg>
            </div>
            <div class="eyebrow-row center">
              <span class="eyebrow-dot"></span>
              <span class="eyebrow">REAL-TIME INSIGHTS</span>
            </div>
            <h2>Analytics unlock after your first parking stay.</h2>
            <p>Parkly keeps charts blank until genuine trip telemetry exists on your account.</p>
          </div>
        </section>

        <!-- SETTINGS TAB: ADVANCED CONTROL STUDIO -->
        <section
          v-else
          id="settings-panel"
          class="settings-studio"
          role="tabpanel"
          aria-labelledby="settings-tab"
        >
          <!-- Left Sticky Rail: Live Card Preview + Interactive Appearance Studio -->
          <aside class="settings-rail">
            <div class="live-sync-card">
              <div class="sync-header">
                <span class="card-kicker">LIVE CARD PREVIEW</span>
                <span class="sync-badge" :class="{ dirty: isProfileDirty }">
                  <i></i>{{ isProfileDirty ? 'Unsaved draft' : 'Synced' }}
                </span>
              </div>

              <div class="sync-identity-row">
                <div class="sync-avatar">
                  <img v-if="displayAvatar" :src="displayAvatar" :alt="profileForm.name || user.name" />
                  <span v-else>{{ livePreviewInitials }}</span>
                </div>
                <div class="sync-text">
                  <strong>{{ profileForm.name.trim() || user.name }}</strong>
                  <small>{{ profileForm.email.trim() || user.email }}</small>
                  <span class="sync-phone">{{ profileForm.phone.trim() || 'No phone linked' }}</span>
                </div>
              </div>

              <div class="sync-footer-pills">
                <span class="role-pill">{{ roleLabel }}</span>
                <span class="status-pill" :class="{ active: user.is_active }">
                  <i></i>{{ statusLabel }}
                </span>
              </div>
            </div>

            <!-- Interactive Theme Appearance Picker -->
            <div class="studio-card">
              <div class="studio-head-mini">
                <p class="card-kicker">APPEARANCE</p>
                <h4>Workspace Theme</h4>
              </div>
              <div class="theme-picker-grid">
                <button
                  type="button"
                  class="theme-option-card"
                  :class="{ selected: currentTheme === 'light' }"
                  @click="setTheme('light')"
                >
                  <div class="theme-mini-ui light-ui" aria-hidden="true">
                    <span class="mini-bar"></span>
                    <span class="mini-line"></span>
                    <span class="mini-accent"></span>
                  </div>
                  <div class="theme-opt-label">
                    <span>Light</span>
                    <span v-if="currentTheme === 'light'" class="opt-check"></span>
                  </div>
                </button>

                <button
                  type="button"
                  class="theme-option-card"
                  :class="{ selected: currentTheme === 'dark' }"
                  @click="setTheme('dark')"
                >
                  <div class="theme-mini-ui dark-ui" aria-hidden="true">
                    <span class="mini-bar"></span>
                    <span class="mini-line"></span>
                    <span class="mini-accent"></span>
                  </div>
                  <div class="theme-opt-label">
                    <span>Dark</span>
                    <span v-if="currentTheme === 'dark'" class="opt-check"></span>
                  </div>
                </button>
              </div>
            </div>
          </aside>

          <!-- Right Main Studio Workspace -->
          <div class="settings-workspace">
            <!-- Panel 1: Identity & Contact Studio -->
            <article class="studio-card">
              <div class="studio-card-header">
                <div class="studio-title-group">
                  <div class="studio-icon teal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                  </div>
                  <div>
                    <p class="card-kicker">IDENTITY STUDIO</p>
                    <h3>Personal Information</h3>
                  </div>
                </div>
                <span class="verified-pill" :class="{ isVerified: Boolean(user.email_verified_at) }">
                  {{ user.email_verified_at ? 'Email Verified' : 'Email Unverified' }}
                </span>
              </div>

              <!-- Recessed Avatar Studio Well -->
              <div class="avatar-studio-well">
                <div class="well-left">
                  <div class="well-avatar">
                    <img v-if="displayAvatar" :src="displayAvatar" :alt="user.name" />
                    <span v-else>{{ livePreviewInitials }}</span>
                  </div>
                  <div>
                    <strong>Profile Avatar</strong>
                    <small>JPG, PNG or WebP image for your current browser session preview.</small>
                  </div>
                </div>
                <div class="well-actions">
                  <AppButton
                    v-if="avatarPreview"
                    variant="ghost"
                    size="sm"
                    pill
                    @click="clearAvatarPreview"
                  >
                    Remove
                  </AppButton>
                  <AppButton variant="secondary" size="sm" pill @click="chooseAvatar">
                    Upload Photo
                  </AppButton>
                </div>
              </div>

              <div v-if="profileSuccessMsg" class="form-banner success" role="status">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <span>{{ profileSuccessMsg }}</span>
              </div>

              <div v-else-if="profileErrors.general" class="form-banner error" role="alert">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="12" />
                  <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span>{{ profileErrors.general }}</span>
              </div>

              <form class="settings-form" novalidate @submit.prevent="handleSaveProfile">
                <div class="studio-form-row-2">
                  <AppInput
                    id="settings-name"
                    v-model="profileForm.name"
                    label="Full Name"
                    placeholder="Your full name"
                    :error="profileErrors.name"
                    required
                  >
                    <template #prefix>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                      </svg>
                    </template>
                  </AppInput>

                  <AppInput
                    id="settings-phone"
                    v-model="profileForm.phone"
                    type="tel"
                    label="Phone Number"
                    placeholder="+1 (555) 000-0000"
                    :error="profileErrors.phone"
                  >
                    <template #prefix>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                      </svg>
                    </template>
                  </AppInput>
                </div>

                <AppInput
                  id="settings-email"
                  v-model="profileForm.email"
                  type="email"
                  label="Email Address"
                  placeholder="name@example.com"
                  :error="profileErrors.email"
                  hint="Updating your email address clears existing verification until re-verified."
                  required
                >
                  <template #prefix>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="2" y="4" width="20" height="16" rx="2" />
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                  </template>
                </AppInput>

                <div class="readonly-meta-strip">
                  <div>
                    <span>Managed Account Role</span>
                    <strong>{{ roleLabel }}</strong>
                  </div>
                  <div>
                    <span>Member Since</span>
                    <strong>{{ formattedJoinedDate }}</strong>
                  </div>
                </div>

                <div class="studio-save-footer">
                  <span class="save-hint">
                    {{ isProfileDirty ? 'You have unsaved profile changes.' : 'All profile fields are up to date.' }}
                  </span>
                  <div class="save-btns">
                    <AppButton
                      type="button"
                      variant="ghost"
                      size="sm"
                      pill
                      :disabled="!isProfileDirty || isSavingProfile"
                      @click="syncProfileFormFromUser"
                    >
                      Reset
                    </AppButton>
                    <AppButton
                      type="submit"
                      variant="primary"
                      size="md"
                      pill
                      :loading="isSavingProfile"
                      :disabled="!isProfileDirty"
                    >
                      Save Changes
                    </AppButton>
                  </div>
                </div>
              </form>
            </article>

            <!-- Panel 2: Security & Credentials Vault -->
            <article class="studio-card">
              <div class="studio-card-header">
                <div class="studio-title-group">
                  <div class="studio-icon indigo">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                      <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                      <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                  </div>
                  <div>
                    <p class="card-kicker">SECURITY VAULT</p>
                    <h3>Password &amp; Access</h3>
                  </div>
                </div>
                <span class="empty-tag">Minimum 8 chars</span>
              </div>

              <div v-if="passwordSuccessMsg" class="form-banner success" role="status">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <span>{{ passwordSuccessMsg }}</span>
              </div>

              <div v-else-if="passwordErrors.general" class="form-banner error" role="alert">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="12" />
                  <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span>{{ passwordErrors.general }}</span>
              </div>

              <form class="settings-form" novalidate @submit.prevent="handleUpdatePassword">
                <AppInput
                  id="settings-current-pwd"
                  v-model="passwordForm.current_password"
                  :type="showCurrentPwd ? 'text' : 'password'"
                  label="Current Password"
                  placeholder="Enter your existing password"
                  :error="passwordErrors.current_password"
                  required
                >
                  <template #suffix>
                    <button
                      type="button"
                      class="pwd-icon-btn"
                      :aria-label="showCurrentPwd ? 'Hide current password' : 'Show current password'"
                      @click="showCurrentPwd = !showCurrentPwd"
                    >
                      <svg v-if="showCurrentPwd" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                      </svg>
                      <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                      </svg>
                    </button>
                  </template>
                </AppInput>

                <div class="studio-form-row-2">
                  <AppInput
                    id="settings-new-pwd"
                    v-model="passwordForm.password"
                    :type="showNewPwd ? 'text' : 'password'"
                    label="New Password"
                    placeholder="At least 8 characters"
                    :error="passwordErrors.password"
                    required
                  >
                    <template #suffix>
                      <button
                        type="button"
                        class="pwd-icon-btn"
                        :aria-label="showNewPwd ? 'Hide new password' : 'Show new password'"
                        @click="showNewPwd = !showNewPwd"
                      >
                        <svg v-if="showNewPwd" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                          <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </template>
                  </AppInput>

                  <AppInput
                    id="settings-confirm-pwd"
                    v-model="passwordForm.password_confirmation"
                    :type="showNewPwd ? 'text' : 'password'"
                    label="Confirm Password"
                    placeholder="Repeat new password"
                    :error="passwordErrors.password_confirmation"
                    required
                  />
                </div>

                <PasswordStrengthBar v-if="passwordForm.password" :password="passwordForm.password" />

                <div class="studio-save-footer">
                  <span class="save-hint">Protects your Parkly session across devices.</span>
                  <AppButton type="submit" variant="secondary" size="md" pill :loading="isSavingPassword">
                    Update Password
                  </AppButton>
                </div>
              </form>
            </article>

            <!-- Panel 3: Session Control & Sign Out -->
            <article class="studio-card session-danger-card">
              <div class="session-danger-inner">
                <div>
                  <div class="eyebrow-row">
                    <span class="eyebrow-dot"></span>
                    <span class="eyebrow">ACTIVE SANCTUM SESSION</span>
                  </div>
                  <h3>Device &amp; Session Management</h3>
                  <p>You are signed in via encrypted cookie-based authentication. Logging out terminates this device session cleanly.</p>
                </div>
                <AppButton variant="danger" size="md" pill :loading="isLoggingOut" @click="handleLogout">
                  Sign Out of Device
                </AppButton>
              </div>
            </article>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<style scoped>
.profile-page {
  min-height: calc(100vh - 92px);
  padding: 12px 0 88px;
}

.profile-container {
  width: min(1200px, calc(100% - 32px));
  margin: 0 auto;
}

/* ============================================================
   HERO PROFILE HEADER CARD
   ============================================================ */
.profile-hero-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 28px;
  overflow: hidden;
  box-shadow: var(--shadow-halo);
}

.profile-cover {
  position: relative;
  width: 100%;
  height: 282px;
  overflow: hidden;
  border-bottom: 1px solid var(--color-border);
}

.cover-top-badge {
  position: absolute;
  top: 18px;
  right: 20px;
  z-index: 2;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 14px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.14em;
  color: var(--color-text);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  box-shadow: var(--shadow-sm);
}

.profile-page.is-dark .cover-top-badge,
:global([data-theme='dark']) .cover-top-badge {
  color: #f1f5f9;
  background: rgba(11, 18, 32, 0.85);
  border-color: rgba(148, 163, 184, 0.28);
}

@media (prefers-color-scheme: dark) {
  :global(:root:not([data-theme='light'])) .cover-top-badge {
    color: #f1f5f9;
    background: rgba(11, 18, 32, 0.85);
    border-color: rgba(148, 163, 184, 0.28);
  }
}

.live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--color-primary);
  box-shadow: 0 0 8px var(--color-primary);
}

.identity-panel {
  padding: 22px 32px 18px;
  background: var(--color-surface);
}

.identity-main {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 24px;
  padding-bottom: 22px;
}

.identity-left {
  display: flex;
  align-items: center;
  gap: 28px;
  min-width: 0;
  flex: 1;
}

.avatar-wrap {
  position: relative;
  z-index: 3;
  flex: 0 0 auto;
  margin-top: -88px;
}

.avatar-orbit-ring {
  position: absolute;
  inset: -7px;
  border-radius: 50%;
  border: 1.5px dashed rgba(20, 184, 166, 0.5);
  pointer-events: none;
  animation: avatar-orbit-spin 24s linear infinite;
}

@keyframes avatar-orbit-spin {
  to {
    transform: rotate(360deg);
  }
}

.avatar-large {
  width: 138px;
  height: 138px;
  border: 5px solid var(--color-surface);
  border-radius: 50%;
  position: relative;
  cursor: pointer;
  background: linear-gradient(135deg, var(--color-primary), #0f172a);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  font-weight: 800;
  padding: 0;
  box-shadow: 0 14px 32px rgba(15, 118, 110, 0.28);
  transition: transform var(--transition-fast), box-shadow var(--transition-fast);
}

.avatar-large:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 18px 36px rgba(15, 118, 110, 0.36);
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-camera {
  position: absolute;
  right: 3px;
  bottom: 3px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: var(--color-primary);
  color: #ffffff;
  border: 3px solid var(--color-surface);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.avatar-input {
  display: none;
}

.identity-copy {
  flex: 1;
  min-width: 0;
}

.eyebrow-row {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  margin-bottom: 6px;
}

.eyebrow-row.center {
  justify-content: center;
}

.eyebrow-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-primary);
}

.eyebrow,
.card-kicker {
  font-size: 10.5px;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.eyebrow {
  color: var(--color-primary);
}

.identity-copy h1 {
  margin: 0;
  font-size: clamp(1.55rem, 2.6vw, 2.25rem);
  letter-spacing: -0.03em;
  line-height: 1.15;
  color: var(--color-text);
  word-break: break-word;
}

.identity-email {
  margin: 4px 0 12px;
  color: var(--color-text-muted);
  font-size: 14px;
  word-break: break-all;
}

.identity-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
}

.role-pill,
.status-pill,
.verified-pill {
  border-radius: 999px;
  padding: 4px 12px;
  font-weight: 700;
  font-size: 11px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.role-pill {
  color: var(--color-primary);
  background: var(--color-primary-light);
}

.status-pill {
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  background: var(--color-surface-subtle);
}

.status-pill i {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #94a3b8;
}

.status-pill.active {
  color: #059669;
  border-color: rgba(5, 150, 105, 0.28);
  background: rgba(16, 185, 129, 0.1);
}

.status-pill.active i {
  background: #10b981;
}

.verified-pill {
  color: var(--color-text-muted);
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
}

.verified-pill.isVerified {
  color: var(--color-primary);
  background: var(--color-primary-light);
  border-color: transparent;
}

.identity-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
  flex-wrap: wrap;
}

.logout-pill-btn {
  color: var(--color-danger);
  border: 1px solid var(--color-border);
}

.logout-pill-btn:hover {
  background-color: var(--color-danger-bg);
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

/* Pill Tabs */
.tabs-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--color-border);
}

.pill-tabs {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px;
  border-radius: 999px;
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
}

.pill-tab {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: var(--color-text-muted);
  font: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition-fast);
}

.pill-tab:hover {
  color: var(--color-text);
}

.pill-tab.active {
  background: var(--color-surface);
  color: var(--color-primary);
  box-shadow: var(--shadow-sm);
}

.member-since-note {
  font-size: 12px;
  color: var(--color-text-muted);
  font-weight: 500;
}

/* ============================================================
   12-COLUMN ASYMMETRIC BENTO COCKPIT
   ============================================================ */
.profile-main {
  margin-top: 26px;
}

.cockpit-grid {
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 20px;
}

.cockpit-left-rail {
  grid-column: span 7;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.cockpit-right-rail {
  grid-column: span 5;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.bottom-left-card {
  grid-column: span 6;
}

.bottom-right-card {
  grid-column: span 6;
}

/* Signature Luxury Digital Pass Card */
.member-pass-card {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 28px 30px;
  background: linear-gradient(135deg, #0b1220 0%, #13253e 55%, #0d4b4a 100%);
  color: #f8fafc;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 274px;
  box-shadow: 0 22px 44px -14px rgba(11, 18, 32, 0.35);
  transition: transform 240ms cubic-bezier(0.22, 1, 0.36, 1), box-shadow 240ms ease;
  will-change: transform;
}

.member-pass-card:hover {
  box-shadow: 0 28px 54px -12px rgba(15, 118, 110, 0.38);
}

.pass-foil-sheen {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 1;
  opacity: var(--foil-opacity, 0);
  background: radial-gradient(
    360px circle at var(--foil-x, 50%) var(--foil-y, 50%),
    rgba(94, 234, 212, 0.22),
    rgba(20, 184, 166, 0.06) 45%,
    transparent 70%
  );
  transition: opacity 220ms ease;
}

.pass-orbit-svg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.pass-top,
.pass-holder-block,
.pass-bottom,
.pass-gate-preview {
  position: relative;
  z-index: 2;
}

.pass-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.pass-brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.pass-chip {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: linear-gradient(135deg, #14b8a6, #0f766e);
  color: #ffffff;
  font-weight: 800;
  font-size: 18px;
  display: grid;
  place-items: center;
  box-shadow: 0 6px 16px rgba(20, 184, 166, 0.35);
}

.pass-eyebrow {
  display: block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.16em;
  color: #5eead4;
}

.pass-tier {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #e2e8f0;
}

.pass-top-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pass-toggle-btn {
  padding: 5px 11px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #e2e8f0;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color var(--transition-fast);
}

.pass-toggle-btn:hover {
  background: rgba(20, 184, 166, 0.24);
  border-color: #14b8a6;
}

.pass-status-dot {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 5px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.16);
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  color: #cbd5e1;
}

.pass-status-dot i {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #94a3b8;
}

.pass-status-dot.active i {
  background: #34d399;
  box-shadow: 0 0 8px #34d399;
}

.pass-holder-block {
  margin: 28px 0 24px;
}

.pass-label {
  display: block;
  font-size: 9.5px;
  font-weight: 700;
  letter-spacing: 0.14em;
  color: #94a3b8;
  margin-bottom: 4px;
}

.pass-holder-name {
  margin: 0;
  font-size: clamp(1.35rem, 2.2vw, 1.85rem);
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.02em;
}

.pass-holder-email {
  margin: 4px 0 0;
  font-size: 13px;
  color: #cbd5e1;
}

.pass-bottom {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.pass-meta-group {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.pass-meta-group strong {
  font-size: 13px;
  color: #f8fafc;
}

.pass-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 999px;
  background: #14b8a6;
  color: #0b1220;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: transform var(--transition-fast), background-color var(--transition-fast);
}

.pass-cta:hover {
  background: #2dd4bf;
  transform: translateY(-1px);
  text-decoration: none;
}

/* Pass Gate Preview Face */
.pass-gate-preview {
  display: flex;
  align-items: center;
  gap: 22px;
  margin-top: 22px;
  padding: 18px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px dashed rgba(94, 234, 212, 0.35);
}

.gate-qr-frame {
  width: 78px;
  height: 78px;
  flex-shrink: 0;
  padding: 8px;
  border-radius: 12px;
  background: rgba(11, 18, 32, 0.65);
}

.gate-qr-frame svg {
  width: 100%;
  height: 100%;
}

.gate-preview-copy h3 {
  margin: 2px 0 6px;
  font-size: 17px;
  color: #ffffff;
}

.gate-preview-copy p {
  margin: 0;
  font-size: 12.5px;
  color: #cbd5e1;
  line-height: 1.5;
}

/* Quick Mobility Action Dock */
.quick-dock-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.quick-dock-tile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 18px;
  border-radius: 18px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  text-decoration: none;
  cursor: pointer;
  text-align: left;
  transition: border-color var(--transition-fast), transform var(--transition-fast), box-shadow var(--transition-fast);
}

.quick-dock-tile:hover {
  border-color: var(--color-primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  text-decoration: none;
}

.dock-icon {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  flex-shrink: 0;
}

.dock-icon.teal {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.dock-icon.indigo {
  background: rgba(99, 102, 241, 0.12);
  color: #6366f1;
}

.dock-icon.slate {
  background: var(--color-surface-subtle);
  color: var(--color-text-muted);
}

.dock-text {
  flex: 1;
  min-width: 0;
}

.dock-text strong,
.dock-text small {
  display: block;
}

.dock-text strong {
  font-size: 13px;
  color: var(--color-text);
}

.dock-text small {
  font-size: 11px;
  color: var(--color-text-muted);
}

.dock-arrow {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-primary);
}

/* Right Rail: Radar Session Console */
.session-radar-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 22px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  box-shadow: var(--shadow-card-elevated);
}

.radar-header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.radar-visual {
  width: 68px;
  height: 68px;
}

.radar-visual svg {
  width: 100%;
  height: 100%;
}

.mini-radar-arm {
  transform-origin: 50px 50px;
  animation: mini-radar-spin 4s linear infinite;
}

@keyframes mini-radar-spin {
  to {
    transform: rotate(360deg);
  }
}

.standby-chip {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.12em;
  padding: 4px 10px;
  border-radius: 999px;
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  color: var(--color-text-muted);
}

.session-copy h3 {
  margin: 0 0 6px;
  font-size: 19px;
}

.session-copy p {
  margin: 0 0 14px;
  font-size: 13px;
  color: var(--color-text-muted);
  line-height: 1.55;
}

.card-action-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 700;
  color: var(--color-primary);
}

/* Account Setup Progress Ring Card */
.bento-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 22px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
}

.bento-card:hover {
  border-color: var(--color-border-hover);
  box-shadow: var(--shadow-md);
}

.readiness-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 16px;
}

.progress-ring-wrap {
  position: relative;
  width: 58px;
  height: 58px;
  display: grid;
  place-items: center;
  flex-shrink: 0;
}

.progress-ring-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.ring-track {
  fill: none;
  stroke: var(--color-surface-subtle);
  stroke-width: 5;
}

.ring-fill {
  fill: none;
  stroke: var(--color-primary);
  stroke-width: 5;
  stroke-linecap: round;
  transition: stroke-dashoffset 400ms ease;
}

.ring-text {
  position: absolute;
  font-size: 12px;
  font-weight: 800;
  color: var(--color-text);
}

.readiness-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.readiness-list li {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding: 10px 12px;
  border-radius: 12px;
  background: var(--color-surface-subtle);
  font-size: 12px;
  position: relative;
}

.check-dot {
  position: absolute;
  top: 11px;
  right: 11px;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #94a3b8;
}

.check-dot.ok {
  background: #10b981;
  box-shadow: 0 0 6px rgba(16, 185, 129, 0.45);
}

.check-label {
  color: var(--color-text-muted);
  font-size: 11px;
}

.readiness-list strong {
  color: var(--color-text);
  font-size: 12.5px;
  word-break: break-word;
}

/* Bottom Telemetry Split */
.bento-card-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.card-kicker {
  color: var(--color-text-muted);
  margin: 0 0 4px;
}

.bento-card h3 {
  margin: 0;
  font-size: 17px;
}

.telemetry-empty-stage {
  display: flex;
  align-items: flex-end;
  gap: 20px;
  margin-bottom: 14px;
}

.empty-metric {
  font-size: 36px;
  font-weight: 700;
  color: var(--color-text-subtle);
  line-height: 1;
}

.empty-tag {
  font-size: 11px;
  font-weight: 600;
  color: var(--color-text-muted);
  background: var(--color-surface-subtle);
  padding: 4px 10px;
  border-radius: 999px;
}

.baseline-svg {
  flex: 1;
  height: 48px;
}

.empty-bars-visual {
  flex: 1;
  height: 48px;
  display: flex;
  align-items: flex-end;
  gap: 8px;
  border-bottom: 1.5px dashed var(--color-border);
}

.empty-bars-visual span {
  flex: 1;
  border-radius: 6px 6px 0 0;
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  border-bottom: 0;
}

.card-muted-note {
  margin: auto 0 0;
  font-size: 12.5px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* ============================================================
   ACTIVITY, ANALYTICS & SETTINGS TABS
   ============================================================ */
.activity-split {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 20px;
}

.capability-list {
  list-style: none;
  padding: 0;
  margin: 18px 0 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.capability-list li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px;
  border-radius: 14px;
  background: var(--color-surface-subtle);
}

.cap-dot {
  width: 8px;
  height: 8px;
  margin-top: 6px;
  border-radius: 50%;
  background: var(--color-primary);
  flex-shrink: 0;
}

.capability-list strong,
.capability-list small {
  display: block;
}

.capability-list strong {
  font-size: 13.5px;
  margin-bottom: 2px;
}

.capability-list small {
  font-size: 12px;
  color: var(--color-text-muted);
}

.analytics-cockpit {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.kpi-placeholder-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.editorial-empty-card {
  position: relative;
  overflow: hidden;
  min-height: 330px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 26px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 44px 28px;
  box-shadow: var(--shadow-card-elevated);
}

.empty-orbit-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.empty-icon-ring {
  position: relative;
  z-index: 2;
  width: 64px;
  height: 64px;
  border-radius: 20px;
  background: var(--color-primary-light);
  color: var(--color-primary);
  display: grid;
  place-items: center;
  margin-bottom: 18px;
}

.editorial-empty-card h2 {
  position: relative;
  z-index: 2;
  margin: 4px 0 10px;
  font-size: clamp(1.35rem, 2.2vw, 1.75rem);
}

.editorial-empty-card p {
  position: relative;
  z-index: 2;
  max-width: 460px;
  color: var(--color-text-muted);
  font-size: 14px;
  margin: 0 0 24px;
}

/* ============================================================
   ADVANCED SETTINGS CONTROL STUDIO
   ============================================================ */
.settings-studio {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 22px;
  align-items: start;
}

.settings-rail {
  display: flex;
  flex-direction: column;
  gap: 18px;
  position: sticky;
  top: 96px;
}

.live-sync-card {
  border-radius: 22px;
  padding: 22px;
  background: linear-gradient(145deg, #0b1220 0%, #13253e 65%, #0e3d3f 100%);
  color: #f8fafc;
  border: 1px solid rgba(20, 184, 166, 0.25);
  box-shadow: 0 18px 36px -14px rgba(11, 18, 32, 0.45);
}

.sync-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 18px;
}

.sync-header .card-kicker {
  color: #5eead4;
  margin: 0;
}

.sync-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  background: rgba(16, 185, 129, 0.14);
  color: #34d399;
}

.sync-badge i {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #34d399;
}

.sync-badge.dirty {
  background: rgba(245, 158, 11, 0.18);
  color: #fbbf24;
}

.sync-badge.dirty i {
  background: #fbbf24;
}

.sync-identity-row {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
}

.sync-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #14b8a6, #0f766e);
  border: 2px solid rgba(94, 234, 212, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 800;
  color: #ffffff;
  overflow: hidden;
  flex-shrink: 0;
}

.sync-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.sync-text {
  min-width: 0;
  flex: 1;
}

.sync-text strong,
.sync-text small,
.sync-phone {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.sync-text strong {
  font-size: 15px;
  color: #ffffff;
}

.sync-text small {
  font-size: 12px;
  color: #cbd5e1;
  margin-top: 2px;
}

.sync-phone {
  font-size: 11px;
  color: #5eead4;
  margin-top: 3px;
}

.sync-footer-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.studio-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 22px;
  padding: 24px;
  box-shadow: var(--shadow-sm);
}

.studio-head-mini h4 {
  margin: 0 0 14px;
  font-size: 15px;
}

.theme-picker-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.theme-option-card {
  border: 1.5px solid var(--color-border);
  background: var(--color-surface-subtle);
  border-radius: 14px;
  padding: 10px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: border-color var(--transition-fast), transform var(--transition-fast);
}

.theme-option-card:hover {
  border-color: var(--color-border-hover);
  transform: translateY(-1px);
}

.theme-option-card.selected {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-ring);
}

.theme-mini-ui {
  height: 44px;
  border-radius: 8px;
  padding: 7px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.light-ui {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.dark-ui {
  background: #0b1220;
  border: 1px solid #26364c;
}

.mini-bar {
  width: 60%;
  height: 6px;
  border-radius: 3px;
  background: currentColor;
  opacity: 0.25;
}

.light-ui .mini-bar,
.light-ui .mini-line {
  color: #0f172a;
}

.dark-ui .mini-bar,
.dark-ui .mini-line {
  color: #f1f5f9;
}

.mini-line {
  width: 85%;
  height: 4px;
  border-radius: 2px;
  background: currentColor;
  opacity: 0.14;
}

.mini-accent {
  width: 34%;
  height: 6px;
  border-radius: 999px;
  background: #14b8a6;
  margin-top: auto;
}

.theme-opt-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text);
}

.opt-check {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--color-primary);
}

.settings-workspace {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.studio-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 20px;
}

.studio-title-group {
  display: flex;
  align-items: center;
  gap: 14px;
}

.studio-icon {
  width: 42px;
  height: 42px;
  border-radius: 13px;
  display: grid;
  place-items: center;
  flex-shrink: 0;
}

.studio-icon.teal {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.studio-icon.indigo {
  background: rgba(99, 102, 241, 0.12);
  color: #6366f1;
}

.studio-title-group h3 {
  margin: 0;
  font-size: 17px;
}

.avatar-studio-well {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 16px;
  border-radius: 16px;
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
  margin-bottom: 18px;
}

.well-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.well-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), #1e293b);
  color: #ffffff;
  font-weight: 800;
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
}

.well-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.well-left strong,
.well-left small {
  display: block;
}

.well-left strong {
  font-size: 13.5px;
}

.well-left small {
  font-size: 11.5px;
  color: var(--color-text-muted);
}

.well-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.studio-form-row-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 14px;
}

.form-banner.success {
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.3);
  color: #10b981;
}

.form-banner.error {
  background: var(--color-danger-bg);
  border: 1px solid var(--color-danger-border);
  color: var(--color-danger);
}

.readonly-meta-strip {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  padding: 13px 16px;
  border-radius: 14px;
  background: var(--color-surface-subtle);
  border: 1px solid var(--color-border);
}

.readonly-meta-strip span,
.readonly-meta-strip strong {
  display: block;
}

.readonly-meta-strip span {
  font-size: 11px;
  color: var(--color-text-muted);
  margin-bottom: 2px;
}

.readonly-meta-strip strong {
  font-size: 13px;
  color: var(--color-text);
}

.studio-save-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding-top: 14px;
  margin-top: 4px;
  border-top: 1px solid var(--color-border);
}

.save-hint {
  font-size: 12px;
  color: var(--color-text-muted);
}

.save-btns {
  display: flex;
  align-items: center;
  gap: 8px;
}

.pwd-icon-btn {
  background: none;
  border: 0;
  color: var(--color-text-muted);
  cursor: pointer;
  display: grid;
  place-items: center;
  padding: 2px;
  transition: color var(--transition-fast);
}

.pwd-icon-btn:hover {
  color: var(--color-primary);
}

.session-danger-card {
  border-color: var(--color-danger-border);
}

.session-danger-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.session-danger-inner h3 {
  margin: 2px 0 4px;
  font-size: 16px;
}

.session-danger-inner p {
  margin: 0;
  font-size: 12.5px;
  color: var(--color-text-muted);
  max-width: 520px;
}

/* Loading */
.profile-loading {
  min-height: 60vh;
  display: grid;
  place-items: center;
  align-content: center;
  gap: 14px;
  color: var(--color-text-muted);
}

.loading-spinner {
  width: 30px;
  height: 30px;
  border: 3px solid var(--color-border);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: profile-spin 0.8s linear infinite;
}

@keyframes profile-spin {
  to {
    transform: rotate(360deg);
  }
}

/* ============================================================
   RESPONSIVE BREAKPOINTS
   ============================================================ */
@media (max-width: 1024px) {
  .cockpit-left-rail,
  .cockpit-right-rail,
  .bottom-left-card,
  .bottom-right-card {
    grid-column: span 12;
  }

  .activity-split,
  .settings-studio,
  .kpi-placeholder-row {
    grid-template-columns: 1fr;
  }

  .settings-rail {
    position: static;
  }
}

@media (max-width: 820px) {
  .profile-cover {
    height: 220px;
  }

  .identity-panel {
    padding: 18px 22px 16px;
  }

  .identity-main {
    flex-direction: column;
    align-items: flex-start;
    gap: 18px;
  }

  .avatar-wrap {
    margin-top: -68px;
  }

  .avatar-large {
    width: 114px;
    height: 114px;
    font-size: 32px;
  }

  .tabs-bar {
    flex-direction: column;
    align-items: flex-start;
  }

  .pill-tabs {
    width: 100%;
    overflow-x: auto;
  }

  .pill-tab {
    flex-shrink: 0;
  }

  .quick-dock-grid,
  .studio-form-row-2 {
    grid-template-columns: 1fr;
  }

  .session-danger-inner,
  .avatar-studio-well,
  .studio-save-footer {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 560px) {
  .profile-container {
    width: calc(100% - 20px);
  }

  .profile-hero-card {
    border-radius: 22px;
  }

  .profile-cover {
    height: 196px;
  }

  .cover-top-badge {
    top: 10px;
    right: 12px;
    padding: 4px 10px;
    font-size: 8.5px;
  }

  .identity-panel {
    padding: 16px 16px 14px;
  }

  .identity-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .avatar-wrap {
    margin-top: -42px;
  }

  .avatar-large {
    width: 84px;
    height: 84px;
    font-size: 24px;
  }

  .avatar-camera {
    width: 28px;
    height: 28px;
  }

  .member-pass-card {
    padding: 22px 20px;
  }

  .readiness-list,
  .readonly-meta-strip {
    grid-template-columns: 1fr;
  }
}
</style>
