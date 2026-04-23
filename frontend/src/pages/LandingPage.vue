<template>
  <div class="page-wrapper bg-dark text-white overflow-x-hidden">
    <Navbar />

    <!-- ─── Hero ──────────────────────────────────────── -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
      <!-- Background gradient mesh -->
      <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950" />
      <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 left-1/4 w-96 h-96 bg-orange-500 rounded-full filter blur-[128px]" />
        <div class="absolute bottom-20 right-1/4 w-80 h-80 bg-orange-700 rounded-full filter blur-[128px]" />
      </div>

      <div class="relative z-10 text-center px-4 max-w-5xl mx-auto animate-fadeInUp">
        <!-- Tag -->
        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 text-sm font-medium text-orange-300 mb-6">
          <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse" />
          The Sports Community Platform
        </div>

        <!-- Headline -->
        <h1 class="text-5xl sm:text-6xl md:text-7xl font-black leading-[1.05] tracking-tight mb-6">
          Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600">Game.</span><br />
          Play Together.
        </h1>

        <p class="text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
          Discover local matches, connect with athletes, and never miss a game again.
          B-SSAHTY makes it effortless to organize and join sports events near you.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <router-link to="/register" class="btn-primary text-base !py-3.5 !px-8">
            Get Started — It's Free 🏅
          </router-link>
          <router-link to="/events" class="btn-secondary text-base !py-3.5 !px-8 !text-white !border-white/20 hover:!border-orange-400">
            Browse Events →
          </router-link>
        </div>

        <!-- Stats row -->
        <div class="flex flex-wrap justify-center gap-8 mt-16 text-center">
          <div v-for="stat in stats" :key="stat.label">
            <p class="text-3xl font-black text-white">{{ stat.value }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ stat.label }}</p>
          </div>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-gray-600 animate-bounce">
        <span class="text-xs tracking-widest uppercase">scroll</span>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </section>

    <!-- ─── Bento Features Grid ──────────────────────── -->
    <section class="py-24 px-4 bg-gray-950">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
          <p class="text-orange-500 font-semibold tracking-widest text-sm uppercase mb-3">Features</p>
          <h2 class="text-4xl md:text-5xl font-black text-white">Everything you need<br />to play more</h2>
        </div>

        <!-- Bento grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 auto-rows-[240px]">
          <!-- Large card -->
          <div class="lg:col-span-2 bg-gradient-to-br from-orange-500 to-orange-700 rounded-3xl p-8 flex flex-col justify-end overflow-hidden relative">
            <div class="absolute top-6 right-6 text-7xl opacity-30">📍</div>
            <span class="badge bg-white/20 text-white w-fit mb-3">Live Map</span>
            <h3 class="text-2xl font-bold text-white">Find Spots & Active Games Near You</h3>
            <p class="text-orange-100 mt-2 text-sm">Real-time map powered by PostGIS — discover courts, parks, and who's playing now.</p>
          </div>

          <!-- Small card -->
          <div class="bg-gray-800/60 border border-gray-700/50 rounded-3xl p-6 flex flex-col justify-between">
            <span class="text-4xl">🏟</span>
            <div>
              <h3 class="text-xl font-bold text-white">Organize Matches</h3>
              <p class="text-gray-400 text-sm mt-1">Set up events with date, time, location & skill level.</p>
            </div>
          </div>

          <!-- Small card -->
          <div class="bg-gray-800/60 border border-gray-700/50 rounded-3xl p-6 flex flex-col justify-between">
            <span class="text-4xl">💬</span>
            <div>
              <h3 class="text-xl font-bold text-white">Community Feed</h3>
              <p class="text-gray-400 text-sm mt-1">Share moments, ask questions, and follow local athletes.</p>
            </div>
          </div>

          <!-- Large card -->
          <div class="lg:col-span-2 bg-gray-800/60 border border-gray-700/50 rounded-3xl p-8 flex flex-col justify-end overflow-hidden relative">
            <div class="absolute top-6 right-6 text-7xl opacity-20">📨</div>
            <span class="badge bg-orange-500/20 text-orange-400 w-fit mb-3">Direct Messaging</span>
            <h3 class="text-2xl font-bold text-white">Chat with Teammates</h3>
            <p class="text-gray-400 mt-2 text-sm">Coordinate games, share tips, and build lasting sports connections.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ─── Sports Tags ───────────────────────────────── -->
    <section class="py-16 px-4 bg-gray-950 border-t border-gray-800/50">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-black text-white mb-10">All Your Favourite Sports</h2>
        <div class="flex flex-wrap justify-center gap-3">
          <span
            v-for="s in allSports" :key="s.name"
            class="flex items-center gap-2 px-4 py-2 rounded-full bg-gray-800 border border-gray-700/50 text-sm font-medium text-gray-300 hover:border-orange-500 hover:text-orange-400 transition-all cursor-pointer"
          >
            {{ s.emoji }} {{ s.name }}
          </span>
        </div>
      </div>
    </section>

    <!-- ─── CTA ───────────────────────────────────────── -->
    <section class="py-24 px-4 bg-gradient-to-r from-orange-600 to-orange-500 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-white rounded-full" />
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full" />
      </div>
      <div class="relative z-10 text-center max-w-2xl mx-auto">
        <h2 class="text-4xl md:text-5xl font-black text-white mb-5">Ready to Play?</h2>
        <p class="text-orange-100 text-lg mb-8">Join thousands of athletes already finding their game on B-SSAHTY.</p>
        <router-link to="/register" class="inline-flex items-center gap-2 px-10 py-4 bg-white text-orange-600 font-bold rounded-full hover:shadow-2xl hover:-translate-y-1 transition-all text-lg">
          Join for Free →
        </router-link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800/50 py-8 px-4 text-center text-gray-600 text-sm">
      © 2026 B-SSAHTY — Built for athletes, by athletes.
    </footer>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'

const stats = [
  { value: '12k+', label: 'Athletes' },
  { value: '3k+',  label: 'Events / month' },
  { value: '180+', label: 'Venues' },
  { value: '40+',  label: 'Cities' },
]

const allSports = [
  { name: 'Football',   emoji: '⚽' }, { name: 'Basketball', emoji: '🏀' },
  { name: 'Tennis',     emoji: '🎾' }, { name: 'Volleyball', emoji: '🏐' },
  { name: 'Running',    emoji: '🏃' }, { name: 'Cycling',    emoji: '🚴' },
  { name: 'Swimming',   emoji: '🏊' }, { name: 'Padel',      emoji: '🎾' },
  { name: 'Boxing',     emoji: '🥊' }, { name: 'CrossFit',   emoji: '🏋️' },
  { name: 'Yoga',       emoji: '🧘' }, { name: 'Badminton',  emoji: '🏸' },
]
</script>
