import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'
import { useTheme } from './composables/useTheme'
import { useAuthStore } from './stores/auth'

// Initialize the saved or system theme prior to mounting
const { initTheme } = useTheme()
initTheme()

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize session auth verification on app startup
const authStore = useAuthStore()
authStore.initializeAuth().finally(() => {
  app.mount('#app')
})
