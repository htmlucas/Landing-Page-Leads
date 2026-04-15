<script setup>
import { usePage } from '@inertiajs/vue3'

defineOptions({
  layout: null
})

const { props } = usePage()

const reasonMessages = {
  invalid: 'Invalid link',
  expired: 'This link has expired',
  used: 'This link has already been used'
}

const message = reasonMessages[props.reason] ?? 'Something went wrong'
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">

    <div class="transition-all duration-300 hover:scale-[1.02] p-4">
      <div class="bg-white shadow-xl hover:shadow-2xl transition-all duration-300 ...">
        
        <div class="text-red-500 text-5xl mb-4">
          ✖
        </div>

        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
          {{ message }}
        </h1>

        <p class="text-gray-600">
          Please try again or request a new deletion link.
        </p>

      </div>
    </div>
  </div>
</template>