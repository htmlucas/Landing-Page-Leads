<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Pages/Components/TextInput.vue';

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    avatar: null,
    preview: null
});

const change = (e) => {
    const file = e.target.files[0];
    form.avatar = file;
    form.preview = URL.createObjectURL(file);
}

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
            <!-- Upload avatar -->
             <div class="grid place-items-center">
                <div class="relative w-28 h-28 rounded-full overflow-hidden border border-slate-300">
                    <label for="avatar" class="absolute inset-0 grid content-end cursor-pointer">
                        <span class="bg-white/70 pb-2 text-center">Avatar</span>
                    </label>
                    <input type="file" @input="change" id="avatar" hidden />

                    <img class="object-cover w-28 h-28" :src="form.preview ?? 'storage/avatars/default.png'" alt="">
                </div>
                <p class="error" mt-2>{{  form.errors.avatar }}</p>
             </div>

            <TextInput name="Name" v-model="form.name" :message="form.errors.name" />
            <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
            <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" />
            <TextInput name="Confirm Password" type="password" v-model="form.password_confirmation" :message="form.errors.password_confirmation" />

            <div class="pt-4 text-center">
                <p class="text-slate-600 mb-3">
                    Already a user?
                    <a :href="route('login')" class="text-blue-600 hover:text-blue-700 font-medium">Login</a>
                </p>
                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Register
                </button>
            </div>
        </form>

    </div>
</template>