<script setup>

import VInput from './components/VInput.vue';
import VSelect from './components/VSelect.vue';
import VCheckbox from './components/VCheckbox.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import { onMounted, ref, useTemplateRef, watch } from 'vue';
import { useValidation } from './composables/validation';
import moment from 'moment-timezone';
import PowCaptcha from './components/PowCaptcha.vue';
import VError from './components/VError.vue';

const props = defineProps({
    submitRoute: String,
    captchaChallengeRoute: String,
});

const { validate } = useValidation();

const times = [
    '00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00',
    '12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00',
];
const generatingStatus = ref(null);
const captchaVerifying = ref(false);
const globalError = ref(null);
const selectSpecificDates = ref(false);
const formErrors = ref({});
const form = ref({
    name: null,
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    entire_period: false,
    start_date: moment().format('YYYY-MM-DD'),
    end_date: moment().add(6, 'day').format('YYYY-MM-DD'),
    start_time: '09:00',
    end_time: '17:00',
    destroy_at: moment().add(6, 'day').format('YYYY-MM-DD'),
});

watch(() => form.value.end_date, () => {
    form.value.destroy_at = form.value.end_date;
});

const datesSelected = ref([]);
const generateDatesSelected = () => {
    datesSelected.value = [];
    const startDate = moment(form.value.start_date, 'YYYY-MM-DD');
    const endDate = moment(form.value.end_date, 'YYYY-MM-DD');
    if(!startDate.isValid() || !endDate.isValid()) {
        selectSpecificDates.value = false;
        return;
    }
    const current = startDate.clone();
    while(current <= endDate) {
        let date = current.clone();
        let datestring = current.format('YYYY-MM-DD');
        datesSelected.value.push({
            date: date,
            datestring: datestring,
            selected: true,
        });
        current.add(1, 'day');
    }
}
watch(() => form.value.start_date + '|' + form.value.end_date, generateDatesSelected);

const captcha = useTemplateRef('captcha');

const verifyCaptcha = () => {
    if(!validateForm()) {
        return;
    }
    captchaVerifying.value = true;
    captcha.value.completeCaptcha();
}

const submit = (captchaToken) => {
    generatingStatus.value = 'Encrypting data...';
    formErrors.value = {};
    let crypt = new Crypt();
    crypt.freshKeys();

    let dataForm = {
        name: form.value.name,
        timezone: form.value.timezone,
        entire_period: form.value.entire_period,
        start_date: form.value.start_date,
        end_date: form.value.end_date,
        dates: datesSelected.value.filter(d => d.selected).map(d => d.datestring),
        start_time: form.value.start_time,
        end_time: form.value.end_time,
    };

    let encryptedForm = {
        private_key: crypt.encrypt(crypt.privateKey),
        public_key: crypt.publicKey,
        data: crypt.encrypt(JSON.stringify(dataForm)),
        destroy_at: form.value.destroy_at,
        captcha: captchaToken,
    };

    encryptedForm.signature = crypt.getSignatureFromFormObject(['data', 'destroy_at'], encryptedForm);

    generatingStatus.value = 'Communicating with server...';

    axios.post(props.submitRoute, encryptedForm)
        .then(response => {
            generatingStatus.value = 'Success! Redirecting...';
            window.location.href=crypt.appendKeyToUrl(response.data.show_route);
        })
        .catch(error => {
            globalError.value = 'Something went wrong.';
            generatingStatus.value = null;
            if(error.response && error.response.data.errors) {
                Object.entries(error.response.data.errors).forEach(([k, v]) => {
                    formErrors.value[k] = v[0];
                });
                console.log(formErrors);
            }
        });
}

const validateForm = () => {
    formErrors.value = validate({
        name: ['required', 'maxLen:100'],
        timezone: ['required'],
        start_date: ['required', 'date', 'dateEqualOrAfter:today'],
        end_date: ['required', 'date', 'dateEqualOrAfter:start_date'],
        start_time: ['required', 'time'],
        end_time: ['required', 'time'],
        destroy_at: ['required', 'date', 'dateEqualOrAfter:tomorrow'],
    }, form.value);
    return Object.values(formErrors.value).length == 0;
}

const timeGt = (time1, time2) => {
    return moment(time1, 'HH:mm') > moment(time2, 'HH:mm');
}
const timeLt = (time1, time2) => {
    return moment(time1, 'HH:mm') < moment(time2, 'HH:mm');
}

onMounted(() => {
    generateDatesSelected();
});

</script>

<template>
    <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Create a Meeting</h1>
    <form class="flex flex-col max-w-lg mx-auto mb-10" @submit.prevent="verifyCaptcha" aria-live="polite">
        <VAlert v-model="globalError"></VAlert>

        <div :class="[captchaVerifying ? 'hidden' : '']">
            <div class="my-5">
                <VLabel class="text-left sr-only" for="name">Meeting Name</VLabel>
                <VInput type="text" name="name" id="name" placeholder="Meeting Name" v-model="form.name" v-model:error="formErrors.name"
                    maxlength="255"
                    required></VInput>
            </div>

            <fieldset class="mt-10">
                <VLegend>What date range should the meeting be in?</VLegend>
                <div class="grid md:grid-cols-2 md:gap-10">
                    <div class="my-5">
                        <VLabel class="text-left" for="start_date">Start Date</VLabel>
                        <VInput type="date" name="start_date" id="start_date" v-model="form.start_date" v-model:error="formErrors.start_date"
                            :min="moment().format('YYYY-MM-DD')" :max="moment().add(6, 'months').format('YYYY-MM-DD')"
                            required></VInput>
                    </div>
                    <div class="my-5">
                        <VLabel class="text-left" for="end_date">End Date</VLabel>
                        <VInput type="date" name="end_date" id="end_date" v-model="form.end_date" v-model:error="formErrors.end_date"
                            :min="moment(form.start_date, 'YYYY-MM-DD', true).isValid() ? moment(form.start_date).format('YYYY-MM-DD') : null"
                            :max="moment().add(6, 'months').format('YYYY-MM-DD')"
                            required></VInput>
                    </div>
                    <div class="mt-5 col-span-full md:-mt-5" :class="[selectSpecificDates ? 'mb-5 md:mb-0' : '']">
                        <VLabel class="text-left" for="specific_dates">
                            <span class="inline-block mr-3">Select Specific Dates</span>
                            <VCheckbox type="checkbox" name="specific_dates" id="specific_dates" v-model="selectSpecificDates"></VCheckbox>
                        </VLabel>
                    </div>
                    <ul v-if="selectSpecificDates" class="grid grid-cols-2 md:grid-cols-4 col-span-full gap-3">
                        <li v-for="(date, index) in datesSelected" :key="'date' + index">
                            <input type="checkbox" :id="'date' + date.datestring" :name="date.datestring" v-model="date.selected"
                                class="peer sr-only"
                                :disabled="datesSelected.filter(d => d.selected).length == 1 && date.selected">
                            <label
                                class="block text-center
                                    peer-focus:ring-1
                                    border-2 border-gray-300 peer-checked:border-green-600
                                    bg-gray-50 peer-checked:bg-green-50
                                    rounded shadow p-1 select-none
                                "
                                :for="'date' + date.datestring">
                                <b>{{ date.date.format('ddd Do MMM') }}</b>
                            </label>
                        </li>
                    </ul>
                </div>
            </fieldset>

            <fieldset class="mt-10 mb-5">
                <VLegend>What times do you want the meeting between?</VLegend>
                <div class="grid md:grid-cols-2 md:gap-10">
                    <div class="mt-5 col-span-full mb-5 md:-mb-5">
                        <VLabel class="text-left" for="entire_period">
                            <span class="inline-block mr-3">Require availability for entire period?</span>
                            <VCheckbox type="checkbox" name="entire_period" id="entire_period" v-model="form.entire_period"></VCheckbox>
                        </VLabel>
                    </div>
                    <div class="mb-5 md:mb-0">
                        <VLabel class="text-left" for="start_time">Start Time</VLabel>
                        <VSelect name="start_time" id="start_time" v-model="form.start_time" v-model:error="formErrors.start_time"
                            required>
                            <option v-for="time in times" :key="'startTime' + time" :value="time" :disabled="!timeLt(time, form.end_time, true)">
                                {{ moment(time, 'HH:mm').format('h a') }}
                            </option>
                        </VSelect>
                    </div>

                    <div>
                        <VLabel class="text-left" for="end_time">End Time</VLabel>
                        <VSelect name="end_time" id="end_time" v-model="form.end_time" v-model:error="formErrors.end_time"
                            required>
                            <option v-for="time in times" :key="'endTime' + time" :value="time" :disabled="!timeGt(time, form.start_time)">
                                {{ moment(time, 'HH:mm').format('h a') }}
                            </option>
                        </VSelect>
                    </div>
                </div>
            </fieldset>

            <fieldset class="mt-10">
                <VLegend>Extra info</VLegend>
                <div class="my-5">
                    <VLabel class="text-left" for="destroy_at">Destroy Date</VLabel>
                    <VInput type="date" name="destroy_at" id="destroy_at" v-model="form.destroy_at" v-model:error="formErrors.destroy_at"
                        :min="moment().add(1, 'days').format('YYYY-MM-DD')"
                        :max="moment().add(6, 'months').format('YYYY-MM-DD')"></VInput>
                </div>
                <div class="my-5">
                    <VLabel class="text-left" for="timezone">Timezone</VLabel>
                    <VSelect name="timezone" id="timezone" v-model="form.timezone" v-model:error="formErrors.timezone" required>
                        <option v-for="tz in moment.tz.names()" :key="'timezone' + tz" :value="tz">{{ tz }}</option>
                    </VSelect>
                </div>
            </fieldset>
        </div>

        <div class="text-right mt-10">
            <div class="text-center flex justify-center">
                <PowCaptcha
                    ref="captcha"
                    :challenge-route="props.captchaChallengeRoute"
                    @completed="submit"
                ></PowCaptcha>
            </div>
            <VButton v-if="!captchaVerifying" type="submit" size="lg">Create!</VButton>
        </div>
        <div class="text-center">
            <VError v-model="formErrors.captcha"></VError>
        </div>
    </form>
    <div v-if="generatingStatus" class="fixed inset-0 bg-gray-100 flex flex-col items-center justify-center font-semibold text-2xl" role="alert">
        <span class="animate-pulse">{{ generatingStatus }}</span>
        🔒
    </div>
</template>