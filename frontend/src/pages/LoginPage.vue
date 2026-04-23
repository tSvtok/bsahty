<template>
  <div class="min-h-screen flex bg-background">
    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 flex-col items-center justify-center relative overflow-hidden p-12">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-orange-500 rounded-full filter blur-[100px]" />
        <div class="absolute bottom-1/4 right-1/4 w-56 h-56 bg-orange-700 rounded-full filter blur-[100px]" />
      </div>
      <div class="relative z-10 text-center">
        <router-link to="/" class="flex items-center justify-center gap-3 mb-10">
          <span class="w-12 h-12 rounded-2xl bg-gradient-primary flex items-center justify-center text-white text-xl font-black shadow-xl">B</span>
          <span class="text-2xl font-black text-white tracking-tight">B-SSAHTY</span>
        </router-link>
        <h2 class="text-3xl font-black text-white leading-snug mb-4">Welcome back,<br />Athlete. 🏅</h2>
        <p class="text-gray-400">Your teammates are waiting for you.<br />Jump back into the game.</p>
        <div class="flex flex-wrap justify-center gap-2 mt-8">
          <span v-for="s in sports" :key="s" class="px-3 py-1.5 rounded-full bg-gray-800/60 border border-gray-700 text-sm text-gray-400">{{ s }}</span>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-sm">
        <!-- Mobile logo -->
        <router-link to="/" class="flex items-center gap-2 mb-8 lg:hidden">
          <span class="w-9 h-9 rounded-xl bg-gradient-primary flex items-center justify-center text-white font-black shadow-md">B</span>
          <span class="text-xl font-black">B-SSAHTY</span>
        </router-link>

        <h1 class="text-2xl font-black mb-1">Sign in</h1>
        <p class="text-gray-500 text-sm mb-6">Don't have an account? <router-link to="/register" class="text-orange-500 font-semibold hover:underline">Register</router-link></p>

        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <!-- Email -->
          <div>
            <label class="label">Email</label>
            <input v-model="form.email" type="email" placeholder="you@example.com" class="input-field" :class="{ error: errors.email }" autocomplete="email" />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
          </div>

          <!-- Password -->
          <div>
            <label class="label">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPwd ? 'text' : 'password'"
                placeholder="••••••••"
                class="input-field !pr-10"
                :class="{ error: errors.password }"
                autocomplete="current-password"
              />
              <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg v-if="!showPwd" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
          </div>

          <!-- Server error -->
          <div v-if="auth.error" class="bg-red-50 text-red-600 text-sm rounded-xl px-4 py-2.5">{{ auth.error }}</div>

          <button type="submit" class="btn-primary w-full justify-center mt-1" :disabled="auth.loading">
            <svg v-if="auth.loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ auth.loading ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()
const showPwd = ref(false)
const form   = reactive({ email: '', password: '' })
const errors = reactive({ email: '', password: '' })

const sports = ['⚽ Football', '🏀 Basketball', '🎾 Tennis', '🏐 Volleyball', '🏃 Running', '🚴 Cycling']

function validate() {
  errors.email = errors.password = ''
  if (!form.email)    errors.email    = 'Email is required.'
  if (!form.password) errors.password = 'Password is required.'
  return !errors.email && !errors.password
}

async function submit() {
  if (!validate()) return
  const ok = await auth.login({ email: form.email, password: form.password })
  if (ok) router.push('/feed')
}
</script>
