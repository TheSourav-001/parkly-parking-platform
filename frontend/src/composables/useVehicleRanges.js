import { ref } from 'vue'

// Baseline verified vehicle classes so the 4 vehicle cards are always displayed
const defaultVehicleClasses = [
  {
    id: 1,
    slug: 'car',
    name: 'Car / Sedan',
    displayName: 'Car / Sedan',
    description: 'Standard cars, sedans and compact hatchbacks.',
    min_hourly_rate: 40,
    max_hourly_rate: 60,
    rate_display: '40 to 60 BDT per hour',
    facility_count: 12,
    is_published: true,
    photo1920: '/images/vehicles/car.webp',
    photo960: '/images/vehicles/car.webp',
    filterKey: 'car',
    indexStr: '01',
  },
  {
    id: 2,
    slug: 'suv',
    name: 'SUV / 4x4',
    displayName: 'SUV / 4x4',
    description: 'SUVs, crossovers, 4x4s and full-sized passenger vehicles.',
    min_hourly_rate: 50,
    max_hourly_rate: 80,
    rate_display: '50 to 80 BDT per hour',
    facility_count: 9,
    is_published: true,
    photo1920: '/images/vehicles/suv.webp',
    photo960: '/images/vehicles/suv.webp',
    filterKey: 'suv',
    indexStr: '02',
  },
  {
    id: 3,
    slug: 'motorcycle',
    name: 'Motorcycle',
    displayName: 'Motorcycle',
    description: 'Standard motorbikes, scooters and two-wheelers.',
    min_hourly_rate: 15,
    max_hourly_rate: 30,
    rate_display: '15 to 30 BDT per hour',
    facility_count: 14,
    is_published: true,
    photo1920: '/images/vehicles/motorcycle.webp',
    photo960: '/images/vehicles/motorcycle.webp',
    filterKey: 'bike',
    indexStr: '03',
  },
  {
    id: 4,
    slug: 'others',
    name: 'Others / Light Commercial',
    displayName: 'Others / Light Commercial',
    description: 'Vans, microbuses and light commercial vehicles.',
    min_hourly_rate: 40,
    max_hourly_rate: 70,
    rate_display: '40 to 70 BDT per hour',
    facility_count: 6,
    is_published: true,
    photo1920: '/images/vehicles/others.webp',
    photo960: '/images/vehicles/others.webp',
    filterKey: 'others',
    indexStr: '04',
  },
]

const vehicleRanges = ref([...defaultVehicleClasses])
const isLoading = ref(false)
const error = ref(null)

// Clean light-studio vehicles mapping with white / light-silver vehicle cuts
const vehicleClassMeta = {
  car: {
    photo1920: '/images/vehicles/car.webp',
    photo960: '/images/vehicles/car.webp',
    label: 'Car / Sedan',
    defaultDesc: 'Standard cars, sedans and compact hatchbacks.',
    filterKey: 'car',
  },
  suv: {
    photo1920: '/images/vehicles/suv.webp',
    photo960: '/images/vehicles/suv.webp',
    label: 'SUV / 4x4',
    defaultDesc: 'SUVs, crossovers, 4x4s and full-sized passenger vehicles.',
    filterKey: 'suv',
  },
  motorcycle: {
    photo1920: '/images/vehicles/motorcycle.webp',
    photo960: '/images/vehicles/motorcycle.webp',
    label: 'Motorcycle',
    defaultDesc: 'Standard motorbikes, scooters and two-wheelers.',
    filterKey: 'bike',
  },
  others: {
    photo1920: '/images/vehicles/others.webp',
    photo960: '/images/vehicles/others.webp',
    label: 'Others / Light Commercial',
    defaultDesc: 'Vans, microbuses and light commercial vehicles.',
    filterKey: 'others',
  },
}

export function useVehicleRanges() {
  async function fetchRanges() {
    isLoading.value = true
    error.value = null

    try {
      const res = await fetch('/api/pricing/ranges', {
        headers: { Accept: 'application/json' },
      })

      if (!res.ok) {
        throw new Error(`Failed to load rates (HTTP ${res.status})`)
      }

      const json = await res.json()
      const rawData = json.data || []

      if (Array.isArray(rawData) && rawData.length > 0) {
        // Normalize with client photos and metadata
        vehicleRanges.value = rawData.map((item, idx) => {
          const slugKey = (item.slug || '').toLowerCase()
          const metaKey = vehicleClassMeta[slugKey] ? slugKey : (idx === 0 ? 'car' : idx === 1 ? 'suv' : idx === 2 ? 'motorcycle' : 'others')
          const meta = vehicleClassMeta[metaKey]

          return {
            ...item,
            displayName: item.name || meta.label,
            description: item.description || meta.defaultDesc,
            photo1920: meta.photo1920,
            photo960: meta.photo960,
            cutout: meta.cutout,
            silhouette: meta.silhouette,
            filterKey: meta.filterKey,
            indexStr: String(idx + 1).padStart(2, '0'),
          }
        })
      } else {
        // Keep baseline platform vehicle classes if server returns empty list
        vehicleRanges.value = [...defaultVehicleClasses]
      }
    } catch (err) {
      console.warn('API /api/pricing/ranges unavailable, maintaining baseline vehicle classes:', err)
      error.value = err.message || 'Could not load rates'
      if (!vehicleRanges.value || vehicleRanges.value.length === 0) {
        vehicleRanges.value = [...defaultVehicleClasses]
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    vehicleRanges,
    isLoading,
    error,
    fetchRanges,
  }
}
