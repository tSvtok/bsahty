<template>
  <div class="page-wrapper flex flex-col" style="height: 100vh;">
    <Navbar />

    <!-- Chat layout -->
    <div class="flex flex-1 overflow-hidden pt-16">
      <!-- Back + header -->
      <div class="flex flex-col flex-1 max-w-2xl mx-auto w-full">
        <!-- Chat header -->
        <div class="px-4 py-3 bg-white border-b border-gray-100 flex items-center gap-3 shrink-0">
          <router-link to="/messages" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </router-link>
          <img :src="otherAvatar" class="w-9 h-9 rounded-full object-cover" />
          <div>
            <p class="font-semibold text-sm">{{ otherUser?.name || 'Athlete' }}</p>
            <p class="text-xs text-green-500 font-medium">Online</p>
          </div>
          <div class="flex-1" />
          <button class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"/></svg>
          </button>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-4 py-4 flex flex-col gap-3 bg-gray-50">
          <div v-if="loading" class="flex justify-center items-center h-full">
            <div class="w-6 h-6 border-2 border-orange-500 border-t-transparent rounded-full animate-spin" />
          </div>

          <template v-else>
            <div
              v-for="msg in messages" :key="msg.id"
              class="flex gap-2"
              :class="isMe(msg) ? 'justify-end' : 'justify-start'"
            >
              <img v-if="!isMe(msg)" :src="otherAvatar" class="w-7 h-7 rounded-full object-cover shrink-0 mt-auto" />
              <div
                class="max-w-xs px-4 py-2.5 rounded-2xl text-sm leading-relaxed"
                :class="isMe(msg)
                  ? 'bg-orange-500 text-white rounded-br-md'
                  : 'bg-white text-gray-800 shadow-sm rounded-bl-md'"
              >
                {{ msg.body }}
                <p class="text-[10px] mt-1 opacity-60 text-right">{{ formatTime(msg.created_at) }}</p>
              </div>
            </div>
          </template>
        </div>

        <!-- Input -->
        <div class="px-4 py-3 bg-white border-t border-gray-100 flex items-center gap-2 shrink-0">
          <input
            v-model="newMessage"
            @keydown.enter.prevent="sendMessage"
            type="text"
            placeholder="Type a message…"
            class="flex-1 px-4 py-2.5 bg-gray-100 rounded-full text-sm border-none outline-none focus:bg-white focus:ring-2 focus:ring-orange-400/30 transition-all"
          />
          <button
            @click="sendMessage"
            :disabled="!newMessage.trim() || sending"
            class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white hover:bg-orange-600 disabled:opacity-50 transition-all shrink-0 shadow-md"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import { messagingApi } from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const route       = useRoute()
const auth        = useAuthStore()
const messages    = ref([])
const newMessage  = ref('')
const loading     = ref(true)
const sending     = ref(false)
const messagesEl  = ref(null)
const otherUser   = ref(null)

const conversationId = computed(() => route.params.id)

const otherAvatar = computed(() =>
  otherUser.value?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(otherUser.value?.name || 'A')}&background=f97316&color=fff&size=60`
)

function isMe(msg) { return msg.sender_id === auth.user?.id }

function formatTime(ts) {
  if (!ts) return ''
  return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

async function scrollToBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

async function sendMessage() {
  if (!newMessage.value.trim() || sending.value) return
  const body = newMessage.value.trim()
  newMessage.value = ''
  sending.value = true

  // Optimistic
  messages.value.push({ id: Date.now(), body, sender_id: auth.user?.id, created_at: new Date().toISOString() })
  await scrollToBottom()

  try {
    await messagingApi.send({ conversation_id: conversationId.value, body })
  } catch {
    messages.value.pop()
  } finally {
    sending.value = false
  }
}

onMounted(async () => {
  try {
    const res  = await messagingApi.conversation(conversationId.value)
    messages.value = res.data.messages || []
    otherUser.value = res.data.other_user
  } catch {}
  finally {
    loading.value = false
    await scrollToBottom()
  }
})
</script>
