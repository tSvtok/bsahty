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

        <div class="flex flex-col lg:flex-row flex-1 overflow-hidden" style="min-height: calc(100vh - 9.5rem);">
          <!-- Map -->
          <div ref="mapEl" class="flex-1 z-0 min-h-[400px] lg:min-h-0" />

          <!-- Spots panel -->
          <div class="w-full lg:w-80 shrink-0 flex flex-col border-t lg:border-t-0 lg:border-l border-gray-100 bg-white overflow-y-auto max-h-[50vh] lg:max-h-none">
            <div class="p-4 border-b border-gray-100">
              <h2 class="font-semibold text-sm text-gray-600">Nearby Spots ({{ appStore.spots.length }})</h2>
            </div>

            <div v-if="appStore.spotsLoading" class="p-4 flex flex-col gap-3">
              <div v-for="i in 4" :key="i" class="animate-pulse flex gap-3">
                <div class="w-12 h-12 bg-gray-100 rounded-xl shrink-0" />
                <div class="flex-1"><div class="h-3 bg-gray-100 rounded-full mb-2" /><div class="h-2 bg-gray-100 rounded-full w-2/3" /></div>
              </div>
            </div>

            <div v-else class="flex flex-col">
              <div
                v-for="spot in appStore.spots" :key="spot.id"
                @click="flyTo(spot)"
                class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition-colors"
              >
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl shrink-0">
                  {{ spotEmoji(spot.type) }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-sm truncate">{{ spot.name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ spot.address || spot.type }}</p>
                  <div class="flex items-center gap-1 mt-1">
                    <span class="w-2 h-2 rounded-full" :class="spot.active ? 'bg-green-400' : 'bg-gray-300'" />
                    <span class="text-xs text-gray-400">{{ spot.active ? 'Active now' : 'Inactive' }}</span>
                  </div>
                </div>
              </div>

              <div v-if="!appStore.spots.length" class="p-8 text-center text-gray-400">
                <div class="text-3xl mb-2">📍</div>
                <p class="text-sm">No spots found. Try clicking "Near Me".</p>
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
import { ref, onMounted, onUnmounted, watch } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import SuggestSpotModal from '@/modals/SuggestSpotModal.vue'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()
const mapEl    = ref(null)
const showSuggest = ref(false)
const newSpotCoords = ref(null)
let leafletMap = null

const spotEmoji = (type) => ({
  gym: '🏋️', court: '🏀', stadium: '🏟', pool: '🏊',
  park: '🌳', track: '🏃', default: '📍'
}[type?.toLowerCase()] || '📍')

onMounted(async () => {
  await appStore.fetchSpots()

  const L = (await import('leaflet')).default

  leafletMap = L.map(mapEl.value).setView([36.7538, 3.0588], 12)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(leafletMap)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(leafletMap)

  // Markers layer group
  const markersLayer = L.layerGroup().addTo(leafletMap)

  // Watch for spots changes to update markers
  watch(() => appStore.spots, (newSpots) => {
    markersLayer.clearLayers()
    const orange = L.divIcon({
      html: `<div style="width:32px;height:32px;background:#f97316;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(249,115,22,.5);display:flex;align-items:center;justify-content:center;font-size:14px;">📍</div>`,
      className: '', iconSize: [32, 32], iconAnchor: [16, 16],
    })

    newSpots.forEach(spot => {
      if (spot.lat && spot.lng) {
        L.marker([spot.lat, spot.lng], { icon: orange })
          .addTo(markersLayer)
          .bindPopup(`<b>${spot.name}</b><br>${spot.address || spot.type}`)
      }
    })
  }, { immediate: true })

  // Handle map click to suggest spot

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
      alert("Could not get your location. Please ensure location services are enabled.")
    },
    { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 }
  )
}

function flyTo(spot) {
  if (spot.lat && spot.lng) leafletMap?.flyTo([spot.lat, spot.lng], 16)
}

onUnmounted(() => { leafletMap?.remove(); leafletMap = null })
</script>
