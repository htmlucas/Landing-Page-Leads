<script setup>
import  { usePage } from '@inertiajs/vue3';


const { flash } = usePage().props;
</script>


<template>
    <Head title="Dashboard" />
    <div class="">
        <div v-if="flash.greet" class="flex items-center p-2 mb-4 text-sm text-green-800">
            <span class="font-medium">{{ flash.greet }}</span>
        </div>
        <h1>
            Welcome to the Dashboard! {{ $page.props.auth.user.name }}
        </h1>
    </div>
</template>