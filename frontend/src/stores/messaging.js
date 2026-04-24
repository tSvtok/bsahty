import { defineStore } from 'pinia'
import { ref } from 'vue'
import { messagingApi } from '@/services/api'

export const useMessagingStore = defineStore('messaging', () => {
  const unreadCount = ref(0)
  const conversations = ref([])

  async function fetchUnreadCount() {
    try {
      const res = await messagingApi.conversations() // Assuming we check unread across all
      // Better yet, use the specific unread-count endpoint
      const resCount = await messagingApi.unreadCount()
      unreadCount.value = resCount.data.count
    } catch (err) {
      console.error('Failed to fetch unread count:', err)
    }
  }

  function decrementUnread() {
    if (unreadCount.value > 0) unreadCount.value--
  }

  function incrementUnread() {
    unreadCount.value++
  }

  return {
    unreadCount,
    conversations,
    fetchUnreadCount,
    decrementUnread,
    incrementUnread
  }
})
