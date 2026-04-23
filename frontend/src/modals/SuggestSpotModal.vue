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

      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-xl">{{ error }}</div>

      <div class="flex gap-3 mt-2">
        <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1">Cancel</button>
        <button type="submit" class="btn-primary flex-1" :disabled="loading">
          {{ loading ? 'Submitting...' : 'Suggest Spot 📍' }}
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
  longitude: props.initialLng || 3.05 
})

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    form.latitude = props.initialLat || 36.75
    form.longitude = props.initialLng || 3.05
    form.name = ''
  }
})

async function submit() {
  loading.value = true
  error.value   = null
  try {
    await api.post('/spots', {
      ...form,
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
