<script setup>
const model = defineModel()

</script>

<template>
    <p class="bg-red-600 rounded shadow px-2 py-1 flex align-center text-gray-50 font-semibold" v-if="model" role="alert">
        <span class="flex-1">{{ model }}</span>
        <button @click="model = null" class="cursor-pointer">x</button>
    </p>
</template>