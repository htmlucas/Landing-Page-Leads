<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    lead: Object,
    availableOrigins: Object
})

const form = useForm({
    name: props.lead.name,
    email: props.lead.email,
    phone: props.lead.phone,
    origins: props.lead.origins || []
})

function submit() {
    form.put(route('admin.leads.update', props.lead.id), {
        onSuccess: () => {
            // Redirecionar para a lista de leads após a atualização
        }
    })
}
</script>

<template>
    <div class="max-w-xl mx-auto py-10">

        <h1 class="text-2xl font-bold mb-6">
            Editar Lead
        </h1>

        <form @submit.prevent="submit" class="space-y-4">

            <div>
                <label>Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="border rounded p-2 w-full"
                />
                <div v-if="form.errors.email" class="text-red-500 text-sm">
                    {{ form.errors.email }}
                </div>
            </div>

            <div>
                <label>Nome</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="border rounded p-2 w-full"
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm">
                    {{ form.errors.name }}
                </div>
            </div>

            <div>
                <label>Telefone</label>
                <input
                    v-model="form.phone"
                    type="tel"
                    class="border rounded p-2 w-full"
                />
                <div v-if="form.errors.phone" class="text-red-500 text-sm">
                    {{ form.errors.phone }}
                </div>
            </div>

            <div class="">
                <label class="block font-semibold mb-2">
                    Origins
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">

                    <label
                        v-for="(label, value) in availableOrigins"
                        :key="value"
                        class="flex items-center gap-2 p-2 border rounded cursor-pointer hover:bg-gray-50"
                    >
                        <input
                            type="checkbox"
                            :value="value"
                            v-model="form.origins"
                            class="w-4 h-4"
                        />

                        <span>
                            {{ label }}
                        </span>
                    </label>

                </div>

                <div v-if="form.errors.origins" class="text-red-500 text-sm mt-1">
                    {{ form.errors.origins }}
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="bg-indigo-600 text-white px-4 py-2 rounded"
            >
                Salvar
            </button>

        </form>

    </div>
</template>