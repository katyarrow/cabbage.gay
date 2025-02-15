<script setup>
    import VError from './VError.vue';

    defineProps({
        modelValue: {
            type: String,
            required: false,
        }
    })
    const emit = defineEmits(['update:modelValue'])

    const handleInput = ($event) => {
       emit('update:modelValue', $event.target.value)
    }

    const error = defineModel('error');
</script>

<template>
    <select v-bind="$attrs" class="
        border-transparent
        border-2
        border-b-green-700
        focus:border-b-green-900
        w-full
        font-medium
        text-lg
        py-3
        focus:px-3
        focus:outline-0
        focus:bg-green-200
        transition-all
        rounded-tl
        rounded-tr
        disabled:bg-gray-100
    "
    :class="[error ? 'border-b-red-600' : '']"
    :value="modelValue"
    @input="handleInput($event)"><slot></slot></select>
    <VError v-model="error"></VError>
</template>