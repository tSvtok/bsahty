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
          <div v-for="i in 3" :key="i" class="card p-5 animate-pulse">
            <div class="flex items-center gap-3 mb-5">
              <div class="w-10 h-10 rounded-full bg-gray-100" />
              <div class="flex-1">
                <div class="h-3.5 bg-gray-100 rounded-full w-32 mb-2" />
                <div class="h-2.5 bg-gray-50 rounded-full w-20" />
              </div>
              <div class="w-8 h-4 bg-gray-50 rounded-full" />
            </div>
            <div class="space-y-3 mb-6">
              <div class="h-3 bg-gray-100 rounded-full w-full" />
              <div class="h-3 bg-gray-100 rounded-full w-5/6" />
              <div class="h-3 bg-gray-100 rounded-full w-2/3" />
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
              <div class="flex gap-4">
                <div class="h-4 bg-gray-50 rounded-full w-12" />
                <div class="h-4 bg-gray-50 rounded-full w-12" />
              </div>
              <div class="h-4 bg-gray-50 rounded-full w-16" />
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
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'

const auth     = useAuthStore()
const appStore = useAppStore()
const showCreatePost = ref(false)
const selectedCategory = ref('All')

const categories = ['All', 'FOOTBALL', 'BASKETBALL', 'TENNIS', 'RUNNING', 'CYCLING', 'FITNESS', 'OTHER']

function toggleCategory(cat) {
  selectedCategory.value = cat
  const params = cat === 'All' ? {} : { sport_category: cat }
  appStore.fetchFeed(params)
}

const myAvatar = computed(() =>
  auth.user?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

onMounted(() => {
  appStore.fetchFeed()
  // Refresh feed every 30 seconds to show new comments/posts
  const interval = setInterval(() => appStore.fetchFeed(), 30000)
  onUnmounted(() => clearInterval(interval))
})
</script>
