<template>
  <aside class="w-16 lg:w-64 shrink-0 hidden sm:flex flex-col gap-2 py-6 px-2 lg:px-3 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto transition-all duration-300">
    <!-- User Info -->
    <div v-if="auth.isLoggedIn" class="flex items-center gap-3 px-2 lg:px-3 py-3 mb-2">
      <img :src="avatar" :alt="auth.user?.name" class="w-11 h-11 rounded-full object-cover ring-2 ring-orange-400/40 shrink-0" />
      <div class="min-w-0 hidden lg:block">
        <p class="font-semibold text-sm truncate">{{ auth.user?.name || 'Athlete' }}</p>
        <p class="text-xs text-gray-500 truncate">{{ auth.user?.email }}</p>
      </div>
    </div>

    <!-- Nav Links -->
    <nav class="flex flex-col gap-0.5">
      <router-link
        v-for="link in navLinks" :key="link.to"
        :to="link.to"
        class="sidebar-link"
        :class="{ active: $route.path.startsWith(link.to) }"
      >
        <span class="w-10 h-10 lg:w-9 lg:h-9 rounded-xl flex items-center justify-center transition-colors shrink-0" :class="$route.path.startsWith(link.to) ? 'bg-orange-500 text-white shadow-md' : 'bg-gray-100 text-gray-600'">
          <span v-html="link.icon" class="w-5 h-5 [&>svg]:w-5 [&>svg]:h-5"></span>
        </span>
        <span class="hidden lg:inline">{{ link.label }}</span>
      </router-link>
    </nav>

    <div class="flex-1" />

    <!-- Bottom actions -->
    <div class="flex flex-col gap-0.5 border-t border-gray-100 pt-3">
      <router-link to="/profile" class="sidebar-link" :class="{ active: $route.path === '/profile' }">
        <span class="w-10 h-10 lg:w-9 lg:h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 shrink-0">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
        </span>
        <span class="hidden lg:inline">Profile</span>
      </router-link>
      <button @click="handleLogout" class="sidebar-link text-red-500 hover:bg-red-50">
        <span class="w-10 h-10 lg:w-9 lg:h-9 rounded-xl bg-red-50 flex items-center justify-center text-red-400 shrink-0">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        </span>
        <span class="hidden lg:inline">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth   = useAuthStore()
const router = useRouter()

const avatar = computed(() =>
  auth.user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

async function handleLogout() {
  await auth.logout()
  router.push('/')
}

const navLinks = computed(() => {
  const links = [
    { to: '/feed',     label: 'Feed',     icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>` },
    { to: '/explore',  label: 'Explore',  icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>` },
    { to: '/map',      label: 'Map',      icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>` },
    { to: '/events',   label: 'Events',   icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>` },
    { to: '/friends',  label: 'Friends',  icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>` },
    { to: '/messages', label: 'Messages', icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>` },
  ]
  if (auth.user?.role === 'ADMIN') {
    links.push({ to: '/admin', label: 'Admin', icon: `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>` })
  }
  return links
})
</script>

<style scoped>
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.875rem;
  color: #374151;
  font-weight: 500;
  font-size: 0.9375rem;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}
.sidebar-link:hover { background: #f9f8f6; }
.sidebar-link.active { color: #ea580c; font-weight: 600; }
</style>
