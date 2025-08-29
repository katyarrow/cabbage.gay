<script setup>

import VInput from '../VInput.vue';
import VSelect from '../VSelect.vue';
import VLabel from '../VLabel.vue';
import VButton from '../VButton.vue';
import VAlert from '../VAlert.vue';
import moment from 'moment';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import constants from '#root/constants';

const props = defineProps({
    meeting: Object,
    mode: String,
    symbolMode: Boolean,
    attendees: Array,
    showDifferentTimezoneInfo: Boolean,
    userTimezone: String,
});

const emit = defineEmits([
    'submit',
    'do-add-availability-shake',
]);
const selectedAttendee = defineModel('selectedAttendee', {type: Object, default: null});
const selectedDay = defineModel('selectedDay', {type: Object, default: null});

const dateFormat = 'YYYY-MM-DD';
const dateTimeFormat = 'YYYY-MM-DD HH:mm';
const timeFormat = 'HH:mm';
const generalError = ref(null);
const days = ref([]);
const times = ref([]);
const page = ref(0);
const perPage = ref(7);
const displayType = ref('proportion');
const availability = ref({
    name: null,
    dates: {},
});
const currentDiscoModeColorIndex = ref(0);
const discoModeColors = ['red', 'orange', 'yellow', 'green', 'blue', 'violet'];

const paginatedDays = computed(() => {
    let startIndex = perPage.value * page.value;
    let endIndex = (perPage.value * page.value) + perPage.value;
    return {
        days: days.value.filter((v, index) => index >= startIndex && index < endIndex),
        isFirstPage: page.value == 0,
        isLastPage: (page.value + 1) * perPage.value >= days.value.length,
        currentPage: page.value,
        lastPage: Math.ceil(days.value.length / perPage.value),
        totalPages: Math.ceil(days.value.length / perPage.value),
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

    if(page.value >= paginatedDays.value.totalPages) {
        page.value = paginatedDays.value.totalPages - 1;
    }
}

const generateDays = () => {
    const startDate = moment.tz(props.meeting.start_date, dateFormat, props.meeting.timezone);
    const endDate = moment.tz(props.meeting.end_date, dateFormat, props.meeting.timezone);
    const startTime = moment.tz(props.meeting.start_time, timeFormat, props.meeting.timezone);
    const endTime = moment.tz(props.meeting.end_time, timeFormat, props.meeting.timezone);
    const dates = props.meeting.dates;

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
        if(dates && dates.length && dates.includes(currDate.format(dateFormat))) {
            days.value.push({
                date: currDate.clone(),
            });
        }
        currDate.add(1, 'day');
    }

    let currTime = startTime.clone();
    while(currTime < endTime) {
        let userTime = currTime.clone().tz(props.userTimezone);
        let time = currTime.clone();

        let tzTimeDiffInDays = ((parseInt(userTime.format('YYYY')) * 365) + parseInt(userTime.format('DDD'))) - ((parseInt(time.format('YYYY')) * 365) + parseInt(time.format('DDD')));

        if(tzTimeDiffInDays == 0) {
            tzTimeDiffInDays = null
        } else if (Math.abs(tzTimeDiffInDays) > 1) {
            tzTimeDiffInDays = (tzTimeDiffInDays >= 0 ? '+' : '') + tzTimeDiffInDays +  ' days';
        } else {
            tzTimeDiffInDays = (tzTimeDiffInDays >= 0 ? '+' : '') + tzTimeDiffInDays +  ' day';
        }
        times.value.push({
            time: time,
            userTime: userTime,
            tzTimeDiffInDays: tzTimeDiffInDays,
        });
        currTime.add(30, 'minutes');
    }
}

const generateFreshAvailability = () => {
    availability.value.dates = {};
    availability.value.name = null;
}

const addDateAndTime = (date, time, tz) => {
    tz = tz || props.meeting.timezone;
    return moment.tz(date.format(dateFormat) + ' ' + time.format(timeFormat), dateTimeFormat, tz);
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

const toggleDay = (day, time) => {
    if(selectedDayIsSame(day, time)) {
        selectedDay.value = null;
        return;
    }
    selectedAttendee.value = null;
    nextTick(() => {
        let displayInfo = gridSquareDisplayInfo(day.date, time.time);
        selectedDay.value = {
            date: day.date,
            time: time.time,
            datetime: addDateAndTime(day.date, time.time),
            attendeesYes: displayInfo.attendeesYes,
            attendeesMaybe: displayInfo.attendeesMaybe,
            attendeesNo: displayInfo.attendeesNo,
        };
    });
}

const selectedDayIsSame = (day, time) => {
    return selectedDay.value && selectedDay.value.datetime.isSame(addDateAndTime(day.date, time.time));
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
    let valuesPositive = values.filter(v => v == constants.AVAILABILITY_YES || v == constants.AVAILABILITY_MAYBE);
    let valuesYes = values.filter(v => v == constants.AVAILABILITY_YES);
    let attendeesYes = attendees.filter(a => a.dates[datetime] == constants.AVAILABILITY_YES);
    let valuesMaybe = values.filter(v => v == constants.AVAILABILITY_MAYBE);
    let attendeesMaybe = attendees.filter(a => a.dates[datetime] == constants.AVAILABILITY_MAYBE);
    let attendeesNo = attendees.filter(a => !(a.dates[datetime] == constants.AVAILABILITY_YES || a.dates[datetime] == constants.AVAILABILITY_MAYBE));
    let ratio = values.length > 0 ? Math.round((valuesMaybe.length / Math.max(1, valuesPositive.length)) * 4 ) / 4 : 0;
    let inverseRatio = values.length > 0 ? 1 - ratio : 0;
    let opacity = attendees.length > 0 ? Math.round((valuesPositive.length / attendees.length) * 100) / 100 : 0;
    let gradientColor = gradient[Math.round(ratio * (gradient.length - 1))];

    let info = {
        total: valuesPositive.length,
        totalYes: valuesYes.length,
        totalMaybe: valuesMaybe.length,
        ratio: ratio,
        inverseRatio: inverseRatio,
        opacity: opacity,
        gradientColor: gradientColor,
        attendeesYes: attendeesYes,
        attendeesMaybe: attendeesMaybe,
        attendeesNo: attendeesNo,
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

const startDrag = (dateIndex, timeIndex, selectOne) => {
    dragInfo.value.dragging = true;
    dragInfo.value.startDateIndex = dateIndex;
    dragInfo.value.startTimeIndex = timeIndex;
    let value;
    switch(getAvailabilityByIndex(pageAwareIndex(dateIndex), timeIndex)){
        case constants.AVAILABILITY_YES: value = constants.AVAILABILITY_MAYBE; break;
        case constants.AVAILABILITY_MAYBE: value = constants.AVAILABILITY_NO; break;
        default: value = constants.AVAILABILITY_YES;
    }
    dragInfo.value.value = value;
    dragInfo.value.originalValues = {...availability.value.dates};
    addOrUpdateAvailability(pageAwareIndex(dateIndex), timeIndex, dragInfo.value.value);

    if(selectOne) {
        dragInfo.value.dragging = false;
    }
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

const swipePrevPage = () => {
    if(props.mode == constants.MEETING_MODE_SHOW) {
        prevPage();
    }
}

const swipeNextPage = () => {
    if(props.mode == constants.MEETING_MODE_SHOW) {
        nextPage();
    }
}

const prevPage = () => {
    if(!paginatedDays.value.isFirstPage) {
        page.value--;
    }
}

const nextPage = () => {
    if(!paginatedDays.value.isLastPage) {
        page.value++;
    }
}

const setDiscoModeInterval = () => {
    setInterval(() => {
        currentDiscoModeColorIndex.value = (currentDiscoModeColorIndex.value + 1) % discoModeColors.length;
    }, 300);
}

const doAddAvailabilityShake = () => {
    emit('do-add-availability-shake');
}

onMounted(() => {
    document.addEventListener('mouseup', stopDrag);
    document.addEventListener('touchend', stopDrag);
    addEventListener("resize", updatePageSize);
    updatePageSize();
    setDiscoModeInterval();
});

generateDays();

watch(() => props.mode, () => {
    if(props.mode == constants.MEETING_MODE_ADD) {
        generateFreshAvailability();
    }
    selectedDay.value = null;
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
    <component :is="props.mode == constants.MEETING_MODE_SHOW ? 'div' : 'form'" @submit.prevent="submit">
        <p class="text-center text-gray-800">{{ currentPaginatedMonths }}</p>
        <div class="relative flex items-center">
            <button dusk="prev-page-btn" type="button" class="absolute -left-6 cursor-pointer" v-if="!paginatedDays.isFirstPage" @click="prevPage">
                <span class="sr-only">Previous page</span>
                <i class="fa fa-chevron-left text-3xl text-gray-300"></i>
            </button>
            <table
                class="w-full my-5 table-fixed touch-none"
                ondragstart="return false;"
                ondragend="return false;"
                :key="JSON.stringify(props.selectedAttendee)"
                v-touch:swipe.left="swipeNextPage"
                v-touch:swipe.right="swipePrevPage">
                <thead>
                    <tr>
                        <th scope="col" class="w-11"><span class="sr-only">Times</span></th>
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
                            <span v-if="showDifferentTimezoneInfo" class="select-none text-nowrap">
                                <time v-if="timeIndex % 2 == 0">
                                    {{ time.userTime.format('h a') }}
                                    <span v-if="time.tzTimeDiffInDays != null" class="sr-only">
                                        {{ time.tzTimeDiffInDays }}
                                    </span>
                                </time>
                                <time v-else>
                                    <span class="sr-only">{{ time.userTime.format('h:mm a') }}</span>
                                    <span v-if="time.tzTimeDiffInDays != null"
                                        class="inline-block text-xs bg-yellow-200 text-gray-700 rounded-full px-1">
                                        {{ time.tzTimeDiffInDays }}
                                    </span>
                                </time>
                            </span>
                            <span v-else :class="[timeIndex % 2 == 0 ? '' : 'sr-only']" class="select-none text-nowrap">
                                <time v-if="timeIndex % 2 == 0">{{ time.time.format('h a') }}</time>
                                <time v-else>{{ time.time.format('h:mm a') }}</time>
                            </span>
                        </th>
                        <td
                            draggable="false"
                            v-for="(day, dateIndex) in paginatedDays.days"
                            class="border border-gray-400 h-5 relative"
                            :class="[
                                timeIndex % 2 == 0 ? 'border-b-0' : 'border-t-0',
                                selectedDayIsSame(day, time) ? 'border-dashed border border-gray-600' : '',
                            ]"
                        >
                            <div v-if="mode == constants.MEETING_MODE_ADD" class="grid-square h-full flex items-center justify-center relative"
                                draggable="false"
                                :data-dateindex="dateIndex"
                                :data-timeindex="timeIndex"
                                :class="[
                                    {[constants.AVAILABILITY_YES]: 'bg-green-400', [constants.AVAILABILITY_MAYBE]: 'bg-yellow-300', [constants.AVAILABILITY_NO]: ''}[getAvailability(day.date, time.time)]
                                ]"
                                :dusk="'availability_selector_' + addDateAndTime(day.date, time.time).format('YYYY-MM-DD-HH-mm')"
                            >
                                <div
                                    :class="[symbolMode ? '' : 'sr-only']"
                                    class="text-center text-gray-600 text-xs"
                                    :dusk="'availability_icon_' + addDateAndTime(day.date, time.time).format('YYYY-MM-DD-HH-mm')"
                                >
                                    <i class="far fa-circle-check" v-if="getAvailability(day.date, time.time) == constants.AVAILABILITY_YES" aria-label="Yes"></i>
                                    <i class="far fa-circle-question" v-else-if="getAvailability(day.date, time.time) == constants.AVAILABILITY_MAYBE" aria-label="Maybe"></i>
                                    <span v-else class="sr-only">No</span>
                                </div>
                                <button type="button" class="absolute inset-0"
                                    @keydown.enter="startDrag(dateIndex, timeIndex, true)"
                                    @keydown.space="startDrag(dateIndex, timeIndex, true)"
                                    @pointerdown="startDrag(dateIndex, timeIndex)"
                                    @mouseenter="hoverEnter(dateIndex, timeIndex)"
                                    @pointermove="pointerMove"
                                    :dusk="'availability_btn_' + addDateAndTime(day.date, time.time).format('YYYY-MM-DD-HH-mm')">
                                    <span class="sr-only" v-if="getAvailability(day.date, time.time) == constants.AVAILABILITY_YES">Change to maybe</span>
                                    <span class="sr-only" v-else="getAvailability(day.date, time.time) == constants.AVAILABILITY_MAYBE">Change to no</span>
                                    <span class="sr-only" v-else>Change to yes</span>
                                </button>
                            </div>
                            <div v-else class="h-full" @pointerdown="!gridSquareDisplayInfo(day.date, time.time).total ? doAddAvailabilityShake() : null">
                                <div v-if="displayType == 'proportion' || props.selectedAttendee" class="h-full w-full relative select-none" :style="{opacity: gridSquareDisplayInfo(day.date, time.time).opacity}">
                                    <div
                                        class="bg-yellow-300 inline-flex items-center justify-center absolute top-0 bottom-0 left-0"
                                        :style="{width: (gridSquareDisplayInfo(day.date, time.time).ratio * 100) + '%'}">
                                        <span class="sr-only">{{ 'Maybe ' + (gridSquareDisplayInfo(day.date, time.time).ratio * 100) + '%' }}</span>
                                        <i class="far fa-circle-question text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).ratio > 0"></i>
                                        <span v-else>&nbsp;</span>
                                    </div>
                                    <div
                                        class="bg-green-500 inline-flex items-center justify-center absolute top-0 bottom-0 right-0"
                                        :style="{width: (gridSquareDisplayInfo(day.date, time.time).inverseRatio * 100) + '%'}">
                                        <span class="sr-only">{{ 'Yes ' + (gridSquareDisplayInfo(day.date, time.time).inverseRatio * 100) + '%' }}</span>
                                        <i class="far fa-circle-check text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).inverseRatio > 0"></i>
                                        <span v-else>&nbsp;</span>
                                    </div>
                                </div>
                                <div v-else-if="displayType == 'simple'"
                                    class="h-full w-full relative select-none bg-green-500 flex items-center justify-center"
                                    :style="{opacity: gridSquareDisplayInfo(day.date, time.time).opacity}">
                                    <i class="far fa-circle-check text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).opacity > 0"></i>
                                    <span class="sr-only">{{ gridSquareDisplayInfo(day.date, time.time).total }} responses</span>
                                </div>
                                <div v-else-if="displayType == 'numbers'">
                                    <div class="flex items-center justify-around md:px-1 text-xs font-bold text-nowrap" v-if="gridSquareDisplayInfo(day.date, time.time).total > 0">
                                        <span class="flex items-center gap-1">
                                            <span class="text-yellow-600 sm:text-gray-800">{{ gridSquareDisplayInfo(day.date, time.time).totalMaybe }}</span>
                                            <i class="far fa-circle-question text-yellow-600" aria-label="Maybe"></i>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="text-green-500 sm:text-gray-800">{{ gridSquareDisplayInfo(day.date, time.time).totalYes }}</span>
                                            <i class="far fa-circle-check text-green-500" aria-label="Yes"></i>
                                        </span>
                                    </div>
                                </div>
                                <div v-else-if="displayType == 'gradient'"
                                    class="h-full w-full relative select-none flex items-center justify-center"
                                    :style="{
                                        opacity: gridSquareDisplayInfo(day.date, time.time).opacity,
                                        'background-color': gridSquareDisplayInfo(day.date, time.time).gradientColor,
                                    }">
                                    <span class="sr-only">{{ gridSquareDisplayInfo(day.date, time.time).total }} Responses</span>
                                    <span class="sr-only">{{ gridSquareDisplayInfo(day.date, time.time).totalYes }} Yes</span>
                                    <span class="sr-only">{{ gridSquareDisplayInfo(day.date, time.time).totalMaybe }} Maybe</span>
                                    <span v-if="props.symbolMode" class="flex items-center gap-1" aria-hidden="true">
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
                                            <i class="far fa-circle-question" aria-label="Maybe"></i>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span>{{ gridSquareDisplayInfo(day.date, time.time).totalYes }}</span>
                                            <i class="far fa-circle-check" aria-label="Yes"></i>
                                        </span>
                                    </div>
                                </div>
                                <div v-else-if="displayType == 'disco'"
                                    class="h-full w-full relative select-none flex items-center justify-center transition-colors duration-150"
                                    :style="{
                                        opacity: gridSquareDisplayInfo(day.date, time.time).opacity,
                                        'background-color': discoModeColors[currentDiscoModeColorIndex],
                                    }">
                                    <i class="far fa-circle-check text-xs" v-if="props.symbolMode && gridSquareDisplayInfo(day.date, time.time).opacity > 0"></i>
                                    <span class="sr-only">{{ gridSquareDisplayInfo(day.date, time.time).total }} responses</span>
                                </div>
                                <button
                                    type="button"
                                    class="box-border absolute inset-0 hover:border border-dashed"
                                    :class="[selectedDayIsSame(day, time) ? 'border' : '']"
                                    @click="toggleDay(day, time)"
                                >
                                    <span class="sr-only">Toggle attendees on this time slot</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button dusk="next-page-btn" type="button" class="absolute -right-6 cursor-pointer" v-if="!paginatedDays.isLastPage" @click="nextPage">
                <span class="sr-only">Next page</span>
                <i class="fa fa-chevron-right text-3xl text-gray-300"></i>
            </button>
        </div>
        <div class="flex gap-5 justify-between items-center" v-if="mode == constants.MEETING_MODE_SHOW">
            <p class="text-sm text-gray-500">Page {{ paginatedDays.currentPage + 1 }} / {{ paginatedDays.lastPage }}</p>
            <div class="flex items-center flex-1 gap-5">
                <VLabel for="display_type" class="sr-only">Display Mode</VLabel>
                <VSelect v-model="displayType" size="sm" id="display_type">
                    <option value="simple">Simple</option>
                    <option value="proportion">Proportion Bars</option>
                    <option value="gradient">Gradient</option>
                    <option value="numbers">Numbers</option>
                    <option value="gradient_numbers">Gradient + Numbers</option>
                    <option value="disco">DISCO (warning: flashing lights)</option>
                </VSelect>
            </div>
        </div>
        <div class="flex gap-5 justify-center items-center" v-if="mode == constants.MEETING_MODE_ADD && paginatedDays.totalPages > 1">
            <VButton dusk="adding-prev-page-btn" type="button" size="sm" :disabled="paginatedDays.isFirstPage" @click="prevPage" class="flex-1 md:flex-auto">
                <i class="fa fa-chevron-left"></i> Previous <span class="sr-only">Page</span>
            </VButton>
            <p class="text-gray-500">Page {{ paginatedDays.currentPage + 1 }} / {{ paginatedDays.lastPage }}</p>
            <VButton dusk="adding-next-page-btn" type="button" size="sm" :disabled="paginatedDays.isLastPage" @click="nextPage" class="flex-1 md:flex-auto">
                Next <span class="sr-only">Page</span> <i class="fa fa-chevron-right"></i>
            </VButton>
        </div>
        <div v-if="props.mode == constants.MEETING_MODE_ADD" class="flex items-center gap-5 mt-5">
            <div class="flex-1">
                <VLabel for="name" class="sr-only">Your Name</VLabel>
                <VInput v-model="availability.name" placeholder="Add a name" id="name" name="name" required maxlength="128"></VInput>
            </div>
            <VButton type="submit" :disabled="!availability.name" dusk="finish-button">Finish</VButton>
        </div>
    </component>
</template>