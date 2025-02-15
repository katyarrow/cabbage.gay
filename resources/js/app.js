import { createVfm } from 'vue-final-modal';
import 'vue-final-modal/style.css';
import './bootstrap';
import './crypt';

import CreateMeeting from './vue/CreateMeeting.vue';
import ShowMeeting from './vue/ShowMeeting.vue';

import { createApp } from 'vue';

const app = createApp();
const vfm = createVfm()
app.use(vfm);

app.component('CreateMeeting', CreateMeeting);
app.component('ShowMeeting', ShowMeeting);

document.addEventListener('DOMContentLoaded', () => {
    app.mount('#app');
});