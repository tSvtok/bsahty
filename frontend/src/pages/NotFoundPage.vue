<template>
  <div class="min-h-screen bg-background flex flex-col items-center justify-center p-6 text-center">
    <div class="relative mb-8">
      <div class="text-[12rem] font-black text-gray-100 select-none">404</div>
      <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-8xl transform hover:scale-110 transition-transform duration-500 cursor-default">🚧</span>
      </div>
    </div>
    
    <h1 class="text-3xl font-black mb-2 text-gray-800">Page Not Found</h1>
    <p class="text-gray-500 max-w-md mb-8">
      Oops! The page you are looking for doesn't exist or has been moved to another stadium.
    </p>
    
    <div class="flex flex-col sm:flex-row gap-4">
      <button @click="$router.back()" class="btn-secondary px-8">Go Back</button>
      <router-link to="/feed" class="btn-primary px-8">Return Home</router-link>
    </div>
  </div>
</template>

<script setup>
// No extra logic needed
</script>
