<script setup>

import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import moment from 'moment';
import { onMounted, ref } from 'vue';

const props = defineProps({
    rota: Object,
    siteTitle: String,
});

const globalError = ref(null);
const loaded = ref(false);
const posting = ref(false);
const crypt = ref(null);
const shared = ref(false);

const rota = ref({
    name: null,
    destroy_at: null,
});

const symbolMode = ref(false);

onMounted(async() => {
    document.getElementsByTagName('title')[0].innerHTML = 'loading... ' + props.siteTitle;
    crypt.value = Crypt.generateFromUrl();
    await crypt.value.awaitReady();
    let rotaData = JSON.parse(crypt.value.decrypt(props.rota.data));
    Object.keys(rota.value).forEach(key => {
        rota.value[key] = rotaData[key];
    });
    crypt.value.privateKey = crypt.value.decrypt(props.rota.private_key);
    rota.value.destroy_at = props.rota.destroy_at;
    rota.value.show_route = props.rota.show_route;
    rota.value.name = rota.value.name.substring(0, 255);
    loaded.value = true;

    let rotaTitle = rota.value.name.substring(0, 100);
    rotaTitle = rotaTitle.length < rota.value.name.length ? rotaTitle + '...' : rotaTitle;
    document.getElementsByTagName('title')[0].innerHTML = rotaTitle + ' - ' + props.siteTitle;
});

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    shared.value = true;
    setTimeout(() => shared.value = false, 3000);
}

</script>

<template>
    <div aria-live="polite">
        <VAlert v-model="globalError"></VAlert>
        <div v-if="!loaded" class="flex items-center justify-center mt-10">
            <span class="animate-pulse font-semibold text-2xl" v-if="!loaded">Loading...</span>
        </div>
        <div
            v-else
            :class="[posting ? 'hidden' : '']"
            :aria-hidden="posting"
        >
            <div class="grid grid-cols-2 py-3">
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold tracking-wider text-left">{{ rota.name }}</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <VButton size="xs" @click="copyLink" class="hidden md:block">
                            <span v-if="!shared">Share <i class="fas fa-share"></i></span>
                            <span v-else>Copied <i class="fas fa-check"></i></span>
                        </VButton>
                    </div>
                </div>
                <div class="flex flex-col gap-2 items-start">
                    <VButton size="xs" @click="copyLink" class="block md:hidden" dusk="share-btn">
                        <span v-if="!shared">Share <i class="fas fa-share"></i></span>
                        <span v-else>Copied <i class="fas fa-check"></i></span>
                    </VButton>
                </div>
            </div>
        </div>
        <div v-if="posting" class="flex items-center justify-center mt-10">
            <span class="animate-pulse font-semibold text-2xl">Sending data...</span>
        </div>
        <p class="text-center text-gray-700 font-medium mt-20">
            This rota will self destruct on the {{ moment(rota.destroy_at).format('Do of MMMM YYYY') }}
        </p>
    </div>
</template>