<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6 max-w-3xl">
        <!-- Cover + avatar -->
        <div class="card mb-6 overflow-hidden">
          <!-- Cover -->
          <div class="aspect-[3/1] bg-gradient-to-br from-orange-400 to-orange-600 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1517649763962-0c623066013b?w=800')] bg-cover bg-center" />
          </div>

          <!-- Info -->
          <div class="px-6 pb-6">
            <div class="flex items-end justify-between -mt-10 mb-4">
              <img :src="avatar" :alt="user?.name"
                class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-lg" />
              
              <div v-if="isOwnProfile">
                <button @click="showEdit = true" class="btn-secondary !py-1.5 !px-4 !text-sm mb-1">
                  Edit Profile
                </button>
              </div>
              <div v-else class="flex gap-2">
                <button v-if="friendshipStatus === 'NONE'" @click="sendFriendRequest" class="btn-primary !py-1.5 !px-4 !text-sm mb-1">
                  Add Friend
                </button>
                <span v-else-if="friendshipStatus === 'PENDING'" class="badge badge-primary !py-2 !px-4 mb-1">
                  Pending
                </span>
                <button v-else-if="friendshipStatus === 'ACCEPTED'" @click="removeFriend" class="btn-secondary !py-1.5 !px-4 !text-sm mb-1 text-red-500">
                  Friends
                </button>
                <button class="btn-secondary !py-1.5 !px-4 !text-sm mb-1">Message</button>
              </div>
            </div>

            <h1 class="text-xl font-black">{{ user?.name || 'Athlete' }}</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ user?.email }}</p>
            <p v-if="user?.bio" class="text-gray-700 text-sm mt-3 leading-relaxed">{{ user.bio }}</p>

            <!-- Location -->
            <div v-if="user?.location" class="flex items-center gap-1.5 text-sm text-gray-500 mt-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
              {{ user.location }}
            </div>

            <!-- Favorite sports -->
            <div v-if="user?.sports?.length" class="flex flex-wrap gap-2 mt-4">
              <span v-for="s in user.sports" :key="s" class="badge badge-primary">{{ s }}</span>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4 mb-6">
          <div v-for="stat in stats" :key="stat.label" class="card p-3 sm:p-5 text-center">
            <p class="text-xl sm:text-2xl font-black text-orange-500">{{ stat.value }}</p>
            <p class="text-[10px] sm:text-xs text-gray-500 mt-1 font-medium">{{ stat.label }}</p>
          </div>
        </div>

        <!-- Recent activity tabs -->
        <div class="card overflow-hidden">
          <div class="flex border-b border-gray-100">
            <button
              v-for="tab in tabs" :key="tab"
              @click="activeTab = tab"
              class="flex-1 py-3 text-sm font-semibold transition-colors"
              :class="activeTab === tab ? 'text-orange-500 border-b-2 border-orange-500' : 'text-gray-500 hover:text-gray-700'"
            >{{ tab }}</button>
          </div>

          <div class="p-6">
            <!-- Posts tab -->
            <template v-if="activeTab === 'Posts'">
              <div v-if="myPosts.length" class="flex flex-col gap-4">
                <PostCard v-for="p in myPosts" :key="p.id" :post="p" />
              </div>
              <div v-else class="text-center text-gray-400 py-8">
                <div class="text-4xl mb-2">✍️</div>
                <p class="text-sm">No posts yet.</p>
              </div>
            </template>

            <!-- Events tab -->
            <template v-if="activeTab === 'Events'">
              <div v-if="myEvents.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <EventCard v-for="e in myEvents" :key="e.id" :event="e" />
              </div>
              <div v-else class="text-center text-gray-400 py-8">
                <div class="text-4xl mb-2">🏟</div>
                <p class="text-sm">No events yet.</p>
              </div>
            </template>

            <!-- About tab -->
            <template v-if="activeTab === 'About'">
              <dl class="flex flex-col gap-4 text-sm">
                <div class="flex gap-4">
                  <dt class="text-gray-500 w-32 shrink-0 font-medium">Joined</dt>
                  <dd class="text-gray-800">{{ joinedDate }}</dd>
                </div>
                <div class="flex gap-4">
                  <dt class="text-gray-500 w-32 shrink-0 font-medium">Location</dt>
                  <dd class="text-gray-800">{{ user?.location || '—' }}</dd>
                </div>
                <div class="flex gap-4">
                  <dt class="text-gray-500 w-32 shrink-0 font-medium">Sports</dt>
                  <dd class="text-gray-800">{{ user?.sports?.join(', ') || '—' }}</dd>
                </div>
              </dl>
            </template>
          </div>
        </div>
      </main>
    </div>

    <EditProfileModal v-model="showEdit" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import PostCard from '@/components/PostCard.vue'
import EventCard from '@/components/EventCard.vue'
import EditProfileModal from '@/modals/EditProfileModal.vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'
import api from '@/services/api'

const route    = useRoute()
const auth     = useAuthStore()
const appStore = useAppStore()

const showEdit  = ref(false)
const activeTab = ref('Posts')
const tabs = ['Posts', 'Events', 'About']

const otherUser = ref(null)
const friendship = ref(null)
const loading = ref(false)

const isOwnProfile = computed(() => !route.params.id || parseInt(route.params.id) === auth.user?.id)
const user = computed(() => isOwnProfile.value ? auth.user : otherUser.value)

const friendshipStatus = computed(() => {
  if (!friendship.value) return 'NONE'
  return friendship.value.status
})

const avatar = computed(() =>
  user.value?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name || 'A')}&background=f97316&color=fff&size=160`
)

const myPosts = computed(() => appStore.posts.filter(p => p.user_id === user.value?.id))
const myEvents = computed(() => appStore.events.filter(e => e.organizer_id === user.value?.id))

const stats = computed(() => [
  { value: myPosts.value.length,  label: 'Posts' },
  { value: myEvents.value.length, label: 'Events' },
  { value: user.value?.sports?.length || 0, label: 'Sports' },
])

const joinedDate = computed(() => {
  if (!user.value?.created_at) return '—'
  return new Date(user.value.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
})

async function fetchProfile() {
  if (isOwnProfile.value) return
  
  loading.value = true
  try {
    const res = await api.get(`/users/${route.params.id}`)
    otherUser.value = res.data.data
    
    // Check friendship status
    const friendsRes = await api.get('/friendships')
    const friendsList = friendsRes.data.data || []
    friendship.value = friendsList.find(f => 
      (f.user_id === auth.user?.id && f.friend_id === otherUser.value.id) ||
      (f.friend_id === auth.user?.id && f.user_id === otherUser.value.id)
    )
  } catch (e) {
    console.error('Failed to fetch profile', e)
  } finally {
    loading.value = false
  }
}

async function sendFriendRequest() {
  try {
    const res = await api.post('/friendships', { friend_id: user.value.id })
    friendship.value = res.data.data
  } catch (e) {}
}

async function removeFriend() {
  if (!friendship.value) return
  try {
    await api.delete(`/friendships/${friendship.value.id}`)
    friendship.value = null
  } catch (e) {}
}

onMounted(() => { 
  appStore.fetchFeed()
  appStore.fetchEvents()
  fetchProfile()
})

watch(() => route.params.id, fetchProfile)
</script>
