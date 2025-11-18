<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Pages/Components/TextInput.vue';
import { Text } from 'vue';

const props = defineProps({
    lead_origin: String,
})

const form = useForm({
    email: null,
    consent: false,
    origin: props.lead_origin,
});

const submitForm = () => {
    
    form.post(route('subscribe'),{
        onError: () => {
            
        },
    });
    console.log('Form submitted:', form);
};

</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-gray-100 p-8">
        <div v-if="$page.props.flash.greet" class="flex items-center p-2 mb-4 text-sm text-green-800">
            <span class="font-medium">{{ $page.props.flash.greet }}</span>
        </div>
        <h1 class="text-4xl font-bold mb-4">Campaign Page</h1>
        <p class="text-lg text-gray-700">Welcome to the Campaign page!</p>
        <div class="w-2/4 mx-auto mt-8">
            <form 
                @submit.prevent="submitForm"
                class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-8 space-y-6"
            >
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
                <div class="flex items-center gap-2">
                    <label for="consent">Consent?</label>
                    <input type="checkbox" id="consent" v-model="form.consent">
                </div>
                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                    Send
                </button>
            </form>
            
        </div>
        
    </div>
</template>