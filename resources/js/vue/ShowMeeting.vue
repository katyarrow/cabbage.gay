<script setup>

import VCheckbox from './components/VCheckbox.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import moment from 'moment';
import { onMounted, ref } from 'vue';
import EntirePeriod from './components/Meeting/EntirePeriod.vue';
import { VueFinalModal } from 'vue-final-modal';
import OptionalPeriod from './components/Meeting/OptionalPeriod.vue';
import VLabel from './components/VLabel.vue';
import LZString from 'lz-string';

const props = defineProps({
    meeting: Object,
    siteTitle: String,
});

const globalError = ref(null);
const loaded = ref(false);
const posting = ref(false);
const crypt = ref(null);
const mode = ref('show');
const shared = ref(false);
const selectedAttendee = ref(null);
const selectedDay = ref(null);

const meeting = ref({
    name: null,
    timezone: null,
    entire_period: null,
    start_date: null,
    end_date: null,
    dates: null,
    start_time: null,
    end_time: null,
    destroy_at: null,
});

const attendees = ref([]);
const symbolMode = ref(false);
const showDifferentTimezoneInfo = ref(false);
const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const parseAttendees = (encryptedAttendeeArray) => {
    return encryptedAttendeeArray.map(a => {
        let data;
        try{
            data = crypt.value.decrypt(a.data);
            if(data.startsWith('compressed:')) {
                data = LZString.decompressFromEncodedURIComponent(data.replace('compressed:', ''));
            }
            data = JSON.parse(data);
        } catch (SyntaxError) {
            // in case the attendee data is not valid json.
            return false;
        }
        data.identifier = a.identifier;
        data.destroy_route = a.destroy_route;
        data.destroy_challenge = a.destroy_challenge;
        data.showDelete = a.showDelete;
        data.name = data.name.substring(0, 128);
        return data;
    })
    .filter(a => a !== false)
    .sort((a, b) => a.name.localeCompare(b.name, navigator.languages[0] || navigator.language, {numeric: true, ignorePunctuation: true}));
}

onMounted(async() => {
    document.getElementsByTagName('title')[0].innerHTML = 'loading... ' + props.siteTitle;
    crypt.value = Crypt.generateFromUrl();
    await crypt.value.awaitReady();
    let meetingData = JSON.parse(crypt.value.decrypt(props.meeting.data));
    Object.keys(meeting.value).forEach(key => {
        meeting.value[key] = meetingData[key];
    });
    crypt.value.privateKey = crypt.value.decrypt(props.meeting.private_key);
    meeting.value.destroy_at = props.meeting.destroy_at;
    meeting.value.show_route = props.meeting.show_route;
    meeting.value.attendee_store_route = props.meeting.attendee_store_route;
    meeting.value.name = meeting.value.name.substring(0, 255);
    attendees.value = parseAttendees(props.meeting.attendees)
    loaded.value = true;

    let meetingTitle = meeting.value.name.substring(0, 100);
    meetingTitle = meetingTitle.length < meeting.value.name.length ? meetingTitle + '...' : meetingTitle;
    document.getElementsByTagName('title')[0].innerHTML = meetingTitle + ' - ' + props.siteTitle;
});

const onAttendeeSubmit = (attendeeString) => {
    posting.value = true;
    let compressed = 'compressed:' + LZString.compressToEncodedURIComponent(attendeeString);
    let encryptedForm = {
        data: crypt.value.encrypt(compressed),
    }
    encryptedForm.signature = crypt.value.getSignatureFromFormObject(['data'], encryptedForm);
    axios.post(meeting.value.attendee_store_route, encryptedForm)
        .then(response => {
            if(response.data.error) {
                globalError.value = response.data.message || 'Something went wrong.';
            } else {
                attendees.value = parseAttendees(response.data.data);
            }
            mode.value = 'show';
            return;
        })
        .catch(error => {
            console.log(error);
            globalError.value = 'Something went wrong.';
        })
        .then(() => posting.value = false);
}

const deleteAttendee = (attendee) => {
    posting.value = true;
    let encryptedForm = {
        destroy_challenge: attendee.destroy_challenge,
    }
    encryptedForm.signature = crypt.value.getSignatureFromFormObject(['destroy_challenge'], encryptedForm);
    axios.post(attendee.destroy_route, encryptedForm)
        .then(response => {
            attendees.value = parseAttendees(response.data.data);
        })
        .catch(error => {
            console.log(error);
            globalError.value = 'Something went wrong.';
        })
        .then(() => posting.value = false);
}

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    shared.value = true;
    setTimeout(() => shared.value = false, 3000);
}

</script>

<template>
    <div aria-live="polite">
        <VAlert v-model="globalError"></VAlert>
        <div v-if="!loaded || posting" class="flex items-center justify-center mt-10">
            <span class="animate-pulse font-semibold text-2xl" v-if="!loaded">Loading...</span>
            <span class="animate-pulse font-semibold text-2xl" v-if="posting">Sending data...</span>
        </div>
        <div v-else :class="meeting.entire_period ? '' : 'px-5'">
            <div class="grid grid-cols-2 py-3">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold tracking-wider text-left">{{ meeting.name }}</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-600 whitespace-nowrap">
                            {{ moment.tz(meeting.start_time, 'HH:mm', meeting.timezone).format('h a') }}
                            -
                            {{ moment.tz(meeting.end_time, 'HH:mm', meeting.timezone).format('h a') }}
                            ({{ meeting.timezone }})
                        </span>
                        <VButton size="xs" @click="copyLink" class="hidden md:block">
                            <span v-if="!shared">Share <i class="fas fa-share"></i></span>
                            <span v-else>Copied <i class="fas fa-check"></i></span>
                        </VButton>
                    </div>
                </div>
                <div class="flex flex-col justify-start items-end gap-2">
                    <VButton size="sm" v-if="mode == 'show'" @click="mode = 'add'">Add&nbsp;Availability</VButton>
                    <VButton size="sm" v-if="mode == 'add'" color="danger" @click="mode = 'show'">Cancel</VButton>
                    <VLabel class="select-none hidden md:block" v-if="!meeting.entire_period">
                        Symbol Mode
                        <VCheckbox v-model="symbolMode"></VCheckbox>
                    </VLabel>
                </div>
                <div class="flex flex-col gap-2 items-start">
                    <div v-if="meeting.timezone !== userTimezone" class="flex items-center">
                        <VLabel>
                            <span class="normal-case mr-1">Show {{ userTimezone }}</span>
                            <VCheckbox v-model="showDifferentTimezoneInfo"></VCheckbox>
                        </VLabel>
                    </div>
                    <VButton size="xs" @click="copyLink" class="block md:hidden">
                        <span v-if="!shared">Share <i class="fas fa-share"></i></span>
                        <span v-else>Copied <i class="fas fa-check"></i></span>
                    </VButton>
                </div>
                <div>
                    <div class="text-right">
                        <VLabel class="select-none block md:hidden" v-if="!meeting.entire_period">
                            Symbol Mode
                            <VCheckbox v-model="symbolMode"></VCheckbox>
                        </VLabel>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-7">
                <div :class="[mode == 'show' ? 'col-span-4' : 'col-span-full']">
                    <EntirePeriod
                        v-if="meeting.entire_period"
                        :meeting="meeting"
                        :attendees="attendees"
                        :show-different-timezone-info="showDifferentTimezoneInfo"
                        :user-timezone="userTimezone"
                        v-model:selected-attendee="selectedAttendee"
                        v-model:selected-day="selectedDay"
                        :mode="mode"
                        @submit="onAttendeeSubmit"
                    ></EntirePeriod>
                    <OptionalPeriod
                        v-else
                        :meeting="meeting"
                        :attendees="attendees"
                        :mode="mode"
                        :symbol-mode="symbolMode"
                        :show-different-timezone-info="showDifferentTimezoneInfo"
                        :user-timezone="userTimezone"
                        v-model:selected-attendee="selectedAttendee"
                        v-model:selected-day="selectedDay"
                        @submit="onAttendeeSubmit"
                    ></OptionalPeriod>
                </div>
                <div :class="[meeting.entire_period ? '' : 'my-5 md:mt-10']" v-if="mode == 'show'">
                    <h2 class="text-lg font-semibold tracking-wider text-left text-nowrap">Responses ({{ attendees.length }})</h2>
                    <ul class="flex flex-col gap-3">
                        <li v-for="attendee in attendees">
                            <span
                                v-if="selectedDay"
                                class="font-medium"
                                :class="[
                                    selectedDay.attendeesYes.find(a => a.identifier == attendee.identifier) ? 'text-green-700' : '',
                                    selectedDay.attendeesMaybe.find(a => a.identifier == attendee.identifier) ? 'text-yellow-600' : '',
                                    selectedDay.attendeesNo.find(a => a.identifier == attendee.identifier) ? 'opacity-50 line-through' : '',
                                ]"
                                @click="selectedAttendee = selectedAttendee === attendee ? selectedAttendee = null : selectedAttendee = attendee">
                                <i class="far fa-circle-check" aria-label="Yes" v-if="selectedDay.attendeesYes.find(a => a.identifier == attendee.identifier)"></i>
                                <i class="far fa-circle-question" aria-label="Maybe" v-else-if="selectedDay.attendeesMaybe.find(a => a.identifier == attendee.identifier)"></i>
                                <i class="far fa-circle-xmark" aria-label="No" v-else-if="selectedDay.attendeesNo.find(a => a.identifier == attendee.identifier)"></i>
                                <span class="ml-2 break-all">{{ attendee.name }}</span>
                            </span>
                            <span v-else class="inline-block">
                                <button
                                    type="button"
                                    class="hover:text-green-600 hover:font-medium cursor-pointer break-all inline text-left"
                                    :class="[selectedAttendee === attendee ? 'text-green-500 font-medium' : '']"
                                    @click="selectedAttendee = selectedAttendee === attendee ? selectedAttendee = null : selectedAttendee = attendee">
                                    • <span class="sr-only">Select</span> {{ attendee.name }}
                                </button>
                                <button @click="attendee.showDelete = true" aria-label="Delete responder" class="cursor-pointer ml-2">
                                    <i class="fa fa-xmark text-red-600 text-sm"></i>
                                </button>
                                <VueFinalModal
                                    v-model="attendee.showDelete" class="flex justify-center items-center"
                                    content-class="bg-white p-5 rounded relative">
                                    <button class="absolute top-0 right-1 cursor-pointer" @click="attendee.showDelete = false" aria-label="Close Modal"><i class="fa fa-xmark"></i></button>
                                    <form @submit.prevent="deleteAttendee(attendee)">
                                        <span class="break-all">Are you sure you want to delete this response for "{{ attendee.name }}"?</span>
                                        <div class="flex items-center justify-between mt-5">
                                            <VButton type="submit" color="danger">Delete</VButton>
                                            <VButton type="button" color="secondary" @click="attendee.showDelete = false">Cancel</VButton>
                                        </div>
                                    </form>
                                </VueFinalModal>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="text-center text-gray-700 font-medium mt-20">
            This poll will self destruct on the {{ moment(meeting.destroy_at).format('Do of MMMM YYYY') }}
        </p>
    </div>
</template>