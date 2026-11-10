import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import { useTheme } from './composables/useTheme'

// Initialize the saved or system theme prior to mounting
const { initTheme } = useTheme()
initTheme()

createApp(App).use(router).mount('#app')
