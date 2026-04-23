<template>
  <BaseModal :modelValue="modelValue" title="Share Something" @update:modelValue="$emit('update:modelValue', $event)">
    <form @submit.prevent="submit" class="flex flex-col gap-4">
      <!-- Author row -->
      <div class="flex items-center gap-3">
        <img :src="myAvatar" class="w-10 h-10 rounded-full object-cover" />
        <div>
          <p class="font-semibold text-sm">{{ auth.user?.name }}</p>
          <span class="badge badge-primary text-xs">Public</span>
        </div>
      </div>

      <!-- Body -->
      <textarea
        v-model="form.body"
        rows="4"
        placeholder="What's happening in your sports world? 🏅"
        class="input-field resize-none"
        :class="{ error: errors.body }"
      />
      <p v-if="errors.body" class="text-red-500 text-xs -mt-2">{{ errors.body }}</p>

      <!-- Image Upload -->
      <div>
        <label class="label">Add an Image <span class="text-gray-400 font-normal">(optional)</span></label>
        <div 
          v-if="!form.image_url"
          class="border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-orange-300 hover:bg-orange-50/50 transition-all cursor-pointer group"
          @click="$refs.fileInput.click()"
        >
          <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-orange-100 group-hover:text-orange-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
          <p class="text-sm text-gray-500 font-medium">Click to upload photo</p>
          <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>
          <input ref="fileInput" type="file" class="hidden" accept="image/*" @change="handleFileUpload" />
        </div>

        <!-- Preview -->
        <div v-else class="relative group rounded-2xl overflow-hidden border border-gray-100">
          <img :src="form.image_url" class="w-full max-h-64 object-cover" />
          <button 
            @click="form.image_url = ''"
            class="absolute top-2 right-2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </div>

      <!-- Sport tag -->
      <div>
        <label class="label">Tag a Sport</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="s in sports" :key="s"
            type="button"
            @click="form.sport = form.sport === s ? '' : s"
            class="px-3 py-1 rounded-full text-sm font-medium border transition-all"
            :class="form.sport === s ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600 hover:border-orange-300'"
          >{{ s }}</button>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3 pt-2">
        <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1">Cancel</button>
        <button type="submit" class="btn-primary flex-1" :disabled="loading">
          <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          {{ loading ? 'Posting…' : 'Post' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, computed } from 'vue'
import BaseModal from './BaseModal.vue'
import { useAuthStore } from '@/stores/auth'
import { useAppStore } from '@/stores/app'
import api from '@/services/api'

defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue'])

const auth    = useAuthStore()
const appStore = useAppStore()

const loading = ref(false)
const form    = ref({ body: '', image_url: '', sport: '' })
const errors  = ref({})

const myAvatar = computed(() =>
  auth.user?.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

const sports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Running', 'Cycling', 'Padel', 'Swimming']

function validate() {
  errors.value = {}
  if (!form.value.body.trim()) errors.value.body = 'Please write something.'
  return !Object.keys(errors.value).length
}

async function handleFileUpload(e) {
  const file = e.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)
  formData.append('type', 'post')

  loading.value = true
  try {
    const res = await api.post('/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.value.image_url = res.data.url
  } catch (err) {
    errors.value.body = 'Failed to upload image.'
  } finally {
    loading.value = false
  }
}

async function submit() {
  if (!validate()) return
  loading.value = true
  try {
    const payload = {
      content: form.value.body,
      sport_category: form.value.sport ? form.value.sport.toUpperCase() : 'OTHER',
      image: form.value.image_url
    }
    await appStore.createPost(payload)
    form.value = { body: '', image_url: '', sport: '' }
    emit('update:modelValue', false)
  } catch (e) {
    errors.value.body = e?.response?.data?.message || 'Failed to post.'
  } finally {
    loading.value = false
  }
}
</script>
