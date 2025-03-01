<script setup>
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';

const props = defineProps({
    challengeRoute: String,
});

const emit = defineEmits([
    'completed',
    'failed',
]);

const percentRemaining = ref(100);
const started = ref(false);
const completed = ref(false);
const error = ref(false);

const completeCaptcha = async () => {
    let response = await axios.get(props.challengeRoute);
    let captcha = response.data.data;
    started.value = true;
    let pow = new POW();
    pow.calculatePowAnswers(captcha);
    let interval = setInterval(() => {
        percentRemaining.value = (pow.currentTotalTries / pow.estimatedTotalTries) * 100;
        if(pow.isFinished() && pow.hasAllAnswers()) {
            verifyCaptcha(captcha, pow);
            clearInterval(interval)
        }
        if(pow.isFinished() && !pow.hasAllAnswers()) {
            error.value = true;
            emit('failed');
            clearInterval(interval)
        }
    }, 100);
}

const verifyCaptcha = async (captcha, pow) => {
    try {
        let captchaData = {id: captcha.id, answers: pow.answers};
        let response = await axios.post(captcha.verify_route, {
            captcha: captchaData,
        });
        if(response.data.success) {
            completed.value = true;
            emit('completed', response.data.solved_token);
        } else {
            error.value = true;
        }
    } catch {
        error.value = true;
        emit('failed');
    }
}

defineExpose({
    completeCaptcha
});

</script>

<template>
    <div class="p-2 rounded shadow border border-green-600 font-medium w-3xs"
        v-if="started" aria-live="true" role="alert">
        <div v-if="error" class="flex items-center text-red-700 text-lg">
            <i class="fa fa-xmark text-2xl mr-3"></i>
            <span>Captcha Failed! Refresh Page</span>
        </div>
        <div class=" grid grid-cols-3 gap-5" v-else>
            <span class="flex items-center justify-center text-3xl">
                <i class="fa fa-check text-green-600" v-if="completed"></i>
                <i class="fa fa-spinner fa-spin text-gray-500" v-else></i>
            </span>
            <span class="flex items-center justify-center col-span-2 text-2xl">
                <span v-if="completed">Completed!</span>
                <span v-else>Verifying</span>
            </span>
            <span class="col-span-full relative rounded overflow-hidden h-3 bg-gray-50 w-full">
                <div class="h-full bg-green-600" :style="{width: percentRemaining + '%'}">
                    <span class="sr-only">{{ percentRemaining }}%</span>
                </div>
            </span>
        </div>
    </div>
</template>