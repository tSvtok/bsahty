<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6 max-w-4xl">
        <header class="mb-8">
          <h1 class="text-2xl font-black text-gray-900">Find Athletes</h1>
          <p class="text-gray-500 text-sm">Connect with others in the community</p>
        </header>

        <!-- Search/Filter -->
        <div class="card p-4 mb-8 flex gap-3">
          <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search by name or sport..." 
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-orange-400/30 transition-all"
            />
          </div>
        </div>

        <!-- Users Grid -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="i in 6" :key="i" class="card p-5 animate-pulse">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-12 h-12 rounded-full bg-gray-100" />
              <div class="flex-1">
                <div class="h-3 bg-gray-100 rounded-full w-24 mb-2" />
                <div class="h-2 bg-gray-50 rounded-full w-16" />
              </div>
            </div>
            <div class="h-8 bg-gray-50 rounded-lg w-full" />
          </div>
        </div>

        <div v-else-if="!filteredUsers.length" class="card p-10 text-center text-gray-400">
          <div class="text-5xl mb-3"></div>
          <p>No athletes found matching your search.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="user in filteredUsers" :key="user.id" class="card p-5 flex flex-col items-center text-center group hover:shadow-lg transition-all duration-300">
            <router-link :to="`/profile/${user.id}`" class="block relative mb-3">
              <img 
                :src="user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=f97316&color=fff&size=120`" 
                class="w-20 h-20 rounded-full object-cover ring-4 ring-gray-50 group-hover:ring-orange-100 transition-all"
              />
              <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 border-4 border-white rounded-full" />
            </router-link>
            
            <router-link :to="`/profile/${user.id}`" class="font-bold text-gray-900 hover:text-orange-600 transition-colors">
              {{ user.name }}
            </router-link>
            <p class="text-xs text-gray-500 mb-4">{{ user.email }}</p>

            <div class="flex flex-wrap justify-center gap-1.5 mb-5 h-6 overflow-hidden">
              <span v-for="s in (user.sports || ['Athlete'])" :key="s" class="px-2 py-0.5 bg-orange-50 text-orange-600 rounded text-[10px] font-bold uppercase tracking-wider">
                {{ s }}
              </span>
            </div>

            <button 
              v-if="!isFriend(user.id)"
              @click="addFriend(user.id)"
              class="btn-primary !py-2 w-full text-xs"
              :disabled="friendRequests.has(user.id)"
            >
              {{ friendRequests.has(user.id) ? 'Request Sent' : 'Add Friend' }}
            </button>
            <button 
              v-else
              class="btn-secondary !py-2 w-full text-xs text-gray-400"
              disabled
            >
              Already Friends
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const route = useRoute()
const auth = useAuthStore()
const users = ref([])
const friendships = ref([])
const loading = ref(false)
const searchQuery = ref(route.query.q || '')
const friendRequests = ref(new Set())

watch(() => route.query.q, (newQ) => {
  searchQuery.value = newQ || ''
})

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    if (u.id === auth.user?.id) return false
    const match = u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                  (u.sports && u.sports.some(s => s.toLowerCase().includes(searchQuery.value.toLowerCase())))
    return match
  })
})

async function fetchData() {
  loading.value = true
  try {
    const [uRes, fRes] = await Promise.all([
      api.get('/users'),
      api.get('/friendships')
    ])
    users.value = uRes.data.data
    friendships.value = fRes.data.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

function isFriend(userId) {
  return friendships.value.some(f => 
    (f.user_id === auth.user?.id && f.friend_id === userId && f.status === 'ACCEPTED') ||
    (f.friend_id === auth.user?.id && f.user_id === userId && f.status === 'ACCEPTED')
  )
}

async function addFriend(userId) {
  try {
    await api.post('/friendships', { friend_id: userId })
    friendRequests.value.add(userId)
  } catch (e) {}
}

onMounted(fetchData)
</script>
