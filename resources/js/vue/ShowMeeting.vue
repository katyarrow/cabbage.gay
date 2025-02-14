<script setup>

import VInput from './components/VInput.vue';
import VLabel from './components/VLabel.vue';
import VLegend from './components/VLegend.vue';
import VButton from './components/VButton.vue';
import VAlert from './components/VAlert.vue';
import moment from 'moment';
import { ref } from 'vue';

const meeting = ref({
    name: 'Example Meeting',
    start_date: moment().subtract(1, 'days'),
    end_date: moment().add(1, 'days'),
    start_time: '09:00',
    end_time: '17:00',
});

const selected = ref([]);

const initialSelect = ref(null);

const startMouse = (time, day) => {
    initialSelect.value = [time, day];
}

const endMouse = (time, day) => {
    for(let i = initialSelect.value[0]; i <= time; i++) {
        selected.value.push([i, day]);
    }
    initialSelect.value = null;
}

const isSelected = (time, day) => {
    return selected.value.find(v => v[0] == time && v[1] == day);
}

</script>

<template>
    <div class="grid grid-cols-2 py-3">
        <h1 class="text-xl font-bold tracking-wider text-left">{{ meeting.name }}</h1>
        <div class="text-right">
            <VButton size="sm">Add Availability</VButton>
        </div>
    </div>
    <table class="w-full table-auto">
        <thead>
            <tr>
                <th class="w-auto">TIME</th>
                <th>Fri 07</th>
                <th>Sat 08</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="i in 8">
                <td class="p-1 w-auto whitespace-nowrap select-none">{{ i + 8 }} {{ i + 9 > 12 ? 'AM' : 'PM' }}</td>
                <td
                    class="p-1 w-1/2 border border-gray-200"
                    :class="[isSelected(i, 1) ? 'bg-green-300' : '']"
                    @mousedown="startMouse(i, 1)"
                    @mouseup="endMouse(i, 1)"
                ></td>
                <td
                    class="p-1 w-1/2 border border-gray-200"
                    :class="[isSelected(i, 2) ? 'bg-green-300' : '']"
                    @click="selectDatetime(i, 2)"
                ></td>
            </tr>
        </tbody>
    </table>
</template>