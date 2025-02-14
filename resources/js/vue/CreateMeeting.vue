<script setup>

import VInput from './components/VInput.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import { onMounted, ref } from 'vue';

onMounted(() => {

})

const generatingStatus = ref(null);
const error = ref(null);

const submit = () => {
    generatingStatus.value = 'Generating Crypt Keys';
}

</script>

<template>
    <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Create a Meeting</h1>
    <form class="flex flex-col max-w-lg mx-auto mb-10" @submit.prevent="submit">
        <VAlert v-model="error"></VAlert>
        <div class="my-5">
            <VLabel class="text-left sr-only" for="name">Meeting Name</VLabel>
            <VInput type="text" name="name" id="name" placeholder="Meeting Name"></VInput>
        </div>

        <fieldset class="mt-10">
            <VLegend>What date range should the meeting be in?</VLegend>
            <div class="grid md:grid-cols-2 md:gap-10">
                <div class="my-5">
                    <VLabel class="text-left" for="start_date">Start Date</VLabel>
                    <VInput type="date" name="start_date" id="start_date"></VInput>
                </div>

                <div class="my-5">
                    <VLabel class="text-left" for="end_date">End Date</VLabel>
                    <VInput type="date" name="end_date" id="end_date"></VInput>
                </div>
            </div>
        </fieldset>

        <fieldset class="mt-10">
            <VLegend>What times do you want the meeting between?</VLegend>
            <div class="grid md:grid-cols-2 md:gap-10">
                <div class="my-5">
                    <VLabel class="text-left" for="start_time">Start Time</VLabel>
                    <VInput type="time" name="start_time" id="start_time"></VInput>
                </div>

                <div class="my-5">
                    <VLabel class="text-left" for="end_time">End Time</VLabel>
                    <VInput type="time" name="end_time" id="end_time"></VInput>
                </div>
            </div>
        </fieldset>

        <fieldset class="mt-10">
            <VLegend>Extra info</VLegend>
            <div class="my-5">
                <VLabel class="text-left" for="destroy_date">Destroy Date</VLabel>
                <VInput type="date" name="destroy_date" id="destroy_date"></VInput>
                <small>If left blank will default to 6 months from now</small>
            </div>
        </fieldset>

        <div class="text-right mt-10">
            <VButton type="submit" size="lg">Create!</VButton>
        </div>
    </form>
    <div v-if="generatingStatus" class="fixed inset-0 bg-gray-100 flex flex-col items-center justify-center font-semibold text-2xl">
        <span class="animate-pulse">{{ generatingStatus }}</span>
        🔒
    </div>
</template>