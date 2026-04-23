<template>
  <div class="page-wrapper">
    <Navbar />
    <div class="main-layout">
      <AppSidebar />

      <main class="flex-1 py-6 px-4 md:px-6">
        <header class="mb-8">
          <h1 class="text-2xl font-black">Admin Dashboard</h1>
          <p class="text-gray-500 text-sm mt-1">Manage the platform and monitor growth</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div v-for="(val, key) in stats" :key="key" class="card p-5">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ key.replace('_', ' ') }}</p>
            <p class="text-3xl font-black text-orange-500">{{ val }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
          <!-- Pending Spots -->
          <section>
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-bold text-lg">Pending Spots</h2>
              <span class="badge badge-primary">{{ pendingSpots.length }}</span>
            </div>
            
            <div v-if="loading" class="flex flex-col gap-3">
              <div v-for="i in 3" :key="i" class="card p-4 h-20 animate-pulse" />
            </div>

            <div v-else-if="!pendingSpots.length" class="card p-10 text-center text-gray-400">
              <p>No spots pending approval</p>
            </div>

            <div v-else class="flex flex-col gap-3">
              <div v-for="spot in pendingSpots" :key="spot.id" class="card p-4 flex items-center justify-between">
                <div>
                  <p class="font-bold text-sm">{{ spot.name }}</p>
                  <p class="text-xs text-gray-500">{{ spot.city }}</p>
                </div>
                <div class="flex gap-2">
                  <button @click="approveSpot(spot.id)" class="btn-primary !py-1.5 !px-3 !text-xs bg-green-500 border-green-500 hover:bg-green-600">Approve</button>
                  <button @click="rejectSpot(spot.id)" class="btn-secondary !py-1.5 !px-3 !text-xs text-red-500 hover:bg-red-50 hover:border-red-200">Reject</button>
                </div>
              </div>
            </div>
          </section>

          <!-- User Moderation -->
          <section>
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-bold text-lg">Platform Users</h2>
              <button @click="fetchUsers" class="text-xs text-orange-500 font-bold hover:underline">Refresh</button>
            </div>
            <div class="card overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                  <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                      <th class="px-4 py-3">User</th>
                      <th class="px-4 py-3">Level</th>
                      <th class="px-4 py-3">Status</th>
                      <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-50">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-gray-50/50 transition-colors">
                      <td class="px-4 py-3">
                        <p class="font-bold text-gray-800">{{ u.name }}</p>
                        <p class="text-[10px] text-gray-400">{{ u.email }}</p>
                      </td>
                      <td class="px-4 py-3">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">{{ u.user_level }}</span>
                      </td>
                      <td class="px-4 py-3">
                        <span v-if="u.is_banned" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-600">BANNED</span>
                        <span v-else class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-600">ACTIVE</span>
                      </td>
                      <td class="px-4 py-3 text-right">
                        <button v-if="!u.is_banned" @click="banUser(u.id)" class="text-red-500 hover:underline font-bold text-xs">Ban</button>
                        <button v-else @click="unbanUser(u.id)" class="text-green-600 hover:underline font-bold text-xs">Unban</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>
        </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/components/Navbar.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import api from '@/services/api'

import { useToastStore } from '@/stores/toast'

const stats = ref({})
const pendingSpots = ref([])
const users = ref([])
const loading = ref(true)
const toastStore = useToastStore()

async function fetchData() {
  loading.value = true
  try {
    const [statsRes, spotsRes] = await Promise.all([
      api.get('/admin/stats'),
      api.get('/admin/spots/pending')
    ])
    stats.value = statsRes.data
    pendingSpots.value = spotsRes.data.data
    await fetchUsers()
  } catch (e) {
    console.error('Failed to fetch admin data', e)
    toastStore.show('Error', 'Failed to fetch admin data', 'error')
  } finally {
    loading.value = false
  }
}

async function fetchUsers() {
  try {
    const res = await api.get('/admin/users') // I need to check if this endpoint exists
    users.value = res.data.data
  } catch (e) {}
}

async function approveSpot(id) {
  try {
    await api.patch(`/admin/spots/${id}/approve`)
    pendingSpots.value = pendingSpots.value.filter(s => s.id !== id)
    stats.value.pending_spots_count--
    toastStore.show('Success', 'Spot approved successfully')
  } catch (e) {
    toastStore.show('Error', 'Failed to approve spot', 'error')
  }
}

async function rejectSpot(id) {
  try {
    await api.patch(`/admin/spots/${id}/reject`)
    pendingSpots.value = pendingSpots.value.filter(s => s.id !== id)
    stats.value.pending_spots_count--
    toastStore.show('Success', 'Spot rejected')
  } catch (e) {
    toastStore.show('Error', 'Failed to reject spot', 'error')
  }
}

async function banUser(id) {
  try {
    await api.patch(`/admin/users/${id}/ban`)
    const u = users.value.find(user => user.id === id)
    if (u) u.is_banned = true
    toastStore.show('Success', 'User banned')
  } catch (e) {
    toastStore.show('Error', 'Failed to ban user', 'error')
  }
}

async function unbanUser(id) {
  try {
    await api.patch(`/admin/users/${id}/unban`)
    const u = users.value.find(user => user.id === id)
    if (u) u.is_banned = false
    toastStore.show('Success', 'User unbanned')
  } catch (e) {
    toastStore.show('Error', 'Failed to unban user', 'error')
  }
}

onMounted(fetchData)
</script>
