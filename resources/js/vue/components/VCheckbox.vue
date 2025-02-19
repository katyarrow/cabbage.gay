<script setup>
    import VError from './VError.vue';

    defineProps({
        modelValue: {
            type: Boolean,
            required: false,
        }
    })
    const emit = defineEmits(['update:modelValue'])

    const handleInput = ($event) => {
       emit('update:modelValue', !!$event.target.checked)
    }

    const error = defineModel('error');
</script>

<template>
    <input type="checkbox" v-bind="$attrs" class="
        accent-green-700
        transition-colors
    "
    :class="[error ? 'border-b-red-600' : '']"
    :checked="modelValue"
    @change="handleInput($event)">
    <VError v-model="error"></VError>
</template>