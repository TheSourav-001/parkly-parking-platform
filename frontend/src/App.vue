
<script setup>
import { ref, onMounted } from 'vue'
import {
  getDivisions,
  getDistricts,
  getUpazilas,
  getVehicleTypes,
} from './services/api.js'

const divisions = ref([])
const districts = ref([])
const upazilas = ref([])
const vehicleTypes = ref([])

const selectedDivision = ref('')
const selectedDistrict = ref('')
const selectedUpazila = ref('')
const selectedVehicle = ref('')

const loading = ref(false)
const errorMessage = ref('')
const searchMessage = ref('')

async function loadInitialData() {
  loading.value = true
  errorMessage.value = ''

  try {
    const [divisionData, vehicleData] = await Promise.all([
      getDivisions(),
      getVehicleTypes(),
    ])

    divisions.value = divisionData
    vehicleTypes.value = vehicleData
  } catch (error) {
    errorMessage.value =
      'Unable to load search options. Please check that the Parkly backend is running.'
    console.error(error)
  } finally {
    loading.value = false
  }
}

async function handleDivisionChange() {
  selectedDistrict.value = ''
  selectedUpazila.value = ''
  districts.value = []
  upazilas.value = []
  searchMessage.value = ''

  if (!selectedDivision.value) return

  try {
    districts.value = await getDistricts(selectedDivision.value)
  } catch (error) {
    errorMessage.value = error.message
  }
}

async function handleDistrictChange() {
  selectedUpazila.value = ''
  upazilas.value = []
  searchMessage.value = ''

  if (!selectedDistrict.value) return

  try {
    upazilas.value = await getUpazilas(selectedDistrict.value)
  } catch (error) {
    errorMessage.value = error.message
  }
}

function handleSearch() {
  searchMessage.value = ''

  if (
    !selectedDivision.value ||
    !selectedDistrict.value ||
    !selectedUpazila.value ||
    !selectedVehicle.value
  ) {
    searchMessage.value = 'Please select all four search options.'
    return
  }

  const division = divisions.value.find(
    item => String(item.id) === String(selectedDivision.value)
  )

  const district = districts.value.find(
    item => String(item.id) === String(selectedDistrict.value)
  )

  const upazila = upazilas.value.find(
    item => String(item.id) === String(selectedUpazila.value)
  )

  const vehicle = vehicleTypes.value.find(
    item => String(item.id) === String(selectedVehicle.value)
  )

  searchMessage.value =
    `Your search is ready: ${vehicle.name} parking in ${upazila.name}, ${district.name}, ${division.name}.`
}

onMounted(loadInitialData)
</script>

<template>
  <div class="app-wrapper">

    <header class="navbar">
      <a href="#" class="brand">
        <span class="brand-icon">P</span>
        <span>Parkly</span>
      </a>

      <nav class="nav-links">
        <a href="#home">Home</a>
        <a href="#search">Find Parking</a>
        <a href="#about">About</a>
      </nav>

      <div class="nav-actions">
        <button class="login-button" type="button">Log In</button>
        <button class="signup-button" type="button">Get Started</button>
      </div>
    </header>

    <main>
      <section id="home" class="hero">
        <div class="hero-content">
          <span class="eyebrow">SMART PARKING, MADE SIMPLE</span>

          <h1>
            Find your perfect
            <span>parking spot.</span>
          </h1>

          <p>
            Discover convenient parking locations across Bangladesh.
            Save time, avoid unnecessary searching, and park with confidence.
          </p>

          <a href="#search" class="hero-button">
            Find Parking <span>→</span>
          </a>

          <div class="hero-stats">
            <div>
              <strong>Easy</strong>
              <span>Location search</span>
            </div>
            <div>
              <strong>Smart</strong>
              <span>Vehicle selection</span>
            </div>
            <div>
              <strong>Simple</strong>
              <span>Parking discovery</span>
            </div>
          </div>
        </div>

        <div class="hero-visual">
          <img
            src="./assets/hero.png"
            alt="Parkly parking illustration"
          />
          <div class="floating-card">
            <span class="floating-icon">✓</span>
            <div>
              <strong>Parking made easier</strong>
              <small>Search by your location</small>
            </div>
          </div>
        </div>
      </section>

      <section id="search" class="search-section">
        <div class="section-heading">
          <span class="eyebrow">START YOUR JOURNEY</span>
          <h2>Find parking near you</h2>
          <p>Select your location and vehicle to get started.</p>
        </div>

        <div class="search-card">
          <div class="search-card-heading">
            <div>
              <span class="search-icon">⌖</span>
              <div>
                <h3>Parking preferences</h3>
                <p>Choose where and what you want to park.</p>
              </div>
            </div>

            <span v-if="loading" class="loading-label">
              Loading options...
            </span>
          </div>

          <div class="form-grid">

            <div class="form-group">
              <label for="division">Division</label>
              <select
                id="division"
                v-model="selectedDivision"
                @change="handleDivisionChange"
                :disabled="loading || divisions.length === 0"
              >
                <option value="">Select division</option>
                <option
                  v-for="division in divisions"
                  :key="division.id"
                  :value="division.id"
                >
                  {{ division.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="district">District</label>
              <select
                id="district"
                v-model="selectedDistrict"
                @change="handleDistrictChange"
                :disabled="!selectedDivision"
              >
                <option value="">Select district</option>
                <option
                  v-for="district in districts"
                  :key="district.id"
                  :value="district.id"
                >
                  {{ district.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="upazila">Upazila</label>
              <select
                id="upazila"
                v-model="selectedUpazila"
                :disabled="!selectedDistrict"
              >
                <option value="">Select Upazila</option>
                <option
                  v-for="upazila in upazilas"
                  :key="upazila.id"
                  :value="upazila.id"
                >
                  {{ upazila.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="vehicle">Vehicle type</label>
              <select
                id="vehicle"
                v-model="selectedVehicle"
                :disabled="loading || vehicleTypes.length === 0"
              >
                <option value="">Select vehicle</option>
                <option
                  v-for="vehicle in vehicleTypes"
                  :key="vehicle.id"
                  :value="vehicle.id"
                >
                  {{ vehicle.name }}
                </option>
              </select>
            </div>

          </div>

          <button
            class="search-button"
            type="button"
            @click="handleSearch"
          >
            Search Parking <span>→</span>
          </button>

          <p v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </p>

          <p v-if="searchMessage" class="search-message">
            {{ searchMessage }}
          </p>
        </div>
      </section>

      <section id="about" class="features-section">
        <div class="section-heading">
          <span class="eyebrow">WHY PARKLY</span>
          <h2>A smarter way to park</h2>
          <p>Designed to make finding parking more convenient.</p>
        </div>

        <div class="feature-grid">
          <article class="feature-card">
            <span class="feature-icon">⌖</span>
            <h3>Location-based search</h3>
            <p>Choose your preferred location through a simple selection system.</p>
          </article>

          <article class="feature-card">
            <span class="feature-icon">🚗</span>
            <h3>Vehicle preferences</h3>
            <p>Select your vehicle type to make your parking search more relevant.</p>
          </article>

          <article class="feature-card">
            <span class="feature-icon">◷</span>
            <h3>Save your time</h3>
            <p>Move toward a more convenient parking discovery experience.</p>
          </article>
        </div>
      </section>
    </main>

    <footer class="footer">
      <a href="#home" class="brand">
        <span class="brand-icon">P</span>
        <span>Parkly</span>
      </a>
      <p>Making parking easier, one location at a time.</p>
      <small>© 2026 Parkly. All rights reserved.</small>
    </footer>

  </div>
</template>
