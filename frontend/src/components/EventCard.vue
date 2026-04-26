<template>
  <article 
    class="card overflow-hidden flex flex-col cursor-pointer group hover:border-orange-200 transition-all active:scale-[0.98]"
    @click="router.push(`/events/${event.id}`)"
  >
    <!-- Cover Image -->
    <div class="h-40 sm:h-48 relative overflow-hidden bg-gray-100">
      <img 
        v-if="displayImage" 
        :src="displayImage" 
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
        @error="(e) => e.target.src = sportImageMap.default"
      />
      
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60" />

      <!-- Badges on top of image -->
      <div class="absolute bottom-3 left-3 flex gap-2">
        <span class="glass px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-white border-white/20">
          {{ event.sport }}
        </span>
        <span v-if="event.level" class="bg-orange-500/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider text-white shadow-lg">
          {{ event.level }}
        </span>
      </div>

      <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors" />
    </div>

    <div class="p-5 flex flex-col gap-3">
    <!-- Header row -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <h3 class="font-bold text-base leading-snug group-hover:text-orange-500 transition-colors truncate">{{ event.title }}</h3>
      </div>
      <div class="text-right shrink-0">
        <p class="text-xs font-semibold text-orange-500">{{ formattedDate }}</p>
        <p class="text-xs text-gray-500">{{ formattedTime }}</p>
      </div>
    </div>

    <!-- Location -->
    <div class="flex items-center gap-1.5 text-sm text-gray-500">
      <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <!-- Has coordinates → internal map link -->
      <router-link
        v-if="event.latitude && event.longitude"
        :to="{ name: 'map', query: { lat: event.latitude, lng: event.longitude, label: event.location } }"
        class="truncate hover:text-orange-500 hover:underline transition-colors"
        title="View on Map"
        @click.stop
      >{{ event.location }}</router-link>
      <!-- No coordinates → plain text -->
      <span v-else class="truncate">{{ event.location || '—' }}</span>
    </div>

    <!-- Participants progress -->
    <div class="flex flex-col gap-1.5">
      <div class="flex items-center justify-between text-xs">
        <span class="text-gray-500 font-medium">{{ participantCount }} / {{ maxParticipants }} players</span>
        <span :class="isFull ? 'text-red-500' : 'text-green-600'" class="font-semibold">
          {{ isFull ? 'Full' : `${spotsLeft} spots left` }}
        </span>
      </div>
      <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
        <div
          class="h-full rounded-full transition-all duration-700"
          :class="progressColor"
          :style="{ width: `${progressPct}%` }"
        />
      </div>
    </div>

    <!-- Organizer + action -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <img :src="organizerAvatar" class="w-6 h-6 rounded-full object-cover" />
        <span class="text-xs text-gray-500">by <span class="font-medium text-gray-700">{{ event.organizer?.name || 'Organizer' }}</span></span>
      </div>
      <button
        @click.stop="handleJoin"
        class="btn-primary !py-1.5 !px-4 !text-xs"
        :class="{ '!opacity-50 !cursor-not-allowed': isFull }"
        :disabled="isFull"
      >
        {{ joined ? 'Joined ' : (isFull ? 'Full' : 'Join') }}
      </button>
    </div>
  </div>
</article>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { eventsApi } from '@/services/api'
import echo from '@/services/echo'

const props = defineProps({ event: { type: Object, required: true } })
const router = useRouter()
const joined = ref(props.event.is_joined || false)
const realTimeParticipantCount = ref(props.event.participants_count || 0)

const participantCount = computed(() => realTimeParticipantCount.value)
const maxParticipants  = computed(() => props.event.max_participants || 10)
const spotsLeft   = computed(() => maxParticipants.value - participantCount.value)
const isFull      = computed(() => spotsLeft.value <= 0)
const progressPct = computed(() => Math.min(100, (participantCount.value / maxParticipants.value) * 100))

const sportImageMap = {
  football: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800',
  basketball: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800',
  tennis: 'https://images.unsplash.com/photo-1595435066311-66774a3f1234?w=800',
  volleyball: 'https://images.unsplash.com/photo-1592656094267-764a45160876?w=800',
  running: 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=800',
  cycling: 'https://images.unsplash.com/photo-1483721310020-03333e577078?w=800',
  padel: 'https://images.unsplash.com/photo-1626245917164-326e033e6f98?w=800',
  swimming: 'https://images.unsplash.com/photo-1530549387074-d56a99e142e0?w=800',
  default: 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=800'
}

const displayImage = computed(() => {
  return props.event.image_url || sportImageMap[props.event.sport?.toLowerCase()] || sportImageMap.default
})

const progressColor = computed(() => {
  if (progressPct.value >= 90) return 'bg-red-400'
  if (progressPct.value >= 60) return 'bg-orange-400'
  return 'bg-green-400'
})

const organizerAvatar = computed(() => 
  props.event.organizer?.avatar_url || 
  `https://ui-avatars.com/api/?name=${encodeURIComponent(props.event.organizer?.name || 'O')}&background=f97316&color=fff&size=40`
)

const formattedDate = computed(() => {
  if (!props.event.date) return ''
  return new Date(props.event.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
})

const formattedTime = computed(() => {
  if (!props.event.date) return ''
  const date = new Date(props.event.date)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })
})

async function handleJoin() {
  const isJoining = !joined.value
  if (isJoining && isFull.value) return
  
  joined.value = isJoining
  realTimeParticipantCount.value += isJoining ? 1 : -1

  try { 
    await eventsApi.update(props.event.id, { join: isJoining }) 
  }
  catch { 
    joined.value = !isJoining
    realTimeParticipantCount.value += isJoining ? -1 : 1
  }
}

onMounted(() => {
  echo.channel('events')
    .listen('.participants.updated', (e) => {
      if (e.eventId === props.event.id) {
        realTimeParticipantCount.value = e.participantCount
      }
    })
})

onUnmounted(() => {
  echo.leaveChannel('events')
})
</script>
