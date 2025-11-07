<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submitForm = () => {
    // Handle form submission logic here
    form.post(route('register'),{
        onError: () => {
            form.reset('password','password_confirmation');
        },
    });
    console.log('Form submitted:', form);
};
</script>

<template>
    <Head :title=" $page.component" />

    <div class="w-2/4 mx-auto">        
        <form @submit.prevent="submitForm"
            class="max-w-md mx-auto bg-white shadow-lg rounded-xl p-8 space-y-6">
            <h1 class="block text-gray-700 font-medium mb-2">Form</h1>
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" v-model="form.name"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <small> {{ form.errors.name }} </small>
            </div>

            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" v-model="form.email"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <small> {{ form.errors.email }} </small>
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" v-model="form.password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <small> {{ form.errors.password }} </small>
            </div>

            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" v-model="form.password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <small> {{ form.errors.password_confirmation }} </small>
            </div>

            <div class="pt-4 text-center">
                <p class="text-slate-600 mb-3">
                    Already a user?
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Login</a>
                </p>
                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Register
                </button>
            </div>
        </form>

    </div>
</template>