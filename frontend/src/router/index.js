import { createRouter, createWebHistory } from 'vue-router'
import DesignSystemShowcase from '../components/DesignSystemShowcase.vue'
import LoginView from '../views/auth/LoginView.vue'
import RegisterView from '../views/auth/RegisterView.vue'
import ForgotPasswordView from '../views/auth/ForgotPasswordView.vue'
import ResetPasswordView from '../views/auth/ResetPasswordView.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: DesignSystemShowcase,
    meta: { title: 'Parkly — Design System', isAuth: false },
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { title: 'Sign In — Parkly', isAuth: true, guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { title: 'Create Account — Parkly', isAuth: true, guestOnly: true },
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPasswordView,
    meta: { title: 'Forgot Password — Parkly', isAuth: true, guestOnly: true },
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPasswordView,
    meta: { title: 'Reset Password — Parkly', isAuth: true, guestOnly: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Route guard to handle guestOnly routes and post-Google callback
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Handle Google OAuth callback parameter on arrival at any route
  if (to.query.google === 'success') {
    await authStore.handleGoogleCallback()
    // Clean up temporary google query parameter from URL
    const query = { ...to.query }
    delete query.google
    return next({ path: to.path, query, replace: true })
  }

  // Prevent authenticated users from visiting guestOnly auth pages
  if (to.meta?.guestOnly && authStore.isAuthenticated) {
    return next({ path: '/' })
  }

  next()
})

// Update browser document title on route transition
router.afterEach((to) => {
  if (to.meta && to.meta.title) {
    document.title = to.meta.title
  }
})

export default router
