<script setup>
defineProps({
    total_leads: Number,
    last_7_days_leads: Number,
    origins_leads: Object
})

</script>

<template>
    <Head :title=" $page.component" />

 <div class="max-w-5xl mx-auto py-10 space-y-8">

        <h1 class="text-3xl font-bold text-gray-800">
            Dashboard
        </h1>

        <!-- Widgets principais -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Total Leads -->
            <div class="bg-white shadow-md rounded-2xl p-6">
                <p class="text-sm text-gray-500">Total Leads</p>
                <h2 class="text-3xl font-bold text-indigo-600">
                    {{ total_leads }}
                </h2>
            </div>

            <!-- Últimos 7 dias -->
            <div class="bg-white shadow-md rounded-2xl p-6">
                <p class="text-sm text-gray-500">Últimos 7 dias</p>
                <h2 class="text-3xl font-bold text-green-600">
                    {{ last_7_days_leads }}
                </h2>
            </div>

            <!-- Placeholder 30 dias -->
            <div class="bg-white shadow-md rounded-2xl p-6">
                <p class="text-sm text-gray-500">Últimos 30 dias</p>
                <h2 class="text-3xl font-bold text-blue-600">
                    Em breve
                </h2>
            </div>

        </div>

        <!-- Leads por origem -->
        <div class="bg-white shadow-md rounded-2xl p-6">
            <h2 class="text-xl font-semibold mb-4">
                Top 3 Origens
            </h2>

            <ul class="space-y-2">
                <li
                    v-for="(count, origin) in origins_leads"
                    :key="origin"
                    class="flex justify-between border-b pb-2"
                >
                    <span class="text-gray-600">{{ origin }}</span>
                    <span class="font-semibold text-gray-800">
                        {{ count }}
                    </span>
                </li>
            </ul>
        </div>

    </div>
</template>