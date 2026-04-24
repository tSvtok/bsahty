<template>
  <teleport to="body">
    <transition name="modal-backdrop">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4"
        @click.self="emit('update:modelValue', false)"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />

        <!-- Panel -->
        <transition name="modal-content">
          <div
            v-if="modelValue"
            class="relative bg-white rounded-[2rem] sm:rounded-3xl shadow-2xl w-full overflow-hidden mx-auto"
            :class="sizeClass"
          >
            <!-- Header -->
            <div class="flex items-center justify-between px-4 sm:px-6 pt-5 sm:pt-6 pb-4 border-b border-gray-100">
              <h2 class="text-lg sm:text-xl font-bold">{{ title }}</h2>
              <button
                @click="emit('update:modelValue', false)"
                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Body -->
            <div class="px-4 sm:px-6 pb-6 pt-5 overflow-y-auto max-h-[85vh]">
              <slot />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  title: { type: String, default: '' },
  size:  { type: String, default: 'md' },
})
const emit = defineEmits(['update:modelValue'])

const sizeClass = computed(() => ({
  sm: 'max-w-sm',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
}[props.size] || 'max-w-lg'))
</script>
