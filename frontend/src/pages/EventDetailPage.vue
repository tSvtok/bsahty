<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6 max-w-4xl mx-auto">
        <!-- Loading state -->
        <div v-if="loading" class="animate-pulse space-y-6">
          <div class="h-64 bg-gray-100 rounded-[2.5rem]" />
          <div class="space-y-3">
            <div class="h-8 bg-gray-100 rounded-full w-1/2" />
            <div class="h-4 bg-gray-100 rounded-full w-1/3" />
          </div>
        </div>

        <!-- Error state -->
        <div v-else-if="error" class="card p-12 text-center">
          <div class="text-5xl mb-4"></div>
          <h2 class="text-xl font-bold mb-2">Event not found</h2>
          <p class="text-gray-500 mb-6">The event you're looking for doesn't exist or has been removed.</p>
          <router-link to="/events" class="btn-primary inline-flex">Go back to Events</router-link>
        </div>

        <!-- Content -->
        <div v-else class="space-y-6">
          <!-- Hero section -->
          <div class="relative h-48 sm:h-64 rounded-[2.5rem] overflow-hidden group shadow-2xl">
            <!-- Background Image or Gradient -->
            <div v-if="event.image_url" class="absolute inset-0">
              <img :src="event.image_url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Match Cover" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
            </div>
            <div v-else class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
              <span class="text-7xl sm:text-9xl transform group-hover:scale-110 transition-transform duration-500">{{ sportEmoji }}</span>
            </div>
            
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors" />
            <div class="absolute bottom-6 left-8 right-8 text-white">
              <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider">{{ event.sport }}</span>
                <span v-if="event.level" class="px-3 py-1 bg-orange-400 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg">{{ event.level }}</span>
              </div>
              <h1 class="text-3xl sm:text-4xl font-black drop-shadow-lg">{{ event.title }}</h1>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left column: Info -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Details card -->
              <div class="card p-6 sm:p-8">
                <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                  <span class="w-2 h-6 bg-orange-500 rounded-full" />
                  Event Details
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Date</p>
                      <p class="font-bold text-gray-700">{{ formattedFullDate }}</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Time</p>
                      <p class="font-bold text-gray-700">{{ formattedTime }}</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Location</p>
                      <p class="font-bold text-gray-700">{{ event.location }}</p>
                    </div>
                  </div>

                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500">
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                      <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Availability</p>
                      <p class="font-bold" :class="isFull ? 'text-red-500' : 'text-green-600'">{{ spotsLeft }} spots remaining</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50">
                  <p class="text-gray-600 leading-relaxed">{{ event.description || 'No description provided for this event.' }}</p>
                </div>
              </div>

              <!-- Discussion section -->
              <div class="card p-6 sm:p-8">
                <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                  <span class="w-2 h-6 bg-blue-500 rounded-full" />
                  Discussion
                </h3>
                
                <div v-if="!event.questions?.length" class="text-center py-10 text-gray-400">
                  <p>No questions yet. Be the first to ask!</p>
                </div>
                
                <div v-else class="space-y-4">
                   <div v-for="q in event.questions" :key="q.id" class="p-4 bg-gray-50 rounded-2xl">
                     <div class="flex gap-3 mb-2">
                       <img :src="q.user?.avatar_url || `https://ui-avatars.com/api/?name=${q.user?.name}&background=random`" class="w-8 h-8 rounded-full" />
                       <div>
                         <p class="text-xs font-bold text-gray-700">{{ q.user?.name }}</p>
                         <p class="text-[10px] text-gray-400">{{ new Date(q.created_at).toLocaleDateString() }}</p>
                       </div>
                     </div>
                     <p class="text-sm text-gray-600 ml-11">{{ q.content }}</p>
                   </div>
                </div>
              </div>
            </div>

            <!-- Right column: Organizer & Participants -->
            <div class="space-y-6">
              <!-- Organizer -->
              <div class="card p-6 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4">Organized by</p>
                <router-link :to="`/profile/${event.organizer_id}`" class="block group">
                  <img :src="organizerAvatar" class="w-20 h-20 rounded-3xl mx-auto object-cover mb-3 shadow-xl group-hover:scale-105 transition-transform" />
                  <h4 class="font-bold text-gray-800 group-hover:text-orange-500 transition-colors">{{ event.organizer?.name }}</h4>
                  <p class="text-xs text-gray-500">{{ event.organizer?.city || 'B-SSAHTY Athlete' }}</p>
                </router-link>
                
                <div class="mt-6">
                  <button 
                    @click="handleJoin" 
                    class="w-full justify-center btn-primary"
                    :disabled="joining || (isFull && !joined)"
                  >
                    <template v-if="joining">
                       <svg class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                       Processing...
                    </template>
                    <template v-else>
                      {{ joined ? 'Leave Event' : (isFull ? 'Full' : 'Join Event') }}
                    </template>
                  </button>
                </div>
              </div>

              <!-- Participants -->
              <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                  <h4 class="font-bold text-gray-800">Participants</h4>
                  <span class="text-xs font-bold text-orange-500">{{ participantCount }} / {{ event.max_participants }}</span>
                </div>
                
                <div class="space-y-3">
                  <div v-for="p in event.participants" :key="p.id" class="flex items-center gap-3">
                    <img :src="p.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.name)}&background=random`" class="w-8 h-8 rounded-full object-cover" />
                    <span class="text-sm font-medium text-gray-700">{{ p.name }}</span>
                    <span v-if="p.id === event.organizer_id" class="ml-auto text-[10px] font-black text-orange-500 uppercase">Host</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import { eventsApi } from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const auth  = useAuthStore()

const loading = ref(true)
const joining = ref(false)
const error   = ref(null)
const event   = ref(null)

const sportEmojiMap = {
  football: '', basketball: '', tennis: '', volleyball: '',
  swimming: '', running: '', cycling: '', padel: '', default: ''
}

const sportEmoji = computed(() => sportEmojiMap[event.value?.sport?.toLowerCase()] || sportEmojiMap.default)
const participantCount = computed(() => event.value?.participants?.length || 0)
const maxParticipants  = computed(() => event.value?.max_participants || 10)
const spotsLeft = computed(() => Math.max(0, maxParticipants.value - participantCount.value))
const isFull    = computed(() => spotsLeft.value <= 0)
const joined    = computed(() => event.value?.participants?.some(p => p.id === auth.user?.id))

const organizerAvatar = computed(() => 
  event.value?.organizer?.avatar_url || 
  `https://ui-avatars.com/api/?name=${encodeURIComponent(event.value?.organizer?.name || 'O')}&background=f97316&color=fff&size=80`
)

const formattedFullDate = computed(() => {
  if (!event.value?.date) return ''
  return new Date(event.value.date).toLocaleDateString('en-US', { 
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
  })
})

const formattedTime = computed(() => {
  if (!event.value?.date) return ''
  const date = new Date(event.value.date)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })
})

async function fetchEvent() {
  loading.value = true
  try {
    const res = await eventsApi.get(route.params.id)
    event.value = res.data.data
  } catch (err) {
    error.value = err
  } finally {
    loading.value = false
  }
}

async function handleJoin() {
  if (!event.value) return
  joining.value = true
  const isJoining = !joined.value
  
  try {
    await eventsApi.update(event.value.id, { join: isJoining })
    // Reload event to get updated participant list
    await fetchEvent()
  } catch (err) {
    alert('Failed to update event status')
  } finally {
    joining.value = false
  }
}

onMounted(fetchEvent)
</script>

<style scoped>
</style>
