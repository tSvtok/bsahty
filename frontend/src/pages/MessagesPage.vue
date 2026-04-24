<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6 max-w-2xl">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-black">Messages</h1>
          <span class="badge badge-primary">{{ conversations.length }}</span>
        </div>

        <!-- Search convos -->
        <div class="relative mb-4">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="searchTerm" type="text" placeholder="Search conversations..." class="input-field !pl-9" />
        </div>

        <!-- List -->
        <div v-if="loading" class="flex flex-col gap-2">
          <div v-for="i in 5" :key="i" class="card p-4 flex gap-3 animate-pulse">
            <div class="w-12 h-12 rounded-full bg-gray-100 shrink-0" />
            <div class="flex-1"><div class="h-3 bg-gray-100 rounded-full mb-2 w-1/2"/><div class="h-2 bg-gray-100 rounded-full w-3/4"/></div>
          </div>
        </div>

        <div v-else-if="!filteredConversations.length" class="card p-10 text-center text-gray-400">
          <div class="text-5xl mb-3">💬</div>
          <p class="font-medium text-gray-700">No conversations yet</p>
          <p class="text-sm mt-1">Connect with athletes and start chatting!</p>
        </div>

        <div v-else class="flex flex-col gap-3">
          <router-link
            v-for="c in filteredConversations"
            :key="c.id"
            :to="`/messages/${c.id}`"
            class="card p-4 flex items-center gap-4 hover:shadow-lg hover:border-orange-200 transition-all cursor-pointer text-inherit no-underline group"
          >
            <div class="relative">
              <img :src="getAvatar(c.other_user)" class="w-14 h-14 rounded-full object-cover ring-2 ring-gray-50 group-hover:ring-orange-100 transition-all" />
              <div v-if="c.other_user?.is_online" class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white shadow-sm" />
              <span v-if="c.unread_count" class="absolute -top-1 -right-1 min-w-[20px] h-5 bg-orange-500 rounded-full text-white text-[10px] font-black flex items-center justify-center px-1 ring-2 ring-white shadow-sm">
                {{ c.unread_count > 9 ? '9+' : c.unread_count }}
              </span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-0.5">
                <p class="font-bold text-sm text-gray-900">{{ c.other_user?.name }}</p>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ formatTime(c.last_message?.created_at) }}</span>
              </div>
              <div class="flex items-center gap-1">
                <p class="text-sm text-gray-500 truncate" :class="{ 'font-semibold text-gray-900': c.unread_count }">
                  <span v-if="c.last_message?.user_id === auth.user?.id" class="text-gray-400 mr-0.5">You:</span>
                  {{ c.last_message?.body || 'No messages yet' }}
                </p>
              </div>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
              <svg class="w-5 h-5 text-gray-300 group-hover:text-orange-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
          </router-link>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import { messagingApi } from '@/services/api'

const conversations = ref([])
const loading       = ref(true)
const searchTerm    = ref('')

const filteredConversations = computed(() => {
  if (!searchTerm.value) return conversations.value
  return conversations.value.filter(c =>
    c.other_user?.name?.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

function getAvatar(user) {
  return user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'A')}&background=f97316&color=fff&size=80`
}

function formatTime(ts) {
  if (!ts) return ''
  const d = new Date(ts), diff = (Date.now() - d) / 1000
  if (diff < 86400) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  return d.toLocaleDateString()
}

onMounted(async () => {
  try {
    const res = await messagingApi.conversations()
    conversations.value = res.data.data || []
  } catch {}
  finally { loading.value = false }
})
</script>
