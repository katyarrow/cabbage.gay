<script setup>
import { ref } from 'vue';

const model = defineModel()

const props = defineProps({
    level: {
        type: String,
        default: 'danger',
    },
    contextList: {
        type: Array,
        default: null,
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
    <div class="rounded shadow px-2 py-1 flex align-center text-gray-50 font-semibold" :class="colors" v-if="model" role="alert">
        <div class="flex-1">
            {{ model }}
            <ul v-if="contextList">
                <li class="list-disc" v-for="message in contextList">{{ message }}</li>
            </ul>
        </div>
        <button @click="model = null" class="cursor-pointer">x</button>
    </div>
</template>