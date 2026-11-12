<script setup>
/**
 * ProfileView Component
 * Route: /profile
 * Personal dashboard displaying authenticated user data from Pinia auth store / backend API.
 */
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import AppButton from '../components/common/AppButton.vue'
import ProfileParkingAnimation from '../components/profile/ProfileParkingAnimation.vue'

const router = useRouter()
const authStore = useAuthStore()

const activeTab = ref('overview')
const isLoading = ref(false)
const isLoggingOut = ref(false)
const avatarPreview = ref('')
const avatarInput = ref(null)

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

onMounted(async () => {
  if (!authStore.user) {
    isLoading.value = true
    try {
      await authStore.fetchUser()
    } catch {
      router.push('/login')
    } finally {
      isLoading.value = false
    }
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
  <div class="profile-page">
    <div v-if="isLoading" class="profile-loading">
      <span class="loading-spinner"></span>
      <p>Loading your Parkly dashboard...</p>
    </div>

    <div v-else-if="user" class="profile-container">
      <!-- Unified Facebook-Style Profile Header Card (Cover + Identity + Tabs) -->
      <header class="profile-header-card">
        <!-- Wide Animated Cover Banner -->
        <div class="profile-cover" aria-label="Smart parking cover illustration">
          <ProfileParkingAnimation />
        </div>

        <!-- Identity Section Sitting Cleanly Below Cover -->
        <div class="identity-panel">
          <div class="identity-main">
            <div class="identity-left">
              <!-- Overlapping Circular Avatar (sits safely over cover's left grass island) -->
              <div class="avatar-wrap">
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

              <!-- User Info Stack -->
              <div class="identity-copy">
                <p class="eyebrow">{{ user.role === 'operator' ? 'OPERATOR ACCOUNT' : 'DRIVER ACCOUNT' }}</p>
                <h1>{{ user.name }}</h1>
                <p class="identity-email">{{ user.email }}</p>
                <div class="identity-meta">
                  <span class="role-pill">{{ roleLabel }}</span>
                  <span class="meta-dot" aria-hidden="true">•</span>
                  <span>Member since {{ formattedJoinedDate }}</span>
                  <span class="meta-dot" aria-hidden="true">•</span>
                  <span>{{ user.email_verified_at ? 'Email verified' : 'Email not verified' }}</span>
                </div>
              </div>
            </div>

            <!-- Right Status & Logout Actions -->
            <div class="identity-actions">
              <span class="status-pill" :class="{ active: user.is_active }">
                <i></i>{{ statusLabel }}
              </span>
              <AppButton
                variant="ghost"
                size="sm"
                class="logout-pill-btn"
                :loading="isLoggingOut"
                @click="handleLogout"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <polyline points="16 17 21 12 16 7" />
                  <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                <span>Logout</span>
              </AppButton>
            </div>
          </div>

          <!-- Integrated Tab Bar -->
          <nav class="profile-tabs" role="tablist" aria-label="Profile sections">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              :id="`${tab.id}-tab`"
              type="button"
              class="profile-tab"
              :class="{ active: activeTab === tab.id }"
              :aria-selected="activeTab === tab.id"
              :aria-controls="`${tab.id}-panel`"
              role="tab"
              @click="activeTab = tab.id"
            >
              {{ tab.label }}
            </button>
          </nav>
        </div>
      </header>

      <!-- Tab Content Area -->
      <main class="profile-main">
        <!-- OVERVIEW TAB -->
        <section
          v-if="activeTab === 'overview'"
          id="overview-panel"
          class="tab-panel"
          role="tabpanel"
          aria-labelledby="overview-tab"
        >
          <div class="section-heading">
            <div>
              <p class="eyebrow">YOUR PARKLY SPACE</p>
              <h2>Everything in one place.</h2>
            </div>
            <router-link to="/" class="text-action">Find parking <span>↗</span></router-link>
          </div>

          <div class="overview-grid">
            <article class="dashboard-card featured-card">
              <div class="card-icon teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C2.1 11.1 2 11.5 2 12v4c0 .6.4 1 1 1h2" />
                  <circle cx="7" cy="17" r="2" />
                  <circle cx="17" cy="17" r="2" />
                </svg>
              </div>
              <p class="card-kicker">PARKING ACTIVITY</p>
              <h3>No parking activity yet</h3>
              <p>When you book your first spot, your active passes and parking history will appear here.</p>
              <router-link to="/" class="card-link">Explore parking <span>→</span></router-link>
            </article>

            <article class="dashboard-card">
              <div class="card-icon navy">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
              </div>
              <p class="card-kicker">BOOKING SUMMARY</p>
              <strong class="empty-metric">—</strong>
              <p>Reservation counts and totals will show after your first completed booking.</p>
            </article>

            <article class="dashboard-card">
              <div class="card-icon violet">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19V5M4 19h16M8 16v-3M12 16V8M16 16v-6" />
                </svg>
              </div>
              <p class="card-kicker">PERSONAL INSIGHTS</p>
              <strong class="empty-metric">—</strong>
              <p>Usage trends unlock automatically once your account records real parking trips.</p>
            </article>
          </div>

          <div class="details-card">
            <div class="section-heading compact">
              <div>
                <p class="eyebrow">ACCOUNT SNAPSHOT</p>
                <h2>Your verified account details.</h2>
              </div>
            </div>
            <div class="snapshot-grid">
              <div>
                <span>Account status</span>
                <strong>{{ statusLabel }}</strong>
              </div>
              <div>
                <span>Account type</span>
                <strong>{{ roleLabel }}</strong>
              </div>
              <div>
                <span>Phone number</span>
                <strong>{{ user.phone || 'Not provided' }}</strong>
              </div>
              <div>
                <span>Email verification</span>
                <strong>{{ user.email_verified_at ? 'Verified' : 'Not verified' }}</strong>
              </div>
            </div>
          </div>
        </section>

        <!-- ACTIVITY TAB -->
        <section
          v-else-if="activeTab === 'activity'"
          id="activity-panel"
          class="tab-panel empty-panel"
          role="tabpanel"
          aria-labelledby="activity-tab"
        >
          <div class="empty-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </div>
          <p class="eyebrow">RECENT ACTIVITY</p>
          <h2>Your Parkly timeline is ready.</h2>
          <p>No reservations or parking sessions have been recorded yet. Book a spot to start tracking your activity here.</p>
          <router-link to="/">
            <AppButton variant="primary" size="md">Find parking</AppButton>
          </router-link>
        </section>

        <!-- ANALYTICS TAB -->
        <section
          v-else-if="activeTab === 'analytics'"
          id="analytics-panel"
          class="tab-panel empty-panel"
          role="tabpanel"
          aria-labelledby="analytics-tab"
        >
          <div class="empty-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 3v18h18" />
              <path d="m19 9-5 5-4-4-3 3" />
            </svg>
          </div>
          <p class="eyebrow">ANALYTICS</p>
          <h2>Insights follow real parking activity.</h2>
          <p>Parkly waits for genuine trip and reservation records before displaying personal statistics.</p>
        </section>

        <!-- SETTINGS TAB -->
        <section
          v-else
          id="settings-panel"
          class="tab-panel settings-panel"
          role="tabpanel"
          aria-labelledby="settings-tab"
        >
          <div class="section-heading">
            <div>
              <p class="eyebrow">ACCOUNT SETTINGS</p>
              <h2>Manage your Parkly identity.</h2>
            </div>
          </div>
          <div class="settings-list">
            <div class="settings-row">
              <div>
                <strong>Profile photo</strong>
                <small>Preview a custom profile picture in your browser session.</small>
              </div>
              <AppButton variant="secondary" size="sm" @click="chooseAvatar">Upload photo</AppButton>
            </div>
            <div class="settings-row">
              <div>
                <strong>Account email</strong>
                <small>{{ user.email }}</small>
              </div>
              <span class="settings-tag">{{ user.email_verified_at ? 'Verified' : 'Unverified' }}</span>
            </div>
            <div class="settings-row">
              <div>
                <strong>Sign out of Parkly</strong>
                <small>End your current authenticated session on this device.</small>
              </div>
              <AppButton variant="danger" size="sm" :loading="isLoggingOut" @click="handleLogout">
                Logout
              </AppButton>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<style scoped>
.profile-page {
  min-height: calc(100vh - 92px);
  padding: 10px 0 76px;
}

.profile-container {
  width: min(1160px, calc(100% - 32px));
  margin: 0 auto;
}

/* ============================================================
   UNIFIED FACEBOOK-STYLE PROFILE HEADER CARD (ZERO OVERLAP)
   ============================================================ */
.profile-header-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  overflow: hidden;
  box-shadow: var(--shadow-card-elevated);
}

.profile-cover {
  position: relative;
  width: 100%;
  height: 268px;
  overflow: hidden;
  border-bottom: 1px solid var(--color-border);
}

/* Identity Panel cleanly below Cover */
.identity-panel {
  padding: 20px 32px 0;
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
  gap: 24px;
  min-width: 0;
  flex: 1;
}

.avatar-wrap {
  position: relative;
  z-index: 3;
  flex: 0 0 auto;
  margin-top: -82px;
}

.avatar-large {
  width: 132px;
  height: 132px;
  border: 5px solid var(--color-surface);
  border-radius: 50%;
  position: relative;
  overflow: visible;
  cursor: pointer;
  background: linear-gradient(135deg, var(--color-primary), #1e293b);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 38px;
  font-weight: 800;
  padding: 0;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.18);
  transition: transform var(--transition-fast);
}

.avatar-large:hover {
  transform: translateY(-2px);
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-fallback {
  line-height: 1;
}

.avatar-camera {
  position: absolute;
  right: 2px;
  bottom: 2px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: var(--color-primary);
  color: #ffffff;
  border: 3px solid var(--color-surface);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.avatar-input {
  display: none;
}

.identity-copy {
  flex: 1;
  min-width: 0;
}

.eyebrow,
.card-kicker {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.14em;
}

.eyebrow {
  color: var(--color-primary);
  margin: 0 0 6px;
}

.identity-copy h1 {
  margin: 0;
  font-size: clamp(1.5rem, 2.5vw, 2.15rem);
  letter-spacing: -0.03em;
  line-height: 1.15;
  color: var(--color-text);
  word-break: break-word;
}

.identity-email {
  margin: 4px 0 10px;
  color: var(--color-text-muted);
  font-size: 14px;
  word-break: break-all;
}

.identity-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 10px;
  color: var(--color-text-muted);
  font-size: 12px;
  align-items: center;
}

.meta-dot {
  opacity: 0.45;
}

.role-pill,
.status-pill {
  border-radius: 999px;
  padding: 4px 11px;
  font-weight: 700;
  font-size: 11px;
}

.role-pill {
  color: var(--color-primary);
  background: var(--color-primary-light);
}

.identity-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
  flex-wrap: wrap;
  padding-top: 4px;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: var(--color-text-muted);
  border: 1px solid var(--color-border);
  white-space: nowrap;
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
  background: rgba(16, 185, 129, 0.09);
}

.status-pill.active i {
  background: #10b981;
}

.logout-pill-btn {
  color: var(--color-danger);
  border: 1px solid var(--color-border);
  border-radius: 999px;
  gap: 6px;
}

.logout-pill-btn:hover {
  background-color: var(--color-danger-bg);
  border-color: var(--color-danger-border);
  color: var(--color-danger);
}

/* Integrated Tabs Strip */
.profile-tabs {
  display: flex;
  gap: 28px;
  border-top: 1px solid var(--color-border);
  padding-top: 14px;
}

.profile-tab {
  padding: 0 0 14px;
  border: 0;
  border-bottom: 2.5px solid transparent;
  background: none;
  color: var(--color-text-muted);
  font: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: color var(--transition-fast), border-color var(--transition-fast);
}

.profile-tab:hover,
.profile-tab.active {
  color: var(--color-primary);
}

.profile-tab.active {
  border-bottom-color: var(--color-primary);
}

/* ============================================================
   TAB CONTENT PANELS
   ============================================================ */
.profile-main {
  margin-top: 28px;
}

.section-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 20px;
  margin-bottom: 18px;
}

.section-heading.compact {
  margin-bottom: 16px;
}

.section-heading h2 {
  margin: 0;
  font-size: clamp(1.25rem, 2vw, 1.55rem);
  letter-spacing: -0.025em;
}

.text-action,
.card-link {
  color: var(--color-primary);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
}

.overview-grid {
  display: grid;
  grid-template-columns: 1.25fr 1fr 1fr;
  gap: 16px;
}

.dashboard-card,
.details-card {
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
  padding: 22px;
}

.dashboard-card {
  min-height: 205px;
  display: flex;
  flex-direction: column;
}

.featured-card {
  background: linear-gradient(145deg, var(--color-primary-light), var(--color-surface));
}

.card-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  margin-bottom: 18px;
}

.card-icon.teal {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.card-icon.navy {
  background: rgba(30, 41, 59, 0.1);
  color: var(--color-text-muted);
}

.card-icon.violet {
  background: rgba(99, 102, 241, 0.12);
  color: #6366f1;
}

.card-kicker {
  color: var(--color-text-muted);
  margin: 0 0 8px;
}

.dashboard-card h3 {
  margin: 0 0 8px;
  font-size: 18px;
}

.dashboard-card p:not(.card-kicker) {
  color: var(--color-text-muted);
  font-size: 13px;
  line-height: 1.55;
  margin: 0 0 18px;
}

.card-link {
  margin-top: auto;
  display: inline-flex;
  gap: 6px;
}

.empty-metric {
  display: block;
  font-size: 34px;
  color: var(--color-text-subtle);
  margin: 0 0 8px;
  line-height: 1;
}

.details-card {
  margin-top: 16px;
}

.snapshot-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}

.snapshot-grid div {
  padding: 15px;
  border-radius: 12px;
  background: var(--color-surface-subtle);
}

.snapshot-grid span,
.snapshot-grid strong {
  display: block;
}

.snapshot-grid span {
  color: var(--color-text-muted);
  font-size: 11px;
  margin-bottom: 6px;
}

.snapshot-grid strong {
  font-size: 13px;
}

/* Empty Panels (Activity & Analytics) */
.empty-panel {
  min-height: 320px;
  background: var(--color-surface);
  border: 1px dashed var(--color-border);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  text-align: center;
  padding: 36px 24px;
}

.empty-panel h2 {
  margin: 2px 0 10px;
}

.empty-panel > p:not(.eyebrow) {
  max-width: 440px;
  color: var(--color-text-muted);
  font-size: 14px;
  line-height: 1.6;
  margin: 0 0 22px;
}

.empty-icon {
  width: 54px;
  height: 54px;
  display: grid;
  place-items: center;
  border-radius: 16px;
  background: var(--color-surface-subtle);
  color: var(--color-primary);
  margin-bottom: 18px;
}

/* Settings List */
.settings-list {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.settings-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 20px 24px;
  border-bottom: 1px solid var(--color-border);
}

.settings-row:last-child {
  border-bottom: 0;
}

.settings-row strong,
.settings-row small {
  display: block;
}

.settings-row strong {
  font-size: 14px;
  margin-bottom: 4px;
}

.settings-row small {
  color: var(--color-text-muted);
  font-size: 12px;
}

.settings-tag {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-muted);
  background: var(--color-surface-subtle);
  padding: 5px 12px;
  border-radius: 999px;
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
@media (max-width: 860px) {
  .profile-cover {
    height: 215px;
  }

  .identity-panel {
    padding: 18px 22px 0;
  }

  .identity-main {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .avatar-wrap {
    margin-top: -66px;
  }

  .avatar-large {
    width: 112px;
    height: 112px;
    font-size: 32px;
  }

  .profile-tabs {
    gap: 20px;
    overflow-x: auto;
  }

  .profile-tab {
    flex-shrink: 0;
  }

  .overview-grid {
    grid-template-columns: 1fr;
  }

  .snapshot-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 540px) {
  .profile-container {
    width: calc(100% - 20px);
  }

  .profile-cover {
    height: 175px;
  }

  .identity-panel {
    padding: 16px 18px 0;
  }

  .identity-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 14px;
  }

  .avatar-wrap {
    margin-top: -54px;
  }

  .avatar-large {
    width: 96px;
    height: 96px;
    font-size: 28px;
  }

  .avatar-camera {
    width: 30px;
    height: 30px;
  }

  .meta-dot {
    display: none;
  }

  .identity-meta {
    gap: 6px 12px;
  }

  .snapshot-grid {
    grid-template-columns: 1fr;
  }

  .settings-row {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
