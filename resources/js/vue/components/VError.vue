<script setup>
const model = defineModel()

</script>

<template>
    <span class="text-red-600" role="alert" v-if="model">{{ model }}</span>
</template>