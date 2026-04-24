<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <!-- Feed column -->
      <main class="flex-1 py-6 px-4 md:px-6 max-w-2xl">
        <!-- Category Filters -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2 no-scrollbar">
          <button
            v-for="cat in categories" :key="cat"
            @click="toggleCategory(cat)"
            class="px-4 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 border"
            :class="selectedCategory === cat ? 'bg-orange-500 border-orange-500 text-white' : 'bg-white border-gray-200 text-gray-500 hover:border-orange-200'"
          >
            {{ cat }}
          </button>
        </div>

        <!-- Create post trigger -->
        <div class="card p-4 flex items-center gap-3 mb-6 cursor-pointer hover:shadow-md transition-shadow" @click="showCreatePost = true">
          <img :src="myAvatar" class="w-10 h-10 rounded-full object-cover" />
          <div class="flex-1 px-4 py-2.5 bg-gray-50 rounded-full text-gray-400 text-sm hover:bg-gray-100 transition-colors">
            What's happening in your world, {{ auth.user?.name?.split(' ')[0] || 'Athlete' }}? 🏅
          </div>
          <button class="btn-primary !py-2 !px-4 !text-sm shrink-0" @click.stop="showCreatePost = true">Post</button>
        </div>

        <!-- Posts -->
        <div v-if="appStore.feedLoading" class="flex flex-col gap-4">
          <div v-for="i in 3" :key="i" class="card p-5">
            <div class="flex items-center gap-3 mb-5">
              <SkeletonLoader width="40px" height="40px" class="!rounded-full" />
              <div class="flex-1">
                <SkeletonLoader width="120px" height="0.875rem" class="mb-2" />
                <SkeletonLoader width="80px" height="0.625rem" />
              </div>
              <SkeletonLoader width="32px" height="1rem" />
            </div>
            <div class="space-y-3 mb-6">
              <SkeletonLoader width="100%" height="0.75rem" />
              <SkeletonLoader width="90%" height="0.75rem" />
              <SkeletonLoader width="60%" height="0.75rem" />
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
              <div class="flex gap-4">
                <SkeletonLoader width="48px" height="1rem" />
                <SkeletonLoader width="48px" height="1rem" />
              </div>
              <SkeletonLoader width="64px" height="1rem" />
            </div>
          </div>
        </div>

        <div v-else-if="!appStore.posts.length" class="card p-10 text-center text-gray-400">
          <div class="text-5xl mb-3">🏅</div>
          <h3 class="font-semibold text-gray-700 mb-1">No posts yet</h3>
          <p class="text-sm">Be the first to share something with the community!</p>
        </div>

        <div v-else class="flex flex-col gap-4">
          <PostCard v-for="post in appStore.posts" :key="post.id" :post="post" />
          
          <!-- Infinite Scroll Target -->
          <div ref="loadMoreTarget" class="py-10 flex justify-center">
            <div v-if="appStore.feedLoading && appStore.posts.length" class="flex gap-2">
              <SkeletonLoader width="8px" height="8px" class="!rounded-full animate-bounce" />
              <SkeletonLoader width="8px" height="8px" class="!rounded-full animate-bounce [animation-delay:0.2s]" />
              <SkeletonLoader width="8px" height="8px" class="!rounded-full animate-bounce [animation-delay:0.4s]" />
            </div>
          </div>
        </div>
      </main>

      <!-- Right sidebar -->
      <aside class="w-80 shrink-0 hidden xl:block py-6 px-4">
        <TrendingWidget />
      </aside>
    </div>

    <CreatePostModal v-model="showCreatePost" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import PostCard from '@/components/PostCard.vue'
import TrendingWidget from '@/components/TrendingWidget.vue'
import CreatePostModal from '@/modals/CreatePostModal.vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'

const auth     = useAuthStore()
const appStore = useAppStore()
const showCreatePost = ref(false)
const selectedCategory = ref('All')
const loadMoreTarget = ref(null)

const categories = ['All', 'FOOTBALL', 'BASKETBALL', 'TENNIS', 'RUNNING', 'CYCLING', 'FITNESS', 'OTHER']

function toggleCategory(cat) {
  selectedCategory.value = cat
  const params = cat === 'All' ? {} : { sport_category: cat }
  appStore.fetchFeed({ ...params, page: 1 })
}

const myAvatar = computed(() =>
  auth.user?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

let observer = null

onMounted(() => {
  appStore.fetchFeed()
  
  // Infinite Scroll Observer
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !appStore.feedLoading) {
      if (appStore.feedPagination.current_page < appStore.feedPagination.last_page) {
        const params = selectedCategory.value === 'All' ? {} : { sport_category: selectedCategory.value }
        appStore.fetchFeed({ ...params, page: appStore.feedPagination.current_page + 1 }, true)
      }
    }
  }, { threshold: 0.1 })

  if (loadMoreTarget.value) observer.observe(loadMoreTarget.value)
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>
