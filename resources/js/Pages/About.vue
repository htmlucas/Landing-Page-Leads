<script setup>
    defineOptions({
        user: String
    });
</script>

<template>
    <Head title="About" />
    <div>
        <h1>About page {{ user }}</h1>
    </div>
</template>