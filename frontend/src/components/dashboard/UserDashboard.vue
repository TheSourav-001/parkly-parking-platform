
<script setup>
import { ref } from 'vue'

const activeMenu = ref('Overview')

const menuItems = [
  { name: 'Overview', icon: '◫' },
  { name: 'My Bookings', icon: '▤' },
  { name: 'Saved Parking', icon: '♡' },
  { name: 'My Vehicles', icon: '▱' },
  { name: 'Profile Settings', icon: '⚙' },
]

const stats = [
  { label: 'Total Bookings', value: '0', icon: '▤', note: 'Your booking history' },
  { label: 'Upcoming Bookings', value: '0', icon: '◷', note: 'Your next parking visits' },
  { label: 'Saved Locations', value: '0', icon: '♡', note: 'Places you have saved' },
]
</script>

<template>
  <div class="dashboard">
    <aside class="sidebar">
      <a href="/" class="brand">
        <span class="brand-icon">P</span>
        <span>parkly<span class="brand-dot">.</span></span>
      </a>

      <div class="sidebar-label">MENU</div>

      <nav class="sidebar-nav">
        <button
          v-for="item in menuItems"
          :key="item.name"
          class="nav-item"
          :class="{ active: activeMenu === item.name }"
          @click="activeMenu = item.name"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span>{{ item.name }}</span>
        </button>
      </nav>

      <div class="sidebar-bottom">
        <div class="help-card">
          <div class="help-icon">?</div>
          <h4>Need some help?</h4>
          <p>We're here to help you with your parking experience.</p>
          <a href="mailto:support@parkly.com">Contact Support →</a>
        </div>

        <div class="sidebar-footer">
          <span class="status-dot"></span>
          Parkly is ready for you
        </div>
      </div>
    </aside>

    <main class="main-content">
      <header class="topbar">
        <div>
          <p class="breadcrumb">Dashboard / {{ activeMenu }}</p>
          <h1>{{ activeMenu }}</h1>
        </div>

        <div class="topbar-right">
          <button class="notification-button" aria-label="Notifications">
            ♧
            <span class="notification-dot"></span>
          </button>

          <div class="profile">
            <div class="avatar">U</div>
            <div class="profile-info">
              <strong>Parkly User</strong>
              <span>Customer</span>
            </div>
            <span class="profile-arrow">⌄</span>
          </div>
        </div>
      </header>

      <section class="welcome-section">
        <div>
          <p class="welcome-label">YOUR PARKLY SPACE</p>
          <h2>Welcome back! <span>👋</span></h2>
          <p class="welcome-description">
            Manage your parking activities and find your next parking spot.
          </p>
        </div>

        <div class="welcome-decoration" aria-hidden="true">
          <div class="decoration-circle circle-one"></div>
          <div class="decoration-circle circle-two"></div>
          <div class="decoration-car">P</div>
        </div>
      </section>

      <section class="stats-grid">
        <article v-for="stat in stats" :key="stat.label" class="stat-card">
          <div class="stat-top">
            <span class="stat-icon">{{ stat.icon }}</span>
            <span class="stat-indicator">Overview</span>
          </div>
          <h3>{{ stat.value }}</h3>
          <p class="stat-label">{{ stat.label }}</p>
          <p class="stat-note">{{ stat.note }}</p>
        </article>
      </section>

      <section class="content-grid">
        <article class="panel bookings-panel">
          <div class="panel-heading">
            <div>
              <h3>Recent Bookings</h3>
              <p>Your latest parking activity</p>
            </div>
            <button class="text-button" @click="activeMenu = 'My Bookings'">
              View all →
            </button>
          </div>

          <div class="empty-state">
            <div class="empty-illustration">
              <span>▤</span>
              <div class="empty-circle"></div>
            </div>
            <h4>No bookings yet</h4>
            <p>
              Your parking bookings will appear here once you make your first reservation.
            </p>
            <button class="primary-button" @click="activeMenu = 'Overview'">
              Explore Parkly
            </button>
          </div>
        </article>

        <article class="panel quick-panel">
          <div class="panel-heading">
            <div>
              <h3>Quick Actions</h3>
              <p>Manage your account easily</p>
            </div>
          </div>

          <button class="quick-action" @click="activeMenu = 'My Bookings'">
            <span class="quick-icon">▤</span>
            <span class="quick-text">
              <strong>My Bookings</strong>
              <small>Check your reservations</small>
            </span>
            <span class="quick-arrow">→</span>
          </button>

          <button class="quick-action" @click="activeMenu = 'Saved Parking'">
            <span class="quick-icon">♡</span>
            <span class="quick-text">
              <strong>Saved Parking</strong>
              <small>View your favourite places</small>
            </span>
            <span class="quick-arrow">→</span>
          </button>

          <button class="quick-action" @click="activeMenu = 'My Vehicles'">
            <span class="quick-icon">▱</span>
            <span class="quick-text">
              <strong>My Vehicles</strong>
              <small>Manage your vehicles</small>
            </span>
            <span class="quick-arrow">→</span>
          </button>

          <button class="quick-action" @click="activeMenu = 'Profile Settings'">
            <span class="quick-icon">⚙</span>
            <span class="quick-text">
              <strong>Profile Settings</strong>
              <small>Update your information</small>
            </span>
            <span class="quick-arrow">→</span>
          </button>
        </article>
      </section>

      <footer class="dashboard-footer">
        <span>© 2026 Parkly. All rights reserved.</span>
        <span>Making parking simpler.</span>
      </footer>
    </main>
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  display: flex;
  background: #f6f8f9;
  color: #172b2a;
  font-family: 'DM Sans', sans-serif;
}

.sidebar {
  width: 255px;
  min-height: 100vh;
  background: #fff;
  border-right: 1px solid #e9eeee;
  padding: 30px 20px 22px;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #183c38;
  text-decoration: none;
  font-size: 25px;
  font-weight: 800;
  letter-spacing: -1px;
  padding: 0 8px;
  margin-bottom: 55px;
}

.brand-icon {
  width: 36px;
  height: 36px;
  display: grid;
  place-items: center;
  background: #087f70;
  color: white;
  border-radius: 11px;
  font-size: 21px;
}

.brand-dot {
  color: #0caa91;
}

.sidebar-label {
  padding: 0 12px;
  margin-bottom: 13px;
  color: #9ba9a7;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 1.4px;
}

.sidebar-nav {
  display: grid;
  gap: 7px;
}

.nav-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 13px;
  padding: 13px 14px;
  border: 0;
  border-radius: 9px;
  background: transparent;
  color: #71817e;
  text-align: left;
  font: inherit;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s ease;
}

.nav-item:hover,
.nav-item.active {
  background: #eaf6f3;
  color: #087f70;
}

.nav-icon {
  width: 19px;
  text-align: center;
  font-size: 18px;
}

.sidebar-bottom {
  margin-top: auto;
}

.help-card {
  background: #f2f8f6;
  border-radius: 12px;
  padding: 17px;
  margin-bottom: 24px;
}

.help-icon {
  width: 25px;
  height: 25px;
  display: grid;
  place-items: center;
  border-radius: 50%;
  background: #d6eee7;
  color: #087f70;
  font-weight: 800;
}

.help-card h4 {
  margin: 13px 0 6px;
  font-size: 13px;
}

.help-card p {
  color: #80908c;
  font-size: 11px;
  line-height: 1.6;
  margin: 0 0 12px;
}

.help-card a {
  color: #087f70;
  text-decoration: none;
  font-size: 11px;
  font-weight: 800;
}

.sidebar-footer {
  color: #9ba9a7;
  font-size: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 5px;
}

.status-dot,
.notification-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #16b88a;
}

.main-content {
  min-width: 0;
  flex: 1;
  padding: 30px 42px 20px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 28px;
}

.breadcrumb {
  margin: 0 0 5px;
  color: #99a6a3;
  font-size: 11px;
}

.topbar h1 {
  margin: 0;
  font-size: 23px;
  letter-spacing: -0.6px;
}

.topbar-right,
.profile {
  display: flex;
  align-items: center;
}

.topbar-right {
  gap: 24px;
}

.notification-button {
  position: relative;
  width: 39px;
  height: 39px;
  border: 1px solid #e7edeb;
  border-radius: 10px;
  background: white;
  color: #62736f;
  font-size: 20px;
}

.notification-dot {
  position: absolute;
  top: 8px;
  right: 8px;
  border: 1px solid white;
}

.profile {
  gap: 10px;
}

.avatar {
  width: 39px;
  height: 39px;
  display: grid;
  place-items: center;
  border-radius: 50%;
  background: #dcefe9;
  color: #087f70;
  font-weight: 800;
}

.profile-info {
  display: grid;
  gap: 3px;
}

.profile-info strong {
  font-size: 12px;
}

.profile-info span {
  color: #9ba9a7;
  font-size: 10px;
}

.profile-arrow {
  color: #85938f;
  margin-left: 4px;
}

.welcome-section {
  position: relative;
  min-height: 185px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
  padding: 30px 38px;
  border-radius: 16px;
  background: linear-gradient(110deg, #0c7669, #075b54);
  color: white;
}

.welcome-label {
  color: #a9e2d5;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 1.5px;
}

.welcome-section h2 {
  margin: 12px 0 8px;
  font-size: 28px;
  letter-spacing: -0.8px;
}

.welcome-description {
  max-width: 440px;
  margin: 0;
  color: #d0e9e2;
  font-size: 12px;
  line-height: 1.7;
}

.welcome-decoration {
  position: relative;
  width: 200px;
  height: 150px;
  flex-shrink: 0;
}

.decoration-circle {
  position: absolute;
  border: 1px solid #ffffff25;
  border-radius: 50%;
}

.circle-one {
  width: 150px;
  height: 150px;
  right: 10px;
  top: 0;
}

.circle-two {
  width: 105px;
  height: 105px;
  right: 33px;
  top: 22px;
}

.decoration-car {
  position: absolute;
  width: 70px;
  height: 70px;
  right: 50px;
  top: 40px;
  display: grid;
  place-items: center;
  border-radius: 20px;
  background: #ffffff20;
  color: white;
  font-size: 33px;
  font-weight: 800;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 17px;
  margin: 24px 0;
}

.stat-card,
.panel {
  background: white;
  border: 1px solid #edf0ef;
  border-radius: 13px;
}

.stat-card {
  padding: 19px 21px;
}

.stat-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-icon {
  width: 37px;
  height: 37px;
  display: grid;
  place-items: center;
  border-radius: 10px;
  background: #eaf6f3;
  color: #087f70;
  font-size: 20px;
}

.stat-indicator {
  color: #a1adaa;
  font-size: 10px;
}

.stat-card h3 {
  margin: 17px 0 2px;
  font-size: 27px;
}

.stat-label {
  margin: 0;
  font-size: 12px;
  font-weight: 700;
}

.stat-note {
  margin: 7px 0 0;
  color: #9ba9a7;
  font-size: 10px;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.65fr) minmax(260px, 1fr);
  gap: 18px;
}

.panel {
  padding: 21px;
}

.panel-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 10px;
}

.panel-heading h3 {
  margin: 0;
  font-size: 14px;
}

.panel-heading p {
  margin: 6px 0 0;
  color: #9ba9a7;
  font-size: 10px;
}

.text-button {
  border: 0;
  background: transparent;
  color: #087f70;
  font: inherit;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.empty-state {
  min-height: 250px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 20px 10px;
}

.empty-illustration {
  position: relative;
  width: 65px;
  height: 65px;
  display: grid;
  place-items: center;
  margin-bottom: 13px;
  border-radius: 18px;
  background: #edf7f4;
  color: #087f70;
  font-size: 30px;
}

.empty-circle {
  position: absolute;
  width: 10px;
  height: 10px;
  top: 7px;
  right: 7px;
  border-radius: 50%;
  background: #a8ddd0;
}

.empty-state h4 {
  margin: 0 0 7px;
  font-size: 13px;
}

.empty-state p {
  max-width: 300px;
  margin: 0 0 16px;
  color: #98a5a2;
  font-size: 10px;
  line-height: 1.7;
}

.primary-button {
  border: 0;
  border-radius: 7px;
  background: #087f70;
  color: white;
  padding: 10px 17px;
  font: inherit;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.primary-button:hover {
  background: #06695e;
}

.quick-panel {
  display: flex;
  flex-direction: column;
}

.quick-panel .panel-heading {
  margin-bottom: 15px;
}

.quick-action {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 13px 0;
  border: 0;
  border-bottom: 1px solid #f0f2f1;
  background: transparent;
  text-align: left;
  cursor: pointer;
}

.quick-action:last-child {
  border-bottom: 0;
}

.quick-icon {
  width: 35px;
  height: 35px;
  display: grid;
  place-items: center;
  border-radius: 9px;
  background: #f0f7f5;
  color: #087f70;
  font-size: 18px;
}

.quick-text {
  flex: 1;
  display: grid;
  gap: 4px;
}

.quick-text strong {
  color: #263b37;
  font-size: 11px;
}

.quick-text small {
  color: #9ba9a7;
  font-size: 9px;
}

.quick-arrow {
  color: #94a39f;
}

.dashboard-footer {
  display: flex;
  justify-content: space-between;
  gap: 10px;
  padding: 25px 2px 5px;
  color: #a0adaa;
  font-size: 10px;
}

@media (max-width: 1050px) {
  .main-content {
    padding: 25px;
  }

  .content-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 750px) {
  .dashboard {
    display: block;
  }

  .sidebar {
    width: 100%;
    min-height: auto;
    padding: 15px 18px;
    border-right: 0;
    border-bottom: 1px solid #e9eeee;
  }

  .brand {
    margin-bottom: 18px;
  }

  .sidebar-label,
  .sidebar-bottom {
    display: none;
  }

  .sidebar-nav {
    display: flex;
    overflow-x: auto;
    gap: 5px;
  }

  .nav-item {
    width: auto;
    flex-shrink: 0;
    padding: 10px;
    font-size: 11px;
  }

  .main-content {
    padding: 22px 16px;
  }

  .welcome-section {
    min-height: 160px;
    padding: 23px;
  }

  .welcome-section h2 {
    font-size: 23px;
  }

  .welcome-decoration {
    width: 90px;
    opacity: 0.6;
  }

  .circle-one {
    right: -45px;
  }

  .circle-two {
    right: -22px;
  }

  .decoration-car {
    right: -9px;
  }

  .stats-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .stat-card {
    padding: 15px;
  }

  .stat-card h3 {
    margin-top: 10px;
  }

  .topbar-right {
    gap: 12px;
  }

  .profile-info,
  .profile-arrow {
    display: none;
  }
}

@media (max-width: 420px) {
  .notification-button {
    display: none;
  }

  .welcome-decoration {
    display: none;
  }

  .dashboard-footer {
    flex-direction: column;
  }
}
</style>
