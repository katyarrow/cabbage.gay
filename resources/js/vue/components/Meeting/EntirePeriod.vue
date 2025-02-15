<script setup>

import VInput from '../VInput.vue';
import VLabel from '../VLabel.vue';
import VButton from '../VButton.vue';
import VAlert from '../VAlert.vue';
import moment from 'moment';
import { onMounted, ref, watch } from 'vue';
import { VueFinalModal } from 'vue-final-modal';

const props = defineProps({
    meeting: Object,
    mode: String,
    attendees: Array,
});

const emit = defineEmits(['submit']);

const generalError = ref(null);
const days = ref([]);
const availability = ref({
    name: null,
    dates: {},
});

const generateDays = () => {
    const startDate = moment(props.meeting.start_date, 'YYYY-MM-DD');
    const endDate = moment(props.meeting.end_date, 'YYYY-MM-DD');
    const current = startDate.clone();

    if(!startDate.isValid() || !endDate.isValid()) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }
    if(!startDate.isSameOrBefore(endDate)) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }
    while(current <= endDate) {
        days.value.push({
            date: current.clone(),
            showYes: false,
            showMaybe: false,
            showNo: false,
        });
        current.add(1, 'day');
    }
}

const generateFreshAvailability = () => {
    let newAvailability = {};
    days.value.forEach(day => {
        newAvailability[day.date.format('YYYY-MM-DD')] = 'no';
    });
    availability.value.dates = newAvailability;
    availability.value.name = null;
}

generateDays();

watch(() => props.mode, () => {
    if(props.mode == 'add') {
        generateFreshAvailability();
    }
})

const submit = () => {
    emit('submit', JSON.stringify(availability.value));
}

const valueCountOnDate = (date, value) => {
    return props.attendees.filter(a => a.dates[date] == value).length;
}

const valueOnDateArray = (date, value) => {
    return props.attendees.filter(a => a.dates[date] == value);
}

</script>

<template>
    <VAlert v-model="generalError" level="warning"></VAlert>
    <component :is="props.mode == 'show' ? 'div' : 'form'" @submit.prevent="submit">
        <ul class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <li
                v-for="day in days" :key="day.date.format('YYYY-MM-DD')"
                class="px-3 py-4 border border-gray-200 rounded relative"
            >
                <p class="absolute top-0 right-1 text-xs opacity-50">
                    <span class="sr-only">Year </span>
                    {{ day.date.format('YYYY') }}
                </p>
                <p class="text-center font-semibold tracking-wide">
                    {{ day.date.format('ddd Do MMMM') }}
                </p>
                <div class="flex items-center justify-center gap-3 mt-3 text-center text-sm" v-if="mode == 'show'">
                    <button class="bg-green-600 rounded px-2 text-white cursor-pointer" @click="day.showYes = true">
                        <span class="sr-only">Yes</span>
                        <span class="font-bold rounded tracking-widest whitespace-nowrap">
                            <span class="">{{ valueCountOnDate(day.date.format('YYYY-MM-DD'), 'yes') }}</span>
                            <i class="far fa-circle-check inline-block ml-px text-xs"></i>
                        </span>
                    </button>
                    <VueFinalModal
                        v-model="day.showYes" class="flex justify-center items-center"
                        content-class="bg-white p-5 rounded relative">
                        <button class="absolute top-0 right-1 cursor-pointer" @click="day.showYes = false" aria-label="Close Modal"><i class="fa fa-xmark"></i></button>
                        <ul class="list-disc list-inside">
                            <h2 class="text-lg font-semibold tracking-wider text-left">Available on {{ day.date.format('ddd Do MMMM') }}</h2>
                            <li v-for="attendee in valueOnDateArray(day.date.format('YYYY-MM-DD'), 'yes')">
                                {{ attendee.name }}
                            </li>
                        </ul>
                    </VueFinalModal>
                    <button class="bg-yellow-400 rounded px-2 cursor-pointer" @click="day.showMaybe = true">
                        <span class="sr-only">Maybe</span>
                        <span class="font-bold rounded tracking-widest whitespace-nowrap">
                            <span class="">{{ valueCountOnDate(day.date.format('YYYY-MM-DD'), 'maybe') }}</span>
                            <i class="far fa-circle-question inline-block ml-px text-xs"></i>
                        </span>
                    </button>
                    <VueFinalModal
                        v-model="day.showMaybe" class="flex justify-center items-center"
                        content-class="bg-white p-5 rounded relative">
                        <button class="absolute top-0 right-1 cursor-pointer" @click="day.showMaybe = false" aria-label="Close Modal"><i class="fa fa-xmark"></i></button>
                        <h2 class="text-lg font-semibold tracking-wider text-left">Maybe available on {{ day.date.format('ddd Do MMMM') }}</h2>
                        <ul class="list-disc list-inside">
                            <li v-for="attendee in valueOnDateArray(day.date.format('YYYY-MM-DD'), 'maybe')">
                                {{ attendee.name }}
                            </li>
                        </ul>
                    </VueFinalModal>
                    <button class="bg-gray-300 rounded px-2 cursor-pointer" @click="day.showNo = true">
                        <span class="sr-only">No</span>
                        <span class="font-bold rounded tracking-widest whitespace-nowrap">
                            <span class="">{{ valueCountOnDate(day.date.format('YYYY-MM-DD'), 'no') }}</span>
                            <i class="far fa-circle-xmark inline-block ml-px text-xs"></i>
                        </span>
                    </button>
                    <VueFinalModal
                        v-model="day.showNo" class="flex justify-center items-center"
                        content-class="bg-white p-5 rounded relative">
                        <button class="absolute top-0 right-1 cursor-pointer" @click="day.showNo = false" aria-label="Close Modal"><i class="fa fa-xmark"></i></button>
                        <h2 class="text-lg font-semibold tracking-wider text-left">Not available on {{ day.date.format('ddd Do MMMM') }}</h2>
                        <ul class="list-disc list-inside">
                            <li v-for="attendee in valueOnDateArray(day.date.format('YYYY-MM-DD'), 'no')">
                                {{ attendee.name }}
                            </li>
                        </ul>
                    </VueFinalModal>
                </div>
                <div v-else-if="mode == 'add'" class="mt-3">
                    <div class="text-xl flex items-center justify-around">
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format('YYYY-MM-DD')]" value="yes">
                            <span class="px-2 py-1 rounded text-green-600 peer-checked:bg-green-600 peer-checked:text-white">
                                <i class="far fa-circle-check"></i>
                            </span>
                            <span class="sr-only">Available</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format('YYYY-MM-DD')]" value="maybe">
                            <span class="px-2 py-1 rounded text-yellow-500 peer-checked:bg-yellow-400 peer-checked:text-gray-800">
                                <i class="far fa-circle-question"></i>
                            </span>
                            <span class="sr-only">Maybe Available</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format('YYYY-MM-DD')]" value="no">
                            <span class="px-2 py-1 rounded text-gray-400 peer-checked:bg-gray-300 peer-checked:text-gray-800">
                                <i class="far fa-circle-xmark"></i>
                            </span>
                            <span class="sr-only">Not Available</span>
                        </label>
                    </div>
                </div>
            </li>
        </ul>
        <div v-if="props.mode == 'add'" class="flex items-center gap-5 mt-5">
            <div class="flex-1">
                <VLabel for="name" class="sr-only">Your Name</VLabel>
                <VInput v-model="availability.name" placeholder="Add a name" id="name" name="name" required maxlength="255"></VInput>
            </div>
            <VButton type="submit" :disabled="!availability.name">Finish</VButton>
        </div>
    </component>
</template>