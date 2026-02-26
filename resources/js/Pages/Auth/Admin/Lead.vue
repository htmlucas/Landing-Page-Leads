<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    leads: Object,
    filters: Object
})

/*
|--------------------------------------------------------------------------
| Filtros
|--------------------------------------------------------------------------
*/
const filters = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    origin: props.filters.origin || '',
    email: props.filters.email || '',
    page_size: props.filters.page_size || 10,
})

function applyFilters() {
    router.get('/admin/leads', filters, {
        preserveState: true,
        replace: true,
    })
}

function resetFilters() {
    router.get('/admin/leads')
}

/*
|--------------------------------------------------------------------------
| Seleção para export
|--------------------------------------------------------------------------
*/
const selected = ref([])

function toggleAll(event) {
    if (event.target.checked) {
        selected.value = props.leads.data.map(l => l.id)
    } else {
        selected.value = []
    }
}

async function exportSelected() {
    const response = await axios.post('/admin/leads/export', {
        ids: selected.value
    }, {
        responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'leads.xlsx')
    document.body.appendChild(link)
    link.click()
}
</script>

<template>
    <Head :title=" $page.component" />

    <div class="max-w-7xl mx-auto py-10 space-y-8">

        <h1 class="text-3xl font-bold">Leads</h1>

        <div v-if="$page.props.flash.export_error" class="flex items-center p-2 mb-4 text-sm text-red-800">
            <span class="font-medium">{{ $page.props.flash.export_error }}</span>
        </div>

        <div v-if="$page.props.flash.message" class="flex items-center p-2 mb-4 text-sm text-red-800">
            <span class="font-medium">{{ $page.props.flash.message }}</span>
        </div>

        <!-- FILTROS -->
        <div class="bg-white p-6 rounded-2xl shadow space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                <input
                    type="date"
                    v-model="filters.date_from"
                    class="border rounded p-2"
                />

                <input
                    type="date"
                    v-model="filters.date_to"
                    class="border rounded p-2"
                />

                <input
                    type="text"
                    placeholder="Buscar email"
                    v-model="filters.email"
                    class="border rounded p-2"
                />

                <select v-model="filters.origin" class="border rounded p-2">
                    <option value="">Todas origins</option>
                    <option value="google_ads">Google Ads</option>
                    <option value="landing">Landing</option>
                    <option value="youtube_ads">Youtube Ads</option>
                </select>

                <select v-model="filters.page_size" class="border rounded p-2">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>

            </div>

            <div class="flex gap-3">
                <button
                    @click="applyFilters"
                    class="bg-indigo-600 text-white px-4 py-2 rounded"
                >
                    Aplicar
                </button>

                <button
                    @click="resetFilters"
                    class="bg-gray-300 px-4 py-2 rounded"
                >
                    Resetar
                </button>
            </div>

        </div>

        <!-- TABELA -->
        <div class="bg-white rounded-2xl shadow overflow-x-auto">

            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">
                            <input type="checkbox" @change="toggleAll" />
                        </th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Origins</th>
                        <th class="p-3 text-left">Data</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="lead in leads.data"
                        :key="lead.id"
                        class="border-t"
                    >
                        <td class="p-3">
                            <input
                                type="checkbox"
                                v-model="selected"
                                :value="lead.id"
                            />
                        </td>

                        <td class="p-3">{{ lead.email }}</td>

                        <td class="p-3">
                            <span
                                v-for="origin in lead.origins"
                                :key="origin"
                                class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded mr-1"
                            >
                                {{ origin }}
                            </span>
                        </td>

                        <td class="p-3">
                            {{ new Date(lead.created_at).toLocaleDateString() }}
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <!-- PAGINAÇÃO -->
        <div class="flex justify-between items-center">

            <button
                @click="exportSelected"
                :disabled="!selected.length"
                class="bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50"
            >
                Exportar Selecionados
            </button>

            <div class="flex gap-1">
                <button
                    v-for="link in leads.links"
                    :key="link.label"
                    v-html="link.label"
                    :disabled="!link.url"
                    @click="router.visit(link.url)"
                    class="px-3 py-1 border rounded"
                    :class="{ 'bg-indigo-600 text-white': link.active }"
                />
            </div>

        </div>

    </div>
</template>