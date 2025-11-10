<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Pages/Components/TextInput.vue';

const form = useForm({
    email: '',
    password: '',
    remember: null,
});

const submitForm = () => {
    // Handle form submission logic here
    form.post(route('login'),{
        onError: () => {
            form.reset('password', 'remember');
        },
    });
    console.log('Form submitted:', form);
};
</script>

<template>
    <Head :title="Login" />

    <div class="w-2/4 mx-auto">        
        <form @submit.prevent="submitForm"
            class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-8 space-y-6">
            <h1 class="block text-gray-700 font-medium mb-2">Login to your account</h1>
            <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
            <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" />

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <label for="remember">Remember Me?</label>
                    <input type="checkbox" id="remember" v-model="form.remember">
                </div>

                <p class="text-slate-600">
                    Need an account?
                    <a :href="route('register')" class="text-blue-600 hover:text-blue-700 font-medium">Register</a>
                </p>
            </div>
            <div class="text-center">                
                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                    Login
                </button>
            </div>
        </form>

    </div>
</template>