<template>
  <div class="min-h-screen flex bg-background">
    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 flex-col items-center justify-center relative overflow-hidden p-12">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-1/4 right-1/4 w-72 h-72 bg-orange-500 rounded-full filter blur-[100px]" />
        <div class="absolute bottom-1/3 left-1/4 w-56 h-56 bg-orange-700 rounded-full filter blur-[100px]" />
      </div>
      <div class="relative z-10 text-center">
        <router-link to="/" class="flex items-center justify-center gap-3 mb-10">
          <span class="w-12 h-12 rounded-2xl bg-gradient-primary flex items-center justify-center text-white text-xl font-black shadow-xl">B</span>
          <span class="text-2xl font-black text-white tracking-tight">B-SSAHTY</span>
        </router-link>
        <h2 class="text-3xl font-black text-white leading-snug mb-4">Join the community.<br />Play more. 🏅</h2>
        <p class="text-gray-400 text-sm leading-relaxed">Connect with local athletes,<br />discover games, and never train alone.</p>
        <!-- Animated avatars row -->
        <div class="flex justify-center -space-x-3 mt-8">
          <img v-for="n in 5" :key="n" :src="`https://i.pravatar.cc/64?img=${n+10}`" class="w-10 h-10 rounded-full ring-2 ring-gray-900" />
          <span class="w-10 h-10 rounded-full ring-2 ring-gray-900 bg-orange-500 flex items-center justify-center text-xs font-bold text-white">+1k</span>
        </div>
        <p class="text-gray-500 text-xs mt-3">Join 12,000+ athletes already playing</p>
      </div>
    </div>

    <!-- Form -->
    <div class="flex-1 flex items-center justify-center p-6 overflow-y-auto">
      <div class="w-full max-w-sm py-6">
        <!-- Mobile logo -->
        <router-link to="/" class="flex items-center gap-2 mb-8 lg:hidden">
          <span class="w-9 h-9 rounded-xl bg-gradient-primary flex items-center justify-center text-white font-black shadow-md">B</span>
          <span class="text-xl font-black">B-SSAHTY</span>
        </router-link>

        <h1 class="text-2xl font-black mb-1">Create account</h1>
        <p class="text-gray-500 text-sm mb-6">Already have an account? <router-link to="/login" class="text-orange-500 font-semibold hover:underline">Sign in</router-link></p>

        <form @submit.prevent="submit" class="flex flex-col gap-4">
          <!-- Name -->
          <div>
            <label class="label">Full Name <span class="text-red-400">*</span></label>
            <input v-model="form.name" type="text" placeholder="John Doe" class="input-field" :class="{ error: errors.name }" autocomplete="name" />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label class="label">Email <span class="text-red-400">*</span></label>
            <input v-model="form.email" type="email" placeholder="you@example.com" class="input-field" :class="{ error: errors.email }" autocomplete="email" />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
          </div>

          <!-- City -->
          <div>
            <label class="label">City</label>
            <input v-model="form.city" type="text" placeholder="Paris, London..." class="input-field" />
          </div>

          <!-- Password -->
          <div>
            <label class="label">Password <span class="text-red-400">*</span></label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPwd ? 'text' : 'password'"
                placeholder="At least 8 characters"
                class="input-field !pr-10"
                :class="{ error: errors.password }"
                autocomplete="new-password"
              />
              <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg v-if="!showPwd" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>
            <!-- Strength indicator -->
            <div class="flex gap-1 mt-2">
              <div v-for="i in 4" :key="i" class="h-1 flex-1 rounded-full transition-colors" :class="strengthColor(i)" />
            </div>
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
          </div>

          <!-- Confirm password -->
          <div>
            <label class="label">Confirm Password <span class="text-red-400">*</span></label>
            <input
              v-model="form.password_confirmation"
              type="password"
              placeholder="Repeat password"
              class="input-field"
              :class="{ error: errors.password_confirmation }"
            />
            <p v-if="errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ errors.password_confirmation }}</p>
          </div>

          <!-- Favourite sports -->
          <div>
            <label class="label">Favourite Sports <span class="text-gray-400 font-normal">(pick 1+)</span></label>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="s in allSports" :key="s"
                type="button"
                @click="toggleSport(s)"
                class="px-3 py-1 rounded-full text-xs font-semibold border transition-all"
                :class="form.sports.includes(s) ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-200 text-gray-600 hover:border-orange-300'"
              >{{ s }}</button>
            </div>
          </div>

          <!-- Server error -->
          <div v-if="auth.error" class="bg-red-50 text-red-600 text-sm rounded-xl px-4 py-2.5">{{ auth.error }}</div>

          <button type="submit" class="btn-primary w-full justify-center mt-1" :disabled="auth.loading">
            <svg v-if="auth.loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ auth.loading ? 'Creating account…' : 'Create Account 🏅' }}
          </button>

          <p class="text-xs text-gray-400 text-center">By registering you agree to our Terms of Service and Privacy Policy.</p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()
const showPwd = ref(false)

const form   = reactive({ name: '', email: '', password: '', password_confirmation: '', city: '', sports: [] })
const errors = reactive({ name: '', email: '', password: '', password_confirmation: '' })

const allSports = ['Football', 'Basketball', 'Tennis', 'Volleyball', 'Running', 'Cycling', 'Padel', 'Swimming', 'Gym', 'Yoga']

function toggleSport(s) {
  const i = form.sports.indexOf(s)
  if (i === -1) form.sports.push(s)
  else form.sports.splice(i, 1)
}

const passwordStrength = computed(() => {
  const p = form.password
  let score = 0
  if (p.length >= 8)  score++
  if (/[A-Z]/.test(p)) score++
  if (/[0-9]/.test(p)) score++
  if (/[^A-Za-z0-9]/.test(p)) score++
  return score
})

function strengthColor(level) {
  if (passwordStrength.value < level) return 'bg-gray-100'
  if (passwordStrength.value <= 1) return 'bg-red-400'
  if (passwordStrength.value <= 2) return 'bg-yellow-400'
  if (passwordStrength.value <= 3) return 'bg-blue-400'
  return 'bg-green-400'
}

function validate() {
  errors.name = errors.email = errors.password = errors.password_confirmation = ''
  if (!form.name.trim())     errors.name = 'Name is required.'
  if (!form.email)           errors.email = 'Email is required.'
  if (form.password.length < 8) errors.password = 'Password must be at least 8 characters.'
  if (form.password !== form.password_confirmation) errors.password_confirmation = 'Passwords do not match.'
  return !errors.name && !errors.email && !errors.password && !errors.password_confirmation
}

async function submit() {
  if (!validate()) return
  const ok = await auth.register({ ...form })
  if (ok) router.push('/feed')
}
</script>
