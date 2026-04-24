<template>
  <nav :class="{ 'shadow-lg border-transparent': isScrolled }" class="fixed top-0 inset-x-0 z-50 h-16 bg-surface/90 backdrop-blur-xl border-b border-border flex items-center px-4 md:px-6 gap-4 transition-all duration-300">
    <!-- Logo -->
    <router-link to="/" class="flex items-center gap-2 font-bold text-xl tracking-tight mr-4 shrink-0">
      <span class="w-8 h-8 rounded-xl bg-gradient-primary flex items-center justify-center text-white text-sm font-black shadow-md">B</span>
      <span class="hidden sm:inline text-gray-900">B-SSAHTY</span>
    </router-link>

    <!-- Search Bar -->
    <div class="flex-1 max-w-md hidden md:block">
      <div class="relative">
        <button @click="handleSearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors z-10">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </button>
        <input
          v-model="search"
          type="text"
          placeholder="Search athletes, events, spots..."
          class="w-full pl-9 pr-4 py-2 bg-gray-100 rounded-full text-sm border-none outline-none focus:bg-white focus:ring-2 focus:ring-orange-400/30 transition-all"
          @keyup.enter="handleSearch"
        />
      </div>
    </div>

    <div class="flex-1" />

    <!-- Nav Links (desktop) -->
    <div v-if="auth.isLoggedIn" class="hidden md:flex items-center gap-1">
      <router-link v-for="link in navLinks" :key="link.to" :to="link.to" class="nav-link" :class="{ active: $route.path === link.to }">
        <component :is="link.icon" class="w-5 h-5" />
        <span class="text-sm font-medium">{{ link.label }}</span>
      </router-link>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-2 ml-2">
      <!-- Mobile menu button -->
      <button v-if="auth.isLoggedIn" @click="showMobileMenu = !showMobileMenu" class="md:hidden w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Notifications -->
      <div class="relative">
        <button v-if="auth.isLoggedIn" @click="showNotifications = !showNotifications" class="relative w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
          <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span v-if="notifications.filter(n => !n.read).length > 0" class="absolute top-1.5 right-1.5 w-2 h-2 bg-orange-500 rounded-full ring-2 ring-white"></span>
        </button>

        <!-- Notification Dropdown -->
        <transition name="mobile-menu">
          <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
              <h3 class="font-bold text-sm">Notifications</h3>
              <button @click="markAllRead" class="text-xs text-orange-500 font-medium hover:underline">Clear all</button>
            </div>
            <div class="max-h-96 overflow-y-auto">
              <div v-if="notifications.length === 0" class="p-8 text-center text-gray-400">
                <p class="text-xs">No new notifications</p>
              </div>
              <div v-for="(n, i) in notifications" :key="i" class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors flex gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-lg shrink-0">
                  {{ n.icon }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-800 leading-tight">
                    <span class="font-bold">{{ n.title }}</span> {{ n.body }}
                  </p>
                  <p class="text-[10px] text-gray-400 mt-1">{{ n.time }}</p>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Messages -->
      <router-link v-if="auth.isLoggedIn" to="/messages" class="relative w-9 h-9 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <span v-if="unreadMessagesCount > 0" class="absolute top-1 right-1 min-w-[1.25rem] h-5 px-1 bg-orange-600 text-white text-[10px] font-black rounded-full ring-2 ring-white flex items-center justify-center">
          {{ unreadMessagesCount > 9 ? '9+' : unreadMessagesCount }}
        </span>
      </router-link>

      <!-- Avatar / Auth buttons -->
      <template v-if="auth.isLoggedIn">
        <router-link to="/profile">
          <img :src="avatar" :alt="auth.user?.name" class="w-9 h-9 rounded-full object-cover ring-2 ring-orange-500/30 hover:ring-orange-500 transition-all cursor-pointer" />
        </router-link>
      </template>
      <template v-else>
        <router-link to="/login"  class="btn-secondary !py-1.5 !px-4 !text-sm">Login</router-link>
        <router-link to="/register" class="btn-primary !py-1.5 !px-4 !text-sm">Join Free</router-link>
      </template>
    </div>

    <!-- Mobile menu -->
    <transition name="mobile-menu">
      <div v-if="showMobileMenu && auth.isLoggedIn" class="md:hidden absolute top-16 left-0 right-0 bg-surface border-b border-border shadow-2xl z-40">
        <nav class="flex flex-col py-2">
          <router-link
            v-for="link in navLinks" :key="link.to"
            :to="link.to"
            @click="showMobileMenu = false"
            class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
            :class="{ 'bg-orange-50 text-orange-600': $route.path === link.to }"
          >
            <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center" :class="$route.path === link.to ? 'bg-orange-500 text-white' : ''">
              <component :is="link.icon" class="w-5 h-5" />
            </span>
            <span class="font-medium">{{ link.label }}</span>
          </router-link>
          <div class="border-t border-gray-100 mt-2 pt-2">
            <router-link to="/profile" @click="showMobileMenu = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
              <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              </span>
              <span class="font-medium">Profile</span>
            </router-link>
            <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
              <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
              </span>
              <span class="font-medium">Logout</span>
            </button>
          </div>
        </nav>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, computed, h, onMounted, onUnmounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useMessagingStore } from '@/stores/messaging'
import { useRouter } from 'vue-router'
import echo from '@/services/echo'
import api from '@/services/api'

const auth = useAuthStore()
const messagingStore = useMessagingStore()
const router = useRouter()
const search = ref('')
const showMobileMenu = ref(false)
const showNotifications = ref(false)
const notifications = ref([])
const unreadMessagesCount = computed(() => messagingStore.unreadCount)
const isScrolled = ref(false)

function addNotification(n) {
  notifications.value.unshift({
    ...n,
    time: 'Just now'
  })
}

function handleSearch() {
  if (!search.value.trim()) return
  router.push({ path: '/explore', query: { q: search.value.trim() } })
  search.value = ''
}

const avatar = computed(() =>
  auth.user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

const handleLogout = async () => {
  try {
    await auth.logout()
    showMobileMenu.value = false
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

function handleScroll() {
  isScrolled.value = window.scrollY > 10
}

function setupEcho() {
  if (!auth.user?.id) return

  echo.private(`user.${auth.user.id}`)
    .listen('.friend.request', (e) => {
      addNotification({
        title: 'Friend Request',
        body: 'Someone sent you a friend request!',
        icon: ''
      })
    })
    .listen('.message.new', (e) => {
      messagingStore.incrementUnread()
      addNotification({
        title: 'New Message',
        body: `You received a new message.`,
        icon: ''
      })
    })

  // Public events
  echo.channel('events')
    .listen('.event.created', (e) => {
      addNotification({
        title: 'New Event',
        body: `${e.event.organizer?.name || 'Someone'} organized a new ${e.event.sport} match!`,
        icon: ''
      })
    })
}

watch(() => auth.user?.id, (newId) => {
  if (newId) setupEcho()
  else echo.leave(`user.${auth.user?.id}`)
})

async function fetchNotifications() {
  if (!auth.isLoggedIn) return
  try {
    const res = await api.get('/notifications')
    const items = res.data.data.data || []
    notifications.value = items.map(n => ({
      id: n.id,
      title: n.data.title || 'Notification',
      body: n.data.message || n.data.body || n.data.message || '',
      icon: n.data.icon || '',
      time: new Date(n.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      read: !!n.read_at
    }))
  } catch (err) { console.error('Failed to fetch notifications:', err) }
}

async function fetchUnreadCount() {
  if (!auth.isLoggedIn) return
  await messagingStore.fetchUnreadCount()
}

async function markAllRead() {
  try {
    await api.post('/notifications/read-all')
    notifications.value = []
  } catch (err) { console.error(err) }
}

onMounted(() => {
  setupEcho()
  fetchNotifications()
  fetchUnreadCount()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  if (auth.user?.id) echo.leave(`user.${auth.user.id}`)
  window.removeEventListener('scroll', handleScroll)
})

// Icon Components
const HomeIcon = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })]) }
const MapIcon = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7' })]) }
const CalendarIcon = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })]) }
const ChatIcon = { render: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })]) }

const navLinks = [
  { to: '/feed', label: 'Feed', icon: HomeIcon },
  { to: '/map', label: 'Map', icon: MapIcon },
  { to: '/events', label: 'Events', icon: CalendarIcon },
  { to: '/messages', label: 'Messages', icon: ChatIcon },
]
</script>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.4rem 0.875rem;
  border-radius: 9999px;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s ease;
  font-weight: 500;
}
.nav-link:hover { background: #f3f4f6; color: #111827; }
.nav-link.active { background: rgba(249,115,22,0.1); color: #ea580c; }

.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s ease;
}
.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
