<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Pages/Components/TextInput.vue';
import { Text } from 'vue';

const props = defineProps({
    lead_origin: String,
})

const form = useForm({
    email: null,
    consent: null,
    hp: '',
    recaptcha_token: '',
});

const submitForm = () => {
    grecaptcha.ready(() => {
        grecaptcha.execute(import.meta.env.VITE_RECAPTCHA_SITE_KEY, { action: "subscribe" })
            .then((token) => {
                form.recaptcha_token = token;
                    form.post(route('leads.deletion.request'),{
                        onSuccess: () => {
                            form.reset('email', 'consent');
                        },
                    });
            });
    });



};

</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-gray-100 p-8">
        <div v-if="$page.props.flash.message" class="flex items-center p-2 mb-4 text-sm text-green-800">
            <span class="font-medium">{{ $page.props.flash.message }}</span>
        </div>

        
        <h1 class="text-4xl font-bold mb-4">Delete Page</h1>
        <p class="text-lg text-gray-700">If you want to delete your data, send us an email!</p>
        <div class="w-2/4 mx-auto mt-8">
            <form 
                @submit.prevent="submitForm"
                class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-8 space-y-6"
            >

            <!-- HoneyPot (evitar spam) -->
                <input 
                    type="text"
                    name="hp"
                    v-model="form.hp"
                    style="display:none"
                    autocomplete="off"
                />


                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
                
                <div class="flex items-center gap-2">
                    <div>
                        <label for="consent">Consent?</label>
                        <input type="checkbox" id="consent" v-model="form.consent">
                        <small class="text-red-600" v-if="form.errors.consent"> {{ form.errors.consent }} </small>
                    </div>
                </div>
                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                    Send
                </button>
            </form>
        </div>
        
    </div>
</template>