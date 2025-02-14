<script setup>
import { ref } from 'vue';

const props = defineProps({
    href: {type: String, default: null},
    size: {type: String, default: 'md'},
    color: {type: String, default: 'success'},
});


const sizeClasses = ref('');
switch(props.size) {
    case 'sm': sizeClasses.value = 'py-0 px-2 text-sm'; break;
    case 'md': sizeClasses.value = 'py-1 px-3 text-base'; break;
    case 'lg': sizeClasses.value = 'py-2 px-5 text-lg'; break;
}



const colorClasses = ref('');
switch(props.color) {
    case 'success': colorClasses.value = 'bg-green-700 text-gray-50 hover:bg-green-900'; break;
    case 'danger': colorClasses.value = 'bg-red-700 text-gray-50 hover:bg-red-900'; break;
    case 'warning': colorClasses.value = 'bg-amber-700 text-gray-50 hover:bg-amber-900'; break;
    case 'secondary': colorClasses.value = 'bg-green-400 text-gray-50 hover:bg-slate-600'; break;
}

const classes = [
    `rounded font-bold uppercase tracking-widest cursor-pointer transition-colors shadow`,
    colorClasses.value,
    sizeClasses.value,
].join(' ');


</script>

<template>
    <a v-if="href" :href="href" :class="classes"><slot></slot></a>
    <button v-else :class="classes"><slot></slot></button>
</template>