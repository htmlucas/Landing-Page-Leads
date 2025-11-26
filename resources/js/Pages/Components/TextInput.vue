<script setup>
const model = defineModel({
    type: null,
    required: true
})
const props = defineProps({
    name: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'text'
    },
    message: String,
});

</script>

<template>
    <div>
        <label for="name" class="block text-gray-700 font-medium mb-2">{{ name }}</label>
        <input 
            :type="type" 
            id="name" 
            :placeholder="name" 
            name="name" 
            v-model="model" 
            :class="message ? '!border-red-500' : 'border-gray-300'"
            class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
        <small class="text-red-600" v-if="message"> {{ message }} </small>
    </div>
</template>