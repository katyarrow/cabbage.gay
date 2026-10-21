<script setup>

import VInput from './components/VInput.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import { ref, useTemplateRef, watch } from 'vue';
import { useValidation } from './composables/validation';
import moment from 'moment-timezone';
import PowCaptcha from './components/PowCaptcha.vue';
import DonationMessage from './components/DonationMessage.vue';
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
const formErrors = ref({});
const form = ref({
    name: null,
    destroy_at: moment().add(6, 'day').format('YYYY-MM-DD'),
});

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
        roles: [
            {name: 'Availability', id: 1}
        ],
        timeslots: [
            {name: '12pm-1pm', id: 1},
            {name: '1pm-2pm', id: 2},
            {name: '2pm-3pm', id: 3},
            {name: '3pm-4pm', id: 4},
            {name: '4pm-5pm', id: 5},
        ],
        people: [],
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
        destroy_at: ['required', 'date', 'dateEqualOrAfter:tomorrow'],
    }, form.value);
    return Object.values(formErrors.value).length == 0;
}

</script>

<template>
    <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Create a Rota</h1>
    <form class="flex flex-col max-w-lg mx-auto mb-10" @submit.prevent="verifyCaptcha" aria-live="polite">
        <VAlert v-model="globalError"></VAlert>

        <div :class="[captchaVerifying ? 'hidden' : '']">
            <div class="my-5">
                <VLabel class="text-left sr-only" for="name">Rota Name</VLabel>
                <VInput type="text" name="name" id="name" placeholder="Rota Name" v-model="form.name" v-model:error="formErrors.name"
                    maxlength="255"
                    required></VInput>
            </div>

            <fieldset class="mt-10">
                <VLegend>Extra info</VLegend>
                <div class="my-5">
                    <VLabel class="text-left" for="destroy_at">Destroy Date</VLabel>
                    <VInput type="date" name="destroy_at" id="destroy_at" v-model="form.destroy_at" v-model:error="formErrors.destroy_at"
                        :min="moment().add(1, 'days').format('YYYY-MM-DD')"
                        :max="moment().add(6, 'months').format('YYYY-MM-DD')"></VInput>
                </div>
            </fieldset>
        </div>

        <div class="text-right mt-10">
            <div class="text-center flex flex-col justify-center items-center gap-10">
                <PowCaptcha
                    ref="captcha"
                    :challenge-route="props.captchaChallengeRoute"
                    @completed="submit"
                ></PowCaptcha>
                <DonationMessage v-if="captchaVerifying"></DonationMessage>
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