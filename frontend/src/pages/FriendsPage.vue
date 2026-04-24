<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6 max-w-4xl mx-auto">
        <header class="mb-8">
          <h1 class="text-3xl font-black text-gray-900 tracking-tight">Connections</h1>
          <p class="text-gray-500 mt-1">Manage your friends and incoming requests.</p>
        </header>

        <!-- Tabs -->
        <div class="flex gap-4 border-b border-gray-100 mb-6">
          <button 
            @click="activeTab = 'friends'" 
            class="pb-3 text-sm font-bold transition-all relative"
            :class="activeTab === 'friends' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-600'"
          >
            My Friends
            <span v-if="activeTab === 'friends'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-full" />
          </button>
          <button 
            @click="activeTab = 'requests'" 
            class="pb-3 text-sm font-bold transition-all relative flex items-center gap-1.5"
            :class="activeTab === 'requests' ? 'text-orange-500' : 'text-gray-400 hover:text-gray-600'"
          >
            Pending Requests
            <span v-if="pendingRequests.length" class="bg-orange-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ pendingRequests.length }}</span>
            <span v-if="activeTab === 'requests'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-full" />
          </button>
        </div>

        <!-- Friends List -->
        <div v-if="activeTab === 'friends'">
          <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="i in 4" :key="i" class="card p-4 flex items-center gap-3">
              <SkeletonLoader width="48px" height="48px" class="!rounded-full" />
              <div class="flex-1">
                <SkeletonLoader width="60%" height="0.875rem" class="mb-2" />
                <SkeletonLoader width="40%" height="0.625rem" />
              </div>
            </div>
          </div>

          <div v-else-if="!friends.length" class="card p-12 text-center text-gray-400">
            <div class="text-5xl mb-3">🤝</div>
            <h3 class="font-bold text-gray-700">No friends yet</h3>
            <p class="text-sm mt-1">Find other athletes in the Explore tab!</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="f in friends" :key="f.id" class="card p-4 flex items-center gap-3 group hover:border-orange-200 transition-colors">
              <img :src="getFriend(f).avatar || defaultAvatar(getFriend(f).name)" class="w-12 h-12 rounded-full object-cover shrink-0" />
              <div class="flex-1 min-w-0">
                <router-link :to="'/users/' + getFriend(f).id" class="font-bold text-gray-900 hover:text-orange-500 truncate block">
                  {{ getFriend(f).name }}
                </router-link>
                <p class="text-xs text-gray-500 truncate">{{ getFriend(f).location || 'Athlete' }}</p>
              </div>
              <div class="flex items-center gap-1">
                <button @click="startChat(getFriend(f).id)" class="btn-ghost !text-orange-500 !p-2 transition-colors" title="Message">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                </button>
                <button @click="unfriend(f.id)" class="btn-ghost !text-red-500 !p-2 opacity-0 group-hover:opacity-100 transition-opacity" title="Remove Friend">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Requests -->
        <div v-else>
          <div v-if="!pendingRequests.length" class="card p-12 text-center text-gray-400">
            <div class="text-5xl mb-3">📬</div>
            <h3 class="font-bold text-gray-700">No pending requests</h3>
            <p class="text-sm mt-1">When someone wants to connect, it will show up here.</p>
          </div>

          <div v-else class="flex flex-col gap-4">
            <div v-for="req in pendingRequests" :key="req.id" class="card p-4 flex items-center gap-4">
              <img :src="req.user.avatar || defaultAvatar(req.user.name)" class="w-12 h-12 rounded-full object-cover" />
              <div class="flex-1 min-w-0">
                <router-link :to="'/users/' + req.user.id" class="font-bold text-gray-900 hover:text-orange-500">
                  {{ req.user.name }}
                </router-link>
                <p class="text-xs text-gray-500 truncate">Wants to be your friend</p>
              </div>
              <div class="flex gap-2">
                <button @click="handleRequest(req.id, 'ACCEPTED')" class="btn-primary !py-1.5 !px-4 !text-xs">Accept</button>
                <button @click="handleRequest(req.id, 'REJECTED')" class="btn-secondary !py-1.5 !px-4 !text-xs">Decline</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const activeTab = ref('friends')
const friends = ref([])
const pendingRequests = ref([])
const loading = ref(true)

const defaultAvatar = (name) => `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f97316&color=fff`

const getFriend = (friendship) => {
  return friendship.user_id === auth.user.id ? friendship.friend : friendship.user
}

async function fetchData() {
  loading.value = true
  try {
    const [fRes, pRes] = await Promise.all([
      api.get('/friendships?status=ACCEPTED'),
      api.get('/friendships?status=PENDING')
    ])
    friends.value = fRes.data.data
    // Only show requests where I am the receiver
    pendingRequests.value = pRes.data.data.filter(r => r.friend_id === auth.user.id)
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function startChat(friendId) {
  try {
    const res = await api.post('/conversations', { user_ids: [friendId] })
    const convo = res.data
    router.push(`/messages/${convo.id}`)
  } catch (err) {
    alert('Failed to start conversation')
  }
}

async function handleRequest(id, status) {
  try {
    await api.patch(`/friendships/${id}`, { status })
    fetchData()
  } catch (err) {
    alert('Failed to update request')
  }
}

async function unfriend(id) {
  if (!confirm('Are you sure you want to remove this friend?')) return
  try {
    await api.delete(`/friendships/${id}`)
    fetchData()
  } catch (err) {
    alert('Failed to remove friend')
  }
}

onMounted(fetchData)
</script>
