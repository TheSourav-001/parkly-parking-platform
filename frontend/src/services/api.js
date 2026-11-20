
const API_BASE_URL = 'http://127.0.0.1:8000/api'

async function request(endpoint) {
  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    headers: {
      Accept: 'application/json',
    },
  })

  const result = await response.json()

  if (!response.ok || !result.success) {
    throw new Error(result.message || 'Something went wrong.')
  }

  return result.data
}

export const getDivisions = () => {
  return request('/locations?type=division')
}

export const getDistricts = (divisionId) => {
  return request(`/locations?type=district&parent_id=${divisionId}`)
}

export const getUpazilas = (districtId) => {
  return request(`/locations?type=thana&parent_id=${districtId}`)
}

export const getVehicleTypes = () => {
  return request('/vehicle-types')
}
