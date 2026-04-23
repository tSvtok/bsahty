import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/',          name: 'landing',  component: () => import('@/pages/LandingPage.vue') },
  { path: '/login',     name: 'login',    component: () => import('@/pages/LoginPage.vue'),    meta: { guest: true } },
  { path: '/register',  name: 'register', component: () => import('@/pages/RegisterPage.vue'), meta: { guest: true } },
  { path: '/feed',      name: 'feed',     component: () => import('@/pages/FeedPage.vue'),     meta: { auth: true } },
  { path: '/map',       name: 'map',      component: () => import('@/pages/MapPage.vue'),      meta: { auth: true } },
  { path: '/events',    name: 'events',   component: () => import('@/pages/EventsPage.vue'),   meta: { auth: true } },
  { path: '/profile',   name: 'profile',  component: () => import('@/pages/ProfilePage.vue'),  meta: { auth: true } },
  { path: '/explore',   name: 'explore',  component: () => import('@/pages/ExplorePage.vue'),  meta: { auth: true } },
  { path: '/profile/:id', name: 'user-profile', component: () => import('@/pages/ProfilePage.vue'), meta: { auth: true } },
  { path: '/messages',  name: 'messages', component: () => import('@/pages/MessagesPage.vue'), meta: { auth: true } },
  { path: '/messages/:id', name: 'chat', component: () => import('@/pages/ChatPage.vue'),      meta: { auth: true } },
  { path: '/admin',        name: 'admin', component: () => import('@/pages/AdminPage.vue'),     meta: { auth: true, admin: true } },
  { path: '/:pathMatch(.*)*', redirect: '/' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  
  // Update Title
  const baseTitle = 'B-SSAHTY'
  const pageTitle = to.name ? to.name.charAt(0).toUpperCase() + to.name.slice(1) : ''
  document.title = pageTitle ? `${pageTitle} | ${baseTitle}` : baseTitle

  if (to.meta.auth  && !auth.isLoggedIn) return { name: 'login' }
  if (to.meta.guest && auth.isLoggedIn)  return { name: 'feed' }
  if (to.meta.admin && auth.user?.role !== 'ADMIN') return { name: 'feed' }
})

export default router
