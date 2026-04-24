<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
          <div>
            <h1 class="text-2xl font-black">Local Matches</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ nearbyOnly ? `Showing games in ${auth.user?.city || 'your area'}` : 'Find and join games happening near you' }}</p>
          </div>
          <button @click="showCreate = true" class="btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Organize Match
          </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
          <button
            v-for="s in ['All', ...sports]" :key="s"
            @click="toggleFilter(s)"
            class="px-4 py-1.5 rounded-full text-sm font-medium border transition-all"
            :class="filter === s ? 'bg-orange-500 text-white border-orange-500 shadow' : 'border-gray-200 text-gray-600 hover:border-orange-300 bg-white'"
          >{{ s }}</button>

          <div class="h-6 w-px bg-gray-200 mx-1 hidden sm:block" />

          <button
            @click="toggleNearby"
            class="px-4 py-1.5 rounded-full text-sm font-medium border transition-all flex items-center gap-1.5"
            :class="nearbyOnly ? 'bg-blue-500 text-white border-blue-500 shadow' : 'border-gray-200 text-gray-600 hover:border-blue-300 bg-white'"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Near Me
          </button>
        </div>

        <!-- Events grid -->
        <div v-if="appStore.eventsLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <div v-for="i in 6" :key="i" class="card p-5 animate-pulse flex flex-col gap-3">
            <div class="h-32 bg-gray-100 rounded-2xl mb-2" />
            <div class="h-4 bg-gray-100 rounded-full w-3/4" />
            <div class="flex gap-2">
              <div class="h-5 bg-gray-50 rounded-full w-16" />
              <div class="h-5 bg-gray-50 rounded-full w-16" />
            </div>
            <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between">
              <div class="h-3 bg-gray-50 rounded-full w-20" />
              <div class="h-6 bg-gray-100 rounded-full w-16" />
            </div>
          </div>
        </div>

        <div v-else-if="!appStore.events.length" class="card p-12 text-center text-gray-400">
          <div class="text-5xl mb-3"></div>
          <h3 class="font-semibold text-gray-700 mb-1">No events found</h3>
          <p class="text-sm">Try a different sport or organize your own match!</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <EventCard v-for="event in appStore.events" :key="event.id" :event="event" />
        </div>

        <!-- Infinite Scroll Target -->
        <div ref="loadMoreTarget" class="py-10 flex justify-center">
          <div v-if="appStore.eventsLoading && appStore.events.length" class="flex gap-2">
            <div class="w-2 h-2 rounded-full bg-orange-400 animate-bounce" />
            <div class="w-2 h-2 rounded-full bg-orange-400 animate-bounce [animation-delay:0.2s]" />
            <div class="w-2 h-2 rounded-full bg-orange-400 animate-bounce [animation-delay:0.4s]" />
          </div>
        </div>
      </main>

      <!-- Right sidebar -->
      <aside class="w-72 shrink-0 hidden xl:block py-6 px-4">
        <TrendingWidget />
      </aside>
    </div>

    <CreateEventModal v-model="showCreate" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import EventCard from '@/components/EventCard.vue'
import TrendingWidget from '@/components/TrendingWidget.vue'
import CreateEventModal from '@/modals/CreateEventModal.vue'
import { useAppStore } from '@/stores/app'
import { useAuthStore } from '@/stores/auth'

const appStore   = useAppStore()
const auth       = useAuthStore()
const showCreate = ref(false)
const filter     = ref('All')
const nearbyOnly = ref(false)
const loadMoreTarget = ref(null)

const sports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Running', 'Cycling', 'Padel']

function toggleFilter(s) {
  filter.value = s
  fetchWithParams()
}

function toggleNearby() {
  nearbyOnly.value = !nearbyOnly.value
  fetchWithParams()
}

function fetchWithParams(append = false) {
  const page = append ? appStore.eventsPagination.current_page + 1 : 1
  const params = { page }
  if (filter.value !== 'All') params.sport = filter.value
  if (nearbyOnly.value) params.nearby = true
  
  appStore.fetchEvents(params, append)
}

let observer = null

onMounted(() => {
  appStore.fetchEvents()

  // Infinite Scroll Observer
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !appStore.eventsLoading) {
      if (appStore.eventsPagination.current_page < appStore.eventsPagination.last_page) {
        fetchWithParams(true)
      }
    }
  }, { threshold: 0.1 })

  if (loadMoreTarget.value) observer.observe(loadMoreTarget.value)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>
