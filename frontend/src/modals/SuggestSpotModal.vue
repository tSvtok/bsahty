<template>
  <BaseModal :modelValue="modelValue" title="Suggest a New Spot" @update:modelValue="$emit('update:modelValue', $event)">
    <form @submit.prevent="submit" class="flex flex-col gap-4">
      <p class="text-sm text-gray-500">Know a great place to play? Suggest it to the community. It will be reviewed by our team.</p>
      
      <div>
        <label class="label">Spot Name</label>
        <input v-model="form.name" type="text" placeholder="e.g. Central Park Court" class="input-field" required />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Latitude</label>
          <input v-model.number="form.latitude" type="number" step="any" class="input-field" required />
        </div>
        <div>
          <label class="label">Longitude</label>
          <input v-model.number="form.longitude" type="number" step="any" class="input-field" required />
        </div>
      </div>

      <p class="text-[10px] text-gray-400 italic">* You can usually find these by right-clicking on a map.</p>
      
      <!-- Image Upload -->
      <div>
        <label class="label">Add an Image <span class="text-gray-400 font-normal">(optional)</span></label>
        <div 
          v-if="!form.image"
          class="border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50/50 transition-all cursor-pointer group"
          @click="$refs.fileInput.click()"
        >
          <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-orange-100 group-hover:text-orange-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
          <p class="text-sm text-gray-500 font-medium">Click to upload photo</p>
          <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="handleFileUpload" />
        </div>

        <div v-else class="relative group rounded-2xl overflow-hidden border border-gray-100">
          <img :src="form.image" class="w-full h-32 object-cover" />
          <button 
            @click="form.image = ''; form.image_path = ''"
            class="absolute top-2 right-2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-xl">{{ error }}</div>

      <div class="flex gap-3 mt-2">
        <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1">Cancel</button>
        <button type="submit" class="btn-primary flex-1" :disabled="loading">
          {{ loading ? 'Submitting...' : 'Suggest Spot ' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import BaseModal from './BaseModal.vue'
import api from '@/services/api'
import { useAppStore } from '@/stores/app'

const props = defineProps({ 
  modelValue: Boolean,
  initialLat: Number,
  initialLng: Number
})
const emit  = defineEmits(['update:modelValue'])
const appStore = useAppStore()

const loading = ref(false)
const error   = ref(null)
const form    = reactive({ 
  name: '', 
  latitude: props.initialLat || 36.75, 
  longitude: props.initialLng || 3.05,
  image: '',
  image_path: ''
})

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    form.latitude = props.initialLat || 36.75
    form.longitude = props.initialLng || 3.05
    form.name = ''
    form.image = ''
    form.image_path = ''
  }
})

async function handleFileUpload(e) {
  const file = e.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)
  formData.append('type', 'spot')

  loading.value = true
  try {
    const res = await api.post('/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.image = res.data.url
    form.image_path = res.data.path
  } catch (err) {
    error.value = 'Failed to upload image.'
  } finally {
    loading.value = false
  }
}

async function submit() {
  loading.value = true
  error.value   = null
  try {
    await api.post('/spots', {
      ...form,
      image: form.image_path,
      status: 'PENDING'
    })
    emit('update:modelValue', false)
    // Optional: show a success toast
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to suggest spot.'
  } finally {
    loading.value = false
  }
}
</script>
