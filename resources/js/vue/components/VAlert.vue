<script setup>
import { ref } from 'vue';

const model = defineModel()

const props = defineProps({
    level: {
        type: String,
        default: 'danger',
    }
});

const colors = ref(null);

if(props.level == 'danger') {
    colors.value = 'bg-red-600 text-gray-50';
} else if(props.level == 'warning') {
    colors.value = 'bg-yellow-300 text-gray-800';
}

</script>

<template>
    <p class="rounded shadow px-2 py-1 flex align-center text-gray-50 font-semibold" :class="colors" v-if="model" role="alert">
        <span class="flex-1">{{ model }}</span>
        <button @click="model = null" class="cursor-pointer">x</button>
    </p>
</template>