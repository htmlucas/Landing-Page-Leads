<script setup>
defineProps({
    audits: Array
})

const formatJson = (value) => {
    try {
        return JSON.stringify(JSON.parse(value), null, 2)
    } catch {
        return value
    }
}
</script>

<template>
    <Head :title="$page.component" />

    <div class="max-w-4xl mx-auto py-8 space-y-6">

        <h1 class="text-2xl font-bold mb-6">Histórico de Alterações</h1>

        <div v-if="audits.length">
            <div 
                v-for="audit in audits" 
                :key="audit.id"
                class="bg-white shadow-md rounded-xl p-6 border border-gray-200"
            >
                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-sm text-gray-500">
                            {{ audit.subject_type }}
                        </p>
                        <p class="font-semibold text-lg">
                            ID: {{ audit.subject_id }}
                        </p>
                    </div>

                    <span 
                        class="px-3 py-1 text-xs rounded-full font-semibold"
                        :class="{
                            'bg-green-100 text-green-700': audit.description === 'created',
                            'bg-yellow-100 text-yellow-700': audit.description === 'updated',
                            'bg-red-100 text-red-700': audit.description === 'deleted'
                        }"
                    >
                        {{ audit.description }}
                    </span>
                </div>

                <!-- Mudanças -->
                <div>
                    <h2 class="text-sm font-semibold text-gray-600 mb-2">
                        Alterações:
                    </h2>

                    <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto">
                        {{ formatJson(audit.properties) }}
                    </pre>
                </div>

                <!-- Footer -->
                <div class="mt-4 text-xs text-gray-400">
                    {{ audit.created_at }}
                </div>
            </div>
        </div>

        <div v-else class="text-center text-gray-500">
            Nenhuma alteração encontrada.
        </div>

    </div>
</template>