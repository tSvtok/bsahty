<template>
  <BaseModal :modelValue="modelValue" title="Organize a Match " size="lg" @update:modelValue="$emit('update:modelValue', $event)">
    <form @submit.prevent="submit" class="flex flex-col gap-5">
      <!-- Title -->
      <div>
        <label class="label">Match Title <span class="text-red-400">*</span></label>
        <input v-model="form.title" type="text" placeholder='e.g. "Sunday Football 5v5"' class="input-field" :class="{ error: errors.title }" />
        <p v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</p>
      </div>

      <!-- Sport -->
      <div>
        <label class="label">Sport <span class="text-red-400">*</span></label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="s in sports" :key="s.name"
            type="button"
            @click="form.sport = s.name"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium border transition-all"
            :class="form.sport === s.name ? 'bg-orange-500 text-white border-orange-500 shadow-md' : 'border-gray-200 text-gray-600 hover:border-orange-300'"
          >
            <span>{{ s.emoji }}</span> {{ s.name }}
          </button>
        </div>
        <p v-if="errors.sport" class="text-red-500 text-xs mt-1">{{ errors.sport }}</p>
      </div>

      <!-- Date + Time -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Date <span class="text-red-400">*</span></label>
          <input v-model="form.date" type="date" class="input-field" :class="{ error: errors.date }" :min="today" />
          <p v-if="errors.date" class="text-red-500 text-xs mt-1">{{ errors.date }}</p>
        </div>
        <div>
          <label class="label">Time <span class="text-red-400">*</span></label>
          <input v-model="form.time" type="time" class="input-field" :class="{ error: errors.time }" />
          <p v-if="errors.time" class="text-red-500 text-xs mt-1">{{ errors.time }}</p>
        </div>
      </div>

      <!-- Location -->
      <div>
        <label class="label">Location <span class="text-red-400">*</span></label>
        <input v-model="form.location" type="text" placeholder="Stadium, park, court name" class="input-field" :class="{ error: errors.location }" />
        <p v-if="errors.location" class="text-red-500 text-xs mt-1">{{ errors.location }}</p>
      </div>

      <!-- Participants + Level -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Max Players</label>
          <input v-model.number="form.max_participants" type="number" min="2" max="100" placeholder="10" class="input-field" />
        </div>
        <div>
          <label class="label">Level</label>
          <select v-model="form.level" class="input-field">
            <option value="">Any level</option>
            <option>Beginner</option>
            <option>Intermediate</option>
            <option>Advanced</option>
            <option>Professional</option>
          </select>
        </div>
      </div>

      <!-- Description -->
      <div>
        <label class="label">Description <span class="text-gray-400 font-normal">(optional)</span></label>
        <textarea v-model="form.description" rows="3" placeholder="Tell players what to expect, what to bring" class="input-field resize-none" />
      </div>

      <!-- Image Upload -->
      <div>
        <label class="label">Event Cover <span class="text-gray-400 font-normal">(optional)</span></label>
        <div 
          v-if="!form.image"
          class="border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50/50 transition-all cursor-pointer group"
          @click="$refs.fileInput.click()"
        >
          <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-orange-100 group-hover:text-orange-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
          <p class="text-sm text-gray-500 font-medium">Click to upload cover photo</p>
          <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="handleFileUpload" />
        </div>

        <div v-else class="relative group rounded-2xl overflow-hidden border border-gray-100">
          <img :src="form.image" class="w-full h-40 object-cover" />
          <button 
            @click="form.image = ''"
            class="absolute top-2 right-2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <!-- Error -->
      <p v-if="errors.general" class="text-red-500 text-sm bg-red-50 rounded-xl px-4 py-2">{{ errors.general }}</p>

      <!-- Actions -->
      <div class="flex gap-3 pt-1">
        <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1">Cancel</button>
        <button type="submit" class="btn-primary flex-1" :disabled="loading">
          <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          {{ loading ? 'Creating' : 'Create Match' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref } from 'vue'
import BaseModal from './BaseModal.vue'
import { useAppStore } from '@/stores/app'
import api from '@/services/api'

defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue'])

const appStore = useAppStore()
const loading  = ref(false)
const errors   = ref({})

const today = new Date().toISOString().split('T')[0]

const form = ref({
  title: '', sport: '', date: '', time: '', location: '',
  max_participants: 10, level: '', description: '', image: '',
})

const sports = [
  { name: 'Football',   emoji: '⚽' },
  { name: 'Basketball', emoji: '🏀' },
  { name: 'Tennis',     emoji: '🎾' },
  { name: 'Volleyball', emoji: '🏐' },
  { name: 'Running',    emoji: '🏃' },
  { name: 'Cycling',    emoji: '🚲' },
  { name: 'Padel',      emoji: '🎾' },
  { name: 'Swimming',   emoji: '🏊' },
]

function validate() {
  errors.value = {}
  if (!form.value.title.trim())    errors.value.title    = 'Title is required.'
  if (!form.value.sport)           errors.value.sport    = 'Please select a sport.'
  if (!form.value.date)            errors.value.date     = 'Date is required.'
  if (!form.value.time)            errors.value.time     = 'Time is required.'
  if (!form.value.location.trim()) errors.value.location = 'Location is required.'
  return !Object.keys(errors.value).length
}

async function handleFileUpload(e) {
  const file = e.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)
  formData.append('type', 'event')

  loading.value = true
  try {
    const res = await api.post('/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.value.image = res.data.url
  } catch (err) {
    errors.value.general = 'Failed to upload image.'
  } finally {
    loading.value = false
  }
}

async function submit() {
  if (!validate()) return
  loading.value = true
  try {
    await appStore.createEvent({ ...form.value })
    form.value = { title: '', sport: '', date: '', time: '', location: '', max_participants: 10, level: '', description: '' }
    emit('update:modelValue', false)
  } catch (e) {
    errors.value.general = e?.response?.data?.message || 'Failed to create event.'
  } finally {
    loading.value = false
  }
}
</script>
