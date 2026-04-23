<template>
  <article class="card p-5 flex flex-col gap-3 cursor-pointer group">
    <!-- Header row -->
    <div class="flex items-start justify-between gap-3">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-2xl">{{ sportEmoji }}</span>
          <span class="badge badge-primary text-xs">{{ event.sport }}</span>
          <span v-if="event.level" class="badge badge-info text-xs">{{ event.level }}</span>
        </div>
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
      <span class="truncate">{{ event.location }}</span>
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
        {{ joined ? 'Joined ✓' : (isFull ? 'Full' : 'Join') }}
      </button>
    </div>
  </article>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { eventsApi } from '@/services/api'
import echo from '@/services/echo'

const props = defineProps({ event: { type: Object, required: true } })
const joined = ref(props.event.is_joined || false)
const realTimeParticipantCount = ref(props.event.participants_count || 0)

const sportEmojiMap = {
  football: '⚽', basketball: '🏀', tennis: '🎾', volleyball: '🏐',
  swimming: '🏊', running: '🏃', cycling: '🚴', padel: '🎾', default: '🏅'
}

const sportEmoji = computed(() => sportEmojiMap[props.event.sport?.toLowerCase()] || sportEmojiMap.default)
const participantCount = computed(() => realTimeParticipantCount.value)
const maxParticipants  = computed(() => props.event.max_participants || 10)
const spotsLeft   = computed(() => maxParticipants.value - participantCount.value)
const isFull      = computed(() => spotsLeft.value <= 0)
const progressPct = computed(() => Math.min(100, (participantCount.value / maxParticipants.value) * 100))

const progressColor = computed(() => {
  if (progressPct.value >= 90) return 'bg-red-400'
  if (progressPct.value >= 60) return 'bg-orange-400'
  return 'bg-green-400'
})

const organizerAvatar = computed(() =>
  props.event.organizer?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(props.event.organizer?.name || 'O')}&background=f97316&color=fff&size=40`
)

const formattedDate = computed(() => {
  if (!props.event.date) return ''
  return new Date(props.event.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
})

const formattedTime = computed(() => {
  if (!props.event.time) return ''
  return props.event.time.slice(0, 5)
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
