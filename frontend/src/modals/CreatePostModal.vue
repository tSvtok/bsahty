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
        placeholder="What's happening in your sports world? "
        class="input-field resize-none"
        :class="{ error: errors.body }"
      />
      <p v-if="errors.body" class="text-red-500 text-xs -mt-2">{{ errors.body }}</p>


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
          {{ loading ? 'Posting' : 'Post' }}
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
const form    = ref({ body: '', sport: '' })
const errors  = ref({})

const myAvatar = computed(() =>
  auth.user?.avatar_url ||
  `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.name || 'A')}&background=f97316&color=fff`
)

const sports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Running', 'Cycling', 'Padel', 'Swimming']

function validate() {
  errors.value = {}
  if (!form.value.body.trim()) errors.value.body = 'Please write something.'
  return !Object.keys(errors.value).length
}


async function submit() {
  if (!validate()) return
  loading.value = true
  try {
    const payload = {
      content: form.value.body,
      sport_category: form.value.sport ? form.value.sport.toUpperCase() : 'OTHER'
    }
    await appStore.createPost(payload)
    form.value = { body: '', sport: '' }
    emit('update:modelValue', false)
  } catch (e) {
    errors.value.body = e?.response?.data?.message || 'Failed to post.'
  } finally {
    loading.value = false
  }
}
</script>
