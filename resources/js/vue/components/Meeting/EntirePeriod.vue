<script setup>

import VInput from '../VInput.vue';
import VLabel from '../VLabel.vue';
import VButton from '../VButton.vue';
import VAlert from '../VAlert.vue';
import EntirePeriodDateDisplay from './EntirePeriodDateDisplay.vue';
import moment from 'moment';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    meeting: Object,
    mode: String,
    attendees: Array,
    showDifferentTimezoneInfo: Boolean,
    userTimezone: String,
});

const emit = defineEmits(['submit']);
const selectedAttendee = defineModel('selectedAttendee', {type: Object, default: null});
const selectedDay = defineModel('selectedDay', {type: Object, default: null});

const dateFormat = 'YYYY-MM-DD';
const generalError = ref(null);
const days = ref([]);
const availability = ref({
    name: null,
    dates: {},
});

const generateDays = () => {
    const startDate = moment.tz(props.meeting.start_date, dateFormat, props.meeting.timezone);
    const endDate = moment.tz(props.meeting.end_date, dateFormat, props.meeting.timezone);
    const current = startDate.clone();
    const dates = props.meeting.dates;

    if(!startDate.isValid() || !endDate.isValid()) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }
    if(!startDate.isSameOrBefore(endDate)) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }
    while(current <= endDate) {
        if(dates && dates.length && dates.includes(current.format(dateFormat))) {
            let date = current.clone();
            let userDateStart = moment.tz(date.format('YYYY-MM-DD ' + props.meeting.start_time), 'YYYY-MM-DD HH:mm', props.meeting.timezone).tz(props.userTimezone);
            let userDateEnd = moment.tz(date.format('YYYY-MM-DD ' + props.meeting.end_time), 'YYYY-MM-DD HH:mm', props.meeting.timezone).tz(props.userTimezone);
            let userStartAndEndOnDifferentDays = userDateStart.day() != userDateEnd.day();
            let userStartAndEndOnDifferentYears = userDateStart.year() != userDateEnd.year();
            days.value.push({
                date: date,
                userDateStart: userDateStart,
                userDateEnd: userDateEnd,
                userStartAndEndOnDifferentDays: userStartAndEndOnDifferentDays,
            });
        }
        current.add(1, 'day');
    }
}

const generateFreshAvailability = () => {
    let newAvailability = {};
    days.value.forEach(day => {
        newAvailability[day.date.format(dateFormat)] = 'no';
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

const filteredAttendees = computed(() => {
    return props.selectedAttendee ? props.attendees.filter(a => a == props.selectedAttendee) : props.attendees;
});

const attendeesOnDayWithValue = (date, value) => {
    return filteredAttendees.value.filter(a => a.dates[date] == value);
}

const valueCountOnDate = (date, value) => {
    return attendeesOnDayWithValue(date, value).length;
}

const selectedDayIsSame = (day) => {
    return selectedDay.value && selectedDay.value.date.isSame(day.date);
}

const toggleDay = (day) => {
    if(selectedDayIsSame(day)) {
        selectedDay.value = null;
        return;
    }
    selectedAttendee.value = null;
    nextTick(() => {
        selectedDay.value = {
            date: day.date,
            attendeesYes: attendeesOnDayWithValue(day.date.format(dateFormat), 'yes'),
            attendeesMaybe: attendeesOnDayWithValue(day.date.format(dateFormat), 'maybe'),
            attendeesNo: attendeesOnDayWithValue(day.date.format(dateFormat), 'no'),
        };
    })
}

</script>

<template>
    <VAlert v-model="generalError" level="warning"></VAlert>
    <component :is="props.mode == 'show' ? 'div' : 'form'" @submit.prevent="submit">
        <ul class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <li
                v-for="day in days" :key="day.date.format(dateFormat)"
            >
                <div
                    class="px-3 py-4 border rounded relative cursor-pointer hover:bg-gray-50 focus:bg-gray-50 transition-colors"
                    :class="[selectedDayIsSame(day) ? 'border-green-600' : 'border-gray-200']"
                    v-if="mode == 'show'">
                    <EntirePeriodDateDisplay
                        :day="day"
                        :show-different-timezone-info="props.showDifferentTimezoneInfo"
                    ></EntirePeriodDateDisplay>
                    <div class="flex items-center justify-center gap-3 mt-3 text-center text-sm">
                        <div class="bg-green-600 rounded px-2 text-white cursor-pointer">
                            <span class="sr-only">Yes</span>
                            <span class="font-bold rounded tracking-widest whitespace-nowrap">
                                <span :dusk="'availability_' + day.date.format('YYYYMMDD') + '_yes'">
                                    {{ valueCountOnDate(day.date.format(dateFormat), 'yes') }}
                                </span>
                                <i class="far fa-circle-check inline-block ml-px text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="bg-yellow-400 rounded px-2 cursor-pointer">
                            <span class="sr-only">Maybe</span>
                            <span class="font-bold rounded tracking-widest whitespace-nowrap">
                                <span :dusk="'availability_' + day.date.format('YYYYMMDD') + '_maybe'">
                                    {{ valueCountOnDate(day.date.format(dateFormat), 'maybe') }}
                                </span>
                                <i class="far fa-circle-question inline-block ml-px text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="bg-gray-300 rounded px-2 cursor-pointer">
                            <span class="sr-only">No</span>
                            <span class="font-bold rounded tracking-widest whitespace-nowrap">
                                <span :dusk="'availability_' + day.date.format('YYYYMMDD') + '_no'">
                                    {{ valueCountOnDate(day.date.format(dateFormat), 'no') }}
                                </span>
                                <i class="far fa-circle-xmark inline-block ml-px text-xs" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    <button class="absolute inset-0" @click="toggleDay(day)">
                        <span class="sr-only" v-if="selectedDayIsSame(day)">Show responses for this day.</span>
                        <span class="sr-only" v-else>Unselect responses for this day</span>
                    </button>
                </div>
                <div v-else-if="mode == 'add'" class="px-3 py-4 border border-gray-200 rounded relative">
                    <EntirePeriodDateDisplay
                        :day="day"
                        :show-different-timezone-info="props.showDifferentTimezoneInfo"
                    ></EntirePeriodDateDisplay>
                    <div class="text-xl flex items-center justify-around mt-3">
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format(dateFormat)]" value="yes">
                            <span class="px-2 py-1 rounded text-green-600 peer-checked:bg-green-600 peer-checked:text-white">
                                <i class="far fa-circle-check" aria-hidden="true"></i>
                            </span>
                            <span class="sr-only">Available</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format(dateFormat)]" value="maybe">
                            <span class="px-2 py-1 rounded text-yellow-500 peer-checked:bg-yellow-400 peer-checked:text-gray-800">
                                <i class="far fa-circle-question" aria-hidden="true"></i>
                            </span>
                            <span class="sr-only">Maybe Available</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" :name="'availability_' + day.date.format('YYYYMMDD')"
                            class="peer sr-only" v-model="availability.dates[day.date.format(dateFormat)]" value="no">
                            <span class="px-2 py-1 rounded text-gray-400 peer-checked:bg-gray-300 peer-checked:text-gray-800">
                                <i class="far fa-circle-xmark" aria-hidden="true"></i>
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
                <VInput v-model="availability.name" placeholder="Add a name" id="name" name="name" required maxlength="128"></VInput>
            </div>
            <VButton type="submit" :disabled="!availability.name" dusk="finish-button">Finish</VButton>
        </div>
    </component>
</template>