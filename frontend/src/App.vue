<template>
  <router-view v-slot="{ Component }">
    <transition name="fade" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
  <Toast />
</template>

<script setup>
import Toast from '@/components/Toast.vue'
import { useToastStore } from '@/stores/toast'
import { useNotifications } from '@/composables/useNotifications'
import { watch, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const toastStore = useToastStore()
const auth = useAuthStore()
const { setup, teardown } = useNotifications()

onMounted(() => {
  if (auth.isLoggedIn) setup()
})

watch(() => auth.isLoggedIn, (val) => {
  if (val) setup()
  else teardown()
})
</script>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.22s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
