<script setup>
import { usePage } from '@inertiajs/vue3'

defineOptions({
  layout: null
})

const { props } = usePage()
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="transition-all duration-300 hover:scale-[1.02]">
        <div class="bg-white shadow-xl hover:shadow-2xl transition-all duration-300 ...">
        
        <div class="text-green-500 text-5xl mb-4">
            ✔
        </div>

        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
            Data Deletion Confirmed
        </h1>

        <p class="text-gray-600">
            Your request to delete your data has been successfully processed.
        </p>

        </div>
    </div>
  </div>
</template>