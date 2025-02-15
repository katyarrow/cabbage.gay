<script setup>

import VInput from './components/VInput.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import moment from 'moment';
import { onMounted, ref } from 'vue';
import EntirePeriod from './components/Meeting/EntirePeriod.vue';
import { VueFinalModal } from 'vue-final-modal';

const props = defineProps({
    meeting: Object,
});

const globalError = ref(null);
const loaded = ref(false);
const posting = ref(false);
const crypt = ref(null);
const mode = ref('show');

const meeting = ref({
    name: null,
    timezone: null,
    entire_period: null,
    start_date: null,
    end_date: null,
    start_time: null,
    end_time: null,
    destroy_at: null,
});

const attendees = ref([]);

const parseAttendees = (encryptedAttendeeArray) => {
    return encryptedAttendeeArray.map(a => {
        let data = JSON.parse(crypt.value.decrypt(a.data));
        data.identifier = a.identifier;
        data.destroy_route = a.destroy_route;
        data.destroy_challenge = a.destroy_challenge;
        data.showDelete = a.showDelete;
        return data;
    });
}

onMounted(async() => {
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
    attendees.value = parseAttendees(props.meeting.attendees)
    loaded.value = true;
});

const onAttendeeSubmit = (attendeeString) => {
    posting.value = true;
    let encryptedForm = {
        data: crypt.value.encrypt(attendeeString),
    }
    encryptedForm.signature = crypt.value.getSignatureFromFormObject(['data'], encryptedForm);
    axios.post(meeting.value.attendee_store_route, encryptedForm)
        .then(response => {
            attendees.value = parseAttendees(response.data.data);
            mode.value = 'show';
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

</script>

<template>
    <div aria-live="polite">
        <VAlert v-model="globalError"></VAlert>
        <div v-if="!loaded || posting" class="flex items-center justify-center mt-10">
            <span class="animate-pulse font-semibold text-2xl" v-if="!loaded">Loading...</span>
            <span class="animate-pulse font-semibold text-2xl" v-if="posting">Sending data...</span>
        </div>
        <div v-else>
            <div class="grid grid-cols-2 py-3">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold tracking-wider text-left">{{ meeting.name }}</h1>
                    <div>
                        <span class="text-gray-600 whitespace-nowrap">
                            {{ moment(meeting.start_time, 'HH:mm').format('h a') }}
                            -
                            {{ moment(meeting.end_time, 'HH:mm').format('h a') }}
                            ({{ meeting.timezone }})
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <VButton size="sm" v-if="mode == 'show'" @click="mode = 'add'">Add Availability</VButton>
                    <VButton size="sm" v-if="mode == 'add'" color="danger" @click="mode = 'show'">Cancel</VButton>
                </div>
            </div>
            <EntirePeriod
                v-if="meeting.entire_period"
                :meeting="meeting"
                :attendees="attendees"
                :mode="mode"
                @submit="onAttendeeSubmit"
                ></EntirePeriod>
            <div class="my-5">
                <h2 class="text-lg font-semibold tracking-wider text-left">Responders ({{ attendees.length }})</h2>
                <ul class="list-disc list-inside grid grid-cols-1 md:grid-cols-4 gap-3">
                    <li v-for="attendee in attendees">
                        {{ attendee.name }}
                        <button @click="attendee.showDelete = true" aria-label="Delete responder" class="cursor-pointer ml-2">
                            <i class="fa fa-xmark text-red-600 text-sm"></i>
                        </button>
                        <VueFinalModal
                            v-model="attendee.showDelete" class="flex justify-center items-center"
                            content-class="bg-white p-5 rounded relative">
                            <button class="absolute top-0 right-1 cursor-pointer" @click="attendee.showDelete = false" aria-label="Close Modal"><i class="fa fa-xmark"></i></button>
                            <form @submit.prevent="deleteAttendee(attendee)">
                                Are you sure you want to delete this response?
                                <div class="flex items-center justify-between mt-5">
                                    <VButton type="submit" color="danger">Delete</VButton>
                                    <VButton type="button" color="secondary" @click="attendee.showDelete = false">Cancel</VButton>
                                </div>
                            </form>
                        </VueFinalModal>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>