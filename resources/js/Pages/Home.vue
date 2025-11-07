<script setup>
    import demo from '@/Layouts/Demo.vue';

    defineOptions({
        layout: demo,
    });
</script>

<template>
    <Head :title=" $page.component" />
    <Link class="mt-[600px] block" href="/" preserve-scroll> Refresh</Link>
</template>