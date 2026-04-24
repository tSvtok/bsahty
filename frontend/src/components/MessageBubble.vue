<template>
  <div class="flex flex-col mb-4" :class="isMe ? 'items-end' : 'items-start'">
    <div 
      class="max-w-[85%] sm:max-w-[70%] px-4 py-2.5 rounded-2xl text-sm shadow-sm"
      :class="isMe ? 'bg-orange-500 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'"
    >
      {{ message.content || message.body }}
    </div>
    <div class="flex items-center gap-1 mt-1 px-1">
      <span class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">{{ formatTime(message.created_at) }}</span>
      <svg v-if="isMe" class="w-3 h-3 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
      </svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  message: { type: Object, required: true }
})

const auth = useAuthStore()
const isMe = computed(() => props.message.user_id === auth.user?.id)

function formatTime(ts) {
  if (!ts) return ''
  const d = new Date(ts)
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}
</script>
