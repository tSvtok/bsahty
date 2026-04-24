<template>
  <div class="page-wrapper bg-background text-gray-900 overflow-x-hidden min-h-screen">
    <Navbar />
    
    <div :class="{ 'main-layout': auth.isLoggedIn }">
      <!-- Sidebar only for logged in athletes -->
      <AppSidebar v-if="auth.isLoggedIn" />

      <main class="flex-1 overflow-x-hidden">
        <!-- ─── Hero ──────────────────────────────────────── -->
        <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden" :class="auth.isLoggedIn ? 'pt-6' : 'pt-20'">
          <!-- Background gradient mesh (Light Version) -->
          <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 via-white to-orange-50/30" />
          <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-1/4 w-96 h-96 bg-orange-200 rounded-full filter blur-[128px]" />
            <div class="absolute bottom-20 right-1/4 w-80 h-80 bg-orange-300 rounded-full filter blur-[128px]" />
          </div>

          <div class="relative z-10 text-center px-4 max-w-5xl mx-auto animate-fadeInUp">
            <!-- Tag -->
            <div class="inline-flex items-center gap-2 bg-orange-100/80 backdrop-blur-md rounded-full px-4 py-2 text-sm font-bold text-orange-600 mb-6 border border-orange-200/50 shadow-sm">
              <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse" />
              THE SPORTS COMMUNITY PLATFORM
            </div>

            <!-- Headline -->
            <h1 class="text-5xl sm:text-6xl md:text-8xl font-black leading-[0.95] tracking-tighter mb-8 text-gray-950">
              Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-600">Game.</span><br />
              Play Together.
            </h1>

            <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
              Discover local matches, connect with athletes, and never miss a game again.
              B-SSAHTY makes it effortless to organize and join sports events near you.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
              <router-link v-if="!auth.isLoggedIn" to="/register" class="btn-primary text-lg !py-4 !px-10 shadow-xl hover:shadow-orange-500/20">
                Get Started — It's Free 
              </router-link>
              <router-link to="/events" class="btn-primary text-lg !py-4 !px-10 shadow-xl hover:shadow-orange-500/20">
                Find A Match
              </router-link>
              <router-link to="/map" class="btn-secondary text-lg !py-4 !px-10 bg-white shadow-sm border-gray-200">
                Explore Map →
              </router-link>
            </div>

            <!-- Stats row -->
            <div class="flex flex-wrap justify-center gap-10 mt-20">
              <div v-for="stat in stats" :key="stat.label" class="text-center group">
                <p class="text-4xl font-black text-gray-900 group-hover:text-orange-500 transition-colors">{{ stat.value }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">{{ stat.label }}</p>
              </div>
            </div>
          </div>

          <!-- Scroll indicator -->
          <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-gray-400 animate-bounce">
            <span class="text-[10px] font-black tracking-widest uppercase">scroll</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </section>

        <!-- ─── Bento Features Grid ──────────────────────── -->
        <section class="py-24 px-4 bg-white border-y border-gray-100">
          <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
              <p class="text-orange-500 font-black tracking-[0.2em] text-xs uppercase mb-3">EXPERIENCE</p>
              <h2 class="text-4xl md:text-6xl font-black text-gray-950 tracking-tighter">Everything you need<br />to play more</h2>
            </div>

            <!-- Bento grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-[280px]">
              <!-- Large card: Map -->
              <div class="lg:col-span-2 rounded-[2.5rem] p-10 flex flex-col justify-end overflow-hidden relative group shadow-lg hover:shadow-2xl transition-all duration-500">
                <img src="https://images.unsplash.com/photo-1526676037777-05a232554f77?w=1200" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                <div class="relative z-10">
                  <span class="inline-block bg-orange-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-4">Live Map</span>
                  <h3 class="text-3xl font-black text-white">Find Spots Near You</h3>
                  <p class="text-orange-50/80 mt-2 text-sm max-w-md font-medium">Real-time map powered by PostGIS — discover courts, parks, and who's playing now.</p>
                </div>
              </div>

              <!-- Small card: Organize -->
              <div class="bg-gray-50 border border-gray-100 rounded-[2.5rem] p-8 flex flex-col justify-between group hover:bg-orange-50 transition-all duration-500">
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                  <h3 class="text-xl font-black text-gray-900">Organize Matches</h3>
                  <p class="text-gray-500 text-sm mt-1 font-medium">Set up events with date, time, location & skill level.</p>
                </div>
              </div>

              <!-- Small card: Feed -->
              <div class="bg-gray-50 border border-gray-100 rounded-[2.5rem] p-8 flex flex-col justify-between group hover:bg-orange-50 transition-all duration-500">
                <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                  <h3 class="text-xl font-black text-gray-900">Community Feed</h3>
                  <p class="text-gray-500 text-sm mt-1 font-medium">Share moments, ask questions, and follow local athletes.</p>
                </div>
              </div>

              <!-- Large card: Chat -->
              <div class="lg:col-span-2 rounded-[2.5rem] p-10 flex flex-col justify-end overflow-hidden relative group shadow-lg hover:shadow-2xl transition-all duration-500">
                <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?w=1200" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-gray-900/40 to-transparent" />
                <div class="relative z-10">
                  <span class="inline-block bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-4">Direct Messaging</span>
                  <h3 class="text-3xl font-black text-white">Chat with Teammates</h3>
                  <p class="text-blue-50/80 mt-2 text-sm max-w-md font-medium">Coordinate games, share tips, and build lasting sports connections.</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ─── Sports Tags ───────────────────────────────── -->
        <section class="py-24 px-4 bg-background">
          <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-black text-gray-950 mb-12 tracking-tighter">Choose Your Arena</h2>
            <div class="flex flex-wrap justify-center gap-3">
              <span
                v-for="s in allSports" :key="s.name"
                class="flex items-center gap-2 px-6 py-3 rounded-full bg-white border border-gray-100 text-sm font-bold text-gray-700 hover:border-orange-500 hover:text-orange-500 hover:shadow-md transition-all cursor-pointer"
              >
                {{ s.name }}
              </span>
            </div>
          </div>
        </section>

        <!-- ─── CTA ───────────────────────────────────────── -->
        <section class="py-28 px-4 relative overflow-hidden" v-if="!auth.isLoggedIn">
          <div class="max-w-6xl mx-auto bg-gradient-primary rounded-[3rem] py-20 px-8 relative overflow-hidden shadow-2xl">
            <div class="absolute inset-0 opacity-10">
              <div class="absolute -top-20 -right-20 w-96 h-96 bg-white rounded-full" />
              <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-white rounded-full" />
            </div>
            <div class="relative z-10 text-center max-w-2xl mx-auto">
              <h2 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tighter">Ready to Play?</h2>
              <p class="text-orange-100 text-lg mb-10 font-medium">Join thousands of athletes already finding their game on B-SSAHTY.</p>
              <router-link to="/register" class="inline-flex items-center gap-3 px-12 py-5 bg-white text-orange-600 font-black rounded-full hover:shadow-2xl hover:-translate-y-1 transition-all text-xl">
                Join for Free — Now
              </router-link>
            </div>
          </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-12 px-4 text-center">
          <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-center gap-2 font-bold text-xl tracking-tight mb-6">
              <span class="w-8 h-8 rounded-xl bg-gradient-primary flex items-center justify-center text-white text-sm font-black shadow-md">B</span>
              <span class="text-gray-900 uppercase">B-SSAHTY</span>
            </div>
            <p class="text-gray-400 text-sm font-medium">
              © 2026 B-SSAHTY — Built for athletes, by athletes.
            </p>
          </div>
        </footer>
      </main>
    </div>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const stats = [
  { value: '12k+', label: 'Athletes' },
  { value: '3k+',  label: 'Events / month' },
  { value: '180+', label: 'Venues' },
  { value: '40+',  label: 'Cities' },
]

const allSports = [
  { name: 'Football' }, { name: 'Basketball' },
  { name: 'Tennis' }, { name: 'Volleyball' },
  { name: 'Running' }, { name: 'Cycling' },
  { name: 'Swimming' }, { name: 'Padel' },
  { name: 'Boxing' }, { name: 'CrossFit' },
  { name: 'Yoga' }, { name: 'Badminton' },
]
</script>
