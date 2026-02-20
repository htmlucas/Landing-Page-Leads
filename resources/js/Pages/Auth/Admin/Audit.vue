<script setup>
defineProps({
    audits: Array
})

</script>

<template>
    <Head :title=" $page.component" />

    <div class="w-2/4 mx-auto">        

        <div v-if="audits.length" class="flex items-center p-2 mb-4">
            <div v-for="audit in audits" :key="audit.id">
                <div class="">
                    <h1>Objeto: </h1>
                    <small>{{audit.subject_type }}</small> 
                </div>
                <div class="">
                    <h1>Ação: </h1>
                    <small>{{audit.description }}</small> 
                </div>
                <div class="">
                    <h1>ID do Objeto: </h1>
                    <small>{{audit.subject_id }}</small> 
                </div>
                <div class="">
                    <h1>Oque mudou: </h1>
                    <small>{{audit.properties }}</small> 
                </div>
                
            </div>
        </div>


    </div>
</template>