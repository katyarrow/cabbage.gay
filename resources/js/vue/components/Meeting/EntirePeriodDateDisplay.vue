<script setup>
const props = defineProps({
    day: Object,
    showDifferentTimezoneInfo: Boolean,
});
</script>

<template>
<div v-if="props.showDifferentTimezoneInfo">
    <p class="absolute top-0 right-1 text-xs opacity-50">
        <span class="sr-only">Year </span>
        {{ day.userDateStart.format('YYYY') }}
        {{ day.userStartAndEndOnDifferentYears ? ' - ' + day.userDateEnd.format('YYYY') : ''}}
    </p>
    <p class="text-center font-semibold tracking-wide">
        {{ day.userDateStart.format(day.userStartAndEndOnDifferentDays ? 'ddd Do MMM ha' : 'ddd Do MMM') }}
        <div v-if="day.userStartAndEndOnDifferentDays">
            <div class="leading-0.5">-</div>
            {{ day.userDateEnd.format('ddd Do MMM ha') }}
        </div>
        <div v-else class="text-gray-600 text-sm">
            {{ day.userDateStart.format('h a') }}
            -
            {{ day.userDateEnd.format('h a') }}
        </div>
    </p>
</div>
<div v-else>
    <p class="absolute top-0 right-1 text-xs opacity-50">
        <span class="sr-only">Year </span>
        {{ day.date.format('YYYY') }}
    </p>
    <p class="text-center font-semibold tracking-wide">
        {{ day.date.format('ddd Do MMMM') }}
    </p>
</div>
</template>