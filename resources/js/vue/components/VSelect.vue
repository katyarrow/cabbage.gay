<script setup>
import { ref } from 'vue';
import VError from './VError.vue';

const props = defineProps({
    modelValue: {type: String, required: false},
    size: {type: String, default: 'lg'},
})
const emit = defineEmits(['update:modelValue'])

const handleInput = ($event) => {
    emit('update:modelValue', $event.target.value)
}

const error = defineModel('error');

const sizeClasses = ref('');
switch(props.size) {
    case 'sm': sizeClasses.value = 'py-1 focus:px-1 text-sm'; break;
    case 'md': sizeClasses.value = 'py-2 focus:px-2 text-base'; break;
    case 'lg': sizeClasses.value = 'py-3 focus:px-3 text-lg'; break;
}
</script>

<template>
    <select v-bind="$attrs" class="
        border-transparent
        border-2
        border-b-green-700
        focus:border-b-green-900
        w-full
        font-medium
        focus:outline-0
        focus:bg-green-200
        transition-all
        rounded-tl
        rounded-tr
        disabled:bg-gray-100
    "
    :class="[error ? 'border-b-red-600' : '', sizeClasses]"
    :value="modelValue"
    @input="handleInput($event)"><slot></slot></select>
    <VError v-model="error"></VError>
</template>