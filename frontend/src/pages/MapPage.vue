<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 flex flex-col gap-0 overflow-hidden">
        <!-- Top bar -->
        <div class="px-4 md:px-6 py-4 bg-white border-b border-gray-100 flex items-center gap-3">
          <h1 class="text-lg font-bold">Sport Spots & Active Games</h1>
          <div class="flex-1" />
          <div class="flex items-center gap-2">
            <button @click="showSuggest = true" class="btn-secondary !py-1.5 !px-4 !text-sm flex items-center gap-1.5 border-orange-200 text-orange-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Suggest Spot
            </button>
            <button @click="locateMe" class="btn-secondary !py-1.5 !px-4 !text-sm flex items-center gap-1.5">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Near Me
            </button>
          </div>
        </div>

        <div class="flex flex-col lg:flex-row flex-1 overflow-hidden relative">
          <!-- Map container with dashboard frame -->
          <div class="relative flex-1 p-4 md:p-6 bg-gray-50/50 flex flex-col min-h-[450px] lg:min-h-0">
            <div class="flex-1 relative rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-white ring-1 ring-black/5 z-0 bg-white">
              <div ref="mapEl" class="absolute inset-0" />
              
              <!-- Mobile Toggle List Button (Inside Frame) -->
              <button 
                @click="showMobileList = !showMobileList"
                class="lg:hidden absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000] px-6 py-3 bg-orange-500 text-white rounded-full font-bold shadow-xl flex items-center gap-2 active:scale-95 transition-all"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                {{ showMobileList ? 'Hide List' : 'View Nearby Spots' }}
              </button>
            </div>
          </div>

          <!-- Spots panel -->
          <div 
            class="w-full lg:w-96 shrink-0 flex flex-col border-t lg:border-t-0 lg:border-l border-gray-100 bg-white transition-all duration-500 ease-in-out z-10 lg:rounded-none rounded-t-[2.5rem] shadow-2xl lg:shadow-none"
            :class="[
              showMobileList ? 'h-[60vh] lg:h-full opacity-100 translate-y-0' : 'h-0 lg:h-full opacity-0 lg:opacity-100 translate-y-10 lg:translate-y-0 pointer-events-none lg:pointer-events-auto'
            ]"
          >
            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-20">
              <h2 class="font-bold text-gray-900">Nearby Spots</h2>
              <span class="badge badge-primary">{{ displayedSpots.length }}</span>
            </div>

            <div class="flex-1 overflow-y-auto">
              <div v-if="appStore.spotsLoading" class="p-4 flex flex-col gap-6">
                <div v-for="i in 5" :key="i" class="flex gap-3">
                  <SkeletonLoader width="48px" height="48px" class="!rounded-2xl shrink-0" />
                  <div class="flex-1">
                    <SkeletonLoader width="80%" height="0.875rem" class="mb-2.5" />
                    <SkeletonLoader width="50%" height="0.625rem" />
                  </div>
                </div>
              </div>

              <div v-else class="flex flex-col">
                <div
                  v-for="spot in displayedSpots" :key="spot.id"
                  @click="handleSpotClick(spot)"
                  class="flex items-start gap-3 p-4 hover:bg-orange-50/50 cursor-pointer border-b border-gray-50 transition-colors group"
                >
                  <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                    {{ spotEmoji(spot.type) }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-bold text-sm text-gray-900 truncate">{{ spot.name }}</p>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ spot.address || spot.type }}</p>
                    <div class="flex items-center gap-2 mt-2">
                      <span class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500" />
                        <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Active</span>
                      </span>
                      <span v-if="getDistance(spot.lat || spot.latitude || spot.coordinates?.lat, spot.lng || spot.longitude || spot.coordinates?.lng)" class="text-[10px] text-gray-400 font-medium px-1.5 py-0.5 bg-gray-100 rounded">
                        {{ getDistance(spot.lat || spot.latitude || spot.coordinates?.lat, spot.lng || spot.longitude || spot.coordinates?.lng) }} km
                      </span>
                    </div>
                  </div>
                </div>

                <div v-if="!appStore.spots.length" class="p-12 text-center">
                  <div class="text-5xl mb-4 opacity-20"></div>
                  <h3 class="font-bold text-gray-900 mb-1">No spots nearby</h3>
                  <p class="text-sm text-gray-500">Try zooming out or moving the map to find active spots.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <SuggestSpotModal 
        v-model="showSuggest" 
        :initial-lat="newSpotCoords?.lat" 
        :initial-lng="newSpotCoords?.lng" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import SuggestSpotModal from '@/modals/SuggestSpotModal.vue'
import SkeletonLoader from '@/components/SkeletonLoader.vue'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const mapEl    = ref(null)
const showSuggest = ref(false)
const showMobileList = ref(false)
const newSpotCoords = ref(null)
const displayedSpots = computed(() => appStore.spots.slice(0, 10))
let leafletMap = null

watch(showMobileList, () => {
  setTimeout(() => leafletMap?.invalidateSize(), 350)
})

function handleSpotClick(spot) {
  flyTo(spot)
  showMobileList.value = false
}

const userCoords = ref(null)

function getDistance(spotLat, spotLng) {
  if (!userCoords.value || !spotLat || !spotLng) return null
  const R = 6371 // Radius of the earth in km
  const dLat = (spotLat - userCoords.value.lat) * Math.PI / 180
  const dLon = (spotLng - userCoords.value.lng) * Math.PI / 180
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(userCoords.value.lat * Math.PI / 180) * Math.cos(spotLat * Math.PI / 180) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
  const d = R * c
  return d.toFixed(1)
}

const spotEmoji = (type) => ({
  gym: '', court: '', stadium: '', pool: '',
  park: '', track: '', fitness: '', default: ''
}[type?.toLowerCase()] || '')

onMounted(async () => {
  await appStore.fetchSpots()

  const L = (await import('leaflet')).default
  const { nextTick } = await import('vue')

  await nextTick()

  leafletMap = L.map(mapEl.value, {
    zoomControl: true,
    tap: false // Recommended for mobile
  }).setView([36.7538, 3.0588], 12)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(leafletMap)

  // Force size recalculation
  setTimeout(() => {
    leafletMap?.invalidateSize()
  }, 250)

  // Markers layer group
  const markersLayer = L.layerGroup().addTo(leafletMap)

  // Watch for spots changes to update markers
  watch(() => appStore.spots, (newSpots) => {
    if (!leafletMap || !markersLayer) return
    markersLayer.clearLayers()
    
    const orange = L.divIcon({
      html: `<div style="width:32px;height:32px;background:#f97316;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(249,115,22,.5);display:flex;align-items:center;justify-content:center;font-size:14px;cursor:pointer;"></div>`,
      className: 'custom-spot-icon', 
      iconSize: [32, 32], 
      iconAnchor: [16, 16],
    })

    newSpots.forEach(spot => {
      const lat = spot.lat || spot.latitude || spot.coordinates?.lat
      const lng = spot.lng || spot.longitude || spot.coordinates?.lng
      
      if (lat && lng) {
        L.marker([lat, lng], { icon: orange })
          .addTo(markersLayer)
          .bindPopup(`
            <div class="p-1">
              <p class="font-bold text-gray-900">${spot.name}</p>
              <p class="text-xs text-gray-500 mt-0.5">${spot.address || spot.type || ''}</p>
            </div>
          `)
      }
    })
  }, { immediate: true })

  // Handle map click to suggest spot
  leafletMap.on('click', (e) => {
    const { lat, lng } = e.latlng
    newSpotCoords.value = { lat, lng }
    showSuggest.value = true
  })
})

async function locateMe() {
  if (!navigator.geolocation) {
    alert("Geolocation is not supported by your browser.")
    return
  }
  
  navigator.geolocation.getCurrentPosition(
    async ({ coords }) => {
      const { latitude: lat, longitude: lng } = coords
      userCoords.value = { lat, lng }
      console.log('Position found:', lat, lng)
      if (leafletMap) {
        leafletMap.setView([lat, lng], 15)
        // Add a blue marker for user position
        const L = (await import('leaflet')).default
        L.circleMarker([lat, lng], {
          radius: 8,
          fillColor: "#3b82f6",
          color: "#fff",
          weight: 3,
          fillOpacity: 1
        }).addTo(leafletMap).bindPopup("You are here").openPopup()
      }
      await appStore.fetchNearby(lat, lng)
    },
    (err) => {
      console.error('Geolocation error:', err)
      let msg = "Could not get your location."
      if (err.code === 1) msg = "Location access was denied. Please check your browser settings."
      if (err.code === 2) msg = "Location unavailable. Your device might not have a GPS signal."
      if (err.code === 3) msg = "Location request timed out. Please try again."
      alert(msg)
    },
    { enableHighAccuracy: false, timeout: 20000, maximumAge: Infinity }
  )
}

function flyTo(spot) {
  if (spot.lat && spot.lng) leafletMap?.flyTo([spot.lat, spot.lng], 16)
}

onUnmounted(() => { leafletMap?.remove(); leafletMap = null })
</script>
