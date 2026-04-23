<template>
  <BaseModal :modelValue="modelValue" title="Edit Profile" @update:modelValue="$emit('update:modelValue', $event)">
    <form @submit.prevent="submit" class="flex flex-col gap-5">
      <!-- Avatar -->
      <div class="flex flex-col items-center gap-3">
        <div class="relative group">
          <img :src="previewAvatar" class="w-24 h-24 rounded-full object-cover ring-4 ring-orange-400/30 transition-opacity group-hover:opacity-80" />
          <label class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
            <div class="bg-black/40 p-2 rounded-full text-white">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <input type="file" class="hidden" accept="image/*" @change="handleFileUpload" />
          </label>
        </div>
        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Click image to upload</p>
      </div>

      <!-- Name -->
      <div>
        <label class="label">Full Name <span class="text-red-400">*</span></label>
        <input v-model="form.name" type="text" placeholder="Your name" class="input-field" :class="{ error: errors.name }" />
        <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
      </div>

      <!-- Bio -->
      <div>
        <label class="label">Bio</label>
        <textarea v-model="form.bio" rows="3" placeholder="Tell others about yourself…" class="input-field resize-none" />
      </div>

      <!-- City -->
      <div>
        <label class="label">City</label>
        <input v-model="form.city" type="text" placeholder="Paris, London..." class="input-field" />
      </div>

      <!-- Favorite sports -->
      <div>
        <label class="label">Favorite Sports</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="s in allSports" :key="s"
            type="button"
            @click="toggleSport(s)"
            class="px-3 py-1 rounded-full text-sm font-medium border transition-all"
            :class="form.sports.includes(s) ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600 hover:border-orange-300'"
          >{{ s }}</button>
        </div>
      </div>

      <p v-if="errors.general" class="text-red-500 text-sm bg-red-50 rounded-xl px-4 py-2">{{ errors.general }}</p>

      <div class="flex gap-3 pt-1">
        <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1">Cancel</button>
        <button type="submit" class="btn-primary flex-1" :disabled="loading">
          {{ loading ? 'Saving…' : 'Save Changes' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import BaseModal from './BaseModal.vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

defineProps({ modelValue: Boolean })
const emit = defineEmits(['update:modelValue'])

const auth    = useAuthStore()
const loading = ref(false)
const errors  = ref({})

const form = ref({
  name:   auth.user?.name   || '',
  bio:    auth.user?.bio    || '',
  avatar: auth.user?.avatar || '',
  city:   auth.user?.city   || '',
  sports: auth.user?.sports || [],
})

const previewAvatar = computed(() =>
  form.value.avatar ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(form.value.name || 'A')}&background=f97316&color=fff&size=160`
)

const allSports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Running', 'Cycling', 'Padel', 'Swimming', 'Gym', 'CrossFit']

function toggleSport(s) {
  const idx = form.value.sports.indexOf(s)
  if (idx === -1) form.value.sports.push(s)
  else form.value.sports.splice(idx, 1)
}

async function handleFileUpload(e) {
  const file = e.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)
  formData.append('type', 'avatar')

  loading.value = true
  try {
    const res = await api.post('/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.value.avatar = res.data.url
  } catch (err) {
    errors.value.general = 'Failed to upload image.'
  } finally {
    loading.value = false
  }
}

async function submit() {
  errors.value = {}
  if (!form.value.name.trim()) { errors.value.name = 'Name is required.'; return }
  loading.value = true
  try {
    const res = await api.put('/me', form.value)
    auth.user = res.data
    emit('update:modelValue', false)
  } catch (e) {
    errors.value.general = e?.response?.data?.message || 'Failed to save changes.'
  } finally {
    loading.value = false
  }
}
</script>
