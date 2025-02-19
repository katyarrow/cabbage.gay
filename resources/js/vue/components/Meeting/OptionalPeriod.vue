<script setup>

import VInput from '../VInput.vue';
import VCheckbox from '../VCheckbox.vue';
import VSelect from '../VSelect.vue';
import VLabel from '../VLabel.vue';
import VButton from '../VButton.vue';
import VAlert from '../VAlert.vue';
import moment from 'moment';
import { computed, onMounted, ref, watch } from 'vue';
import { VueFinalModal } from 'vue-final-modal';

const props = defineProps({
    meeting: Object,
    mode: String,
    symbolMode: Boolean,
    attendees: Array,
    selectedAttendee: Object,
});

const emit = defineEmits(['submit']);

const dateFormat = 'YYYY-MM-DD';
const dateTimeFormat = 'YYYY-MM-DD HH:mm';
const timeFormat = 'HH:mm';
const generalError = ref(null);
const days = ref([]);
const times = ref([]);
const page = ref(0);
const perPage = ref(7);
const displayType = ref('simple');
const availability = ref({
    name: null,
    dates: {},
});

const paginatedDays = computed(() => {
    let startIndex = perPage.value * page.value;
    let endIndex = (perPage.value * page.value) + perPage.value;
    return {
        days: days.value.filter((v, index) => index >= startIndex && index < endIndex),
        isFirstPage: page.value == 0,
        isLastPage: (page.value + 1) * perPage.value >= days.value.length,
        currentPage: page.value,
        lastPage: Math.ceil(days.value.length / perPage.value),
    };
});

const currentPaginatedMonths = computed(() => {
    if(!paginatedDays.value.days || !paginatedDays.value.days.length) {
        return '';
    }
    let earliest = paginatedDays.value.days[0].date.format('MMMM');
    let latest = paginatedDays.value.days[paginatedDays.value.days.length - 1].date.format('MMMM');
    if(earliest == latest) {
        return earliest;
    }
    return earliest + ' - ' + latest;
});

const pageAwareIndex = (index) => {
    return (perPage.value * page.value) + index;
}

const updatePageSize = () => {
    if(document.body.clientWidth >= 768) {
        perPage.value = 7;
    } else {
        perPage.value = 4;
    }
}

const generateDays = () => {
    const startDate = moment(props.meeting.start_date, dateFormat);
    const endDate = moment(props.meeting.end_date, dateFormat);
    const startTime = moment(props.meeting.start_time, timeFormat);
    const endTime = moment(props.meeting.end_time, timeFormat);

    if(!startDate.isValid() || !endDate.isValid() || !startTime.isValid() || !endTime.isValid()) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }
    if(!startDate.isSameOrBefore(endDate) || !startTime.isBefore(endTime)) {
        generalError.value = 'Invalid dates or times on meeting.';
        return;
    }

    const currDate = startDate.clone();
    while(currDate <= endDate) {
        days.value.push({
            date: currDate.clone(),
        });
        currDate.add(1, 'day');
    }

    let currTime = startTime.clone();
    while(currTime < endTime) {
        times.value.push({
            time: currTime.clone(),
            showAttendees: false,
        });
        currTime.add(30, 'minutes');
    }
}

const generateFreshAvailability = () => {
    availability.value.dates = {};
    availability.value.name = null;
}

const addDateAndTime = (date, time) => {
    return moment(date.format(dateFormat) + ' ' + time.format(timeFormat), dateTimeFormat);
}

const addOrUpdateAvailability = (dateIndex, timeIndex, value) => {
    let date = days.value[dateIndex].date;
    let time = times.value[timeIndex].time;
    availability.value.dates[addDateAndTime(date, time).format(dateTimeFormat)] = value;
}

const getAvailability = (date, time) => {
    return availability.value.dates[addDateAndTime(date, time).format(dateTimeFormat)];
}

const getAvailabilityByIndex = (dateIndex, timeIndex) => {
    let date = days.value[dateIndex].date;
    let time = times.value[timeIndex].time;
    return getAvailability(date, time);
}

const gridSquareDisplayInfoCache = ref({});

const gradient = [
    '#00c750',
    '#68d03a',
    '#9ed723',
    '#cfdb14',
    '#ffdd1f',
];

const gridSquareDisplayInfo = (date, time) => {
    let datetime = addDateAndTime(date, time).format(dateTimeFormat);
    if(gridSquareDisplayInfoCache.value[datetime]) {
        return gridSquareDisplayInfoCache.value[datetime];
    }
    let attendees = props.selectedAttendee ? props.attendees.filter(a => a == props.selectedAttendee) : props.attendees;
    let values = attendees.map(a => a.dates[datetime]).filter(v => !!v);
    let valuesPositive = values.filter(v => v == 'yes' || v == 'maybe');
    let valuesYes = values.filter(v => v == 'yes');
    let valuesMaybe = values.filter(v => v == 'maybe');
    let ratio = Math.round((valuesMaybe.length / Math.max(1, valuesPositive.length)) * 4 ) / 4;
    let inverseRatio = 1 - ratio;
    let opacity = Math.round((valuesPositive.length / attendees.length) * 100) / 100;
    let gradientColor = gradient[Math.round(ratio * (gradient.length - 1))];

    let info = {
        total: valuesPositive.length,
        totalYes: valuesYes.length,
        totalMaybe: valuesMaybe.length,
        ratio: ratio,
        inverseRatio: inverseRatio,
        opacity: opacity,
        gradientColor: gradientColor,
    };
    gridSquareDisplayInfoCache.value[datetime] = info;
    return info;
}

const dragInfo = ref({
    dragging: false,
    startDateIndex: null,
    startTimeIndex: null,
    value: null,
    originalValues: {},
});

const startDrag = (dateIndex, timeIndex) => {
    dragInfo.value.dragging = true;
    dragInfo.value.startDateIndex = dateIndex;
    dragInfo.value.startTimeIndex = timeIndex;
    let value;
    switch(getAvailabilityByIndex(pageAwareIndex(dateIndex), timeIndex)){
        case 'yes': value = 'maybe'; break;
        case 'maybe': value = 'no'; break;
        default: value = 'yes';
    }
    dragInfo.value.value = value;
    dragInfo.value.originalValues = {...availability.value.dates};
    addOrUpdateAvailability(pageAwareIndex(dateIndex), timeIndex, dragInfo.value.value);
}

const stopDrag = () => {
    dragInfo.value.dragging = false;
}

const hoverEnter = (dateIndex, timeIndex) => {
    if(!dragInfo.value.dragging) {
        return;
    }
    availability.value.dates = {...dragInfo.value.originalValues};
    for(let di = pageAwareIndex(dragInfo.value.startDateIndex); di <= pageAwareIndex(dateIndex); di++) {
        for(let ti = dragInfo.value.startTimeIndex; ti <= timeIndex; ti++) {
            addOrUpdateAvailability(di, ti, dragInfo.value.value);
        }
    }
}

const pointerMove = (event) => {
    document.querySelectorAll('.grid-square').forEach(el => {
        let rect = el.getBoundingClientRect();
        if(event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom) {
            let dateIndex = parseInt(el.dataset.dateindex);
            let timeIndex = parseInt(el.dataset.timeindex);
            hoverEnter(dateIndex, timeIndex);
        }
    });
}

onMounted(() => {
    document.addEventListener('mouseup', stopDrag);
    document.addEventListener('touchend', stopDrag);
    addEventListener("resize", updatePageSize);
    updatePageSize();
});

generateDays();

watch(() => props.mode, () => {
    if(props.mode == 'add') {
        generateFreshAvailability();
    }
    gridSquareDisplayInfoCache.value = {};
})
watch(() => props.selectedAttendee, () => {
    gridSquareDisplayInfoCache.value = {};
})

const submit = () => {
    emit('submit', JSON.stringify(availability.value));
}

</script>

<template>
    <VAlert v-model="generalError" level="warning"></VAlert>
    <component :is="props.mode == 'show' ? 'div' : 'form'" @submit.prevent="submit">
        <p class="text-center text-gray-800">{{ currentPaginatedMonths }}</p>
        <div class="relative flex items-center">
            <button type="button" class="absolute -left-6 cursor-pointer" v-if="!paginatedDays.isFirstPage" @click="page--">
                <span class="sr-only">Previous page</span>
                <i class="fa fa-chevron-left text-3xl text-gray-300"></i>
            </button>
            <table class="w-full my-5 table-fixed touch-none" ondragstart="return false;" ondragend="return false;" :key="JSON.stringify(props.selectedAttendee)">
                <thead>
                    <tr>
                        <th scope="col" class="w-10"><span class="sr-only">Times</span></th>
                        <th scope="col" v-for="day in paginatedDays.days">
                            <div class="flex flex-col items-center font-normal select-none">
                                <span class="uppercase text-xs">{{ day.date.format('ddd') }}</span>
                                <span>{{ day.date.format('D') }}</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(time, timeIndex) in times" draggable="false">
                        <th scope="row" class="text-xs md:text-sm text-right pr-2 w-2 align-top font-normal">
                            <span v-if="timeIndex % 2 == 0" class="select-none">{{ time.time.format('h a') }}</span>
                        </th>
                        <td
                            draggable="false"
                            v-for="(day, dateIndex) in paginatedDays.days"
                            class="border border-gray-400 h-5"
                            :class="[
                                timeIndex % 2 == 0 ? 'border-b-0' : 'border-t-0'
                            ]"
                        >
                            <div v-if="mode == 'add'" class="grid-square h-full flex items-center justify-center"
                                draggable="false"
                                @pointerdown="startDrag(dateIndex, timeIndex)"
                                @mouseenter="hoverEnter(dateIndex, timeIndex)"
                                @pointermove="pointerMove"
                                :data-dateindex="dateIndex"
                                :data-timeindex="timeIndex"
                                :class="[
                                    {'yes': 'bg-green-400', 'maybe': 'bg-yellow-300', 'no': ''}[getAvailability(day.date, time.time)]
                                ]">
                                <div v-if="props.symbolMode" class="text-center text-gray-600 text-xs">
                                    <i class="far fa-circle-check" v-if="getAvailability(day.date, time.time) == 'yes'"></i>
                                    <i class="far fa-circle-question" v-else-if="getAvailability(day.date, time.time) == 'maybe'"></i>
                                </div>
                            </div>
                            <div v-else class="h-full">
                                <div v-if="displayType == 'proportion' || props.selectedAttendee" class="h-full w-full relative select-none" :style="{opacity: gridSquareDisplayInfo(day.date, time.time).opacity}">
                                    <div
                                        class="bg-yellow-300 inline-flex items-center justify-center absolute top-0 bottom-0 left-0"
                                        :style="{width: gridSquareDisplayInfo(day.date, time.time).ratio * 100 + '%'}">
                                        <i class="far fa-circle-question text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).ratio > 0"></i>
                                        <span v-else>&nbsp;</span>
                                    </div>
                                    <div
                                        class="bg-green-500 inline-flex items-center justify-center absolute top-0 bottom-0 right-0"
                                        :style="{width: gridSquareDisplayInfo(day.date, time.time).inverseRatio * 100 + '%'}">
                                        <i class="far fa-circle-check text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).inverseRatio > 0"></i>
                                        <span v-else>&nbsp;</span>
                                    </div>
                                </div>
                                <div v-else-if="displayType == 'simple'"
                                    class="h-full w-full relative select-none bg-green-500 flex items-center justify-center"
                                    :style="{opacity: gridSquareDisplayInfo(day.date, time.time).opacity}">
                                    <i class="far fa-circle-check text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).opacity > 0"></i>
                                </div>
                                <div v-else-if="displayType == 'numbers'">
                                    <div class="flex items-center justify-around md:px-1 text-xs font-bold text-nowrap" v-if="gridSquareDisplayInfo(day.date, time.time).total > 0">
                                        <span class="flex items-center gap-1">
                                            <span class="text-yellow-600 sm:text-gray-800">{{ gridSquareDisplayInfo(day.date, time.time).totalMaybe }}</span>
                                            <i class="far fa-circle-question text-yellow-600"></i>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="text-green-500 sm:text-gray-800">{{ gridSquareDisplayInfo(day.date, time.time).totalYes }}</span>
                                            <i class="far fa-circle-check text-green-500"></i>
                                        </span>
                                    </div>
                                </div>
                                <div v-else-if="displayType == 'gradient'"
                                    class="h-full w-full relative select-none flex items-center justify-center"
                                    :style="{
                                        opacity: gridSquareDisplayInfo(day.date, time.time).opacity,
                                        'background-color': gridSquareDisplayInfo(day.date, time.time).gradientColor,
                                    }">
                                    <span v-if="props.symbolMode" class="flex items-center gap-1">
                                        <i class="far fa-circle-question text-xs" v-if="gridSquareDisplayInfo(day.date, time.time).ratio > 0"></i>
                                        <span v-if="gridSquareDisplayInfo(day.date, time.time).ratio > 0 && gridSquareDisplayInfo(day.date, time.time).inverseRatio > 0">+</span>
                                        <i class="far fa-circle-check text-xs" v-if="gridSquareDisplayInfo(day.date, time.time).inverseRatio > 0"></i>
                                    </span>
                                </div>
                                <div v-else-if="displayType == 'gradient_numbers'"
                                    class="h-full w-full relative select-none"
                                    :style="{
                                        opacity: gridSquareDisplayInfo(day.date, time.time).opacity,
                                        'background-color': gridSquareDisplayInfo(day.date, time.time).gradientColor,
                                    }">
                                    <div class="flex items-center justify-around md:px-1 text-xs font-bold text-nowrap" v-if="gridSquareDisplayInfo(day.date, time.time).total > 0">
                                        <span class="flex items-center gap-1">
                                            <span>{{ gridSquareDisplayInfo(day.date, time.time).totalMaybe }}</span>
                                            <i class="far fa-circle-question"></i>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>{{ gridSquareDisplayInfo(day.date, time.time).totalYes }}</span>
                                            <i class="far fa-circle-check"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="absolute -right-6 cursor-pointer" v-if="!paginatedDays.isLastPage" @click="page++">
                <span class="sr-only">Next page</span>
                <i class="fa fa-chevron-right text-3xl text-gray-300"></i>
            </button>
        </div>
        <div class="flex gap-5 justify-between items-center" v-if="mode == 'show'">
            <p class="text-sm text-gray-500">Page {{ paginatedDays.currentPage + 1 }} / {{ paginatedDays.lastPage }}</p>
            <div class="flex items-center flex-1 gap-5">
                <VLabel for="display_type" class="sr-only">Display Mode</VLabel>
                <VSelect v-model="displayType" size="sm" id="display_type">
                    <option value="simple">Simple</option>
                    <option value="proportion">Proportion Bars</option>
                    <option value="gradient">Gradient</option>
                    <option value="numbers">Numbers</option>
                    <option value="gradient_numbers">Gradient + Numbers</option>
                </VSelect>
            </div>
        </div>
        <div class="flex gap-5 justify-center items-center" v-if="mode == 'add'">
            <VButton type="button" size="sm" :disabled="paginatedDays.isFirstPage" @click="page--" class="flex-1 md:flex-auto">
                <i class="fa fa-chevron-left"></i> Previous <span class="sr-only">Page</span>
            </VButton>
            <p class="text-gray-500">Page {{ paginatedDays.currentPage + 1 }} / {{ paginatedDays.lastPage }}</p>
            <VButton type="button" size="sm" :disabled="paginatedDays.isLastPage" @click="page++" class="flex-1 md:flex-auto">
                Next <span class="sr-only">Page</span> <i class="fa fa-chevron-right"></i>
            </VButton>
        </div>
        <div v-if="props.mode == 'add'" class="flex items-center gap-5 mt-5">
            <div class="flex-1">
                <VLabel for="name" class="sr-only">Your Name</VLabel>
                <VInput v-model="availability.name" placeholder="Add a name" id="name" name="name" required maxlength="255"></VInput>
            </div>
            <VButton type="submit" :disabled="!availability.name">Finish</VButton>
        </div>
    </component>
</template>