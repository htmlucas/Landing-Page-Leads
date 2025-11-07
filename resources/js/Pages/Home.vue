<script setup>
    import demo from '@/Layouts/Demo.vue';

    defineOptions({
        layout: demo,
    });
</script>

<template>
    <Head :title=" $page.component" />
    <div>
        <h1>{{ $page.props.auth.user}}</h1>
    </div>
</template>