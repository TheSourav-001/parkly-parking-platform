import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../components/HomePage.vue'
import DesignSystemShowcase from '../components/DesignSystemShowcase.vue'
import LoginView from '../views/auth/LoginView.vue'
import RegisterView from '../views/auth/RegisterView.vue'
import ForgotPasswordView from '../views/auth/ForgotPasswordView.vue'
import ResetPasswordView from '../views/auth/ResetPasswordView.vue'
import ProfileView from '../views/ProfileView.vue'
import TermsView from '../views/legal/TermsView.vue'
import PrivacyView from '../views/legal/PrivacyView.vue'
import CancellationPolicyView from '../views/legal/CancellationPolicyView.vue'
import LocationDataUseView from '../views/legal/LocationDataUseView.vue'
import ContactView from '../views/ContactView.vue'
import OperatorEnquiryView from '../views/OperatorEnquiryView.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
    meta: { title: 'Parkly — Discover & Book Smart Parking', isAuth: false },
  },
  {
    path: '/find-parking',
    name: 'find-parking',
    component: () => import('../views/FindParkingView.vue'),
    meta: { title: 'Find Parking — Parkly', isAuth: false },
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfileView,
    meta: { title: 'My Profile — Parkly', isAuth: false, requiresAuth: true },
  },
  {
    path: '/design-system',
    name: 'design-system',
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
    path: '/terms',
    name: 'terms',
    component: TermsView,
    meta: { title: 'Terms of Service — Parkly', isAuth: false },
  },
  {
    path: '/privacy',
    name: 'privacy',
    component: PrivacyView,
    meta: { title: 'Privacy Policy — Parkly', isAuth: false },
  },
  {
    path: '/cancellation-policy',
    name: 'cancellation-policy',
    component: CancellationPolicyView,
    meta: { title: 'Cancellation & Refund Policy — Parkly', isAuth: false },
  },
  {
    path: '/location-and-data-use',
    name: 'location-and-data-use',
    component: LocationDataUseView,
    meta: { title: 'Location & Data Use — Parkly', isAuth: false },
  },
  {
    path: '/contact',
    name: 'contact',
    component: ContactView,
    meta: { title: 'Contact Support — Parkly', isAuth: false },
  },
  {
    path: '/operators/enquiry',
    name: 'operators-enquiry',
    component: OperatorEnquiryView,
    meta: { title: 'List Your Parking — Parkly', isAuth: false },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      // Map cross-route and footer aliases to actual section element IDs
      const aliasMap = {
        '#find-parking': '#discovery',
        '#journey': '#how-it-works',
        '#pricing': '#featured',
        '#operators': '#discovery',
        '#faq': '#how-it-works',
      }
      const targetHash = aliasMap[to.hash] || to.hash

      return new Promise((resolve) => {
        // Wait slightly for DOM to settle if navigating between distinct routes
        setTimeout(() => {
          const el = document.querySelector(targetHash)
          if (el) {
            resolve({ el: targetHash, behavior: 'smooth', top: 110 })
          } else {
            resolve({ top: 0, behavior: 'smooth' })
          }
        }, 120)
      })
    }
    return { top: 0 }
  },
})

// Route guard to handle guestOnly & requiresAuth routes and post-Google callback
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

  // Prevent unauthenticated users from visiting requiresAuth pages
  if (to.meta?.requiresAuth && !authStore.isAuthenticated) {
    return next({ path: '/login', query: { redirect: to.fullPath } })
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
