<script setup>

import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import moment from 'moment';
import { onMounted, ref } from 'vue';
import { VueFinalModal } from 'vue-final-modal';
import VInput from './components/VInput.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import { useEcho } from '@laravel/echo-vue';
import LZString from 'lz-string';

const props = defineProps({
    rota: Object,
    siteTitle: String,
});

const globalError = ref(null);
const loaded = ref(false);
const posting = ref(false);
const crypt = ref(null);
const shared = ref(false);
const headersLocked = ref(true);
const addPersonModalShow = ref(false);
const addPersonModalData = ref(null);
const addPersonForm = ref({
    name: null,
});
const formErrors = ref({});
const lastHeadersUpdate = ref(moment());
const headersWaitingForUpdate = ref(false);

const rota = ref({
    name: null,
    destroy_at: null,
    roles: [],
    timeslots: [],
    people: [],
});

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href);
    shared.value = true;
    setTimeout(() => shared.value = false, 3000);
}

const moveItemInArray = (arr, fromIdx, toIdx) => {
  let numberOfDeletedElm = 1;
  const elm = arr.splice(fromIdx, numberOfDeletedElm)[0];
  numberOfDeletedElm = 0;
  arr.splice(toIdx, numberOfDeletedElm, elm);
}

const getUniqueRoleId = () => {
    return getNextIdFromExistingIds(rota.value.roles.map(role => role.id));
}

const getUniqueTimeslotId = () => {
    return getNextIdFromExistingIds(rota.value.timeslots.map(role => role.id));
}

const getUniquePersonId = () => {
    return getNextIdFromExistingIds(rota.value.people.map(p => p.id));
}

const getNextIdFromExistingIds = (existingIds) => {
    return existingIds.reduce((prev, curr) => curr > prev ? curr : prev, 0) + 1;
}

const submitHeaders = () => {
    headersLocked.value = true;
    onHeaderUpdate();
}

const onHeaderUpdate = () => {
    prunePeopleList();
    if(lastHeadersUpdate.value > moment().subtract(1, 'seconds') && !headersWaitingForUpdate.value) {
        headersWaitingForUpdate.value = true;
        setTimeout(() => {
            headersWaitingForUpdate.value = false;
            encryptAndUpdateRota();
            lastHeadersUpdate.value = moment();
        }, 1000)
    } else if(!headersWaitingForUpdate.value) {
        encryptAndUpdateRota();
        lastHeadersUpdate.value = moment();
    }

}

const encryptAndUpdateRota = (successCallback) => {
    formErrors.value = {};

    let dataForm = {
        name: rota.value.name,
        roles: rota.value.roles,
        timeslots: rota.value.timeslots,
        people: rota.value.people,
    };

    let compressed = 'compressed:' + LZString.compressToEncodedURIComponent(JSON.stringify(dataForm));

    let encryptedForm = {
        data: crypt.value.encrypt(compressed),
    };
    encryptedForm.signature = crypt.value.getSignatureFromFormObject(['data'], encryptedForm);

    axios.post(rota.value.update_route, encryptedForm)
        .then(response => {
            if(successCallback) {
                successCallback();
            }
        })
        .catch(error => {
            globalError.value = 'Something went wrong.';
            if(error.response && error.response.data.errors) {
                Object.entries(error.response.data.errors).forEach(([k, v]) => {
                    formErrors.value[k] = v[0];
                });
                console.log(formErrors);
            }
        });
}

const addRole = (name) => {
    name = name ?? '';
    let role = {name: name, id: getUniqueRoleId()};
    rota.value.roles.push(role);
    onHeaderUpdate();
    return role;
}

const removeRole = (role) => {
    rota.value.roles = rota.value.roles.filter((value) => value.id != role.id);
    onHeaderUpdate();
}

const moveRoleRight = (role) => {
    let idx = rota.value.roles.findIndex((v) => role.id == v.id);
    if(idx < rota.value.roles.length - 1) {
        moveItemInArray(rota.value.roles, idx, idx + 1);
    }
    onHeaderUpdate();
}

const moveRoleLeft = (role) => {
    let idx = rota.value.roles.findIndex((v) => role.id == v.id);
    if(idx > 0) {
        moveItemInArray(rota.value.roles, idx, idx - 1);
    }
    onHeaderUpdate();
}

const addTimeslot = (name) => {
    name = name ?? '';
    let timeslot = {name: name, id: getUniqueTimeslotId()}
    rota.value.timeslots.push(timeslot);
    onHeaderUpdate();
    return timeslot;
}

const removeTimeslot = (timeslot) => {
    rota.value.timeslots = rota.value.timeslots.filter((value) => value.id != timeslot.id);
    onHeaderUpdate();
}

const moveTimeslotDown = (timeslot) => {
    let idx = rota.value.timeslots.findIndex((v) => timeslot.id == v.id);
    if(idx < rota.value.timeslots.length - 1) {
        moveItemInArray(rota.value.timeslots, idx, idx + 1);
    }
    onHeaderUpdate();
}

const moveTimeslotUp = (timeslot) => {
    let idx = rota.value.timeslots.findIndex((v) => timeslot.id == v.id);
    if(idx > 0) {
        moveItemInArray(rota.value.timeslots, idx, idx - 1);
    }
    onHeaderUpdate();
}

const openAddPersonModal = (role, timeslot) => {
    addPersonModalShow.value = true;
    addPersonModalData.value = {role: role, timeslot: timeslot};
}

const closeAddPersonModal = () => {
    addPersonModalShow.value = false;
    addPersonModalData.value = {role: null, timeslot: null};
    addPersonForm.value.name = null;
}

const addPerson = (name, role, timeslot) => {
    rota.value.people.push({
        name: name,
        id: getUniquePersonId(),
        rid: role.id,
        tid: timeslot.id,
    });
    encryptAndUpdateRota(() => {
        closeAddPersonModal();
    });
}

const removePerson = (person) => {
    rota.value.people = rota.value.people.filter((value) => value.id != person.id);
    encryptAndUpdateRota();
}

const prunePeopleList = () => {
    let roleIds = rota.value.roles.map(r => r.id);
    let timeslotIds = rota.value.timeslots.map(r => r.id);
    rota.value.people = rota.value.people.filter(p => roleIds.includes(p.rid) && timeslotIds.includes(p.tid));
}

const peopleForRoleAndTimeslot = (role, timeslot) => {
    return rota.value.people
        .filter(person => person.rid == role.id && person.tid == timeslot.id)
        .sort((a, b) => a.name.localeCompare(b.name));
}

const uniquePeopleList = () => {
    return [...new Set(rota.value.people.map(p => p.name))].sort((a, b) => a.localeCompare(b));
}

const decryptAndSetRotaData = async (rotaData) => {
    await crypt.value.awaitReady();
    let data = crypt.value.decrypt(rotaData)
    if(data.startsWith('compressed:')) {
        data = LZString.decompressFromEncodedURIComponent(data.replace('compressed:', ''));
    }
    rotaData = JSON.parse(data);
    Object.keys(rotaData).forEach(key => {
        rota.value[key] = rotaData[key];
    });
}

const setUpEchoListeners = () => {
    Echo.channel(`rota.${rota.value.identifier}.updated`)
        .listen('.rota.updated', (event) => {
            decryptAndSetRotaData(event.data);
        });
}

onMounted(async() => {
    document.getElementsByTagName('title')[0].innerHTML = 'loading... ' + props.siteTitle;
    crypt.value = Crypt.generateFromUrl();
    await decryptAndSetRotaData(props.rota.data);
    await crypt.value.awaitReady();
    crypt.value.privateKey = crypt.value.decrypt(props.rota.private_key);
    rota.value.destroy_at = props.rota.destroy_at;
    rota.value.show_route = props.rota.show_route;
    rota.value.update_route = props.rota.update_route;
    rota.value.name = rota.value.name.substring(0, 255);
    rota.value.roles = rota.value.roles ?? [];
    rota.value.timeslots = rota.value.timeslots ?? [];
    rota.value.people = rota.value.people ?? [];
    rota.value.identifier = props.rota.identifier;
    rota.value.data = props.rota.data ?? [];
    loaded.value = true;

    let rotaTitle = rota.value.name.substring(0, 100);
    rotaTitle = rotaTitle.length < rota.value.name.length ? rotaTitle + '...' : rotaTitle;
    document.getElementsByTagName('title')[0].innerHTML = rotaTitle + ' - ' + props.siteTitle;

    setUpEchoListeners();
});

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
            <div class="flex items-center gap-2 py-3">
                <h1 class="text-xl font-bold tracking-wider text-left break-all">{{ rota.name }}</h1>
                <VButton size="xs" @click="copyLink" class="" dusk="share-btn">
                    <span class="flex items-center gap-1" v-if="!shared">Share <i class="fas fa-share"></i></span>
                    <span class="flex items-center gap-1" v-else>Copied <i class="fas fa-check"></i></span>
                </VButton>
            </div>
        </div>
        <div v-if="posting" class="flex items-center justify-center mt-10">
            <span class="animate-pulse font-semibold text-2xl">Sending data...</span>
        </div>
        <component :is="headersLocked ? 'div' : 'form'" @submit.prevent="submitHeaders">
            <div class="flex items-center max-w-full gap-2 justify-center">
                <div class="overflow-x-auto max-w-full">
                    <table class="mx-px my-5 table-fixed touch-none text-sm md:text-normal">
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="min-w-24 max-w-30 sticky top-0 left-0 bg-white z-10"
                                    :class="[headersLocked ? '' : 'border border-gray-600']"
                                >
                                    <span class="sr-only">Timeslots</span>
                                    <button class="cursor-pointer" @click="headersLocked ? headersLocked = false : null" :type="headersLocked ? 'button' : 'submit'">
                                        <i class="fas fa-edit fa-xs text-green-700" aria-label="Edit headers" title="Edit headers" v-if="headersLocked"></i>
                                        <i class="fas fa-save text-white bg-green-700 rounded shadow p-1 m-1" aria-label="Save headers" title="Save headers" v-else></i>
                                    </button>
                                </th>
                                <th
                                    scope="col"
                                    v-for="role in rota.roles"
                                    :class="[!headersLocked ? 'border border-gray-600 bg-gray-100' : '']"
                                >
                                    <div class="flex flex-col items-center font-normal select-none">
                                        <span v-if="headersLocked" class="break-all"><b>{{ role.name }}</b></span>
                                        <div v-else class="flex items-center gap-1 px-1">
                                            <label :for="'roleEditInput' + role.id" class="sr-only">Edit role</label>
                                            <button type="button" class="cursor-pointer" @click="moveRoleLeft(role)" v-if="rota.roles.length > 1">
                                                <i class="fas fa-angle-left fa-xs text-gray-400" aria-label="Move role left" title="Move role left"></i>
                                            </button>
                                            <input class="text-center w-full" type="text" v-model="role.name" :id="'roleEditInput' + role.id"
                                                placeholder="Role..." @input="onHeaderUpdate" maxlength="64">
                                            <button type="button" class="cursor-pointer" @click="moveRoleRight(role)" v-if="rota.roles.length > 1">
                                                <i class="fas fa-angle-right fa-xs text-gray-400" aria-label="Move role right" title="Move role right"></i>
                                            </button>
                                            <button type="button" class="cursor-pointer mx-2" @click="removeRole(role)" v-if="rota.roles.length > 1">
                                                <i class="fas fa-trash fa-xs text-gray-400" aria-label="Remove role" title="Remove role"></i>
                                            </button>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="timeslot in rota.timeslots" draggable="false">
                                <th
                                    scope="row"
                                    class="sticky left-0 bg-white z-10 break-all"
                                    :class="[!headersLocked ? 'border border-gray-600 bg-gray-100' : '']"
                                >
                                    <div class="flex flex-col items-center font-normal select-none px-1">
                                        <span v-if="headersLocked"><b>{{ timeslot.name }}</b></span>
                                        <div v-else class="flex items-center px-1 gap-1">
                                            <label :for="'timeslotEditInput' + timeslot.id" class="sr-only">Edit timeslot</label>
                                            <input
                                                class="text-center w-full"
                                                type="text"
                                                v-model="timeslot.name"
                                                :id="'timeslotEditInput' + timeslot.id"
                                                @input="onHeaderUpdate"
                                                maxlDown="64"
                                            >
                                            <div class="flex flex-col justify-center gap-1">
                                                <button type="button" class="cursor-pointer" @click="moveTimeslotUp(timeslot)" v-if="rota.timeslots.length > 1">
                                                    <i class="fas fa-angle-up fa-xs text-gray-400" aria-label="Move timeslot up" title="Move timeslot up"></i>
                                                </button>
                                                <button type="button" class="cursor-pointer" @click="removeTimeslot(timeslot)"
                                                    v-if="rota.timeslots.length > 1">
                                                    <i class="fas fa-trash fa-xs text-gray-400" aria-label="Remove slot" title="Remove slot"></i>
                                                </button>
                                                <button type="button" class="cursor-pointer" @click="moveTimeslotDown(timeslot)" v-if="rota.timeslots.length > 1">
                                                    <i class="fas fa-angle-down fa-xs text-gray-400" aria-label="Move timeslot down" title="Move timeslot down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <td
                                    v-for="role in rota.roles"
                                    class="border border-gray-400 px-2 py-1 relative text-center min-w-40 group"
                                    :class="[!headersLocked ? 'pointer-events-none opacity-50' : '']"
                                    :disabled="!headersLocked"
                                >
                                    <ul>
                                        <li
                                            v-for="person in peopleForRoleAndTimeslot(role, timeslot)"
                                            class="bg-gray-300 rounded-full px-1 py-px my-1 font-semibold"
                                        >
                                            {{ person.name }}
                                            <button @click="removePerson(person)" class="text-gray-500 text-xs cursor-pointer">
                                                <i class="fa fa-trash" :aria-label="'Remove ' + person.name"></i>
                                            </button>
                                        </li>
                                    </ul>
                                    <VButton
                                        size="xs"
                                        class="opacity-50"
                                        aria-label="Add person"
                                        @click="openAddPersonModal(role, timeslot)"
                                    >
                                        <i class="fas fa-plus pl-px"></i>
                                    </VButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="">
                    <VButton
                        type="button"
                        color="secondary"
                        size="sm"
                        v-if="!headersLocked && rota.roles.length < 24"
                        @click="addRole()"
                    >
                        <i class="fas fa-plus" aria-label="Add role" title="Add role"></i>
                    </VButton>
                </div>
            </div>
            <div class="flex justify-center mt-2">
                <VButton
                    type="button"
                    color="secondary"
                    size="sm"
                    v-if="!headersLocked && rota.timeslots.length < 96"
                    @click="addTimeslot()"
                >
                    <i class="fas fa-plus" aria-label="Add slot" title="Add slot"></i>
                </VButton>
            </div>
        </component>
        <p class="text-center text-gray-700 font-medium mt-20">
            This rota will self destruct on the {{ moment(rota.destroy_at).format('Do of MMMM YYYY') }}
        </p>
        <VueFinalModal
            v-model="addPersonModalShow"
            class="flex justify-center items-center"
            content-class="bg-white rounded relative"
        >
            <form @submit.prevent="addPerson(addPersonForm.name, addPersonModalData.role, addPersonModalData.timeslot)">
                <div class="w-xs md:w-xl p-5 mx-2 max-w-screen">
                    <fieldset class="mb-3">
                        <VLegend class="mb-4">
                            Add person to {{ addPersonModalData.role.name }} for {{ addPersonModalData.timeslot.name }}
                        </VLegend>
                        <VLabel for="addPersonName">Name</VLabel>
                        <VInput id="addPersonName" v-model="addPersonForm.name" maxlength="32" required></VInput>
                        <ul class="flex gap-2 flex-wrap items-center">
                            <li v-for="person in uniquePeopleList()" class="my-3">
                                <VButton
                                    type="button"
                                    @click="addPersonForm.name = person"
                                    color="secondary"
                                    size="sm"
                                >{{ person }}</VButton>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="flex justify-between">
                        <VButton type="button" color="secondary" @click="addPersonModalShow = false">Cancel</VButton>
                        <VButton type="submit">Add Person</VButton>
                    </div>
                </div>
            </form>
        </VueFinalModal>
    </div>
</template>