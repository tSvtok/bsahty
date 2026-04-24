<template>
  <div class="card-flat p-5 flex flex-col gap-4">
    <div class="flex items-center justify-between">
      <h3 class="font-bold text-base"> Trending Near You</h3>
      <span class="text-xs text-gray-400">This week</span>
    </div>

    <div class="flex flex-col gap-3">
      <div
        v-for="(sport, i) in trending"
        :key="sport.name"
        class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-50 transition-colors cursor-pointer"
      >
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-xl shrink-0">
          {{ sport.emoji }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-1">
            <p class="font-semibold text-sm">{{ sport.name }}</p>
            <span class="text-xs font-bold text-orange-500">{{ sport.events }} events</span>
          </div>
          <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-orange-400 to-orange-600 rounded-full"
              :style="{ width: `${sport.popularity}%` }"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Quick join CTA -->
    <router-link to="/events" class="btn-primary w-full justify-center">
      Browse All Events →
    </router-link>
  </div>
</template>

<script setup>
// Mock trending data — replace with API call in production
const trending = [
  { name: 'Football',    emoji: '', events: 24, popularity: 92 },
  { name: 'Basketball',  emoji: '', events: 18, popularity: 74 },
  { name: 'Tennis',      emoji: '', events: 15, popularity: 60 },
  { name: 'Volleyball',  emoji: '', events: 11, popularity: 45 },
  { name: 'Running',     emoji: '', events: 8,  popularity: 32 },
]
</script>
